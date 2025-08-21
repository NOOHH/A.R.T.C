    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<!-- Quiz Information Panel -->
<div class="quiz-settings-panel">
    <div class="row">
        <div class="col-md-8">
            <h5 class="mb-3">Quiz Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Quiz Title</label>
                    <input type="text" class="form-control" id="quiz_title" value="<?php echo e($quiz->quiz_title); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Time Limit (minutes)</label>
                    <input type="number" class="form-control" id="time_limit" value="<?php echo e($quiz->time_limit); ?>">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="status">
                        <option value="draft" <?php echo e($quiz->status === 'draft' ? 'selected' : ''); ?>>Draft</option>
                        <option value="published" <?php echo e($quiz->status === 'published' ? 'selected' : ''); ?>>Published</option>
                        <option value="archived" <?php echo e($quiz->status === 'archived' ? 'selected' : ''); ?>>Archived</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Program</label>
                    <select class="form-select" id="program_id" name="program_id" required onchange="fetchModulesForProgram(this.value)">
                        <option value="">Select Program</option>
                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($program->id); ?>" <?php echo e((isset($quiz->program_id) && $quiz->program_id == $program->id) ? 'selected' : ''); ?>><?php echo e($program->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Module</label>
                    <select class="form-select" id="module_id" name="module_id" required onchange="fetchCoursesForModule(this.value)">
                        <option value="">Select Module</option>
                        <?php if(isset($modules)): ?>
                            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($module->id); ?>" <?php echo e((isset($quiz->module_id) && $quiz->module_id == $module->id) ? 'selected' : ''); ?>><?php echo e($module->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Course</label>
                    <select class="form-select" id="course_id" name="course_id" required>
                        <option value="">Select Course</option>
                        <?php if(isset($courses)): ?>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course->id); ?>" <?php echo e((isset($quiz->course_id) && $quiz->course_id == $course->id) ? 'selected' : ''); ?>><?php echo e($course->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Max Attempts</label>
                    <input type="number" class="form-control" id="max_attempts" value="<?php echo e($quiz->max_attempts ?? 1); ?>" min="1">
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label">Instructions</label>
                <textarea class="form-control" id="instructions" rows="3"><?php echo e($quiz->instructions); ?></textarea>
            </div>
        </div>
        <div class="col-md-4">
            <h5 class="mb-3">Quiz Settings</h5>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="allow_retakes" <?php echo e($quiz->allow_retakes ? 'checked' : ''); ?>>
                <label class="form-check-label" for="allow_retakes">
                    Allow Retakes
                </label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="instant_feedback" <?php echo e($quiz->instant_feedback ? 'checked' : ''); ?>>
                <label class="form-check-label" for="instant_feedback">
                    Instant Feedback
                </label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="show_correct_answers" <?php echo e($quiz->show_correct_answers ? 'checked' : ''); ?>>
                <label class="form-check-label" for="show_correct_answers">
                    Show Correct Answers
                </label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="randomize_order" <?php echo e($quiz->randomize_order ? 'checked' : ''); ?>>
                <label class="form-check-label" for="randomize_order">
                    Randomize Questions
                </label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="randomize_mc_options" <?php echo e($quiz->randomize_mc_options ?? false ? 'checked' : ''); ?>>
                <label class="form-check-label" for="randomize_mc_options">
                    Randomize Multiple Choice Options
                </label>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Max Retakes</label>
                <input type="number" class="form-control" id="max_attempts" value="<?php echo e($quiz->max_attempts ?? 1); ?>" min="1">
            </div>
        </div>
    </div>
</div>

<!-- Questions Section -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Questions (<?php echo e($quiz->questions->count()); ?>)</h4>
    <button type="button" class="btn btn-primary" onclick="addNewQuestionInModal()">
        <i class="bi bi-plus-circle"></i> Add Question
    </button>
</div>

<!-- Questions List -->
<div id="questions-container">
    <?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="question-editor" data-question-id="<?php echo e($question->id); ?>">
            <div class="question-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Question <?php echo e($index + 1); ?></h6>
                    <div>
                        <select class="form-select form-select-sm me-2" style="width: auto; display: inline-block;" onchange="changeQuestionTypeInModal(<?php echo e($question->id); ?>, this.value)">
                            <option value="multiple_choice" <?php echo e($question->question_type === 'multiple_choice' ? 'selected' : ''); ?>>Multiple Choice</option>
                            <option value="true_false" <?php echo e($question->question_type === 'true_false' ? 'selected' : ''); ?>>True/False</option>
                            <option value="short_answer" <?php echo e($question->question_type === 'short_answer' ? 'selected' : ''); ?>>Short Answer</option>
                            <option value="essay" <?php echo e($question->question_type === 'essay' ? 'selected' : ''); ?>>Essay</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteQuestionInModal(<?php echo e($question->id); ?>)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="question-content">
                <div class="mb-3">
                    <label class="form-label">Question Text</label>
                    <textarea class="form-control" name="question_text_<?php echo e($question->id); ?>" rows="3"><?php echo e($question->question_text); ?></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Points</label>
                        <input type="number" class="form-control" name="points_<?php echo e($question->id); ?>" value="<?php echo e($question->points ?? 1); ?>" min="1">
                    </div>
                </div>
                
                <!-- Question Type Specific Content -->
                <div class="question-options-container">
                    <?php if($question->question_type === 'multiple_choice'): ?>
                        <?php echo $__env->make('professor.partials.question-options-multiple-choice', ['question' => $question], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php elseif($question->question_type === 'true_false'): ?>
                        <?php echo $__env->make('professor.partials.question-options-true-false', ['question' => $question], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php elseif($question->question_type === 'short_answer'): ?>
                        <?php echo $__env->make('professor.partials.question-options-short-answer', ['question' => $question], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php elseif($question->question_type === 'essay'): ?>
                        <?php echo $__env->make('professor.partials.question-options-essay', ['question' => $question], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Explanation (Optional)</label>
                    <textarea class="form-control" name="explanation_<?php echo e($question->id); ?>" rows="2"><?php echo e($question->explanation); ?></textarea>
                    <small class="text-muted">This will be shown to students after they answer the question (if instant feedback is enabled).</small>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <!-- Empty State for JS integration (must be inside questions-container for JS to find it) -->
    <div id="quizEmptyState" class="text-center" style="display:none; width:100%;">
        <i class="bi bi-inbox" style="font-size:2.5rem;color:#bdbdbd"></i>
        <div class="mt-2 text-muted">
            No questions yet—click <b>Add Question</b> or use AI Generate to get started.
        </div>
    </div>
</div>

<!-- Empty State for JS integration -->

<?php if($quiz->questions->count() === 0): ?>
    <div class="alert alert-info text-center">
        <i class="bi bi-info-circle"></i>
        <h5>No Questions Yet</h5>
        <p>Start building your quiz by adding some questions!</p>
        <button type="button" class="btn btn-primary" onclick="addNewQuestionInModal()">
            <i class="bi bi-plus-circle"></i> Add Your First Question
        </button>
    </div>
<?php endif; ?>

<style>
    .question-editor {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
        margin-bottom: 20px;
    }
    
    .question-header {
        background: #e9ecef;
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        border-radius: 8px 8px 0 0;
    }
    
    .question-content {
        padding: 20px;
    }
    
    .option-input {
        margin-bottom: 10px;
    }
    
    .add-option-btn {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        color: #6c757d;
        padding: 10px;
        border-radius: 5px;
        width: 100%;
    }
    
    .add-option-btn:hover {
        border-color: #007bff;
        color: #007bff;
    }
    
    .quiz-settings-panel {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\Quiz Generator\professor\quiz-questions-edit-modal.blade.php ENDPATH**/ ?>