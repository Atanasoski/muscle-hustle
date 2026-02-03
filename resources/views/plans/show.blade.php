@extends('layouts.app')

@section('title', $plan->name . ' - Plan Details')

@section('content')
    <div x-data="{
        editModalOpen: false,
        editingWorkout: null,
        addExerciseModalOpen: false,
        selectedWorkout: null,
        exercises: [],
        equipmentTypes: @js($equipmentTypes),
        muscleGroups: @js($muscleGroups),
        search: '',
        selectedEquipment: [],
        selectedMuscles: [],
        selectedExerciseId: null,
        targetSets: 3,
        targetReps: 10,
        targetWeight: 0,
        restSeconds: 120,
        dayOfWeekOptions: @js($dayOfWeekOptions ?? [
            ['value' => '', 'letter' => '—', 'title' => 'No day assigned'],
            ['value' => '0', 'letter' => 'M', 'title' => 'Monday'],
            ['value' => '1', 'letter' => 'T', 'title' => 'Tuesday'],
            ['value' => '2', 'letter' => 'W', 'title' => 'Wednesday'],
            ['value' => '3', 'letter' => 'T', 'title' => 'Thursday'],
            ['value' => '4', 'letter' => 'F', 'title' => 'Friday'],
            ['value' => '5', 'letter' => 'S', 'title' => 'Saturday'],
            ['value' => '6', 'letter' => 'S', 'title' => 'Sunday'],
        ]),
        workoutExerciseData: @js($workoutExerciseData),
        openEditModal(workout) {
            this.editingWorkout = {
                id: workout.id,
                name: workout.name,
                description: workout.description || '',
                day_of_week: workout.day_of_week !== null ? workout.day_of_week.toString() : '',
                updateUrl: workout.updateUrl
            };
            this.editModalOpen = true;
        },
        closeEditModal() {
            this.editModalOpen = false;
            setTimeout(() => { this.editingWorkout = null; }, 300);
        },
        openAddExerciseModal(workoutId, workoutName) {
            this.selectedWorkout = { id: workoutId, name: workoutName };
            this.exercises = this.workoutExerciseData[workoutId] || [];
            this.search = '';
            this.selectedEquipment = [];
            this.selectedMuscles = [];
            this.selectedExerciseId = null;
            this.targetSets = 3;
            this.targetReps = 10;
            this.targetWeight = 0;
            this.restSeconds = 120;
            this.addExerciseModalOpen = true;
        },
        closeAddExerciseModal() {
            this.addExerciseModalOpen = false;
            setTimeout(() => {
                this.selectedWorkout = null;
                this.selectedExerciseId = null;
            }, 300);
        },
        toggleEquipment(id) {
            const index = this.selectedEquipment.indexOf(id);
            if (index > -1) {
                this.selectedEquipment.splice(index, 1);
            } else {
                this.selectedEquipment.push(id);
            }
        },
        toggleMuscle(id) {
            const index = this.selectedMuscles.indexOf(id);
            if (index > -1) {
                this.selectedMuscles.splice(index, 1);
            } else {
                this.selectedMuscles.push(id);
            }
        },
        get filteredExercises() {
            return this.exercises.filter(ex => {
                const q = this.search.trim().toLowerCase();
                const matchesSearch = q === '' ||
                    ex.name.toLowerCase().includes(q) ||
                    (ex.muscle_groups && ex.muscle_groups.some(mg => mg.name.toLowerCase().includes(q)));
                const primaryIds = ex.primary_muscle_group_ids || [];
                const matchesMuscle = this.selectedMuscles.length === 0 ||
                    primaryIds.some(id => this.selectedMuscles.includes(id));
                const matchesEquipment = this.selectedEquipment.length === 0 ||
                    (ex.equipment_type_id != null && this.selectedEquipment.includes(ex.equipment_type_id));
                return matchesSearch && matchesMuscle && matchesEquipment;
            });
        },
        get selectedExercise() {
            return this.exercises.find(ex => ex.id === this.selectedExerciseId);
        },
        clearFilters() {
            this.selectedEquipment = [];
            this.selectedMuscles = [];
        }
    }">
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
                    Plan for <a href="{{ route('users.show', $plan->user) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">{{ $plan->user->name }}</a>
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
            </div>
        </div>
    </div>

    <!-- Workout Templates Section -->
    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-800">
        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Workout Templates</h3>
            <a href="{{ route('workouts.create', $plan) }}" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Workout Template
            </a>
        </div>

        @if($plan->workoutTemplates->count() > 0)
            @php
                $dayColors = [
                    'Monday' => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800',
                    'Tuesday' => 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800',
                    'Wednesday' => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800',
                    'Thursday' => 'bg-orange-50 text-orange-600 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800',
                    'Friday' => 'bg-pink-50 text-pink-600 border-pink-200 dark:bg-pink-900/30 dark:text-pink-400 dark:border-pink-800',
                    'Saturday' => 'bg-cyan-50 text-cyan-600 border-cyan-200 dark:bg-cyan-900/30 dark:text-cyan-400 dark:border-cyan-800',
                    'Sunday' => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800',
                ];
            @endphp
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($plan->workoutTemplates as $workout)
                    <div class="group rounded-xl border border-gray-200 bg-white p-4 transition-all hover:border-gray-300 hover:shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:hover:border-gray-600">
                        <!-- Header Row -->
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div class="flex min-w-0 flex-1 items-start gap-3">
                                <!-- Icon -->
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                                <!-- Title & Description -->
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $workout->name }}
                                    </h3>
                                    <p class="mt-0.5 truncate text-xs text-gray-500 dark:text-gray-400">
                                        {{ $workout->description ?? 'No description' }}
                                    </p>
                                </div>
                            </div>
                            <!-- Day Badge -->
                            @if($workout->day_of_week !== null && isset($dayNames[$workout->day_of_week]))
                                @php
                                    $dayName = $dayNames[$workout->day_of_week];
                                    $colorClasses = $dayColors[$dayName] ?? 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700';
                                @endphp
                                <span class="shrink-0 rounded-full border px-2.5 py-1 text-xs font-medium {{ $colorClasses }}">
                                    {{ $dayName }}
                                </span>
                            @endif
                        </div>

                        <!-- Footer Row -->
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-800">
                            <!-- Exercise Count -->
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $workout->workout_template_exercises_count ?? 0 }} exercises
                            </span>

                            <!-- Quick Actions -->
                            <div class="flex items-center gap-1">
                                <button @click="openAddExerciseModal({{ $workout->id }}, '{{ addslashes($workout->name) }}')" type="button" class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-gray-600 transition-colors hover:bg-emerald-50 hover:text-emerald-600 dark:text-gray-400 dark:hover:bg-emerald-900/30 dark:hover:text-emerald-400">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Exercise
                                </button>

                                <button @click="openEditModal({
                                    id: {{ $workout->id }},
                                    name: '{{ addslashes($workout->name) }}',
                                    description: '{{ addslashes($workout->description ?? '') }}',
                                    day_of_week: {{ $workout->day_of_week ?? 'null' }},
                                    updateUrl: '{{ route('workouts.update', $workout) }}'
                                })" type="button" class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>

                                <a href="{{ route('workouts.show', $workout) }}" class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-gray-900 transition-colors hover:bg-gray-100 hover:text-gray-950 dark:text-gray-200 dark:hover:bg-gray-800 dark:hover:text-white">
                                    Manage
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No workout templates yet</p>
                <a href="{{ route('workouts.create', $plan) }}" class="mt-4 inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                    Create First Workout Template
                </a>
            </div>
        @endif
    </div>

    <!-- Edit Workout Template Modal -->
    <div x-show="editModalOpen"
        x-cloak
        @keydown.escape.window="closeEditModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">
        <!-- Backdrop -->
        <div x-show="editModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-gray-900 dark:bg-opacity-75"
            @click="closeEditModal"></div>

        <!-- Modal Panel -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="editModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-2xl transform overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl transition-all dark:border-gray-800 dark:bg-gray-900"
                @click.away="closeEditModal">

                <form :action="editingWorkout?.updateUrl" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Modal Header -->
                    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">
                                    Edit Workout Template
                                </h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Update workout template details
                                </p>
                            </div>
                            <button type="button"
                                @click="closeEditModal"
                                class="rounded-lg p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="max-h-[60vh] overflow-y-auto px-6 py-4">
                        <div class="space-y-5">
                            <!-- Workout Name -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Workout Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                    name="name"
                                    x-model="editingWorkout.name"
                                    required
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-white dark:focus:ring-white/10" />
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Description
                                </label>
                                <textarea name="description"
                                    x-model="editingWorkout.description"
                                    rows="4"
                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-white dark:focus:ring-white/10"></textarea>
                            </div>

                            <!-- Day of Week -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Day of Week
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="option in dayOfWeekOptions" :key="option.value">
                                        <label class="relative inline-flex h-10 min-w-10 cursor-pointer select-none">
                                            <input type="radio"
                                                name="day_of_week"
                                                :value="option.value"
                                                x-model="editingWorkout.day_of_week"
                                                class="peer sr-only" />
                                            <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm font-semibold text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-100 peer-checked:border-gray-900 peer-checked:bg-gray-900 peer-checked:text-white dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-500 dark:hover:bg-gray-700 dark:peer-checked:border-white dark:peer-checked:bg-white dark:peer-checked:text-gray-900 peer-checked:hidden"
                                                :title="option.title"
                                                x-text="option.letter"></span>
                                            <span class="absolute inset-0 hidden h-10 min-w-10 items-center justify-center rounded-lg border border-gray-900 bg-gray-900 text-white peer-checked:inline-flex dark:border-white dark:bg-white dark:text-gray-900"
                                                :title="option.title">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                        <div class="flex justify-end gap-3">
                            <button type="button"
                                @click="closeEditModal"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                                Update Workout Template
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Exercise Modal -->
    <div x-show="addExerciseModalOpen"
        x-cloak
        @keydown.escape.window="closeAddExerciseModal"
        class="modal fixed inset-0 flex items-center justify-center overflow-y-auto p-5"
        style="z-index: 999999 !important;"
        aria-labelledby="add-exercise-modal-title"
        role="dialog"
        aria-modal="true">
        <!-- Backdrop -->
        <div x-show="addExerciseModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-gray-900 dark:bg-opacity-75"
            @click="closeAddExerciseModal"></div>

        <!-- Modal Panel -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="addExerciseModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-3xl transform overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl transition-all dark:border-gray-800 dark:bg-gray-900"
                @click.away="closeAddExerciseModal">

                <form :action="selectedWorkout ? `/workouts/${selectedWorkout.id}/exercises` : '#'" method="POST">
                    @csrf

                    <!-- Modal Header -->
                    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white" id="add-exercise-modal-title">
                                    Add Exercise to Workout Template
                                </h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="selectedWorkout ? `Add an exercise to ${selectedWorkout.name}` : ''"></p>
                            </div>
                            <button type="button"
                                @click="closeAddExerciseModal"
                                class="rounded-lg p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="max-h-[70vh] overflow-y-auto px-6 py-4">
                        <div class="space-y-5">
                            <!-- Exercise Selector -->
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">
                                    Exercise <span class="text-red-500">*</span>
                                </label>

                                <!-- Hidden input for form submission -->
                                <input type="hidden" name="exercise_id" :value="selectedExerciseId">

                                <!-- Selected Exercise Display -->
                                <template x-if="selectedExercise">
                                    <div class="flex items-center justify-between rounded-lg border border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h2m-2 0V8m0 4v4m0-4H1m2 0h2m14-4h-2m2 0v4m0-4V8m0 4h2m-2 0h-2M6 12h12M6 12V8m0 4v4m12-4V8m0 4v4M6 12H4m14 0h2M6 16H4m14 0h2"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedExercise.name"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedExercise.equipment_type_name"></p>
                                            </div>
                                        </div>
                                        <button type="button"
                                            @click="selectedExerciseId = null"
                                            class="rounded-full p-2 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                <!-- Exercise Selector Panel -->
                                <template x-if="!selectedExercise">
                                    <div class="space-y-4 rounded-lg border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                        <!-- Search Input -->
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            <input type="text"
                                                x-model="search"
                                                placeholder="Search exercises..."
                                                class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-4 text-sm outline-none transition-shadow focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-white dark:focus:ring-white/10" />
                                        </div>

                                        <!-- Equipment Type Filters -->
                                        <div>
                                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Equipment Type
                                            </label>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="equipment in equipmentTypes" :key="equipment.id">
                                                    <button type="button"
                                                        @click="toggleEquipment(equipment.id)"
                                                        :class="selectedEquipment.includes(equipment.id)
                                                            ? 'bg-gray-900 border-gray-900 text-white dark:bg-white dark:border-white dark:text-gray-900'
                                                            : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700'"
                                                        class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-medium transition-all duration-200">
                                                        <span x-text="equipment.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Muscle Group Filters -->
                                        <div>
                                            <div class="mb-2 flex items-center justify-between">
                                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                    Muscle Groups
                                                </label>
                                                <template x-if="selectedEquipment.length > 0 || selectedMuscles.length > 0">
                                                    <button type="button"
                                                        @click="clearFilters()"
                                                        class="text-xs font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                                        Clear filters
                                                    </button>
                                                </template>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="muscle in muscleGroups" :key="muscle.id">
                                                    <button type="button"
                                                        @click="toggleMuscle(muscle.id)"
                                                        :class="selectedMuscles.includes(muscle.id)
                                                            ? 'bg-gray-900 border-gray-900 text-white dark:bg-white dark:border-white dark:text-gray-900'
                                                            : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700'"
                                                        class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-medium transition-all duration-200">
                                                        <span x-text="muscle.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Results List -->
                                        <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Available Exercises (<span x-text="filteredExercises.length"></span>)
                                            </label>
                                            <div class="custom-scrollbar max-h-60 space-y-1 overflow-y-auto pr-1">
                                                <template x-if="filteredExercises.length === 0">
                                                    <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                        No exercises found. Try adjusting your filters.
                                                    </div>
                                                </template>
                                                <template x-for="exercise in filteredExercises" :key="exercise.id">
                                                    <button type="button"
                                                        @click="selectedExerciseId = exercise.id"
                                                        class="group flex w-full items-center justify-between rounded-lg border border-transparent px-3 py-2 text-left transition-all hover:border-gray-200 hover:bg-white hover:shadow-sm dark:hover:border-gray-600 dark:hover:bg-gray-800">
                                                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white" x-text="exercise.name"></span>
                                                        <span class="text-xs text-gray-400 group-hover:text-gray-500 dark:text-gray-500" x-text="exercise.equipment_type_name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Form Fields (shown when exercise is selected) -->
                            <template x-if="selectedExerciseId">
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Sets -->
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Sets
                                        </label>
                                        <input type="number"
                                            name="target_sets"
                                            x-model="targetSets"
                                            min="0"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10" />
                                    </div>

                                    <!-- Reps -->
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Reps
                                        </label>
                                        <input type="number"
                                            name="target_reps"
                                            x-model="targetReps"
                                            min="0"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10" />
                                    </div>

                                    <!-- Weight -->
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Weight (kg)
                                        </label>
                                        <input type="number"
                                            name="target_weight"
                                            x-model="targetWeight"
                                            min="0"
                                            step="0.1"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10" />
                                    </div>

                                    <!-- Rest -->
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Rest (seconds)
                                        </label>
                                        <input type="number"
                                            name="rest_seconds"
                                            x-model="restSeconds"
                                            min="0"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10" />
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                        <div class="flex justify-end gap-3">
                            <button type="button"
                                @click="closeAddExerciseModal"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit"
                                :disabled="!selectedExerciseId"
                                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                                Add Exercise
                            </button>
                        </div>
                    </div>
                </form>
        </div>
    </div>
    </div>
@endsection
