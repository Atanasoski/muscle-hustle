@extends('layouts.app')

@section('title', $user->name . ' - User Details')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <div class="mb-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        Users
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">{{ $user->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                @if($user->profile_photo)
                    <img src="{{ $user->profile_photo }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full mr-4 object-cover">
                @else
                    <div class="w-16 h-16 rounded-full mr-4 bg-blue-500 flex items-center justify-center text-white font-bold text-2xl">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $user->name }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500">User since {{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-5 h-5 text-blue-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalWorkouts }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Workouts</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-5 h-5 text-green-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $completedWorkouts }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Completed</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-5 h-5 text-purple-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $activePlans }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Active Plans</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-5 h-5 text-orange-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                    @if($lastWorkout)
                        {{ $lastWorkout->performed_at->diffForHumans() }}
                    @else
                        Never
                    @endif
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Last Workout</p>
            </div>
        </div>
    </div>

    <!-- Fitness Metrics Cards -->
    @if(isset($fitnessMetrics) && !empty($fitnessMetrics))
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $fitnessMetrics['strength_score']['current'] ?? 0 }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Strength Score</p>
                    @if(isset($fitnessMetrics['strength_score']['recent_gain']) && $fitnessMetrics['strength_score']['recent_gain'] != 0)
                        <p class="text-xs mt-1 {{ $fitnessMetrics['strength_score']['recent_gain'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $fitnessMetrics['strength_score']['recent_gain'] > 0 ? '+' : '' }}{{ $fitnessMetrics['strength_score']['recent_gain'] }} (30d)
                        </p>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-teal-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $fitnessMetrics['strength_balance']['percentage'] ?? 0 }}%
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Strength Balance</p>
                    @if(isset($fitnessMetrics['strength_balance']['level']))
                        <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">
                            {{ str_replace('_', ' ', $fitnessMetrics['strength_balance']['level']) }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-cyan-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        @if(isset($fitnessMetrics['weekly_progress']['trend']))
                            @if($fitnessMetrics['weekly_progress']['trend'] === 'up')
                                <span class="text-green-500">+{{ $fitnessMetrics['weekly_progress']['percentage'] }}%</span>
                            @elseif($fitnessMetrics['weekly_progress']['trend'] === 'down')
                                <span class="text-red-500">-{{ $fitnessMetrics['weekly_progress']['percentage'] }}%</span>
                            @else
                                {{ $fitnessMetrics['weekly_progress']['percentage'] }}%
                            @endif
                        @else
                            0%
                        @endif
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Weekly Progress</p>
                    @if(isset($fitnessMetrics['weekly_progress']['current_week_workouts']))
                        <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">
                            {{ $fitnessMetrics['weekly_progress']['current_week_workouts'] }} workouts this week
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Weekly Workout Frequency Chart -->
    @if(isset($weeklyWorkoutData) && !empty($weeklyWorkoutData))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Workout Frequency (Last 12 Weeks)</h3>
            <div id="memberProgressChart"></div>
            <script type="application/json" id="memberProgressChartData">
                {!! json_encode($weeklyWorkoutData) !!}
            </script>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Profile Information</h3>
                @if($user->profile)
                    <div class="space-y-4">
                        @if($user->profile->age)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Age</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user->profile->age }} years</p>
                            </div>
                        @endif

                        @if($user->profile->gender)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Gender</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ ucfirst($user->profile->gender->value) }}</p>
                            </div>
                        @endif

                        @if($user->profile->height)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Height</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user->profile->height }} cm</p>
                            </div>
                        @endif

                        @if($user->profile->weight)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Weight</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user->profile->weight }} kg</p>
                            </div>
                        @endif

                        @if($user->profile->fitness_goal)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Fitness Goal</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $user->profile->fitness_goal->value)) }}</p>
                            </div>
                        @endif

                        @if($user->profile->training_experience)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Training Experience</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ ucfirst($user->profile->training_experience->value) }}</p>
                            </div>
                        @endif

                        @if($user->profile->training_days_per_week)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Training Days/Week</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user->profile->training_days_per_week }}</p>
                            </div>
                        @endif

                        @if($user->profile->workout_duration_minutes)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Workout Duration</p>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user->profile->workout_duration_minutes }} minutes</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No profile information available.</p>
                @endif

                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Last Login</p>
                    <p class="text-base font-medium text-gray-900 dark:text-white">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Workout Plans -->
            @if($user->plans->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Workout Plans</h3>
                    <div class="space-y-4">
                        @foreach($user->plans as $plan)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $plan->name }}</h4>
                                        @if($plan->description)
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $plan->description }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            {{ $plan->workout_templates_count ?? 0 }} workout templates
                                        </p>
                                    </div>
                                    <div>
                                        @if($plan->is_active)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                Inactive
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Workout Sessions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Recent Workout Sessions</h3>
                @if($recentWorkouts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Workout</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentWorkouts as $workout)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $workout->performed_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $workout->performed_at->format('g:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $workout->workoutTemplate ? $workout->workoutTemplate->name : 'Custom Workout' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($workout->completed_at)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                    Completed
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                    In Progress
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $recentWorkouts->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No workout sessions yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
