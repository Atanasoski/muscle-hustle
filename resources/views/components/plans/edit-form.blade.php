@props([
    'subtitle' => null,
])

{{-- Rendered inside a parent with x-data containing editingPlan. Form uses editingPlan from parent scope. --}}
<form :action="editingPlan.update_url" method="POST">
    @csrf
    @method('PUT')

    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white" id="edit-plan-modal-title">
                    Edit Plan
                </h2>
                @if ($subtitle)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
                @endif
            </div>
            <button type="button"
                @click="closeEditModal()"
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
                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">Plan Name <span class="text-red-500">*</span></label>
                <input type="text"
                    name="name"
                    x-model="editingPlan.name"
                    required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none transition-shadow focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">Description</label>
                <textarea name="description"
                    x-model="editingPlan.description"
                    rows="4"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none transition-shadow focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10"></textarea>
            </div>

            <input type="hidden" name="type" value="program">

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">Duration (weeks)</label>
                <input type="number"
                    name="duration_weeks"
                    x-model="editingPlan.duration_weeks"
                    min="1"
                    max="52"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none transition-shadow focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10" />
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Number of weeks for this program (1-52)</p>
            </div>

            <div>
                <input type="hidden" name="is_active" value="0" />
                <label class="flex cursor-pointer items-center text-sm font-semibold text-gray-700 select-none dark:text-gray-400">
                    <input type="checkbox"
                        name="is_active"
                        value="1"
                        class="sr-only"
                        x-model="editingPlan.is_active" />
                    <span class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]"
                        :class="editingPlan.is_active ? 'border-brand-500 bg-brand-500' : 'border-gray-300 bg-transparent dark:border-gray-700'">
                        <span x-show="editingPlan.is_active">
                            <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11.6666 3.5L5.24992 9.91667L2.33325 7" />
                            </svg>
                        </span>
                    </span>
                    Set as Active Plan
                </label>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Only one plan can be active at a time.</p>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        <div class="flex justify-end gap-3">
            <button type="button" @click="closeEditModal()" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</button>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">Update Plan</button>
        </div>
    </div>
</form>
