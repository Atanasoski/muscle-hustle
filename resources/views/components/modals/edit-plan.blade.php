@props([
    'subtitle' => null,
])

{{-- Expects parent Alpine scope: editModalOpen, editingPlan, closeEditModal() --}}
<div x-show="editModalOpen"
    x-cloak
    @keydown.escape.window="closeEditModal()"
    class="modal fixed inset-0 flex items-center justify-center overflow-y-auto p-5"
    style="z-index: 999999 !important;"
    aria-labelledby="edit-plan-modal-title"
    role="dialog"
    aria-modal="true">
    <div x-show="editModalOpen"
        class="fixed inset-0 bg-gray-100 opacity-80 transition-opacity dark:bg-gray-900 dark:opacity-100"
        @click="closeEditModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="editModalOpen"
            x-ref="editPlanModalContent"
            class="relative w-full max-w-3xl transform overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl transition-all dark:border-gray-800 dark:bg-gray-900"
            @click.away="closeEditModal()">
            <template x-if="editingPlan">
                <x-plans.edit-form :subtitle="$subtitle" />
            </template>
        </div>
    </div>
</div>
