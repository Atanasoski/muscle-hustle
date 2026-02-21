<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Movement Pattern Classification
    |--------------------------------------------------------------------------
    |
    | Compound movements are multi-joint exercises that should be prioritized
    | at the beginning of a workout. Isolation movements target single muscles
    | and are typically performed later in the workout.
    |
    */

    'compound_patterns' => [
        'PRESS',
        'ROW',
        'SQUAT',
        'HINGE',
        'PULL_VERTICAL',
        'LEG_PRESS',
        'DIP',
        'LUNGE_SPLIT_SQUAT',
        'HIP_THRUST_BRIDGE',
        'PUSHUP',
    ],

    'isolation_patterns' => [
        'FLY',
        'ELBOW_FLEXION',
        'ELBOW_EXTENSION',
        'KNEE_FLEXION',
        'KNEE_EXTENSION',
        'CALF_RAISE',
        'REAR_DELT_FLY',
        'FACE_PULL',
        'PULLOVER_STRAIGHT_ARM',
        'HIP_ABDUCTION',
        'TRUNK_FLEXION',
        'ROTATION',
    ],

    /*
    |--------------------------------------------------------------------------
    | Duration Estimation
    |--------------------------------------------------------------------------
    |
    | Average time in seconds for completing a single set, used to estimate
    | total workout duration.
    |
    */

    'set_duration_seconds' => 45,

    /*
    |--------------------------------------------------------------------------
    | Exercise Limits
    |--------------------------------------------------------------------------
    |
    | Maximum and minimum exercises per target region, movement pattern, and total workout.
    |
    */

    'max_exercises_per_region' => 4,
    'min_exercises_per_region' => 1,
    'max_exercises_per_pattern' => 4,
    'max_total_exercises' => 10,
    'min_total_exercises' => 4,

    /*
    |--------------------------------------------------------------------------
    | Fitness Goal Defaults
    |--------------------------------------------------------------------------
    |
    | Default sets, reps, and rest periods based on fitness goal.
    | These are used when user has no exercise history.
    |
    */

    'fitness_goal_defaults' => [
        'strength' => [
            'sets' => 4,
            'reps' => 5,
            'rest_seconds' => 180,
        ],
        'muscle_gain' => [
            'sets' => 4,
            'reps' => 10,
            'rest_seconds' => 90,
        ],
        'fat_loss' => [
            'sets' => 3,
            'reps' => 15,
            'rest_seconds' => 45,
        ],
        'general_fitness' => [
            'sets' => 3,
            'reps' => 12,
            'rest_seconds' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Exercise Count Targets by Goal and Experience
    |--------------------------------------------------------------------------
    |
    | Defines minimum, maximum, and compound ratio for exercises based on
    | user's fitness goal and training experience level.
    |
    | - min: Minimum number of exercises to include
    | - max: Maximum number of exercises to include
    | - compound_ratio: Fraction (0.0-1.0) of exercises that should be compound
    |
    */

    'exercise_count_targets' => [
        'strength' => [
            'beginner' => [
                'min' => 3,
                'max' => 5,
                'compound_ratio' => 1.0,
            ],
            'intermediate' => [
                'min' => 4,
                'max' => 6,
                'compound_ratio' => 0.80,
            ],
            'advanced' => [
                'min' => 5,
                'max' => 7,
                'compound_ratio' => 0.70,
            ],
        ],
        'muscle_gain' => [
            'beginner' => [
                'min' => 4,
                'max' => 6,
                'compound_ratio' => 0.80,
            ],
            'intermediate' => [
                'min' => 5,
                'max' => 8,
                'compound_ratio' => 0.60,
            ],
            'advanced' => [
                'min' => 6,
                'max' => 10,
                'compound_ratio' => 0.50,
            ],
        ],
        'fat_loss' => [
            'beginner' => [
                'min' => 4,
                'max' => 6,
                'compound_ratio' => 0.75,
            ],
            'intermediate' => [
                'min' => 5,
                'max' => 8,
                'compound_ratio' => 0.60,
            ],
            'advanced' => [
                'min' => 6,
                'max' => 10,
                'compound_ratio' => 0.50,
            ],
        ],
        'general_fitness' => [
            'beginner' => [
                'min' => 4,
                'max' => 6,
                'compound_ratio' => 0.75,
            ],
            'intermediate' => [
                'min' => 5,
                'max' => 7,
                'compound_ratio' => 0.60,
            ],
            'advanced' => [
                'min' => 5,
                'max' => 8,
                'compound_ratio' => 0.50,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Time Estimation Modifiers
    |--------------------------------------------------------------------------
    |
    | Modifiers for time estimation based on exercise type.
    | These are used to differentiate compound vs isolation exercise time.
    |
    */

    'compound_time_modifier' => 1.0,
    'isolation_time_modifier' => 0.5,
];
