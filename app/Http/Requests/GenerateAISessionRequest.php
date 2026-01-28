<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateAISessionRequest extends FormRequest
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
            'duration_minutes' => 'nullable|integer|min:15|max:180',
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'string|exists:categories,slug',
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
            'duration_minutes.integer' => 'Duration must be an integer.',
            'duration_minutes.min' => 'Duration must be at least 15 minutes.',
            'duration_minutes.max' => 'Duration must not exceed 180 minutes.',
            'preferred_categories.array' => 'Preferred categories must be an array.',
            'preferred_categories.*.exists' => 'One or more category slugs do not exist.',
            'difficulty.in' => 'Difficulty must be one of: beginner, intermediate, advanced.',
        ];
    }
}
