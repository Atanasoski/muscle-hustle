<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\WorkoutTemplate;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::now();
        $dayOfWeek = $today->dayOfWeek === 0 ? 6 : $today->dayOfWeek - 1; // Convert to 0 (Mon) - 6 (Sun)

        // Get today's workout template
        $todayWorkout = WorkoutTemplate::where('user_id', $user->id)
            ->where('day_of_week', $dayOfWeek)
            ->with('exercises')
            ->first();

        // Get this week's workouts
        $weekWorkouts = WorkoutTemplate::where('user_id', $user->id)
            ->whereNotNull('day_of_week')
            ->with('exercises')
            ->orderBy('day_of_week')
            ->get();

        // Get this week's meal plan
        $weekStart = $today->copy()->startOfWeek();
        $mealPlan = MealPlan::where('user_id', $user->id)
            ->where('week_start_date', $weekStart->toDateString())
            ->with('meals')
            ->first();

        // Get today's meals
        $todayMeals = $mealPlan ? $mealPlan->meals->where('day_of_week', $dayOfWeek) : collect();

        // Get last 3 completed workouts
        $recentWorkouts = $user->workoutSessions()
            ->whereNotNull('completed_at')
            ->with(['workoutTemplate', 'setLogs.exercise'])
            ->latest('performed_at')
            ->take(4)
            ->get();

        // Calculate workout streak
        $streak = $this->calculateStreak($user->id);

        return view('dashboard', compact('todayWorkout', 'weekWorkouts', 'mealPlan', 'dayOfWeek', 'todayMeals', 'recentWorkouts', 'streak'));
    }

    private function calculateStreak(int $userId): int
    {
        // Get all completed workout dates
        $completedDates = \App\Models\WorkoutSession::where('user_id', $userId)
            ->whereNotNull('performed_at')
            ->pluck('performed_at')
            ->map(fn ($date) => $date->toDateString())
            ->unique()
            ->values();

        if ($completedDates->isEmpty()) {
            return 0;
        }

        // Get all planned workout days (day_of_week)
        $plannedDays = WorkoutTemplate::where('user_id', $userId)
            ->whereNotNull('day_of_week')
            ->pluck('day_of_week')
            ->toArray();

        $streak = 0;
        $checkDate = Carbon::yesterday();

        // Check backwards from yesterday
        while (true) {
            $dateString = $checkDate->toDateString();
            $dayOfWeek = $checkDate->dayOfWeek === 0 ? 6 : $checkDate->dayOfWeek - 1;

            // Check if this day had a completed workout
            if ($completedDates->contains($dateString)) {
                $streak++;
                $checkDate->subDay();

                continue;
            }

            // Check if this was a planned workout day
            if (in_array($dayOfWeek, $plannedDays)) {
                // Workout was planned but not completed - break streak
                break;
            }

            // It was a rest day, skip and continue
            $checkDate->subDay();

            // Safety: don't check more than 365 days back
            if ($checkDate->diffInDays(Carbon::now()) > 365) {
                break;
            }
        }

        return $streak;
    }
}
