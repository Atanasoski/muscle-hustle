<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkoutTemplateRequest extends FormRequest
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
            'plan_id' => [
                'required',
                'exists:plans,id',
                function ($attribute, $value, $fail) {
                    $plan = \App\Models\Plan::with('user')->find($value);
                    $user = auth()->user();
                    if ($plan && $plan->user->partner_id !== $user->partner_id) {
                        $fail('The selected plan does not belong to your partner.');
                    }
                },
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'day_of_week' => 'nullable|integer|min:0|max:6',
        ];
    }
}
