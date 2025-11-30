<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Nutrition Parser
    |--------------------------------------------------------------------------
    |
    | This option controls the default nutrition parser that will be used
    | when parsing food text. Supported: "openai", "nutritionix"
    |
    */

    'default_parser' => env('NUTRITION_PARSER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the OpenAI nutrition parser.
    |
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Nutritionix Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Nutritionix nutrition parser.
    | Get your keys at: https://www.nutritionix.com/business/api
    |
    */

    'nutritionix' => [
        'app_id' => env('NUTRITIONIX_APP_ID'),
        'app_key' => env('NUTRITIONIX_APP_KEY'),
    ],

];
