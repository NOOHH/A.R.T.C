@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between">
                    <h5 class="mb-0"><i class="bi bi-card-checklist"></i> Cloud Security Quiz</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-light" onclick="printQuiz()">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        <button class="btn btn-sm btn-outline-light" onclick="toggleAnswers()">
                            <i class="bi bi-eye"></i> <span id="toggleBtnText">Show Answers</span>
                        </button>
                    </div>
                </div>
                <div class="card-body" id="quizContent">
                    @if (isset($formattedQuiz) && !empty($formattedQuiz))
                        <div class="quiz-info mb-4">
                            <h4>Cloud Security & GRC Knowledge Quiz</h4>
                            <p>Generated from: {{ implode(', ', $sources ?? ['Cloud Security Lecture Materials']) }}</p>
                        </div>
                        
                        <div class="quiz-questions">
                            {!! nl2br(e($formattedQuiz)) !!}
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <p>No quiz was generated. Please try again with different parameters.</p>
                        </div>
                    @endif
                </div>
                @if (isset($quiz) && !empty($quiz))
                <div class="card-footer">
                    <form action="{{ route('cloud-security.save') }}" method="POST">
                        @csrf
                        <input type="hidden" name="quiz_data" value="{{ json_encode($quiz) }}">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Quiz Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="Cloud Security & GRC Quiz" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Description (Optional)</label>
                                    <input type="text" class="form-control" id="description" name="description" value="Generated from Cloud Security lecture materials">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Quiz to Database
                            </button>
                            
                            <form action="{{ route('cloud-security.regenerate') }}" method="POST" class="d-inline" id="regenerateForm">
                                @csrf
                                <input type="hidden" name="sources" value="{{ json_encode($sources) }}">
                                <input type="hidden" name="min_mcq" value="{{ count($quiz['mcqs']) }}">
                                <input type="hidden" name="min_tf" value="{{ count($quiz['true_false']) }}">
                                <button type="submit" class="btn btn-info" id="regenerateBtn">
                                    <i class="bi bi-arrow-clockwise"></i> Regenerate Different Questions
                                </button>
                            </form>
                            
                            <a href="{{ route('cloud-security.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Generate Another Quiz
                            </a>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            
            <!-- Student Quiz View -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-person-check"></i> Student Quiz View (Interactive)</h5>
                </div>
                <div class="card-body">
                    <div id="interactiveQuiz">
                        @if (isset($quiz) && !empty($quiz['mcqs']))
                            <h4 class="mb-3">I. Multiple-Choice Questions</h4>
                            @foreach($quiz['mcqs'] as $index => $mcq)
                                <div class="card mb-3 question-card" data-question-type="mcq" data-question-number="{{ $index + 1 }}">
                                    <div class="card-header">
                                        <strong>Question {{ $index + 1 }}:</strong> {{ $mcq['text'] }}
                                    </div>
                                    <div class="card-body">
                                        <div class="options">
                                            @foreach($mcq['options'] as $letter => $option)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" 
                                                        name="mcq_{{ $index + 1 }}" 
                                                        id="mcq_{{ $index + 1 }}_{{ $letter }}" 
                                                        value="{{ $letter }}">
                                                    <label class="form-check-label w-100" for="mcq_{{ $index + 1 }}_{{ $letter }}">
                                                        {{ $letter }}. {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="answer-feedback mt-3 d-none">
                                            <div class="correct-answer alert alert-success">
                                                Correct! The answer is <strong>{{ getAnswerForQuestion($index + 1, $quiz['answer_key'], 'mcq') }}</strong>.
                                                @if(getExplanationForQuestion($index + 1, $quiz['answer_key']))
                                                    <br>
                                                    <em>{{ getExplanationForQuestion($index + 1, $quiz['answer_key']) }}</em>
                                                @endif
                                            </div>
                                            <div class="wrong-answer alert alert-danger">
                                                Incorrect. The correct answer is <strong>{{ getAnswerForQuestion($index + 1, $quiz['answer_key'], 'mcq') }}</strong>.
                                                @if(getExplanationForQuestion($index + 1, $quiz['answer_key']))
                                                    <br>
                                                    <em>{{ getExplanationForQuestion($index + 1, $quiz['answer_key']) }}</em>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <h4 class="mb-3 mt-5">II. True/False Statements</h4>
                            @foreach($quiz['true_false'] as $index => $tf)
                                <div class="card mb-3 question-card" data-question-type="tf" data-question-number="{{ count($quiz['mcqs']) + $index + 1 }}">
                                    <div class="card-header">
                                        <strong>Question {{ count($quiz['mcqs']) + $index + 1 }}:</strong> {{ $tf['statement'] }}
                                    </div>
                                    <div class="card-body">
                                        <div class="options">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" 
                                                    name="tf_{{ count($quiz['mcqs']) + $index + 1 }}" 
                                                    id="tf_{{ count($quiz['mcqs']) + $index + 1 }}_true" 
                                                    value="True">
                                                <label class="form-check-label" for="tf_{{ count($quiz['mcqs']) + $index + 1 }}_true">
                                                    True
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" 
                                                    name="tf_{{ count($quiz['mcqs']) + $index + 1 }}" 
                                                    id="tf_{{ count($quiz['mcqs']) + $index + 1 }}_false" 
                                                    value="False">
                                                <label class="form-check-label" for="tf_{{ count($quiz['mcqs']) + $index + 1 }}_false">
                                                    False
                                                </label>
                                            </div>
                                        </div>
                                        <div class="answer-feedback mt-3 d-none">
                                            <div class="correct-answer alert alert-success">
                                                Correct! The statement is <strong>{{ getAnswerForQuestion(count($quiz['mcqs']) + $index + 1, $quiz['answer_key'], 'tf') }}</strong>.
                                                @if(getExplanationForQuestion(count($quiz['mcqs']) + $index + 1, $quiz['answer_key']))
                                                    <br>
                                                    <em>{{ getExplanationForQuestion(count($quiz['mcqs']) + $index + 1, $quiz['answer_key']) }}</em>
                                                @endif
                                            </div>
                                            <div class="wrong-answer alert alert-danger">
                                                Incorrect. The statement is <strong>{{ getAnswerForQuestion(count($quiz['mcqs']) + $index + 1, $quiz['answer_key'], 'tf') }}</strong>.
                                                @if(getExplanationForQuestion(count($quiz['mcqs']) + $index + 1, $quiz['answer_key']))
                                                    <br>
                                                    <em>{{ getExplanationForQuestion(count($quiz['mcqs']) + $index + 1, $quiz['answer_key']) }}</em>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button id="checkAnswersBtn" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Check Answers
                                </button>
                                <button id="resetQuizBtn" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                            </div>
                            
                            <div class="mt-4 d-none" id="quizResults">
                                <div class="alert alert-primary">
                                    <h5>Quiz Results</h5>
                                    <p>You answered <span id="correctCount">0</span> out of <span id="totalQuestions">{{ count($quiz['mcqs']) + count($quiz['true_false']) }}</span> questions correctly.</p>
                                    <div class="progress">
                                        <div id="scoreProgressBar" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <p>No quiz data available for interactive mode.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
function getAnswerForQuestion($questionNumber, $answerKey, $type = 'mcq') {
    foreach ($answerKey as $key) {
        if ($key['number'] == $questionNumber) {
            return $key['answer'];
        }
    }
    return $type === 'mcq' ? 'A' : 'True';
}

function getExplanationForQuestion($questionNumber, $answerKey) {
    foreach ($answerKey as $key) {
        if ($key['number'] == $questionNumber && !empty($key['explanation'])) {
            return $key['explanation'];
        }
    }
    return '';
}
@endphp

@endsection

@push('styles')
<style>
#quizContent {
    white-space: pre-wrap;
    font-family: 'Courier New', monospace;
}
.quiz-info {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 1rem;
}
.answer-key {
    display: none;
}
.answer-key.show {
    display: block;
}
.question-card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}
.question-card.correct {
    border-left: 5px solid #28a745;
}
.question-card.incorrect {
    border-left: 5px solid #dc3545;
}
.option-correct {
    background-color: rgba(40, 167, 69, 0.1);
}
.option-incorrect {
    background-color: rgba(220, 53, 69, 0.1);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to shuffle MCQ options
    function shuffleMcqOptions() {
        const mcqCards = document.querySelectorAll('[data-question-type="mcq"]');
        
        mcqCards.forEach(card => {
            const options = card.querySelector('.options');
            const optionsArray = Array.from(options.children);
            
            // Shuffle array
            for (let i = optionsArray.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [optionsArray[i], optionsArray[j]] = [optionsArray[j], optionsArray[i]];
            }
            
            // Clear and append in new order
            options.innerHTML = '';
            optionsArray.forEach(option => options.appendChild(option));
        });
    }
    
    // Shuffle MCQ options on page load
    shuffleMcqOptions();
    
    // Check Answers functionality
    const checkAnswersBtn = document.getElementById('checkAnswersBtn');
    const resetQuizBtn = document.getElementById('resetQuizBtn');
    const quizResults = document.getElementById('quizResults');
    const correctCountEl = document.getElementById('correctCount');
    const totalQuestionsEl = document.getElementById('totalQuestions');
    const scoreProgressBar = document.getElementById('scoreProgressBar');
    
    if (checkAnswersBtn) {
        checkAnswersBtn.addEventListener('click', function() {
            const questionCards = document.querySelectorAll('.question-card');
            let correctCount = 0;
            
            questionCards.forEach(card => {
                const questionNumber = card.dataset.questionNumber;
                const questionType = card.dataset.questionType;
                const correctAnswer = getCorrectAnswer(questionNumber);
                
                const selectedInput = questionType === 'mcq' 
                    ? document.querySelector(`input[name="mcq_${questionNumber}"]:checked`)
                    : document.querySelector(`input[name="tf_${questionNumber}"]:checked`);
                
                const feedbackEl = card.querySelector('.answer-feedback');
                const correctFeedback = feedbackEl.querySelector('.correct-answer');
                const wrongFeedback = feedbackEl.querySelector('.wrong-answer');
                
                if (selectedInput) {
                    feedbackEl.classList.remove('d-none');
                    
                    if (selectedInput.value === correctAnswer) {
                        card.classList.add('correct');
                        correctFeedback.classList.remove('d-none');
                        wrongFeedback.classList.add('d-none');
                        correctCount++;
                    } else {
                        card.classList.add('incorrect');
                        correctFeedback.classList.add('d-none');
                        wrongFeedback.classList.remove('d-none');
                    }
                } else {
                    // No answer selected
                    card.classList.add('incorrect');
                }
            });
            
            // Update and show results
            const totalQuestions = questionCards.length;
            const scorePercentage = Math.round((correctCount / totalQuestions) * 100);
            
            correctCountEl.textContent = correctCount;
            totalQuestionsEl.textContent = totalQuestions;
            scoreProgressBar.style.width = `${scorePercentage}%`;
            scoreProgressBar.textContent = `${scorePercentage}%`;
            scoreProgressBar.setAttribute('aria-valuenow', scorePercentage);
            
            // Apply color based on score
            if (scorePercentage >= 80) {
                scoreProgressBar.classList.add('bg-success');
            } else if (scorePercentage >= 60) {
                scoreProgressBar.classList.add('bg-info');
            } else if (scorePercentage >= 40) {
                scoreProgressBar.classList.add('bg-warning');
            } else {
                scoreProgressBar.classList.add('bg-danger');
            }
            
            quizResults.classList.remove('d-none');
        });
    }
    
    if (resetQuizBtn) {
        resetQuizBtn.addEventListener('click', function() {
            // Clear all selections
            document.querySelectorAll('input[type="radio"]').forEach(input => {
                input.checked = false;
            });
            
            // Hide all feedback
            document.querySelectorAll('.answer-feedback').forEach(feedback => {
                feedback.classList.add('d-none');
            });
            
            // Reset card styling
            document.querySelectorAll('.question-card').forEach(card => {
                card.classList.remove('correct', 'incorrect');
            });
            
            // Hide results
            quizResults.classList.add('d-none');
            
            // Reset progress bar
            scoreProgressBar.style.width = '0%';
            scoreProgressBar.textContent = '0%';
            scoreProgressBar.setAttribute('aria-valuenow', 0);
            scoreProgressBar.classList.remove('bg-success', 'bg-info', 'bg-warning', 'bg-danger');
        });
    }
    
    function getCorrectAnswer(questionNumber) {
        @if(isset($quiz) && !empty($quiz['answer_key']))
            @foreach($quiz['answer_key'] as $key)
                if ({{ $key['number'] }} == questionNumber) {
                    return "{{ $key['answer'] }}";
                }
            @endforeach
        @endif
        return "";
    }
});

function toggleAnswers() {
    const answerSections = document.querySelectorAll(".answer-key");
    const toggleBtn = document.getElementById("toggleBtnText");
    
    answerSections.forEach(section => {
        section.classList.toggle("show");
    });
    
    if (toggleBtn.innerText === "Show Answers") {
        toggleBtn.innerText = "Hide Answers";
    } else {
        toggleBtn.innerText = "Show Answers";
    }
}

function printQuiz() {
    window.print();
}

// Handle regenerate button to show loading state
document.getElementById('regenerateForm').addEventListener('submit', function() {
    const regenerateBtn = document.getElementById('regenerateBtn');
    regenerateBtn.disabled = true;
    regenerateBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...';
});
</script>
@endpush
