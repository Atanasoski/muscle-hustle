@props([
    'text' => '',
    'position' => 'top', // top, bottom, left, right
])

<div 
    x-data="{ 
        show: false,
        position: '{{ $position }}'
    }"
    @mouseenter="show = true"
    @mouseleave="show = false"
    class="relative inline-block"
>
    {{ $slot }}
    
    <div 
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-99999 rounded-lg bg-gray-900 px-2.5 py-1.5 text-xs font-medium text-white shadow-lg dark:bg-gray-800"
        :class="{
            'bottom-full left-1/2 -translate-x-1/2 mb-2': position === 'top',
            'top-full left-1/2 -translate-x-1/2 mt-2': position === 'bottom',
            'right-full top-1/2 -translate-y-1/2 mr-2': position === 'left',
            'left-full top-1/2 -translate-y-1/2 ml-2': position === 'right',
        }"
        style="display: none; max-width: 250px; white-space: nowrap;"
    >
        {{ $text }}
        <div 
            class="absolute h-2 w-2 rotate-45 bg-gray-900 dark:bg-gray-800"
            :class="{
                'bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2': position === 'top',
                'top-0 left-1/2 -translate-x-1/2 -translate-y-1/2': position === 'bottom',
                'right-0 top-1/2 -translate-x-1/2 -translate-y-1/2': position === 'left',
                'left-0 top-1/2 translate-x-1/2 -translate-y-1/2': position === 'right',
            }"
        ></div>
    </div>
</div>
