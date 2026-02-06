@extends('layouts.app')

@section('title', $plan->name . ' - Plan Details')

@section('content')
    <div x-data="{
        editModalOpen: false,
        editingWorkout: null,
        addWorkoutModalOpen: false,
        dayOfWeekOptions: [
            { value: '', letter: '—', title: 'Unassigned' },
            { value: 0, letter: 'M', title: 'Monday' },
            { value: 1, letter: 'T', title: 'Tuesday' },
            { value: 2, letter: 'W', title: 'Wednesday' },
            { value: 3, letter: 'T', title: 'Thursday' },
            { value: 4, letter: 'F', title: 'Friday' },
            { value: 5, letter: 'S', title: 'Saturday' },
            { value: 6, letter: 'S', title: 'Sunday' }
        ],
        openEditModal(workout) {
            this.editingWorkout = workout;
            this.editModalOpen = true;
        },
        closeEditModal() {
            this.editModalOpen = false;
            this.editingWorkout = null;
        },
        addWorkoutDayOld: @js(old('day_of_week')),
        openAddWorkoutModal() {
            this.addWorkoutModalOpen = true;
        },
        closeAddWorkoutModal() {
            this.addWorkoutModalOpen = false;
        }
    }" x-init="if ({{ Js::encode($errors->hasAny(['name','description','day_of_week']) && old('plan_id') == $plan->id) }}) { addWorkoutModalOpen = true }">
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
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    @if($plan->isProgram())
                        <span class="inline-flex items-center rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                            Program
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                            Custom Plan
                        </span>
                    @endif
                    @if($plan->isProgram() && $plan->duration_weeks)
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            • {{ $plan->duration_weeks }} {{ Str::plural('week', $plan->duration_weeks) }}
                        </span>
                    @endif
                </div>
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
            <button type="button" @click="openAddWorkoutModal()" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Workout Template
            </button>
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

                        <!-- Program Info (Week & Order) -->
                        @if($plan->isProgram())
                            <div class="mb-3 flex items-center gap-2">
                                <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                    Week {{ $workout->week_number }}
                                </span>
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                    Order {{ $workout->order_index + 1 }}
                                </span>
                            </div>
                        @endif

                        <!-- Footer Row -->
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-800">
                            <!-- Exercise Count -->
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $workout->workout_template_exercises_count ?? 0 }} exercises
                            </span>

                            <!-- Quick Actions -->
                            <div class="flex items-center gap-1">

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
                                    Exercises
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>

                                <form action="{{ route('workouts.destroy', $workout) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this workout template? This will also delete all associated exercises.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-red-600 transition-colors hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
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
                <button type="button" @click="openAddWorkoutModal()" class="mt-4 inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                    Create First Workout Template
                </button>
            </div>
        @endif
    </div>

    <!-- Edit Workout Template Modal -->
    <div x-show="editModalOpen"
        x-cloak
        @keydown.escape.window="closeEditModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="z-index: 999999 !important;"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">
        <!-- Backdrop -->
        <div x-show="editModalOpen"
            class="fixed inset-0 bg-gray-100 opacity-80 transition-opacity dark:bg-gray-900 dark:opacity-100"
            @click="closeEditModal"></div>

        <!-- Modal Panel (only render form when editingWorkout is set to avoid null reference on load) -->
        <div class="flex min-h-full items-center justify-center p-4">
            <template x-if="editingWorkout">
                <div x-show="editModalOpen"
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
            </template>
        </div>
    </div>

    <!-- Add Workout Template Modal -->
    <div x-show="addWorkoutModalOpen"
        x-cloak
        @keydown.escape.window="closeAddWorkoutModal()"
        class="modal fixed inset-0 flex items-center justify-center overflow-y-auto p-5"
        style="z-index: 999999 !important;"
        aria-labelledby="add-workout-modal-title"
        role="dialog"
        aria-modal="true">
        <div x-show="addWorkoutModalOpen"
            class="fixed inset-0 bg-gray-100 opacity-80 transition-opacity dark:bg-gray-900 dark:opacity-100"
            @click="closeAddWorkoutModal()"></div>

        <div class="flex min-h-full w-full items-center justify-center p-4">
            <div x-show="addWorkoutModalOpen"
                class="relative w-full max-w-2xl transform overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl transition-all dark:border-gray-800 dark:bg-gray-900"
                @click.away="closeAddWorkoutModal()">

                <form action="{{ route('workouts.store', $plan) }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white" id="add-workout-modal-title">
                                    Add Workout Template
                                </h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a new workout template for this plan.</p>
                            </div>
                            <button type="button"
                                @click="closeAddWorkoutModal()"
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
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">Workout Name <span class="text-red-500">*</span></label>
                                <input type="text"
                                    name="name"
                                    id="add-workout-name"
                                    required
                                    value="{{ old('name') }}"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none transition-shadow focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10"
                                    placeholder="e.g. Day 1 - Push" />
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">Description</label>
                                <textarea name="description"
                                    id="add-workout-description"
                                    rows="4"
                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none transition-shadow focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10"
                                    placeholder="Optional description">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">Day of Week</label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="option in dayOfWeekOptions" :key="option.value">
                                        <label class="relative inline-flex h-10 min-w-10 cursor-pointer select-none">
                                            <input type="radio"
                                                name="day_of_week"
                                                :value="option.value === '' ? '' : option.value"
                                                :checked="(option.value === '' && (addWorkoutDayOld === null || addWorkoutDayOld === '')) || (option.value !== '' && addWorkoutDayOld == option.value)"
                                                class="peer sr-only" />
                                            <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm font-semibold text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-100 peer-checked:border-gray-900 peer-checked:bg-gray-900 peer-checked:text-white dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-500 dark:hover:bg-gray-700 dark:peer-checked:border-gray-900 dark:peer-checked:bg-gray-900 peer-checked:hidden"
                                                :title="option.title"
                                                x-text="option.letter"></span>
                                            <span class="absolute inset-0 hidden h-10 min-w-10 items-center justify-center rounded-lg border border-gray-900 bg-gray-900 text-white peer-checked:inline-flex dark:border-gray-900 dark:bg-gray-900"
                                                :title="option.title">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </span>
                                        </label>
                                    </template>
                                </div>
                                @error('day_of_week')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="closeAddWorkoutModal()" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">Create Workout Template</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
