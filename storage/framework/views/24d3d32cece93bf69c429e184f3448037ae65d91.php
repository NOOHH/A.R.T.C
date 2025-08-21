<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Quiz Title</th>
                <th>Program</th>
                <th>Module</th>
                <th>Course</th>
                <th>Questions</th>
                <th>Time Limit</th>
                <th>Attempts</th>
                <th>Deadline</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0"><?php echo e($quiz->quiz_title); ?></h6>
                                <?php if($quiz->quiz_description): ?>
                                    <small class="text-muted"><?php echo e(Str::limit($quiz->quiz_description, 50)); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-primary"><?php echo e($quiz->program->program_name ?? 'N/A'); ?></span>
                    </td>
                    <td>
                        <?php if($quiz->module): ?>
                            <span class="badge bg-info"><?php echo e($quiz->module->module_name); ?></span>
                        <?php else: ?>
                            <span class="text-muted">All Modules</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($quiz->course): ?>
                            <span class="badge bg-secondary"><?php echo e($quiz->course->subject_name); ?></span>
                        <?php else: ?>
                            <span class="text-muted">All Courses</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-dark"><?php echo e($quiz->questions->count()); ?> questions</span>
                    </td>
                    <td>
                        <?php if($quiz->time_limit): ?>
                            <i class="bi bi-clock"></i> <?php echo e($quiz->time_limit); ?> mins
                        <?php else: ?>
                            <span class="text-muted">No limit</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($quiz->max_attempts && $quiz->max_attempts > 0): ?>
                            <i class="bi bi-arrow-repeat"></i> <?php echo e($quiz->max_attempts); ?>

                        <?php else: ?>
                            <span class="text-success"><i class="bi bi-infinity"></i> Unlimited</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($quiz->due_date): ?>
                            <?php
                                $dueDate = \Carbon\Carbon::parse($quiz->due_date);
                                $isOverdue = $dueDate->isPast();
                            ?>
                            <div class="small <?php echo e($isOverdue ? 'text-danger' : 'text-warning'); ?>">
                                <i class="bi bi-calendar-event"></i>
                                <?php echo e($dueDate->format('M d, Y')); ?><br>
                                <small><?php echo e($dueDate->format('g:i A')); ?></small>
                                <?php if($isOverdue): ?>
                                    <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Overdue</small>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-muted">No deadline</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="small text-muted">
                            <?php echo e($quiz->created_at->format('M d, Y')); ?><br>
                            <small><?php echo e($quiz->created_at->format('g:i A')); ?></small>
                        </div>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <?php if($status !== 'archived'): ?>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="editQuiz(<?php echo e($quiz->quiz_id); ?>)" title="Edit Quiz">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            <?php endif; ?>
                            
                            <?php if($status === 'draft'): ?>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="changeQuizStatus(<?php echo e($quiz->quiz_id); ?>, 'published')" title="Publish Quiz">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeQuizStatus(<?php echo e($quiz->quiz_id); ?>, 'archived')" title="Archive Quiz">
                                    <i class="bi bi-archive"></i>
                                </button>
                            <?php elseif($status === 'published'): ?>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="changeQuizStatus(<?php echo e($quiz->quiz_id); ?>, 'draft')" title="Move to Draft">
                                    <i class="bi bi-file-text"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeQuizStatus(<?php echo e($quiz->quiz_id); ?>, 'archived')" title="Archive Quiz">
                                    <i class="bi bi-archive"></i>
                                </button>
                            <?php elseif($status === 'archived'): ?>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="changeQuizStatus(<?php echo e($quiz->quiz_id); ?>, 'draft')" title="Restore to Draft">
                                    <i class="bi bi-file-text"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="changeQuizStatus(<?php echo e($quiz->quiz_id); ?>, 'published')" title="Publish Quiz">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteQuiz(<?php echo e($quiz->quiz_id); ?>)" title="Delete Quiz">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    
    <?php if($quizzes->isEmpty()): ?>
        <div class="text-center py-4">
            <p class="text-muted">No <?php echo e($status); ?> quizzes found</p>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\quiz-generator\quiz-table.blade.php ENDPATH**/ ?>