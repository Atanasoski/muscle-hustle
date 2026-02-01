<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class ExerciseSelector extends Component
{
    public string $name;

    public string $id;

    public mixed $exercises;

    public ?string $selected;

    public ?string $selectedExerciseId;

    public bool $required;

    public string $placeholder;

    public array $equipmentTypes;

    public array $muscleGroups;

    /**
     * Create a new component instance.
     */
    public function __construct(
        mixed $exercises,
        string $name = 'exercise_id',
        string $id = 'exercise-selector',
        ?string $selected = null,
        ?string $selectedExerciseId = null,
        bool $required = true,
        string $placeholder = 'Search exercises...',
        array|Collection $equipmentTypes = [],
        array|Collection $muscleGroups = []
    ) {
        $this->exercises = $exercises;
        $this->name = $name;
        $this->id = $id;
        $this->selected = $selected ?? $selectedExerciseId;
        $this->selectedExerciseId = $selected ?? $selectedExerciseId;
        $this->required = $required;
        $this->placeholder = $placeholder;
        $this->equipmentTypes = $equipmentTypes instanceof Collection
            ? $equipmentTypes->values()->all()
            : $equipmentTypes;
        $this->muscleGroups = $muscleGroups instanceof Collection
            ? $muscleGroups->values()->all()
            : $muscleGroups;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.exercise-selector');
    }
}
