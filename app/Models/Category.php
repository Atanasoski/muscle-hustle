<?php

namespace App\Models;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'slug',
        'display_order',
        'icon',
        'color',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type' => CategoryType::class,
        ];
    }

    /**
     * Scope: Get only workout categories
     */
    public function scopeWorkout($query)
    {
        return $query->where('type', CategoryType::Workout);
    }

    /**
     * Scope: Get categories by type
     */
    public function scopeOfType($query, CategoryType $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Relationship: Category has many Exercises
     */
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }
}
