<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'food_id',
        'quantity',
        'unit',
        'notes',
        'order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'order' => 'integer',
    ];

    /**
     * Get the recipe that owns this ingredient
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Get the food for this ingredient
     */
    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }
}
