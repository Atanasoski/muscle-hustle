<?php

namespace App\Services\Nutrition\Parsers;

use App\Services\Nutrition\Contracts\NutritionParserInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAINutritionParser implements NutritionParserInterface
{
    private string $apiKey;

    private string $model;

    public function __construct()
    {
        $this->apiKey = config('nutrition.openai.api_key');
        $this->model = config('nutrition.openai.model', 'gpt-4');
    }

    /**
     * Parse nutrition text using OpenAI API
     */
    public function parse(string $text): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $text,
                    ],
                ],
                'temperature' => 0.1, // Low temperature for more consistent results
            ]);

            if (! $response->successful()) {
                Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('Failed to parse nutrition data with OpenAI');
            }

            $content = $response->json('choices.0.message.content');
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from OpenAI');
            }

            return $this->formatResponse($data);
        } catch (\Exception $e) {
            Log::error('OpenAI nutrition parsing failed', [
                'error' => $e->getMessage(),
                'text' => $text,
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
You are a nutrition expert. Parse the user's food intake text and return ONLY a valid JSON object with this exact structure:

{
  "items": [
    {
      "name": "Chicken Breast",
      "quantity": 300,
      "unit": "g",
      "calories": 495,
      "protein": 93,
      "carbs": 0,
      "fat": 10.5,
      "fiber": 0,
      "sugar": 0
    }
  ]
}

Rules:
1. Always return nutritional values per 100g/ml as the basis
2. Multiply by quantity to get actual values
3. Be precise with measurements (convert cups, tbsp, etc. to grams/ml)
4. For generic items (e.g., "chicken"), assume standard cooked versions
5. Round decimals to 1 decimal place
6. Return ONLY valid JSON, no markdown, no explanations
7. If you can't identify a food, make your best educated guess
8. For "handful", assume ~30g; for "medium", assume standard serving size
9. All numeric values should be numbers, not strings

Common conversions:
- 1 cup rice (cooked) = 195g
- 1 cup vegetables = 150g
- 1 tbsp = 15ml
- 1 tsp = 5ml
- 1 medium fruit = 150g
PROMPT;
    }

    /**
     * Format the API response to match our interface
     */
    private function formatResponse(array $data): array
    {
        $items = $data['items'] ?? [];
        $totals = [
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
            'fiber' => 0,
            'sugar' => 0,
        ];

        foreach ($items as $item) {
            $totals['calories'] += $item['calories'] ?? 0;
            $totals['protein'] += $item['protein'] ?? 0;
            $totals['carbs'] += $item['carbs'] ?? 0;
            $totals['fat'] += $item['fat'] ?? 0;
            $totals['fiber'] += $item['fiber'] ?? 0;
            $totals['sugar'] += $item['sugar'] ?? 0;
        }

        return [
            'items' => $items,
            'totals' => $totals,
        ];
    }
}
