<div class="exercise-selector-component" style="position: relative;">
    <input type="hidden" id="{{ $id }}-input" name="{{ $name }}" value="{{ $selected }}" {{ $required ? 'required' : '' }}>
    
    <div class="mb-3">
        <input type="text" 
               id="{{ $id }}-search" 
               class="form-control" 
               placeholder="🔍 {{ $placeholder }}" 
               autocomplete="off"
               @if($selected)
                   @foreach($exercises as $categoryExercises)
                       @foreach($categoryExercises as $exercise)
                           @if($exercise->id == $selected)
                               value="{{ $exercise->name }}"
                           @endif
                       @endforeach
                   @endforeach
               @endif>
        
        <div id="{{ $id }}-list" class="list-group" style="position: absolute; z-index: 1050; width: 100%; max-height: 300px; overflow-y: auto; display: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
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
    const listContainer = document.getElementById(`${selectorId}-list`);
    
    if (!searchInput) return;
    
    // Search functionality - show list when typing
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        // Show the list when user starts typing
        listContainer.style.display = 'block';
        
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
    
    // Show list when focusing on input and reset filters
    searchInput.addEventListener('focus', function() {
        listContainer.style.display = 'block';
        
        // Show all exercises when opening the list
        exerciseOptions.forEach(opt => opt.classList.remove('d-none'));
        categories.forEach(cat => cat.classList.remove('d-none'));
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
            
            // Populate search input with selected exercise name
            searchInput.value = this.getAttribute('data-name');
            
            // Hide the list after selection
            listContainer.style.display = 'none';
        });
    });
    
    // Close list when clicking outside
    document.addEventListener('click', function(e) {
        const component = searchInput.closest('.exercise-selector-component');
        if (component && !component.contains(e.target)) {
            listContainer.style.display = 'none';
        }
    });
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

