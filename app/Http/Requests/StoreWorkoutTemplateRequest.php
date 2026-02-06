<?php

namespace App\Http\Requests;

use App\Models\WorkoutTemplate;
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('day_of_week') && $this->day_of_week === '') {
            $this->merge(['day_of_week' => null]);
        }
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
                    $currentUser = auth()->user();
                    if (! $plan || ! $currentUser) {
                        return;
                    }
                    $planPartnerId = $plan->user_id === null
                        ? $plan->partner_id
                        : $plan->user?->partner_id;
                    if ($planPartnerId === null || $planPartnerId !== $currentUser->partner_id) {
                        $fail('The selected plan does not belong to your partner.');
                    }
                },
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'day_of_week' => [
                'nullable',
                'integer',
                'min:0',
                'max:6',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null) {
                        return;
                    }
                    $plan = $this->route('plan');
                    if (! $plan) {
                        return;
                    }
                    $existing = WorkoutTemplate::where('plan_id', $plan->id)
                        ->where('day_of_week', (int) $value)
                        ->first();
                    if ($existing) {
                        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $dayName = $dayNames[(int) $value] ?? 'Day '.$value;
                        $fail($dayName.' is already assigned to \''.$existing->name.'\'.');
                    }
                },
            ],
        ];
    }
}
