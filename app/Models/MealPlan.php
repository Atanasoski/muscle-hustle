<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlan extends Model
{
    protected $fillable = [
        'user_id',
        'week_start_date',
    ];

    protected $casts = [
        'week_start_date' => 'date',
    ];

    /**
     * Relationship: MealPlan belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: MealPlan has many Meals
     */
    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }
}
