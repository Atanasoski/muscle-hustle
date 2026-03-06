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
    | Exercise Count Safety Rails
    |--------------------------------------------------------------------------
    |
    | Absolute minimum and maximum exercise counts to prevent edge cases.
    | Duration is the primary constraint, but these provide safety boundaries.
    |
    */

    'exercise_count_safety' => [
        'min' => 3,
        'max' => 12,
    ],

    /*
    |--------------------------------------------------------------------------
    | Compound Exercise Ratios by Goal
    |--------------------------------------------------------------------------
    |
    | Target fraction (0.0-1.0) of exercises that should be compound movements
    | based on fitness goal. This influences exercise selection to maintain
    | appropriate compound-to-isolation balance.
    |
    */

    'compound_ratios' => [
        'strength' => 0.80,
        'muscle_gain' => 0.60,
        'fat_loss' => 0.60,
        'general_fitness' => 0.65,
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Time Buffer
    |--------------------------------------------------------------------------
    |
    | Percentage of session duration reserved for warm-up, transitions, and
    | other non-exercise activities. Applied as a buffer when calculating
    | available time for exercises.
    |
    */

    'session_time_buffer' => 0.10,

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
