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
                <small class="text-muted">Supported: PDF, Word, CSV, TXT. Max: 10MB.</small>
                <small class="text-info d-block mt-1">
                    <i class="bi bi-lightbulb"></i> For technical topics (Linux, PHP, JavaScript, etc.), you can skip the document - we'll use our technical question database!
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
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="randomize_order" name="randomize_order">
                    <label class="form-check-label" for="randomize_order">Randomize Order</label>
                </div>
            </div>
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
                    <div class="col-md-4">
                        <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                        <input type="number" class="form-control" id="time_limit" name="time_limit" value="60" min="1" max="300">
                    </div>
                    <div class="col-md-4">
                        <label for="max_attempts" class="form-label">Max Attempts (optional)</label>
                        <input type="number" class="form-control" id="max_attempts" name="max_attempts" min="1" max="10" placeholder="Unlimited">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allow_retakes" name="allow_retakes">
                            <label class="form-check-label" for="allow_retakes">Allow Retakes</label>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="instant_feedback" name="instant_feedback">
                            <label class="form-check-label" for="instant_feedback">Instant Feedback</label>
                            <small class="form-text text-muted d-block">Show correct/incorrect after each question</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show_correct_answers" name="show_correct_answers" checked>
                            <label class="form-check-label" for="show_correct_answers">Show Correct Answers</label>
                            <small class="form-text text-muted d-block">Display correct answers at end</small>
                        </div>
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
    <h4 class="mb-3">Your Draft & Published Quizzes</h4>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th>Tags</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quizzes as $quiz)
                <tr>
                    <td>{{ $quiz->quiz_title }}</td>
                    <td>{{ $quiz->program->program_name ?? '-' }}</td>
                    <td>
                        @if($quiz->is_draft)
                            <span class="badge bg-warning text-dark">Draft</span>
                        @else
                            <span class="badge bg-success">Published</span>
                        @endif
                    </td>
                    <td>
                        @if($quiz->tags && is_array($quiz->tags))
                            @foreach($quiz->tags as $tag)
                                <span class="badge bg-info text-dark">{{ $tag }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if($quiz->status === 'draft')
                            <span class="badge bg-warning text-dark">Draft</span>
                        @elseif($quiz->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Archived</span>
                        @endif
                        
                        @if($quiz->allow_retakes)
                            <span class="badge bg-info text-dark">Retakes Allowed</span>
                        @endif
                        
                        @if($quiz->instant_feedback)
                            <span class="badge bg-primary">Instant Feedback</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-outline-secondary btn-sm view-questions-btn" data-quiz-id="{{ $quiz->quiz_id }}">View/Edit Questions</button>
                        <button class="btn btn-outline-primary btn-sm preview-quiz-btn" data-quiz-id="{{ $quiz->quiz_id }}">Preview</button>
                        
                        @if($quiz->status === 'draft')
                            <button class="btn btn-success btn-sm publish-quiz-btn" data-quiz-id="{{ $quiz->quiz_id }}">Publish</button>
                        @elseif($quiz->status === 'published')
                            <button class="btn btn-warning btn-sm archive-quiz-btn" data-quiz-id="{{ $quiz->quiz_id }}">Archive</button>
                        @endif
                        
                        <button class="btn btn-danger btn-sm delete-quiz-btn" data-quiz-id="{{ $quiz->quiz_id }}">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No quizzes found.</td></tr>
                @endforelse
            </tbody>
        </table>
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
</script>
@endpush
