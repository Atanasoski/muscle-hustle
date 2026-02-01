@extends('layouts.app')

@section('title', 'Add Exercise to ' . $workoutTemplate->name)

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Add Exercise'" :items="[['label' => 'Users', 'url' => route('users.index')], ['label' => $workoutTemplate->plan->user->name, 'url' => route('users.show', $workoutTemplate->plan->user)], ['label' => $workoutTemplate->plan->name, 'url' => route('plans.show', $workoutTemplate->plan)], ['label' => $workoutTemplate->name, 'url' => route('workouts.show', $workoutTemplate)], ['label' => 'Exercises', 'url' => route('workout-exercises.index', $workoutTemplate)]]" />

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

    <x-common.component-card title="Add Exercise to Workout Template" :desc="'Add an exercise to ' . $workoutTemplate->name">
        <form action="{{ route('workout-exercises.store', $workoutTemplate) }}" method="POST" @keydown.escape.window="window.location.href='{{ route('workout-exercises.index', $workoutTemplate) }}'">
            @csrf

            <div class="space-y-6">
                <!-- Exercise Selection -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Exercise <span class="text-red-500">*</span>
                    </label>
                    <x-exercise-selector 
                        :exercises="$exercises" 
                        :equipment-types="$equipmentTypes" 
                        :muscle-groups="$muscleGroups"
                        :selected-exercise-id="old('exercise_id')"
                    />
                    @error('exercise_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Only exercises linked to your partner are shown. Exercises already in this workout are hidden.</p>
                </div>

                <!-- Targets Section (Order, Sets, Reps, Weight, Rest in one row) -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Targets <span class="font-normal text-gray-400 dark:text-gray-500">(optional)</span>
                    </label>

                    <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
                        <!-- Order -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">
                                Order
                            </label>
                            <input type="number"
                                name="order"
                                id="order"
                                value="{{ old('order') }}"
                                min="0"
                                placeholder="{{ $maxOrder + 1 }}"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-9 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('order') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                            @error('order')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sets -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">
                                Sets
                            </label>
                            <div class="relative">
                                <input type="number"
                                    name="target_sets"
                                    id="target_sets"
                                    value="{{ old('target_sets') }}"
                                    min="0"
                                    step="1"
                                    placeholder=""
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-9 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('target_sets') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                                <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-xs text-gray-400 dark:text-gray-500">sets</span>
                            </div>
                            @error('target_sets')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reps -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">
                                Reps
                            </label>
                            <div class="relative">
                                <input type="number"
                                    name="target_reps"
                                    id="target_reps"
                                    value="{{ old('target_reps') }}"
                                    min="0"
                                    step="1"
                                    placeholder=""
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-9 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('target_reps') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                                <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-xs text-gray-400 dark:text-gray-500">reps</span>
                            </div>
                            @error('target_reps')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Weight -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">
                                Weight
                            </label>
                            <div class="relative">
                                <input type="number"
                                    name="target_weight"
                                    id="target_weight"
                                    value="{{ old('target_weight') }}"
                                    min="0"
                                    step="0.1"
                                    placeholder=""
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-9 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('target_weight') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                                <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-xs text-gray-400 dark:text-gray-500">kg</span>
                            </div>
                            @error('target_weight')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rest -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-500 dark:text-gray-400">
                                Rest
                            </label>
                            <div class="relative">
                                <input type="number"
                                    name="rest_seconds"
                                    id="rest_seconds"
                                    value="{{ old('rest_seconds') }}"
                                    min="0"
                                    step="1"
                                    placeholder=""
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-9 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('rest_seconds') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                                <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-xs text-gray-400 dark:text-gray-500">sec</span>
                            </div>
                            @error('rest_seconds')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Order is automatically set if left empty.
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                <a href="{{ route('workout-exercises.index', $workoutTemplate) }}">
                    <x-ui.button variant="outline" size="md" type="button">
                        Cancel
                    </x-ui.button>
                </a>
                <x-ui.button variant="primary" size="md" type="submit">
                    Add to Workout
                </x-ui.button>
            </div>
        </form>
    </x-common.component-card>
@endsection
