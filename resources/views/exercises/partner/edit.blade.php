@extends('layouts.app')

@section('title', 'Edit ' . $exercise->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <x-common.page-breadcrumb :pageTitle="'Edit ' . $exercise->name" />

    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Customize this exercise for your partner
            </p>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('exercises.updatePartner', $exercise) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column: Form Fields -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Exercise Information Card -->
                <x-common.component-card title="Exercise Information">
                    <div>
                        <!-- Custom Description -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Custom Description
                            </label>
                            <textarea name="description" 
                                      rows="6"
                                      placeholder="Enter custom description for this exercise (leave empty to use default)..."
                                      class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">{{ $formDescription }}</textarea>
                            @if($exercise->description && (!$pivot || !$pivot->description))
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Currently using default description</p>
                            @endif
                        </div>
                    </div>
                </x-common.component-card>

                <!-- Media Section -->
                <x-common.component-card title="Media">
                    @if($exercise->muscle_group_image)
                        <div class="mb-6 rounded-lg border border-gray-200 bg-blue-50 p-4 dark:border-gray-800 dark:bg-blue-900/20">
                            <div class="mb-3 flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Muscle Group Image (Auto-generated)
                                </label>
                                <x-ui.badge variant="light" color="info" size="sm">
                                    Read-only
                                </x-ui.badge>
                            </div>
                            <div class="flex items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                                <img src="{{ asset('storage/' . $exercise->muscle_group_image) }}" alt="Muscle group image" class="max-h-64 w-full object-contain">
                            </div>
                            <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">This image is automatically generated based on the exercise's muscle groups.</p>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 gap-6 {{ ($formImage || $exercise->image) && ($formVideo || $exercise->video) ? 'md:grid-cols-2' : '' }}">
                        <!-- Custom Image -->
                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Custom Image
                                </label>
                                @if($formImage)
                                    <x-ui.badge variant="light" color="success" size="sm">
                                        Custom Set
                                    </x-ui.badge>
                                @elseif($exercise->image)
                                    <x-ui.badge variant="light" color="light" size="sm">
                                        Using Default
                                    </x-ui.badge>
                                @endif
                            </div>
                            @if($formImage)
                                <div class="mb-3 flex items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                                    <img src="{{ asset('storage/' . $formImage) }}" alt="Exercise image" class="max-h-96 w-full object-contain">
                                </div>
                                <p class="mb-2 text-xs text-green-600 dark:text-green-400">✓ Custom image is currently set</p>
                            @elseif($exercise->image)
                                <div class="mb-3 flex items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                                    <img src="{{ asset('storage/' . $exercise->image) }}" alt="Exercise image" class="max-h-96 w-full object-contain">
                                </div>
                                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Currently using default image</p>
                            @endif
                            <input type="file" 
                                   name="image" 
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                   class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                @if($formImage)
                                    Upload a new image to replace the current custom one (leave empty to keep current)
                                @else
                                    Upload a custom image for this exercise (leave empty to use default)
                                @endif
                            </p>
                        </div>

                        <!-- Custom Video -->
                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Custom Video
                                </label>
                                @if($formVideo)
                                    <x-ui.badge variant="light" color="success" size="sm">
                                        Custom Set
                                    </x-ui.badge>
                                @elseif($exercise->video)
                                    <x-ui.badge variant="light" color="light" size="sm">
                                        Using Default
                                    </x-ui.badge>
                                @endif
                            </div>
                            @if($formVideo)
                                <div class="mb-3 overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                                    <video src="{{ asset('storage/' . $formVideo) }}" controls class="h-auto w-full">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                <p class="mb-2 text-xs text-green-600 dark:text-green-400">✓ Custom video is currently set</p>
                            @elseif($exercise->video)
                                <div class="mb-3 overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                                    <video src="{{ asset('storage/' . $exercise->video) }}" controls class="h-auto w-full">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Currently using default video</p>
                            @endif
                            <input type="file" 
                                   name="video" 
                                   accept="video/mp4,video/webm,video/ogg"
                                   class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:focus:border-brand-500">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                @if($formVideo)
                                    Upload a new video to replace the current custom one (leave empty to keep current)
                                @else
                                    Upload a custom video for this exercise (leave empty to use default)
                                @endif
                            </p>
                        </div>
                    </div>
                </x-common.component-card>
            </div>

            <!-- Right Column: Status & Quick Info -->
            <div class="space-y-6">
                <!-- Status Card -->
                <x-common.component-card title="Status">
                    <div class="space-y-6">
                        <!-- Exercise Name -->
                        <div>
                            <label class="mb-2 block text-xs font-medium text-gray-500 dark:text-gray-400">
                                Exercise Name
                            </label>
                            <input type="text" 
                                   value="{{ $exercise->name }}" 
                                   disabled
                                   class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500 outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-400">
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="mb-2 block text-xs font-medium text-gray-500 dark:text-gray-400">
                                Category
                            </label>
                            <div class="flex items-center gap-2" style="color: {{ $exercise->category->color }};">
                                <span class="text-lg">{{ $exercise->category->icon }}</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $exercise->category->name }}</span>
                            </div>
                        </div>

                        <!-- Default Rest Time -->
                        <div>
                            <label class="mb-2 block text-xs font-medium text-gray-500 dark:text-gray-400">
                                Default Rest Time
                            </label>
                            <p class="text-base font-medium text-gray-800 dark:text-white/90">{{ $exercise->default_rest_sec }} seconds</p>
                        </div>

                        <!-- Current Customizations -->
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-800">
                            <label class="mb-2 block text-xs font-medium text-gray-500 dark:text-gray-400">
                                Current Customizations
                            </label>
                            <div class="space-y-2">
                                <!-- Custom Description -->
                                <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    @if($pivot && $pivot->description)
                                        <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    @endif
                                    <span>Custom Description</span>
                                </div>
                                
                                <!-- Custom Image -->
                                <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    @if($pivot && $pivot->image)
                                        <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    @endif
                                    <span>Custom Image</span>
                                </div>
                                
                                <!-- Custom Video -->
                                <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    @if($pivot && $pivot->video)
                                        <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    @endif
                                    <span>Custom Video</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-common.component-card>

                <!-- Footer Buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('partner.exercises.show', $exercise) }}" class="flex-1">
                        <x-ui.button type="button" 
                                variant="outline"
                                className="w-full">
                            Cancel
                        </x-ui.button>
                    </a>
                    <x-ui.button type="submit"
                            variant="primary"
                            className="flex-1">
                        Save Changes
                    </x-ui.button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
