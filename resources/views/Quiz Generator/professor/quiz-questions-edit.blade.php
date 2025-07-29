@extends('professor.professor-layouts.professor-layout')

@section('title', 'Edit Quiz Questions')

@section('styles')
<style>
    .question-editor {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
        margin-bottom: 20px;
    }
    
    .question-header {
        background: #e9ecef;
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        border-radius: 8px 8px 0 0;
    }
    
    .question-content {
        padding: 20px;
    }
    
    .option-input {
        margin-bottom: 10px;
    }
    
    .add-option-btn {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        color: #6c757d;
        padding: 10px;
        border-radius: 5px;
        width: 100%;
    }
    
    .add-option-btn:hover {
        border-color: #007bff;
        color: #007bff;
    }
    
    .question-actions {
        background: #f8f9fa;
        padding: 15px;
        border-top: 1px solid #dee2e6;
        border-radius: 0 0 8px 8px;
    }
    
    .correct-answer-indicator {
        color: #28a745;
        font-weight: bold;
    }
    
    .quiz-settings-panel {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Edit Quiz Questions</h2>
                    <p class="text-muted mb-0">{{ $quiz->quiz_title }}</p>
                </div>
                <div>
                    <a href="{{ route('professor.quiz-generator') }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back to Quiz Generator
                    </a>
                    <button type="button" class="btn btn-success" onclick="saveAllQuestions()">
                        <i class="bi bi-check-circle"></i> Save All Changes
                    </button>
                </div>
            </div>

            <!-- Quiz Information Panel -->
            <div class="quiz-settings-panel">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="mb-3">Quiz Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Quiz Title</label>
                                <input type="text" class="form-control" id="quiz_title" value="{{ $quiz->quiz_title }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Time Limit (minutes)</label>
                                <input type="number" class="form-control" id="time_limit" value="{{ $quiz->time_limit }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Difficulty</label>
                                <select class="form-select" id="difficulty">
                                    <option value="easy" {{ $quiz->difficulty === 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ $quiz->difficulty === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ $quiz->difficulty === 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="status">
                                    <option value="draft" {{ $quiz->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ $quiz->status === 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ $quiz->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Instructions</label>
                            <textarea class="form-control" id="instructions" rows="3">{{ $quiz->instructions }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5 class="mb-3">Quiz Settings</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="allow_retakes" {{ $quiz->allow_retakes ? 'checked' : '' }}>
                            <label class="form-check-label" for="allow_retakes">
                                Allow Retakes
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="instant_feedback" {{ $quiz->instant_feedback ? 'checked' : '' }}>
                            <label class="form-check-label" for="instant_feedback">
                                Instant Feedback
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="show_correct_answers" {{ $quiz->show_correct_answers ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_correct_answers">
                                Show Correct Answers
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="randomize_order" {{ $quiz->randomize_order ? 'checked' : '' }}>
                            <label class="form-check-label" for="randomize_order">
                                Randomize Questions
                            </label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Max Retakes</label>
                            <input type="number" class="form-control" id="max_attempts" value="{{ $quiz->max_attempts ?? 1 }}" min="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Questions ({{ $quiz->questions->count() }})</h4>
                <button type="button" class="btn btn-primary" onclick="addNewQuestion()">
                    <i class="bi bi-plus-circle"></i> Add Question
                </button>
            </div>

            <!-- Questions List -->
            <div id="questions-container">
                @foreach($quiz->questions as $index => $question)
                    <div class="question-editor" data-question-id="{{ $question->id }}">
                        <div class="question-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Question {{ $index + 1 }}</h6>
                                <div>
                                    <select class="form-select form-select-sm me-2" style="width: auto; display: inline-block;" onchange="changeQuestionType({{ $question->id }}, this.value)">
                                        <option value="multiple_choice" {{ $question->question_type === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                        <option value="true_false" {{ $question->question_type === 'true_false' ? 'selected' : '' }}>True/False</option>
                                        <option value="short_answer" {{ $question->question_type === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                        <option value="essay" {{ $question->question_type === 'essay' ? 'selected' : '' }}>Essay</option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteQuestion({{ $question->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="question-content">
                            <div class="mb-3">
                                <label class="form-label">Question Text</label>
                                <textarea class="form-control" name="question_text_{{ $question->id }}" rows="3">{{ $question->question_text }}</textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Points</label>
                                    <input type="number" class="form-control" name="points_{{ $question->id }}" value="{{ $question->points ?? 1 }}" min="1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Difficulty</label>
                                    <select class="form-select" name="difficulty_{{ $question->id }}">
                                        <option value="easy" {{ $question->difficulty === 'easy' ? 'selected' : '' }}>Easy</option>
                                        <option value="medium" {{ $question->difficulty === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="hard" {{ $question->difficulty === 'hard' ? 'selected' : '' }}>Hard</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Question Type Specific Content -->
                            <div id="question-options-{{ $question->id }}">
                                @if($question->question_type === 'multiple_choice')
                                    @include('professor.partials.question-options-multiple-choice', ['question' => $question])
                                @elseif($question->question_type === 'true_false')
                                    @include('professor.partials.question-options-true-false', ['question' => $question])
                                @elseif($question->question_type === 'short_answer')
                                    @include('professor.partials.question-options-short-answer', ['question' => $question])
                                @elseif($question->question_type === 'essay')
                                    @include('professor.partials.question-options-essay', ['question' => $question])
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Explanation (Optional)</label>
                                <textarea class="form-control" name="explanation_{{ $question->id }}" rows="2">{{ $question->explanation }}</textarea>
                                <small class="text-muted">This will be shown to students after they answer the question (if instant feedback is enabled).</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($quiz->questions->count() === 0)
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i>
                    <h5>No Questions Yet</h5>
                    <p>Start building your quiz by adding some questions!</p>
                    <button type="button" class="btn btn-primary" onclick="addNewQuestion()">
                        <i class="bi bi-plus-circle"></i> Add Your First Question
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Question Template (Hidden) -->
<div id="question-template" style="display: none;">
    <div class="question-editor" data-question-id="new">
        <div class="question-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">New Question</h6>
                <div>
                    <select class="form-select form-select-sm me-2" style="width: auto; display: inline-block;" onchange="changeQuestionType('new', this.value)">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="short_answer">Short Answer</option>
                        <option value="essay">Essay</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteQuestion('new')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="question-content">
            <div class="mb-3">
                <label class="form-label">Question Text</label>
                <textarea class="form-control" name="question_text_new" rows="3" placeholder="Enter your question here..."></textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Points</label>
                    <input type="number" class="form-control" name="points_new" value="1" min="1">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Difficulty</label>
                    <select class="form-select" name="difficulty_new">
                        <option value="easy">Easy</option>
                        <option value="medium" selected>Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>
            </div>
            
            <div id="question-options-new">
                <!-- Question type specific options will be loaded here -->
            </div>
            
            <div class="mb-3">
                <label class="form-label">Explanation (Optional)</label>
                <textarea class="form-control" name="explanation_new" rows="2" placeholder="Provide an explanation for the correct answer..."></textarea>
                <small class="text-muted">This will be shown to students after they answer the question (if instant feedback is enabled).</small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let questionCounter = {{ $quiz->questions->count() }};

function addNewQuestion() {
    questionCounter++;
    const template = document.getElementById('question-template').innerHTML;
    const newId = 'temp_' + Date.now();
    
    // Replace 'new' with unique temporary ID
    const newQuestionHtml = template.replace(/new/g, newId)
                                  .replace(/New Question/, `Question ${questionCounter}`);
    
    // Append to questions container
    const container = document.getElementById('questions-container');
    const div = document.createElement('div');
    div.innerHTML = newQuestionHtml;
    container.appendChild(div.firstElementChild);
    
    // Load default multiple choice options
    changeQuestionType(newId, 'multiple_choice');
    
    // Scroll to new question
    div.firstElementChild.scrollIntoView({ behavior: 'smooth' });
}

function deleteQuestion(questionId) {
    if (confirm('Are you sure you want to delete this question?')) {
        const questionElement = document.querySelector(`[data-question-id="${questionId}"]`);
        if (questionElement) {
            questionElement.remove();
            updateQuestionNumbers();
        }
    }
}

function changeQuestionType(questionId, questionType) {
    const optionsContainer = document.getElementById(`question-options-${questionId}`);
    
                fetch(`{{ route('professor.quiz-generator.question.options') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            question_type: questionType,
            question_id: questionId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.html) {
            optionsContainer.innerHTML = data.html;
        }
    })
    .catch(error => {
        console.error('Error changing question type:', error);
    });
}

function updateQuestionNumbers() {
    const questions = document.querySelectorAll('.question-editor');
    questions.forEach((question, index) => {
        const header = question.querySelector('.question-header h6');
        header.textContent = `Question ${index + 1}`;
    });
    questionCounter = questions.length;
}

function saveAllQuestions() {
    const quizData = {
        quiz_title: document.getElementById('quiz_title').value,
        time_limit: document.getElementById('time_limit').value,
        difficulty: document.getElementById('difficulty').value,
        status: document.getElementById('status').value,
        instructions: document.getElementById('instructions').value,
        allow_retakes: document.getElementById('allow_retakes').checked,
        instant_feedback: document.getElementById('instant_feedback').checked,
        show_correct_answers: document.getElementById('show_correct_answers').checked,
        randomize_order: document.getElementById('randomize_order').checked,
        max_attempts: document.getElementById('max_attempts').value,
        questions: []
    };
    
    // Collect all questions
    const questionElements = document.querySelectorAll('.question-editor');
    questionElements.forEach((questionEl) => {
        const questionId = questionEl.getAttribute('data-question-id');
        const questionData = {
            id: questionId.startsWith('temp_') ? null : questionId,
            question_text: questionEl.querySelector(`[name="question_text_${questionId}"]`).value,
            question_type: questionEl.querySelector(`select[onchange*="${questionId}"]`).value,
            points: questionEl.querySelector(`[name="points_${questionId}"]`).value,
            difficulty: questionEl.querySelector(`[name="difficulty_${questionId}"]`).value,
            explanation: questionEl.querySelector(`[name="explanation_${questionId}"]`).value,
            options: {},
            correct_answer: ''
        };
        
        // Collect question type specific data
        const questionType = questionData.question_type;
        if (questionType === 'multiple_choice') {
            const options = {};
            const optionInputs = questionEl.querySelectorAll('.option-input input[type="text"]');
            const correctRadio = questionEl.querySelector('input[type="radio"]:checked');
            
            optionInputs.forEach((input, index) => {
                const letter = String.fromCharCode(65 + index); // A, B, C, D
                if (input.value.trim()) {
                    options[letter] = input.value.trim();
                }
            });
            
            questionData.options = options;
            if (correctRadio) {
                questionData.correct_answer = correctRadio.value;
            }
        } else if (questionType === 'true_false') {
            questionData.options = { A: 'True', B: 'False' };
            const correctRadio = questionEl.querySelector('input[type="radio"]:checked');
            if (correctRadio) {
                questionData.correct_answer = correctRadio.value;
            }
        } else if (questionType === 'short_answer' || questionType === 'essay') {
            const answerInput = questionEl.querySelector(`[name="correct_answer_${questionId}"]`);
            if (answerInput) {
                questionData.correct_answer = answerInput.value;
            }
        }
        
        quizData.questions.push(questionData);
    });
    
    // Save to server
                fetch(`{{ route('professor.quiz-generator.questions.update', $quiz->quiz_id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(quizData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Quiz updated successfully!');
            // Refresh page to show updated data
            window.location.reload();
        } else {
            alert('Error updating quiz: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error saving questions:', error);
        alert('Error saving quiz. Please try again.');
    });
}

// Auto-save draft every 2 minutes
setInterval(() => {
    if (document.getElementById('status').value === 'draft') {
        saveAllQuestions();
    }
}, 120000);
</script>
@endsection
