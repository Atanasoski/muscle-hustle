@props([
    'plan',
    'subtitle' => 'Create a new workout template for this plan.',
])

{{-- Expects parent Alpine scope: addWorkoutModalOpen, addWorkoutWeekNumber, closeAddWorkoutModal() --}}
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
                :subtitle="$subtitle"
            />
        </div>
    </div>
</div>
