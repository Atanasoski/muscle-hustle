<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meal extends Model
{
    protected $fillable = [
        'meal_plan_id',
        'recipe_id',
        'servings',
        'day_of_week',
        'type',
        'name',
        'serving_size',
        'calories',
        'protein',
        'carbs',
        'fat',
        'notes',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'servings' => 'decimal:2',
    ];

    /**
     * Relationship: Meal belongs to MealPlan
     */
    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }

    /**
     * Relationship: Meal belongs to Recipe (optional)
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Relationship: Meal has many Foods
     */
    public function foods(): BelongsToMany
    {
        return $this->belongsToMany(Food::class, 'meal_food')
            ->withPivot('servings', 'grams')
            ->withTimestamps();
    }

    /**
     * Calculate total nutrition from all foods in this meal
     */
    public function calculateNutrition(): array
    {
        $nutrition = [
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
        ];

        foreach ($this->foods as $food) {
            $grams = $food->pivot->grams;
            $multiplier = $grams / 100; // Since nutrition is per 100g

            $nutrition['calories'] += $food->calories * $multiplier;
            $nutrition['protein'] += $food->protein * $multiplier;
            $nutrition['carbs'] += $food->carbs * $multiplier;
            $nutrition['fat'] += $food->fat * $multiplier;
        }

        return $nutrition;
    }

    /**
     * Update the meal's nutrition totals based on its foods
     */
    public function updateNutrition(): void
    {
        $nutrition = $this->calculateNutrition();

        $this->update([
            'calories' => round($nutrition['calories']),
            'protein' => round($nutrition['protein']),
            'carbs' => round($nutrition['carbs']),
            'fat' => round($nutrition['fat']),
        ]);
    }
}
