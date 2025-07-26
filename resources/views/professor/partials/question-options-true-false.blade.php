<div class="options-container">
    <label class="form-label">Correct Answer</label>
    
    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="correct_answer_{{ $question->id }}" 
               value="A" {{ $question->correct_answer === 'A' || $question->correct_answer === 'True' ? 'checked' : '' }}>
        <label class="form-check-label">
            <strong>True</strong>
        </label>
    </div>
    
    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="correct_answer_{{ $question->id }}" 
               value="B" {{ $question->correct_answer === 'B' || $question->correct_answer === 'False' ? 'checked' : '' }}>
        <label class="form-check-label">
            <strong>False</strong>
        </label>
    </div>
    
    <small class="text-muted">Select whether the statement is true or false.</small>
</div>
