@props([
    'selected' => false,
])

@php
    $baseStyles = 'inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200 border cursor-pointer';
    
    $selectedStyles = $selected 
        ? 'bg-brand-50 border-brand-200 text-brand-700 shadow-sm dark:bg-brand-500/15 dark:border-brand-500/30 dark:text-brand-400' 
        : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:border-gray-600';
@endphp

<button
    type="button"
    {{ $attributes->merge(['class' => $baseStyles . ' ' . $selectedStyles]) }}
>
    @if($selected)
        <svg class="mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    @endif
    {{ $slot }}
</button>
