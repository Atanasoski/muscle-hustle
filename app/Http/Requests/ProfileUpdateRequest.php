<?php

namespace App\Http\Requests;

use App\Enums\FitnessGoal;
use App\Enums\Gender;
use App\Enums\TrainingExperience;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'fitness_goal' => ['nullable', Rule::enum(FitnessGoal::class)],
            'age' => ['nullable', 'integer', 'min:1', 'max:150'],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'height' => ['nullable', 'integer', 'min:50', 'max:300'],
            'weight' => ['nullable', 'numeric', 'min:1', 'max:500'],
            'training_experience' => ['nullable', Rule::enum(TrainingExperience::class)],
            'training_days_per_week' => ['nullable', 'integer', 'min:1', 'max:7'],
            'workout_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
        ];
    }
}
