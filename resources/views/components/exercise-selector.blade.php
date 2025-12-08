<div class="exercise-selector-component">
    <input type="hidden" id="{{ $id }}-input" name="{{ $name }}" value="{{ $selected }}" {{ $required ? 'required' : '' }}>
    
    <div class="mb-3">
        <input type="text" 
               id="{{ $id }}-search" 
               class="form-control mb-2" 
               placeholder="🔍 {{ $placeholder }}" 
               autocomplete="off">
        
        <div id="{{ $id }}-list" class="list-group" style="max-height: 300px; overflow-y: auto;">
            @foreach($exercises as $categoryName => $categoryExercises)
                <div class="exercise-category" data-category="{{ $categoryName }}">
                    <div class="list-group-item bg-light fw-bold text-primary small py-2 border-0">
                        {{ $categoryName }}
                    </div>
                    @foreach($categoryExercises as $exercise)
                        <a href="#" 
                           class="list-group-item list-group-item-action exercise-option {{ $selected == $exercise->id ? 'active' : '' }}" 
                           data-id="{{ $exercise->id }}" 
                           data-name="{{ $exercise->name }}">
                            {{ $exercise->name }}
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>
        
        <div id="{{ $id }}-selected" class="mt-2 {{ $selected ? '' : 'd-none' }}">
            <small class="text-muted">Selected:</small> 
            <span class="badge bg-primary" id="{{ $id }}-selected-name">
                @if($selected)
                    @foreach($exercises as $categoryExercises)
                        @foreach($categoryExercises as $exercise)
                            @if($exercise->id == $selected)
                                {{ $exercise->name }}
                            @endif
                        @endforeach
                    @endforeach
                @endif
            </span>
            <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" id="{{ $id }}-clear">Clear</button>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
// Reusable Exercise Selector functionality
function initExerciseSelector(selectorId) {
    const searchInput = document.getElementById(`${selectorId}-search`);
    const exerciseOptions = document.querySelectorAll(`#${selectorId}-list .exercise-option`);
    const categories = document.querySelectorAll(`#${selectorId}-list .exercise-category`);
    const selectedInput = document.getElementById(`${selectorId}-input`);
    const selectedDiv = document.getElementById(`${selectorId}-selected`);
    const selectedName = document.getElementById(`${selectorId}-selected-name`);
    const clearBtn = document.getElementById(`${selectorId}-clear`);
    
    if (!searchInput) return;
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        exerciseOptions.forEach(option => {
            const text = option.getAttribute('data-name').toLowerCase();
            if (text.includes(searchTerm)) {
                option.classList.remove('d-none');
            } else {
                option.classList.add('d-none');
            }
        });
        
        // Hide categories with no visible exercises
        categories.forEach(category => {
            const visibleExercises = category.querySelectorAll('.exercise-option:not(.d-none)');
            if (visibleExercises.length === 0) {
                category.classList.add('d-none');
            } else {
                category.classList.remove('d-none');
            }
        });
    });
    
    // Exercise selection
    exerciseOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active state from all options
            exerciseOptions.forEach(opt => opt.classList.remove('active'));
            
            // Add active state to selected
            this.classList.add('active');
            
            // Set hidden input value
            selectedInput.value = this.getAttribute('data-id');
            
            // Show selected exercise badge
            selectedName.textContent = this.getAttribute('data-name');
            selectedDiv.classList.remove('d-none');
        });
    });
    
    // Clear selection
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            exerciseOptions.forEach(opt => opt.classList.remove('active'));
            selectedInput.value = '';
            selectedDiv.classList.add('d-none');
            searchInput.value = '';
            
            // Show all exercises
            exerciseOptions.forEach(opt => opt.classList.remove('d-none'));
            categories.forEach(cat => cat.classList.remove('d-none'));
        });
    }
}

// Auto-initialize all exercise selectors on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.exercise-selector-component').forEach(component => {
        const selectorId = component.querySelector('[id$="-input"]')?.id.replace('-input', '');
        if (selectorId) {
            initExerciseSelector(selectorId);
        }
    });
});
</script>
@endpush
@endonce
