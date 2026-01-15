@extends('layouts.app')

@section('title', 'Exercise Library')

@section('content')
<div x-data="{ editModal: 0 }" class="space-y-6">
    <!-- Breadcrumb -->
    <x-common.page-breadcrumb pageTitle="Exercise Library" />

    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Select exercises for your partner and customize them
            </p>
        </div>
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
                                            @if(isset($exercise->is_linked) && $exercise->is_linked)
                                                <x-ui.badge variant="light" color="success" size="sm">
                                                    Linked
                                                </x-ui.badge>
                                            @else
                                                <x-ui.badge variant="light" color="light" size="sm">
                                                    Not Linked
                                                </x-ui.badge>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center justify-end gap-2">
                                                @if(isset($exercise->is_linked) && $exercise->is_linked)
                                                    <x-ui.button @click="editModal = {{ $exercise->id }}" variant="outline" size="sm" className="px-3! py-1.5!">
                                                        <x-slot:startIcon>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </x-slot:startIcon>
                                                    </x-ui.button>
                                                    <form action="{{ route('exercises.unlink', $exercise) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('Unlink this exercise? This will remove all customizations.')">
                                                        @csrf
                                                        <x-ui.button type="submit" variant="outline" size="sm" className="px-3! py-1.5! text-orange-600! ring-orange-300! hover:bg-orange-50! dark:text-orange-400! dark:ring-orange-700! dark:hover:bg-orange-500/10!">
                                                            <x-slot:startIcon>
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                                                </svg>
                                                            </x-slot:startIcon>
                                                        </x-ui.button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('exercises.link', $exercise) }}" 
                                                          method="POST" 
                                                          class="inline">
                                                        @csrf
                                                        <x-ui.button type="submit" variant="primary" size="sm" className="px-3! py-1.5!">
                                                            <x-slot:startIcon>
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                </svg>
                                                            </x-slot:startIcon>
                                                            Link
                                                        </x-ui.button>
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
                                                    Customize Exercise for Partner
                                                </h3>

                                                <!-- Form -->
                                                <form action="{{ route('exercises.updatePartner', $exercise) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="space-y-4">
                                                        <div>
                                                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                Exercise Name
                                                            </label>
                                                            <input type="text" 
                                                                   value="{{ $exercise->name }}" 
                                                                   disabled
                                                                   class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-gray-500 outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-400">
                                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This is the original exercise name (cannot be changed)</p>
                                                        </div>

                                                            <div>
                                                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                    Custom Description
                                                                </label>
                                                                <textarea name="description" 
                                                                          rows="4"
                                                                          placeholder="Enter custom description for this exercise (leave empty to use default)..."
                                                                          class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">{{ $exercise->pivot_data?->description ?? '' }}</textarea>
                                                                @if($exercise->effective_description && ($exercise->pivot_data?->description === null || !$exercise->pivot_data))
                                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Currently using default description</p>
                                                                @endif
                                                        </div>

                                                        <div>
                                                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                    Custom Image
                                                                </label>
                                                                @if($exercise->effective_image_url)
                                                                    <div class="mb-2">
                                                                        <img src="{{ asset($exercise->effective_image_url) }}" alt="Exercise image" class="h-20 w-full object-contain rounded-lg border border-gray-200 dark:border-gray-800">
                                                                    </div>
                                                                    <p class="mb-2 text-xs text-green-600 dark:text-green-400">✓ Custom image is currently set</p>
                                                                @endif
                                                                <input type="file" 
                                                                       name="image" 
                                                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                                                       class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">
                                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                    @if($exercise->effective_image_url)
                                                                        Upload a new image to replace the current one (leave empty to keep current)
                                                                    @else
                                                                        Upload a custom image for this exercise (leave empty to use default)
                                                                    @endif
                                                                </p>
                                                        </div>

                                                        <div>
                                                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                    Custom Video
                                                                </label>
                                                                @if($exercise->effective_video_url)
                                                                    <div class="mb-2">
                                                                        <video src="{{ asset($exercise->effective_video_url) }}" controls class="h-48 w-full rounded-lg border border-gray-200 dark:border-gray-800"></video>
                                                                    </div>
                                                                    <p class="mb-2 text-xs text-green-600 dark:text-green-400">✓ Custom video is currently set</p>
                                                                @endif
                                                                <input type="file" 
                                                                       name="video" 
                                                                       accept="video/mp4,video/webm,video/ogg"
                                                                       class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">
                                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                    @if($exercise->effective_video_url)
                                                                        Upload a new video to replace the current one (leave empty to keep current)
                                                                    @else
                                                                        Upload a custom video for this exercise (leave empty to use default)
                                                                    @endif
                                                                </p>
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

</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
