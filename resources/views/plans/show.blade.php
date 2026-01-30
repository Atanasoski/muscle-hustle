@extends('layouts.app')

@section('title', $plan->name . ' - Plan Details')

@section('content')
    <x-common.page-breadcrumb :pageTitle="$plan->name" :items="[['label' => 'Users', 'url' => route('users.index')], ['label' => $plan->user->name, 'url' => route('users.show', $plan->user)]]" />

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <p class="text-sm text-green-800 dark:text-green-400">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Plan Header Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30 mb-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <h2 class="truncate text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $plan->name }}
                </h2>
                @if($plan->description)
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $plan->description }}</p>
                @endif
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Plan for <a href="{{ route('users.show', $plan->user) }}" class="font-medium text-brand-600 hover:underline dark:text-brand-400">{{ $plan->user->name }}</a>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if($plan->is_active)
                    <div class="rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-center dark:border-green-900/40 dark:bg-green-900/10">
                        <div class="mt-1 text-sm font-semibold text-green-700 dark:text-green-300">Active</div>
                    </div>
                @else
                    <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-center dark:border-red-900/40 dark:bg-red-900/10">
                        <div class="mt-1 text-sm font-semibold text-red-700 dark:text-red-300">Inactive</div>
                    </div>
                @endif
                <a href="{{ route('plans.index', $plan->user) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Back to Plans
                </a>
                <a href="{{ route('plans.edit', $plan) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Edit
                </a>
                <form action="{{ route('plans.destroy', $plan) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this plan? This will also delete all associated workout templates.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 transition-colors hover:bg-red-50 dark:border-red-700 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900/20">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Workout Templates Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Workout Templates</h3>
            <a href="{{ route('workouts.create', $plan) }}" class="inline-flex items-center justify-center rounded-lg border border-brand-500 bg-brand-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-brand-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Workout Template
            </a>
        </div>

        @if($plan->workoutTemplates->count() > 0)
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($plan->workoutTemplates as $workout)
                    <div class="rounded-xl border border-gray-200 p-4 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900/40">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="truncate text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $workout->name }}
                                        </div>
                                        @if($workout->description)
                                            <div class="truncate text-sm text-gray-500 dark:text-gray-400">{{ $workout->description }}</div>
                                        @else
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Workout</div>
                                        @endif
                                    </div>
                                </div>
                                @if($workout->day_of_week !== null && isset($dayNames[$workout->day_of_week]))
                                    <div class="shrink-0">
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                            {{ $dayNames[$workout->day_of_week] }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $workout->workout_template_exercises_count ?? 0 }} exercises
                                </div>
                                <a href="{{ route('workouts.show', $workout) }}" class="flex items-center gap-1 text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">
                                    Manage
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No workout templates yet</p>
                <a href="{{ route('workouts.create', $plan) }}" class="mt-4 inline-flex items-center justify-center rounded-lg border border-brand-500 bg-brand-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-brand-600">
                    Create First Workout Template
                </a>
            </div>
        @endif
    </div>
@endsection
