@extends('layouts.app')

@section('title', 'Exercise Library')

@section('content')
<div x-data="{ createModal: false, editModal: 0 }" class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white">
                Exercise Library
            </h2>
            <p class="text-sm text-body dark:text-bodydark">Manage your exercises and video tutorials</p>
        </div>
        <button @click="createModal = true" 
                class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-5 py-3 text-center font-medium text-white hover:bg-opacity-90">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Custom Exercise
        </button>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2">
                <svg class="w-5 h-5 text-bodydark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input type="text" 
                   id="exercise-search" 
                   placeholder="Search exercises by name..."
                   class="w-full rounded-lg border border-stroke bg-white py-3 pl-12 pr-4 text-black focus:border-primary focus:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary">
        </div>
    </div>

    <!-- Exercises by Category -->
    @foreach($categories as $category)
        @if($category->exercises->count() > 0)
        <div class="exercise-category mb-6" data-category="{{ $category->name }}">
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <!-- Category Header -->
                <div class="border-b border-stroke px-6 py-4 dark:border-strokedark" style="background: {{ $category->color }};">
                    <h3 class="font-bold text-white flex items-center gap-2">
                        <span>{{ $category->icon }} {{ $category->name }}</span>
                        <span class="rounded-full bg-white px-2.5 py-0.5 text-sm font-medium text-black">
                            {{ $category->exercises->count() }}
                        </span>
                    </h3>
                </div>

                <!-- Exercises Table -->
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-2 text-left dark:bg-meta-4">
                                <th class="px-6 py-4 font-medium text-black dark:text-white">
                                    Exercise Name
                                </th>
                                <th class="px-6 py-4 font-medium text-black dark:text-white text-center hidden md:table-cell">
                                    Rest Time
                                </th>
                                <th class="px-6 py-4 font-medium text-black dark:text-white text-center">
                                    Type
                                </th>
                                <th class="px-6 py-4 font-medium text-black dark:text-white text-right">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->exercises as $exercise)
                                <tr class="exercise-row border-b border-stroke dark:border-strokedark hover:bg-gray-2 dark:hover:bg-meta-4" 
                                    data-name="{{ strtolower($exercise->name) }}">
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-black dark:text-white">{{ $exercise->name }}</span>
                                        @if($exercise->user_id)
                                            <span class="ml-2 inline-flex rounded-full bg-warning bg-opacity-10 px-2.5 py-0.5 text-xs font-medium text-warning">
                                                Custom
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center hidden md:table-cell">
                                        <span class="inline-flex rounded-full bg-bodydark bg-opacity-10 px-2.5 py-0.5 text-sm font-medium text-bodydark dark:bg-white dark:bg-opacity-10 dark:text-white">
                                            {{ $exercise->default_rest_sec }}s
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($exercise->user_id)
                                            <span class="inline-flex rounded-full bg-warning bg-opacity-10 px-2.5 py-0.5 text-sm font-medium text-warning">
                                                Custom
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-success bg-opacity-10 px-2.5 py-0.5 text-sm font-medium text-success">
                                                Global
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="editModal = {{ $exercise->id }}"
                                                    class="inline-flex items-center justify-center rounded-md border border-primary px-3 py-1.5 text-center font-medium text-primary hover:bg-primary hover:text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            @if($exercise->user_id)
                                                <form action="{{ route('exercises.destroy', $exercise) }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      onsubmit="return confirm('Delete this exercise?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center rounded-md border border-danger px-3 py-1.5 text-center font-medium text-danger hover:bg-danger hover:text-white">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Modal for this exercise -->
                                <template x-teleport="body">
                                    <div x-show="editModal === {{ $exercise->id }}" 
                                         x-cloak
                                         @click.self="editModal = 0"
                                         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4">
                                        <div @click.away="editModal = 0" 
                                             class="w-full max-w-lg rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                                            <!-- Header -->
                                            <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                                                <h3 class="text-xl font-semibold text-black dark:text-white">
                                                    Edit Exercise
                                                </h3>
                                            </div>

                                            <!-- Form -->
                                            <form action="{{ route('exercises.update', $exercise) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="p-6 space-y-4">
                                                    <div>
                                                        <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">
                                                            Exercise Name <span class="text-meta-1">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               name="name" 
                                                               value="{{ $exercise->name }}" 
                                                               required
                                                               class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 text-black outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white">
                                                    </div>

                                                    <div>
                                                        <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">
                                                            Description
                                                        </label>
                                                        <textarea name="description" 
                                                                  rows="3"
                                                                  placeholder="Exercise instructions or notes..."
                                                                  class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 text-black outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white">{{ $exercise->description }}</textarea>
                                                    </div>

                                                    <div>
                                                        <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">
                                                            Category <span class="text-meta-1">*</span>
                                                        </label>
                                                        <select name="category_id" 
                                                                required
                                                                class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 text-black outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white">
                                                            @foreach($categories as $cat)
                                                                <option value="{{ $cat->id }}" {{ $exercise->category_id == $cat->id ? 'selected' : '' }}>
                                                                    {{ $cat->icon }} {{ $cat->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">
                                                            Default Rest Time (seconds)
                                                        </label>
                                                        <input type="number" 
                                                               name="default_rest_sec" 
                                                               value="{{ $exercise->default_rest_sec }}" 
                                                               min="0"
                                                               class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 text-black outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white">
                                                    </div>
                                                </div>

                                                <!-- Footer -->
                                                <div class="flex gap-4 border-t border-stroke px-6 py-4 dark:border-strokedark">
                                                    <button type="button" 
                                                            @click="editModal = 0"
                                                            class="flex-1 rounded border border-stroke px-6 py-2.5 font-medium text-black hover:shadow-1 dark:border-strokedark dark:text-white">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                            class="flex-1 rounded bg-primary px-6 py-2.5 font-medium text-white hover:bg-opacity-90">
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </template>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    <!-- Create Exercise Modal -->
    <template x-teleport="body">
        <div x-show="createModal" 
             x-cloak
             @click.self="createModal = false"
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4">
            <div @click.away="createModal = false" 
                 class="w-full max-w-lg rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <!-- Header -->
                <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                    <h3 class="text-xl font-semibold text-black dark:text-white">
                        Add Custom Exercise
                    </h3>
                </div>

                <!-- Form -->
                <form action="{{ route('exercises.store') }}" method="POST">
                    @csrf
                    
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">
                                Exercise Name <span class="text-meta-1">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   required 
                                   placeholder="e.g., Dumbbell Curl"
                                   class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 text-black outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white">
                        </div>

                        <div>
                            <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">
                                Description
                            </label>
                            <textarea name="description" 
                                      rows="3"
                                      placeholder="Exercise instructions or notes..."
                                      class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 text-black outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white"></textarea>
                        </div>

                        <div>
                            <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">
                                Category <span class="text-meta-1">*</span>
                            </label>
                            <input type="text" 
                                   id="category-search-create" 
                                   placeholder="🔍 Search categories..." 
                                   autocomplete="off"
                                   class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 text-black outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white mb-2">
                            <select name="category_id" 
                                    id="category-select-create" 
                                    required 
                                    size="6"
                                    class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 text-black outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white">
                                <option value="">Select category...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">
                                Default Rest Time (seconds)
                            </label>
                            <input type="number" 
                                   name="default_rest_sec" 
                                   value="90" 
                                   min="0"
                                   class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 text-black outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white">
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex gap-4 border-t border-stroke px-6 py-4 dark:border-strokedark">
                        <button type="button" 
                                @click="createModal = false"
                                class="flex-1 rounded border border-stroke px-6 py-2.5 font-medium text-black hover:shadow-1 dark:border-strokedark dark:text-white">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 rounded bg-primary px-6 py-2.5 font-medium text-white hover:bg-opacity-90">
                            Create Exercise
                        </button>
                    </div>
                </form>
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
