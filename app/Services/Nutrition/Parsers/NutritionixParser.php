<?php

namespace App\Services\Nutrition\Parsers;

use App\Services\Nutrition\Contracts\NutritionParserInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NutritionixParser implements NutritionParserInterface
{
    private string $appId;

    private string $appKey;

    public function __construct()
    {
        $this->appId = config('nutrition.nutritionix.app_id');
        $this->appKey = config('nutrition.nutritionix.app_key');
    }

    /**
     * Parse nutrition text using Nutritionix API
     */
    public function parse(string $text): array
    {
        try {
            $response = Http::withHeaders([
                'x-app-id' => $this->appId,
                'x-app-key' => $this->appKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://trackapi.nutritionix.com/v2/natural/nutrients', [
                'query' => $text,
            ]);

            if (! $response->successful()) {
                Log::error('Nutritionix API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('Failed to parse nutrition data with Nutritionix');
            }

            $data = $response->json();

            return $this->formatResponse($data);
        } catch (\Exception $e) {
            Log::error('Nutritionix nutrition parsing failed', [
                'error' => $e->getMessage(),
                'text' => $text,
            ]);

            throw $e;
        }
    }

    /**
     * Format the Nutritionix API response to match our interface
     */
    private function formatResponse(array $data): array
    {
        $foods = $data['foods'] ?? [];
        $items = [];
        $totals = [
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
            'fiber' => 0,
            'sugar' => 0,
        ];

        foreach ($foods as $food) {
            $item = [
                'name' => $food['food_name'] ?? 'Unknown',
                'quantity' => $food['serving_weight_grams'] ?? 0,
                'unit' => $food['serving_unit'] ?? 'g',
                'calories' => $food['nf_calories'] ?? 0,
                'protein' => $food['nf_protein'] ?? 0,
                'carbs' => $food['nf_total_carbohydrate'] ?? 0,
                'fat' => $food['nf_total_fat'] ?? 0,
                'fiber' => $food['nf_dietary_fiber'] ?? 0,
                'sugar' => $food['nf_sugars'] ?? 0,
            ];

            $items[] = $item;

            $totals['calories'] += $item['calories'];
            $totals['protein'] += $item['protein'];
            $totals['carbs'] += $item['carbs'];
            $totals['fat'] += $item['fat'];
            $totals['fiber'] += $item['fiber'];
            $totals['sugar'] += $item['sugar'];
        }

        return [
            'items' => $items,
            'totals' => $totals,
        ];
    }
}
