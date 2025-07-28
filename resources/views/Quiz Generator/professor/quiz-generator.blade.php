@extends('professor.layout')
@section('content')
<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-robot"></i> AI Quiz Generator</h2>
    
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
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <!-- Debug Info (will be populated by JavaScript on errors) -->
    <div id="debug-alert" class="alert alert-info" style="display: none;">
        <h6>Debug Information:</h6>
        <div id="debug-content"></div>
    </div>
    
    <form id="quiz-generator-form" enctype="multipart/form-data" method="POST" action="{{ route('professor.quiz-generator.generate') }}">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label for="program_id" class="form-label">Program</label>
                <select id="program_id" name="program_id" class="form-select" required>
                    <option value="">Select Program</option>
                    @foreach($assignedPrograms as $program)
                        <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="module_id" class="form-label">Module</label>
                <select id="module_id" name="module_id" class="form-select" required disabled>
                    <option value="">Select Module</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="course_id" class="form-label">Course</label>
                <select id="course_id" name="course_id" class="form-select" required disabled>
                    <option value="">Select Course</option>
                </select>
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label for="document" class="form-label">Upload Document</label>
                <input type="file" class="form-control" id="document" name="document" accept=".pdf,.doc,.docx,.csv,.txt">
                <small class="text-muted">Supported: PDF, Word, CSV, TXT. Max: 50MB.</small>
                <small class="text-info d-block mt-1">
                    <i class="bi bi-lightbulb"></i> For technical topics (Linux, PHP, JavaScript, etc.), you can skip the document - we'll use our technical question database!
                </small>
                <small class="text-success d-block mt-1">
                    <i class="bi bi-magic"></i> Enhanced with Tesseract OCR for scanned/image-based PDFs!
                </small>
            </div>
            <div class="col-md-2">
                <label for="num_questions" class="form-label"># Questions</label>
                <input type="number" class="form-control" id="num_questions" name="num_questions" min="5" max="50" value="10" required>
            </div>
            <div class="col-md-3">
                <label for="quiz_type" class="form-label">Question Type</label>
                <select id="quiz_type" name="quiz_type" class="form-select" required>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                    <option value="flashcard">Flashcard</option>
                    <option value="mixed">Mixed (MCQ & T/F)</option>
                </select>
            </div>
            <div class="col-md-3"></div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="quiz_title" class="form-label">Quiz Title</label>
                <input type="text" class="form-control" id="quiz_title" name="quiz_title" required>
            </div>
            <div class="col-md-6">
                <label for="tags" class="form-label">Tags <small class="text-muted">(comma separated)</small></label>
                <input type="text" class="form-control" id="tags" name="tags" placeholder="e.g. algebra, calculus, midterm">
            </div>
        </div>
        <div class="mb-3">
            <label for="quiz_description" class="form-label">Quiz Description (Optional)</label>
            <textarea class="form-control" id="quiz_description" name="quiz_description" rows="2" placeholder="Brief description of the quiz content..."></textarea>
        </div>
        <div class="mb-3">
            <label for="instructions" class="form-label">Instructions (Optional)</label>
            <textarea class="form-control" id="instructions" name="instructions" rows="2" placeholder="Any special instructions for students..."></textarea>
        </div>
        
        <!-- Quiz Settings -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-gear"></i> Quiz Settings</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                        <input type="number" class="form-control" id="time_limit" name="time_limit" value="60" min="1" max="300">
                    </div>
                    <div class="col-md-3">
                        <label for="max_attempts" class="form-label">Max Attempts</label>
                        <input type="number" class="form-control" id="max_attempts" name="max_attempts" min="1" max="10" value="1">
                    </div>
                    <div class="col-md-3">
                        <label for="quiz_format" class="form-label">Question Format</label>
                        <select id="quiz_format" name="quiz_format" class="form-select">
                            <option value="comprehensive">Comprehensive (7 Types)</option>
                            <option value="multiple_choice">Multiple Choice Only</option>
                            <option value="true_false">True/False Only</option>
                            <option value="mixed">Mixed Traditional</option>
                        </select>
                        <small class="text-muted">Comprehensive includes: Multiple Choice, True/False, Fill-in-Blank, Short Answer, Acronym Expansion, Definition Recall, and Matching</small>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="allow_retakes" name="allow_retakes">
                            <label class="form-check-label" for="allow_retakes">Allow Retakes</label>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label for="topic_focus" class="form-label">Topic Focus (Optional)</label>
                        <input type="text" class="form-control" id="topic_focus" name="topic_focus" placeholder="e.g., threat intelligence, cybersecurity frameworks, malware analysis">
                        <small class="text-muted">Specify particular topics to emphasize, or leave blank for general coverage</small>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="instant_feedback" name="instant_feedback">
                            <label class="form-check-label" for="instant_feedback">Instant Feedback</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="show_correct_answers" name="show_correct_answers" checked>
                            <label class="form-check-label" for="show_correct_answers">Show Correct Answers</label>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="randomize_order" name="randomize_order">
                            <label class="form-check-label" for="randomize_order">Randomize Question Order</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="randomize_mc_options" name="randomize_mc_options">
                            <label class="form-check-label" for="randomize_mc_options">Randomize Multiple Choice Options</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Additional space for future settings -->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-magic"></i> Generate Quiz (Draft)</button>
        </div>
    </form>

    <!-- QuizAPI Information Section -->
    <div class="alert alert-info mt-3">
        <h6><i class="bi bi-database"></i> Technical Quiz Database Available</h6>
        <p class="mb-2">For technical topics, we can generate questions from our extensive database covering:</p>
        <div class="row">
            <div class="col-md-4">
                <strong>Programming:</strong><br>
                <small>PHP, JavaScript, Python, SQL, etc.</small>
            </div>
            <div class="col-md-4">
                <strong>DevOps & Cloud:</strong><br>
                <small>Docker, Kubernetes, Linux, Cloud</small>
            </div>
            <div class="col-md-4">
                <strong>Security & Networking:</strong><br>
                <small>Cybersecurity, Networking, DevOps</small>
            </div>
        </div>
        <small class="text-muted">Simply include the topic name in your quiz title (e.g., "PHP Programming Quiz", "Linux Administration Test") and you can skip uploading a document!</small>
    </div>

    <hr class="my-4">
    <h4 class="mb-3">Quiz Management</h4>
    
    <!-- Status Filter Tabs -->
    <ul class="nav nav-tabs mb-3" id="quizTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="draft-tab" data-bs-toggle="tab" data-bs-target="#draft-pane" type="button" role="tab">
                <i class="bi bi-file-earmark-text"></i> Draft Quizzes 
                <span class="badge bg-warning text-dark ms-1">{{ $quizzes->where('status', 'draft')->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="published-tab" data-bs-toggle="tab" data-bs-target="#published-pane" type="button" role="tab">
                <i class="bi bi-check-circle"></i> Published Quizzes 
                <span class="badge bg-success ms-1">{{ $quizzes->where('status', 'published')->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archived-pane" type="button" role="tab">
                <i class="bi bi-archive"></i> Archived Quizzes 
                <span class="badge bg-secondary ms-1">{{ $quizzes->where('status', 'archived')->count() }}</span>
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="quizTabsContent">
        <!-- Draft Quizzes -->
        <div class="tab-pane fade show active" id="draft-pane" role="tabpanel">
            <div class="card">
                <div class="card-header bg-warning bg-opacity-10">
                    <h6 class="mb-0"><i class="bi bi-file-earmark-text"></i> Draft Quizzes</h6>
                </div>
                <div class="card-body">
                    @include('Quiz Generator.professor.quiz-table', ['quizzes' => $quizzes->where('status', 'draft'), 'status' => 'draft'])
                </div>
            </div>
        </div>

        <!-- Published Quizzes -->
        <div class="tab-pane fade" id="published-pane" role="tabpanel">
            <div class="card">
                <div class="card-header bg-success bg-opacity-10">
                    <h6 class="mb-0"><i class="bi bi-check-circle"></i> Published Quizzes</h6>
                </div>
                <div class="card-body">
                    @include('Quiz Generator.professor.quiz-table', ['quizzes' => $quizzes->where('status', 'published'), 'status' => 'published'])
                </div>
            </div>
        </div>

        <!-- Archived Quizzes -->
        <div class="tab-pane fade" id="archived-pane" role="tabpanel">
            <div class="card">
                <div class="card-header bg-secondary bg-opacity-10">
                    <h6 class="mb-0"><i class="bi bi-archive"></i> Archived Quizzes</h6>
                </div>
                <div class="card-body">
                    @include('Quiz Generator.professor.quiz-table', ['quizzes' => $quizzes->where('status', 'archived'), 'status' => 'archived'])
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing/Editing Questions -->
    <div class="modal fade" id="questionsModal" tabindex="-1" aria-labelledby="questionsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="questionsModalLabel">Quiz Questions</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="questions-modal-body">
            <!-- Questions will be loaded here via AJAX -->
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for Previewing Quiz as Student -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="previewModalLabel">Quiz Preview (Student View)</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="preview-modal-body">
            <!-- Preview will be loaded here via AJAX -->
          </div>
        </div>
      </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/quiz-editor-simple.js') }}"></script>
<script>
// Additional CSRF token setup for AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Quiz status management functions
function publishQuiz(quizId) {
    if (confirm('Are you sure you want to publish this quiz? It will be available to students.')) {
        $.post('/professor/quiz-generator/publish', {
            quiz_id: quizId,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        })
        .fail(function() {
            alert('Error publishing quiz. Please try again.');
        });
    }
}

function archiveQuiz(quizId) {
    if (confirm('Are you sure you want to archive this quiz? It will no longer be available to students.')) {
        $.post('/professor/quiz-generator/archive', {
            quiz_id: quizId,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        })
        .fail(function() {
            alert('Error archiving quiz. Please try again.');
        });
    }
}

function restoreQuiz(quizId) {
    if (confirm('Are you sure you want to restore this quiz to draft status?')) {
        $.post('/professor/quiz-generator/restore', {
            quiz_id: quizId,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        })
        .fail(function() {
            alert('Error restoring quiz. Please try again.');
        });
    }
}
</script>
@endpush
