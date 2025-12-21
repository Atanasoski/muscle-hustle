<?php

namespace App\Http\Controllers\Api;

use App\Enums\FitnessGoal;
use App\Enums\Gender;
use App\Enums\TrainingExperience;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Get authenticated user.
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()->load(['partner', 'profile'])),
        ]);
    }

    /**
     * Update authenticated user's profile.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($request->user()->id),
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
        ]);

        $user = $request->user();

        // Update user fields
        $userFields = ['name', 'email', 'profile_photo'];
        $userData = array_intersect_key($validated, array_flip($userFields));
        if (! empty($userData)) {
            $user->fill($userData);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Store new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update or create user profile
        $profileFields = [
            'fitness_goal',
            'age',
            'gender',
            'height',
            'weight',
            'training_experience',
            'training_days_per_week',
            'workout_duration_minutes',
        ];
        $profileData = array_intersect_key($validated, array_flip($profileFields));

        if (! empty($profileData)) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user->load(['partner', 'profile'])),
        ]);
    }
}
