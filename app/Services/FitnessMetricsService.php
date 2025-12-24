<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FitnessMetricsService
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user->load('profile');
    }

    /**
     * Get all fitness metrics for the user.
     */
    public function getMetrics(): array
    {
        return [
            'strength_score' => $this->getStrengthScore(),
            'strength_balance' => $this->getStrengthBalance(),
            'weekly_progress' => $this->getWeeklyProgress(),
        ];
    }

    /**
     * Calculate strength score based on 1RM estimates and relative strength.
     */
    private function getStrengthScore(): array
    {
        $profile = $this->user->profile;
        if (! $profile || ! $profile->weight) {
            return $this->getDefaultStrengthScore();
        }

        $recent30Days = Carbon::now()->subDays(30);
        $previous30Days = Carbon::now()->subDays(60);

        // Get recent and previous period data
        $recentSets = $this->getUserSetLogs()->where('workout_sessions.performed_at', '>=', $recent30Days);
        $previousSets = $this->getUserSetLogs()
            ->where('workout_sessions.performed_at', '>=', $previous30Days)
            ->where('workout_sessions.performed_at', '<', $recent30Days);

        // Calculate weighted strength scores
        $currentScore = $this->calculateRelativeStrengthScore($recentSets, $profile->weight);
        $previousScore = $this->calculateRelativeStrengthScore($previousSets, $profile->weight);

        $recentGain = $currentScore - $previousScore;
        $level = $this->determineStrengthLevel($currentScore, $profile);

        return [
            'current' => (int) round($currentScore),
            'level' => $level,
            'recent_gain' => (int) round($recentGain),
            'gain_period' => 'last_30_days',
        ];
    }

    /**
     * Calculate strength balance across muscle groups.
     */
    private function getStrengthBalance(): array
    {
        $recent30Days = Carbon::now()->subDays(30);
        $previous30Days = Carbon::now()->subDays(60);

        // Get volume data for muscle groups
        $recentVolumes = $this->getMuscleGroupVolumes($recent30Days);
        $previousVolumes = $this->getMuscleGroupVolumes($previous30Days, $recent30Days);

        // Calculate balance
        $totalVolume = array_sum($recentVolumes);
        $muscleGroups = [];

        if ($totalVolume > 0) {
            foreach ($recentVolumes as $muscleGroup => $volume) {
                $muscleGroups[strtolower($muscleGroup)] = (int) round(($volume / $totalVolume) * 100);
            }
        }

        // Ensure we have all required muscle groups
        $requiredGroups = ['chest', 'back', 'legs', 'shoulders', 'arms', 'core'];
        foreach ($requiredGroups as $group) {
            if (! isset($muscleGroups[$group])) {
                $muscleGroups[$group] = 0;
            }
        }

        // Calculate balance percentage and level
        $balancePercentage = $this->calculateBalancePercentage($muscleGroups);
        $balanceLevel = $this->determineBalanceLevel($balancePercentage);

        // Calculate recent change
        $previousBalance = $this->calculateBalancePercentage(
            $this->getMuscleGroupPercentages($previousVolumes)
        );
        $recentChange = $balancePercentage - $previousBalance;

        return [
            'percentage' => (int) round($balancePercentage),
            'level' => $balanceLevel,
            'recent_change' => (int) round($recentChange),
            'muscle_groups' => $muscleGroups,
        ];
    }

    /**
     * Calculate weekly progress comparison.
     */
    private function getWeeklyProgress(): array
    {
        $currentWeekStart = Carbon::now()->startOfWeek(); // Monday
        $currentWeekEnd = Carbon::now()->endOfWeek(); // Sunday
        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $previousWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $currentWeekWorkouts = WorkoutSession::where('user_id', $this->user->id)
            ->whereNotNull('completed_at')
            ->whereBetween('performed_at', [$currentWeekStart, $currentWeekEnd])
            ->count();

        $previousWeekWorkouts = WorkoutSession::where('user_id', $this->user->id)
            ->whereNotNull('completed_at')
            ->whereBetween('performed_at', [$previousWeekStart, $previousWeekEnd])
            ->count();

        // Calculate percentage change
        $percentage = 0;
        $trend = 'same';

        if ($previousWeekWorkouts > 0) {
            $percentage = (($currentWeekWorkouts - $previousWeekWorkouts) / $previousWeekWorkouts) * 100;
        } elseif ($currentWeekWorkouts > 0) {
            $percentage = 100; // 100% increase from 0
        }

        if ($percentage > 0) {
            $trend = 'up';
        } elseif ($percentage < 0) {
            $trend = 'down';
        }

        return [
            'percentage' => (int) round(abs($percentage)),
            'trend' => $trend,
            'current_week_workouts' => $currentWeekWorkouts,
            'previous_week_workouts' => $previousWeekWorkouts,
        ];
    }

    /**
     * Get user's set logs with related data.
     */
    private function getUserSetLogs()
    {
        return DB::table('workout_set_logs')
            ->join('workout_sessions', 'workout_set_logs.workout_session_id', '=', 'workout_sessions.id')
            ->join('workout_exercises', 'workout_set_logs.exercise_id', '=', 'workout_exercises.id')
            ->join('categories', 'workout_exercises.category_id', '=', 'categories.id')
            ->where('workout_sessions.user_id', $this->user->id)
            ->whereNotNull('workout_sessions.completed_at')
            ->whereNotNull('workout_set_logs.weight')
            ->where('workout_set_logs.weight', '>', 0)
            ->where('workout_set_logs.reps', '>', 0)
            ->select([
                'workout_set_logs.*',
                'workout_sessions.performed_at',
                'workout_exercises.name as exercise_name',
                'categories.name as category_name',
            ]);
    }

    /**
     * Calculate relative strength score from set data.
     */
    private function calculateRelativeStrengthScore($sets, float $bodyWeight): float
    {
        $exerciseMaxes = [];

        foreach ($sets->get() as $set) {
            // Calculate 1RM using Epley formula: 1RM = weight × (1 + reps/30)
            $oneRM = $set->weight * (1 + ($set->reps / 30));

            // Keep track of best 1RM for each exercise
            $exerciseKey = $set->exercise_name;
            if (! isset($exerciseMaxes[$exerciseKey]) || $oneRM > $exerciseMaxes[$exerciseKey]) {
                $exerciseMaxes[$exerciseKey] = $oneRM;
            }
        }

        // Calculate relative strength: sum of best 1RMs / body weight
        $totalOneRM = array_sum($exerciseMaxes);
        $relativeStrength = $bodyWeight > 0 ? $totalOneRM / $bodyWeight : 0;

        // Scale to a more meaningful range (multiply by 100 for better UX)
        return $relativeStrength * 100;
    }

    /**
     * Get volume data by muscle group for a given period.
     */
    private function getMuscleGroupVolumes(Carbon $fromDate, ?Carbon $toDate = null): array
    {
        $query = $this->getUserSetLogs()->where('workout_sessions.performed_at', '>=', $fromDate);

        if ($toDate) {
            $query->where('workout_sessions.performed_at', '<', $toDate);
        }

        $volumes = [];
        foreach ($query->get() as $set) {
            $muscleGroup = $set->category_name;
            $volume = $set->weight * $set->reps; // Volume = weight × reps

            if (! isset($volumes[$muscleGroup])) {
                $volumes[$muscleGroup] = 0;
            }
            $volumes[$muscleGroup] += $volume;
        }

        return $volumes;
    }

    /**
     * Convert volumes to percentages.
     */
    private function getMuscleGroupPercentages(array $volumes): array
    {
        $total = array_sum($volumes);
        $percentages = [];

        if ($total > 0) {
            foreach ($volumes as $group => $volume) {
                $percentages[strtolower($group)] = ($volume / $total) * 100;
            }
        }

        return $percentages;
    }

    /**
     * Calculate balance percentage based on how evenly distributed the training is.
     */
    private function calculateBalancePercentage(array $muscleGroupPercentages): float
    {
        if (empty($muscleGroupPercentages)) {
            return 0;
        }

        $idealPercentage = 100 / count($muscleGroupPercentages);
        $totalDeviation = 0;

        foreach ($muscleGroupPercentages as $percentage) {
            $totalDeviation += abs($percentage - $idealPercentage);
        }

        // Convert deviation to balance score (lower deviation = higher balance)
        $maxPossibleDeviation = $idealPercentage * count($muscleGroupPercentages);
        $balanceScore = 100 - (($totalDeviation / $maxPossibleDeviation) * 100);

        return max(0, $balanceScore);
    }

    /**
     * Determine strength level based on score and demographics.
     */
    private function determineStrengthLevel(float $score, $profile): string
    {
        // Basic thresholds - in a real app, these would be more sophisticated
        // and factor in age, gender, training experience, etc.

        $thresholds = $this->getStrengthThresholds($profile);

        if ($score >= $thresholds['advanced']) {
            return 'ADVANCED';
        } elseif ($score >= $thresholds['intermediate']) {
            return 'INTERMEDIATE';
        } else {
            return 'BEGINNER';
        }
    }

    /**
     * Get strength thresholds based on user demographics.
     */
    private function getStrengthThresholds($profile): array
    {
        $base = ['beginner' => 0, 'intermediate' => 200, 'advanced' => 400];

        // Adjust thresholds based on demographics
        if ($profile) {
            // Gender adjustments
            if ($profile->gender?->value === 'female') {
                $base['intermediate'] *= 0.8;
                $base['advanced'] *= 0.8;
            }

            // Age adjustments
            if ($profile->age && $profile->age > 50) {
                $multiplier = max(0.7, 1 - (($profile->age - 50) * 0.01));
                $base['intermediate'] *= $multiplier;
                $base['advanced'] *= $multiplier;
            }
        }

        return $base;
    }

    /**
     * Determine balance level based on percentage.
     */
    private function determineBalanceLevel(float $percentage): string
    {
        if ($percentage >= 80) {
            return 'EXCELLENT';
        } elseif ($percentage >= 65) {
            return 'GOOD';
        } elseif ($percentage >= 50) {
            return 'FAIR';
        } else {
            return 'NEEDS_IMPROVEMENT';
        }
    }

    /**
     * Get default strength score for users without sufficient data.
     */
    private function getDefaultStrengthScore(): array
    {
        return [
            'current' => 0,
            'level' => 'BEGINNER',
            'recent_gain' => 0,
            'gain_period' => 'last_30_days',
        ];
    }
}
