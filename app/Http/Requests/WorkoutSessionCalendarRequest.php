<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class WorkoutSessionCalendarRequest extends FormRequest
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
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
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
            'start_date.required' => 'The start date is required.',
            'start_date.date_format' => 'The start date must be in YYYY-MM-DD format.',
            'end_date.required' => 'The end date is required.',
            'end_date.date_format' => 'The end date must be in YYYY-MM-DD format.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->start_date && $this->end_date) {
                if ($this->start_date > $this->end_date) {
                    $validator->errors()->add('start_date', 'The start date must be before or equal to the end date.');
                }
            }
        });
    }
}
