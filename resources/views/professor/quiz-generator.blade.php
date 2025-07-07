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
                        
                        <div class="mb-3">
                            <label for="program_id" class="form-label">Select Program</label>
                            <select name="program_id" id="program_id" class="form-select" required>
                                <option value="">Choose a program...</option>
                                @foreach($assignedPrograms as $program)
                                    <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                                @endforeach
                            </select>
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
                            <div class="col-md-4">
                                <label for="num_questions" class="form-label">Number of Questions</label>
                                <select name="num_questions" id="num_questions" class="form-select" required>
                                    <option value="5">5 Questions</option>
                                    <option value="10" selected>10 Questions</option>
                                    <option value="15">15 Questions</option>
                                    <option value="20">20 Questions</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="difficulty" class="form-label">Difficulty Level</label>
                                <select name="difficulty" id="difficulty" class="form-select" required>
                                    <option value="easy">Easy</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                            <div class="col-md-4">
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

<script>
document.getElementById('quizForm').addEventListener('submit', function() {
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    document.getElementById('generateBtn').disabled = true;
});

function previewQuiz(quizId) {
    fetch(`{{ route('professor.quiz-generator.preview', ':id') }}`.replace(':id', quizId))
        .then(response => response.json())
        .then(data => {
            document.getElementById('previewContent').innerHTML = data.html;
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        })
        .catch(error => {
            alert('Error loading quiz preview');
        });
}

function exportQuiz(quizId) {
    window.open(`{{ route('professor.quiz-generator.export', ':id') }}`.replace(':id', quizId), '_blank');
}
</script>
@endsection
