<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public string $variant;

    public string $type;

    public string $size;

    public ?string $icon;

    public ?string $href;

    public ?string $confirm;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $variant = 'primary',
        string $type = 'button',
        string $size = 'md',
        ?string $icon = null,
        ?string $href = null,
        ?string $confirm = null
    ) {
        $this->variant = $variant;
        $this->type = $type;
        $this->size = $size;
        $this->icon = $icon;
        $this->href = $href;
        $this->confirm = $confirm;
    }

    /**
     * Get the button class based on variant
     */
    public function getButtonClass(): string
    {
        $baseClass = 'btn';

        $sizeClass = match ($this->size) {
            'sm' => 'btn-sm',
            'lg' => 'btn-lg',
            default => '',
        };

        return trim("$baseClass $sizeClass");
    }

    /**
     * Get the default variant class (used when no custom btn- class is provided)
     */
    public function getDefaultVariantClass(): string
    {
        return match ($this->variant) {
            'create' => 'btn-primary',
            'edit' => 'btn-info text-white',
            'delete' => 'btn-danger',
            'save' => 'btn-success',
            'cancel' => 'btn-secondary',
            'primary' => 'btn-primary',
            'secondary' => 'btn-secondary',
            'success' => 'btn-success',
            'danger' => 'btn-danger',
            'warning' => 'btn-warning',
            'info' => 'btn-info text-white',
            default => 'btn-primary',
        };
    }

    /**
     * Get the default icon for variant
     */
    public function getIcon(): ?string
    {
        if ($this->icon) {
            return $this->icon;
        }

        return match ($this->variant) {
            'create' => 'bi-plus-circle',
            'edit' => 'bi-pencil',
            'delete' => 'bi-trash',
            'save' => 'bi-check-circle',
            'cancel' => 'bi-x-circle',
            default => null,
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.button');
    }
}
