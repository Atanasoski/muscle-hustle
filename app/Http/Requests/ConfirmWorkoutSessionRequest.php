<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmWorkoutSessionRequest extends FormRequest
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
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_id' => 'required|integer|exists:exercises,id',
            'exercises.*.order' => 'required|integer|min:1',
            'exercises.*.target_sets' => 'required|integer|min:1|max:20',
            'exercises.*.target_reps' => 'required|integer|min:1|max:100',
            'exercises.*.target_weight' => 'required|numeric|min:0',
            'exercises.*.rest_seconds' => 'required|integer|min:0|max:600',
            'rationale' => 'nullable|string|max:1000',
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
            'exercises.required' => 'At least one exercise is required.',
            'exercises.array' => 'Exercises must be an array.',
            'exercises.min' => 'At least one exercise is required.',
            'exercises.*.exercise_id.required' => 'Each exercise must have an exercise_id.',
            'exercises.*.exercise_id.exists' => 'One or more exercise IDs do not exist.',
            'exercises.*.order.required' => 'Each exercise must have an order.',
            'exercises.*.target_sets.required' => 'Each exercise must have target_sets.',
            'exercises.*.target_sets.min' => 'Target sets must be at least 1.',
            'exercises.*.target_reps.required' => 'Each exercise must have target_reps.',
            'exercises.*.target_reps.min' => 'Target reps must be at least 1.',
            'exercises.*.target_weight.required' => 'Each exercise must have target_weight.',
            'exercises.*.rest_seconds.required' => 'Each exercise must have rest_seconds.',
        ];
    }
}
