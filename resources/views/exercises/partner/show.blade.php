@extends('layouts.app')

@section('title', $exercise->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <x-common.page-breadcrumb :pageTitle="$exercise->name" />

    <!-- Action Buttons -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                View exercise details and customizations
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($isLinked)
                <a href="{{ route('partner.exercises.edit', $exercise) }}">
                    <x-ui.button variant="primary" size="md">
                        <x-slot:startIcon>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </x-slot:startIcon>
                        Edit
                    </x-ui.button>
                </a>
                <form action="{{ route('exercises.unlink', $exercise) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('Unlink this exercise? This will remove all customizations.')">
                    @csrf
                    <x-ui.button type="submit" variant="outline" size="md" className="text-orange-600! ring-orange-300! hover:bg-orange-50! dark:text-orange-400! dark:ring-orange-700! dark:hover:bg-orange-500/10!">
                        <x-slot:startIcon>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </x-slot:startIcon>
                        Unlink
                    </x-ui.button>
                </form>
            @else
                <form action="{{ route('exercises.link', $exercise) }}" 
                      method="POST" 
                      class="inline">
                    @csrf
                    <x-ui.button type="submit" variant="primary" size="md">
                        <x-slot:startIcon>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </x-slot:startIcon>
                        Link Exercise
                    </x-ui.button>
                </form>
            @endif
            <a href="{{ route('partner.exercises.index') }}">
                <x-ui.button variant="outline" size="md">
                    <x-slot:startIcon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </x-slot:startIcon>
                    Back to Index
                </x-ui.button>
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column: Basic Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Exercise Details Card -->
            <x-common.component-card title="Exercise Information">
                <div>
                    <!-- Description -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Description
                            @if($pivot && $pivot->description)
                                <x-ui.badge variant="light" color="success" size="sm" className="ml-2">
                                    Custom
                                </x-ui.badge>
                            @else
                                <x-ui.badge variant="light" color="light" size="sm" className="ml-2">
                                    Default
                                </x-ui.badge>
                            @endif
                        </label>
                        @if($effectiveDescription)
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/50">
                                <p class="text-base text-gray-800 dark:text-white/90 whitespace-pre-wrap">{{ $effectiveDescription }}</p>
                            </div>
                        @else
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/50">
                                <p class="text-base text-gray-500 dark:text-gray-400 italic">No description available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </x-common.component-card>

            <!-- Media Section -->
            @if($effectiveImageUrl || $effectiveVideoUrl)
                <x-common.component-card title="Media">
                    <div class="grid grid-cols-1 gap-6 {{ $effectiveImageUrl && $effectiveVideoUrl ? 'md:grid-cols-2' : '' }}">
                        <!-- Image -->
                        @if($effectiveImageUrl)
                            <div>
                                <div class="mb-3 flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Image
                                    </label>
                                    @if($pivot && $pivot->image_url)
                                        <x-ui.badge variant="light" color="success" size="sm">
                                            Custom
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="light" color="light" size="sm">
                                            Default
                                        </x-ui.badge>
                                    @endif
                                </div>
                                <div class="flex items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                                    <img src="{{ asset($effectiveImageUrl) }}" alt="Exercise image" class="max-h-96 w-full object-contain">
                                </div>
                            </div>
                        @endif

                        <!-- Video -->
                        @if($effectiveVideoUrl)
                            <div>
                                <div class="mb-3 flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Video
                                    </label>
                                    @if($pivot && $pivot->video_url)
                                        <x-ui.badge variant="light" color="success" size="sm">
                                            Custom
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="light" color="light" size="sm">
                                            Default
                                        </x-ui.badge>
                                    @endif
                                </div>
                                <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                                    <video src="{{ asset($effectiveVideoUrl) }}" controls class="h-auto w-full">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-common.component-card>
            @endif
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
                        <p class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $exercise->name }}</p>
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

                    @if($isLinked)
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-800">
                            <label class="mb-2 block text-xs font-medium text-gray-500 dark:text-gray-400">
                                Customizations
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
                                    @if($pivot && $pivot->image_url)
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
                                    @if($pivot && $pivot->video_url)
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
                    @endif
                </div>
            </x-common.component-card>
        </div>
    </div>
</div>
@endsection
