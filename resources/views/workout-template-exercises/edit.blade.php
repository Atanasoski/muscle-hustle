@extends('layouts.app')

@section('title', 'Edit Exercise - ' . $workoutTemplateExercise->exercise->name)

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Edit Exercise'" :items="[['label' => 'Users', 'url' => route('users.index')], ['label' => $workoutTemplate->plan->user->name, 'url' => route('users.show', $workoutTemplate->plan->user)], ['label' => $workoutTemplate->plan->name, 'url' => route('plans.show', $workoutTemplate->plan)], ['label' => $workoutTemplate->name, 'url' => route('workouts.show', $workoutTemplate)], ['label' => 'Exercises', 'url' => route('workout-exercises.index', $workoutTemplate)]]" />

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
            <div class="mb-2 text-sm font-semibold text-red-800 dark:text-red-400">
                There were some errors with your submission:
            </div>
            <ul class="list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-common.component-card title="Edit Exercise in Workout Template" :desc="'Update exercise details for ' . $workoutTemplateExercise->exercise->name">
        <form action="{{ route('workout-exercises.update', [$workoutTemplate, $workoutTemplateExercise]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Exercise</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $workoutTemplateExercise->exercise->name }}</p>
                @if($workoutTemplateExercise->exercise->category)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $workoutTemplateExercise->exercise->category->name }}</p>
                @endif
            </div>

            <div class="space-y-5">
                <!-- Order -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Order
                    </label>
                    <input type="number"
                        name="order"
                        id="order"
                        value="{{ old('order', $workoutTemplateExercise->order) }}"
                        min="0"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('order') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                    @error('order')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The order in which this exercise appears in the workout (lower numbers appear first).</p>
                </div>

                <!-- Target Sets -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Target Sets
                    </label>
                    <input type="number"
                        name="target_sets"
                        id="target_sets"
                        value="{{ old('target_sets', $workoutTemplateExercise->target_sets) }}"
                        min="1"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('target_sets') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                    @error('target_sets')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Reps -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Target Reps
                    </label>
                    <input type="number"
                        name="target_reps"
                        id="target_reps"
                        value="{{ old('target_reps', $workoutTemplateExercise->target_reps) }}"
                        min="1"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('target_reps') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                    @error('target_reps')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Weight -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Target Weight (kg)
                    </label>
                    <input type="number"
                        name="target_weight"
                        id="target_weight"
                        value="{{ old('target_weight', $workoutTemplateExercise->target_weight) }}"
                        min="0"
                        step="0.1"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('target_weight') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                    @error('target_weight')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rest Seconds -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Rest Seconds
                    </label>
                    <input type="number"
                        name="rest_seconds"
                        id="rest_seconds"
                        value="{{ old('rest_seconds', $workoutTemplateExercise->rest_seconds) }}"
                        min="0"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('rest_seconds') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                    @error('rest_seconds')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                <a href="{{ route('workout-exercises.index', $workoutTemplate) }}">
                    <x-ui.button variant="outline" size="md">
                        Cancel
                    </x-ui.button>
                </a>
                <x-ui.button variant="primary" size="md" type="submit">
                    Update Exercise
                </x-ui.button>
            </div>
        </form>
    </x-common.component-card>
@endsection
