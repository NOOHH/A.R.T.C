<div class="quiz-questions">
    <div class="quiz-header mb-4">
        <h4><?php echo e($quiz->quiz_title); ?></h4>
        <div class="quiz-meta">
            <span class="badge bg-primary"><?php echo e(ucfirst($quiz->difficulty ?? 'medium')); ?></span>
            <span class="badge bg-info"><?php echo e($quiz->questions->count()); ?> Questions</span>
            <?php if($quiz->time_limit): ?>
                <span class="badge bg-warning"><?php echo e($quiz->time_limit); ?> Minutes</span>
            <?php endif; ?>
        </div>
        <?php if($quiz->instructions): ?>
            <div class="mt-3">
                <strong>Instructions:</strong>
                <p class="text-muted"><?php echo e($quiz->instructions); ?></p>
            </div>
        <?php endif; ?>
        <?php if($quiz->tags): ?>
            <div class="mt-2">
                <strong>Tags:</strong>
                <?php $__currentLoopData = json_decode($quiz->tags, true) ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge bg-secondary me-1"><?php echo e($tag); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if($quiz->questions->count() > 0): ?>
        <div class="questions">
            <?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="question-card mb-4 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="mb-2">Question <?php echo e($index + 1); ?></h6>
                        <span class="badge bg-info"><?php echo e($question->points ?? 1); ?> <?php echo e(($question->points ?? 1) == 1 ? 'point' : 'points'); ?></span>
                    </div>
                    
                    <p class="mb-3"><?php echo e($question->question_text); ?></p>
                    
                    <?php if($question->question_type === 'multiple_choice'): ?>
                        <div class="options">
                            <?php $options = is_string($question->options) ? json_decode($question->options, true) : $question->options ?>
                            <?php if($options && is_array($options)): ?>
                                <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="question_<?php echo e($question->id); ?>" disabled>
                                        <label class="form-check-label">
                                            <strong><?php echo e($key); ?>.</strong> <?php echo e($option); ?>

                                            <?php if($key === $question->correct_answer): ?>
                                                <span class="text-success ms-2"><i class="bi bi-check-circle-fill"></i> Correct Answer</span>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <p class="text-muted">No options available</p>
                            <?php endif; ?>
                        </div>
                    <?php elseif($question->question_type === 'true_false'): ?>
                        <div class="options">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="question_<?php echo e($question->id); ?>" disabled>
                                <label class="form-check-label">
                                    True
                                    <?php if($question->correct_answer === 'A' || $question->correct_answer === 'True'): ?>
                                        <span class="text-success ms-2"><i class="bi bi-check-circle-fill"></i> Correct Answer</span>
                                    <?php endif; ?>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="question_<?php echo e($question->id); ?>" disabled>
                                <label class="form-check-label">
                                    False
                                    <?php if($question->correct_answer === 'B' || $question->correct_answer === 'False'): ?>
                                        <span class="text-success ms-2"><i class="bi bi-check-circle-fill"></i> Correct Answer</span>
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>
                    <?php elseif($question->question_type === 'short_answer'): ?>
                        <div class="mb-3">
                            <label class="form-label">Answer:</label>
                            <input type="text" class="form-control" disabled placeholder="Short answer expected">
                            <small class="text-muted">Expected answer: <?php echo e($question->correct_answer); ?></small>
                        </div>
                    <?php elseif($question->question_type === 'essay'): ?>
                        <div class="mb-3">
                            <label class="form-label">Essay Answer:</label>
                            <textarea class="form-control" rows="4" disabled placeholder="Essay answer expected"></textarea>
                            <?php if($question->correct_answer): ?>
                                <small class="text-muted">Sample answer: <?php echo e($question->correct_answer); ?></small>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Unknown question type: <?php echo e($question->question_type); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php if($question->explanation): ?>
                        <div class="mt-3 p-2 bg-light rounded">
                            <strong>Explanation:</strong>
                            <p class="mb-0 small text-muted"><?php echo e($question->explanation); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No questions found for this quiz.
        </div>
    <?php endif; ?>
    
    <div class="quiz-footer mt-4">
        <div class="row">
            <div class="col-md-6">
                <strong>Total Questions:</strong> <?php echo e($quiz->questions->count()); ?><br>
                <strong>Total Points:</strong> <?php echo e($quiz->questions->sum('points') ?? $quiz->questions->count()); ?><br>
                <?php if($quiz->time_limit): ?>
                    <strong>Time Limit:</strong> <?php echo e($quiz->time_limit); ?> minutes<br>
                <?php endif; ?>
            </div>
            <div class="col-md-6 text-end">
                <strong>Created:</strong> <?php echo e($quiz->created_at->format('M d, Y')); ?><br>
                <strong>Status:</strong> 
                <?php if($quiz->status === 'draft'): ?>
                    <span class="badge bg-warning">Draft</span>
                <?php elseif($quiz->status === 'published'): ?>
                    <span class="badge bg-success">Published</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Archived</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\Quiz Generator\professor\quiz-questions.blade.php ENDPATH**/ ?>