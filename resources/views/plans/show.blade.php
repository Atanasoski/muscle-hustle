@extends('layouts.app')

@section('title', $plan->name . ' - Program Details')

@section('content')
    <div x-data="{
        editModalOpen: false,
        editingWorkout: null,
        addWorkoutModalOpen: false,
        editProgramModalOpen: false,
        {{-- day_of_week (commented out)
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
        --}}
        openEditModal(workout) {
            this.editingWorkout = workout;
            this.editModalOpen = true;
        },
        closeEditModal() {
            this.editModalOpen = false;
            this.editingWorkout = null;
        },
        {{-- addWorkoutDayOld: @js(old('day_of_week')), --}}
        openAddWorkoutModal() {
            this.addWorkoutModalOpen = true;
        },
        closeAddWorkoutModal() {
            this.addWorkoutModalOpen = false;
        },
        openEditProgramModal() {
            this.editProgramModalOpen = true;
        },
        closeEditProgramModal() {
            this.editProgramModalOpen = false;
        }
    }" x-init="
        if ({{ Js::encode($errors->hasAny(['name','description']) && old('plan_id') == $plan->id) }}) { addWorkoutModalOpen = true; }
        if ({{ Js::encode($errors->hasAny(['name','description','duration_weeks']) && session('edit_plan_id') == $plan->id) }}) { editProgramModalOpen = true; }
    ">
    <x-common.page-breadcrumb :pageTitle="$plan->name" :items="[['label' => 'Programs', 'url' => route('partner.programs.index')]]" />

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <p class="text-sm text-green-800 dark:text-green-400">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Program Header Card -->
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
                    <span class="inline-flex items-center rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                        Library Program
                    </span>
                    @if($plan->duration_weeks)
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            • {{ $plan->duration_weeks }} {{ Str::plural('week', $plan->duration_weeks) }}
                        </span>
                    @endif
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Available to all gym members via mobile app
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" @click="openEditProgramModal()" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Program
                </button>
                <a href="{{ route('partner.programs.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Back to Programs
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
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($plan->workoutTemplates as $template)
                    <div class="relative rounded-lg border border-gray-200 bg-white p-4 transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-900">
                        <div class="mb-3 flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $template->name }}</h4>
                                @if($template->description)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $template->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3 flex flex-wrap gap-2">
                            @if($template->week_number)
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    Week {{ $template->week_number }}
                                </span>
                            @endif
                            @if($template->order_index !== null)
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    Order: {{ $template->order_index + 1 }}
                                </span>
                            @endif
                            {{-- day_of_week badge (commented out)
                            @if($template->day_of_week !== null)
                                <span class="inline-flex items-center rounded-full bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                    {{ $dayNames[$template->day_of_week] }}
                                </span>
                            @endif
                            --}}
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-800">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $template->workout_template_exercises_count }} {{ Str::plural('exercise', $template->workout_template_exercises_count) }}
                            </span>
                            <div class="flex gap-2">
                                <a href="{{ route('workouts.show', $template) }}"
                                    class="rounded-lg p-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                    title="View Details">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <button type="button"
                                    @click="openEditModal({{ Js::from(['id' => $template->id, 'name' => $template->name, 'description' => $template->description ?? '', 'updateUrl' => route('workouts.update', $template)]) }})"
                                    class="rounded-lg p-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                    title="Edit">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('workouts.destroy', $template) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this workout template? This will also delete all associated exercises.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded-lg p-1.5 text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300"
                                        title="Delete">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No workout templates</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding workout templates to this program.</p>
                <div class="mt-6">
                    <button type="button" @click="openAddWorkoutModal()"
                        class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Workout Template
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Delete Program Section -->
    <div class="rounded-lg border border-red-200 bg-red-50 p-6 dark:border-red-900/40 dark:bg-red-900/10">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-base font-medium text-red-900 dark:text-red-300">Delete Program</h3>
                <p class="mt-1 text-sm text-red-700 dark:text-red-400">
                    Once you delete this program, it will be removed from the library and users won't be able to clone it anymore.
                </p>
            </div>
            <form action="{{ route('partner.programs.destroy', $plan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this program? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
                    Delete Program
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Program Modal -->
    <div x-show="editProgramModalOpen"
        x-cloak
        @keydown.escape.window="closeEditProgramModal()"
        class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5"
        aria-modal="true"
        role="dialog">
        <div x-show="editProgramModalOpen"
            class="fixed inset-0 bg-gray-400/50 backdrop-blur-[32px]"
            @click="closeEditProgramModal()"
            x-transition></div>
        <div x-show="editProgramModalOpen"
            @click.stop
            class="relative w-full max-w-lg rounded-3xl bg-white p-6 dark:bg-gray-900"
            x-transition>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Program</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update program details.</p>
            <div class="mt-6">
                <x-plans.library-form
                    :action="route('partner.programs.update', $plan)"
                    method="PUT"
                    :plan="$plan"
                    :cancelUrl="route('partner.programs.show', $plan)"
                />
            </div>
        </div>
    </div>

    <!-- Edit Workout Template Modal -->
    <div x-show="editModalOpen"
        x-cloak
        @keydown.escape.window="closeEditModal()"
        class="fixed inset-0 z-99999 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">
        <div x-show="editModalOpen"
            class="fixed inset-0 bg-gray-100 opacity-80 transition-opacity dark:bg-gray-900 dark:opacity-100"
            @click="closeEditModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <template x-if="editingWorkout">
                <div x-show="editModalOpen"
                    class="relative w-full max-w-2xl transform overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl transition-all dark:border-gray-800 dark:bg-gray-900"
                    @click.away="closeEditModal()">
                    <x-workouts.edit-form />
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
                <x-workouts.add-form
                    :storeUrl="route('workouts.store', $plan)"
                    :planId="$plan->id"
                    subtitle="Create a new workout template for this program."
                />
            </div>
        </div>
    </div>
    </div>
@endsection
