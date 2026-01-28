<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\WorkoutAIServiceInterface;
use App\Services\AI\Services\OpenAIWorkoutService;
use InvalidArgumentException;

class WorkoutAIServiceFactory
{
    /**
     * Create a workout AI service instance
     *
     * @param  string|null  $service  Service type (openai) or null for default
     *
     * @throws InvalidArgumentException
     */
    public static function make(?string $service = null): WorkoutAIServiceInterface
    {
        $service = $service ?? config('workout_ai.default_service', 'openai');

        return match ($service) {
            'openai' => new OpenAIWorkoutService,
            default => throw new InvalidArgumentException("Workout AI service [{$service}] is not supported."),
        };
    }
}
