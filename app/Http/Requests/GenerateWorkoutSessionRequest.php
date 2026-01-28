<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateWorkoutSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'focus_muscle_groups' => 'nullable|array',
            'focus_muscle_groups.*' => 'string|max:255',
            'target_regions' => 'nullable|array',
            'target_regions.*' => 'string|exists:target_regions,code',
            'equipment_types' => 'nullable|array',
            'equipment_types.*' => 'string|exists:equipment_types,code',
            'movement_patterns' => 'nullable|array',
            'movement_patterns.*' => 'string|exists:movement_patterns,code',
            'angles' => 'nullable|array',
            'angles.*' => 'string|exists:angles,code',
            'duration_minutes' => 'nullable|integer|min:15|max:180',
            'difficulty' => [
                'nullable',
                Rule::in(['beginner', 'intermediate', 'advanced']),
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'focus_muscle_groups.array' => 'Focus muscle groups must be an array.',
            'focus_muscle_groups.*.string' => 'Each muscle group must be a string.',
            'target_regions.array' => 'Target regions must be an array.',
            'target_regions.*.exists' => 'One or more target region codes do not exist.',
            'equipment_types.array' => 'Equipment types must be an array.',
            'equipment_types.*.exists' => 'One or more equipment type codes do not exist.',
            'movement_patterns.array' => 'Movement patterns must be an array.',
            'movement_patterns.*.exists' => 'One or more movement pattern codes do not exist.',
            'angles.array' => 'Angles must be an array.',
            'angles.*.exists' => 'One or more angle codes do not exist.',
            'duration_minutes.integer' => 'Duration must be an integer.',
            'duration_minutes.min' => 'Duration must be at least 15 minutes.',
            'duration_minutes.max' => 'Duration must not exceed 180 minutes.',
            'difficulty.in' => 'Difficulty must be one of: beginner, intermediate, advanced.',
        ];
    }
}
