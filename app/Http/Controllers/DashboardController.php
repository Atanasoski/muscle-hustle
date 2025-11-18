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

        return view('dashboard', compact('todayWorkout', 'weekWorkouts', 'mealPlan', 'dayOfWeek'));
    }
}
