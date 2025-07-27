/**
 * Overhauled Quiz Generator JavaScript
 * Handles drag-and-drop, AI generation, and modern quiz building interface
 */

class QuizGeneratorOverhauled {
    constructor() {
        this.questions = [];
        this.editingQuizId = null;
        this.currentModal = null;
        this.draggedElement = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeDragAndDrop();
        this.loadPrograms();
    }

    bindEvents() {
        // Modal triggers
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="create-quiz"]')) {
                this.openCreateQuizModal();
            }
            
            if (e.target.matches('[data-action="edit-quiz"]')) {
                const quizId = e.target.dataset.quizId;
                this.openEditQuizModal(quizId);
            }

            if (e.target.matches('[data-action="close-modal"]')) {
                this.closeModal();
            }

            if (e.target.matches('[data-action="add-question"]')) {
                this.addNewQuestion();
            }

            if (e.target.matches('[data-action="save-quiz"]')) {
                this.saveQuiz();
            }

            if (e.target.matches('[data-action="publish-quiz"]')) {
                this.publishQuiz();
            }

            if (e.target.matches('[data-action="delete-question"]')) {
                this.deleteQuestion(e.target.closest('.question-item'));
            }

            if (e.target.matches('[data-action="edit-question"]')) {
                this.editQuestion(e.target.closest('.question-item'));
            }

            if (e.target.matches('[data-action="duplicate-question"]')) {
                this.duplicateQuestion(e.target.closest('.question-item'));
            }

            if (e.target.matches('[data-action="use-ai-question"]')) {
                this.useAiQuestion(e.target.closest('.ai-question-item'));
            }
        });

        // Form change events
        document.addEventListener('change', (e) => {
            if (e.target.matches('#program_id')) {
                this.loadModules(e.target.value);
            }

            if (e.target.matches('#module_id')) {
                this.loadCourses(e.target.value);
            }

            if (e.target.matches('.question-type-select')) {
                this.updateQuestionInterface(e.target);
            }
        });

        // File upload for AI generation
        document.addEventListener('change', (e) => {
            if (e.target.matches('#ai-document-upload')) {
                this.handleDocumentUpload(e.target);
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
            }
        });
    }

    initializeDragAndDrop() {
        // Make questions draggable and droppable
        this.updateDragAndDrop();
    }

    updateDragAndDrop() {
        const canvas = document.getElementById('quiz-canvas');
        if (!canvas) return;

        // Initialize sortable for the canvas
        if (typeof Sortable !== 'undefined') {
            new Sortable(canvas, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: (evt) => {
                    this.updateQuestionOrder();
                }
            });
        }

        // Make individual questions draggable
        canvas.querySelectorAll('.question-item').forEach((item, index) => {
            item.draggable = true;
            item.dataset.questionIndex = index;

            item.addEventListener('dragstart', (e) => {
                this.draggedElement = item;
                e.dataTransfer.effectAllowed = 'move';
                item.classList.add('dragging');
            });

            item.addEventListener('dragend', () => {
                if (this.draggedElement) {
                    this.draggedElement.classList.remove('dragging');
                    this.draggedElement = null;
                }
            });
        });

        // Make canvas a drop zone
        canvas.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        });

        canvas.addEventListener('drop', (e) => {
            e.preventDefault();
            if (!this.draggedElement) return;

            const afterElement = this.getDragAfterElement(canvas, e.clientY);
            if (afterElement == null) {
                canvas.appendChild(this.draggedElement);
            } else {
                canvas.insertBefore(this.draggedElement, afterElement);
            }

            this.updateQuestionOrder();
        });
    }

    getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.question-item:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    updateQuestionOrder() {
        const canvas = document.getElementById('quiz-canvas');
        const questionItems = canvas.querySelectorAll('.question-item');
        
        questionItems.forEach((item, index) => {
            const questionId = item.dataset.questionId;
            const question = this.questions.find(q => q.id === questionId);
            if (question) {
                question.order = index + 1;
            }
            
            // Update visual order indicator
            const orderIndicator = item.querySelector('.question-number');
            if (orderIndicator) {
                orderIndicator.textContent = index + 1;
            }
        });
    }

    async openCreateQuizModal() {
        this.editingQuizId = null;
        this.questions = [];
        
        const modal = document.getElementById('quiz-generator-modal');
        modal.classList.add('show');
        document.body.classList.add('modal-open');
        this.currentModal = modal;

        // Reset form
        this.resetForm();
        this.renderQuestions();
    }

    async openEditQuizModal(quizId) {
        try {
            this.showLoader();
            
            const response = await fetch(`/quiz-generator/edit/${quizId}`, {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            
            if (result.success) {
                this.editingQuizId = quizId;
                this.questions = result.quiz.questions || [];
                
                // Populate form
                this.populateForm(result.quiz);
                
                // Open modal
                const modal = document.getElementById('quiz-generator-modal');
                modal.classList.add('show');
                document.body.classList.add('modal-open');
                this.currentModal = modal;
                
                this.renderQuestions();
            } else {
                this.showNotification('Failed to load quiz data', 'error');
            }
        } catch (error) {
            console.error('Error loading quiz:', error);
            this.showNotification('Error loading quiz data', 'error');
        } finally {
            this.hideLoader();
        }
    }

    closeModal() {
        if (this.currentModal) {
            this.currentModal.classList.remove('show');
            document.body.classList.remove('modal-open');
            this.currentModal = null;
        }

        // Close AI side panel if open
        const aiPanel = document.getElementById('ai-panel');
        if (aiPanel) {
            aiPanel.classList.remove('show');
        }
    }

    resetForm() {
        const form = document.getElementById('quiz-form');
        if (form) {
            form.reset();
        }

        // Clear dynamic dropdowns
        document.getElementById('module_id').innerHTML = '<option value="">Select Module</option>';
        document.getElementById('course_id').innerHTML = '<option value="">Select Course</option>';
        
        // Update modal title
        document.querySelector('.modal-title').textContent = 'Create New Quiz';
    }

    populateForm(quiz) {
        // Update modal title
        document.querySelector('.modal-title').textContent = 'Edit Quiz';
        
        // Populate form fields
        document.getElementById('quiz_title').value = quiz.title || '';
        document.getElementById('quiz_description').value = quiz.description || '';
        document.getElementById('program_id').value = quiz.program_id || '';
        document.getElementById('difficulty').value = quiz.difficulty || 'medium';
        document.getElementById('time_limit').value = quiz.time_limit || 30;
        
        // Load modules and courses if needed
        if (quiz.program_id) {
            this.loadModules(quiz.program_id).then(() => {
                if (quiz.module_id) {
                    document.getElementById('module_id').value = quiz.module_id;
                    this.loadCourses(quiz.module_id).then(() => {
                        if (quiz.course_id) {
                            document.getElementById('course_id').value = quiz.course_id;
                        }
                    });
                }
            });
        }
    }

    async loadPrograms() {
        // Programs should already be loaded in the select element
        // This is just a placeholder for any dynamic loading if needed
    }

    async loadModules(programId) {
        if (!programId) {
            document.getElementById('module_id').innerHTML = '<option value="">Select Module</option>';
            return;
        }

        try {
            const response = await fetch(`/quiz-generator/modules/${programId}`);
            const result = await response.json();
            
            if (result.success) {
                const moduleSelect = document.getElementById('module_id');
                moduleSelect.innerHTML = '<option value="">Select Module</option>';
                
                result.modules.forEach(module => {
                    const option = document.createElement('option');
                    option.value = module.modules_id;
                    option.textContent = module.module_name;
                    moduleSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading modules:', error);
        }
    }

    async loadCourses(moduleId) {
        if (!moduleId) {
            document.getElementById('course_id').innerHTML = '<option value="">Select Course</option>';
            return;
        }

        try {
            const response = await fetch(`/quiz-generator/courses/${moduleId}`);
            const result = await response.json();
            
            if (result.success) {
                const courseSelect = document.getElementById('course_id');
                courseSelect.innerHTML = '<option value="">Select Course</option>';
                
                result.courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.course_id;
                    option.textContent = course.course_name;
                    courseSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading courses:', error);
        }
    }

    addNewQuestion() {
        const newQuestion = {
            id: 'new_' + Date.now(),
            question_text: '',
            question_type: 'multiple_choice',
            points: 1,
            explanation: '',
            options: ['', '', '', ''],
            correct_answer: 0,
            source: 'manual',
            order: this.questions.length + 1
        };

        this.questions.push(newQuestion);
        this.renderQuestions();
        
        // Auto-focus on the new question
        setTimeout(() => {
            const newQuestionElement = document.querySelector(`[data-question-id="${newQuestion.id}"]`);
            if (newQuestionElement) {
                const textInput = newQuestionElement.querySelector('.question-text-input');
                if (textInput) {
                    textInput.focus();
                }
            }
        }, 100);
    }

    deleteQuestion(questionElement) {
        const questionId = questionElement.dataset.questionId;
        this.questions = this.questions.filter(q => q.id !== questionId);
        this.renderQuestions();
        this.showNotification('Question deleted', 'success');
    }

    editQuestion(questionElement) {
        const questionId = questionElement.dataset.questionId;
        const question = this.questions.find(q => q.id === questionId);
        
        if (question) {
            // Toggle edit mode
            questionElement.classList.toggle('editing');
            
            if (questionElement.classList.contains('editing')) {
                // Switch to edit mode - this is handled by the template
                this.renderQuestions();
            } else {
                // Save changes
                this.saveQuestionChanges(questionElement, question);
            }
        }
    }

    saveQuestionChanges(questionElement, question) {
        const questionText = questionElement.querySelector('.question-text-input').value;
        const questionType = questionElement.querySelector('.question-type-select').value;
        const points = parseInt(questionElement.querySelector('.points-input').value) || 1;
        const explanation = questionElement.querySelector('.explanation-input').value;

        question.question_text = questionText;
        question.question_type = questionType;
        question.points = points;
        question.explanation = explanation;

        if (questionType === 'multiple_choice') {
            const optionInputs = questionElement.querySelectorAll('.option-input');
            const correctAnswerSelect = questionElement.querySelector('.correct-answer-select');
            
            question.options = Array.from(optionInputs).map(input => input.value);
            question.correct_answer = parseInt(correctAnswerSelect.value) || 0;
        }

        this.renderQuestions();
        this.showNotification('Question updated', 'success');
    }

    duplicateQuestion(questionElement) {
        const questionId = questionElement.dataset.questionId;
        const originalQuestion = this.questions.find(q => q.id === questionId);
        
        if (originalQuestion) {
            const duplicatedQuestion = {
                ...originalQuestion,
                id: 'dup_' + Date.now(),
                order: this.questions.length + 1
            };
            
            this.questions.push(duplicatedQuestion);
            this.renderQuestions();
            this.showNotification('Question duplicated', 'success');
        }
    }

    updateQuestionInterface(selectElement) {
        const questionElement = selectElement.closest('.question-item');
        const questionId = questionElement.dataset.questionId;
        const question = this.questions.find(q => q.id === questionId);
        
        if (question) {
            question.question_type = selectElement.value;
            
            // Re-render just this question
            this.renderSingleQuestion(questionElement, question);
        }
    }

    async handleDocumentUpload(fileInput) {
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('document', file);
        formData.append('difficulty', document.getElementById('difficulty').value || 'medium');
        formData.append('question_count', '10');
        formData.append('question_types', JSON.stringify(['multiple_choice', 'essay']));

        try {
            this.showAiLoader();
            
            const response = await fetch('/quiz-generator/generate-from-document', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                this.displayAiQuestions(result.questions);
                this.showAiPanel();
                this.showNotification('AI questions generated successfully!', 'success');
            } else {
                this.showNotification(result.message || 'Failed to generate questions', 'error');
            }
        } catch (error) {
            console.error('Error generating AI questions:', error);
            this.showNotification('Error generating AI questions', 'error');
        } finally {
            this.hideAiLoader();
        }
    }

    showAiPanel() {
        const aiPanel = document.getElementById('ai-panel');
        if (aiPanel) {
            aiPanel.classList.add('show');
        }
    }

    displayAiQuestions(aiQuestions) {
        const container = document.getElementById('ai-questions-container');
        if (!container) return;

        container.innerHTML = aiQuestions.map(question => `
            <div class="ai-question-item" data-ai-question-id="${question.id}">
                <div class="ai-question-header">
                    <span class="ai-question-type-badge">${this.formatQuestionType(question.question_type)}</span>
                    <span class="ai-question-points">${question.points} pt${question.points !== 1 ? 's' : ''}</span>
                </div>
                <div class="ai-question-text">${question.question_text}</div>
                ${question.question_type === 'multiple_choice' ? `
                    <div class="ai-question-options">
                        ${question.options.map((option, index) => `
                            <div class="ai-option ${index === question.correct_answer ? 'correct' : ''}">
                                <span class="option-letter">${String.fromCharCode(65 + index)}</span>
                                <span class="option-text">${option}</span>
                                ${index === question.correct_answer ? '<i class="fas fa-check-circle text-success"></i>' : ''}
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
                ${question.explanation ? `
                    <div class="ai-question-explanation">
                        <strong>Explanation:</strong> ${question.explanation}
                    </div>
                ` : ''}
                <div class="ai-question-actions">
                    <button type="button" class="btn btn-primary btn-sm" data-action="use-ai-question">
                        <i class="fas fa-plus"></i> Add to Quiz
                    </button>
                </div>
            </div>
        `).join('');
    }

    useAiQuestion(aiQuestionElement) {
        const aiQuestionId = aiQuestionElement.dataset.aiQuestionId;
        
        // Find the AI question data (you might need to store this differently)
        const aiQuestionData = this.extractAiQuestionData(aiQuestionElement);
        
        if (aiQuestionData) {
            const newQuestion = {
                id: 'ai_' + Date.now(),
                question_text: aiQuestionData.question_text,
                question_type: aiQuestionData.question_type,
                points: aiQuestionData.points,
                explanation: aiQuestionData.explanation,
                options: aiQuestionData.options || [],
                correct_answer: aiQuestionData.correct_answer || 0,
                source: 'ai_generated',
                order: this.questions.length + 1
            };

            this.questions.push(newQuestion);
            this.renderQuestions();
            
            // Remove from AI panel
            aiQuestionElement.remove();
            
            this.showNotification('AI question added to quiz', 'success');
        }
    }

    extractAiQuestionData(element) {
        return {
            question_text: element.querySelector('.ai-question-text').textContent,
            question_type: this.parseQuestionType(element.querySelector('.ai-question-type-badge').textContent),
            points: parseInt(element.querySelector('.ai-question-points').textContent) || 1,
            explanation: element.querySelector('.ai-question-explanation')?.textContent.replace('Explanation: ', '') || '',
            options: Array.from(element.querySelectorAll('.option-text')).map(el => el.textContent),
            correct_answer: Array.from(element.querySelectorAll('.ai-option')).findIndex(opt => opt.classList.contains('correct'))
        };
    }

    renderQuestions() {
        const canvas = document.getElementById('quiz-canvas');
        if (!canvas) return;

        if (this.questions.length === 0) {
            canvas.innerHTML = `
                <div class="empty-canvas">
                    <div class="empty-canvas-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h4>No Questions Yet</h4>
                    <p>Start building your quiz by adding questions manually or using AI generation.</p>
                    <button type="button" class="btn btn-primary" data-action="add-question">
                        <i class="fas fa-plus"></i> Add First Question
                    </button>
                </div>
            `;
            return;
        }

        canvas.innerHTML = this.questions
            .sort((a, b) => a.order - b.order)
            .map(question => this.renderQuestionHTML(question))
            .join('');

        this.updateDragAndDrop();
    }

    renderQuestionHTML(question) {
        return `
            <div class="question-item" data-question-id="${question.id}">
                <div class="question-header">
                    <div class="question-meta">
                        <span class="drag-handle">
                            <i class="fas fa-grip-vertical"></i>
                        </span>
                        <span class="question-number">${question.order}</span>
                        <span class="question-type-badge">${this.formatQuestionType(question.question_type)}</span>
                        <span class="question-points">${question.points} pt${question.points !== 1 ? 's' : ''}</span>
                        ${question.source === 'ai_generated' ? '<span class="ai-badge">AI</span>' : ''}
                    </div>
                    <div class="question-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-action="edit-question" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-action="duplicate-question" title="Duplicate">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-action="delete-question" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="question-content">
                    <div class="question-text-display">${question.question_text || 'Enter question text...'}</div>
                    
                    ${question.question_type === 'multiple_choice' ? `
                        <div class="question-options">
                            ${question.options.map((option, index) => `
                                <div class="option-item ${index === question.correct_answer ? 'correct' : ''}">
                                    <span class="option-letter">${String.fromCharCode(65 + index)}</span>
                                    <span class="option-text">${option || 'Enter option text...'}</span>
                                    ${index === question.correct_answer ? '<i class="fas fa-check-circle text-success"></i>' : ''}
                                </div>
                            `).join('')}
                        </div>
                    ` : question.question_type === 'essay' ? `
                        <div class="essay-placeholder">
                            <i class="fas fa-align-left"></i>
                            <span>Essay question - students will provide written answers</span>
                        </div>
                    ` : `
                        <div class="true-false-options">
                            <div class="option-item ${question.correct_answer === 0 ? 'correct' : ''}">
                                <span class="option-text">True</span>
                                ${question.correct_answer === 0 ? '<i class="fas fa-check-circle text-success"></i>' : ''}
                            </div>
                            <div class="option-item ${question.correct_answer === 1 ? 'correct' : ''}">
                                <span class="option-text">False</span>
                                ${question.correct_answer === 1 ? '<i class="fas fa-check-circle text-success"></i>' : ''}
                            </div>
                        </div>
                    `}
                    
                    ${question.explanation ? `
                        <div class="question-explanation">
                            <strong>Explanation:</strong> ${question.explanation}
                        </div>
                    ` : ''}
                </div>
                
                <!-- Inline editing form (hidden by default) -->
                <div class="question-edit-form" style="display: none;">
                    <div class="form-group mb-3">
                        <label>Question Text</label>
                        <textarea class="form-control question-text-input" rows="3">${question.question_text}</textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Question Type</label>
                            <select class="form-control question-type-select">
                                <option value="multiple_choice" ${question.question_type === 'multiple_choice' ? 'selected' : ''}>Multiple Choice</option>
                                <option value="essay" ${question.question_type === 'essay' ? 'selected' : ''}>Essay</option>
                                <option value="true_false" ${question.question_type === 'true_false' ? 'selected' : ''}>True/False</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Points</label>
                            <input type="number" class="form-control points-input" min="1" value="${question.points}">
                        </div>
                    </div>
                    
                    ${question.question_type === 'multiple_choice' ? `
                        <div class="options-editor mb-3">
                            <label>Options</label>
                            ${question.options.map((option, index) => `
                                <div class="input-group mb-2">
                                    <span class="input-group-text">${String.fromCharCode(65 + index)}</span>
                                    <input type="text" class="form-control option-input" value="${option}">
                                </div>
                            `).join('')}
                            <div class="form-group">
                                <label>Correct Answer</label>
                                <select class="form-control correct-answer-select">
                                    ${question.options.map((_, index) => `
                                        <option value="${index}" ${index === question.correct_answer ? 'selected' : ''}>
                                            ${String.fromCharCode(65 + index)}
                                        </option>
                                    `).join('')}
                                </select>
                            </div>
                        </div>
                    ` : ''}
                    
                    <div class="form-group mb-3">
                        <label>Explanation (Optional)</label>
                        <textarea class="form-control explanation-input" rows="2">${question.explanation}</textarea>
                    </div>
                    
                    <div class="edit-actions">
                        <button type="button" class="btn btn-primary btn-sm save-question-btn">Save Changes</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn">Cancel</button>
                    </div>
                </div>
            </div>
        `;
    }

    renderSingleQuestion(questionElement, question) {
        const newHTML = this.renderQuestionHTML(question);
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = newHTML;
        const newQuestionElement = tempDiv.firstElementChild;
        
        questionElement.replaceWith(newQuestionElement);
        this.updateDragAndDrop();
    }

    async saveQuiz() {
        if (!this.validateForm()) return;

        const formData = this.getFormData();
        formData.is_draft = true;

        try {
            this.showLoader();
            
            const url = this.editingQuizId 
                ? `/quiz-generator/update-quiz/${this.editingQuizId}`
                : '/quiz-generator/save-quiz';
            
            const method = this.editingQuizId ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.closeModal();
                // Optionally reload the quiz list
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showNotification(result.message || 'Failed to save quiz', 'error');
            }
        } catch (error) {
            console.error('Error saving quiz:', error);
            this.showNotification('Error saving quiz', 'error');
        } finally {
            this.hideLoader();
        }
    }

    async publishQuiz() {
        if (!this.validateForm()) return;

        const formData = this.getFormData();
        formData.is_draft = false;

        try {
            this.showLoader();
            
            const url = this.editingQuizId 
                ? `/quiz-generator/update-quiz/${this.editingQuizId}`
                : '/quiz-generator/save-quiz';
            
            const method = this.editingQuizId ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.closeModal();
                // Optionally reload the quiz list
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showNotification(result.message || 'Failed to publish quiz', 'error');
            }
        } catch (error) {
            console.error('Error publishing quiz:', error);
            this.showNotification('Error publishing quiz', 'error');
        } finally {
            this.hideLoader();
        }
    }

    validateForm() {
        const title = document.getElementById('quiz_title').value.trim();
        const program = document.getElementById('program_id').value;
        
        if (!title) {
            this.showNotification('Please enter a quiz title', 'error');
            return false;
        }
        
        if (!program) {
            this.showNotification('Please select a program', 'error');
            return false;
        }
        
        if (this.questions.length === 0) {
            this.showNotification('Please add at least one question', 'error');
            return false;
        }
        
        // Validate each question
        for (let question of this.questions) {
            if (!question.question_text.trim()) {
                this.showNotification('All questions must have text', 'error');
                return false;
            }
            
            if (question.question_type === 'multiple_choice') {
                const validOptions = question.options.filter(opt => opt.trim());
                if (validOptions.length < 2) {
                    this.showNotification('Multiple choice questions must have at least 2 options', 'error');
                    return false;
                }
            }
        }
        
        return true;
    }

    getFormData() {
        return {
            title: document.getElementById('quiz_title').value.trim(),
            description: document.getElementById('quiz_description').value.trim(),
            program_id: document.getElementById('program_id').value,
            module_id: document.getElementById('module_id').value || null,
            course_id: document.getElementById('course_id').value || null,
            difficulty: document.getElementById('difficulty').value,
            time_limit: parseInt(document.getElementById('time_limit').value) || 30,
            questions: this.questions
        };
    }

    // Utility methods
    formatQuestionType(type) {
        const types = {
            'multiple_choice': 'Multiple Choice',
            'essay': 'Essay',
            'true_false': 'True/False'
        };
        return types[type] || type;
    }

    parseQuestionType(formattedType) {
        const types = {
            'Multiple Choice': 'multiple_choice',
            'Essay': 'essay',
            'True/False': 'true_false'
        };
        return types[formattedType] || 'multiple_choice';
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    showLoader() {
        // Show loading overlay
        const loader = document.getElementById('loading-overlay') || this.createLoader();
        loader.style.display = 'flex';
    }

    hideLoader() {
        const loader = document.getElementById('loading-overlay');
        if (loader) {
            loader.style.display = 'none';
        }
    }

    createLoader() {
        const loader = document.createElement('div');
        loader.id = 'loading-overlay';
        loader.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;
        loader.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        `;
        document.body.appendChild(loader);
        return loader;
    }

    showAiLoader() {
        const aiPanel = document.getElementById('ai-panel');
        if (aiPanel) {
            const loader = aiPanel.querySelector('.ai-loader') || this.createAiLoader();
            loader.style.display = 'flex';
        }
    }

    hideAiLoader() {
        const loader = document.querySelector('.ai-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    }

    createAiLoader() {
        const loader = document.createElement('div');
        loader.className = 'ai-loader';
        loader.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Generating...</span>
                </div>
                <p>AI is analyzing your document and generating questions...</p>
            </div>
        `;
        loader.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        `;
        
        const aiPanel = document.getElementById('ai-panel');
        if (aiPanel) {
            aiPanel.appendChild(loader);
        }
        
        return loader;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.quizGenerator = new QuizGeneratorOverhauled();
});
