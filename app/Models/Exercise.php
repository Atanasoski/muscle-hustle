<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use HasFactory;

    protected $table = 'workout_exercises';

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'image_url',
        'default_rest_sec',
    ];

    /**
     * Relationship: Exercise belongs to User (nullable for global exercises)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Exercise belongs to Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship: Exercise has many WorkoutTemplateExercises
     */
    public function workoutTemplateExercises(): HasMany
    {
        return $this->hasMany(WorkoutTemplateExercise::class);
    }

    /**
     * Relationship: Exercise has many SetLogs
     */
    public function setLogs(): HasMany
    {
        return $this->hasMany(SetLog::class);
    }

    /**
     * Relationship: Exercise has many MuscleGroups (many-to-many)
     */
    public function muscleGroups(): BelongsToMany
    {
        return $this->belongsToMany(MuscleGroup::class, 'exercise_muscle_group')
            ->withPivot('is_primary');
    }

    /**
     * Relationship: Exercise's primary muscle groups
     */
    public function primaryMuscleGroups(): BelongsToMany
    {
        return $this->muscleGroups()->wherePivot('is_primary', true);
    }

    /**
     * Relationship: Exercise's secondary muscle groups
     */
    public function secondaryMuscleGroups(): BelongsToMany
    {
        return $this->muscleGroups()->wherePivot('is_primary', false);
    }
}
