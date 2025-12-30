<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddSessionExerciseRequest extends FormRequest
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
            'exercise_id' => 'required|exists:workout_exercises,id',
            'order' => 'nullable|integer|min:0',
            'target_sets' => 'nullable|integer|min:1',
            'target_reps' => 'nullable|integer|min:1',
            'target_weight' => 'nullable|numeric|min:0',
            'rest_seconds' => 'nullable|integer|min:0',
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
            'exercise_id.required' => 'An exercise must be selected.',
            'exercise_id.exists' => 'The selected exercise does not exist.',
            'target_sets.min' => 'Target sets must be at least 1.',
            'target_reps.min' => 'Target reps must be at least 1.',
            'target_weight.min' => 'Target weight cannot be negative.',
            'rest_seconds.min' => 'Rest seconds cannot be negative.',
        ];
    }
}
