@extends('admin.admin-dashboard.admin-dashboard-layout')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
/* Professional Tab Styling for Quiz Generator Page Only */
#quizTabs .nav-link {
    color: #6c757d !important;
    background-color: #f8f9fa !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.5rem !important;
    margin-right: 0.5rem !important;
    padding: 0.75rem 1.25rem !important;
    font-weight: 500 !important;
    transition: all 0.2s ease-in-out !important;
}

#quizTabs .nav-link:hover {
    background-color: #e9ecef !important;
    border-color: #adb5bd !important;
    color: #495057 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
}

#quizTabs .nav-link.active {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
    color: white !important;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3) !important;
}

#quizTabs .nav-link.active:hover {
    background-color: #0b5ed7 !important;
    border-color: #0b5ed7 !important;
    transform: translateY(-1px) !important;
}
/* Quiz Canvas Styles */
.quiz-canvas {
    min-height: 400px;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s ease;
}

.quiz-canvas.drag-over {
    border-color: #0d6efd;
    background-color: #f8f9ff;
}

.quiz-question-item {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 15px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    position: relative;
    cursor: move;
}

.quiz-question-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.quiz-question-item.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
}

/* AI Side Panel */
.ai-questions-container {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.ai-question-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin-bottom: 10px;
    padding: 15px;
    cursor: grab;
    transition: all 0.3s ease;
}

.ai-question-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13,110,253,0.15);
}

.ai-question-card:active {
    cursor: grabbing;
}

/* Professional styling improvements */
.modal-header.bg-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
}

.btn-outline-primary:hover,
.btn-outline-info:hover,
.btn-outline-warning:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    transition: all 0.2s ease;
}

/* Button consistency */
.btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

/* Option styling for multiple choice */
.option-item {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.option-item input[type="text"] {
    flex: 1;
    margin-left: 10px;
    margin-right: 10px;
}

.correct-answer-indicator {
    color: #28a745;
    font-weight: bold;
}

/* Loading states */
.generating-questions {
    text-align: center;
    padding: 40px;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Button states */
.btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.btn.disabled {
    pointer-events: none;
}

/* Animation for regenerate button */
.btn-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Drag and drop visual feedback */
.drop-zone {
    border: 2px dashed #28a745;
    background-color: #f8fff9;
    border-radius: 8px;
    padding: 20px;
    margin: 10px 0;
    text-align: center;
    transition: all 0.3s ease;
}

/* File input styling */
.form-control[type="file"] {
    padding: 8px 12px;
    border-radius: 6px;
}

/* Modal responsiveness */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
    }
    
    .col-8, .col-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    #aiQuestionsPanel {
        display: none !important;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clipboard-check"></i> Admin Quiz Management</h2>
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
                        @include('admin.quiz-generator.quiz-table', ['quizzes' => $draftQuizzes, 'status' => 'draft'])
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
                        @include('admin.quiz-generator.quiz-table', ['quizzes' => $publishedQuizzes, 'status' => 'published'])
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
                        @include('admin.quiz-generator.quiz-table', ['quizzes' => $archivedQuizzes, 'status' => 'archived'])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Quiz Modal -->
<div class="modal fade" id="createQuizModal" tabindex="-1" aria-labelledby="createQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" id="createQuizModalDialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createQuizModalLabel">
                    <i class="bi bi-plus-circle"></i> <span id="modalTitle">Create New Quiz</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0 h-100">
                    <!-- Main Quiz Canvas -->
                    <div class="col-12" id="mainQuizCanvas">
                        <div class="p-4">
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
                                            <div class="col-md-4">
                                                <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                                                <input type="number" class="form-control" id="time_limit" name="time_limit" value="60" min="1">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="max_attempts" class="form-label">Max Attempts</label>
                                                <input type="number" class="form-control" id="max_attempts" name="max_attempts" value="1" min="1">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Retake Options</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="infinite_retakes" name="infinite_retakes">
                                                    <label class="form-check-label" for="infinite_retakes">
                                                        Allow Infinite Retakes
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="has_deadline" name="has_deadline">
                                                    <label class="form-check-label" for="has_deadline">
                                                        Set Quiz Deadline
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="due_date" class="form-label">Deadline Date & Time</label>
                                                <input type="datetime-local" class="form-control" id="due_date" name="due_date" disabled>
                                                <small class="form-text text-muted">Leave unchecked for no deadline</small>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- AI Document Upload Section (Hidden during edit) -->
                            <div class="card mb-4" id="aiGeneratorSection">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-robot"></i> AI Question Generator</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-end">
                                        <div class="col-md-4">
                                            <label for="ai_document" class="form-label">Upload Document</label>
                                            <input type="file" class="form-control" id="ai_document" accept=".pdf,.doc,.docx,.csv,.txt,.jpg,.jpeg,.png" onchange="handleFileChange()">
                                            <small class="text-muted">Upload PDF, Word, CSV, TXT, or Image files. Max 10MB. Uses Gemini AI + Tesseract OCR.</small>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="aiQuestionType" class="form-label">Type</label>
                                            <select class="form-select" id="aiQuestionType">
                                                <option value="multiple_choice">Multiple Choice</option>
                                                <option value="true_false">True/False</option>
                                                <option value="mixed">Mixed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="ai_question_count" class="form-label">Questions to Generate</label>
                                            <input type="number" class="form-control" id="ai_question_count" value="10" min="5" max="50">
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-info w-100" id="generateAIBtn" onclick="generateAIQuestions()">
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                <i class="bi bi-magic"></i> <span id="generateBtnText">Generate Questions</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="regenerateBtn" onclick="regenerateWithSameFile()" style="display: none;">
                                                <i class="bi bi-arrow-clockwise"></i> Regenerate with Same File
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Manual Quiz Canvas -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="bi bi-clipboard-check"></i> Quiz Questions (<span id="questionCount">0</span>)</h6>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addManualQuestion()">
                                            <i class="bi bi-plus"></i> Add Question
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="quizCanvas" class="mb-3 d-flex flex-column align-items-center justify-content-center" style="min-height: 200px; border: 2px dashed #dee2e6; border-radius: 8px; padding: 20px;" ondrop="dropQuestion(event)" ondragover="allowDrop(event)">
                                        <i class="bi bi-clipboard text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted mt-2">No questions added yet. Click Add Question or use AI Generate to get started.</h5>
                                        <p class="text-muted text-center">You can drag and drop questions to reorder them.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- AI Generated Questions Side Panel -->
                    <div class="col-lg-4 bg-light border-start" id="aiQuestionsPanel" style="display: none;">
                        <div class="p-3 h-100 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="bi bi-robot"></i> Generated Questions</h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="closeAIPanel()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <div class="flex-grow-1 overflow-auto" id="aiQuestionsContainer">
                                <!-- AI generated questions will be populated here -->
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <button type="button" class="btn btn-success w-100" onclick="addAllAIQuestions()">
                                    <i class="bi bi-plus-circle"></i> Add All Questions
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDraftBtn" onclick="saveQuiz(true)">
                    <span id="saveDraftText">Save as Draft</span>
                </button>
                <button type="button" class="btn btn-success" id="publishBtn" onclick="saveQuiz(false)">
                    <span id="publishText">Publish Quiz</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
console.log('Admin Quiz Generator JavaScript Loading...');

// Debug session and auth data
console.log('DEBUG Session data:', @json(session()->all()));
console.log('DEBUG Auth::user()', @json(Auth::user()));
console.log('DEBUG Auth::guard("director")->user()', @json(Auth::guard('director')->user()));

// Test CSRF token
console.log('DEBUG CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

// Test programs data
console.log('DEBUG Programs:', @json($assignedPrograms));

// Global variables - Updated for admin context
window.adminQuizGenerator = {
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
    baseUrl: '{{ url("/") }}',
    programs: @json($assignedPrograms),
    currentUser: @json(Auth::user()),
    sessionData: @json(session()->all()),
    userRole: 'admin',
    myId: @json(session('user_id')),
    myName: '@json(session('user_name'))',
    isAuthenticated: @json(session('logged_in'))
};

// Setup global variables for quiz functions
window.csrfToken = window.adminQuizGenerator.csrfToken;
window.myId = window.adminQuizGenerator.myId;
window.myName = window.adminQuizGenerator.myName;
window.isAuthenticated = window.adminQuizGenerator.isAuthenticated;
window.userRole = window.adminQuizGenerator.userRole;

console.log('Global variables initialized:', window.adminQuizGenerator);

// ============================================
// QUIZ MANAGEMENT FUNCTIONS (Admin Version)
// ============================================

// Main function to change quiz status
async function changeQuizStatus(quizId, newStatus) {
    let confirmMessage = '';
    let endpoint = '';
    
    switch(newStatus) {
        case 'published':
            confirmMessage = 'Are you sure you want to publish this quiz? Students will be able to access it.';
            endpoint = `/admin/quiz-generator/${quizId}/publish`;
            break;
        case 'archived':
            confirmMessage = 'Are you sure you want to archive this quiz? Students will no longer be able to access it.';
            endpoint = `/admin/quiz-generator/${quizId}/archive`;
            break;
        case 'draft':
            confirmMessage = 'Are you sure you want to move this quiz back to draft status?';
            endpoint = `/admin/quiz-generator/${quizId}/draft`;
            break;
        default:
            console.error('Invalid status:', newStatus);
            return;
    }
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
        console.error(`Error changing quiz status to ${newStatus}:`, error);
        showAlert('danger', `Error changing quiz status. Please try again.`);
    }
}

// Individual status functions for backward compatibility
async function publishQuiz(quizId) {
    return changeQuizStatus(quizId, 'published');
}

async function archiveQuiz(quizId) {
    return changeQuizStatus(quizId, 'archived');
}

async function restoreQuiz(quizId) {
    return changeQuizStatus(quizId, 'draft');
}

// Edit quiz function
async function editQuiz(quizId) {
    console.log('Edit quiz:', quizId);
    window.currentQuizId = quizId;
    
    // Reset the form
    document.getElementById('quizForm').reset();
    document.getElementById('quizId').value = quizId;
    
    // Clear existing questions
    const quizCanvas = document.getElementById('quizCanvas');
    quizCanvas.innerHTML = `
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading quiz data...</p>
        </div>
    `;
    
    // Show the AI Generator section or hide it based on edit mode
    document.getElementById('aiGeneratorSection').style.display = 'none';
    
    // Update modal title
    document.getElementById('modalTitle').textContent = 'Edit Quiz';
    
    try {
        // Fetch quiz data
        const response = await fetch(`/admin/quiz-generator/quiz/${quizId}`, {
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        const data = await response.json();
        
        if (data.success) {
            console.log('Quiz data loaded:', data);
            
            // Set form values
            document.getElementById('quiz_title').value = data.quiz.title || data.quiz.quiz_title || '';
            document.getElementById('quiz_description').value = data.quiz.quiz_description || '';
            
            // Set program, module, course selects
            const programSelect = document.getElementById('program_id');
            if (programSelect && data.quiz.program_id) {
                programSelect.value = data.quiz.program_id;
                programSelect.dispatchEvent(new Event('change')); // This should trigger loading modules
                
                // Need to set module and course after their options are loaded
                setTimeout(() => {
                    const moduleSelect = document.getElementById('module_id');
                    if (moduleSelect && data.quiz.module_id) {
                        moduleSelect.value = data.quiz.module_id;
                        moduleSelect.dispatchEvent(new Event('change')); // This should trigger loading courses
                        
                        // Need to set course after its options are loaded
                        setTimeout(() => {
                            const courseSelect = document.getElementById('course_id');
                            if (courseSelect && data.quiz.course_id) {
                                courseSelect.value = data.quiz.course_id;
                            }
                        }, 500);
                    }
                }, 500);
            }
            
            // Set quiz settings
            document.getElementById('time_limit').value = data.quiz.time_limit || 60;
            document.getElementById('max_attempts').value = data.quiz.max_attempts || 1;
            document.getElementById('infinite_retakes').checked = data.quiz.infinite_retakes || false;
            if (data.quiz.infinite_retakes) {
                document.getElementById('max_attempts').disabled = true;
            }
            
            document.getElementById('has_deadline').checked = data.quiz.has_deadline || false;
            if (data.quiz.has_deadline && data.quiz.due_date) {
                document.getElementById('due_date').disabled = false;
                document.getElementById('due_date').value = data.quiz.due_date.slice(0, 16); // Format as YYYY-MM-DDTHH:MM
            } else {
                document.getElementById('due_date').disabled = true;
            }
            
            // Add questions to canvas
            window.quizQuestions = {};
            window.questionCounter = 0;
            
            if (data.questions && data.questions.length > 0) {
                quizCanvas.innerHTML = '';
                quizCanvas.className = 'mb-3';
                quizCanvas.style.cssText = 'min-height: auto; border: none; padding: 0;';
                
                data.questions.forEach(question => {
                    addQuestionToCanvas({
                        question: question.question_text,
                        type: question.question_type,
                        options: question.options || [],
                        correct_answer: question.correct_answers ? question.correct_answers[0] : '',
                        explanation: question.explanation || '',
                        points: question.points || 1
                    });
                });
            }
            
            // Set save button text based on current status
            const saveDraftText = document.getElementById('saveDraftText');
            const publishText = document.getElementById('publishText');
            
            if (saveDraftText) {
                saveDraftText.textContent = data.quiz.is_draft ? 'Save as Draft' : 'Move to Draft';
            }
            
            if (publishText) {
                publishText.textContent = data.quiz.is_draft ? 'Publish Quiz' : 'Update Quiz';
            }
            
            // Show the modal
            const quizModal = new bootstrap.Modal(document.getElementById('createQuizModal'));
            quizModal.show();
            
        } else {
            console.error('Failed to load quiz data:', data);
            showAlert('danger', 'Failed to load quiz data. Please try again.');
        }
    } catch (error) {
        console.error('Error fetching quiz data:', error);
        showAlert('danger', 'Error loading quiz data. Please try again.');
    }
}

// Delete quiz function
async function deleteQuiz(quizId) {
    if (!confirm('Are you sure you want to permanently delete this quiz? This action cannot be undone.')) {
        return;
    }
    
    // Double confirmation for safety
    if (!confirm('This will permanently delete the quiz and all its questions. Are you absolutely sure?')) {
        return;
    }
    
    try {
        const response = await fetch(`/admin/quiz-generator/${quizId}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

// Alert function for showing messages
function showAlert(type, message) {
    // Remove any existing alerts
    const existingAlerts = document.querySelectorAll('.alert.position-fixed');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i>
            <div>${message}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Initialize counters and storage
window.questionCounter = 0;
window.quizQuestions = {};
window.aiQuestions = [];
window.currentQuizId = null;

// Store the last uploaded file for regeneration
let lastUploadedFile = null;

// Handle file input change
function handleFileChange() {
    const fileInput = document.getElementById('ai_document');
    const regenerateBtn = document.getElementById('regenerateBtn');
    
    console.log('handleFileChange called');
    console.log('File input files:', fileInput.files);
    
    if (fileInput.files[0]) {
        console.log('New file selected:', fileInput.files[0].name);
        // Hide regenerate button when a new file is selected
        regenerateBtn.style.display = 'none';
        // Clear any existing AI questions
        document.getElementById('aiQuestionsContainer').innerHTML = '';
        // Reset lastUploadedFile to null since we have a new file
        lastUploadedFile = null;
        console.log('lastUploadedFile reset to null');
    }
}

// Generate AI questions - Updated for admin routes
async function generateAIQuestions() {
    const fileInput = document.getElementById('ai_document');
    const questionsCount = document.getElementById('ai_question_count').value;
    const generateBtn = document.getElementById('generateAIBtn');
    const btnText = document.getElementById('generateBtnText');
    const spinner = generateBtn.querySelector('.spinner-border');
    
    console.log('generateAIQuestions called');
    console.log('File input element:', fileInput);
    console.log('File input files:', fileInput?.files);
    console.log('File input value:', fileInput?.value);
    console.log('lastUploadedFile before:', lastUploadedFile);
    
    if (!fileInput) {
        console.error('File input element not found');
        alert('File input not found. Please refresh the page and try again.');
        return;
    }
    
    if (!fileInput.files[0]) {
        console.error('No file selected in input');
        alert('Please select a document to upload');
        return;
    }

    console.log('File selected:', fileInput.files[0].name, 'Size:', fileInput.files[0].size);

    // Store the file for potential regeneration
    lastUploadedFile = fileInput.files[0];
    console.log('lastUploadedFile set to:', lastUploadedFile?.name);

    // Prevent spamming - check if already generating
    if (generateBtn.disabled) {
        return;
    }

    // Show loading state and disable button
    generateBtn.disabled = true;
    generateBtn.classList.add('disabled');
    if (spinner) {
        spinner.classList.remove('d-none');
    }
    if (btnText) {
        btnText.textContent = 'Generating Questions...';
    }

    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('num_questions', questionsCount);
    formData.append('question_type', document.getElementById('aiQuestionType').value);
    formData.append('_token', window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Add timestamp to ensure unique generation
    formData.append('timestamp', Date.now());

    // Show AI panel
    showAIPanel();
    
    // Show loading in AI panel
    document.getElementById('aiQuestionsContainer').innerHTML = `
        <div class="d-flex flex-column align-items-center justify-content-center h-100">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Generating...</span>
            </div>
            <h6 class="text-muted">Generating Questions</h6>
            <p class="text-muted text-center small">Using AI to analyze your document and create unique quiz questions...</p>
            <div class="progress mt-3" style="width: 200px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
            </div>
            <div class="mt-3 text-center">
                <small class="text-muted">
                    <i class="bi bi-info-circle"></i> 
                    This may take a few moments. Please don't click the button again.
                </small>
            </div>
        </div>
    `;

    try {
        console.log('Sending AI generation request...');
        console.log('CSRF Token:', window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        console.log('FormData contents:', {
            file: fileInput.files[0]?.name,
            num_questions: questionsCount,
            question_type: document.getElementById('aiQuestionType').value,
            timestamp: Date.now()
        });
        
        // Updated route for admin
        const response = await fetch('/admin/quiz-generator/generate-ai-questions', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin' // Include session cookies
        });

        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success && data.questions) {
            displayAIQuestions(data.questions);
            // Show regenerate button after successful generation
            const regenerateBtn = document.getElementById('regenerateBtn');
            regenerateBtn.style.display = 'block';
            // Add a subtle animation to draw attention
            regenerateBtn.classList.add('btn-pulse');
            setTimeout(() => regenerateBtn.classList.remove('btn-pulse'), 2000);
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
        // Reset button state after a minimum delay to prevent rapid clicking
        setTimeout(() => {
            // Re-fetch elements to ensure they still exist in the DOM
            const generateBtn = document.getElementById('generateAIBtn');
            if (generateBtn) {
                generateBtn.disabled = false;
                generateBtn.classList.remove('disabled');
                generateBtn.innerHTML = '<i class="bi bi-magic"></i> <span id="generateBtnText">Generate Questions</span>';
            } else {
                console.warn('Generate button element not found during cleanup');
            }
            
            // Re-fetch spinner and btnText elements
            const spinner = generateBtn ? generateBtn.querySelector('.spinner-border') : null;
            const btnText = generateBtn ? generateBtn.querySelector('#generateBtnText') : null;
            
            if (spinner) {
                spinner.classList.add('d-none');
            }
            if (btnText) {
                btnText.textContent = 'Generate Questions';
            }
        }, 2000); // Minimum 2-second delay
    }
}

// Regenerate questions with the same file
async function regenerateWithSameFile() {
    if (!lastUploadedFile) {
        alert('No file available for regeneration. Please upload a file first.');
        return;
    }

    const questionsCount = document.getElementById('ai_question_count').value;
    const regenerateBtn = document.getElementById('regenerateBtn');
    const generateBtn = document.getElementById('generateAIBtn');
    
    // Disable both buttons during regeneration
    regenerateBtn.disabled = true;
    generateBtn.disabled = true;
    regenerateBtn.innerHTML = '<i class="bi bi-arrow-clockwise spinner-border spinner-border-sm"></i> Regenerating...';

    const formData = new FormData();
    formData.append('file', lastUploadedFile);
    formData.append('num_questions', questionsCount);
    formData.append('question_type', document.getElementById('aiQuestionType').value);
    formData.append('_token', window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Add timestamp to ensure unique generation
    formData.append('timestamp', Date.now());
    formData.append('regenerate', 'true'); // Flag to indicate this is a regeneration

    // Show loading in AI panel
    document.getElementById('aiQuestionsContainer').innerHTML = `
        <div class="d-flex flex-column align-items-center justify-content-center h-100">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Regenerating...</span>
            </div>
            <h6 class="text-muted">Regenerating Questions</h6>
            <p class="text-muted text-center small">Creating new questions from the same document...</p>
            <div class="progress mt-3" style="width: 200px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
            </div>
            <div class="mt-3 text-center">
                <small class="text-muted">
                    <i class="bi bi-info-circle"></i> 
                    This may take a few moments. Please don't click the button again.
                </small>
            </div>
        </div>
    `;

    try {
        // Updated route for admin
        const response = await fetch('/admin/quiz-generator/generate-ai-questions', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin' // Include session cookies
        });
        
        const data = await response.json();
        
        if (data.success && data.questions) {
            displayAIQuestions(data.questions);
        } else {
            document.getElementById('aiQuestionsContainer').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> 
                    <strong>Regeneration Failed</strong><br>
                    ${data.message || 'Failed to regenerate questions. Please try again.'}
                </div>
            `;
        }
    } catch (error) {
        console.error('AI Regeneration Error:', error);
        document.getElementById('aiQuestionsContainer').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> 
                <strong>Error</strong><br>
                Network error occurred. Please check your connection and try again.
            </div>
        `;
    } finally {
        // Reset button state after a minimum delay
        setTimeout(() => {
            // Re-fetch elements to ensure they still exist in the DOM
            const regenerateBtn = document.getElementById('regenerateBtn');
            const generateBtn = document.getElementById('generateAIBtn');
            
            if (regenerateBtn) {
                regenerateBtn.disabled = false;
                regenerateBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Regenerate with Same File';
            } else {
                console.warn('Regenerate button element not found during cleanup');
            }
            
            if (generateBtn) {
                generateBtn.disabled = false;
                generateBtn.classList.remove('disabled');
            } else {
                console.warn('Generate button element not found during cleanup');
            }
        }, 2000); // Minimum 2-second delay
    }
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
    
    let html = '';
    questions.forEach((question, index) => {
        html += createAIQuestionCard(question, index);
    });
    
    container.innerHTML = html;
    
    // Store AI questions globally
    window.aiQuestions = questions;
}

function createAIQuestionCard(question, index) {
    return `
        <div class="card mb-3 ai-question-card" data-question-index="${index}">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-info">${question.category || 'General'}</span>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="addAIQuestionToCanvas(${index})">
                        <i class="bi bi-plus"></i> Add
                    </button>
                </div>
                <div class="fw-bold mb-2 small">${question.question}</div>
                ${question.type === 'multiple_choice' && question.options ? renderAIQuestionOptions(question) : ''}
                ${question.explanation ? `<div class="small text-muted mt-2"><strong>Explanation:</strong> ${question.explanation}</div>` : ''}
            </div>
        </div>
    `;
}

function renderAIQuestionOptions(question) {
    if (!question.options) return '';
    
    let optionsHtml = '<div class="small mt-2">';
    
    // Handle object format options (A, B, C, D keys)
    if (typeof question.options === 'object' && !Array.isArray(question.options)) {
        Object.keys(question.options).forEach(key => {
            const isCorrect = question.correct_answer === key;
            optionsHtml += `
                <div class="option-item mb-1 ${isCorrect ? 'text-success fw-bold' : ''}">
                    ${key}. ${question.options[key]}
                    ${isCorrect ? '<i class="bi bi-check-circle text-success ms-1"></i>' : ''}
                </div>
            `;
        });
    }
    // Handle array format options
    else if (Array.isArray(question.options)) {
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

// AI Panel Functions
function showAIPanel() {
    const mainCanvas = document.getElementById('mainQuizCanvas');
    const aiPanel = document.getElementById('aiQuestionsPanel');
    
    mainCanvas.classList.remove('col-12');
    mainCanvas.classList.add('col-8');
    aiPanel.style.display = 'block';
}

function closeAIPanel() {
    const mainCanvas = document.getElementById('mainQuizCanvas');
    const aiPanel = document.getElementById('aiQuestionsPanel');
    
    mainCanvas.classList.remove('col-8');
    mainCanvas.classList.add('col-12');
    aiPanel.style.display = 'none';
}

function addAIQuestionToCanvas(questionIndex) {
    const aiQuestion = window.aiQuestions[questionIndex];
    if (!aiQuestion) return;
    
    // Convert AI question format to our internal format
    const convertedQuestion = {
        question: aiQuestion.question,
        type: aiQuestion.type || 'multiple_choice',
        category: aiQuestion.category || 'general',
        options: convertAIOptions(aiQuestion.options),
        correct_answer: aiQuestion.correct_answer || 'A',
        explanation: aiQuestion.explanation || '',
        points: 1,
        source: 'ai_generated'
    };
    
    addQuestionToCanvas(convertedQuestion);
    
    // Remove from AI panel
    const questionCard = document.querySelector(`[data-question-index="${questionIndex}"]`);
    if (questionCard) {
        questionCard.remove();
    }
}

function convertAIOptions(options) {
    if (!options) return ['', '', '', ''];
    
    // If it's already an array, return as is
    if (Array.isArray(options)) {
        return options;
    }
    
    // If it's an object with A, B, C, D keys, convert to array
    if (typeof options === 'object') {
        return [
            options.A || options.a || '',
            options.B || options.b || '',
            options.C || options.c || '',
            options.D || options.d || ''
        ];
    }
    
    return ['', '', '', ''];
}

function addAllAIQuestions() {
    if (!window.aiQuestions || window.aiQuestions.length === 0) {
        alert('No AI questions available to add.');
        return;
    }
    
    window.aiQuestions.forEach((question, index) => {
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

// Add question to canvas from AI panel or manual
function addQuestionToCanvas(question) {
    // Remove empty state and change canvas layout
    const canvas = document.getElementById('quizCanvas');
    const emptyState = canvas.querySelector('i.bi-clipboard');
    if (emptyState) {
        canvas.innerHTML = '';
        // Change canvas styling for question layout
        canvas.className = 'mb-3';
        canvas.style.cssText = 'min-height: auto; border: none; padding: 0;';
    }

    const questionId = `question_${Date.now()}_${++window.questionCounter}`;
    
    // Create inline question form directly in canvas
    const questionHtml = `
        <div class="quiz-question-item border rounded p-4 mb-3" data-question-id="${questionId}" data-question-type="${question.type}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">
                    <i class="bi bi-grip-vertical text-muted me-2"></i>
                    Question ${window.questionCounter}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion('${questionId}')">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Question Text *</label>
                <textarea class="form-control" rows="3" placeholder="Enter your question here..." onchange="updateQuestionData('${questionId}', 'question', this.value)">${question.question || ''}</textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Question Type</label>
                    <select class="form-select" onchange="changeQuestionType('${questionId}', this.value)">
                        <option value="multiple_choice" ${question.type === 'multiple_choice' ? 'selected' : ''}>Multiple Choice</option>
                        <option value="true_false" ${question.type === 'true_false' ? 'selected' : ''}>True/False</option>
                        <option value="short_answer" ${question.type === 'short_answer' ? 'selected' : ''}>Short Answer</option>
                        <option value="essay" ${question.type === 'essay' ? 'selected' : ''}>Essay</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Points</label>
                    <input type="number" class="form-control" value="${question.points || 1}" min="1" onchange="updateQuestionData('${questionId}', 'points', this.value)">
                </div>
            </div>
            
            <div id="options_${questionId}">
                ${createOptionsForQuestion(question, questionId)}
            </div>
            
            <div class="mt-3">
                <label class="form-label">Explanation (Optional)</label>
                <textarea class="form-control" rows="2" placeholder="Provide an explanation for the correct answer..." onchange="updateQuestionData('${questionId}', 'explanation', this.value)">${question.explanation || ''}</textarea>
            </div>
        </div>
    `;
    
    canvas.insertAdjacentHTML('beforeend', questionHtml);
    
    // Store question data
    if (!window.quizQuestions) {
        window.quizQuestions = {};
    }
    window.quizQuestions[questionId] = {
        id: questionId,
        question: question.question || '',
        type: question.type || 'multiple_choice',
        options: question.options || ['', '', '', ''],
        correct_answer: question.correct_answer || 0,
        points: question.points || 1,
        explanation: question.explanation || ''
    };
    
    // Update question count
    updateQuestionCount();
}

// Create options HTML based on question type
function createOptionsForQuestion(question, questionId) {
    switch (question.type) {
        case 'multiple_choice':
            return `
                <label class="form-label">Answer Options</label>
                ${question.options.map((opt, i) => `
                    <div class="mb-2">
                        <div class="input-group">
                            <div class="input-group-text">
                                <input type="radio" name="correct_${questionId}" value="${i}" 
                                       ${question.correct_answer === String.fromCharCode(65 + i) || question.correct_answer === i ? 'checked' : ''} 
                                       onchange="updateCorrectAnswer('${questionId}', ${i})">
                            </div>
                            <span class="input-group-text">${String.fromCharCode(65 + i)}.</span>
                            <input type="text" class="form-control" placeholder="Option ${String.fromCharCode(65 + i)}" 
                                   value="${opt}" onchange="updateOption('${questionId}', ${i}, this.value)">
                        </div>
                    </div>
                `).join('')}
            `;
        case 'true_false':
            return `
                <label class="form-label">Correct Answer</label>
                <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tf_${questionId}" value="true" id="true_${questionId}" onchange="updateQuestionData('${questionId}', 'correct_answer', 'true')">
                        <label class="form-check-label" for="true_${questionId}">True</label>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tf_${questionId}" value="false" id="false_${questionId}" onchange="updateQuestionData('${questionId}', 'correct_answer', 'false')">
                        <label class="form-check-label" for="false_${questionId}">False</label>
                    </div>
                </div>
            `;
        case 'short_answer':
        case 'essay':
            return `
                <label class="form-label">Sample Answer</label>
                <textarea class="form-control" rows="3" placeholder="Enter a sample answer..." 
                          onchange="updateQuestionData('${questionId}', 'correct_answer', this.value)">${question.correct_answer || ''}</textarea>
            `;
        default:
            return `
                <label class="form-label">Answer Options</label>
                <div class="mb-2">
                    <div class="input-group">
                        <div class="input-group-text">
                            <input type="radio" name="correct_${questionId}" value="0" checked onchange="updateCorrectAnswer('${questionId}', 0)">
                        </div>
                        <span class="input-group-text">A.</span>
                        <input type="text" class="form-control" placeholder="Option A" onchange="updateOption('${questionId}', 0, this.value)">
                    </div>
                </div>
                <div class="mb-2">
                    <div class="input-group">
                        <div class="input-group-text">
                            <input type="radio" name="correct_${questionId}" value="1" onchange="updateCorrectAnswer('${questionId}', 1)">
                        </div>
                        <span class="input-group-text">B.</span>
                        <input type="text" class="form-control" placeholder="Option B" onchange="updateOption('${questionId}', 1, this.value)">
                    </div>
                </div>
                <div class="mb-2">
                    <div class="input-group">
                        <div class="input-group-text">
                            <input type="radio" name="correct_${questionId}" value="2" onchange="updateCorrectAnswer('${questionId}', 2)">
                        </div>
                        <span class="input-group-text">C.</span>
                        <input type="text" class="form-control" placeholder="Option C" onchange="updateOption('${questionId}', 2, this.value)">
                    </div>
                </div>
                <div class="mb-2">
                    <div class="input-group">
                        <div class="input-group-text">
                            <input type="radio" name="correct_${questionId}" value="3" onchange="updateCorrectAnswer('${questionId}', 3)">
                        </div>
                        <span class="input-group-text">D.</span>
                        <input type="text" class="form-control" placeholder="Option D" onchange="updateOption('${questionId}', 3, this.value)">
                    </div>
                </div>
            `;
    }
}

// Add manual question
function addManualQuestion() {
    const question = {
        type: 'multiple_choice',
        question: '',
        options: ['Option A', 'Option B', 'Option C', 'Option D'],
        correct_answer: 0
    };

    addQuestionToCanvas(question);
}

// Helper functions for question management
function updateQuestionData(questionId, field, value) {
    if (!window.quizQuestions) {
        window.quizQuestions = {};
    }
    if (!window.quizQuestions[questionId]) {
        window.quizQuestions[questionId] = {};
    }
    window.quizQuestions[questionId][field] = value;
}

function updateOption(questionId, optionIndex, value) {
    if (!window.quizQuestions[questionId]) {
        window.quizQuestions[questionId] = { options: ['', '', '', ''] };
    }
    if (!window.quizQuestions[questionId].options) {
        window.quizQuestions[questionId].options = ['', '', '', ''];
    }
    window.quizQuestions[questionId].options[optionIndex] = value;
}

function updateCorrectAnswer(questionId, correctIndex) {
    updateQuestionData(questionId, 'correct_answer', correctIndex);
}

function removeQuestion(questionId) {
    if (confirm('Are you sure you want to remove this question?')) {
        // Remove from DOM
        document.querySelector(`[data-question-id="${questionId}"]`).remove();
        
        // Remove from question data
        if (window.quizQuestions && window.quizQuestions[questionId]) {
            delete window.quizQuestions[questionId];
        }
        
        // Show empty canvas if no questions left
        const canvas = document.getElementById('quizCanvas');
        if (canvas.children.length === 0) {
            canvas.innerHTML = `
                <i class="bi bi-clipboard text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-2">No questions added yet. Click Add Question or use AI Generate to get started.</h5>
                <p class="text-muted text-center">You can drag and drop questions to reorder them.</p>
            `;
            canvas.className = 'mb-3 d-flex flex-column align-items-center justify-content-center';
            canvas.style.cssText = 'min-height: 200px; border: 2px dashed #dee2e6; border-radius: 8px; padding: 20px;';
        }
        
        // Update question count
        updateQuestionCount();
    }
}

function changeQuestionType(questionId, newType) {
    updateQuestionData(questionId, 'type', newType);
    
    // Update the options section based on question type
    const optionsContainer = document.getElementById(`options_${questionId}`);
    
    if (newType === 'multiple_choice') {
        optionsContainer.innerHTML = `
            <label class="form-label">Answer Options (Select all correct answers)</label>
            <div class="mb-2">
                <div class="input-group">
                    <div class="input-group-text">
                        <input type="checkbox" name="correct_${questionId}" value="0" checked onchange="updateCorrectAnswers('${questionId}')">
                    </div>
                    <span class="input-group-text">A.</span>
                    <input type="text" class="form-control" placeholder="Option A" onchange="updateOption('${questionId}', 0, this.value)">
                </div>
            </div>
            <div class="mb-2">
                <div class="input-group">
                    <div class="input-group-text">
                        <input type="checkbox" name="correct_${questionId}" value="1" onchange="updateCorrectAnswers('${questionId}')">
                    </div>
                    <span class="input-group-text">B.</span>
                    <input type="text" class="form-control" placeholder="Option B" onchange="updateOption('${questionId}', 1, this.value)">
                </div>
            </div>
            <div class="mb-2">
                <div class="input-group">
                    <div class="input-group-text">
                        <input type="checkbox" name="correct_${questionId}" value="2" onchange="updateCorrectAnswers('${questionId}')">
                    </div>
                    <span class="input-group-text">C.</span>
                    <input type="text" class="form-control" placeholder="Option C" onchange="updateOption('${questionId}', 2, this.value)">
                </div>
            </div>
            <div class="mb-2">
                <div class="input-group">
                    <div class="input-group-text">
                        <input type="checkbox" name="correct_${questionId}" value="3" onchange="updateCorrectAnswers('${questionId}')">
                    </div>
                    <span class="input-group-text">D.</span>
                    <input type="text" class="form-control" placeholder="Option D" onchange="updateOption('${questionId}', 3, this.value)">
                </div>
            </div>
        `;
    } else if (newType === 'true_false') {
        optionsContainer.innerHTML = `
            <label class="form-label">Correct Answer</label>
            <div class="mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tf_${questionId}" value="true" id="true_${questionId}" onchange="updateQuestionData('${questionId}', 'correct_answer', 'true')">
                    <label class="form-check-label" for="true_${questionId}">True</label>
                </div>
            </div>
            <div class="mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tf_${questionId}" value="false" id="false_${questionId}" onchange="updateQuestionData('${questionId}', 'correct_answer', 'false')">
                    <label class="form-check-label" for="false_${questionId}">False</label>
                </div>
            </div>
        `;
    } else if (newType === 'short_answer' || newType === 'essay') {
        optionsContainer.innerHTML = `
            <label class="form-label">Sample Answer</label>
            <textarea class="form-control" rows="3" placeholder="Enter a sample answer..." 
                      onchange="updateQuestionData('${questionId}', 'correct_answer', this.value)"></textarea>
        `;
    }
}

// Program and Module Loading Functions - Updated for admin routes
async function loadModules(programId) {
    try {
        console.log('Fetching modules for program:', programId);
        const response = await fetch(`/admin/quiz-generator/modules/${programId}`, {
            credentials: 'same-origin' // Include session cookies
        });
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Modules data:', data);
        
        const moduleSelect = document.getElementById('module_id');
        if (!moduleSelect) {
            console.error('Module select element not found in loadModules!');
            return;
        }
        
        moduleSelect.innerHTML = '<option value="">Select Module</option>';
        
        if (data.success && data.modules) {
            console.log('Found modules:', data.modules.length);
            data.modules.forEach(module => {
                console.log('Adding module:', module);
                moduleSelect.innerHTML += `<option value="${module.module_id}">${module.module_name}</option>`;
            });
            console.log('Module select options after loading:', moduleSelect.innerHTML);
        } else {
            console.log('No modules found or invalid response');
        }
        
    } catch (error) {
        console.error('Error loading modules:', error);
    }
}

async function loadCourses(moduleId) {
    try {
        console.log('Fetching courses for module:', moduleId);
        const response = await fetch(`/admin/quiz-generator/courses/${moduleId}`, {
            credentials: 'same-origin' // Include session cookies
        });
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Courses data:', data);
        
        const courseSelect = document.getElementById('course_id');
        if (!courseSelect) {
            console.error('Course select element not found in loadCourses!');
            return;
        }
        
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        
        if (data.success && data.courses) {
            console.log('Found courses:', data.courses.length);
            data.courses.forEach(course => {
                console.log('Adding course:', course);
                courseSelect.innerHTML += `<option value="${course.course_id}">${course.course_name}</option>`;
            });
            console.log('Course select options after loading:', courseSelect.innerHTML);
        } else {
            console.log('No courses found or invalid response');
        }
        
    } catch (error) {
        console.error('Error loading courses:', error);
    }
}

// Save quiz - Updated for admin context
async function saveQuiz(isDraft = true) {
    const form = document.getElementById('quizForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const title = document.getElementById('quiz_title').value.trim();
    const programId = document.getElementById('program_id').value;
    
    if (!title) {
        alert('Please enter a quiz title');
        return;
    }
    
    if (!programId) {
        alert('Please select a program');
        return;
    }
    
    // Validate deadline if checkbox is checked
    const hasDeadline = document.getElementById('has_deadline').checked;
    const dueDate = document.getElementById('due_date').value;
    
    if (hasDeadline && !dueDate) {
        alert('Please select a deadline date and time when "Set Quiz Deadline" is checked');
        document.getElementById('due_date').focus();
        return;
    }
    
    // Collect questions data
    const questions = [];
    if (window.quizQuestions) {
        Object.values(window.quizQuestions).forEach((questionData, index) => {
            if (questionData.question && questionData.question.trim()) {
                questions.push({
                    question_text: questionData.question,
                    question_type: questionData.type || 'multiple_choice',
                    points: questionData.points || 1,
                    explanation: questionData.explanation || '',
                    options: questionData.options || [],
                    correct_answers: questionData.correct_answers || [questionData.correct_answer || 0],
                    order: index + 1
                });
            }
        });
    }
    
    if (questions.length === 0) {
        alert('Please add at least one question');
        return;
    }
    
    // Prepare data for submission
    const adminId = window.myId || window.adminQuizGenerator.myId || null;
    console.log('Admin ID for quiz save:', {
        window_myId: window.myId,
        window_adminQuizGenerator_myId: window.adminQuizGenerator ? window.adminQuizGenerator.myId : 'undefined',
        final_admin_id: adminId
    });
    
    // Determine if this is an edit or create operation
    const isEdit = window.currentQuizId && document.getElementById('quizId').value;
    const quizId = isEdit ? window.currentQuizId : null;
    
    // Prepare questions with quiz_id for updates
    const preparedQuestions = questions.map(question => {
        if (isEdit) {
            question.quiz_id = quizId;
        }
        return question;
    });
    
    const quizData = {
        title: title,
        description: document.getElementById('quiz_description').value.trim(),
        program_id: programId,
        module_id: document.getElementById('module_id').value || null,
        course_id: document.getElementById('course_id').value || null,
        admin_id: adminId,
        quiz_id: quizId, // Add quiz_id for updates
        time_limit: parseInt(document.getElementById('time_limit').value) || 60,
        max_attempts: parseInt(document.getElementById('max_attempts').value) || 1,
        infinite_retakes: document.getElementById('infinite_retakes').checked,
        has_deadline: document.getElementById('has_deadline').checked,
        due_date: document.getElementById('has_deadline').checked ? document.getElementById('due_date').value : null,
        questions: preparedQuestions,
        is_draft: isDraft,
        status: isDraft ? 'draft' : 'published',
        _token: window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    console.log('Quiz data being sent:', quizData);

    try {
        // URL and method are already determined above
        const url = isEdit ? 
            `/admin/quiz-generator/update-quiz/${quizId}` : 
            '/admin/quiz-generator/save-quiz';
        
        const method = isEdit ? 'PUT' : 'POST';    
        console.log(`Sending ${method} request to ${url}`, quizData);
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin', // This is crucial for including session cookies
            body: JSON.stringify(quizData)
        });

        // Log the response status for debugging
        console.log('Response status:', response.status);
        
        // Check if response is OK (status in the range 200-299)
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            try {
                // Try to parse as JSON
                const errorData = JSON.parse(errorText);
                showAlert('danger', 'Error saving quiz: ' + (errorData.message || 'Server error'));
            } catch (parseError) {
                // If not valid JSON, show the text
                showAlert('danger', 'Error saving quiz: Server error');
            }
            return;
        }
        
        const data = await response.json();
        
        if (data.success) {
            const action = isEdit ? 'updated' : 'created';
            const status = isDraft ? 'draft' : 'published';
            showAlert('success', `Quiz ${action} successfully as ${status}!`);
            // Close modal and reload page
            const modal = bootstrap.Modal.getInstance(document.getElementById('createQuizModal'));
            modal.hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('danger', 'Error saving quiz: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error saving quiz:', error);
        showAlert('danger', 'Error updating quiz: ' + error.message);
    }
}

function updateQuestionCount() {
    const count = Object.keys(window.quizQuestions || {}).length;
    const questionCountElement = document.getElementById('questionCount');
    if (questionCountElement) {
        questionCountElement.textContent = count;
    }
}

function showAlert(type, message) {
    // Remove any existing alerts
    const existingAlerts = document.querySelectorAll('.alert.position-fixed');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i>
            <div>${message}</div>
        </div>
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

// Drag and drop functionality
function allowDrop(event) {
    event.preventDefault();
    event.currentTarget.classList.add('drag-over');
}

function dropQuestion(event) {
    event.preventDefault();
    event.currentTarget.classList.remove('drag-over');
    
    const questionIndex = event.dataTransfer.getData('text/plain');
    addAIQuestionToCanvas(parseInt(questionIndex));
}

// Clear file input when modal is closed
document.addEventListener('DOMContentLoaded', function() {
    const createQuizModal = document.getElementById('createQuizModal');
    if (createQuizModal) {
        createQuizModal.addEventListener('hidden.bs.modal', function() {
            console.log('Modal closed - resetting state');
            // Clear file input
            const fileInput = document.getElementById('ai_document');
            if (fileInput) {
                fileInput.value = '';
                console.log('File input cleared');
            }
            // Reset regenerate button
            const regenerateBtn = document.getElementById('regenerateBtn');
            if (regenerateBtn) {
                regenerateBtn.style.display = 'none';
                console.log('Regenerate button hidden');
            }
            // Clear AI questions
            const aiContainer = document.getElementById('aiQuestionsContainer');
            if (aiContainer) {
                aiContainer.innerHTML = '';
                console.log('AI container cleared');
            }
            // Reset last uploaded file
            lastUploadedFile = null;
            console.log('lastUploadedFile reset to null');
        });
    }

    // Handle deadline checkbox functionality
    const hasDeadlineCheckbox = document.getElementById('has_deadline');
    const dueDateInput = document.getElementById('due_date');
    const infiniteRetakesCheckbox = document.getElementById('infinite_retakes');
    const maxAttemptsInput = document.getElementById('max_attempts');

    if (hasDeadlineCheckbox && dueDateInput) {
        hasDeadlineCheckbox.addEventListener('change', function() {
            dueDateInput.disabled = !this.checked;
            if (this.checked) {
                dueDateInput.required = true;
                dueDateInput.setAttribute('required', 'required');
            } else {
                dueDateInput.required = false;
                dueDateInput.removeAttribute('required');
                dueDateInput.value = '';
            }
        });
    }

    // Handle infinite retakes functionality
    if (infiniteRetakesCheckbox && maxAttemptsInput) {
        infiniteRetakesCheckbox.addEventListener('change', function() {
            if (this.checked) {
                maxAttemptsInput.disabled = true;
                maxAttemptsInput.value = 999;
            } else {
                maxAttemptsInput.disabled = false;
                maxAttemptsInput.value = 1;
            }
        });
    }

    // Test modal functionality
    const modalElement = document.getElementById('createQuizModal');
    if (modalElement) {
        console.log('Modal element found');
        
        modalElement.addEventListener('shown.bs.modal', function() {
            console.log('Modal shown event fired');
        });
        
        modalElement.addEventListener('hidden.bs.modal', function() {
            console.log('Modal hidden event fired');
        });
    } else {
        console.error('Modal element not found');
    }

    // Set up program selection event listeners
    const programSelect = document.getElementById('program_id');
    const moduleSelect = document.getElementById('module_id');
    const courseSelect = document.getElementById('course_id');
    
    console.log('DOM Elements found:', {
        programSelect: !!programSelect,
        moduleSelect: !!moduleSelect,
        courseSelect: !!courseSelect
    });
    
    if (programSelect) {
        console.log('Adding change event listener to program select');
        programSelect.addEventListener('change', function() {
            const programId = this.value;
            console.log('Program selected:', programId);
            console.log('Module select element:', moduleSelect);
            console.log('Course select element:', courseSelect);
            
            if (programId) {
                console.log('Loading modules for program:', programId);
                loadModules(programId);
                if (moduleSelect) {
                    moduleSelect.disabled = false;
                    console.log('Module select enabled:', !moduleSelect.disabled);
                } else {
                    console.log('Module select element not found!');
                }
            } else {
                console.log('No program selected, disabling dropdowns');
                if (moduleSelect) {
                    moduleSelect.innerHTML = '<option value="">Select Module</option>';
                    moduleSelect.disabled = true;
                }
                if (courseSelect) {
                    courseSelect.innerHTML = '<option value="">Select Course</option>';
                    courseSelect.disabled = true;
                }
            }
        });
    } else {
        console.log('Program select element not found!');
    }
    
    if (moduleSelect) {
        console.log('Adding change event listener to module select');
        moduleSelect.addEventListener('change', function() {
            const moduleId = this.value;
            console.log('Module selected:', moduleId);
            
            if (moduleId) {
                console.log('Loading courses for module:', moduleId);
                loadCourses(moduleId);
                if (courseSelect) {
                    courseSelect.disabled = false;
                    console.log('Course select enabled:', !courseSelect.disabled);
                } else {
                    console.log('Course select element not found!');
                }
            } else {
                console.log('No module selected, disabling course dropdown');
                if (courseSelect) {
                    courseSelect.innerHTML = '<option value="">Select Course</option>';
                    courseSelect.disabled = true;
                }
            }
        });
    } else {
        console.log('Module select element not found!');
    }
});

console.log('Admin Quiz Generator JavaScript Loaded Successfully');

// Quiz management functions for table actions
function editQuiz(quizId) {
    console.log('Edit quiz:', quizId);
    window.currentQuizId = quizId;
    
    // Reset the form
    document.getElementById('quizForm').reset();
    document.getElementById('quizId').value = quizId;
    
    // Clear existing questions
    const quizCanvas = document.getElementById('quizCanvas');
    quizCanvas.innerHTML = `
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading quiz data...</p>
        </div>
    `;
    
    // Show the AI Generator section or hide it based on edit mode
    document.getElementById('aiGeneratorSection').style.display = 'none';
    
    // Update modal title
    document.getElementById('modalTitle').textContent = 'Edit Quiz';
    
    // Fetch quiz data
    fetch(`/admin/quiz-generator/quiz/${quizId}`, {
        headers: {
            'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Quiz data loaded:', data);
            
            // Set form values
            document.getElementById('quiz_title').value = data.quiz.title || data.quiz.quiz_title || '';
            document.getElementById('quiz_description').value = data.quiz.quiz_description || '';
            
            // Set program, module, course selects
            const programSelect = document.getElementById('program_id');
            if (programSelect && data.quiz.program_id) {
                programSelect.value = data.quiz.program_id;
                programSelect.dispatchEvent(new Event('change')); // This should trigger loading modules
                
                // Need to set module and course after their options are loaded
                setTimeout(() => {
                    const moduleSelect = document.getElementById('module_id');
                    if (moduleSelect && data.quiz.module_id) {
                        moduleSelect.value = data.quiz.module_id;
                        moduleSelect.dispatchEvent(new Event('change')); // This should trigger loading courses
                        
                        // Need to set course after its options are loaded
                        setTimeout(() => {
                            const courseSelect = document.getElementById('course_id');
                            if (courseSelect && data.quiz.course_id) {
                                courseSelect.value = data.quiz.course_id;
                            }
                        }, 500);
                    }
                }, 500);
            }
            
            // Set quiz settings
            document.getElementById('time_limit').value = data.quiz.time_limit || 60;
            document.getElementById('max_attempts').value = data.quiz.max_attempts || 1;
            document.getElementById('infinite_retakes').checked = data.quiz.infinite_retakes || false;
            if (data.quiz.infinite_retakes) {
                document.getElementById('max_attempts').disabled = true;
            }
            
            document.getElementById('has_deadline').checked = data.quiz.has_deadline || false;
            if (data.quiz.has_deadline && data.quiz.due_date) {
                document.getElementById('due_date').disabled = false;
                document.getElementById('due_date').value = data.quiz.due_date.slice(0, 16); // Format as YYYY-MM-DDTHH:MM
            } else {
                document.getElementById('due_date').disabled = true;
            }
            
            // Add questions to canvas
            window.quizQuestions = {};
            window.questionCounter = 0;
            
            if (data.questions && data.questions.length > 0) {
                quizCanvas.innerHTML = '';
                quizCanvas.className = 'mb-3';
                quizCanvas.style.cssText = 'min-height: auto; border: none; padding: 0;';
                
                data.questions.forEach(question => {
                    addQuestionToCanvas({
                        question: question.question_text,
                        type: question.question_type,
                        options: question.options || [],
                        correct_answer: question.correct_answers[0] || '',
                        explanation: question.explanation || '',
                        points: question.points || 1
                    });
                });
            }
            
            // Set save button text based on current status
            const saveDraftText = document.getElementById('saveDraftText');
            const publishText = document.getElementById('publishText');
            
            if (saveDraftText) {
                saveDraftText.textContent = data.quiz.is_draft ? 'Save as Draft' : 'Move to Draft';
            }
            
            if (publishText) {
                publishText.textContent = data.quiz.is_draft ? 'Publish Quiz' : 'Update Quiz';
            }
            
            // Show the modal
            const quizModal = new bootstrap.Modal(document.getElementById('createQuizModal'));
            quizModal.show();
            
        } else {
            console.error('Failed to load quiz data:', data);
            showAlert('danger', 'Failed to load quiz data. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error fetching quiz data:', error);
        showAlert('danger', 'Error loading quiz data. Please try again.');
    });
}

function changeQuizStatus(quizId, newStatus) {
    console.log('Change quiz status:', quizId, 'to', newStatus);
    
    if (!confirm(`Are you sure you want to change the quiz status to ${newStatus}?`)) {
        return;
    }
    
    // Map the status to the correct route
    let routeAction = '';
    switch(newStatus) {
        case 'published':
            routeAction = 'publish';
            break;
        case 'draft':
        case 'drafted':
            routeAction = 'draft';
            break;
        case 'archived':
            routeAction = 'archive';
            break;
        default:
            console.error('Unknown status:', newStatus);
            return;
    }
    
    // Debug logs
    console.log(`Sending POST request to: /admin/quiz-generator/${quizId}/${routeAction}`);
    console.log('CSRF Token:', window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Send AJAX request to change status
    fetch(`/admin/quiz-generator/${quizId}/${routeAction}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showAlert('success', data.message || 'Quiz status changed successfully');
            // Reload the page to update the quiz tables
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert('danger', data.message || 'Failed to update quiz status');
        }
    })
    .catch(error => {
        console.error('Error changing quiz status:', error);
        showAlert('danger', 'An error occurred while updating the quiz status');
    });
}
</script>
@endpush
