@extends('professor.layout')
@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clipboard-check"></i> Quiz Management</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createQuizModal">
            <i class="bi bi-plus-circle"></i> Create New Quiz
        </button>
    </div>
    
    <!-- Error/Success Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Quiz Status Tabs -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="quizTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="draft-tab" data-bs-toggle="tab" data-bs-target="#draft" type="button" role="tab">
                        <i class="bi bi-file-text"></i> Draft <span class="badge bg-warning ms-1">{{ $draftQuizzes->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="published-tab" data-bs-toggle="tab" data-bs-target="#published" type="button" role="tab">
                        <i class="bi bi-check-circle"></i> Published <span class="badge bg-success ms-1">{{ $publishedQuizzes->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archived" type="button" role="tab">
                        <i class="bi bi-archive"></i> Archived <span class="badge bg-secondary ms-1">{{ $archivedQuizzes->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="quizTabsContent">
                <!-- Draft Quizzes -->
                <div class="tab-pane fade show active" id="draft" role="tabpanel">
                    @if($draftQuizzes->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-file-text display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No draft quizzes</h5>
                            <p class="text-muted">Create a new quiz to get started</p>
                        </div>
                    @else
                        @include('Quiz Generator.professor.quiz-table', ['quizzes' => $draftQuizzes, 'status' => 'draft'])
                    @endif
                </div>

                <!-- Published Quizzes -->
                <div class="tab-pane fade" id="published" role="tabpanel">
                    @if($publishedQuizzes->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No published quizzes</h5>
                            <p class="text-muted">Publish draft quizzes to make them available to students</p>
                        </div>
                    @else
                        @include('Quiz Generator.professor.quiz-table', ['quizzes' => $publishedQuizzes, 'status' => 'published'])
                    @endif
                </div>

                <!-- Archived Quizzes -->
                <div class="tab-pane fade" id="archived" role="tabpanel">
                    @if($archivedQuizzes->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-archive display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No archived quizzes</h5>
                            <p class="text-muted">Archived quizzes will appear here</p>
                        </div>
                    @else
                        @include('Quiz Generator.professor.quiz-table', ['quizzes' => $archivedQuizzes, 'status' => 'archived'])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Quiz Modal -->
<div class="modal fade" id="createQuizModal" tabindex="-1" aria-labelledby="createQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" id="createQuizModalDialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createQuizModalLabel">
                    <i class="bi bi-plus-circle"></i> <span id="modalTitle">Create New Quiz</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="h-100">
                    <!-- Main Quiz Canvas -->
                    <div class="p-4" id="mainQuizCanvas">
                            <!-- Quiz Basic Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Quiz Information</h6>
                                </div>
                                <div class="card-body">
                                    <form id="quizForm">
                                        @csrf
                                        <input type="hidden" id="quizId" name="quiz_id">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="quiz_title" class="form-label">Quiz Title *</label>
                                                <input type="text" class="form-control" id="quiz_title" name="quiz_title" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="quiz_description" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="quiz_description" name="quiz_description">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <label for="program_id" class="form-label">Program *</label>
                                                <select id="program_id" name="program_id" class="form-select" required>
                                                    <option value="">Select Program</option>
                                                    @foreach($assignedPrograms as $program)
                                                        <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="module_id" class="form-label">Module</label>
                                                <select id="module_id" name="module_id" class="form-select" disabled>
                                                    <option value="">Select Module</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="course_id" class="form-label">Course</label>
                                                <select id="course_id" name="course_id" class="form-select" disabled>
                                                    <option value="">Select Course</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                                                <input type="number" class="form-control" id="time_limit" name="time_limit" value="60" min="1">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- AI Generation Section -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-robot"></i> AI Question Generator</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-end">
                                        <div class="col-md-5">
                                            <label for="aiFile" class="form-label">Upload Document</label>
                                            <input type="file" class="form-control" id="aiFile" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                                            <small class="text-muted">Supported: PDF, Word, Text, Image</small>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="aiNumQuestions" class="form-label">Questions</label>
                                            <input type="number" class="form-control" id="aiNumQuestions" min="1" max="20" value="5">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="aiQuestionType" class="form-label">Type</label>
                                            <select class="form-select" id="aiQuestionType">
                                                <option value="multiple_choice">Multiple Choice</option>
                                                <option value="true_false">True/False</option>
                                                <option value="short_answer">Short Answer</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-info w-100" id="generateAIBtn" onclick="generateAIQuestions()">
                                                <span id="generateSpinner" class="spinner-border spinner-border-sm me-1 d-none"></span>
                                                <span id="generateBtnText"><i class="bi bi-magic"></i> Generate</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Questions Container -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="bi bi-list-ol"></i> Quiz Questions (<span id="questionCountHeader">0</span>)</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addManualQuestion()">
                                        <i class="bi bi-plus"></i> Add Question
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="questionsContainer" class="min-height-200">
                                        <div id="emptyState" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox display-4"></i>
                                            <p class="mt-2">No questions added yet. Use "Add Question" button above or AI Generator to get started.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-success" onclick="saveQuiz()">
                                    <i class="bi bi-save"></i> Save Quiz
                                </button>
                            </div>
                        </div>
        </div>
    </div>

    <!-- AI Questions Panel (overlays as a sidebar) -->
    <div class="position-fixed top-0 end-0 h-100 bg-light border-start shadow-lg" id="aiQuestionsPanel" style="display: none; width: 400px; z-index: 1055;">
        <div class="p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="bi bi-robot"></i> AI Generated Questions</h6>
                <button type="button" class="btn-close" onclick="closeAIPanel()"></button>
            </div>
            <div id="aiQuestionsContainer" class="h-100 overflow-auto">
                <!-- AI questions will be displayed here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.min-height-200 { min-height: 200px; }
.question-card { transition: all 0.2s; }
.question-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.ai-question-card { cursor: pointer; transition: all 0.2s; }
.ai-question-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
</style>
@endpush

@push('scripts')
<script>
// Global variables
let currentQuizId = null;
let questionCounter = 0;
let quizQuestions = {};
let aiQuestions = [];

// Initialize when document loads
document.addEventListener('DOMContentLoaded', function() {
    updateQuestionCount();
    
    // Program change handler
    document.getElementById('program_id').addEventListener('change', function() {
        const programId = this.value;
        loadModules(programId);
    });
    
    // Module change handler
    document.getElementById('module_id').addEventListener('change', function() {
        const moduleId = this.value;
        loadCourses(moduleId);
    });
});

// Load modules by program
async function loadModules(programId) {
    const moduleSelect = document.getElementById('module_id');
    const courseSelect = document.getElementById('course_id');
    
    if (!programId) {
        moduleSelect.innerHTML = '<option value="">Select Module</option>';
        moduleSelect.disabled = true;
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        courseSelect.disabled = true;
        return;
    }
    
    try {
        const response = await fetch(`/professor/quiz-generator/modules/${programId}`);
        const data = await response.json();
        
        moduleSelect.innerHTML = '<option value="">Select Module</option>';
        
        if (data.success && data.modules) {
            data.modules.forEach(module => {
                moduleSelect.innerHTML += `<option value="${module.modules_id}">${module.module_name}</option>`;
            });
            moduleSelect.disabled = false;
        }
        
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        courseSelect.disabled = true;
        
    } catch (error) {
        console.error('Error loading modules:', error);
        moduleSelect.innerHTML = '<option value="">Error loading modules</option>';
    }
}

// Load courses by module
async function loadCourses(moduleId) {
    const courseSelect = document.getElementById('course_id');
    
    if (!moduleId) {
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        courseSelect.disabled = true;
        return;
    }
    
    try {
        const response = await fetch(`/professor/quiz-generator/courses/${moduleId}`);
        const data = await response.json();
        
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        
        if (data.success && data.courses) {
            data.courses.forEach(course => {
                courseSelect.innerHTML += `<option value="${course.subject_id}">${course.subject_name}</option>`;
            });
            courseSelect.disabled = false;
        }
        
    } catch (error) {
        console.error('Error loading courses:', error);
        courseSelect.innerHTML = '<option value="">Error loading courses</option>';
    }
}

// Add manual question
function addManualQuestion() {
    questionCounter++;
    const questionId = `question_${questionCounter}`;
    
    const questionHtml = `
        <div class="question-card border rounded mb-3 p-3" data-question-id="${questionId}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">Question ${questionCounter}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion('${questionId}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="mb-3">
                <label class="form-label">Question Text *</label>
                <input type="text" class="form-control" onchange="updateQuestion('${questionId}', 'question', this.value)" placeholder="Enter your question">
            </div>
            <div class="mb-3">
                <label class="form-label">Question Type</label>
                <select class="form-select" onchange="changeQuestionType('${questionId}', this.value)">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                    <option value="short_answer">Short Answer</option>
                </select>
            </div>
            <div id="options_${questionId}">
                ${generateOptionsHtml(questionId, 'multiple_choice')}
            </div>
        </div>
    `;
    
    document.getElementById('questionsContainer').innerHTML += questionHtml;
    document.getElementById('emptyState').style.display = 'none';
    
    // Initialize question data
    quizQuestions[questionId] = {
        question: '',
        type: 'multiple_choice',
        options: ['', '', '', ''],
        correct_answer: []
    };
    
    updateQuestionCount();
}

// Generate options HTML based on question type
function generateOptionsHtml(questionId, type) {
    if (type === 'multiple_choice') {
        return `
            <label class="form-label">Answer Options (Check all correct answers)</label>
            <div class="mb-2">
                <div class="input-group">
                    <span class="input-group-text">A.</span>
                    <input type="text" class="form-control" placeholder="Option A" onchange="updateOption('${questionId}', 0, this.value)">
                    <span class="input-group-text">
                        <input type="checkbox" onchange="updateCorrectAnswer('${questionId}', 'A', this.checked)">
                    </span>
                </div>
            </div>
            <div class="mb-2">
                <div class="input-group">
                    <span class="input-group-text">B.</span>
                    <input type="text" class="form-control" placeholder="Option B" onchange="updateOption('${questionId}', 1, this.value)">
                    <span class="input-group-text">
                        <input type="checkbox" onchange="updateCorrectAnswer('${questionId}', 'B', this.checked)">
                    </span>
                </div>
            </div>
            <div class="mb-2">
                <div class="input-group">
                    <span class="input-group-text">C.</span>
                    <input type="text" class="form-control" placeholder="Option C" onchange="updateOption('${questionId}', 2, this.value)">
                    <span class="input-group-text">
                        <input type="checkbox" onchange="updateCorrectAnswer('${questionId}', 'C', this.checked)">
                    </span>
                </div>
            </div>
            <div class="mb-2">
                <div class="input-group">
                    <span class="input-group-text">D.</span>
                    <input type="text" class="form-control" placeholder="Option D" onchange="updateOption('${questionId}', 3, this.value)">
                    <span class="input-group-text">
                        <input type="checkbox" onchange="updateCorrectAnswer('${questionId}', 'D', this.checked)">
                    </span>
                </div>
            </div>
        `;
    } else if (type === 'true_false') {
        return `
            <label class="form-label">Correct Answer</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="correct_${questionId}" value="true" checked onchange="updateQuestion('${questionId}', 'correct_answer', this.value)">
                <label class="form-check-label">True</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="correct_${questionId}" value="false" onchange="updateQuestion('${questionId}', 'correct_answer', this.value)">
                <label class="form-check-label">False</label>
            </div>
        `;
    } else {
        return `
            <label class="form-label">Expected Answer</label>
            <input type="text" class="form-control" placeholder="Enter the expected answer" onchange="updateQuestion('${questionId}', 'correct_answer', this.value)">
        `;
    }
}

// Update question data
function updateQuestion(questionId, field, value) {
    if (!quizQuestions[questionId]) {
        quizQuestions[questionId] = {};
    }
    quizQuestions[questionId][field] = value;
}

// Update option
function updateOption(questionId, optionIndex, value) {
    if (!quizQuestions[questionId]) {
        quizQuestions[questionId] = { options: ['', '', '', ''] };
    }
    if (!quizQuestions[questionId].options) {
        quizQuestions[questionId].options = ['', '', '', ''];
    }
    quizQuestions[questionId].options[optionIndex] = value;
}

// Update correct answer (supports multiple answers for multiple choice)
function updateCorrectAnswer(questionId, option, isChecked) {
    if (!quizQuestions[questionId]) {
        quizQuestions[questionId] = {};
    }
    
    if (!quizQuestions[questionId].correct_answer) {
        quizQuestions[questionId].correct_answer = [];
    }
    
    if (isChecked) {
        if (!quizQuestions[questionId].correct_answer.includes(option)) {
            quizQuestions[questionId].correct_answer.push(option);
        }
    } else {
        quizQuestions[questionId].correct_answer = quizQuestions[questionId].correct_answer.filter(ans => ans !== option);
    }
}

// Change question type
function changeQuestionType(questionId, newType) {
    updateQuestion(questionId, 'type', newType);
    
    const optionsContainer = document.getElementById(`options_${questionId}`);
    optionsContainer.innerHTML = generateOptionsHtml(questionId, newType);
    
    // Reset options and correct answer based on type
    if (newType === 'multiple_choice') {
        quizQuestions[questionId].options = ['', '', '', ''];
        quizQuestions[questionId].correct_answer = [];
    } else if (newType === 'true_false') {
        quizQuestions[questionId].options = ['True', 'False'];
        quizQuestions[questionId].correct_answer = 'true';
    } else {
        quizQuestions[questionId].options = [];
        quizQuestions[questionId].correct_answer = '';
    }
}

// Remove question
function removeQuestion(questionId) {
    if (confirm('Are you sure you want to remove this question?')) {
        document.querySelector(`[data-question-id="${questionId}"]`).remove();
        delete quizQuestions[questionId];
        updateQuestionCount();
        
        if (Object.keys(quizQuestions).length === 0) {
            document.getElementById('emptyState').style.display = 'block';
        }
    }
}

// Generate AI questions
async function generateAIQuestions() {
    const fileInput = document.getElementById('aiFile');
    const numQuestions = document.getElementById('aiNumQuestions').value;
    const questionType = document.getElementById('aiQuestionType').value;
    const generateBtn = document.getElementById('generateAIBtn');
    const spinner = document.getElementById('generateSpinner');
    const btnText = document.getElementById('generateBtnText');
    
    if (!fileInput.files.length) {
        alert('Please select a file to upload.');
        return;
    }
    
    // Show loading state
    generateBtn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'Generating...';
    
    showAIPanel();
    
    // Show loading in AI panel
    document.getElementById('aiQuestionsContainer').innerHTML = `
        <div class="d-flex flex-column align-items-center justify-content-center h-100">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Generating...</span>
            </div>
            <h6 class="text-muted">Generating Questions</h6>
            <p class="text-muted text-center small">Using AI to analyze your document and create quiz questions...</p>
        </div>
    `;
    
    try {
        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('num_questions', numQuestions);
        formData.append('question_type', questionType);
        
        const response = await fetch('/professor/quiz-generator/generate-ai-questions', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.questions) {
            aiQuestions = data.questions;
            displayAIQuestions(data.questions);
        } else {
            document.getElementById('aiQuestionsContainer').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> 
                    <strong>Generation Failed</strong><br>
                    ${data.message || 'Failed to generate questions. Please try again.'}
                </div>
            `;
        }
    } catch (error) {
        console.error('AI Generation Error:', error);
        document.getElementById('aiQuestionsContainer').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> 
                <strong>Error</strong><br>
                Network error occurred. Please check your connection and try again.
            </div>
        `;
    } finally {
        // Reset button state
        generateBtn.disabled = false;
        spinner.classList.add('d-none');
        btnText.innerHTML = '<i class="bi bi-magic"></i> Generate';
    }
}

// Show AI panel
function showAIPanel() {
    const aiPanel = document.getElementById('aiQuestionsPanel');
    aiPanel.style.display = 'block';
}

// Close AI panel
function closeAIPanel() {
    const aiPanel = document.getElementById('aiQuestionsPanel');
    aiPanel.style.display = 'none';
}

// Display AI generated questions
function displayAIQuestions(questions) {
    const container = document.getElementById('aiQuestionsContainer');
    
    if (!questions || questions.length === 0) {
        container.innerHTML = `
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> 
                No questions were generated. Please try with a different document.
            </div>
        `;
        return;
    }
    
    let html = '<div class="mb-3"><button type="button" class="btn btn-success btn-sm w-100" onclick="addAllAIQuestions()"><i class="bi bi-plus-circle"></i> Add All Questions</button></div>';
    
    questions.forEach((question, index) => {
        html += createAIQuestionCard(question, index);
    });
    
    container.innerHTML = html;
}

// Create AI question card
function createAIQuestionCard(question, index) {
    const optionsHtml = question.options ? renderQuestionOptions(question) : '';
    
    return `
        <div class="ai-question-card card mb-2" data-question-index="${index}" onclick="addAIQuestionToCanvas(${index})">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-info">${question.category || question.type || 'General'}</span>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="event.stopPropagation(); addAIQuestionToCanvas(${index})">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <div class="fw-bold mb-2 small">${question.question}</div>
                ${optionsHtml}
                ${question.explanation ? `<div class="small text-muted mt-2"><strong>Explanation:</strong> ${question.explanation}</div>` : ''}
            </div>
        </div>
    `;
}

// Render question options
function renderQuestionOptions(question) {
    if (!question.options) return '';
    
    let optionsHtml = '<div class="small mt-2">';
    
    if (Array.isArray(question.options)) {
        question.options.forEach((option, i) => {
            const key = String.fromCharCode(65 + i); // A, B, C, D
            const isCorrect = question.correct_answer === key;
            optionsHtml += `
                <div class="option-item mb-1 ${isCorrect ? 'text-success fw-bold' : ''}">
                    ${key}. ${option}
                    ${isCorrect ? '<i class="bi bi-check-circle text-success ms-1"></i>' : ''}
                </div>
            `;
        });
    }
    
    optionsHtml += '</div>';
    return optionsHtml;
}

// Add AI question to canvas
function addAIQuestionToCanvas(questionIndex) {
    const aiQuestion = aiQuestions[questionIndex];
    if (!aiQuestion) return;
    
    questionCounter++;
    const questionId = `question_${questionCounter}`;
    
    // Convert AI question to our format
    const convertedQuestion = {
        question: aiQuestion.question,
        type: aiQuestion.type || 'multiple_choice',
        options: aiQuestion.options || ['', '', '', ''],
        correct_answer: Array.isArray(aiQuestion.correct_answer) ? aiQuestion.correct_answer : 
                       (aiQuestion.type === 'multiple_choice' ? [aiQuestion.correct_answer || 'A'] : aiQuestion.correct_answer || 'A')
    };
    
    // Add to questions data
    quizQuestions[questionId] = convertedQuestion;
    
    // Create HTML
    const questionHtml = `
        <div class="question-card border rounded mb-3 p-3" data-question-id="${questionId}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">Question ${questionCounter} <span class="badge bg-info">AI Generated</span></h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion('${questionId}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="mb-3">
                <label class="form-label">Question Text *</label>
                <input type="text" class="form-control" value="${convertedQuestion.question}" onchange="updateQuestion('${questionId}', 'question', this.value)">
            </div>
            <div class="mb-3">
                <label class="form-label">Question Type</label>
                <select class="form-select" onchange="changeQuestionType('${questionId}', this.value)">
                    <option value="multiple_choice" ${convertedQuestion.type === 'multiple_choice' ? 'selected' : ''}>Multiple Choice</option>
                    <option value="true_false" ${convertedQuestion.type === 'true_false' ? 'selected' : ''}>True/False</option>
                    <option value="short_answer" ${convertedQuestion.type === 'short_answer' ? 'selected' : ''}>Short Answer</option>
                </select>
            </div>
            <div id="options_${questionId}">
                ${generateOptionsWithValues(questionId, convertedQuestion)}
            </div>
        </div>
    `;
    
    document.getElementById('questionsContainer').innerHTML += questionHtml;
    document.getElementById('emptyState').style.display = 'none';
    updateQuestionCount();
    
    // Remove from AI panel
    const questionCard = document.querySelector(`[data-question-index="${questionIndex}"]`);
    if (questionCard) {
        questionCard.remove();
    }
}

// Generate options HTML with values
function generateOptionsWithValues(questionId, question) {
    if (question.type === 'multiple_choice') {
        let html = '<label class="form-label">Answer Options (Check all correct answers)</label>';
        const letters = ['A', 'B', 'C', 'D'];
        
        for (let i = 0; i < 4; i++) {
            const value = question.options[i] || '';
            const isCorrect = Array.isArray(question.correct_answer) ? 
                            question.correct_answer.includes(letters[i]) : 
                            question.correct_answer === letters[i];
            
            html += `
                <div class="mb-2">
                    <div class="input-group">
                        <span class="input-group-text">${letters[i]}.</span>
                        <input type="text" class="form-control" value="${value}" onchange="updateOption('${questionId}', ${i}, this.value)">
                        <span class="input-group-text">
                            <input type="checkbox" ${isCorrect ? 'checked' : ''} onchange="updateCorrectAnswer('${questionId}', '${letters[i]}', this.checked)">
                        </span>
                    </div>
                </div>
            `;
        }
        
        return html;
    } else if (question.type === 'true_false') {
        return `
            <label class="form-label">Correct Answer</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="correct_${questionId}" value="true" ${question.correct_answer === 'true' ? 'checked' : ''} onchange="updateQuestion('${questionId}', 'correct_answer', this.value)">
                <label class="form-check-label">True</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="correct_${questionId}" value="false" ${question.correct_answer === 'false' ? 'checked' : ''} onchange="updateQuestion('${questionId}', 'correct_answer', this.value)">
                <label class="form-check-label">False</label>
            </div>
        `;
    } else {
        return `
            <label class="form-label">Expected Answer</label>
            <input type="text" class="form-control" value="${question.correct_answer || ''}" onchange="updateQuestion('${questionId}', 'correct_answer', this.value)">
        `;
    }
}

// Add all AI questions
function addAllAIQuestions() {
    if (!aiQuestions || aiQuestions.length === 0) {
        alert('No AI questions available to add.');
        return;
    }
    
    aiQuestions.forEach((question, index) => {
        addAIQuestionToCanvas(index);
    });
    
    // Clear the AI panel
    document.getElementById('aiQuestionsContainer').innerHTML = `
        <div class="text-center text-muted">
            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
            <div class="mt-2">All questions have been added to your quiz!</div>
        </div>
    `;
}

// Save quiz
async function saveQuiz() {
    const title = document.getElementById('quiz_title').value.trim();
    const description = document.getElementById('quiz_description').value.trim();
    const programId = document.getElementById('program_id').value;
    const moduleId = document.getElementById('module_id').value;
    const courseId = document.getElementById('course_id').value;
    const timeLimit = document.getElementById('time_limit').value;
    
    if (!title) {
        alert('Please enter a quiz title.');
        return;
    }
    
    if (!programId) {
        alert('Please select a program.');
        return;
    }
    
    if (Object.keys(quizQuestions).length === 0) {
        alert('Please add at least one question.');
        return;
    }
    
    // Convert questions to array format
    const questionsArray = Object.values(quizQuestions).map((q, index) => ({
        question_text: q.question,
        question_type: q.type,
        options: q.options,
        correct_answer: q.correct_answer,
        points: 1,
        order: index + 1
    }));
    
    try {
        const response = await fetch('/professor/quiz-generator/save-quiz', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                title: title,
                description: description,
                program_id: programId,
                module_id: moduleId,
                course_id: courseId,
                time_limit: timeLimit,
                questions: questionsArray,
                is_draft: true
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Quiz saved successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to save quiz'));
        }
    } catch (error) {
        console.error('Error saving quiz:', error);
        alert('Error saving quiz. Please try again.');
    }
}

// Update question count
function updateQuestionCount() {
    const count = Object.keys(quizQuestions).length;
    document.getElementById('questionCount').textContent = count;
    document.getElementById('questionCountHeader').textContent = count;
}

// Quiz status management functions
async function publishQuiz(quizId) {
    if (!confirm('Are you sure you want to publish this quiz? Students will be able to access it.')) {
        return;
    }
    
    try {
        const response = await fetch(`/professor/quiz-generator/${quizId}/publish`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    } catch (error) {
        console.error('Error publishing quiz:', error);
        showAlert('danger', 'Error publishing quiz. Please try again.');
    }
}

async function archiveQuiz(quizId) {
    if (!confirm('Are you sure you want to archive this quiz? Students will no longer be able to access it.')) {
        return;
    }
    
    try {
        const response = await fetch(`/professor/quiz-generator/${quizId}/archive`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    } catch (error) {
        console.error('Error archiving quiz:', error);
        showAlert('danger', 'Error archiving quiz. Please try again.');
    }
}

async function restoreQuiz(quizId) {
    if (!confirm('Are you sure you want to restore this quiz to draft status?')) {
        return;
    }
    
    try {
        const response = await fetch(`/professor/quiz-generator/${quizId}/draft`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    } catch (error) {
        console.error('Error restoring quiz:', error);
        showAlert('danger', 'Error restoring quiz. Please try again.');
    }
}

// Delete quiz
async function deleteQuiz(quizId) {
    if (!confirm('Are you sure you want to delete this quiz? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/professor/quiz-generator/${quizId}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    } catch (error) {
        console.error('Error deleting quiz:', error);
        showAlert('danger', 'Error deleting quiz. Please try again.');
    }
}

// Show alert
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Event delegation for quiz table buttons
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete-quiz-btn') || e.target.closest('.delete-quiz-btn')) {
        e.preventDefault();
        const btn = e.target.classList.contains('delete-quiz-btn') ? e.target : e.target.closest('.delete-quiz-btn');
        const quizId = btn.getAttribute('data-quiz-id');
        deleteQuiz(quizId);
    }
    
    if (e.target.classList.contains('edit-quiz-btn') || e.target.closest('.edit-quiz-btn')) {
        e.preventDefault();
        const btn = e.target.classList.contains('edit-quiz-btn') ? e.target : e.target.closest('.edit-quiz-btn');
        const quizId = btn.getAttribute('data-quiz-id');
        editQuiz(quizId);
    }
    
    if (e.target.classList.contains('preview-quiz-btn') || e.target.closest('.preview-quiz-btn')) {
        e.preventDefault();
        const btn = e.target.classList.contains('preview-quiz-btn') ? e.target : e.target.closest('.preview-quiz-btn');
        const quizId = btn.getAttribute('data-quiz-id');
        previewQuiz(quizId);
    }
});

// Edit quiz function
async function editQuiz(quizId) {
    try {
        // Fetch quiz data
        const response = await fetch(`/professor/quiz-generator/edit/${quizId}`);
        const data = await response.json();
        
        if (data.success) {
            // Populate form with quiz data
            document.getElementById('quiz_title').value = data.quiz.quiz_title || '';
            document.getElementById('quiz_description').value = data.quiz.quiz_description || '';
            document.getElementById('program_id').value = data.quiz.program_id || '';
            document.getElementById('time_limit').value = data.quiz.time_limit || 60;
            document.getElementById('quizId').value = quizId;
            
            // Load modules and courses if needed
            if (data.quiz.program_id) {
                await loadModules(data.quiz.program_id);
                if (data.quiz.modules_id) {
                    document.getElementById('module_id').value = data.quiz.modules_id;
                    await loadCourses(data.quiz.modules_id);
                    if (data.quiz.course_id) {
                        document.getElementById('course_id').value = data.quiz.course_id;
                    }
                }
            }
            
            // Clear existing questions
            quizQuestions = {};
            questionCounter = 0;
            document.getElementById('questionsContainer').innerHTML = '<div id="emptyState" class="text-center text-muted py-4"><i class="bi bi-inbox display-4"></i><p class="mt-2">No questions added yet. Use "Add Question" button above or AI Generator to get started.</p></div>';
            
            // Load quiz questions
            if (data.questions && data.questions.length > 0) {
                document.getElementById('emptyState').style.display = 'none';
                data.questions.forEach(question => {
                    addExistingQuestion(question);
                });
            }
            
            // Update modal title
            document.getElementById('modalTitle').textContent = 'Edit Quiz';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('createQuizModal'));
            modal.show();
            
        } else {
            alert('Failed to load quiz data: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error loading quiz:', error);
        alert('Error loading quiz. Please try again.');
    }
}

// Add existing question to canvas (for editing)
function addExistingQuestion(questionData) {
    questionCounter++;
    const questionId = `question_${questionCounter}`;
    
    // Convert question data to our format
    const convertedQuestion = {
        question: questionData.question_text,
        type: questionData.question_type,
        options: JSON.parse(questionData.options || '[]'),
        correct_answer: questionData.correct_answer
    };
    
    // Add to questions data
    quizQuestions[questionId] = convertedQuestion;
    
    // Create HTML
    const questionHtml = `
        <div class="question-card border rounded mb-3 p-3" data-question-id="${questionId}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">Question ${questionCounter}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion('${questionId}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="mb-3">
                <label class="form-label">Question Text *</label>
                <input type="text" class="form-control" value="${convertedQuestion.question}" onchange="updateQuestion('${questionId}', 'question', this.value)">
            </div>
            <div class="mb-3">
                <label class="form-label">Question Type</label>
                <select class="form-select" onchange="changeQuestionType('${questionId}', this.value)">
                    <option value="multiple_choice" ${convertedQuestion.type === 'multiple_choice' ? 'selected' : ''}>Multiple Choice</option>
                    <option value="true_false" ${convertedQuestion.type === 'true_false' ? 'selected' : ''}>True/False</option>
                    <option value="short_answer" ${convertedQuestion.type === 'short_answer' ? 'selected' : ''}>Short Answer</option>
                </select>
            </div>
            <div id="options_${questionId}">
                ${generateOptionsWithValues(questionId, convertedQuestion)}
            </div>
        </div>
    `;
    
    document.getElementById('questionsContainer').innerHTML += questionHtml;
    updateQuestionCount();
}

// Preview quiz function
function previewQuiz(quizId) {
    window.open(`/professor/quiz-generator/preview/${quizId}`, '_blank');
}
</script>
@endpush
