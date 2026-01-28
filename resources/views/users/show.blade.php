@extends('layouts.app')

@section('title', $user->name . ' - User Details')

@section('content')
<div class="w-full max-w-full overflow-x-hidden space-y-6">
    <x-common.page-breadcrumb :pageTitle="$user->name" :items="[['label' => 'Users', 'url' => route('users.index')]]" />

    <!-- Top Profile Header Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                @if($user->profile_photo)
                    <img src="{{ $user->profile_photo }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-full object-cover">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-500 text-2xl font-bold text-white">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <div class="min-w-0">
                    <h2 class="truncate text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ $user->name }}
                    </h2>
                    <div class="mt-1 flex flex-col gap-1 text-sm text-gray-500 dark:text-gray-400 sm:flex-row sm:items-center sm:gap-6">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0l-8 0m8 0V8m0 4v4"></path>
                            </svg>
                            <span class="truncate">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>User since {{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pills -->
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-center dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-[11px] font-semibold tracking-wider text-gray-400 dark:text-gray-500">AGE</div>
                    <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $profile?->age ?? '—' }}</div>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-center dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-[11px] font-semibold tracking-wider text-gray-400 dark:text-gray-500">WEIGHT</div>
                    <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $profile?->weight ? $profile->weight.'kg' : '—' }}
                    </div>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-center dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-[11px] font-semibold tracking-wider text-gray-400 dark:text-gray-500">HEIGHT</div>
                    <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $profile?->height ? $profile->height.'cm' : '—' }}
                    </div>
                </div>
                <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-center dark:border-blue-900/40 dark:bg-blue-900/10">
                    <div class="text-[11px] font-semibold tracking-wider text-blue-400">GOAL</div>
                    <div class="mt-1 text-sm font-semibold text-blue-700 dark:text-blue-300">
                        {{ $profile?->fitness_goal ? ucfirst(str_replace('_', ' ', $profile->fitness_goal->value)) : '—' }}
                    </div>
                </div>
                <div class="rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-center dark:border-green-900/40 dark:bg-green-900/10">
                    <div class="text-[11px] font-semibold tracking-wider text-green-500">LEVEL</div>
                    <div class="mt-1 text-sm font-semibold text-green-700 dark:text-green-300">
                        {{ $profile?->training_experience ? ucfirst($profile->training_experience->value) : '—' }}
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-start gap-2 text-sm text-gray-500 dark:text-gray-400 lg:justify-end">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Last login: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
            </div>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
            <div class="flex items-start justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-5">
                <div class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $totalWorkouts }}</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total Workouts</div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
            <div class="flex items-start justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-5">
                <div class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $completedWorkouts }}</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Completed</div>
                @if($completionRate !== null)
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $completionRate }}% completion rate</div>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
            <div class="flex items-start justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5c-2.305 0-4.45-.642-6.16-1.922L12 14z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-5">
                <div class="truncate text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $activePlan ? $activePlan->name : 'None' }}
                </div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Active Plan</div>
            </div>
        </div>
    </div>

    @if(isset($fitnessMetrics) && !empty($fitnessMetrics))
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
                <div class="flex items-start justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    @if(isset($fitnessMetrics['strength_score']['recent_gain']) && $fitnessMetrics['strength_score']['recent_gain'] != 0)
                        <div class="text-xs font-semibold {{ $fitnessMetrics['strength_score']['recent_gain'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $fitnessMetrics['strength_score']['recent_gain'] > 0 ? '+' : '' }}{{ $fitnessMetrics['strength_score']['recent_gain'] }}
                        </div>
                    @endif
                </div>
                <div class="mt-5">
                    <div class="text-3xl font-semibold text-gray-900 dark:text-white">
                        {{ $fitnessMetrics['strength_score']['current'] ?? 0 }}
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Strength Score</div>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">vs last 30 days</div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
                <div class="flex items-start justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    @if(isset($fitnessMetrics['strength_balance']['recent_change']) && $fitnessMetrics['strength_balance']['recent_change'] != 0)
                        <div class="text-xs font-semibold {{ $fitnessMetrics['strength_balance']['recent_change'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $fitnessMetrics['strength_balance']['recent_change'] > 0 ? '+' : '' }}{{ $fitnessMetrics['strength_balance']['recent_change'] }}%
                        </div>
                    @endif
                </div>
                <div class="mt-5">
                    <div class="text-3xl font-semibold text-gray-900 dark:text-white">
                        {{ $fitnessMetrics['strength_balance']['percentage'] ?? 0 }}%
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Strength Balance</div>
                    @if(isset($fitnessMetrics['strength_balance']['level']))
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ str_replace('_', ' ', $fitnessMetrics['strength_balance']['level']) }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
                <div class="flex items-start justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-50 text-slate-600 dark:bg-gray-900 dark:text-gray-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V5m4 14V9m4 10V7m4 12v-6m4 6V4"></path>
                        </svg>
                    </div>
                    @if(isset($fitnessMetrics['weekly_progress']['trend']) && isset($fitnessMetrics['weekly_progress']['percentage']))
                        <div class="text-xs font-semibold {{ $fitnessMetrics['weekly_progress']['trend'] === 'up' ? 'text-green-600 dark:text-green-400' : ($fitnessMetrics['weekly_progress']['trend'] === 'down' ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400') }}">
                            {{ $fitnessMetrics['weekly_progress']['trend'] === 'up' ? '+' : ($fitnessMetrics['weekly_progress']['trend'] === 'down' ? '-' : '') }}{{ $fitnessMetrics['weekly_progress']['percentage'] }}%
                        </div>
                    @endif
                </div>
                <div class="mt-5">
                    <div class="text-3xl font-semibold text-gray-900 dark:text-white">
                        @if($weeklyWorkouts !== null && $weeklyGoal)
                            {{ $weeklyWorkouts }}/{{ $weeklyGoal }}
                        @elseif($weeklyWorkouts !== null)
                            {{ $weeklyWorkouts }}
                        @else
                            0
                        @endif
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Weekly Progress</div>
                    @if($weeklyWorkouts !== null)
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $weeklyWorkouts }} workouts this week</div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Workout Frequency + Plans -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        @if(isset($weeklyWorkoutData) && !empty($weeklyWorkoutData))
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30 lg:col-span-2">
                <div class="mb-4">
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">Workout Frequency</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Last 12 weeks activity</div>
                </div>
                <div class="w-full overflow-x-auto">
                    <div id="userProgressChart" class="min-w-0"></div>
                </div>
                <script type="application/json" id="userProgressChartData">
                    {!! json_encode($weeklyWorkoutData) !!}
                </script>
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="text-lg font-semibold text-gray-900 dark:text-white">Your Plans</div>
                <a href="{{ route('plans.create', $user) }}" class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">
                    + Create Plan
                </a>
            </div>

            @if($user->plans->count() > 0)
                <div class="space-y-4">
                    @foreach($user->plans as $plan)
                        <div class="rounded-xl border border-gray-200 p-4 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900/40 {{ $plan->is_active ? 'bg-green-50/40 dark:bg-green-900/10 border-green-200 dark:border-green-900/40' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 110 4m0-4a2 2 0 100 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6 2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $plan->name }}
                                            </div>
                                            @if($plan->description)
                                                <div class="truncate text-sm text-gray-500 dark:text-gray-400">{{ $plan->description }}</div>
                                            @else
                                                <div class="text-sm text-gray-500 dark:text-gray-400">Plan</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between gap-3">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $plan->workout_templates_count ?? 0 }} templates
                                        </div>
                                        <a href="{{ route('plans.show', $plan) }}" class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">
                                            Manage
                                        </a>
                                    </div>
                                </div>

                                <div class="shrink-0">
                                    @if($plan->is_active)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border border-dashed border-gray-200 p-8 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    No workout plans yet
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Sessions -->
    <div class="flex items-center justify-between gap-3 pt-2">
        <div class="text-xl font-semibold text-gray-900 dark:text-white">Recent Sessions</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">View All History →</div>
    </div>

    @if($recentWorkouts->count() > 0)
        <div class="overflow-x-auto">
            <div class="flex min-w-full gap-4 pb-2">
                @foreach($recentWorkouts as $workout)
                    <div class="w-[260px] shrink-0 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg {{ $workout->completed_at ? 'bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-300' : 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-300' }}">
                                @if($workout->completed_at)
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.26a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $workout->completed_at ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' }}">
                                {{ $workout->completed_at ? 'Completed' : 'In Progress' }}
                            </span>
                        </div>

                        <div class="mt-4">
                            <div class="truncate text-base font-semibold text-gray-900 dark:text-white">
                                {{ $workout->workoutTemplate ? $workout->workoutTemplate->name : 'Custom Workout' }}
                            </div>
                            <div class="mt-3 space-y-2 text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>{{ $workout->performed_at->format('M d') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $workout->performed_at->format('g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-gray-200 p-8 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
            No workout sessions yet
        </div>
    @endif

</div>
@endsection
