<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlanCloningService
{
    /**
     * Clone a plan with all its workout templates and exercises.
     *
     * This method performs a deep copy of a plan, including:
     * - The plan itself
     * - All workout templates
     * - All workout template exercises (pivot records)
     *
     * The cloned plan is assigned to the target user and marked as inactive.
     */
    public function clone(Plan $sourcePlan, User $targetUser): Plan
    {
        return DB::transaction(function () use ($sourcePlan, $targetUser) {
            // 1. Clone the plan
            $newPlan = $sourcePlan->replicate(['id', 'created_at', 'updated_at']);
            $newPlan->user_id = $targetUser->id;
            $newPlan->partner_id = null; // User now owns their copy
            $newPlan->is_active = false;
            $newPlan->save();

            // 2. Clone workout templates
            foreach ($sourcePlan->workoutTemplates as $template) {
                $newTemplate = $template->replicate(['id', 'created_at', 'updated_at']);
                $newTemplate->plan_id = $newPlan->id;
                $newTemplate->save();

                // 3. Clone workout template exercises (pivot records)
                foreach ($template->workoutTemplateExercises as $exercise) {
                    $newExercise = $exercise->replicate(['id', 'created_at', 'updated_at']);
                    $newExercise->workout_template_id = $newTemplate->id;
                    $newExercise->save();
                }
            }

            return $newPlan->fresh(['workoutTemplates.workoutTemplateExercises']);
        });
    }
}
