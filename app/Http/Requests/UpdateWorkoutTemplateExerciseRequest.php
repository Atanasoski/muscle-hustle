<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkoutTemplateExerciseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        if (! $user->hasRole('partner_admin')) {
            return false;
        }

        // Ensure workout template's plan belongs to the same partner
        $workoutTemplate = $this->route('workoutTemplate');
        if ($workoutTemplate) {
            $workoutTemplate->load('plan.user');

            return $user->partner_id === $workoutTemplate->plan->user->partner_id;
        }

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
            'order' => 'nullable|integer|min:0',
            'target_sets' => 'nullable|integer|min:0',
            'target_reps' => 'nullable|integer|min:0',
            'target_weight' => 'nullable|numeric|min:0',
            'rest_seconds' => 'nullable|integer|min:0',
        ];
    }
}
