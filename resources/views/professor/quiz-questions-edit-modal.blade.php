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
                    <label class="form-label">Status</label>
                    <select class="form-select" id="status">
                        <option value="draft" {{ $quiz->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ $quiz->status === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ $quiz->status === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Max Attempts</label>
                    <input type="number" class="form-control" id="max_attempts" value="{{ $quiz->max_attempts ?? 1 }}" min="1">
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
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="randomize_order" {{ $quiz->randomize_order ? 'checked' : '' }}>
                <label class="form-check-label" for="randomize_order">
                    Randomize Questions
                </label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="randomize_mc_options" {{ $quiz->randomize_mc_options ?? false ? 'checked' : '' }}>
                <label class="form-check-label" for="randomize_mc_options">
                    Randomize Multiple Choice Options
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
    <button type="button" class="btn btn-primary" onclick="addNewQuestionInModal()">
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
                        <select class="form-select form-select-sm me-2" style="width: auto; display: inline-block;" onchange="changeQuestionTypeInModal({{ $question->id }}, this.value)">
                            <option value="multiple_choice" {{ $question->question_type === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="true_false" {{ $question->question_type === 'true_false' ? 'selected' : '' }}>True/False</option>
                            <option value="short_answer" {{ $question->question_type === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                            <option value="essay" {{ $question->question_type === 'essay' ? 'selected' : '' }}>Essay</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteQuestionInModal({{ $question->id }})">
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
                    <div class="col-md-12">
                        <label class="form-label">Points</label>
                        <input type="number" class="form-control" name="points_{{ $question->id }}" value="{{ $question->points ?? 1 }}" min="1">
                    </div>
                </div>
                
                <!-- Question Type Specific Content -->
                <div class="question-options-container">
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
        <button type="button" class="btn btn-primary" onclick="addNewQuestionInModal()">
            <i class="bi bi-plus-circle"></i> Add Your First Question
        </button>
    </div>
@endif

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
    
    .quiz-settings-panel {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
