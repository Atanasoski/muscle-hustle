<?php

namespace App\Services\Nutrition\Contracts;

interface NutritionParserInterface
{
    /**
     * Parse nutrition text and return structured data
     *
     * @param  string  $text  The text to parse (e.g., "2 chicken breasts, 1 cup rice")
     * @return array {
     *               'items' => [
     *               [
     *               'name' => string,
     *               'quantity' => float,
     *               'unit' => string,
     *               'calories' => float,
     *               'protein' => float,
     *               'carbs' => float,
     *               'fat' => float,
     *               'fiber' => float,
     *               'sugar' => float,
     *               ],
     *               ...
     *               ],
     *               'totals' => [
     *               'calories' => float,
     *               'protein' => float,
     *               'carbs' => float,
     *               'fat' => float,
     *               'fiber' => float,
     *               'sugar' => float,
     *               ]
     *               }
     */
    public function parse(string $text): array;
}
