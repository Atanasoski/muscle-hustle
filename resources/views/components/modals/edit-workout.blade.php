{{-- Expects parent Alpine scope: editModalOpen, editingWorkout, closeEditModal() --}}
<div x-show="editModalOpen"
    x-cloak
    @keydown.escape.window="closeEditModal()"
    class="fixed inset-0 z-99999 overflow-y-auto"
    style="z-index: 999999 !important;"
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
