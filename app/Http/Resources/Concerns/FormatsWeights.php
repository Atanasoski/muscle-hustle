<?php

namespace App\Http\Resources\Concerns;

trait FormatsWeights
{
    /**
     * Format weight value to remove unnecessary trailing zeros.
     */
    private function formatWeight(string|float|null $weight): float|int|null
    {
        if ($weight === null) {
            return null;
        }

        $floatValue = (float) $weight;

        if ($floatValue == (int) $floatValue) {
            return (int) $floatValue;
        }

        return $floatValue;
    }
}
