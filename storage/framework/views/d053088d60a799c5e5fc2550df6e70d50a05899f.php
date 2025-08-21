<div class="options-container">
    <label class="form-label">Sample Answer / Grading Rubric</label>
    <textarea class="form-control" name="correct_answer_<?php echo e($question->id); ?>" rows="4" 
              placeholder="Provide a sample answer or grading criteria..."><?php echo e($question->correct_answer); ?></textarea>
    <small class="text-muted">
        This will help with manual grading. You can provide key points that should be covered in the answer.
    </small>
    
    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label">Minimum Word Count</label>
            <input type="number" class="form-control" name="min_words_<?php echo e($question->id); ?>" 
                   value="<?php echo e(json_decode($question->metadata ?? '{}', true)['min_words'] ?? ''); ?>" 
                   placeholder="e.g., 50">
        </div>
        <div class="col-md-6">
            <label class="form-label">Maximum Word Count</label>
            <input type="number" class="form-control" name="max_words_<?php echo e($question->id); ?>" 
                   value="<?php echo e(json_decode($question->metadata ?? '{}', true)['max_words'] ?? ''); ?>" 
                   placeholder="e.g., 500">
        </div>
    </div>
    
    <div class="form-check mt-3">
        <input class="form-check-input" type="checkbox" id="requires_manual_grading_<?php echo e($question->id); ?>" 
               <?php echo e((json_decode($question->metadata ?? '{}', true)['requires_manual_grading'] ?? true) ? 'checked' : ''); ?>>
        <label class="form-check-label" for="requires_manual_grading_<?php echo e($question->id); ?>">
            Requires manual grading
        </label>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\partials\question-options-essay.blade.php ENDPATH**/ ?>