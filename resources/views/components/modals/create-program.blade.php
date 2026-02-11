@props([
    'storeUrl' => '',
])

{{-- Expects parent Alpine scope: createModalOpen, closeCreateModal() --}}
<div x-show="createModalOpen"
    x-cloak
    @keydown.escape.window="closeCreateModal()"
    class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5"
    aria-modal="true"
    role="dialog">
    <div x-show="createModalOpen"
        class="fixed inset-0 bg-gray-400/50 backdrop-blur-[32px]"
        @click="closeCreateModal()"
        x-transition></div>
    <div x-show="createModalOpen"
        @click.stop
        class="relative w-full max-w-lg rounded-3xl bg-white p-6 dark:bg-gray-900"
        x-transition>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Create Program</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a new program to the library.</p>
        <div class="mt-6">
            <x-plans.library-form
                :action="$storeUrl"
                method="POST"
                :plan="null"
            />
        </div>
    </div>
</div>
