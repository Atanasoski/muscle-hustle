<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetLog extends Model
{
    protected $table = 'workout_session_set_logs';

    protected $fillable = [
        'workout_session_id',
        'exercise_id',
        'set_number',
        'weight',
        'reps',
        'rest_seconds',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    /**
     * Relationship: SetLog belongs to WorkoutSession
     */
    public function workoutSession(): BelongsTo
    {
        return $this->belongsTo(WorkoutSession::class);
    }

    /**
     * Relationship: SetLog belongs to Exercise
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
