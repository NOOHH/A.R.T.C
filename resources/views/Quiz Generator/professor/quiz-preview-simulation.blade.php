@extends('professor.professor-layouts.professor-layout')

@section('title', 'Quiz Preview - ' . $quiz->quiz_title)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-play-circle"></i> Quiz Preview: {{ $quiz->quiz_title }}</h2>
        <button type="button" class="btn btn-secondary" onclick="closePreview()">
            <i class="bi bi-x-circle"></i> Close Preview
        </button>
    </div>

<div class="quiz-simulation">
    <div class="quiz-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>{{ $quiz->quiz_title }}</h4>
                <div class="quiz-meta">
                    <span class="badge bg-info">{{ $quiz->total_questions }} Questions</span>
                    @if($quiz->time_limit)
                        <span class="badge bg-warning">{{ $quiz->time_limit }} Minutes</span>
                    @endif
                </div>
            </div>
            <div class="quiz-controls">
                <button type="button" class="btn btn-outline-secondary" onclick="resetQuizSimulation()">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </button>
                <div class="alert alert-info d-inline-block mb-0 ms-2" style="padding: 0.375rem 0.75rem;">
                    <i class="bi bi-info-circle"></i> Preview Mode - Submission Disabled
                </div>
            </div>
        </div>
        
        @if($quiz->instructions)
            <div class="mt-3 p-3 bg-light rounded">
                <strong>Instructions:</strong>
                <p class="mb-0 mt-1">{{ $quiz->instructions }}</p>
            </div>
        @endif
        
        <!-- Quiz Progress -->
        <div class="quiz-progress mt-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span>Progress: <span id="current-question">1</span> of {{ $quiz->questions->count() }}</span>
                @if($quiz->time_limit)
                    <span>Time Remaining: <span id="time-remaining" class="text-warning fw-bold">{{ $quiz->time_limit }}:00</span></span>
                @endif
            </div>
            <div class="progress">
                <div id="quiz-progress-bar" class="progress-bar" role="progressbar" style="width: {{ $quiz->questions->count() > 0 ? (100 / $quiz->questions->count()) : 0 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Questions Container -->
    <div id="questions-container">
        @foreach($quiz->questions as $index => $question)
            <div class="question-card {{ $index === 0 ? 'active' : 'd-none' }}" data-question-index="{{ $index }}" data-question-id="{{ $question->id }}">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Question {{ $index + 1 }} of {{ $quiz->questions->count() }}</h6>
                        <small>{{ $question->points ?? 1 }} point(s)</small>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $question->question_text }}</h5>
                        
                        <div class="question-options mt-4">
                            @if($question->question_type === 'multiple_choice')
                                @php $options = is_string($question->options) ? json_decode($question->options, true) : $question->options @endphp
                                @if($options && is_array($options))
                                    @foreach($options as $key => $option)
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="question_{{ $question->id }}" 
                                                   value="{{ $key }}" id="q{{ $question->id }}_{{ $key }}">
                                            <label class="form-check-label" for="q{{ $question->id }}_{{ $key }}">
                                                <strong>{{ $key }}.</strong> {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                                
                            @elseif($question->question_type === 'true_false')
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="question_{{ $question->id }}" 
                                           value="A" id="q{{ $question->id }}_true">
                                    <label class="form-check-label" for="q{{ $question->id }}_true">
                                        <strong>True</strong>
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="question_{{ $question->id }}" 
                                           value="B" id="q{{ $question->id }}_false">
                                    <label class="form-check-label" for="q{{ $question->id }}_false">
                                        <strong>False</strong>
                                    </label>
                                </div>
                                
                            @elseif($question->question_type === 'short_answer')
                                <div class="mb-3">
                                    <textarea class="form-control" name="question_{{ $question->id }}" 
                                              rows="3" placeholder="Enter your answer here..."></textarea>
                                </div>
                                
                            @elseif($question->question_type === 'essay')
                                <div class="mb-3">
                                    <textarea class="form-control" name="question_{{ $question->id }}" 
                                              rows="8" placeholder="Write your essay answer here..."></textarea>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Answer feedback (initially hidden) -->
                        <div class="answer-feedback d-none mt-4" id="feedback_{{ $question->id }}">
                            <div class="alert" id="feedback-alert_{{ $question->id }}">
                                <div class="d-flex align-items-center">
                                    <i class="feedback-icon me-2"></i>
                                    <div>
                                        <strong class="feedback-result"></strong>
                                        <div class="feedback-correct-answer mt-1"></div>
                                        @if($question->explanation)
                                            <div class="feedback-explanation mt-2">
                                                <strong>Explanation:</strong> {{ $question->explanation }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" 
                                    onclick="previousQuestion()" {{ $index === 0 ? 'disabled' : '' }}>
                                <i class="bi bi-chevron-left"></i> Previous
                            </button>
                            
                            @if($quiz->instant_feedback)
                                <button type="button" class="btn btn-primary" onclick="checkAnswer({{ $question->id }})">
                                    Check Answer
                                </button>
                            @endif
                            
                            <button type="button" class="btn btn-primary" 
                                    onclick="nextQuestion()" {{ $index === $quiz->questions->count() - 1 ? 'disabled' : '' }}>
                                Next <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Quiz Summary (initially hidden) -->
    <div id="quiz-summary" class="d-none">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-check-circle"></i> Quiz Completed!</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Your Results:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Score:</strong> <span id="final-score"></span></li>
                            <li><strong>Correct Answers:</strong> <span id="correct-count"></span> / {{ $quiz->questions->count() }}</li>
                            <li><strong>Percentage:</strong> <span id="percentage"></span>%</li>
                            <li><strong>Time Taken:</strong> <span id="time-taken"></span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Question Breakdown:</h6>
                        <div id="question-breakdown"></div>
                    </div>
                </div>
                
                @if($quiz->show_correct_answers)
                    <div class="mt-4">
                        <button type="button" class="btn btn-info" onclick="showCorrectAnswers()">
                            <i class="bi bi-eye"></i> Show Correct Answers
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Quiz simulation variables
let currentQuestionIndex = 0;
let totalQuestions = {{ $quiz->questions->count() }};
let userAnswers = {};
let quizStartTime = new Date();
let timeLimit = {{ $quiz->time_limit ?? 0 }};
let timerInterval;
let isQuizCompleted = false;

// Quiz data for checking answers
const quizData = {
    @foreach($quiz->questions as $question)
        {{ $question->id }}: {
            type: '{{ $question->question_type }}',
            correct_answer: '{{ $question->correct_answer }}',
            options: @json(is_string($question->options) ? json_decode($question->options, true) : $question->options),
            explanation: '{{ $question->explanation }}',
            points: {{ $question->points ?? 1 }}
        },
    @endforeach
};

$(document).ready(function() {
    // Start timer if time limit is set
    if (timeLimit > 0) {
        startTimer();
    }
    
    // Initialize question navigation
    updateProgressBar();
    updateNavigationButtons();
});

function startTimer() {
    let remainingTime = timeLimit * 60; // Convert to seconds
    
    timerInterval = setInterval(function() {
        remainingTime--;
        
        const minutes = Math.floor(remainingTime / 60);
        const seconds = remainingTime % 60;
        
        $('#time-remaining').text(`${minutes}:${seconds.toString().padStart(2, '0')}`);
        
        // Change color when time is running out
        if (remainingTime <= 300) { // 5 minutes
            $('#time-remaining').removeClass('text-warning').addClass('text-danger');
        }
        
        if (remainingTime <= 0) {
            clearInterval(timerInterval);
            alert('Time is up! The quiz will be submitted automatically.');
            submitQuizSimulation();
        }
    }, 1000);
}

function nextQuestion() {
    // Save current answer
    saveCurrentAnswer();
    
    if (currentQuestionIndex < totalQuestions - 1) {
        // Hide current question
        $(`.question-card[data-question-index="${currentQuestionIndex}"]`).addClass('d-none').removeClass('active');
        
        // Show next question
        currentQuestionIndex++;
        $(`.question-card[data-question-index="${currentQuestionIndex}"]`).removeClass('d-none').addClass('active');
        
        updateProgressBar();
        updateNavigationButtons();
        $('#current-question').text(currentQuestionIndex + 1);
    }
}

function previousQuestion() {
    // Save current answer
    saveCurrentAnswer();
    
    if (currentQuestionIndex > 0) {
        // Hide current question
        $(`.question-card[data-question-index="${currentQuestionIndex}"]`).addClass('d-none').removeClass('active');
        
        // Show previous question
        currentQuestionIndex--;
        $(`.question-card[data-question-index="${currentQuestionIndex}"]`).removeClass('d-none').addClass('active');
        
        updateProgressBar();
        updateNavigationButtons();
        $('#current-question').text(currentQuestionIndex + 1);
    }
}

function saveCurrentAnswer() {
    const currentQuestionEl = $(`.question-card[data-question-index="${currentQuestionIndex}"]`);
    const questionId = currentQuestionEl.data('question-id');
    const questionType = quizData[questionId].type;
    
    if (questionType === 'multiple_choice' || questionType === 'true_false') {
        const selectedRadio = currentQuestionEl.find(`input[name="question_${questionId}"]:checked`);
        if (selectedRadio.length > 0) {
            userAnswers[questionId] = selectedRadio.val();
        }
    } else if (questionType === 'short_answer' || questionType === 'essay') {
        const textAnswer = currentQuestionEl.find(`textarea[name="question_${questionId}"]`).val();
        if (textAnswer.trim()) {
            userAnswers[questionId] = textAnswer.trim();
        }
    }
}

function updateProgressBar() {
    const progress = ((currentQuestionIndex + 1) / totalQuestions) * 100;
    $('#quiz-progress-bar').css('width', progress + '%');
}

function updateNavigationButtons() {
    // Update Previous button
    $('.question-card.active .card-footer button:first-child').prop('disabled', currentQuestionIndex === 0);
    
    // Update Next button
    $('.question-card.active .card-footer button:last-child').prop('disabled', currentQuestionIndex === totalQuestions - 1);
}

function checkAnswer(questionId) {
    saveCurrentAnswer();
    
    const userAnswer = userAnswers[questionId];
    const correctAnswer = quizData[questionId].correct_answer;
    const questionType = quizData[questionId].type;
    
    let isCorrect = false;
    let feedbackText = '';
    
    if (questionType === 'multiple_choice' || questionType === 'true_false') {
        isCorrect = userAnswer === correctAnswer;
        feedbackText = isCorrect ? 'Correct!' : 'Incorrect';
    } else {
        // For text answers, we'll just show that it was submitted
        feedbackText = 'Answer submitted for review';
        isCorrect = true; // Show as neutral for text answers
    }
    
    // Show feedback
    const feedbackEl = $(`#feedback_${questionId}`);
    const alertEl = $(`#feedback-alert_${questionId}`);
    
    alertEl.removeClass('alert-success alert-danger alert-info')
           .addClass(isCorrect ? 'alert-success' : 'alert-danger');
    
    feedbackEl.find('.feedback-icon').removeClass('bi-check-circle bi-x-circle')
              .addClass(isCorrect ? 'bi-check-circle' : 'bi-x-circle');
    
    feedbackEl.find('.feedback-result').text(feedbackText);
    
    if (questionType === 'multiple_choice' || questionType === 'true_false') {
        const correctAnswerText = quizData[questionId].options[correctAnswer] || correctAnswer;
        feedbackEl.find('.feedback-correct-answer').html(`<strong>Correct answer:</strong> ${correctAnswer}. ${correctAnswerText}`);
    }
    
    feedbackEl.removeClass('d-none');
}

function submitQuizSimulation() {
    // Disable submission for professors in preview mode
    alert('This is a preview mode. Quiz submission is disabled for professors.\n\nStudents will be able to submit their answers when taking the actual quiz.');
    return false;
}

function resetQuizSimulation() {
    if (confirm('Are you sure you want to reset the quiz? All answers will be lost.')) {
        // Reset variables
        currentQuestionIndex = 0;
        userAnswers = {};
        quizStartTime = new Date();
        isQuizCompleted = false;
        
        // Reset UI
        $('.question-card').addClass('d-none').removeClass('active');
        $(`.question-card[data-question-index="0"]`).removeClass('d-none').addClass('active');
        
        // Clear all answers
        $('input[type="radio"]').prop('checked', false);
        $('textarea').val('');
        
        // Hide all feedback
        $('.answer-feedback').addClass('d-none');
        
        // Reset progress
        updateProgressBar();
        updateNavigationButtons();
        $('#current-question').text('1');
        
        // Hide summary, show questions
        $('#quiz-summary').addClass('d-none');
        $('#questions-container').removeClass('d-none');
        
        // Restart timer
        if (timerInterval) {
            clearInterval(timerInterval);
        }
        if (timeLimit > 0) {
            $('#time-remaining').removeClass('text-danger').addClass('text-warning');
            startTimer();
        }
    }
}

function showCorrectAnswers() {
    // Show all correct answers in the summary
    let correctAnswersHtml = '<div class="mt-4"><h6>Correct Answers:</h6>';
    
    Object.keys(quizData).forEach((questionId, index) => {
        const questionData = quizData[questionId];
        const userAnswer = userAnswers[questionId] || 'No answer';
        const isCorrect = userAnswer === questionData.correct_answer;
        
        correctAnswersHtml += `
            <div class="card mb-2">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <strong>Question ${index + 1}:</strong>
                        <span class="${isCorrect ? 'text-success' : 'text-danger'}">${isCorrect ? '✅' : '❌'}</span>
                    </div>
                    <small class="text-muted">Your answer: ${userAnswer}</small><br>
                    <small class="text-success">Correct: ${questionData.correct_answer}</small>
                </div>
            </div>
        `;
    });
    
    correctAnswersHtml += '</div>';
    $('#quiz-summary .card-body').append(correctAnswersHtml);
    
    // Hide the button
    $('button[onclick="showCorrectAnswers()"]').hide();
}

function closePreview() {
    // Try to close the window if it was opened in a popup
    if (window.opener) {
        window.close();
    } else {
        // If not a popup, redirect back to quiz management
        window.location.href = '/professor/quiz-generator';
    }
}
</script>

<style>
.quiz-simulation .question-card {
    transition: all 0.3s ease;
}

.quiz-simulation .form-check-input:checked + .form-check-label {
    background-color: #e3f2fd;
    border-radius: 4px;
    padding: 8px;
}

.quiz-simulation .answer-feedback {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.quiz-simulation .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.quiz-simulation .quiz-progress .progress {
    height: 8px;
}

.quiz-simulation .quiz-header {
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 1rem;
}
</style>

</div>
</div>
@endsection
