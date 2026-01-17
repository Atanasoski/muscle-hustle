@props([
    'text' => '',
    'position' => 'top', // top, bottom, left, right
])

<div 
    x-data="{ 
        show: false,
        position: '{{ $position }}',
        tooltipX: 0,
        tooltipY: 0,
        tooltipId: 'tooltip-' + Math.random().toString(36).substr(2, 9),
        updateTooltipPosition() {
            if (!this.show) return;
            this.$nextTick(() => {
                const rect = this.$el.getBoundingClientRect();
                const tooltipEl = document.getElementById(this.tooltipId);
                if (!tooltipEl) return;
                
                const tooltipRect = tooltipEl.getBoundingClientRect();
                const scrollX = window.scrollX || window.pageXOffset;
                const scrollY = window.scrollY || window.pageYOffset;
                
                if (this.position === 'top') {
                    this.tooltipX = rect.left + (rect.width / 2) + scrollX;
                    this.tooltipY = rect.top - tooltipRect.height - 8 + scrollY;
                } else if (this.position === 'bottom') {
                    this.tooltipX = rect.left + (rect.width / 2) + scrollX;
                    this.tooltipY = rect.bottom + 8 + scrollY;
                } else if (this.position === 'left') {
                    this.tooltipX = rect.left - tooltipRect.width - 8 + scrollX;
                    this.tooltipY = rect.top + (rect.height / 2) + scrollY;
                } else {
                    this.tooltipX = rect.right + 8 + scrollX;
                    this.tooltipY = rect.top + (rect.height / 2) + scrollY;
                }
            });
        }
    }"
    @mouseenter="show = true; updateTooltipPosition()"
    @mouseleave="show = false"
    class="relative inline-block"
>
    {{ $slot }}
    
    <template x-teleport="body">
        <div 
            :id="tooltipId"
            x-show="show"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed z-99999 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white shadow-lg dark:bg-gray-800 pointer-events-none"
            :style="position === 'top' || position === 'bottom' ? `top: ${tooltipY}px; left: ${tooltipX}px; transform: translateX(-50%);` : `top: ${tooltipY}px; left: ${tooltipX}px; transform: translateY(-50%);`"
            style="display: none; white-space: nowrap;"
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
    </template>
</div>
