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

    /**
     * Get previous set logs for exercises in this session from the last completed session per exercise
     * Uses Eloquent but batches queries efficiently
     */
    public function getPreviousSetLogsForExercises(array $exerciseIds): \Illuminate\Support\Collection
    {
        if (empty($exerciseIds)) {
            return collect();
        }

        // Get all previous completed sessions for this user that have set logs for these exercises
        $previousSessions = WorkoutSession::query()
            ->where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->whereNotNull('completed_at')
            ->whereHas('setLogs', fn ($q) => $q->whereIn('exercise_id', $exerciseIds))
            ->with(['setLogs' => fn ($q) => $q->whereIn('exercise_id', $exerciseIds)->orderBy('set_number')])
            ->orderByDesc('completed_at')
            ->get();

        // Group by exercise_id and get the most recent session's sets for each exercise
        $result = collect();

        foreach ($exerciseIds as $exerciseId) {
            // Find the most recent session that has sets for this exercise
            $latestSession = $previousSessions
                ->filter(fn ($session) => $session->setLogs->contains('exercise_id', $exerciseId))
                ->first();

            if ($latestSession) {
                $sets = $latestSession->setLogs
                    ->where('exercise_id', $exerciseId)
                    ->sortBy('set_number')
                    ->values();

                if ($sets->isNotEmpty()) {
                    $result[$exerciseId] = $sets;
                }
            }
        }

        return $result;
    }
}
