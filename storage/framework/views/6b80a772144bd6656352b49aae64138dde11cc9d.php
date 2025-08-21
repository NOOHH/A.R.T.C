

<?php $__env->startSection('title', 'Edit Quiz Questions'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-pencil-square"></i> Edit Quiz Questions</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('professor.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('quiz-generator')); ?>">Quiz Generator</a></li>
                            <li class="breadcrumb-item active">Edit Questions</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="<?php echo e(route('quiz-generator')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Quizzes
                    </a>
                    <a href="<?php echo e(route('quiz-generator.preview', $quiz->quiz_id)); ?>" class="btn btn-outline-primary">
                        <i class="bi bi-eye"></i> Preview Quiz
                    </a>
                </div>
            </div>

            <!-- Quiz Information Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="bi bi-info-circle"></i> Quiz Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Title:</strong> <?php echo e($quiz->quiz_title); ?><br>
                            <strong>Program:</strong> <?php echo e($quiz->program->program_name ?? 'N/A'); ?><br>
                            <strong>Module:</strong> <?php echo e($quiz->module->module_name ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-6">
                            <strong>Course:</strong> <?php echo e($quiz->course->subject_name ?? 'N/A'); ?><br>
                            <strong>Questions:</strong> <span id="question-count"><?php echo e($quiz->questions->count()); ?></span><br>
                            <strong>Status:</strong> 
                            <span class="badge bg-<?php echo e($quiz->status === 'published' ? 'success' : 'warning'); ?>">
                                <?php echo e(ucfirst($quiz->status)); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Management -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="bi bi-list-ul"></i> Questions</h5>
                    <button class="btn btn-success btn-sm" onclick="showAddQuestionModal()">
                        <i class="bi bi-plus-circle"></i> Add Question
                    </button>
                </div>
                <div class="card-body">
                    <div id="questions-container">
                        <?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="question-item card mb-3" data-question-id="<?php echo e($question->question_id); ?>">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Question <?php echo e($index + 1); ?></h6>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary edit-question-btn" 
                                                data-question-id="<?php echo e($question->question_id); ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <button class="btn btn-outline-danger delete-question-btn" 
                                                data-question-id="<?php echo e($question->question_id); ?>">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="question-content">
                                        <p><strong>Q:</strong> <?php echo e($question->question); ?></p>
                                        
                                        <?php if($question->question_type === 'multiple_choice' && $question->options): ?>
                                            <?php $options = json_decode($question->options, true) ?>
                                            <div class="options">
                                                <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" disabled 
                                                               <?php if($key === $question->correct_answer): ?> checked <?php endif; ?>>
                                                        <label class="form-check-label">
                                                            <?php echo e($key); ?>) <?php echo e($option); ?>

                                                            <?php if($key === $question->correct_answer): ?>
                                                                <span class="badge bg-success ms-2">Correct</span>
                                                            <?php endif; ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php elseif($question->question_type === 'true_false'): ?>
                                            <div class="options">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" disabled 
                                                           <?php if($question->correct_answer === 'A'): ?> checked <?php endif; ?>>
                                                    <label class="form-check-label">
                                                        A) True
                                                        <?php if($question->correct_answer === 'A'): ?>
                                                            <span class="badge bg-success ms-2">Correct</span>
                                                        <?php endif; ?>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" disabled 
                                                           <?php if($question->correct_answer === 'B'): ?> checked <?php endif; ?>>
                                                    <label class="form-check-label">
                                                        B) False
                                                        <?php if($question->correct_answer === 'B'): ?>
                                                            <span class="badge bg-success ms-2">Correct</span>
                                                        <?php endif; ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p><strong>Answer:</strong> <?php echo e($question->correct_answer); ?></p>
                                        <?php endif; ?>
                                        
                                        <?php if($question->explanation): ?>
                                            <div class="explanation mt-2">
                                                <small class="text-muted">
                                                    <strong>Explanation:</strong> <?php echo e($question->explanation); ?>

                                                </small>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="question-meta mt-2">
                                            <span class="badge bg-info"><?php echo e(ucfirst(str_replace('_', ' ', $question->question_type))); ?></span>
                                            <?php if($question->difficulty_level): ?>
                                                <span class="badge bg-secondary"><?php echo e(ucfirst($question->difficulty_level)); ?></span>
                                            <?php endif; ?>
                                            <?php if($question->topic): ?>
                                                <span class="badge bg-light text-dark"><?php echo e($question->topic); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <?php if($quiz->questions->count() === 0): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-question-circle-fill display-4"></i>
                            <h5 class="mt-3">No questions found</h5>
                            <p>Start by adding some questions to your quiz.</p>
                            <button class="btn btn-success" onclick="showAddQuestionModal()">
                                <i class="bi bi-plus-circle"></i> Add First Question
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Question Modal -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionModalLabel">Add Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="questionForm">
                    <input type="hidden" id="questionId" name="questionId">
                    
                    <div class="mb-3">
                        <label for="questionType" class="form-label">Question Type</label>
                        <select class="form-select" id="questionType" name="question_type" required>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="fill_in_blank">Fill in the Blank</option>
                            <option value="short_answer">Short Answer</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="questionText" class="form-label">Question</label>
                        <textarea class="form-control" id="questionText" name="question" rows="3" required></textarea>
                    </div>
                    
                    <!-- Multiple Choice Options -->
                    <div id="multipleChoiceOptions" class="mb-3">
                        <label class="form-label">Answer Options</label>
                        <div class="options-container">
                            <div class="input-group mb-2">
                                <span class="input-group-text">A)</span>
                                <input type="text" class="form-control" name="options[A]" placeholder="Option A">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer" value="A">
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">B)</span>
                                <input type="text" class="form-control" name="options[B]" placeholder="Option B">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer" value="B">
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">C)</span>
                                <input type="text" class="form-control" name="options[C]" placeholder="Option C">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer" value="C">
                                </div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">D)</span>
                                <input type="text" class="form-control" name="options[D]" placeholder="Option D">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="correct_answer" value="D">
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Select the correct answer by clicking the radio button next to it.</small>
                    </div>
                    
                    <!-- True/False Options -->
                    <div id="trueFalseOptions" class="mb-3" style="display: none;">
                        <label class="form-label">Correct Answer</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="correct_answer" value="A" id="trueOption">
                            <label class="form-check-label" for="trueOption">True</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="correct_answer" value="B" id="falseOption">
                            <label class="form-check-label" for="falseOption">False</label>
                        </div>
                    </div>
                    
                    <!-- Fill in Blank / Short Answer -->
                    <div id="textAnswerOptions" class="mb-3" style="display: none;">
                        <label for="correctAnswer" class="form-label">Correct Answer</label>
                        <input type="text" class="form-control" id="correctAnswer" name="correct_answer" 
                               placeholder="Enter the correct answer">
                    </div>
                    
                    <div class="mb-3">
                        <label for="explanation" class="form-label">Explanation (Optional)</label>
                        <textarea class="form-control" id="explanation" name="explanation" rows="2" 
                                  placeholder="Explain why this is the correct answer"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveQuestionBtn">Save Question</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
const quizId = <?php echo e($quiz->quiz_id); ?>;

// Question type change handler
document.getElementById('questionType').addEventListener('change', function() {
    const type = this.value;
    const mcOptions = document.getElementById('multipleChoiceOptions');
    const tfOptions = document.getElementById('trueFalseOptions');
    const textOptions = document.getElementById('textAnswerOptions');
    
    // Hide all option containers
    mcOptions.style.display = 'none';
    tfOptions.style.display = 'none';
    textOptions.style.display = 'none';
    
    // Show relevant container
    if (type === 'multiple_choice') {
        mcOptions.style.display = 'block';
    } else if (type === 'true_false') {
        tfOptions.style.display = 'block';
    } else {
        textOptions.style.display = 'block';
    }
});

// Show add question modal
function showAddQuestionModal() {
    document.getElementById('questionModalLabel').textContent = 'Add Question';
    document.getElementById('questionForm').reset();
    document.getElementById('questionId').value = '';
    document.getElementById('questionType').dispatchEvent(new Event('change'));
    
    const modal = new bootstrap.Modal(document.getElementById('questionModal'));
    modal.show();
}

// Edit question
document.addEventListener('click', function(e) {
    if (e.target.closest('.edit-question-btn')) {
        const questionId = e.target.closest('.edit-question-btn').dataset.questionId;
        editQuestion(questionId);
    }
});

function editQuestion(questionId) {
    // Find question data from the page
    const questionItem = document.querySelector(`[data-question-id="${questionId}"]`);
    // This would need to be implemented with AJAX to get full question data
    
    document.getElementById('questionModalLabel').textContent = 'Edit Question';
    const modal = new bootstrap.Modal(document.getElementById('questionModal'));
    modal.show();
}

// Delete question
document.addEventListener('click', function(e) {
    if (e.target.closest('.delete-question-btn')) {
        const questionId = e.target.closest('.delete-question-btn').dataset.questionId;
        if (confirm('Are you sure you want to delete this question?')) {
            deleteQuestion(questionId);
        }
    }
});

function deleteQuestion(questionId) {
    fetch(`/professor/quiz-generator/${quizId}/questions/${questionId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-question-id="${questionId}"]`).remove();
            updateQuestionCount();
            showAlert('success', data.message);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while deleting the question.');
    });
}

// Save question
document.getElementById('saveQuestionBtn').addEventListener('click', function() {
    const form = document.getElementById('questionForm');
    const formData = new FormData(form);
    const questionId = document.getElementById('questionId').value;
    
    const url = questionId ? 
        `/professor/quiz-generator/${quizId}/questions/${questionId}` :
        `/professor/quiz-generator/${quizId}/questions`;
    
    const method = questionId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('questionModal'));
            modal.hide();
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1000); // Reload to show updated questions
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while saving the question.');
    });
});

function updateQuestionCount() {
    const count = document.querySelectorAll('.question-item').length;
    document.getElementById('question-count').textContent = count;
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\Quiz Generator\professor\quiz-editor.blade.php ENDPATH**/ ?>