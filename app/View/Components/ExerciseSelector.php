<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ExerciseSelector extends Component
{
    public string $name;

    public string $id;

    public mixed $exercises;

    public ?string $selected;

    public bool $required;

    public string $placeholder;

    /**
     * Create a new component instance.
     */
    public function __construct(
        mixed $exercises,
        string $name = 'exercise_id',
        string $id = 'exercise-selector',
        ?string $selected = null,
        bool $required = true,
        string $placeholder = 'Search exercises...'
    ) {
        $this->exercises = $exercises;
        $this->name = $name;
        $this->id = $id;
        $this->selected = $selected;
        $this->required = $required;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.exercise-selector');
    }
}
