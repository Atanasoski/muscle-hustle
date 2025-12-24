<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\User;
use App\Models\WorkoutSession;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('partner_admin')) {
            return $this->partnerDashboard();
        }

        abort(403, 'Unauthorized access to dashboard.');
    }

    /**
     * Admin dashboard with platform-wide metrics.
     */
    private function adminDashboard()
    {
        // Platform stats
        $totalPartners = Partner::count();
        $activePartners = Partner::where('is_active', true)->count();
        $totalUsers = User::count();

        // Partners with metrics
        $partners = Partner::withCount([
            'users',
            'users as active_users_count' => function ($query) {
                $query->whereHas('workoutSessions', function ($q) {
                    $q->whereBetween('performed_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);
                });
            },
        ])
            ->with(['identity', 'users' => function ($query) {
                $query->latest()->take(1);
            }])
            ->withMax('users', 'created_at')
            ->get()
            ->map(function ($partner) {
                $partner->workouts_this_week = WorkoutSession::whereHas('user', function ($query) use ($partner) {
                    $query->where('partner_id', $partner->id);
                })->whereBetween('performed_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ])->count();

                return $partner;
            });

        // Recent activity
        $recentUsers = User::with('partner')
            ->latest()
            ->take(10)
            ->get();

        $recentWorkouts = WorkoutSession::with(['user.partner'])
            ->whereNotNull('completed_at')
            ->latest('performed_at')
            ->take(10)
            ->get();

        return view('dashboard.admin', compact(
            'totalPartners',
            'activePartners',
            'totalUsers',
            'partners',
            'recentUsers',
            'recentWorkouts'
        ));
    }

    /**
     * Partner admin dashboard with gym-specific metrics.
     */
    private function partnerDashboard()
    {
        $user = auth()->user();
        $partner = $user->partner;

        if (! $partner) {
            abort(403, 'No partner associated with your account.');
        }

        // Gym stats
        $totalMembers = $partner->users()->count();
        $activeMembersThisWeek = $partner->users()
            ->whereHas('workoutSessions', function ($query) {
                $query->whereBetween('performed_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
            })
            ->count();

        // Top active members
        $topMembers = $partner->users()
            ->withCount(['workoutSessions' => function ($query) {
                $query->whereBetween('performed_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
            }])
            ->having('workout_sessions_count', '>', 0)
            ->orderByDesc('workout_sessions_count')
            ->take(5)
            ->get();

        // Inactive members (no workout in last 7 days)
        $inactiveMembers = $partner->users()
            ->whereDoesntHave('workoutSessions', function ($query) {
                $query->where('performed_at', '>=', Carbon::now()->subDays(7));
            })
            ->latest()
            ->take(5)
            ->get();

        // Recent members
        $recentMembers = $partner->users()
            ->latest()
            ->take(10)
            ->get();

        // Recent workouts
        $recentWorkouts = WorkoutSession::whereHas('user', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })
            ->with(['user'])
            ->whereNotNull('completed_at')
            ->latest('performed_at')
            ->take(10)
            ->get();

        return view('dashboard.partner', compact(
            'partner',
            'totalMembers',
            'activeMembersThisWeek',
            'topMembers',
            'inactiveMembers',
            'recentMembers',
            'recentWorkouts'
        ));
    }
}
