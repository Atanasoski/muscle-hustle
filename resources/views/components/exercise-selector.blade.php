@props([
    'exercises' => [],
    'equipmentTypes' => [],
    'muscleGroups' => [],
    'selectedExerciseId' => null,
    'name' => 'exercise_id',
])

<div
    x-data="{
        exercises: {{ Js::from($exercises) }},
        equipmentTypes: {{ Js::from($equipmentTypes) }},
        muscleGroups: {{ Js::from($muscleGroups) }},
        search: '',
        selectedEquipment: [],
        selectedMuscles: [],
        selectedExerciseId: {{ $selectedExerciseId ? $selectedExerciseId : 'null' }},
        isOpen: {{ $selectedExerciseId ? 'false' : 'true' }},
        
        toggleEquipment(id) {
            const index = this.selectedEquipment.indexOf(id);
            if (index > -1) {
                this.selectedEquipment.splice(index, 1);
            } else {
                this.selectedEquipment.push(id);
            }
        },
        
        toggleMuscle(id) {
            const index = this.selectedMuscles.indexOf(id);
            if (index > -1) {
                this.selectedMuscles.splice(index, 1);
            } else {
                this.selectedMuscles.push(id);
            }
        },
        
        get filteredExercises() {
            return this.exercises.filter(ex => {
                const q = this.search.trim().toLowerCase();
                const matchesSearch = q === '' ||
                    ex.name.toLowerCase().includes(q) ||
                    (ex.muscle_groups && ex.muscle_groups.some(mg => mg.name.toLowerCase().includes(q)));
                const primaryIds = ex.primary_muscle_group_ids || [];
                const matchesMuscle = this.selectedMuscles.length === 0 ||
                    primaryIds.some(id => this.selectedMuscles.includes(id));
                const matchesEquipment = this.selectedEquipment.length === 0 ||
                    (ex.equipment_type_id != null && this.selectedEquipment.includes(ex.equipment_type_id));
                return matchesSearch && matchesMuscle && matchesEquipment;
            });
        },
        get hasActiveChipFilters() {
            return this.selectedEquipment.length > 0 || this.selectedMuscles.length > 0;
        },
        get hasAnyActiveFilters() {
            return this.search.trim() !== '' || this.hasActiveChipFilters;
        },
        clearFilters() {
            this.selectedEquipment = [];
            this.selectedMuscles = [];
        },
        
        get selectedExercise() {
            return this.exercises.find(ex => ex.id === this.selectedExerciseId);
        },
        
        selectExercise(exercise) {
            this.selectedExerciseId = exercise.id;
            this.isOpen = false;
        },
        
        clearSelection() {
            this.selectedExerciseId = null;
            this.isOpen = true;
        }
    }"
>
    {{-- Hidden input for form submission --}}
    <input type="hidden" name="{{ $name }}" :value="selectedExerciseId">
    
    {{-- Selected Exercise Display --}}
    <template x-if="selectedExercise && !isOpen">
        <div class="flex items-center justify-between rounded-lg border border-brand-200 bg-brand-50 p-4 dark:border-brand-500/30 dark:bg-brand-500/10">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h2m-2 0V8m0 4v4m0-4H1m2 0h2m14-4h-2m2 0v4m0-4V8m0 4h2m-2 0h-2M6 12h12M6 12V8m0 4v4m12-4V8m0 4v4M6 12H4m14 0h2M6 16H4m14 0h2"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-brand-900 dark:text-brand-300" x-text="selectedExercise.name"></p>
                    <p class="text-xs text-brand-600 dark:text-brand-400">
                        <span x-text="selectedExercise.equipment_type_name"></span>
                        <template x-if="selectedExercise.muscle_groups.length > 0">
                            <span>
                                &bull;
                                <span x-text="selectedExercise.muscle_groups.map(mg => mg.name).join(', ')"></span>
                            </span>
                        </template>
                    </p>
                </div>
            </div>
            <button
                type="button"
                @click="clearSelection()"
                class="rounded-full p-2 text-brand-400 transition-colors hover:bg-brand-100 hover:text-brand-600 dark:hover:bg-brand-500/20 dark:hover:text-brand-300"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </template>
    
    {{-- Exercise Selector Panel --}}
    <template x-if="isOpen">
        <div class="space-y-4 rounded-lg border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
            {{-- Search Input --}}
            <div class="relative">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    type="text"
                    x-model="search"
                    placeholder="Search exercises..."
                    class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-4 text-sm outline-none transition-shadow focus:border-brand-300 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-brand-500"
                />
            </div>
            
            {{-- Equipment Type Filters --}}
            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Equipment Type
                </label>
                <div class="flex flex-wrap gap-2">
                    <template x-for="equipment in equipmentTypes" :key="equipment.id">
                        <button
                            type="button"
                            @click="toggleEquipment(equipment.id)"
                            :class="selectedEquipment.includes(equipment.id) 
                                ? 'bg-brand-50 border-brand-200 text-brand-700 shadow-sm dark:bg-brand-500/15 dark:border-brand-500/30 dark:text-brand-400' 
                                : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700'"
                            class="inline-flex items-center rounded-full border px-3 py-1.5 text-sm font-medium transition-all duration-200"
                        >
                            <template x-if="selectedEquipment.includes(equipment.id)">
                                <svg class="mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </template>
                            <span x-text="equipment.name"></span>
                        </button>
                    </template>
                </div>
            </div>
            
            {{-- Muscle Group Filters --}}
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Muscle Groups
                    </label>
                    <template x-if="hasActiveChipFilters">
                        <button
                            type="button"
                            @click="clearFilters()"
                            class="text-xs font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300"
                        >
                            Clear filters
                        </button>
                    </template>
                </div>
                <div class="flex flex-wrap gap-2">
                    <template x-for="muscle in muscleGroups" :key="muscle.id">
                        <button
                            type="button"
                            @click="toggleMuscle(muscle.id)"
                            :class="selectedMuscles.includes(muscle.id) 
                                ? 'bg-brand-50 border-brand-200 text-brand-700 shadow-sm dark:bg-brand-500/15 dark:border-brand-500/30 dark:text-brand-400' 
                                : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700'"
                            class="inline-flex items-center rounded-full border px-3 py-1.5 text-sm font-medium transition-all duration-200"
                        >
                            <template x-if="selectedMuscles.includes(muscle.id)">
                                <svg class="mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </template>
                            <span x-text="muscle.name"></span>
                        </button>
                    </template>
                </div>
            </div>
            
            {{-- Results List --}}
            <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Available Exercises (<span x-text="filteredExercises.length"></span>)
                </label>
                <div class="custom-scrollbar max-h-60 space-y-1 overflow-y-auto pr-1">
                    <template x-if="filteredExercises.length === 0">
                        <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            <template x-if="hasAnyActiveFilters">
                                <span>No exercises found. Try adjusting your filters or search.</span>
                            </template>
                            <template x-if="!hasAnyActiveFilters">
                                <span>No exercises found matching your filters.</span>
                            </template>
                        </div>
                    </template>
                    <template x-for="exercise in filteredExercises" :key="exercise.id">
                        <button
                            type="button"
                            @click="selectExercise(exercise)"
                            class="group flex w-full items-center justify-between rounded-lg border border-transparent px-3 py-2 text-left transition-all hover:border-gray-200 hover:bg-white hover:shadow-sm dark:hover:border-gray-600 dark:hover:bg-gray-800"
                        >
                            <span class="text-sm font-medium text-gray-700 group-hover:text-brand-600 dark:text-gray-300 dark:group-hover:text-brand-400" x-text="exercise.name"></span>
                            <span class="text-xs text-gray-400 group-hover:text-gray-500 dark:text-gray-500" x-text="exercise.equipment_type_name"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
