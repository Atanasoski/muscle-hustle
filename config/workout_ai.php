<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Service
    |--------------------------------------------------------------------------
    |
    | This option controls the default AI service that will be used
    | when generating workout sessions. Supported: "openai"
    |
    */

    'default_service' => env('WORKOUT_AI_SERVICE', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the OpenAI workout generation service.
    |
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_WORKOUT_MODEL', 'gpt-4-turbo-preview'),
        'temperature' => env('OPENAI_WORKOUT_TEMPERATURE', 0.7),
        'max_tokens' => env('OPENAI_WORKOUT_MAX_TOKENS', 2000),
        'timeout' => env('OPENAI_WORKOUT_TIMEOUT', 30),
    ],

];
