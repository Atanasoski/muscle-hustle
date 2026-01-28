<?php

namespace App\Services\AI\Services;

use App\Services\AI\Contracts\WorkoutAIServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIWorkoutService implements WorkoutAIServiceInterface
{
    private string $apiKey;

    private string $model;

    private float $temperature;

    private int $maxTokens;

    private int $timeout;

    public function __construct()
    {
        $this->apiKey = config('workout_ai.openai.api_key');
        $this->model = config('workout_ai.openai.model', 'gpt-4-turbo-preview');
        $this->temperature = config('workout_ai.openai.temperature', 0.7);
        $this->maxTokens = config('workout_ai.openai.max_tokens', 2000);
        $this->timeout = config('workout_ai.openai.timeout', 30);
    }

    /**
     * Generate a workout session using OpenAI API
     */
    public function generateSession(string $prompt): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout($this->timeout)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens,
                'response_format' => ['type' => 'json_object'],
            ]);

            if (! $response->successful()) {
                Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('Failed to generate workout with OpenAI');
            }

            $content = $response->json('choices.0.message.content');
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from OpenAI');
            }

            return $this->formatResponse($data);
        } catch (\Exception $e) {
            Log::error('OpenAI workout generation failed', [
                'error' => $e->getMessage(),
                'prompt' => substr($prompt, 0, 500), // Log first 500 chars for debugging
            ]);

            throw $e;
        }
    }

    /**
     * Get the system prompt for OpenAI
     */
    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
You are an expert fitness trainer and workout program designer. Generate personalized workout sessions based on user profiles, exercise history, and preferences.

Return ONLY a valid JSON object with this exact structure:

{
  "exercises": [
    {
      "exercise_id": 1,
      "order": 1,
      "target_sets": 3,
      "target_reps": 10,
      "target_weight": 50.0,
      "rest_seconds": 90
    }
  ],
  "rationale": "Brief explanation of why this workout was designed this way"
}

Rules:
1. Select exercises that match the user's goals, experience level, and available equipment
2. Order exercises logically (compound movements first, isolation last)
3. Set appropriate target_sets (typically 3-5 sets per exercise)
4. Set appropriate target_reps based on fitness goal (strength: 1-6, hypertrophy: 8-12, endurance: 12+)
5. Set target_weight based on user's previous performance with progressive overload (2.5-5% increase)
6. Set rest_seconds appropriately (strength: 2-5 min, hypertrophy: 60-90 sec, endurance: 30-60 sec)
7. Ensure muscle group balance based on focus_muscle_groups if provided
8. Respect duration_minutes constraint if provided
9. Do not include exercises in exclude_exercises list
10. Return ONLY valid JSON, no markdown, no explanations outside the rationale field
11. All numeric values should be numbers, not strings
12. exercise_id must match an exercise from the provided exercise database
PROMPT;
    }

    /**
     * Format the API response to match our interface
     */
    private function formatResponse(array $data): array
    {
        return [
            'exercises' => $data['exercises'] ?? [],
            'rationale' => $data['rationale'] ?? 'AI-generated workout session',
        ];
    }
}
