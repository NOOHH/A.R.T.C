// Modal-based Quiz Question Editor Functions

function addNewQuestionInModal() {
    const container = document.getElementById('questions-container');
    const questionCount = container.children.length + 1;
    
    const questionHtml = `
        <div class="question-editor" data-question-id="new-${questionCount}">
            <div class="question-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Question ${questionCount}</h6>
                    <div>
                        <select class="form-select form-select-sm me-2" style="width: auto; display: inline-block;" onchange="changeQuestionTypeInModal('new-${questionCount}', this.value)">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="essay">Essay</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteQuestionInModal('new-${questionCount}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="question-content">
                <div class="mb-3">
                    <label class="form-label">Question Text</label>
                    <textarea class="form-control" name="question_text_new-${questionCount}" rows="3" placeholder="Enter your question here..."></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Points</label>
                        <input type="number" class="form-control" name="points_new-${questionCount}" value="1" min="1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Difficulty</label>
                        <select class="form-select" name="difficulty_new-${questionCount}">
                            <option value="easy">Easy</option>
                            <option value="medium" selected>Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                </div>
                
                <div class="question-options-container">
                    ${getQuestionOptionsHtml('multiple_choice', 'new-' + questionCount)}
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Explanation (Optional)</label>
                    <textarea class="form-control" name="explanation_new-${questionCount}" rows="2" placeholder="Provide an explanation for the correct answer..."></textarea>
                    <small class="text-muted">This will be shown to students after they answer the question (if instant feedback is enabled).</small>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', questionHtml);
    
    // Show empty state message if this was the first question
    const emptyAlert = document.querySelector('.alert.alert-info');
    if (emptyAlert) {
        emptyAlert.style.display = 'none';
    }
    
    // Scroll to the new question
    const newQuestion = container.lastElementChild;
    newQuestion.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Focus on the question text area
    setTimeout(() => {
        newQuestion.querySelector('textarea[name^="question_text"]').focus();
    }, 500);
}

function deleteQuestionInModal(questionId) {
    if (confirm('Are you sure you want to delete this question?')) {
        const questionElement = document.querySelector(`[data-question-id="${questionId}"]`);
        questionElement.remove();
        
        // Update question numbers
        updateQuestionNumbers();
        
        // Show empty state if no questions left
        const container = document.getElementById('questions-container');
        if (container.children.length === 0) {
            const emptyAlert = document.querySelector('.alert.alert-info');
            if (emptyAlert) {
                emptyAlert.style.display = 'block';
            }
        }
    }
}

function changeQuestionTypeInModal(questionId, newType) {
    const questionElement = document.querySelector(`[data-question-id="${questionId}"]`);
    const optionsContainer = questionElement.querySelector('.question-options-container');
    
    optionsContainer.innerHTML = getQuestionOptionsHtml(newType, questionId);
}

function getQuestionOptionsHtml(questionType, questionId) {
    switch (questionType) {
        case 'multiple_choice':
            return `
                <div class="mb-3">
                    <label class="form-label">Answer Options</label>
                    <div class="options-container">
                        <div class="option-input">
                            <div class="input-group">
                                <div class="input-group-text">
                                    <input class="form-check-input" type="radio" name="correct_option_${questionId}" value="0">
                                </div>
                                <input type="text" class="form-control" name="option_0_${questionId}" placeholder="Option A">
                                <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                        <div class="option-input">
                            <div class="input-group">
                                <div class="input-group-text">
                                    <input class="form-check-input" type="radio" name="correct_option_${questionId}" value="1">
                                </div>
                                <input type="text" class="form-control" name="option_1_${questionId}" placeholder="Option B">
                                <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="add-option-btn btn" onclick="addOption(this, '${questionId}')">
                        <i class="bi bi-plus"></i> Add Option
                    </button>
                    <small class="text-muted d-block mt-1">Select the correct answer by clicking the radio button next to it.</small>
                </div>
            `;
            
        case 'true_false':
            return `
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
            
        case 'short_answer':
            return `
                <div class="mb-3">
                    <label class="form-label">Acceptable Answers</label>
                    <textarea class="form-control" name="acceptable_answers_${questionId}" rows="3" placeholder="Enter acceptable answers (one per line or separated by commas)"></textarea>
                    <small class="text-muted">Students' answers will be checked against these acceptable answers. Case-insensitive matching is used.</small>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="case_sensitive_${questionId}" id="case_sensitive_${questionId}">
                    <label class="form-check-label" for="case_sensitive_${questionId}">
                        Case sensitive matching
                    </label>
                </div>
            `;
            
        case 'essay':
            return `
                <div class="mb-3">
                    <label class="form-label">Grading Rubric (Optional)</label>
                    <textarea class="form-control" name="rubric_${questionId}" rows="4" placeholder="Provide a grading rubric or key points to look for in student responses..."></textarea>
                    <small class="text-muted">This will help you grade essay responses consistently.</small>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Minimum Word Count</label>
                        <input type="number" class="form-control" name="min_words_${questionId}" value="50" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Maximum Word Count</label>
                        <input type="number" class="form-control" name="max_words_${questionId}" value="500" min="0">
                    </div>
                </div>
            `;
            
        default:
            return '';
    }
}

function addOption(button, questionId) {
    const optionsContainer = button.previousElementSibling;
    const optionCount = optionsContainer.children.length;
    const optionLetter = String.fromCharCode(65 + optionCount); // A, B, C, D, etc.
    
    const optionHtml = `
        <div class="option-input">
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
    
    optionsContainer.insertAdjacentHTML('beforeend', optionHtml);
}

function removeOption(button) {
    const optionInput = button.closest('.option-input');
    const optionsContainer = optionInput.parentElement;
    
    // Don't allow removing if there are only 2 options left
    if (optionsContainer.children.length <= 2) {
        alert('A multiple choice question must have at least 2 options.');
        return;
    }
    
    optionInput.remove();
    
    // Update radio button values and placeholders
    const options = optionsContainer.querySelectorAll('.option-input');
    options.forEach((option, index) => {
        const radio = option.querySelector('input[type="radio"]');
        const textInput = option.querySelector('input[type="text"]');
        const optionLetter = String.fromCharCode(65 + index);
        
        radio.value = index;
        textInput.placeholder = `Option ${optionLetter}`;
        textInput.name = textInput.name.replace(/option_\d+_/, `option_${index}_`);
    });
}

function updateQuestionNumbers() {
    const questions = document.querySelectorAll('.question-editor');
    questions.forEach((question, index) => {
        const header = question.querySelector('.question-header h6');
        header.textContent = `Question ${index + 1}`;
    });
}

function saveQuizChanges() {
    const quizData = {
        quiz_id: currentQuizId,
        quiz_title: document.getElementById('quiz_title').value,
        time_limit: document.getElementById('time_limit').value,
        difficulty: document.getElementById('difficulty').value,
        status: document.getElementById('status').value,
        instructions: document.getElementById('instructions').value,
        allow_retakes: document.getElementById('allow_retakes').checked,
        instant_feedback: document.getElementById('instant_feedback').checked,
        show_correct_answers: document.getElementById('show_correct_answers').checked,
        randomize_order: document.getElementById('randomize_order').checked,
        max_attempts: document.getElementById('max_attempts').value,
        questions: []
    };
    
    // Collect all questions
    const questionElements = document.querySelectorAll('.question-editor');
    questionElements.forEach((questionElement, index) => {
        const questionId = questionElement.dataset.questionId;
        const questionType = questionElement.querySelector('select').value;
        
        const questionData = {
            id: questionId.startsWith('new-') ? null : questionId,
            order: index + 1,
            question_type: questionType,
            question_text: questionElement.querySelector(`[name^="question_text"]`).value,
            points: questionElement.querySelector(`[name^="points"]`).value,
            difficulty: questionElement.querySelector(`[name^="difficulty"]`).value,
            explanation: questionElement.querySelector(`[name^="explanation"]`).value
        };
        
        // Add type-specific data
        switch (questionType) {
            case 'multiple_choice':
                const options = [];
                const correctOptionInputs = questionElement.querySelectorAll('[name^="correct_option"]');
                let correctOption = null;
                
                correctOptionInputs.forEach((radio) => {
                    if (radio.checked) {
                        correctOption = parseInt(radio.value);
                    }
                });
                
                const optionInputs = questionElement.querySelectorAll('[name^="option_"]');
                optionInputs.forEach((input) => {
                    if (input.value.trim()) {
                        options.push(input.value.trim());
                    }
                });
                
                questionData.options = options;
                questionData.correct_option = correctOption;
                break;
                
            case 'true_false':
                const tfRadios = questionElement.querySelectorAll('[name^="correct_answer"]');
                tfRadios.forEach((radio) => {
                    if (radio.checked) {
                        questionData.correct_answer = radio.value === 'true';
                    }
                });
                break;
                
            case 'short_answer':
                questionData.acceptable_answers = questionElement.querySelector(`[name^="acceptable_answers"]`).value;
                questionData.case_sensitive = questionElement.querySelector(`[name^="case_sensitive"]`)?.checked || false;
                break;
                
            case 'essay':
                questionData.rubric = questionElement.querySelector(`[name^="rubric"]`).value;
                questionData.min_words = questionElement.querySelector(`[name^="min_words"]`).value;
                questionData.max_words = questionElement.querySelector(`[name^="max_words"]`).value;
                break;
        }
        
        quizData.questions.push(questionData);
    });
    
    // Show loading state
    const saveButton = document.querySelector('.btn-success');
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
    saveButton.disabled = true;
    
    // Send data to server
    fetch('/professor/quiz-generator/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(quizData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the main page
            showSuccessMessage('Quiz saved successfully!');
            
            // Close modal and refresh the quiz list
            const modal = bootstrap.Modal.getInstance(document.getElementById('editQuizModal'));
            modal.hide();
            
            // Refresh the quiz generator page
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showErrorMessage(data.message || 'Failed to save quiz. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error saving quiz:', error);
        showErrorMessage('An error occurred while saving the quiz. Please try again.');
    })
    .finally(() => {
        // Restore button state
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    });
}

function showSuccessMessage(message) {
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid') || document.body;
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert-success');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

function showErrorMessage(message) {
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid') || document.body;
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 8 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert-danger');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 8000);
}
