<div class="options-container">
    <label class="form-label">Answer Options</label>
    @php 
        $options = is_string($question->options ?? '{}') ? json_decode($question->options, true) : ($question->options ?? []);
        $options = $options ?: ['A' => '', 'B' => '', 'C' => '', 'D' => ''];
    @endphp
    
    @foreach(['A', 'B', 'C', 'D'] as $index => $letter)
        <div class="option-input d-flex align-items-center mb-2">
            <div class="form-check me-2">
                <input class="form-check-input" type="radio" name="correct_answer_{{ $question->id }}" 
                       value="{{ $letter }}" {{ $question->correct_answer === $letter ? 'checked' : '' }}>
            </div>
            <div class="input-group">
                <span class="input-group-text">{{ $letter }}.</span>
                <input type="text" class="form-control" 
                       placeholder="Enter option {{ $letter }}" 
                       value="{{ $options[$letter] ?? '' }}">
            </div>
            @if($index >= 2)
                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeOption(this)">
                    <i class="bi bi-dash"></i>
                </button>
            @endif
        </div>
    @endforeach
    
    @if(count($options) > 4)
        @foreach($options as $letter => $text)
            @if(!in_array($letter, ['A', 'B', 'C', 'D']))
                <div class="option-input d-flex align-items-center mb-2">
                    <div class="form-check me-2">
                        <input class="form-check-input" type="radio" name="correct_answer_{{ $question->id }}" 
                               value="{{ $letter }}" {{ $question->correct_answer === $letter ? 'checked' : '' }}>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text">{{ $letter }}.</span>
                        <input type="text" class="form-control" 
                               placeholder="Enter option {{ $letter }}" 
                               value="{{ $text }}">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeOption(this)">
                        <i class="bi bi-dash"></i>
                    </button>
                </div>
            @endif
        @endforeach
    @endif
    
    <button type="button" class="add-option-btn" onclick="addOption('{{ $question->id }}')">
        <i class="bi bi-plus"></i> Add Another Option
    </button>
    
    <small class="text-muted d-block mt-2">
        Select the radio button next to the correct answer. You must have at least 2 options.
    </small>
</div>

<script>
function addOption(questionId) {
    const container = document.querySelector(`[data-question-id="${questionId}"] .options-container`);
    const existingOptions = container.querySelectorAll('.option-input');
    const nextLetter = String.fromCharCode(65 + existingOptions.length); // A, B, C, D, E, F...
    
    const newOptionHtml = `
        <div class="option-input d-flex align-items-center mb-2">
            <div class="form-check me-2">
                <input class="form-check-input" type="radio" name="correct_answer_${questionId}" value="${nextLetter}">
            </div>
            <div class="input-group">
                <span class="input-group-text">${nextLetter}.</span>
                <input type="text" class="form-control" placeholder="Enter option ${nextLetter}">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeOption(this)">
                <i class="bi bi-dash"></i>
            </button>
        </div>
    `;
    
    const addButton = container.querySelector('.add-option-btn');
    addButton.insertAdjacentHTML('beforebegin', newOptionHtml);
}

function removeOption(button) {
    const optionElement = button.closest('.option-input');
    const container = optionElement.closest('.options-container');
    const remainingOptions = container.querySelectorAll('.option-input');
    
    if (remainingOptions.length > 2) {
        optionElement.remove();
        // Re-letter the remaining options
        const options = container.querySelectorAll('.option-input');
        options.forEach((option, index) => {
            const letter = String.fromCharCode(65 + index);
            option.querySelector('.input-group-text').textContent = letter + '.';
            option.querySelector('input[type="radio"]').value = letter;
        });
    } else {
        alert('You must have at least 2 options.');
    }
}
</script>
