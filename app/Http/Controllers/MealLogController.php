<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Meal;
use Illuminate\Http\Request;

class MealLogController extends Controller
{
    /**
     * Add a food to a meal
     */
    public function addFood(Request $request, Meal $meal)
    {
        // Authorization check
        if ($meal->mealPlan->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'food_id' => 'required|exists:foods,id',
            'servings' => 'required|numeric|min:0.1|max:50',
        ]);

        $food = Food::findOrFail($request->food_id);

        // Calculate grams from servings
        $grams = $food->default_serving_size * $request->servings;

        // Attach food to meal with serving info
        $meal->foods()->attach($food->id, [
            'servings' => $request->servings,
            'grams' => $grams,
        ]);

        // Recalculate meal nutrition
        $meal->updateNutrition();

        return back()->with('success', 'Food added to meal!');
    }

    /**
     * Remove a food from a meal
     */
    public function removeFood(Meal $meal, Food $food)
    {
        // Authorization check
        if ($meal->mealPlan->user_id !== auth()->id()) {
            abort(403);
        }

        $meal->foods()->detach($food->id);

        // Recalculate meal nutrition
        $meal->updateNutrition();

        return back()->with('success', 'Food removed from meal!');
    }

    /**
     * Update servings of a food in a meal
     */
    public function updateServings(Request $request, Meal $meal, Food $food)
    {
        // Authorization check
        if ($meal->mealPlan->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'servings' => 'required|numeric|min:0.1|max:50',
        ]);

        // Calculate new grams
        $grams = $food->default_serving_size * $request->servings;

        // Update pivot
        $meal->foods()->updateExistingPivot($food->id, [
            'servings' => $request->servings,
            'grams' => $grams,
        ]);

        // Recalculate meal nutrition
        $meal->updateNutrition();

        return back()->with('success', 'Servings updated!');
    }
}
