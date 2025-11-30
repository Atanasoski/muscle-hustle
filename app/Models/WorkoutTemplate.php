<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutTemplate extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'day_of_week',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    /**
     * Relationship: WorkoutTemplate belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: WorkoutTemplate has many WorkoutTemplateExercises
     */
    public function workoutTemplateExercises(): HasMany
    {
        return $this->hasMany(WorkoutTemplateExercise::class)->orderBy('order');
    }

    /**
     * Relationship: WorkoutTemplate belongs to many Exercises through WorkoutTemplateExercise
     */
    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'workout_template_exercises')
            ->withPivot(['order', 'target_sets', 'target_reps', 'target_weight', 'rest_seconds'])
            ->withTimestamps()
            ->orderBy('order');
    }

    /**
     * Relationship: WorkoutTemplate has many WorkoutSessions
     */
    public function workoutSessions(): HasMany
    {
        return $this->hasMany(WorkoutSession::class);
    }
}
