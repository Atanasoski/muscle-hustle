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
        'category_id',
        'name',
        'description',
        'image_url',
        'video_url',
        'default_rest_sec',
    ];

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

    /**
     * Relationship: Exercise belongs to many Partners (many-to-many)
     */
    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class, 'partner_exercises')
            ->withPivot(['description', 'image_url', 'video_url'])
            ->withTimestamps();
    }

    /**
     * Scope: Get exercises for a specific partner
     */
    public function scopeForPartner($query, Partner $partner)
    {
        return $query->whereHas('partners', function ($q) use ($partner) {
            $q->where('partners.id', $partner->id);
        });
    }

    /**
     * Get the effective description (pivot override or exercise default)
     */
    public function getEffectiveDescription(?Partner $partner = null): ?string
    {
        if ($partner && $this->relationLoaded('pivot') && $this->pivot) {
            return $this->pivot->description ?? $this->description;
        }

        return $this->description;
    }

    /**
     * Get the effective image URL (pivot override or exercise default)
     */
    public function getEffectiveImageUrl(?Partner $partner = null): ?string
    {
        if ($partner && $this->relationLoaded('pivot') && $this->pivot) {
            return $this->pivot->image_url ?? $this->image_url;
        }

        return $this->image_url;
    }

    /**
     * Get the effective video URL (pivot override or exercise default)
     */
    public function getEffectiveVideoUrl(?Partner $partner = null): ?string
    {
        if ($partner && $this->relationLoaded('pivot') && $this->pivot) {
            return $this->pivot->video_url ?? $this->video_url;
        }

        return $this->video_url;
    }
}
