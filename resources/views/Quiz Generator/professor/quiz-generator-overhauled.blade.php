@extends('professor.professor-layouts.professor-layout')
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
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- AI Document Upload Section -->
                            <div class="card mb-4">
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
                                        <h5 class="text-muted mt-2">Drag questions here or click "Add Question"</h5>
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
                <button type="button" class="btn btn-primary" onclick="saveQuiz(true)">Save as Draft</button>
                <button type="button" class="btn btn-success" onclick="saveQuiz(false)">Publish Quiz</button>
            </div>
                                        <!-- Draggable manual questions will appear here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Questions Side Panel (Hidden by default) -->
                    <div class="col-4 border-start bg-light" id="aiSidePanel" style="display: none;">
                        <div class="p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="bi bi-robot text-info"></i> AI Generated Questions</h6>
                                <button type="button" class="btn-close" onclick="closeSidePanel()"></button>
                            </div>
                            <div id="aiQuestionsContainer" class="ai-questions-container">
                                <!-- AI generated questions will appear here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Questions Modal -->
<div class="modal fade" id="viewQuestionsModal" tabindex="-1" aria-labelledby="viewQuestionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="viewQuestionsModalLabel"><i class="bi bi-list-ul"></i> Quiz Questions</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="questionsList">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* disable interaction and dim the sidebar */
#modernSidebar.sidebar-disabled {
  pointer-events: none;
  opacity: 0.25;
  filter: blur(1px);
  transition: opacity .2s ease, filter .2s ease;
}

/* optionally fully hide instead of dimming (use only if you want it gone) */
#modernSidebar.hide-sidebar {
  display: none !important;
}

/* simplify scroll lock: avoid position:fixed side effects */
body.modal-open {
  overflow: hidden !important;
  position: static !important;
}

/* Modal styles for question view */
#viewQuestionsModal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.explanation-row {
    background-color: #f8f9fa;
    font-size: 0.9em;
}

.explanation-cell {
    padding: 10px 15px;
    border-top: none !important;
    color: #6c757d;
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
    position: relative;
    cursor: move;
    transition: all 0.3s ease;
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

.question-editor {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 15px;
    margin-top: 10px;
}

.question-options {
    display: none;
}

.question-options.active {
    display: block;
}

/* Drag and Drop Visual Feedback */
.drop-zone {
    border: 2px dashed #28a745;
    background-color: #f8fff9;
    border-radius: 8px;
    padding: 20px;
    margin: 10px 0;
    text-align: center;
    transition: all 0.3s ease;
}

.drop-indicator {
    height: 3px;
    background: #0d6efd;
    border-radius: 2px;
    margin: 10px 0;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.drop-indicator.active {
    opacity: 1;
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

/* Progress bar for generation */
.generation-progress {
    width: 100%;
    height: 4px;
    background-color: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 10px;
}

.generation-progress-bar {
    height: 100%;
    background-color: #007bff;
    border-radius: 2px;
    transition: width 0.3s ease;
    animation: progress-animation 2s ease-in-out infinite;
}

@keyframes progress-animation {
    0% { width: 0%; }
    50% { width: 70%; }
    100% { width: 100%; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-fullscreen .col-4 {
        display: none !important;
    }
    
    .modal-fullscreen .col-12 {
        width: 100% !important;
    }
}

/* Question type badges */
.question-type-badge {
    font-size: 0.75rem;
    padding: 2px 8px;
}

/* Sortable placeholder */
.sortable-placeholder {
    border: 2px dashed #6c757d;
    background: #f8f9fa;
    height: 100px;
    margin: 10px 0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

/* Regenerate button styles */
#regenerateBtn {
    transition: all 0.3s ease;
}

#regenerateBtn:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

#regenerateBtn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-pulse {
    animation: pulse 1s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.modal-backdrop {
  z-index: 1050 !important;
}
.modal {
  z-index: 1060 !important;
}

</style>
@endpush

@push('scripts')
<script>
let currentQuizId = null;
let questionCounter = 0;
let aiQuestions = [];
const quizModalEl = document.getElementById('createQuizModal');
const sidebarEl = document.getElementById('modernSidebar');
let savedScroll = 0;

if (quizModalEl) {
  quizModalEl.addEventListener('show.bs.modal', () => {
    // save scroll position so we can restore
    savedScroll = window.scrollY || document.documentElement.scrollTop;

    // disable / dim sidebar
    if (sidebarEl) {
      sidebarEl.classList.add('sidebar-disabled');
      // if you want to fully hide instead, use: sidebarEl.classList.add('hide-sidebar');
    }

    // lock background scrolling (Bootstrap does overflow hidden already; this is reinforcement)
    document.body.classList.add('modal-open');
  });

  quizModalEl.addEventListener('hidden.bs.modal', () => {
    // restore sidebar
    if (sidebarEl) {
      sidebarEl.classList.remove('sidebar-disabled');
      // if you used hide-sidebar, also remove it:
      // sidebarEl.classList.remove('hide-sidebar');
    }

    // restore scroll
    document.body.classList.remove('modal-open');
    window.scrollTo(0, savedScroll);
  });
}

// Initialize when modal opens
document.getElementById('createQuizModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const isEdit = button && button.getAttribute('data-edit-quiz');
    
    if (isEdit) {
        currentQuizId = button.getAttribute('data-quiz-id');
        document.getElementById('modalTitle').textContent = 'Edit Quiz';
        loadQuizData(currentQuizId);
    } else {
        currentQuizId = null;
        document.getElementById('modalTitle').textContent = 'Create New Quiz';
        resetForm();
    }
});

// Reset form for new quiz
function resetForm() {
    document.getElementById('quizForm').reset();
    document.getElementById('quizId').value = '';
    
    // Reset canvas
    const canvas = document.getElementById('quizCanvas');
    canvas.innerHTML = `
        <div id="quizEmptyState" class="text-center">
            <i class="bi bi-inbox" style="font-size: 2.5rem; color: #bdbdbd;"></i>
            <div class="mt-2 text-muted">No questions added yet. Click <b>Add Question</b> or use AI Generate to get started.</div>
        </div>
    `;
    canvas.className = 'mb-3 d-flex flex-column align-items-center justify-content-center';
    canvas.style.cssText = 'min-height: 200px; border: 1px dashed #ccc; padding: 10px;';
    
    closeSidePanel();
    questionCounter = 0;
    window.quizQuestions = {};
}

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
});

// Generate AI questions
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
        
        const response = await fetch('/professor/quiz-generator/generate-ai-questions', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
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
        const response = await fetch('/professor/quiz-generator/generate-ai-questions', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
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
    let html = '';

    questions.forEach((question, index) => {
        html += `
            <div class="ai-question-card" draggable="true" ondragstart="dragStart(event)" data-question-index="${index}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge question-type-badge bg-info">${question.type.replace('_', ' ').toUpperCase()}</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAIQuestionToCanvas(${index})">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <div class="fw-bold mb-2">${question.question}</div>
                ${question.type === 'multiple_choice' && question.options && Object.keys(question.options).length > 0 ? `
                    <div class="small">
                        ${Object.entries(question.options).map(([key, value]) => `
                            <div class="option-item">
                                <span class="${question.correct_answer === key ? 'correct-answer-indicator' : ''}">${key}. ${value}</span>
                            </div>
                        `).join('')}
                    </div>
                ` : question.type === 'true_false' ? `
                    <div class="small">
                        <span class="correct-answer-indicator">Answer: ${question.correct_answer === 'A' ? 'True' : 'False'}</span>
                    </div>
                ` : `
                    <div class="small">
                        <span class="correct-answer-indicator">Answer: ${question.correct_answer || 'Sample answer provided'}</span>
                    </div>
                `}
            </div>
        `;
    });

    container.innerHTML = html;
}

// Show/hide side panel
function showSidePanel() {
    document.getElementById('aiSidePanel').style.display = 'block';
    document.getElementById('mainQuizCanvas').className = 'col-8';
}

function closeSidePanel() {
    document.getElementById('aiSidePanel').style.display = 'none';
    document.getElementById('mainQuizCanvas').className = 'col-12';
}

// Drag and drop functionality
function dragStart(event) {
    event.dataTransfer.setData('text/plain', event.target.getAttribute('data-question-index'));
    event.target.classList.add('dragging');
}

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

// Add AI question to canvas
function addAIQuestionToCanvas(questionIndex) {
    const question = aiQuestions[questionIndex];
    if (!question) return;

    // Remove empty canvas message
    const emptyCanvas = document.getElementById('emptyCanvas');
    if (emptyCanvas) {
        emptyCanvas.remove();
    }

    const canvas = document.getElementById('quizCanvas');
    const questionHtml = createQuestionElement(question, ++questionCounter);
    canvas.insertAdjacentHTML('beforeend', questionHtml);
    
    // Initialize sortable if not already done
    initializeSortable();
}

// Add manual question
function addManualQuestion() {
    // Remove empty state and change canvas layout
    const canvas = document.getElementById('quizCanvas');
    const emptyState = document.getElementById('quizEmptyState');
    if (emptyState) {
        emptyState.remove();
        // Change canvas styling for question layout
        canvas.className = 'mb-3';
        canvas.style.cssText = 'min-height: auto; border: none; padding: 0;';
    }

    const question = {
        type: 'multiple_choice',
        question: '',
        options: ['Option A', 'Option B', 'Option C', 'Option D'],
        correct_answers: [0]
    };

    const questionId = `question_${Date.now()}_${++questionCounter}`;
    
    // Create inline question form directly in canvas
    const questionHtml = `
        <div class="quiz-question-item border rounded p-4 mb-3" data-question-id="${questionId}" data-question-type="${question.type}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">
                    <i class="bi bi-grip-vertical text-muted me-2"></i>
                    Question ${questionCounter}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion('${questionId}')">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Question Text *</label>
                <textarea class="form-control" rows="3" placeholder="Enter your question here..." onchange="updateQuestionData('${questionId}', 'question', this.value)"></textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Question Type</label>
                    <select class="form-select" onchange="changeQuestionType('${questionId}', this.value)">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Points</label>
                    <input type="number" class="form-control" value="1" min="1" onchange="updateQuestionData('${questionId}', 'points', this.value)">
                </div>
            </div>
            
            <div id="options_${questionId}">
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
            </div>
            
            <div class="mt-3">
                <label class="form-label">Explanation (Optional)</label>
                <textarea class="form-control" rows="2" placeholder="Provide an explanation for the correct answer..." onchange="updateQuestionData('${questionId}', 'explanation', this.value)"></textarea>
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
        question: '',
        type: 'multiple_choice',
        options: ['', '', '', ''],
        correct_answers: [0],
        points: 1,
        explanation: ''
    };
}

// Create question element HTML
function createQuestionElement(question, index) {
    const questionId = `question_${Date.now()}_${index}`;
    
    return `
        <div class="quiz-question-item" data-question-id="${questionId}" data-question-type="${question.type}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-grip-vertical text-muted me-2"></i>
                    <span class="badge question-type-badge bg-secondary me-2">${question.type.replace('_', ' ').toUpperCase()}</span>
                    <strong>Question ${index}</strong>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="editQuestion('${questionId}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion('${questionId}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            
            <div class="question-display">
                <div class="fw-bold mb-2">${question.question || 'Enter question text...'}</div>
                ${renderQuestionOptions(question, questionId)}
            </div>
            
            <div class="question-editor" style="display: none;">
                ${createQuestionEditor(question, questionId)}
            </div>
        </div>
    `;
}

// Render question options based on type
function renderQuestionOptions(question, questionId) {
    switch (question.type) {
        case 'multiple_choice':
            return `
                <div class="options-display">
                    ${question.options.map((opt, i) => `
                        <div class="option-item mb-1">
                            <span class="${question.correct_answer.includes(String.fromCharCode(65 + i)) ? 'text-success fw-bold' : ''}">${String.fromCharCode(65 + i)}. ${opt}</span>
                            ${question.correct_answer.includes(String.fromCharCode(65 + i)) ? '<i class="bi bi-check-circle text-success ms-1"></i>' : ''}
                        </div>
                    `).join('')}
                </div>
            `;
        case 'true_false':
            return `<div class="text-success fw-bold">Answer: ${question.correct_answer === 'A' ? 'True' : 'False'}</div>`;
        default:
            return `<div class="text-success fw-bold">Answer: ${question.correct_answer}</div>`;
    }
}

// Create question editor
function createQuestionEditor(question, questionId) {
    return `
        <div class="mb-3">
            <label class="form-label">Question Text</label>
            <textarea class="form-control" rows="3" onchange="updateQuestion('${questionId}', 'question', this.value)">${question.question}</textarea>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Question Type</label>
            <select class="form-select" onchange="changeQuestionType('${questionId}', this.value)">
                <option value="multiple_choice" ${question.type === 'multiple_choice' ? 'selected' : ''}>Multiple Choice</option>
                <option value="true_false" ${question.type === 'true_false' ? 'selected' : ''}>True/False</option>
                <option value="short_answer" ${question.type === 'short_answer' ? 'selected' : ''}>Short Answer</option>
            </select>
        </div>
        
        <div id="options_${questionId}">
            ${createOptionsEditor(question, questionId)}
        </div>
    `;
}

// Create options editor based on question type
function createOptionsEditor(question, questionId) {
    switch (question.type) {
        case 'multiple_choice':
            return `
                <label class="form-label">Options (check correct answers)</label>
                ${question.options.map((opt, i) => `
                    <div class="input-group mb-2">
                        <div class="input-group-text">
                            <input type="checkbox" ${question.correct_answer.includes(String.fromCharCode(65 + i)) ? 'checked' : ''} 
                                   onchange="toggleCorrectAnswer('${questionId}', '${String.fromCharCode(65 + i)}', this.checked)">
                        </div>
                        <span class="input-group-text">${String.fromCharCode(65 + i)}.</span>
                        <input type="text" class="form-control" value="${opt}" 
                               onchange="updateOption('${questionId}', ${i}, this.value)">
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
        default:
            return `
                <label class="form-label">Correct Answer</label>
                <input type="text" class="form-control" value="${question.correct_answer}" 
                       onchange="updateQuestion('${questionId}', 'correct_answer', this.value)">
            `;
    }
}

// Question editing functions
function editQuestion(questionId) {
    const questionItem = document.querySelector(`[data-question-id="${questionId}"]`);
    const display = questionItem.querySelector('.question-display');
    const editor = questionItem.querySelector('.question-editor');
    
    display.style.display = 'none';
    editor.style.display = 'block';
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
                <div id="quizEmptyState" class="text-center">
                    <i class="bi bi-inbox" style="font-size: 2.5rem; color: #bdbdbd;"></i>
                    <div class="mt-2 text-muted">No questions added yet. Click <b>Add Question</b> or use AI Generate to get started.</div>
                </div>
            `;
            canvas.className = 'mb-3 d-flex flex-column align-items-center justify-content-center';
            canvas.style.cssText = 'min-height: 200px; border: 1px dashed #ccc; padding: 10px;';
        }
    }
}

function updateQuestion(questionId, field, value) {
    // Update question data and refresh display
    const questionItem = document.querySelector(`[data-question-id="${questionId}"]`);
    // Implementation for updating question data
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

function updateCorrectAnswers(questionId) {
    const checkboxes = document.querySelectorAll(`input[name="correct_${questionId}"]:checked`);
    const correctAnswers = Array.from(checkboxes).map(cb => parseInt(cb.value));
    updateQuestionData(questionId, 'correct_answers', correctAnswers);
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

// Initialize sortable
function initializeSortable() {
    // Implementation using SortableJS or similar library
}

// Save quiz
async function saveQuiz() {
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
    const professorId = window.myId || (window.Professor && window.Professor.myId) || null;
    console.log('Professor ID for quiz save:', {
        window_myId: window.myId,
        window_Professor_myId: window.Professor ? window.Professor.myId : 'undefined',
        final_professor_id: professorId
    });
    
    const quizData = {
        title: title,
        description: document.getElementById('quiz_description').value.trim(),
        program_id: programId,
        module_id: document.getElementById('module_id').value || null,
        course_id: document.getElementById('course_id').value || null,
        professor_id: professorId,
        time_limit: parseInt(document.getElementById('time_limit').value) || 60,
        max_attempts: parseInt(document.getElementById('max_attempts').value) || 1,
        questions: questions,
        is_draft: true,
                        _token: window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    console.log('Quiz data being sent:', quizData);

    try {
        const url = currentQuizId ? 
            `/professor/quiz-generator/update-quiz/${currentQuizId}` : 
            '{{ route("professor.quiz-generator.save-manual") }}';
        
        const method = currentQuizId ? 'PUT' : 'POST';    
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(quizData)
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Quiz saved successfully!');
            // Close modal and reload page
            const modal = bootstrap.Modal.getInstance(document.getElementById('createQuizModal'));
            modal.hide();
            location.reload();
        } else {
            alert('Error saving quiz: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error saving quiz:', error);
        alert('Error saving quiz. Please try again.');
    }
}

// Program selection event listeners are now set up in DOMContentLoaded

async function loadModules(programId) {
    try {
        console.log('Fetching modules for program:', programId);
        const response = await fetch(`/professor/quiz-generator/modules/${programId}`);
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
        const response = await fetch(`/professor/quiz-generator/courses/${moduleId}`);
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

// Add question to canvas from AI panel
function addQuestionToCanvas(question) {
    // Remove empty state and change canvas layout
    const canvas = document.getElementById('quizCanvas');
    const emptyState = document.getElementById('quizEmptyState');
    if (emptyState) {
        emptyState.remove();
        // Change canvas styling for question layout
        canvas.className = 'mb-3';
        canvas.style.cssText = 'min-height: auto; border: none; padding: 0;';
    }

    const questionId = `question_${Date.now()}_${++questionCounter}`;
    
    // Create inline question form directly in canvas
    const questionHtml = `
        <div class="quiz-question-item border rounded p-4 mb-3" data-question-id="${questionId}" data-question-type="${question.type}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">
                    <i class="bi bi-grip-vertical text-muted me-2"></i>
                    Question ${questionCounter}
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

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize question counter
    window.questionCounter = 0;
    window.quizQuestions = {};
    window.aiQuestions = [];
    
    // Update question count display
    updateQuestionCount();
    
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

function updateQuestionCount() {
    const count = Object.keys(window.quizQuestions || {}).length;
    const questionCountElement = document.getElementById('questionCount');
    if (questionCountElement) {
        questionCountElement.textContent = count;
    }
}

// Quiz Status Management Functions
async function publishQuiz(quizId) {
    if (!confirm('Are you sure you want to publish this quiz? Students will be able to access it.')) {
        return;
    }
    
    try {
        const response = await fetch(`/professor/quiz-generator/${quizId}/publish`, {
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
        console.error('Error restoring quiz:', error);
        showAlert('danger', 'Error restoring quiz. Please try again.');
    }
}

    // Event listeners for quiz management
    document.addEventListener('DOMContentLoaded', function() {
        // Delete quiz buttons
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-quiz-btn') || e.target.closest('.delete-quiz-btn')) {
                e.preventDefault();
                const btn = e.target.classList.contains('delete-quiz-btn') ? e.target : e.target.closest('.delete-quiz-btn');
                const quizId = btn.getAttribute('data-quiz-id');
                deleteQuiz(quizId);
            }
            
            // View Questions Modal button
            if (e.target.classList.contains('view-questions-modal-btn') || e.target.closest('.view-questions-modal-btn')) {
                e.preventDefault();
                const btn = e.target.classList.contains('view-questions-modal-btn') ? e.target : e.target.closest('.view-questions-modal-btn');
                const quizId = btn.getAttribute('data-quiz-id');
                loadQuizQuestionsForModal(quizId);
            }
            
            // Preview Quiz button
            if (e.target.classList.contains('preview-quiz-btn') || e.target.closest('.preview-quiz-btn')) {
                e.preventDefault();
                const btn = e.target.classList.contains('preview-quiz-btn') ? e.target : e.target.closest('.preview-quiz-btn');
                const quizId = btn.getAttribute('data-quiz-id');
                previewQuiz(quizId);
            }
            
            // Edit Quiz button
            if (e.target.classList.contains('edit-quiz-btn') || e.target.closest('.edit-quiz-btn')) {
                e.preventDefault();
                const btn = e.target.classList.contains('edit-quiz-btn') ? e.target : e.target.closest('.edit-quiz-btn');
                const quizId = btn.getAttribute('data-quiz-id');
                editQuiz(quizId);
            }
        });
    });

    // Edit Quiz Questions
    async function loadQuizQuestionsForModal(quizId) {
        try {
            const response = await fetch(`/professor/quiz-generator/api/questions/${quizId}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            const questionsListEl = document.getElementById('questionsList');
            
            if (data.questions && data.questions.length > 0) {
                let html = `
                    <h4 class="mb-3">${data.quiz.title}</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Question</th>
                                    <th>Type</th>
                                    <th>Correct Answer</th>
                                    <th>Points</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.questions.forEach((question, index) => {
                    // Parse options if it's a JSON string
                    let options = question.options;
                    if (typeof options === 'string') {
                        try {
                            options = JSON.parse(options);
                        } catch (e) {
                            console.error('Error parsing options:', e);
                        }
                    }
                    
                    // Format the options display based on question type
                    let optionsDisplay = '';
                    if (question.question_type === 'multiple_choice') {
                        if (Array.isArray(options)) {
                            optionsDisplay = options.join(', ');
                        } else if (typeof options === 'object') {
                            optionsDisplay = Object.entries(options)
                                .map(([key, value]) => `${key}: ${value}`)
                                .join('<br>');
                        }
                    } else if (question.question_type === 'true_false') {
                        optionsDisplay = 'True / False';
                    }
                    
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${question.question_text}</td>
                            <td>${question.question_type === 'multiple_choice' ? 'Multiple Choice' : 'True/False'}</td>
                            <td>${question.correct_answer}</td>
                            <td>${question.points || 1}</td>
                        </tr>
                    `;
                    
                    // If there's an explanation, add it as a collapsible row
                    if (question.explanation) {
                        html += `
                            <tr class="explanation-row">
                                <td colspan="5" class="explanation-cell bg-light">
                                    <strong>Explanation:</strong> ${question.explanation}
                                </td>
                            </tr>
                        `;
                    }
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
                
                questionsListEl.innerHTML = html;
            } else {
                questionsListEl.innerHTML = '<div class="alert alert-info">No questions found for this quiz.</div>';
            }
            
            // Show the modal
            const viewQuestionsModal = new bootstrap.Modal(document.getElementById('viewQuestionsModal'));
            viewQuestionsModal.show();
            
        } catch (error) {
            console.error('Error loading quiz questions:', error);
            document.getElementById('questionsList').innerHTML = `
                <div class="alert alert-danger">
                    Error loading questions: ${error.message}. Please try again.
                </div>
            `;
            
            const viewQuestionsModal = new bootstrap.Modal(document.getElementById('viewQuestionsModal'));
            viewQuestionsModal.show();
        }
    }
    
    // This is kept for backward compatibility - some parts of the app may still reference it
    async function editQuizQuestions(quizId) {
        try {
            window.location.href = `/professor/quiz-generator/questions/${quizId}`;
        } catch (error) {
            console.error('Error navigating to edit questions:', error);
            showAlert('danger', 'Error opening quiz questions editor. Please try again.');
        }
    }

    // Preview Quiz
    async function previewQuiz(quizId) {
        try {
            window.open(`/professor/quiz-generator/preview/${quizId}`, '_blank');
        } catch (error) {
            console.error('Error opening quiz preview:', error);
            showAlert('danger', 'Error opening quiz preview. Please try again.');
        }
    }

    // Edit Quiz
    async function editQuiz(quizId) {
        try {
            window.location.href = `/professor/quiz-generator/edit/${quizId}`;
        } catch (error) {
            console.error('Error navigating to edit quiz:', error);
            showAlert('danger', 'Error opening quiz editor. Please try again.');
        }
    }

async function deleteQuiz(quizId) {
    if (!confirm('Are you sure you want to delete this quiz? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/professor/quiz-generator/${quizId}/delete`, {
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
</script>
@endpush
