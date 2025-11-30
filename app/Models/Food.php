<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'brand',
        'calories',
        'protein',
        'carbs',
        'fat',
        'fiber',
        'sugar',
        'default_serving_unit',
        'default_serving_size',
        'is_favorite',
    ];

    protected $casts = [
        'calories' => 'decimal:2',
        'protein' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fat' => 'decimal:2',
        'fiber' => 'decimal:2',
        'sugar' => 'decimal:2',
        'default_serving_size' => 'decimal:2',
        'is_favorite' => 'boolean',
    ];

    /**
     * Get the user that owns the food
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get recipe ingredients that use this food
     */
    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /**
     * Scope for global foods (not user-specific)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope for user's custom foods
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for favorites
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }
}
