<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\MealPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MealPlannerController extends Controller
{
    /**
     * Show weekly meal planner
     */
    public function index()
    {
        $weekStart = Carbon::now()->startOfWeek();

        // Get or create meal plan for this week
        $mealPlan = MealPlan::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'week_start_date' => $weekStart->toDateString(),
            ]
        );

        // Get all meals for this plan organized by day and type
        $meals = $mealPlan->meals()
            ->orderBy('day_of_week')
            ->orderBy('type')
            ->get();

        // Organize meals in a 2D array [day][type]
        $mealGrid = [];
        $types = ['breakfast', 'lunch', 'dinner', 'snack'];

        for ($day = 0; $day < 7; $day++) {
            foreach ($types as $type) {
                $mealGrid[$day][$type] = $meals->first(function ($meal) use ($day, $type) {
                    return $meal->day_of_week === $day && $meal->type === $type;
                });
            }
        }

        // Calculate daily totals
        $dailyTotals = [];
        for ($day = 0; $day < 7; $day++) {
            $dayMeals = collect($types)
                ->map(fn ($type) => $mealGrid[$day][$type])
                ->filter();

            $dailyTotals[$day] = [
                'calories' => $dayMeals->sum('calories'),
                'protein' => $dayMeals->sum('protein'),
                'carbs' => $dayMeals->sum('carbs'),
                'fat' => $dayMeals->sum('fat'),
            ];
        }

        // Calculate weekly totals
        $weeklyTotals = [
            'calories' => collect($dailyTotals)->sum('calories'),
            'protein' => collect($dailyTotals)->sum('protein'),
            'carbs' => collect($dailyTotals)->sum('carbs'),
            'fat' => collect($dailyTotals)->sum('fat'),
        ];

        // Calculate average per day (only days with meals)
        $daysWithMeals = collect($dailyTotals)
            ->filter(fn ($day) => $day['calories'] > 0)
            ->count();

        $weeklyTotals['avg_calories'] = $daysWithMeals > 0
            ? round($weeklyTotals['calories'] / $daysWithMeals)
            : 0;

        return view('planner.meals', compact('mealPlan', 'mealGrid', 'weekStart', 'dailyTotals', 'weeklyTotals'));
    }

    /**
     * Save meal
     */
    public function store(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'type' => 'required|in:breakfast,lunch,dinner,snack',
            'name' => 'required|string|max:255',
            'serving_size' => 'nullable|string',
            'calories' => 'nullable|integer|min:0',
            'protein' => 'nullable|integer|min:0',
            'carbs' => 'nullable|integer|min:0',
            'fat' => 'nullable|integer|min:0',
        ]);

        $weekStart = Carbon::now()->startOfWeek();

        // Get or create meal plan
        $mealPlan = MealPlan::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'week_start_date' => $weekStart->toDateString(),
            ]
        );

        // Create or update meal
        Meal::updateOrCreate(
            [
                'meal_plan_id' => $mealPlan->id,
                'day_of_week' => $request->day_of_week,
                'type' => $request->type,
            ],
            [
                'name' => $request->name,
                'serving_size' => $request->serving_size,
                'calories' => $request->calories,
                'protein' => $request->protein,
                'carbs' => $request->carbs,
                'fat' => $request->fat,
            ]
        );

        return redirect()->route('planner.meals')
            ->with('success', 'Meal saved successfully!');
    }

    /**
     * Delete meal
     */
    public function destroy(Meal $meal)
    {
        // Authorization check
        $mealPlan = $meal->mealPlan;
        if ($mealPlan->user_id !== auth()->id()) {
            abort(403);
        }

        $meal->delete();

        return redirect()->route('planner.meals')
            ->with('success', 'Meal deleted successfully!');
    }
}
