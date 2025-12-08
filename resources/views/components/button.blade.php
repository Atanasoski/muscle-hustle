@php
    // Check if user provided custom btn- color classes
    $hasCustomBtnClass = preg_match('/btn-(primary|secondary|success|danger|warning|info|light|dark|outline-\w+)/', $attributes->get('class', ''));
    $baseClasses = $getButtonClass();
    
    // Add default variant class only if no custom btn- class is provided
    if (!$hasCustomBtnClass) {
        $baseClasses .= ' ' . $getDefaultVariantClass();
    }
@endphp

@if($href)
    <a {{ $attributes->merge(['href' => $href, 'class' => $baseClasses]) }}>
        @if($getIcon())
            <i class="bi {{ $getIcon() }} me-2"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button 
        {{ $attributes->merge(['type' => $type, 'class' => $baseClasses]) }}
        @if($confirm)
            onclick="return confirm('{{ $confirm }}')"
        @endif
    >
        @if($getIcon())
            <i class="bi {{ $getIcon() }} me-2"></i>
        @endif
        {{ $slot }}
    </button>
@endif
