<?php

namespace App\Services\AI\Contracts;

interface WorkoutAIServiceInterface
{
    /**
     * Generate a workout session using AI
     *
     * @param  string  $prompt  The formatted prompt for AI generation
     * @return array {
     *               'exercises' => [
     *               [
     *               'exercise_id' => int,
     *               'order' => int,
     *               'target_sets' => int,
     *               'target_reps' => int,
     *               'target_weight' => float,
     *               'rest_seconds' => int,
     *               ],
     *               ...
     *               ],
     *               'rationale' => string
     *               }
     */
    public function generateSession(string $prompt): array;
}
