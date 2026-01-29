<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Partner;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    /**
     * Display a listing of the user's plans.
     */
    public function index(Request $request, User $user): View
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can view plans.');
        }

        if ($user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        $plans = Plan::query()
            ->where('user_id', $user->id)
            ->withCount('workoutTemplates')
            ->latest()
            ->paginate(15);

        return view('plans.index', compact('user', 'partner', 'plans'));
    }

    /**
     * Show the form for creating a new plan for a user.
     */
    public function create(Request $request, User $user): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can create plans.');
        }

        if ($user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        return view('plans.create', compact('user', 'partner'));
    }

    /**
     * Store a newly created plan in storage.
     */
    public function store(StorePlanRequest $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        if ($user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        // Deactivate all other plans if this one is being set as active
        if ($request->is_active) {
            Plan::where('user_id', $user->id)
                ->update(['is_active' => false]);
        }

        $plan = Plan::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? false,
        ]);

        return redirect()->route('plans.show', $plan)
            ->with('success', 'Plan created successfully!');
    }

    /**
     * Display the specified plan.
     */
    public function show(Request $request, Plan $plan): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can view plans.');
        }

        $plan->load('user');
        if ($plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        $plan->load([
            'workoutTemplates' => function ($query) {
                $query->withCount('workoutTemplateExercises')
                    ->orderBy('day_of_week')
                    ->orderBy('name');
            },
        ]);

        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('plans.show', compact('plan', 'partner', 'dayNames'));
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Request $request, Plan $plan): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can edit plans.');
        }

        $plan->load('user');
        if ($plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        return view('plans.edit', compact('plan', 'partner'));
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(UpdatePlanRequest $request, Plan $plan): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        $plan->load('user');
        if ($plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        // Deactivate all other plans if this one is being set as active
        if ($request->is_active) {
            Plan::where('user_id', $plan->user_id)
                ->where('id', '!=', $plan->id)
                ->update(['is_active' => false]);
        }

        $plan->update($request->validated());

        return redirect()->route('plans.show', $plan)
            ->with('success', 'Plan updated successfully!');
    }

    /**
     * Remove the specified plan from storage.
     */
    public function destroy(Request $request, Plan $plan): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        $plan->load('user');
        if ($plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $userId = $plan->user_id;
        $plan->delete();

        return redirect()->route('users.show', $userId)
            ->with('success', 'Plan deleted successfully!');
    }
}
