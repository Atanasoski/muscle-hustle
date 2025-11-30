<?php

namespace App\Http\Controllers;

use App\Services\Nutrition\NutritionParserFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NutritionParserController extends Controller
{
    /**
     * Parse nutrition text and return structured data
     */
    public function parse(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:2000',
            'parser' => 'nullable|string|in:openai,nutritionix',
        ]);

        try {
            $parser = NutritionParserFactory::make($request->input('parser'));
            $result = $parser->parse($request->input('text'));

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('Nutrition parsing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to parse nutrition data: '.$e->getMessage(),
            ], 500);
        }
    }
}
