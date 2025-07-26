<div class="quiz-preview">
    <div class="quiz-header mb-4">
        <h4>{{ $quiz->quiz_title }}</h4>
        <div class="quiz-meta">
            <span class="badge bg-primary">{{ ucfirst($quiz->difficulty ?? 'medium') }}</span>
            <span class="badge bg-info">{{ $quiz->total_questions }} Questions</span>
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
    </div>

    <div class="questions">
        @foreach($quiz->questions as $index => $question)
            <div class="question-card mb-4 p-3 border rounded">
                <h6>Question {{ $index + 1 }}</h6>
                <p>{{ $question->question_text }}</p>
                
                @if($question->question_type === 'multiple_choice')
                    <div class="options">
                        @php $options = is_string($question->options) ? json_decode($question->options, true) : $question->options @endphp
                        @if($options && is_array($options))
                            @foreach($options as $key => $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="question_{{ $question->id }}" disabled>
                                    <label class="form-check-label">
                                        <strong>{{ $key }}.</strong> {{ $option }}
                                        @if($key === $question->correct_answer)
                                            <span class="text-success"><i class="bi bi-check-circle"></i> Correct</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @elseif($question->question_type === 'true_false')
                    <div class="options">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question_{{ $question->id }}" disabled>
                            <label class="form-check-label">
                                True
                                @if($question->correct_answer === 'A' || $question->correct_answer === 'True')
                                    <span class="text-success"><i class="bi bi-check-circle"></i> Correct</span>
                                @endif
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question_{{ $question->id }}" disabled>
                            <label class="form-check-label">
                                False
                                @if($question->correct_answer === 'B' || $question->correct_answer === 'False')
                                    <span class="text-success"><i class="bi bi-check-circle"></i> Correct</span>
                                @endif
                            </label>
                        </div>
                    </div>
                @elseif($question->question_type === 'short_answer')
                    <div class="answer-preview">
                        <strong>Expected Answer:</strong> {{ $question->correct_answer }}
                    </div>
                @elseif($question->question_type === 'essay')
                    <div class="answer-preview">
                        <strong>Sample Answer/Rubric:</strong>
                        <p class="text-muted">{{ $question->correct_answer }}</p>
                    </div>
                @endif
                
                @if($question->explanation)
                    <div class="explanation mt-3 p-2 bg-light rounded">
                        <strong>Explanation:</strong> {{ $question->explanation }}
                    </div>
                @endif
                
                <div class="question-meta mt-2">
                    <small class="text-muted">Points: {{ $question->points ?? 1 }}</small>
                </div>
            </div>
        @endforeach
    </div>
</div>
