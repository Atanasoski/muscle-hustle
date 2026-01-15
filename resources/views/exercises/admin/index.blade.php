@extends('layouts.app')

@section('title', 'Exercise Library')

@section('content')
<div x-data="{ createModal: false, editModal: 0 }" class="space-y-6">
    <!-- Breadcrumb -->
    <x-common.page-breadcrumb pageTitle="Exercise Library" />

    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Manage your exercises and video tutorials
            </p>
        </div>
        <x-ui.button @click="createModal = true" variant="primary">
                <x-slot:startIcon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </x-slot:startIcon>
                Add Custom Exercise
            </x-ui.button>
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
               placeholder="Search exercises by name..."
               class="w-full rounded-lg border border-gray-200 bg-white py-3 pl-12 pr-4 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">
    </div>

    <!-- Exercises by Category -->
    @foreach($categories as $category)
        @if($category->exercises->count() > 0)
        <div class="exercise-category" data-category="{{ $category->name }}">
            <x-common.component-card>
                <x-slot:title>
                    <div class="flex items-center gap-2" style="color: {{ $category->color }};">
                        <span>{{ $category->icon }} {{ $category->name }}</span>
                        <x-ui.badge variant="light" color="light" size="sm">
                            {{ $category->exercises->count() }}
                        </x-ui.badge>
                    </div>
                </x-slot:title>

                <!-- Exercises Table -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[800px]">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 text-left sm:px-6 min-w-[200px]">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Exercise Name
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
                                        data-name="{{ strtolower($exercise->name) }}">
                                        <td class="px-5 py-4 sm:px-6 min-w-[200px]">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="font-medium text-gray-800 text-theme-sm dark:text-white/90 truncate max-w-full" title="{{ $exercise->name }}">{{ $exercise->name }}</span>
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
                                                <x-ui.button @click="editModal = {{ $exercise->id }}" variant="outline" size="sm" className="px-3! py-1.5!">
                                                    <x-slot:startIcon>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </x-slot:startIcon>
                                                </x-ui.button>
                                            </div>
                                        </td>
                                    </tr>

                                <!-- Edit Modal for this exercise -->
                                <template x-teleport="body">
                                    <div x-show="editModal === {{ $exercise->id }}" 
                                         x-cloak
                                         @click.self="editModal = 0"
                                         @keydown.escape.window="editModal = 0"
                                         class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5">
                                        <!-- Backdrop -->
                                        <div @click="editModal = 0" 
                                             class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
                                             x-transition:enter="transition ease-out duration-300" 
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100" 
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0">
                                        </div>

                                        <!-- Modal Content -->
                                        <div @click.stop 
                                             class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-gray-900"
                                             x-transition:enter="transition ease-out duration-300" 
                                             x-transition:enter-start="opacity-0 transform scale-95"
                                             x-transition:enter-end="opacity-100 transform scale-100" 
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100 transform scale-100"
                                             x-transition:leave-end="opacity-0 transform scale-95">
                                            <!-- Close Button -->
                                            <button @click="editModal = 0"
                                                class="absolute right-3 top-3 z-999 flex h-9.5 w-9.5 items-center justify-center rounded-full bg-gray-100 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 sm:h-11 sm:w-11">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fillRule="evenodd" clipRule="evenodd"
                                                        d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </button>

                                            <div class="p-6 space-y-4">
                                                <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90 mb-4">
                                                    Edit Exercise
                                                </h3>

                                                <!-- Form -->
                                                <form action="{{ route('exercises.update', $exercise) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="space-y-4">
                                                        <div>
                                                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                    Exercise Name <span class="text-red-500">*</span>
                                                                </label>
                                                                <input type="text" 
                                                                       name="name" 
                                                                       value="{{ $exercise->name }}" 
                                                                       required
                                                                       class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">
                                                            </div>

                                                            <div>
                                                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                    Description
                                                                </label>
                                                                <textarea name="description" 
                                                                          rows="3"
                                                                          placeholder="Exercise instructions or notes..."
                                                                          class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">{{ $exercise->description }}</textarea>
                                                            </div>

                                                            <div>
                                                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                    Category <span class="text-red-500">*</span>
                                                                </label>
                                                                <select name="category_id" 
                                                                        required
                                                                        class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">
                                                                    @foreach($categories as $cat)
                                                                        <option value="{{ $cat->id }}" {{ $exercise->category_id == $cat->id ? 'selected' : '' }}>
                                                                            {{ $cat->icon }} {{ $cat->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div>
                                                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                    Default Rest Time (seconds)
                                                                </label>
                                                                <input type="number" 
                                                                       name="default_rest_sec" 
                                                                       value="{{ $exercise->default_rest_sec }}" 
                                                                       min="0"
                                                                       class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">
                                                            </div>
                                                    </div>

                                                    <!-- Footer -->
                                                    <div class="flex gap-4 mt-6 pt-4 border-t border-gray-200 dark:border-gray-800">
                                                        <x-ui.button type="button" 
                                                                @click="editModal = 0"
                                                                variant="outline"
                                                                className="flex-1">
                                                            Cancel
                                                        </x-ui.button>
                                                        <x-ui.button type="submit"
                                                                variant="primary"
                                                                className="flex-1">
                                                            Save Changes
                                                        </x-ui.button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        </div>
        @endif
    @endforeach

    <!-- Create Exercise Modal -->
    <template x-teleport="body">
        <div x-show="createModal" 
             x-cloak
             @click.self="createModal = false"
             @keydown.escape.window="createModal = false"
             class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5">
            <!-- Backdrop -->
            <div @click="createModal = false" 
                 class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
            </div>

            <!-- Modal Content -->
            <div @click.stop 
                 class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-gray-900"
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100" 
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <!-- Close Button -->
                <button @click="createModal = false"
                    class="absolute right-3 top-3 z-999 flex h-9.5 w-9.5 items-center justify-center rounded-full bg-gray-100 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 sm:h-11 sm:w-11">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fillRule="evenodd" clipRule="evenodd"
                            d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
                            fill="currentColor" />
                    </svg>
                </button>

                <div class="p-6 space-y-4">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90 mb-4">
                        Add Custom Exercise
                    </h3>

                    <!-- Form -->
                    <form action="{{ route('exercises.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Exercise Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       required 
                                       placeholder="e.g., Dumbbell Curl"
                                       class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-500">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Description
                                </label>
                                <textarea name="description" 
                                          rows="3"
                                          placeholder="Exercise instructions or notes..."
                                          class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-500"></textarea>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="category-search-create" 
                                       placeholder="🔍 Search categories..." 
                                       autocomplete="off"
                                       class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-500 mb-2">
                                <select name="category_id" 
                                        id="category-select-create" 
                                        required 
                                        size="6"
                                        class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-500">
                                    <option value="">Select category...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Default Rest Time (seconds)
                                </label>
                                <input type="number" 
                                       name="default_rest_sec" 
                                       value="90" 
                                       min="0"
                                       class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-500">
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex gap-4 mt-6 pt-4 border-t border-gray-200 dark:border-gray-800">
                            <x-ui.button type="button" 
                                    @click="createModal = false"
                                    variant="outline"
                                    className="flex-1">
                                Cancel
                            </x-ui.button>
                            <x-ui.button type="submit"
                                    variant="primary"
                                    className="flex-1">
                                Create Exercise
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
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
            if (exerciseName.includes(searchTerm)) {
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

// Category search in Create Exercise modal
const categorySearchCreate = document.getElementById('category-search-create');
const categorySelectCreate = document.getElementById('category-select-create');

if (categorySearchCreate && categorySelectCreate) {
    categorySearchCreate.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const options = categorySelectCreate.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') return;
            
            const text = option.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    });
}
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
