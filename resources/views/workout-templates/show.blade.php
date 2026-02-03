@extends('layouts.app')

@section('title', $workoutTemplate->name . ' - Workout Template Details')

@section('content')
    <div x-data="{
        addExerciseModalOpen: false,
        exercises: @js($availableExercises),
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
        openAddExerciseModal() {
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
    <x-common.page-breadcrumb :pageTitle="$workoutTemplate->name" :items="[['label' => 'Users', 'url' => route('users.index')], ['label' => $workoutTemplate->plan->user->name, 'url' => route('users.show', $workoutTemplate->plan->user)], ['label' => $workoutTemplate->plan->name, 'url' => route('plans.show', $workoutTemplate->plan)]]" />

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <p class="text-sm text-green-800 dark:text-green-400">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Workout Template Header Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30 mb-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <h2 class="truncate text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $workoutTemplate->name }}
                </h2>
                @if($workoutTemplate->description)
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $workoutTemplate->description }}</p>
                @endif
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Part of <a href="{{ route('plans.show', $workoutTemplate->plan) }}" class="font-medium text-brand-600 hover:underline dark:text-brand-400">{{ $workoutTemplate->plan->name }}</a>
                    @if($dayName)
                        — {{ $dayName }}
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('plans.show', $workoutTemplate->plan) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Back to Plan
                </a>
                <a href="{{ route('workouts.edit', $workoutTemplate) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Edit
                </a>
                <form action="{{ route('workouts.destroy', $workoutTemplate) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this workout template? This will also delete all associated exercises.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 transition-colors hover:bg-red-50 dark:border-red-700 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900/20">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Exercises Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Exercises</h3>
            <button @click="openAddExerciseModal()" type="button" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-indigo-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Exercise
            </button>
        </div>

        @if($exercises->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Exercise</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sets</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reps</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Weight</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rest</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($exercises as $exercise)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $exercise->order }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $exercise->exercise->name }}</div>
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
                                    {{ $exercise->target_weight ? number_format($exercise->target_weight, 1) . ' kg' : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $exercise->rest_seconds ? $exercise->rest_seconds . 's' : '-' }}
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
                <button @click="openAddExerciseModal()" type="button" class="mt-4 inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-indigo-700">
                    Add First Exercise
                </button>
            </div>
        @endif
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
        <div x-show="addExerciseModalOpen"
            class="fixed inset-0 bg-gray-100 opacity-80 transition-opacity dark:bg-gray-900 dark:opacity-100"
            @click="closeAddExerciseModal"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="addExerciseModalOpen"
                class="relative w-full max-w-3xl transform overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl transition-all dark:border-gray-800 dark:bg-gray-900"
                @click.away="closeAddExerciseModal">

                <form action="{{ route('workout-exercises.store', $workoutTemplate) }}" method="POST">
                    @csrf

                    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white" id="add-exercise-modal-title">
                                    Add Exercise to Workout Template
                                </h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add an exercise to {{ $workoutTemplate->name }}</p>
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

                    <div class="max-h-[70vh] overflow-y-auto px-6 py-4">
                        <div class="space-y-5">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">
                                    Exercise <span class="text-red-500">*</span>
                                </label>
                                <input type="hidden" name="exercise_id" :value="selectedExerciseId">
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
                                <template x-if="!selectedExercise">
                                    <div class="space-y-4 rounded-lg border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            <input type="text"
                                                x-model="search"
                                                placeholder="Search exercises..."
                                                class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-4 text-sm outline-none transition-shadow focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-white dark:focus:ring-white/10" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Equipment Type</label>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="equipment in equipmentTypes" :key="equipment.id">
                                                    <button type="button"
                                                        @click="toggleEquipment(equipment.id)"
                                                        :class="selectedEquipment.includes(equipment.id) ? 'bg-gray-900 border-gray-900 text-white dark:bg-white dark:border-white dark:text-gray-900' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700'"
                                                        class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-medium transition-all duration-200">
                                                        <span x-text="equipment.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-2 flex items-center justify-between">
                                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Muscle Groups</label>
                                                <template x-if="selectedEquipment.length > 0 || selectedMuscles.length > 0">
                                                    <button type="button" @click="clearFilters()" class="text-xs font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Clear filters</button>
                                                </template>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="muscle in muscleGroups" :key="muscle.id">
                                                    <button type="button"
                                                        @click="toggleMuscle(muscle.id)"
                                                        :class="selectedMuscles.includes(muscle.id) ? 'bg-gray-900 border-gray-900 text-white dark:bg-white dark:border-white dark:text-gray-900' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700'"
                                                        class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-medium transition-all duration-200">
                                                        <span x-text="muscle.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Available Exercises (<span x-text="filteredExercises.length"></span>)
                                            </label>
                                            <div class="custom-scrollbar max-h-60 space-y-1 overflow-y-auto pr-1">
                                                <template x-if="filteredExercises.length === 0">
                                                    <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">No exercises found.</div>
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

                            <template x-if="selectedExerciseId">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sets</label>
                                        <input type="number" name="target_sets" x-model="targetSets" min="0" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10" />
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Reps</label>
                                        <input type="number" name="target_reps" x-model="targetReps" min="0" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10" />
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Weight (kg)</label>
                                        <input type="number" name="target_weight" x-model="targetWeight" min="0" step="0.1" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10" />
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Rest (seconds)</label>
                                        <input type="number" name="rest_seconds" x-model="restSeconds" min="0" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10" />
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="closeAddExerciseModal" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" :disabled="!selectedExerciseId" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">Add Exercise</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
