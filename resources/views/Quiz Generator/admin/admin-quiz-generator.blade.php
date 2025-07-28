@extends('admin.admin-dashboard-layout')

@section('title', 'AI Quiz Generator')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
    .quiz-generator-container {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin: 20px 0;
    }

    .quiz-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .quiz-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .quiz-header p {
        font-size: 1.1rem;
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    .gemini-badge {
        background: linear-gradient(45deg, #4285f4, #34a853, #fbbc05, #ea4335);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-block;
        margin-top: 0.5rem;
    }

    .form-container {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        outline: none;
    }

    .btn-generate {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-generate:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-upload-area:hover {
        border-color: #667eea;
        background-color: #f8f9ff;
    }

    .file-upload-area.dragover {
        border-color: #667eea;
        background-color: #f8f9ff;
    }

    .quiz-result {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 2rem;
        border: 1px solid #dee2e6;
    }

    .question-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .question-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .option-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #eee;
    }

    .option-item:last-child {
        border-bottom: none;
    }

    .correct-answer {
        background-color: #d4edda;
        color: #155724;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    @media (max-width: 768px) {
        .quiz-header h1 {
            font-size: 1.8rem;
        }
        
        .form-container {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="quiz-generator-container">
    <!-- Header -->
    <div class="quiz-header">
        <h1><i class="bi bi-robot"></i> AI Quiz Generator</h1>
        <p>Generate intelligent quizzes from your documents using Google Gemini AI</p>
        <div class="gemini-badge">Powered by Google Gemini</div>
    </div>

    <div class="form-container">
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
        <form method="POST" action="{{ route('admin.quiz-generator.generate') }}" enctype="multipart/form-data" id="adminQuizForm">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="program_id" class="form-label">Select Program <span class="text-danger">*</span></label>
                        <select name="program_id" id="program_id" class="form-select" required>
                            <option value="">Choose a program...</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="batch_id" class="form-label">Select Batch <span class="text-danger">*</span></label>
                        <select name="batch_id" id="batch_id" class="form-select" required disabled>
                            <option value="">Choose a batch...</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="document" class="form-label">Upload Document <span class="text-danger">*</span></label>
                <div class="file-upload-area" onclick="document.getElementById('document').click()">
                    <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #667eea;"></i>
                    <p class="mt-2 mb-0">Click to upload or drag and drop your document</p>
                    <small class="text-muted">Supported formats: PDF, Word (.doc, .docx), CSV, Text files. Maximum size: 10MB</small>
                    <input type="file" class="form-control" id="document" name="document" 
                           accept=".pdf,.doc,.docx,.csv,.txt" required style="display: none;">
                </div>
                <div id="fileName" class="mt-2 text-muted" style="display: none;"></div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="num_questions" class="form-label">Number of Questions</label>
                        <select name="num_questions" id="num_questions" class="form-select" required>
                            <option value="5">5 Questions</option>
                            <option value="10" selected>10 Questions</option>
                            <option value="15">15 Questions</option>
                            <option value="20">20 Questions</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="difficulty" class="form-label">Difficulty Level</label>
                        <select name="difficulty" id="difficulty" class="form-select" required>
                            <option value="easy">Easy</option>
                            <option value="medium" selected>Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="quiz_type" class="form-label">Question Type</label>
                        <select name="quiz_type" id="quiz_type" class="form-select" required>
                            <option value="multiple_choice" selected>Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="quiz_title" class="form-label">Quiz Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="quiz_title" name="quiz_title" 
                       placeholder="Enter a title for this quiz" required>
            </div>

            <div class="form-group">
                <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                <input type="number" class="form-control" id="time_limit" name="time_limit" 
                       min="1" max="180" value="30" placeholder="30">
            </div>

            <div class="form-group">
                <label for="quiz_description" class="form-label">Quiz Description</label>
                <textarea class="form-control" id="quiz_description" name="quiz_description" 
                          rows="3" placeholder="Enter a description for this quiz"></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-generate" id="generateBtn">
                    <i class="bi bi-robot"></i> Generate Quiz
                </button>
            </div>
        </form>
    </div>
</div>

@if(isset($quiz) && $quiz)
<div class="quiz-generator-container">
    <div class="quiz-header">
        <h2><i class="bi bi-check-circle"></i> Quiz Generated Successfully!</h2>
        <p>{{ $quiz->quiz_title }}</p>
    </div>

    <div class="form-container">
        <div class="quiz-result">
            <h4>Quiz Details</h4>
            <p><strong>Title:</strong> {{ $quiz->quiz_title }}</p>
            <p><strong>Description:</strong> {{ $quiz->quiz_description ?? 'No description provided' }}</p>
            <p><strong>Number of Questions:</strong> {{ count($quiz->questions) }}</p>
            <p><strong>Time Limit:</strong> {{ $quiz->time_limit }} minutes</p>
            <p><strong>Difficulty:</strong> {{ ucfirst($quiz->difficulty) }}</p>
            <p><strong>Question Type:</strong> {{ str_replace('_', ' ', ucfirst($quiz->quiz_type)) }}</p>
            
            <div class="mt-4">
                <h5>Questions Preview:</h5>
                @foreach($quiz->questions as $index => $question)
                    <div class="question-card">
                        <div class="question-title">
                            {{ $index + 1 }}. {{ $question->question_text }}
                        </div>
                        
                        @if($question->question_type === 'multiple_choice')
                            <div class="options">
                                @foreach($question->options as $option)
                                    <div class="option-item">
                                        <span class="{{ $option === $question->correct_answer ? 'correct-answer' : '' }}">
                                            {{ $option }}
                                            @if($option === $question->correct_answer)
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question->question_type === 'true_false')
                            <div class="options">
                                <div class="option-item">
                                    <span class="{{ $question->correct_answer === 'true' ? 'correct-answer' : '' }}">
                                        True
                                        @if($question->correct_answer === 'true')
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @endif
                                    </span>
                                </div>
                                <div class="option-item">
                                    <span class="{{ $question->correct_answer === 'false' ? 'correct-answer' : '' }}">
                                        False
                                        @if($question->correct_answer === 'false')
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4 text-center">
                <a href="{{ route('admin.modules.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Back to Modules
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Program and batch selection
    const programSelect = document.getElementById('program_id');
    const batchSelect = document.getElementById('batch_id');
    const documentInput = document.getElementById('document');
    const fileNameDiv = document.getElementById('fileName');
    const generateBtn = document.getElementById('generateBtn');
    const quizForm = document.getElementById('adminQuizForm');

    // Define the batch URL template
    const batchUrlTemplate = '{{ url("/admin/programs/:PID/batches") }}';

    // Handle program selection
    programSelect.addEventListener('change', function() {
        const pid = this.value;
        batchSelect.innerHTML = '<option value="">Choose a batch…</option>';
        batchSelect.disabled = true;

        if (!pid) {
            return;
        }

        const url = batchUrlTemplate.replace(':PID', pid);
        console.log('Fetching batches from:', url);

        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (data.success && data.batches) {
                    data.batches.forEach(batch => {
                        batchSelect.add(new Option(batch.batch_name, batch.id));
                    });
                    batchSelect.disabled = false;
                } else {
                    console.error('Error in response:', data.message || 'Unknown error');
                }
            })
            .catch(err => {
                console.error('Error fetching batches:', err);
                batchSelect.disabled = false;
            });
    });
  

    // Handle file upload
    documentInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            fileNameDiv.textContent = `Selected: ${file.name}`;
            fileNameDiv.style.display = 'block';
        } else {
            fileNameDiv.style.display = 'none';
        }
    });

    // Handle drag and drop
    const fileUploadArea = document.querySelector('.file-upload-area');
    
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            documentInput.files = files;
            const file = files[0];
            fileNameDiv.textContent = `Selected: ${file.name}`;
            fileNameDiv.style.display = 'block';
        }
    });

    // Handle form submission
    quizForm.addEventListener('submit', function(e) {
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generating Quiz...';
    });
});
</script>
@endpush
