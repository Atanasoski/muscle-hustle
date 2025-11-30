<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'instructions',
        'prep_time_minutes',
        'cook_time_minutes',
        'servings',
        'meal_type',
        'is_favorite',
        'tags',
    ];

    protected $casts = [
        'servings' => 'decimal:2',
        'is_favorite' => 'boolean',
        'tags' => 'array',
    ];

    /**
     * Get the user that owns the recipe
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ingredients for this recipe
     */
    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class)->orderBy('order');
    }

    /**
     * Get the meals that use this recipe
     */
    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    /**
     * Calculate total nutritional values for this recipe
     */
    public function getTotalNutrition(): array
    {
        $totals = [
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
            'fiber' => 0,
            'sugar' => 0,
        ];

        foreach ($this->recipeIngredients as $ingredient) {
            $food = $ingredient->food;
            $multiplier = $ingredient->quantity / 100; // Convert to per-100g basis

            $totals['calories'] += $food->calories * $multiplier;
            $totals['protein'] += $food->protein * $multiplier;
            $totals['carbs'] += $food->carbs * $multiplier;
            $totals['fat'] += $food->fat * $multiplier;
            $totals['fiber'] += $food->fiber * $multiplier;
            $totals['sugar'] += $food->sugar * $multiplier;
        }

        return $totals;
    }

    /**
     * Get nutrition per serving
     */
    public function getNutritionPerServing(): array
    {
        $totals = $this->getTotalNutrition();
        $servings = $this->servings ?: 1;

        return [
            'calories' => round($totals['calories'] / $servings, 2),
            'protein' => round($totals['protein'] / $servings, 2),
            'carbs' => round($totals['carbs'] / $servings, 2),
            'fat' => round($totals['fat'] / $servings, 2),
            'fiber' => round($totals['fiber'] / $servings, 2),
            'sugar' => round($totals['sugar'] / $servings, 2),
        ];
    }

    /**
     * Get total prep + cook time
     */
    public function getTotalTimeAttribute(): int
    {
        return ($this->prep_time_minutes ?: 0) + ($this->cook_time_minutes ?: 0);
    }

    /**
     * Scope for favorites
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope by meal type
     */
    public function scopeByMealType($query, $type)
    {
        return $query->where('meal_type', $type);
    }
}
