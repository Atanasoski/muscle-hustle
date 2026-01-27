@extends('layouts.app')

@section('title', $plan->name . ' - Plan Details')

@section('content')
    <x-common.page-breadcrumb :pageTitle="$plan->name" :items="[['label' => 'Users', 'url' => route('users.index')], ['label' => $plan->user->name, 'url' => route('users.show', $plan->user)]]" />

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <p class="text-sm text-green-800 dark:text-green-400">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $plan->name }}
                </h2>
                @if($plan->description)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $plan->description }}</p>
                @endif
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                    Plan for <a href="{{ route('users.show', $plan->user) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $plan->user->name }}</a>
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if($plan->is_active)
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                        Active
                    </span>
                @else
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                        Inactive
                    </span>
                @endif
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
            <div class="space-y-4">
                @foreach($plan->workoutTemplates as $workout)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <h4 class="font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('workouts.show', $workout) }}" class="hover:text-brand-500 dark:hover:text-brand-400">
                                            {{ $workout->name }}
                                        </a>
                                    </h4>
                                    @if($workout->day_of_week !== null)
                                        @php
                                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                            {{ $days[$workout->day_of_week] }}
                                        </span>
                                    @endif
                                </div>
                                @if($workout->description)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $workout->description }}</p>
                                @endif
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    {{ $workout->workout_template_exercises_count ?? 0 }} exercises
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('workouts.show', $workout) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    View
                                </a>
                                <a href="{{ route('workouts.edit', $workout) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                                    Edit
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
