<?php

namespace App\Services\Nutrition;

use App\Services\Nutrition\Contracts\NutritionParserInterface;
use App\Services\Nutrition\Parsers\NutritionixParser;
use App\Services\Nutrition\Parsers\OpenAINutritionParser;
use InvalidArgumentException;

class NutritionParserFactory
{
    /**
     * Create a nutrition parser instance
     *
     * @param  string|null  $parser  Parser type (openai, nutritionix) or null for default
     *
     * @throws InvalidArgumentException
     */
    public static function make(?string $parser = null): NutritionParserInterface
    {
        $parser = $parser ?? config('nutrition.default_parser', 'openai');

        return match ($parser) {
            'openai' => new OpenAINutritionParser,
            'nutritionix' => new NutritionixParser,
            default => throw new InvalidArgumentException("Nutrition parser [{$parser}] is not supported."),
        };
    }
}
