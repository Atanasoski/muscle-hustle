@props([
    'user',
    'storeUrl' => '',
])

{{-- Expects parent Alpine scope: createPlanModalOpen, closeCreatePlanModal() --}}
<div x-show="createPlanModalOpen"
    x-cloak
    @keydown.escape.window="closeCreatePlanModal()"
    class="modal fixed inset-0 flex items-center justify-center overflow-y-auto p-5"
    style="z-index: 999999 !important;"
    aria-labelledby="create-plan-modal-title"
    role="dialog"
    aria-modal="true">
    <div x-show="createPlanModalOpen"
        class="fixed inset-0 bg-gray-100 opacity-80 transition-opacity dark:bg-gray-900 dark:opacity-100"
        @click="closeCreatePlanModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="createPlanModalOpen"
            class="relative w-full max-w-3xl transform overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl transition-all dark:border-gray-800 dark:bg-gray-900"
            @click.away="closeCreatePlanModal()">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white" id="create-plan-modal-title">
                    Create Plan
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a new workout plan for {{ $user->name }}</p>
            </div>
            <div class="max-h-[70vh] overflow-y-auto px-6 py-4">
                @include('plans._form', [
                    'action' => $storeUrl,
                    'method' => 'POST',
                    'context' => 'user',
                    'user' => $user,
                    'plan' => null,
                    'cancelAlpineHandler' => 'closeCreatePlanModal',
                ])
            </div>
        </div>
    </div>
</div>
