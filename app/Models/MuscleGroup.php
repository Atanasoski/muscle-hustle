<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MuscleGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'body_region',
    ];

    /**
     * Relationship: MuscleGroup has many Exercises (many-to-many)
     */
    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'exercise_muscle_group')
            ->withPivot('is_primary');
    }

    /**
     * Scope: Get only upper body muscle groups
     */
    public function scopeUpperBody($query)
    {
        return $query->where('body_region', 'upper');
    }

    /**
     * Scope: Get only lower body muscle groups
     */
    public function scopeLowerBody($query)
    {
        return $query->where('body_region', 'lower');
    }

    /**
     * Scope: Get only core muscle groups
     */
    public function scopeCore($query)
    {
        return $query->where('body_region', 'core');
    }
}
