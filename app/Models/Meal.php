<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meal extends Model
{
    protected $fillable = [
        'meal_plan_id',
        'day_of_week',
        'type',
        'name',
        'serving_size',
        'calories',
        'protein',
        'carbs',
        'fat',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    /**
     * Relationship: Meal belongs to MealPlan
     */
    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }
}
