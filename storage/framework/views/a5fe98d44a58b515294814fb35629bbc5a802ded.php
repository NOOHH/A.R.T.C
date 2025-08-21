<div class="options-container">
    <label class="form-label">Expected Answer</label>
    <input type="text" class="form-control" name="correct_answer_<?php echo e($question->id); ?>" 
           value="<?php echo e($question->correct_answer); ?>" 
           placeholder="Enter the expected answer">
    <small class="text-muted">
        Students will need to provide a short text answer. This will be used for automatic grading if exact matching is enabled.
    </small>
    
    <div class="form-check mt-3">
        <input class="form-check-input" type="checkbox" id="case_sensitive_<?php echo e($question->id); ?>" 
               <?php echo e((json_decode($question->metadata ?? '{}', true)['case_sensitive'] ?? false) ? 'checked' : ''); ?>>
        <label class="form-check-label" for="case_sensitive_<?php echo e($question->id); ?>">
            Case sensitive matching
        </label>
    </div>
    
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="exact_match_<?php echo e($question->id); ?>" 
               <?php echo e((json_decode($question->metadata ?? '{}', true)['exact_match'] ?? true) ? 'checked' : ''); ?>>
        <label class="form-check-label" for="exact_match_<?php echo e($question->id); ?>">
            Require exact match (otherwise partial credit may be given)
        </label>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\partials\question-options-short-answer.blade.php ENDPATH**/ ?>