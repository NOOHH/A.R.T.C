@extends('student.student-dashboard.layouts.student-dashboard-layout')

@section('title', 'Take Quiz - ' . $quiz->quiz_title)

@push('styles')
<style>
    .quiz-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .quiz-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 30px;
        text-align: center;
    }
    
    .quiz-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }
    
    .question-card {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .question-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #2c3e50;
    }
    
    .option {
        margin-bottom: 10px;
    }
    
    .option input[type="radio"] {
        margin-right: 10px;
    }
    
    .option label {
        cursor: pointer;
        display: block;
        padding: 10px;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    
    .option label:hover {
        background-color: #f8f9fa;
    }
    
    .option input[type="radio"]:checked + label {
        background-color: #e3f2fd;
        border-color: #2196f3;
    }
    
    .timer {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #dc3545;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        z-index: 1000;
    }
    
    .submit-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 5px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
</style>
@endpush

@section('content')
<div class="quiz-container">
    <div class="quiz-header">
        <h1>{{ $quiz->quiz_title }}</h1>
        <p>{{ $quiz->instructions }}</p>
    </div>
    
    <div class="quiz-info">
        <div class="row">
            <div class="col-md-3">
                <strong>Questions:</strong> {{ $quiz->questions->count() }}
            </div>
            <div class="col-md-3">
                <strong>Time Limit:</strong> {{ $quiz->time_limit }} minutes
            </div>
            <div class="col-md-3">
                <strong>Difficulty:</strong> {{ ucfirst($quiz->difficulty) }}
            </div>
            <div class="col-md-3">
                <strong>Due Date:</strong> {{ $deadline->due_date->format('M d, Y') }}
            </div>
        </div>
    </div>
    
    <div class="timer" id="timer">
        <span id="time-remaining">{{ $quiz->time_limit }}:00</span>
    </div>
    
    <form id="quizForm" method="POST" action="{{ route('student.ai-quiz.submit', $quiz->quiz_id) }}">
        @csrf
        
        @foreach($quiz->questions as $index => $question)
            <div class="question-card">
                <div class="question-title">
                    {{ $index + 1 }}. {{ $question->question_text }}
                </div>
                
                <div class="options">
                    @if($question->question_type === 'multiple_choice')
                        @foreach($question->options as $optionIndex => $option)
                            <div class="option">
                                <input type="radio" 
                                       name="answers[{{ $question->quiz_id }}]" 
                                       value="{{ $option }}" 
                                       id="q{{ $question->quiz_id }}_{{ $optionIndex }}">
                                <label for="q{{ $question->quiz_id }}_{{ $optionIndex }}">
                                    {{ $option }}
                                </label>
                            </div>
                        @endforeach
                    @elseif($question->question_type === 'true_false')
                        <div class="option">
                            <input type="radio" 
                                   name="answers[{{ $question->quiz_id }}]" 
                                   value="true" 
                                   id="q{{ $question->quiz_id }}_true">
                            <label for="q{{ $question->quiz_id }}_true">True</label>
                        </div>
                        <div class="option">
                            <input type="radio" 
                                   name="answers[{{ $question->quiz_id }}]" 
                                   value="false" 
                                   id="q{{ $question->quiz_id }}_false">
                            <label for="q{{ $question->quiz_id }}_false">False</label>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
        
        <div class="text-center mt-4">
            <button type="submit" class="submit-btn" id="submitBtn">
                Submit Quiz
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeLimit = {{ $quiz->time_limit }}; // minutes
    const startTime = Date.now();
    const endTime = startTime + (timeLimit * 60 * 1000);
    
    const timerElement = document.getElementById('time-remaining');
    const submitBtn = document.getElementById('submitBtn');
    const quizForm = document.getElementById('quizForm');
    
    function updateTimer() {
        const now = Date.now();
        const remaining = Math.max(0, endTime - now);
        
        const minutes = Math.floor(remaining / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000);
        
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (remaining <= 0) {
            // Time's up! Auto-submit
            submitQuiz();
        }
    }
    
    function submitQuiz() {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        
        const formData = new FormData(quizForm);
        const timeTaken = Math.round((Date.now() - startTime) / 1000); // in seconds
        formData.append('time_taken', timeTaken);
        
        fetch(quizForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Quiz submitted successfully! Your score: ${data.score}/${data.total_questions}`);
                window.location.href = '{{ route("student.dashboard") }}';
            } else {
                alert('Error: ' + data.message);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Quiz';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the quiz.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Quiz';
        });
    }
    
    // Update timer every second
    setInterval(updateTimer, 1000);
    updateTimer();
    
    // Handle form submission
    quizForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (confirm('Are you sure you want to submit the quiz? This action cannot be undone.')) {
            submitQuiz();
        }
    });
    
    // Prevent page refresh/back button
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = '';
    });
});
</script>
@endpush
