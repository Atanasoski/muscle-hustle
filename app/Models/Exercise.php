<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'video_url',
        'pixabay_video_path',
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
}
