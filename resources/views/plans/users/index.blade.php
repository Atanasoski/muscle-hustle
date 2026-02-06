@extends('layouts.app')

@section('title', 'Plans - ' . $user->name)

@section('content')
<div class="space-y-6" x-data="{
    editModalOpen: false,
    editingPlan: null,
    openEditModal(planData) {
        this.editingPlan = typeof planData === 'string' ? JSON.parse(planData) : planData;
        this.editModalOpen = true;
    },
    closeEditModal() {
        this.editModalOpen = false;
        this.editingPlan = null;
    }
}">
    <x-common.page-breadcrumb
        pageTitle="Plans"
        :items="[
            ['label' => 'Users', 'url' => route('users.index')],
            ['label' => $user->name, 'url' => route('users.show', $user)],
        ]"
    />

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-xl font-semibold text-gray-900 dark:text-white">Plans for {{ $user->name }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Manage and create plans for this user.</div>
        </div>
        <a href="{{ route('plans.create', $user) }}" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
            Create Plan
        </a>
    </div>

    <div class="w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
        @if($plans->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900">
                            <th class="px-6 py-4 font-medium text-gray-500 dark:text-gray-400" style="width: 300px;">Plan Details</th>
                            <th class="px-6 py-4 font-medium text-gray-500 dark:text-gray-400">Type</th>
                            <th class="px-6 py-4 font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-4 font-medium text-gray-500 dark:text-gray-400">Templates</th>
                            <th class="px-6 py-4 font-medium text-gray-500 dark:text-gray-400">Last Updated</th>
                            <th class="px-6 py-4 text-right font-medium text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($plans as $plan)
                            @php
                                $planModalData = [
                                    'id' => $plan->id,
                                    'name' => $plan->name,
                                    'description' => $plan->description ?? '',
                                    'type' => $plan->type?->value ?? 'custom',
                                    'duration_weeks' => $plan->duration_weeks,
                                    'is_active' => (bool) $plan->is_active,
                                    'update_url' => route('plans.update', $plan),
                                ];
                            @endphp
                            <tr class="group transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-700"
                                data-plan="{{ json_encode($planModalData) }}">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <a href="{{ route('plans.show', $plan) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ $plan->name }}
                                        </a>
                                        <span class="mt-1 line-clamp-1 max-w-[250px] text-xs text-gray-500 dark:text-gray-400">
                                            {{ $plan->description ?? 'No description' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($plan->isProgram())
                                        <span class="inline-flex items-center rounded-full border border-purple-200 bg-purple-50 px-2.5 py-0.5 text-xs font-medium text-purple-700 dark:border-purple-900/40 dark:bg-purple-900/30 dark:text-purple-300">
                                            Program
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full border border-blue-200 bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:border-blue-900/40 dark:bg-blue-900/30 dark:text-blue-300">
                                            Custom Plan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($plan->is_active)
                                        <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:border-green-900/40 dark:bg-green-900/30 dark:text-green-300">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full border border-red-200 bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:border-red-900/40 dark:bg-red-900/30 dark:text-red-300">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-gray-600 dark:text-gray-300">
                                        <span class="font-medium">{{ $plan->workout_templates_count ?? 0 }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col text-xs">
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            {{ $plan->updated_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button"
                                            @click="openEditModal($el.closest('tr').dataset.plan)"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
                                            title="Edit Plan">
                                            <svg class="h-4 w-4 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <form action="{{ route('plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg transition-colors hover:bg-gray-100 dark:hover:bg-gray-800" title="Delete">
                                                <svg class="h-4 w-4 text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Table Footer / Pagination -->
            <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Showing <span class="font-medium text-gray-900 dark:text-white">{{ $plans->firstItem() }}</span>-<span class="font-medium text-gray-900 dark:text-white">{{ $plans->lastItem() }}</span> of <span class="font-medium text-gray-900 dark:text-white">{{ $plans->total() }}</span> plans
                </span>
                <div class="flex gap-2">
                    @if($plans->onFirstPage())
                        <span class="inline-flex h-8 items-center justify-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-600">
                            Previous
                        </span>
                    @else
                        <a href="{{ $plans->previousPageUrl() }}" class="inline-flex h-8 items-center justify-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                            Previous
                        </a>
                    @endif
                    @if($plans->hasMorePages())
                        <a href="{{ $plans->nextPageUrl() }}" class="inline-flex h-8 items-center justify-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                            Next
                        </a>
                    @else
                        <span class="inline-flex h-8 items-center justify-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-600">
                            Next
                        </span>
                    @endif
                </div>
            </div>
        @else
            <div class="p-10 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No plans</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new plan for this user.</p>
                <div class="mt-6">
                    <a href="{{ route('plans.create', $user) }}" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                        Create Plan
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Edit Plan Modal (matches Add Exercise modal structure) -->
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
                class="relative w-full max-w-3xl transform overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl transition-all dark:border-gray-800 dark:bg-gray-900"
                @click.away="closeEditModal()">
                <template x-if="editingPlan">
                    <form :action="editingPlan.update_url" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white" id="edit-plan-modal-title">
                                        Edit Plan
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update plan details for {{ $user->name }}.</p>
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

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">Plan Type <span class="text-red-500">*</span></label>
                                    <select name="type"
                                        x-model="editingPlan.type"
                                        required
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 outline-none focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-white dark:focus:ring-white/10">
                                        <option value="custom">Custom Plan</option>
                                        <option value="program">Program</option>
                                    </select>
                                </div>

                                <div x-show="editingPlan.type === 'program'" x-transition>
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
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

