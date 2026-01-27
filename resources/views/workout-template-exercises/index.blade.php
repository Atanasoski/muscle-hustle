@extends('layouts.app')

@section('title', 'Exercises - ' . $workoutTemplate->name)

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Exercises'" :items="[['label' => 'Users', 'url' => route('users.index')], ['label' => $workoutTemplate->plan->user->name, 'url' => route('users.show', $workoutTemplate->plan->user)], ['label' => $workoutTemplate->plan->name, 'url' => route('plans.show', $workoutTemplate->plan)], ['label' => $workoutTemplate->name, 'url' => route('workouts.show', $workoutTemplate)]]" />

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
                    Exercises in {{ $workoutTemplate->name }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Manage exercises for this workout template
                </p>
            </div>
            <a href="{{ route('workout-exercises.create', $workoutTemplate) }}" class="inline-flex items-center justify-center rounded-lg border border-brand-500 bg-brand-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-brand-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Exercise
            </a>
        </div>
    </div>

    <!-- Exercises Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        @if($workoutTemplate->workoutTemplateExercises->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Exercise</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sets</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reps</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Weight (kg)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rest (sec)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($workoutTemplate->workoutTemplateExercises->sortBy('order') as $exercise)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $exercise->order }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $exercise->exercise->name }}</div>
                                    @if($exercise->exercise->muscleGroups->count() > 0)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $exercise->exercise->muscleGroups->pluck('name')->implode(', ') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $exercise->exercise->category->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $exercise->target_sets ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $exercise->target_reps ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $exercise->target_weight ? number_format($exercise->target_weight, 1) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $exercise->rest_seconds ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('workout-exercises.edit', [$workoutTemplate, $exercise]) }}" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>
                                    <form action="{{ route('workout-exercises.destroy', [$workoutTemplate, $exercise]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to remove this exercise?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No exercises yet</p>
                <a href="{{ route('workout-exercises.create', $workoutTemplate) }}" class="mt-4 inline-flex items-center justify-center rounded-lg border border-brand-500 bg-brand-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-brand-600">
                    Add First Exercise
                </a>
            </div>
        @endif
    </div>
@endsection
