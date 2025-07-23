@extends('professor.layout')

@section('title', 'AI Quiz Generator')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-robot"></i> AI Quiz Generator</h2>
              
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Upload Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Generate Quiz from Document</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('professor.quiz-generator.generate') }}" enctype="multipart/form-data" id="quizForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="program_id" class="form-label">Program</label>
                                <select name="program_id" id="program_id" class="form-select" required>
                                    <option value="">Select Program</option>
                                    @foreach($assignedPrograms as $program)
                                        <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="module_id" class="form-label">Module</label>
                                <select name="module_id" id="module_id" class="form-select" required disabled>
                                    <option value="">Select Module</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="course_id" class="form-label">Course</label>
                                <select name="course_id" id="course_id" class="form-select" required disabled>
                                    <option value="">Select Course</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="content_id" class="form-label">Course Content</label>
                                <select name="content_id" id="content_id" class="form-select" required disabled>
                                    <option value="">Select Content</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="document" class="form-label">Upload Document</label>
                            <input type="file" class="form-control" id="document" name="document" 
                                   accept=".pdf,.doc,.docx,.csv,.txt" required>
                            <div class="form-text">
                                Supported formats: PDF, Word (.doc, .docx), CSV, Text files. Maximum size: 10MB
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="num_questions" class="form-label">Number of Questions</label>
                                <select name="num_questions" id="num_questions" class="form-select" required>
                                    <option value="5">5 Questions</option>
                                    <option value="10" selected>10 Questions</option>
                                    <option value="15">15 Questions</option>
                                    <option value="20">20 Questions</option>
                                    <option value="25">25 Questions</option>
                                    <option value="30">30 Questions</option>
                                    <option value="50">50 Questions</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="quiz_type" class="form-label">Question Type</label>
                                <select name="quiz_type" id="quiz_type" class="form-select" required>
                                    <option value="multiple_choice" selected>Multiple Choice</option>
                                    <option value="true_false">True/False</option>
                                    <option value="mixed">Mixed</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="quiz_title" class="form-label">Quiz Title</label>
                            <input type="text" class="form-control" id="quiz_title" name="quiz_title" 
                                   placeholder="Enter a title for this quiz" required>
                        </div>

                        <div class="mb-3">
                            <label for="instructions" class="form-label">Instructions (Optional)</label>
                            <textarea class="form-control" id="instructions" name="instructions" rows="3"
                                      placeholder="Enter any special instructions for students..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" id="generateBtn">
                            <i class="bi bi-magic"></i> Generate Quiz
                        </button>
                    </form>
                </div>
            </div>

            <!-- Generated Quizzes -->
            @if(isset($quizzes) && $quizzes->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Your Generated Quizzes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($quizzes as $quiz)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $quiz->quiz_title }}</h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    {{ $quiz->program->program_name }}<br>
                                                    {{ $quiz->questions->count() }} questions • 
                                                    {{ ucfirst($quiz->difficulty) }} difficulty<br>
                                                    Created {{ $quiz->created_at->diffForHumans() }}
                                                </small>
                                            </p>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        onclick="previewQuiz({{ $quiz->quiz_id }})">
                                                    <i class="bi bi-eye"></i> Preview
                                                </button>
                                                <button type="button" class="btn btn-outline-success" 
                                                        onclick="exportQuiz({{ $quiz->quiz_id }})">
                                                    <i class="bi bi-download"></i> Export
                                                </button>
                                                <form method="POST" action="{{ route('professor.quiz-generator.delete', $quiz->quiz_id) }}" 
                                                      style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- How it Works -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">How It Works</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <i class="bi bi-cloud-upload display-4 text-primary mb-3"></i>
                            <h6>1. Upload Document</h6>
                            <p class="small text-muted">Upload your PDF, Word, or CSV file containing the content</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <i class="bi bi-gear display-4 text-success mb-3"></i>
                            <h6>2. Configure Quiz</h6>
                            <p class="small text-muted">Set the number of questions, difficulty, and question type</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <i class="bi bi-robot display-4 text-warning mb-3"></i>
                            <h6>3. AI Processing</h6>
                            <p class="small text-muted">Our AI analyzes the content and generates relevant questions</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <i class="bi bi-check-circle display-4 text-info mb-3"></i>
                            <h6>4. Review & Use</h6>
                            <p class="small text-muted">Preview, edit, and export your generated quiz</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5>Generating Quiz...</h5>
                <p class="text-muted">Our AI is analyzing your document and creating questions. This may take a few moments.</p>
            </div>
        </div>
    </div>
</div>

<!-- Quiz Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quiz Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Quiz content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- AI-Generated Quiz Modal with Edit Capability -->
<div class="modal fade" id="editQuizModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review and Edit Generated Quiz</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editQuizContent">
                <!-- Quiz content will be loaded here for editing -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="regenerateQuiz()">
                    <i class="bi bi-arrow-clockwise"></i> Regenerate
                </button>
                <button type="button" class="btn btn-success" onclick="approveQuiz()">
                    <i class="bi bi-check-circle"></i> Approve & Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Enhanced quiz generation with modal preview
let currentQuizData = null;

function showQuizPreview(quizData) {
    currentQuizData = quizData;
    
    let html = `
        <div class="quiz-preview-edit">
            <div class="mb-3">
                <label class="form-label">Quiz Title</label>
                <input type="text" class="form-control" id="editQuizTitle" value="${quizData.title}">
            </div>
            <div class="mb-3">
                <label class="form-label">Instructions</label>
                <textarea class="form-control" id="editQuizInstructions" rows="2">${quizData.instructions || ''}</textarea>
            </div>
            <hr>
            <h6>Questions (Click to edit)</h6>
            <div id="questionsContainer">`;
    
    quizData.questions.forEach((question, index) => {
        html += `
            <div class="question-edit-card mb-3 p-3 border rounded" data-question-index="${index}">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="mb-2">Question ${index + 1}</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" onclick="editQuestion(${index})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteQuestion(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <p class="question-text">${question.question}</p>
                <div class="options-preview">`;
        
        if (question.type === 'multiple_choice') {
            Object.entries(question.options).forEach(([key, option]) => {
                const isCorrect = key === question.correct_answer;
                html += `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" disabled ${isCorrect ? 'checked' : ''}>
                        <label class="form-check-label ${isCorrect ? 'text-success fw-bold' : ''}">
                            ${key}. ${option}
                        </label>
                    </div>`;
            });
        } else {
            html += `
                <div class="form-check">
                    <input class="form-check-input" type="radio" disabled ${question.correct_answer === 'A' ? 'checked' : ''}>
                    <label class="form-check-label ${question.correct_answer === 'A' ? 'text-success fw-bold' : ''}">True</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" disabled ${question.correct_answer === 'B' ? 'checked' : ''}>
                    <label class="form-check-label ${question.correct_answer === 'B' ? 'text-success fw-bold' : ''}">False</label>
                </div>`;
        }
        
        html += `
                </div>
                <small class="text-muted">Points: ${question.points || 1}</small>
            </div>`;
    });
    
    html += `
            </div>
            <button type="button" class="btn btn-outline-primary" onclick="addNewQuestion()">
                <i class="bi bi-plus"></i> Add Question
            </button>
        </div>`;
    
    document.getElementById('editQuizContent').innerHTML = html;
    const modal = new bootstrap.Modal(document.getElementById('editQuizModal'));
    modal.show();
}

function editQuestion(index) {
    const question = currentQuizData.questions[index];
    // Implementation for editing individual questions
    console.log('Editing question:', question);
}

function deleteQuestion(index) {
    if (confirm('Are you sure you want to delete this question?')) {
        currentQuizData.questions.splice(index, 1);
        showQuizPreview(currentQuizData);
    }
}

function addNewQuestion() {
    // Implementation for adding new questions
    console.log('Adding new question');
}

function regenerateQuiz() {
    if (confirm('This will generate new questions. Continue?')) {
        // Re-submit the form to regenerate
        document.getElementById('quizForm').submit();
    }
}

function approveQuiz() {
    // Update the form data with edited content
    const editedTitle = document.getElementById('editQuizTitle').value;
    const editedInstructions = document.getElementById('editQuizInstructions').value;
    
    // Create hidden inputs with the approved quiz data
    const form = document.getElementById('quizForm');
    
    // Add approved flag
    const approvedInput = document.createElement('input');
    approvedInput.type = 'hidden';
    approvedInput.name = 'approved_quiz';
    approvedInput.value = JSON.stringify({
        title: editedTitle,
        instructions: editedInstructions,
        questions: currentQuizData.questions
    });
    form.appendChild(approvedInput);
    
    // Submit the form
    form.submit();
}

// Override the original form submission to show preview first
document.getElementById('quizForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Show loading modal
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    // Simulate AI processing and show preview
    setTimeout(() => {
        loadingModal.hide();
        
        // Mock generated quiz data (in real implementation, this would come from server)
        const mockQuizData = {
            title: document.getElementById('quiz_title').value,
            instructions: document.getElementById('instructions').value,
            questions: [
                {
                    question: "Sample question generated from your document content?",
                    type: "multiple_choice",
                    options: {
                        "A": "Correct answer based on document",
                        "B": "Incorrect option",
                        "C": "Another incorrect option", 
                        "D": "Yet another incorrect option"
                    },
                    correct_answer: "A",
                    points: 1
                }
                // Add more mock questions as needed
            ]
        };
        
        showQuizPreview(mockQuizData);
    }, 3000);
});

// Cascading dropdowns for course content
document.addEventListener('DOMContentLoaded', function() {
    const programSelect = document.getElementById('program_id');
    const moduleSelect = document.getElementById('module_id');
    const courseSelect = document.getElementById('course_id');
    const contentSelect = document.getElementById('content_id');

    // Program change handler
    programSelect.addEventListener('change', function() {
        const programId = this.value;
        
        // Reset dependent dropdowns
        moduleSelect.innerHTML = '<option value="">Select Module</option>';
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        contentSelect.innerHTML = '<option value="">Select Content</option>';
        
        moduleSelect.disabled = true;
        courseSelect.disabled = true;
        contentSelect.disabled = true;

        if (programId) {
            fetch(`/quiz-generator/modules/${programId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.modules.forEach(module => {
                            moduleSelect.innerHTML += `<option value="${module.module_id}">${module.module_name}</option>`;
                        });
                        moduleSelect.disabled = false;
                    }
                })
                .catch(error => console.error('Error loading modules:', error));
        }
    });

    // Module change handler
    moduleSelect.addEventListener('change', function() {
        const moduleId = this.value;
        
        // Reset dependent dropdowns
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        contentSelect.innerHTML = '<option value="">Select Content</option>';
        
        courseSelect.disabled = true;
        contentSelect.disabled = true;

        if (moduleId) {
            fetch(`/quiz-generator/courses/${moduleId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.courses.forEach(course => {
                            courseSelect.innerHTML += `<option value="${course.course_id}">${course.course_name}</option>`;
                        });
                        courseSelect.disabled = false;
                    }
                })
                .catch(error => console.error('Error loading courses:', error));
        }
    });

    // Course change handler
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        
        // Reset dependent dropdown
        contentSelect.innerHTML = '<option value="">Select Content</option>';
        contentSelect.disabled = true;

        if (courseId) {
            fetch(`/quiz-generator/contents/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.contents.forEach(content => {
                            contentSelect.innerHTML += `<option value="${content.content_id}">${content.content_title}</option>`;
                        });
                        contentSelect.disabled = false;
                    }
                })
                .catch(error => console.error('Error loading contents:', error));
        }
    });
});
</script>
@endsection
