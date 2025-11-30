<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
