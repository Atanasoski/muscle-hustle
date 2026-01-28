@extends('layouts.app')

@section('title', 'Exercise Library')

@section('content')
<div x-data="{ 
    collapsedCategories: {},
    toggleCategory(categoryId) {
        this.collapsedCategories[categoryId] = !this.collapsedCategories[categoryId];
    },
    isCategoryCollapsed(categoryId) {
        return this.collapsedCategories[categoryId] || false;
    }
}" class="space-y-6">
    <!-- Breadcrumb -->
    <x-common.page-breadcrumb pageTitle="Exercise Library" />

    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Manage your exercises and video tutorials
            </p>
        </div>
        <a href="{{ route('exercises.create') }}">
            <x-ui.button variant="primary">
                <x-slot:startIcon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </x-slot:startIcon>
                Add Custom Exercise
            </x-ui.button>
        </a>
    </div>

    <!-- Search Bar -->
    <div class="relative">
        <span class="absolute left-4 top-1/2 -translate-y-1/2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </span>
        <input type="text" 
               id="exercise-search" 
               placeholder="Search exercises by name or muscle group..."
               class="w-full rounded-lg border border-gray-200 bg-white py-3 pl-12 pr-4 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">
    </div>

    <!-- Exercises by Category -->
    @foreach($categories as $category)
        @if($category->exercises->count() > 0)
        <div class="exercise-category" data-category="{{ $category->name }}">
            <x-common.component-card>
                <x-slot:title>
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2" style="color: {{ $category->color }};">
                            <span>{{ $category->icon }} {{ $category->name }}</span>
                            <x-ui.badge variant="light" color="light" size="sm">
                                {{ $category->exercises->count() }}
                            </x-ui.badge>
                        </div>
                        <x-ui.button 
                            @click="toggleCategory({{ $category->id }})"
                            variant="outline" 
                            size="sm" 
                            className="px-3! py-1.5!">
                            <span x-text="isCategoryCollapsed({{ $category->id }}) ? 'Expand' : 'Collapse'"></span>
                        </x-ui.button>
                    </div>
                </x-slot:title>

                <!-- Exercises Table -->
                <div x-show="!isCategoryCollapsed({{ $category->id }})"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[800px]">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 text-left sm:px-6 min-w-[200px]">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Exercise Name
                                        </p>
                                    </th>
                                    <th class="px-5 py-3 text-left sm:px-6 hidden lg:table-cell">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Muscle Groups
                                        </p>
                                    </th>
                                    <th class="px-5 py-3 text-center sm:px-6 hidden md:table-cell">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Rest Time
                                        </p>
                                    </th>
                                    <th class="px-5 py-3 text-center sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Type
                                        </p>
                                    </th>
                                    <th class="px-5 py-3 text-right sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Actions
                                        </p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->exercises as $exercise)
                                    <tr class="exercise-row border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/2" 
                                        data-name="{{ strtolower($exercise->name) }}"
                                        data-muscle-groups="{{ strtolower($exercise->muscleGroups->pluck('name')->implode(' ')) }}">
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('exercises.show', $exercise) }}" class="font-medium text-gray-800 text-theme-sm dark:text-white/90 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                                                    {{ $exercise->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6 hidden lg:table-cell">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse($exercise->muscleGroups as $muscleGroup)
                                                    <x-ui.badge variant="light" color="light" size="sm">
                                                        {{ $muscleGroup->name }}
                                                    </x-ui.badge>
                                                @empty
                                                    <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-center sm:px-6 hidden md:table-cell">
                                            <x-ui.badge variant="light" color="light" size="sm">
                                                {{ $exercise->default_rest_sec }}s
                                            </x-ui.badge>
                                        </td>
                                        <td class="px-5 py-4 text-center sm:px-6">
                                            <x-ui.badge variant="light" color="success" size="sm">
                                                Global
                                            </x-ui.badge>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('exercises.show', $exercise) }}">
                                                    <x-ui.button variant="outline" size="sm" className="px-3! py-1.5!">
                                                        <x-slot:startIcon>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </x-slot:startIcon>
                                                    </x-ui.button>
                                                </a>
                                                <a href="{{ route('exercises.edit', $exercise) }}">
                                                    <x-ui.button variant="outline" size="sm" className="px-3! py-1.5!">
                                                        <x-slot:startIcon>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </x-slot:startIcon>
                                                    </x-ui.button>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        </div>
        @endif
    @endforeach

</div>
@endsection

@push('scripts')
<script>
// Exercise search functionality
const exerciseSearchInput = document.getElementById('exercise-search');
if (exerciseSearchInput) {
    exerciseSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const exerciseRows = document.querySelectorAll('.exercise-row');
        const categories = document.querySelectorAll('.exercise-category');
        
        exerciseRows.forEach(row => {
            const exerciseName = row.getAttribute('data-name');
            const muscleGroups = row.getAttribute('data-muscle-groups') || '';
            const searchableText = exerciseName + ' ' + muscleGroups;
            if (searchableText.includes(searchTerm)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
        
        categories.forEach(category => {
            const tbody = category.querySelector('tbody');
            if (!tbody) return;
            
            const visibleRows = tbody.querySelectorAll('.exercise-row:not(.hidden)');
            if (visibleRows.length === 0) {
                category.classList.add('hidden');
            } else {
                category.classList.remove('hidden');
            }
        });
    });
}

</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
