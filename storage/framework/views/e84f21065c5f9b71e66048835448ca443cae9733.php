<div class="options-container">
    <label class="form-label">Answer Options</label>
    <?php 
        $options = is_string($question->options ?? '{}') ? json_decode($question->options, true) : ($question->options ?? []);
        $options = $options ?: ['A' => '', 'B' => '', 'C' => '', 'D' => ''];
    ?>
    
    <?php $__currentLoopData = ['A', 'B', 'C', 'D']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $letter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="option-input d-flex align-items-center mb-2">
            <div class="form-check me-2">
                <input class="form-check-input" type="radio" name="correct_answer_<?php echo e($question->id); ?>" 
                       value="<?php echo e($letter); ?>" <?php echo e($question->correct_answer === $letter ? 'checked' : ''); ?>>
            </div>
            <div class="input-group">
                <span class="input-group-text"><?php echo e($letter); ?>.</span>
                <input type="text" class="form-control" 
                       placeholder="Enter option <?php echo e($letter); ?>" 
                       value="<?php echo e($options[$letter] ?? ''); ?>">
            </div>
            <?php if($index >= 2): ?>
                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeOption(this)">
                    <i class="bi bi-dash"></i>
                </button>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    <?php if(count($options) > 4): ?>
        <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $letter => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!in_array($letter, ['A', 'B', 'C', 'D'])): ?>
                <div class="option-input d-flex align-items-center mb-2">
                    <div class="form-check me-2">
                        <input class="form-check-input" type="radio" name="correct_answer_<?php echo e($question->id); ?>" 
                               value="<?php echo e($letter); ?>" <?php echo e($question->correct_answer === $letter ? 'checked' : ''); ?>>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><?php echo e($letter); ?>.</span>
                        <input type="text" class="form-control" 
                               placeholder="Enter option <?php echo e($letter); ?>" 
                               value="<?php echo e($text); ?>">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeOption(this)">
                        <i class="bi bi-dash"></i>
                    </button>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    
    <button type="button" class="add-option-btn" onclick="addOption('<?php echo e($question->id); ?>')">
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
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\partials\question-options-multiple-choice.blade.php ENDPATH**/ ?>