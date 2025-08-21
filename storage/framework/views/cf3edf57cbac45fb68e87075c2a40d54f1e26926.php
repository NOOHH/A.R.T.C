<!-- Quiz Table Component -->
<div class="table-responsive">
    <?php if($quizzes->count() > 0): ?>
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Quiz Title</th>
                    <th>Program</th>
                    <th>Questions</th>
                    <th>Settings</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <strong><?php echo e($quiz->quiz_title); ?></strong>
                        <?php if($quiz->tags && is_array($quiz->tags)): ?>
                            <br>
                            <?php $__currentLoopData = $quiz->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-info text-dark me-1"><?php echo e($tag); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($quiz->program->program_name ?? '-'); ?></td>
                    <td>
                        <span class="badge bg-primary">
                            <?php if(property_exists($quiz, 'questions') && $quiz->questions): ?>
                                <?php echo e($quiz->questions->count() ?? 0); ?> Questions
                            <?php elseif(isset($quiz->total_questions)): ?>
                                <?php echo e($quiz->total_questions); ?> Questions
                            <?php else: ?>
                                0 Questions
                            <?php endif; ?>
                        </span>
                        <?php if($quiz->time_limit): ?>
                            <br><small class="text-muted"><i class="bi bi-clock"></i> <?php echo e($quiz->time_limit); ?>min</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($quiz->allow_retakes): ?>
                            <span class="badge bg-info text-dark">Retakes</span>
                        <?php endif; ?>
                        <?php if($quiz->instant_feedback): ?>
                            <span class="badge bg-warning text-dark">Instant Feedback</span>
                        <?php endif; ?>
                        <?php if($quiz->randomize_order): ?>
                            <span class="badge bg-secondary">Random Order</span>
                        <?php endif; ?>
                        <?php if(isset($quiz->randomize_mc_options) && $quiz->randomize_mc_options): ?>
                            <span class="badge bg-secondary">Random Options</span>
                        <?php endif; ?>
                        <?php if($quiz->max_attempts > 1): ?>
                            <span class="badge bg-info">Max: <?php echo e($quiz->max_attempts); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <small class="text-muted"><?php echo e($quiz->created_at->format('M j, Y')); ?></small>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1" role="group">
                            <!-- View Questions (Modal) -->
                            <button class="btn btn-outline-primary btn-sm view-questions-modal-btn" 
                                    data-quiz-id="<?php echo e($quiz->quiz_id); ?>" 
                                    title="View Questions">
                                <i class="bi bi-list-ul"></i>
                                <span class="d-none d-md-inline ms-1">Questions</span>
                            </button>
                            
                            <!-- Preview Quiz -->
                            <button class="btn btn-outline-info btn-sm preview-quiz-btn" 
                                    data-quiz-id="<?php echo e($quiz->quiz_id); ?>"
                                    title="Preview Quiz">
                                <i class="bi bi-eye"></i>
                                <span class="d-none d-md-inline ms-1">Preview</span>
                            </button>

                            <!-- Status-specific actions -->
                            <?php if($status === 'draft'): ?>
                                <!-- Edit Quiz -->
                                <button class="btn btn-outline-warning btn-sm edit-quiz-btn" 
                                        data-quiz-id="<?php echo e($quiz->quiz_id); ?>"
                                        data-edit-quiz="true"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#createQuizModal"
                                        title="Edit Quiz">
                                    <i class="bi bi-pencil"></i>
                                    <span class="d-none d-md-inline ms-1">Edit</span>
                                </button>
                                
                                <button class="btn btn-success btn-sm" 
                                        onclick="publishQuiz('<?php echo e($quiz->quiz_id); ?>')"
                                        title="Publish Quiz">
                                    <i class="bi bi-check-circle"></i>
                                    <span class="d-none d-md-inline ms-1">Publish</span>
                                </button>
                            <?php elseif($status === 'published'): ?>
                                <button class="btn btn-warning btn-sm" 
                                        onclick="archiveQuiz('<?php echo e($quiz->quiz_id); ?>')"
                                        title="Archive Quiz">
                                    <i class="bi bi-archive"></i>
                                    <span class="d-none d-md-inline ms-1">Archive</span>
                                </button>
                            <?php elseif($status === 'archived'): ?>
                                <button class="btn btn-info btn-sm" 
                                        onclick="restoreQuiz('<?php echo e($quiz->quiz_id); ?>')"
                                        title="Restore Quiz">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    <span class="d-none d-md-inline ms-1">Restore</span>
                                </button>
                                
                                <button class="btn btn-danger btn-sm" 
                                        onclick="deleteQuiz('<?php echo e($quiz->quiz_id); ?>')"
                                        title="Delete Quiz Permanently">
                                    <i class="bi bi-trash"></i>
                                    <span class="d-none d-md-inline ms-1">Delete</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="text-center py-4">
            <i class="bi bi-inbox display-4 text-muted"></i>
            <p class="text-muted mt-2">
                <?php if($status === 'draft'): ?>
                    No draft quizzes yet. Create your first quiz above!
                <?php elseif($status === 'published'): ?>
                    No published quizzes yet. Publish a draft quiz to see it here.
                <?php else: ?>
                    No archived quizzes yet.
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\Quiz Generator\professor\quiz-table.blade.php ENDPATH**/ ?>