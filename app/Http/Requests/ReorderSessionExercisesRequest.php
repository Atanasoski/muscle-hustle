<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderSessionExercisesRequest extends FormRequest
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
            'exercise_ids' => 'required|array',
            'exercise_ids.*' => 'required|exists:workout_session_exercises,id',
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
            'exercise_ids.required' => 'Exercise IDs are required.',
            'exercise_ids.array' => 'Exercise IDs must be an array.',
            'exercise_ids.*.exists' => 'One or more exercise IDs do not exist.',
        ];
    }
}
