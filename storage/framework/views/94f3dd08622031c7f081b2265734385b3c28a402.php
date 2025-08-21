
<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-eye"></i> Quiz Preview</h2>
        <a href="<?php echo e(route('professor.quiz-generator')); ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Quiz Manager
        </a>
    </div>
    
    <!-- Quiz Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="mb-0"><?php echo e($quiz->quiz_title); ?></h4>
            <?php if($quiz->quiz_description): ?>
                <p class="text-muted mb-0 mt-2"><?php echo e($quiz->quiz_description); ?></p>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Program:</strong> <?php echo e($quiz->program->program_name ?? 'N/A'); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge 
                            <?php if($quiz->status === 'draft'): ?> bg-warning 
                            <?php elseif($quiz->status === 'published'): ?> bg-success
                            <?php else: ?> bg-secondary <?php endif; ?>">
                            <?php echo e(ucfirst($quiz->status)); ?>

                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Questions:</strong> <?php echo e($quiz->questions->count()); ?></p>
                    <p><strong>Time Limit:</strong> <?php echo e($quiz->time_limit); ?> minutes</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Questions -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list-ol"></i> Questions</h5>
        </div>
        <div class="card-body">
            <?php $__empty_1 = true; $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="question-preview mb-4 p-3 border rounded">
                    <h6 class="fw-bold">Question <?php echo e($index + 1); ?></h6>
                    <p class="mb-3"><?php echo e($question->question_text); ?></p>
                    
                    <?php if($question->question_type === 'multiple_choice'): ?>
                        <div class="options">
                            <?php if($question->options && is_array($question->options)): ?>
                                <?php $__currentLoopData = $question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $letter = chr(65 + $optionIndex); ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="question_<?php echo e($question->id); ?>" disabled 
                                               <?php if($question->correct_answer === $letter || $question->correct_answer === $option): ?> checked <?php endif; ?>>
                                        <label class="form-check-label">
                                            <strong><?php echo e($letter); ?>.</strong> <?php echo e($option); ?>

                                            <?php if($question->correct_answer === $letter || $question->correct_answer === $option): ?>
                                                <span class="badge bg-success ms-2">Correct</span>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    <?php elseif($question->question_type === 'true_false'): ?>
                        <div class="options">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="question_<?php echo e($question->id); ?>" disabled 
                                       <?php if(strtolower($question->correct_answer) === 'true' || $question->correct_answer === 'A' || $question->correct_answer === 'True'): ?> checked <?php endif; ?>>
                                <label class="form-check-label">
                                    True 
                                    <?php if(strtolower($question->correct_answer) === 'true' || $question->correct_answer === 'A' || $question->correct_answer === 'True'): ?> 
                                        <span class="badge bg-success ms-2">Correct</span> 
                                    <?php endif; ?>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="question_<?php echo e($question->id); ?>" disabled 
                                       <?php if(strtolower($question->correct_answer) === 'false' || $question->correct_answer === 'B' || $question->correct_answer === 'False'): ?> checked <?php endif; ?>>
                                <label class="form-check-label">
                                    False 
                                    <?php if(strtolower($question->correct_answer) === 'false' || $question->correct_answer === 'B' || $question->correct_answer === 'False'): ?> 
                                        <span class="badge bg-success ms-2">Correct</span> 
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="form-group">
                            <label class="form-label"><strong>Expected Answer:</strong></label>
                            <div class="alert alert-info"><?php echo e($question->correct_answer); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($question->explanation): ?>
                        <div class="explanation mt-3 p-2 bg-light rounded">
                            <strong>Explanation:</strong> <?php echo e($question->explanation); ?>

                        </div>
                    <?php endif; ?>
                    
                    <div class="text-muted small mt-2">
                        <i class="bi bi-award"></i> Points: <?php echo e($question->points ?? 1); ?>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox display-4"></i>
                    <p class="mt-2">No questions in this quiz yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\Quiz Generator\professor\quiz-preview.blade.php ENDPATH**/ ?>