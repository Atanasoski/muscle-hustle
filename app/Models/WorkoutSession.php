<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workout_template_id',
        'performed_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationship: WorkoutSession belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: WorkoutSession belongs to WorkoutTemplate
     */
    public function workoutTemplate(): BelongsTo
    {
        return $this->belongsTo(WorkoutTemplate::class);
    }

    /**
     * Relationship: WorkoutSession has many SetLogs
     */
    public function setLogs(): HasMany
    {
        return $this->hasMany(SetLog::class);
    }

    /**
     * Relationship: WorkoutSession has many WorkoutSessionExercises
     */
    public function workoutSessionExercises(): HasMany
    {
        return $this->hasMany(WorkoutSessionExercise::class)->orderBy('order');
    }
}
