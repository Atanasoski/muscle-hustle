@props([
    'plan',
])

{{-- Expects parent Alpine scope: editProgramModalOpen, closeEditProgramModal() --}}
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
