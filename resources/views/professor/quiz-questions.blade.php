<div class="quiz-questions">
    <div class="quiz-header mb-4">
        <h4>{{ $quiz->quiz_title }}</h4>
        <div class="quiz-meta">
            <span class="badge bg-primary">{{ ucfirst($quiz->difficulty ?? 'medium') }}</span>
            <span class="badge bg-info">{{ $quiz->questions->count() }} Questions</span>
            @if($quiz->time_limit)
                <span class="badge bg-warning">{{ $quiz->time_limit }} Minutes</span>
            @endif
        </div>
        @if($quiz->instructions)
            <div class="mt-3">
                <strong>Instructions:</strong>
                <p class="text-muted">{{ $quiz->instructions }}</p>
            </div>
        @endif
        @if($quiz->tags)
            <div class="mt-2">
                <strong>Tags:</strong>
                @foreach(json_decode($quiz->tags, true) ?? [] as $tag)
                    <span class="badge bg-secondary me-1">{{ $tag }}</span>
                @endforeach
            </div>
        @endif
    </div>

    @if($quiz->questions->count() > 0)
        <div class="questions">
            @foreach($quiz->questions as $index => $question)
                <div class="question-card mb-4 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="mb-2">Question {{ $index + 1 }}</h6>
                        <span class="badge bg-info">{{ $question->points ?? 1 }} {{ ($question->points ?? 1) == 1 ? 'point' : 'points' }}</span>
                    </div>
                    
                    <p class="mb-3">{{ $question->question_text }}</p>
                    
                    @if($question->question_type === 'multiple_choice')
                        <div class="options">
                            @php $options = is_string($question->options) ? json_decode($question->options, true) : $question->options @endphp
                            @if($options && is_array($options))
                                @foreach($options as $key => $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="question_{{ $question->id }}" disabled>
                                        <label class="form-check-label">
                                            <strong>{{ $key }}.</strong> {{ $option }}
                                            @if($key === $question->correct_answer)
                                                <span class="text-success ms-2"><i class="bi bi-check-circle-fill"></i> Correct Answer</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No options available</p>
                            @endif
                        </div>
                    @elseif($question->question_type === 'true_false')
                        <div class="options">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="question_{{ $question->id }}" disabled>
                                <label class="form-check-label">
                                    True
                                    @if($question->correct_answer === 'A' || $question->correct_answer === 'True')
                                        <span class="text-success ms-2"><i class="bi bi-check-circle-fill"></i> Correct Answer</span>
                                    @endif
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="question_{{ $question->id }}" disabled>
                                <label class="form-check-label">
                                    False
                                    @if($question->correct_answer === 'B' || $question->correct_answer === 'False')
                                        <span class="text-success ms-2"><i class="bi bi-check-circle-fill"></i> Correct Answer</span>
                                    @endif
                                </label>
                            </div>
                        </div>
                    @elseif($question->question_type === 'short_answer')
                        <div class="mb-3">
                            <label class="form-label">Answer:</label>
                            <input type="text" class="form-control" disabled placeholder="Short answer expected">
                            <small class="text-muted">Expected answer: {{ $question->correct_answer }}</small>
                        </div>
                    @elseif($question->question_type === 'essay')
                        <div class="mb-3">
                            <label class="form-label">Essay Answer:</label>
                            <textarea class="form-control" rows="4" disabled placeholder="Essay answer expected"></textarea>
                            @if($question->correct_answer)
                                <small class="text-muted">Sample answer: {{ $question->correct_answer }}</small>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Unknown question type: {{ $question->question_type }}
                        </div>
                    @endif
                    
                    @if($question->explanation)
                        <div class="mt-3 p-2 bg-light rounded">
                            <strong>Explanation:</strong>
                            <p class="mb-0 small text-muted">{{ $question->explanation }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No questions found for this quiz.
        </div>
    @endif
    
    <div class="quiz-footer mt-4">
        <div class="row">
            <div class="col-md-6">
                <strong>Total Questions:</strong> {{ $quiz->questions->count() }}<br>
                <strong>Total Points:</strong> {{ $quiz->questions->sum('points') ?? $quiz->questions->count() }}<br>
                @if($quiz->time_limit)
                    <strong>Time Limit:</strong> {{ $quiz->time_limit }} minutes<br>
                @endif
            </div>
            <div class="col-md-6 text-end">
                <strong>Created:</strong> {{ $quiz->created_at->format('M d, Y') }}<br>
                <strong>Status:</strong> 
                @if($quiz->is_draft)
                    <span class="badge bg-warning">Draft</span>
                @else
                    <span class="badge bg-success">Published</span>
                @endif
            </div>
        </div>
    </div>
</div>
