<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Recipe;
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
            ->with('foods')
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

        // Fetch user's recipes for quick-add
        $recipes = Recipe::where('user_id', auth()->id())
            ->with('recipeIngredients.food')
            ->orderBy('is_favorite', 'desc')
            ->orderBy('name')
            ->get();

        // Fetch foods grouped by category for quick-add
        $foodCategories = Category::food()
            ->with(['foods' => function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('user_id')
                        ->orWhere('user_id', auth()->id());
                })
                    ->orderBy('name')
                    ->limit(50); // Limit for performance
            }])
            ->orderBy('display_order')
            ->get();

        return view('planner.meals', compact('mealPlan', 'mealGrid', 'weekStart', 'dailyTotals', 'weeklyTotals', 'recipes', 'foodCategories'));
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

    /**
     * Generate grocery list from weekly meals
     */
    public function groceryList()
    {
        $weekStart = Carbon::now()->startOfWeek();

        // Get or create meal plan for this week
        $mealPlan = MealPlan::where('user_id', auth()->id())
            ->where('week_start_date', $weekStart->toDateString())
            ->with(['meals.recipe.recipeIngredients.food.category', 'meals.foods.category'])
            ->first();

        if (! $mealPlan) {
            return view('planner.grocery-list', [
                'weekStart' => $weekStart,
                'groceries' => collect(),
                'totalRecipes' => 0,
                'totalMeals' => 0,
            ]);
        }

        // Collect all ingredients from recipes AND individual foods
        $ingredients = [];

        foreach ($mealPlan->meals as $meal) {
            // Add ingredients from recipes
            if ($meal->recipe) {
                $servingMultiplier = $meal->servings / $meal->recipe->servings;

                foreach ($meal->recipe->recipeIngredients as $ingredient) {
                    $key = $ingredient->food_id.'_'.$ingredient->unit;
                    $quantity = $ingredient->quantity * $servingMultiplier;

                    if (isset($ingredients[$key])) {
                        $ingredients[$key]['quantity'] += $quantity;
                        $ingredients[$key]['meals'][] = $meal->name;
                    } else {
                        $ingredients[$key] = [
                            'food' => $ingredient->food,
                            'quantity' => $quantity,
                            'unit' => $ingredient->unit,
                            'meals' => [$meal->name],
                        ];
                    }
                }
            }

            // Add individual foods from meal
            foreach ($meal->foods as $food) {
                // Convert to grams/serving size
                $quantity = $food->pivot->grams;
                $unit = 'g';

                $key = $food->id.'_'.$unit;

                if (isset($ingredients[$key])) {
                    $ingredients[$key]['quantity'] += $quantity;
                    $ingredients[$key]['meals'][] = $meal->name;
                } else {
                    $ingredients[$key] = [
                        'food' => $food,
                        'quantity' => $quantity,
                        'unit' => $unit,
                        'meals' => [$meal->name],
                    ];
                }
            }
        }

        // Group by category
        $groceries = collect($ingredients)->groupBy(function ($item) {
            return $item['food']->category?->name ?: 'Other';
        })->sortKeys();

        $totalRecipes = $mealPlan->meals->filter(fn ($meal) => $meal->recipe_id)->count();
        $totalMeals = $mealPlan->meals->count();

        return view('planner.grocery-list', compact('groceries', 'weekStart', 'totalRecipes', 'totalMeals'));
    }

    /**
     * Show food diary - what you've actually eaten this week
     */
    public function foodDiary()
    {
        $weekStart = Carbon::now()->startOfWeek();

        // Get or create meal plan for this week
        $mealPlan = MealPlan::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'week_start_date' => $weekStart->toDateString(),
            ]
        );

        // Get all meals with logged foods
        $meals = $mealPlan->meals()
            ->with(['foods.category'])
            ->whereHas('foods') // Only meals with logged foods
            ->orderBy('day_of_week')
            ->orderBy('type')
            ->get();

        // Organize by day
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $dayEmojis = ['💪', '🔥', '⚡', '🎯', '🚀', '😎', '🎉'];
        $types = ['breakfast', 'lunch', 'dinner', 'snack'];

        $logsByDay = [];
        for ($day = 0; $day < 7; $day++) {
            $dayMeals = $meals->where('day_of_week', $day);

            $logsByDay[$day] = [
                'name' => $days[$day],
                'emoji' => $dayEmojis[$day],
                'date' => $weekStart->copy()->addDays($day),
                'meals' => [],
                'totals' => ['calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0],
            ];

            foreach ($types as $type) {
                $meal = $dayMeals->firstWhere('type', $type);
                if ($meal && $meal->foods->count() > 0) {
                    $nutrition = $meal->calculateNutrition();
                    $logsByDay[$day]['meals'][$type] = [
                        'meal' => $meal,
                        'nutrition' => $nutrition,
                    ];

                    $logsByDay[$day]['totals']['calories'] += $nutrition['calories'];
                    $logsByDay[$day]['totals']['protein'] += $nutrition['protein'];
                    $logsByDay[$day]['totals']['carbs'] += $nutrition['carbs'];
                    $logsByDay[$day]['totals']['fat'] += $nutrition['fat'];
                }
            }
        }

        // Calculate weekly totals
        $weeklyTotals = [
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
            'foods_logged' => 0,
        ];

        foreach ($logsByDay as $day) {
            $weeklyTotals['calories'] += $day['totals']['calories'];
            $weeklyTotals['protein'] += $day['totals']['protein'];
            $weeklyTotals['carbs'] += $day['totals']['carbs'];
            $weeklyTotals['fat'] += $day['totals']['fat'];
        }

        $weeklyTotals['foods_logged'] = $meals->sum(function ($meal) {
            return $meal->foods->count();
        });

        $daysWithLogs = collect($logsByDay)->filter(fn ($day) => count($day['meals']) > 0)->count();
        $weeklyTotals['avg_calories'] = $daysWithLogs > 0 ? round($weeklyTotals['calories'] / $daysWithLogs) : 0;

        return view('planner.food-diary', compact('logsByDay', 'weekStart', 'weeklyTotals'));
    }
}
