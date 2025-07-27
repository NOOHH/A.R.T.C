// Simple Quiz Editor - Clean Implementation
$(document).ready(function() {
    console.log('Quiz Editor initialized');
    
    // Global variables
    window.currentQuizId = null;
    window.questionCounter = 0;
    
    // Form dropdown handlers
    $('#program_id').change(function() {
        const programId = $(this).val();
        loadModules(programId);
    });
    
    $('#module_id').change(function() {
        const moduleId = $(this).val();
        loadCourses(moduleId);
    });
    
    function loadModules(programId) {
        if (!programId) {
            $('#module_id').html('<option value="">Select Module</option>').prop('disabled', true);
            $('#course_id').html('<option value="">Select Course</option>').prop('disabled', true);
            return;
        }
        
        $.get(`/quiz-generator/modules/${programId}`)
        .done(function(response) {
            let options = '<option value="">Select Module</option>';
            if (response.success && response.modules) {
                response.modules.forEach(function(module) {
                    options += `<option value="${module.module_id}">${module.module_name}</option>`;
                });
            }
            $('#module_id').html(options).prop('disabled', false);
            $('#course_id').html('<option value="">Select Course</option>').prop('disabled', true);
        })
        .fail(function(xhr, status, error) {
            console.error('Failed to load modules:', error);
            $('#module_id').html('<option value="">Error loading modules</option>').prop('disabled', true);
        });
    }

    function loadCourses(moduleId) {
        if (!moduleId) {
            $('#course_id').html('<option value="">Select Course</option>').prop('disabled', true);
            return;
        }
        
        $.get(`/quiz-generator/courses/${moduleId}`)
        .done(function(response) {
            let options = '<option value="">Select Course</option>';
            if (response.success && response.courses) {
                response.courses.forEach(function(course) {
                    options += `<option value="${course.course_id}">${course.course_name}</option>`;
                });
            }
            $('#course_id').html(options).prop('disabled', false);
        })
        .fail(function(xhr, status, error) {
            console.error('Failed to load courses:', error);
            $('#course_id').html('<option value="">Error loading courses</option>').prop('disabled', true);
        });
    }
    
    // Quiz form submission with enhanced loading indicators
    $('#quiz-generator-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = $(this).find('button[type="submit"]');
        const originalText = submitButton.html();
        
        // Show loading indicator
        showLoadingIndicator('Generating quiz...');
        submitButton.html('<i class="bi bi-hourglass-split spinner-border spinner-border-sm me-2"></i>Generating Quiz...').prop('disabled', true);
        
        // Show progress notification
        showProgressNotification('Starting quiz generation...', 'info');
        
        $.ajax({
            url: '/professor/quiz-generator/generate',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: 120000, // 2 minutes timeout
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                // Progress monitoring (if needed)
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        updateProgressNotification('Uploading document... ' + Math.round(percentComplete * 100) + '%', 'info');
                    }
                }, false);
                return xhr;
            },
            beforeSend: function() {
                updateProgressNotification('Processing request...', 'info');
            },
            success: function(response) {
                hideLoadingIndicator();
                if (response.success) {
                    updateProgressNotification('Quiz generated successfully!', 'success');
                    showAlert('success', `✅ ${response.message || 'Quiz generated successfully!'}`);
                    
                    // Show success details if available
                    if (response.quiz_id) {
                        setTimeout(() => {
                            showAlert('info', `Quiz ID: ${response.quiz_id} created with ${response.questions_count || 'multiple'} questions.`);
                        }, 1000);
                    }
                    
                    setTimeout(() => location.reload(), 3000);
                } else {
                    updateProgressNotification('Generation failed', 'danger');
                    showAlert('danger', `❌ ${response.message || 'Failed to generate quiz.'}`);
                }
            },
            error: function(xhr) {
                hideLoadingIndicator();
                updateProgressNotification('Error occurred', 'danger');
                
                let errorMsg = 'An error occurred while generating the quiz.';
                
                if (xhr.status === 422) {
                    // Validation errors
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMsg = 'Validation Error: ' + errors.join(', ');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = 'Validation Error: ' + xhr.responseJSON.message;
                    }
                } else if (xhr.status === 500) {
                    errorMsg = 'Server error occurred. Please try again or contact support.';
                } else if (xhr.status === 403) {
                    errorMsg = 'Permission denied. Please check your access rights.';
                } else if (xhr.status === 0) {
                    errorMsg = 'Connection error. Please check your internet connection.';
                } else if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                showAlert('danger', `❌ ${errorMsg}`);
                console.error('Quiz generation error:', xhr);
            },
            complete: function() {
                submitButton.html(originalText).prop('disabled', false);
                hideProgressNotification();
            }
        });
    });

    // Quiz actions
    $(document).on('click', '.preview-quiz-btn', function() {
        const quizId = $(this).data('quiz-id');
        window.open(`/professor/quiz-generator/preview/${quizId}`, '_blank');
    });

    $(document).on('click', '.export-quiz-btn', function() {
        const quizId = $(this).data('quiz-id');
        window.location.href = `/professor/quiz-generator/export/${quizId}`;
    });

    // Edit questions modal
    $(document).on('click', '.view-questions-btn', function() {
        const quizId = $(this).data('quiz-id');
        window.currentQuizId = quizId;
        
        // Create modal if it doesn't exist
        if ($('#editQuestionsModal').length === 0) {
            createEditModal();
        }
        
        // Load content
        $('#edit-questions-modal-body').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div><p class="mt-3">Loading questions...</p></div>');
        $('#editQuestionsModal').modal('show');
        
        // Load modal content
        $.get(`/professor/quiz-generator/questions/${quizId}/modal-content`)
        .done(function(response) {
            $('#edit-questions-modal-body').html('<div class="container-fluid p-4">' + response + '</div>');
            window.questionCounter = $('.question-editor').length;
        })
        .fail(function(xhr) {
            $('#edit-questions-modal-body').html('<div class="alert alert-danger m-4">Error loading questions</div>');
        });
    });

    function createEditModal() {
        $('body').append(`
            <div class="modal fade" id="editQuestionsModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-pencil-square"></i> Edit Quiz Questions
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0" id="edit-questions-modal-body">
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-arrow-left"></i> Back to Quiz Generator
                            </button>
                            <button type="button" class="btn btn-success" onclick="saveQuizChanges()">
                                <i class="bi bi-check-lg"></i> Save All Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    // Other quiz actions
    $(document).on('click', '.delete-quiz-btn', function() {
        const quizId = $(this).data('quiz-id');
        const quizTitle = $(this).data('quiz-title');
        
        if (confirm(`Are you sure you want to delete "${quizTitle}"?`)) {
            $.ajax({
                url: `/professor/quiz-generator/${quizId}`,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', 'Quiz deleted successfully!');
                        location.reload();
                    }
                }
            });
        }
    });

    $(document).on('click', '.publish-quiz-btn', function() {
        const quizId = $(this).data('quiz-id');
        
        $.ajax({
            url: `/professor/quiz-generator/${quizId}/publish`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    showAlert('success', 'Quiz published successfully!');
                    location.reload();
                }
            }
        });
    });

    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.container').first().prepend(alertHtml);
        setTimeout(() => $('.alert').fadeOut(), 5000);
    }
});

// Modal functions (global scope)
window.addNewQuestion = function() {
    const counter = ++window.questionCounter;
    
    const template = `
        <div class="question-editor" data-question-id="new-${counter}">
            <div class="question-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Question ${counter}</h6>
                    <div>
                        <select class="form-select form-select-sm me-2 d-inline-block" style="width: auto;" onchange="changeQuestionType('new-${counter}', this.value)">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="essay">Essay</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteQuestion('new-${counter}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="question-content">
                <div class="mb-3">
                    <label class="form-label">Question Text</label>
                    <textarea class="form-control" name="question_text_new-${counter}" rows="3" placeholder="Enter your question here..."></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Points</label>
                        <input type="number" class="form-control" name="points_new-${counter}" value="1" min="1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Difficulty</label>
                        <select class="form-select" name="difficulty_new-${counter}">
                            <option value="easy">Easy</option>
                            <option value="medium" selected>Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                </div>
                
                <div class="question-options-container">
                    <!-- Options will be loaded here -->
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Explanation (Optional)</label>
                    <textarea class="form-control" name="explanation_new-${counter}" rows="2" placeholder="Provide an explanation..."></textarea>
                </div>
            </div>
        </div>
    `;
    
    $('#questions-container').append(template);
    changeQuestionType(`new-${counter}`, 'multiple_choice');
};

// Add new question specifically for modal context
window.addNewQuestionInModal = function() {
    const counter = ++window.questionCounter;
    const template = `
        <div class="question-editor border rounded mb-3 p-3" data-question-id="new-${counter}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Question ${counter}</h6>
                <div>
                    <select class="form-select form-select-sm me-2" style="width: auto; display: inline-block;" name="question_type_new-${counter}" onchange="changeQuestionType('new-${counter}', this.value)">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="short_answer">Short Answer</option>
                        <option value="essay">Essay</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteQuestion('new-${counter}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Question Text</label>
                <textarea class="form-control" name="question_text_new-${counter}" rows="3" placeholder="Enter your question here..."></textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">Points</label>
                    <input type="number" class="form-control" name="points_new-${counter}" value="1" min="1">
                </div>
            </div>
            
            <div class="question-options-container">
                <!-- Options will be loaded here based on question type -->
            </div>
            
            <div class="mb-3">
                <label class="form-label">Explanation (Optional)</label>
                <textarea class="form-control" name="explanation_new-${counter}" rows="2" placeholder="Provide an explanation for the correct answer..."></textarea>
            </div>
        </div>
    `;
    
    $('#questions-container').append(template);
    changeQuestionType(`new-${counter}`, 'multiple_choice');
    
    // Update question count in header
    const currentCount = $('.question-editor').length;
    $('h4:contains("Questions")').html(`Questions (${currentCount})`);
    
    // Scroll to new question
    $(`[data-question-id="new-${counter}"]`)[0].scrollIntoView({ behavior: 'smooth' });
};

window.deleteQuestion = function(questionId) {
    if (confirm('Are you sure you want to delete this question?')) {
        $(`[data-question-id="${questionId}"]`).remove();
        updateQuestionNumbers();
        
        // Update question count in header
        const currentCount = $('.question-editor').length;
        $('h4:contains("Questions")').html(`Questions (${currentCount})`);
    }
};

window.changeQuestionType = function(questionId, questionType) {
    const optionsContainer = $(`[data-question-id="${questionId}"] .question-options-container`);
    
    let optionsHtml = '';
    
    if (questionType === 'multiple_choice') {
        optionsHtml = `
            <div class="mb-3">
                <label class="form-label">Answer Options</label>
                <div class="options-container">
                    <div class="option-input mb-2">
                        <div class="input-group">
                            <div class="input-group-text">
                                <input class="form-check-input" type="radio" name="correct_option_${questionId}" value="0">
                            </div>
                            <input type="text" class="form-control" name="option_0_${questionId}" placeholder="Option A">
                        </div>
                    </div>
                    <div class="option-input mb-2">
                        <div class="input-group">
                            <div class="input-group-text">
                                <input class="form-check-input" type="radio" name="correct_option_${questionId}" value="1">
                            </div>
                            <input type="text" class="form-control" name="option_1_${questionId}" placeholder="Option B">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption('${questionId}')">
                    <i class="bi bi-plus"></i> Add Option
                </button>
                <small class="text-muted d-block mt-1">Select the correct answer.</small>
            </div>
        `;
    } else if (questionType === 'true_false') {
        optionsHtml = `
            <div class="mb-3">
                <label class="form-label">Correct Answer</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="correct_answer_${questionId}" value="true" id="true_${questionId}">
                    <label class="form-check-label" for="true_${questionId}">True</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="correct_answer_${questionId}" value="false" id="false_${questionId}">
                    <label class="form-check-label" for="false_${questionId}">False</label>
                </div>
            </div>
        `;
    } else if (questionType === 'short_answer') {
        optionsHtml = `
            <div class="mb-3">
                <label class="form-label">Acceptable Answers</label>
                <textarea class="form-control" name="acceptable_answers_${questionId}" rows="3" placeholder="Enter acceptable answers (one per line)"></textarea>
                <small class="text-muted">Case-insensitive matching is used.</small>
            </div>
        `;
    } else if (questionType === 'essay') {
        optionsHtml = `
            <div class="mb-3">
                <label class="form-label">Grading Rubric (Optional)</label>
                <textarea class="form-control" name="rubric_${questionId}" rows="4" placeholder="Provide grading guidelines..."></textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Minimum Words</label>
                    <input type="number" class="form-control" name="min_words_${questionId}" value="50" min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Maximum Words</label>
                    <input type="number" class="form-control" name="max_words_${questionId}" value="500" min="0">
                </div>
            </div>
        `;
    }
    
    optionsContainer.html(optionsHtml);
};

window.addOption = function(questionId) {
    const optionsContainer = $(`[data-question-id="${questionId}"] .options-container`);
    const optionCount = optionsContainer.children().length;
    const optionLetter = String.fromCharCode(65 + optionCount);
    
    const optionHtml = `
        <div class="option-input mb-2">
            <div class="input-group">
                <div class="input-group-text">
                    <input class="form-check-input" type="radio" name="correct_option_${questionId}" value="${optionCount}">
                </div>
                <input type="text" class="form-control" name="option_${optionCount}_${questionId}" placeholder="Option ${optionLetter}">
                <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    `;
    
    optionsContainer.append(optionHtml);
};

window.removeOption = function(button) {
    const optionInput = $(button).closest('.option-input');
    const optionsContainer = optionInput.parent();
    
    if (optionsContainer.children().length <= 2) {
        alert('A multiple choice question must have at least 2 options.');
        return;
    }
    
    optionInput.remove();
};

function updateQuestionNumbers() {
    $('.question-editor').each(function(index) {
        $(this).find('.question-header h6').text(`Question ${index + 1}`);
    });
    window.questionCounter = $('.question-editor').length;
}

window.saveQuizChanges = function() {
    if (!window.currentQuizId) {
        alert('No quiz selected for editing.');
        return;
    }

    const quizData = {
        quiz_id: window.currentQuizId,
        quiz_title: $('#quiz_title').val() || '',
        time_limit: $('#time_limit').val() || 60,
        status: $('#status').val() || 'draft',
        instructions: $('#instructions').val() || '',
        allow_retakes: $('#allow_retakes').is(':checked') || false,
        instant_feedback: $('#instant_feedback').is(':checked') || false,
        show_correct_answers: $('#show_correct_answers').is(':checked') || false,
        randomize_order: $('#randomize_order').is(':checked') || false,
        randomize_mc_options: $('#randomize_mc_options').is(':checked') || false,
        max_attempts: $('#max_attempts').val() || 1,
        questions: []
    };

    $('.question-editor').each(function(index) {
        const questionEl = $(this);
        const questionId = questionEl.data('question-id');
        const questionType = questionEl.find('select').val() || 'multiple_choice';
        
        const questionData = {
            id: questionId && !questionId.toString().startsWith('new-') ? questionId : null,
            order: index + 1,
            question_type: questionType,
            question_text: questionEl.find(`[name^="question_text"]`).val() || '',
            points: parseInt(questionEl.find(`[name^="points"]`).val()) || 1,
            explanation: questionEl.find(`[name^="explanation"]`).val() || ''
        };

        if (questionType === 'multiple_choice') {
            const options = [];
            let correctOption = null;
            
            questionEl.find('[name^="option_"]').each(function() {
                if ($(this).val().trim()) {
                    options.push($(this).val().trim());
                }
            });
            
            questionEl.find('[name^="correct_option"]').each(function() {
                if ($(this).is(':checked')) {
                    correctOption = parseInt($(this).val());
                }
            });
            
            questionData.options = options;
            questionData.correct_option = correctOption;
            
        } else if (questionType === 'true_false') {
            let correctAnswer = null;
            questionEl.find('[name^="correct_answer"]').each(function() {
                if ($(this).is(':checked')) {
                    correctAnswer = $(this).val() === 'true';
                }
            });
            questionData.correct_answer = correctAnswer;
            
        } else if (questionType === 'short_answer') {
            questionData.acceptable_answers = questionEl.find(`[name^="acceptable_answers"]`).val() || '';
            
        } else if (questionType === 'essay') {
            questionData.rubric = questionEl.find(`[name^="rubric"]`).val() || '';
            questionData.min_words = parseInt(questionEl.find(`[name^="min_words"]`).val()) || 0;
            questionData.max_words = parseInt(questionEl.find(`[name^="max_words"]`).val()) || 1000;
        }
        
        quizData.questions.push(questionData);
    });

    const saveButton = $('.modal-footer .btn-success');
    const originalText = saveButton.html();
    saveButton.html('<i class="bi bi-hourglass-split"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: '/professor/quiz-generator/save',
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: JSON.stringify(quizData),
        success: function(response) {
            if (response.success) {
                $('#editQuestionsModal').modal('hide');
                setTimeout(() => location.reload(), 1000);
            }
        },
        error: function(xhr) {
            console.error('Save error:', xhr);
        },
        complete: function() {
            saveButton.html(originalText).prop('disabled', false);
        }
    });
};

// Enhanced loading and notification functions
function showLoadingIndicator(message = 'Loading...') {
    if ($('#loading-overlay').length === 0) {
        const overlay = $(`
            <div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                 style="background: rgba(0,0,0,0.7); z-index: 9999;">
                <div class="bg-white p-4 rounded-3 text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div id="loading-message" class="fw-bold">${message}</div>
                </div>
            </div>
        `);
        $('body').append(overlay);
    } else {
        $('#loading-message').text(message);
        $('#loading-overlay').show();
    }
}

function hideLoadingIndicator() {
    $('#loading-overlay').fadeOut(300);
}

function showProgressNotification(message, type = 'info') {
    // Remove existing progress notifications
    $('.progress-notification').remove();
    
    const alertClass = {
        'info': 'alert-info',
        'success': 'alert-success', 
        'danger': 'alert-danger',
        'warning': 'alert-warning'
    }[type] || 'alert-info';
    
    const icon = {
        'info': 'bi-info-circle',
        'success': 'bi-check-circle',
        'danger': 'bi-exclamation-triangle',
        'warning': 'bi-exclamation-circle'
    }[type] || 'bi-info-circle';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show progress-notification position-fixed" 
             style="top: 20px; right: 20px; z-index: 1050; min-width: 300px;">
            <i class="bi ${icon} me-2"></i>
            <span class="progress-message">${message}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Auto-hide info messages after 5 seconds
    if (type === 'info') {
        setTimeout(() => {
            notification.fadeOut();
        }, 5000);
    }
}

function updateProgressNotification(message, type = 'info') {
    const existing = $('.progress-notification');
    if (existing.length > 0) {
        existing.find('.progress-message').text(message);
        
        // Update icon and class based on type
        const alertClass = {
            'info': 'alert-info',
            'success': 'alert-success', 
            'danger': 'alert-danger',
            'warning': 'alert-warning'
        }[type] || 'alert-info';
        
        const icon = {
            'info': 'bi-info-circle',
            'success': 'bi-check-circle',
            'danger': 'bi-exclamation-triangle',
            'warning': 'bi-exclamation-circle'
        }[type] || 'bi-info-circle';
        
        existing.removeClass('alert-info alert-success alert-danger alert-warning').addClass(alertClass);
        existing.find('i').removeClass().addClass(`bi ${icon} me-2`);
    } else {
        showProgressNotification(message, type);
    }
}

function hideProgressNotification() {
    $('.progress-notification').fadeOut(300, function() {
        $(this).remove();
    });
}
