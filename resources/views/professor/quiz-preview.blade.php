<div class="quiz-preview">
    <div class="quiz-header mb-4">
        <h4>{{ $quiz->quiz_title }}</h4>
        <div class="quiz-meta">
            <span class="badge bg-primary">{{ ucfirst($quiz->difficulty) }}</span>
            <span class="badge bg-info">{{ $quiz->total_questions }} Questions</span>
            <span class="badge bg-warning">{{ $quiz->time_limit }} Minutes</span>
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
                        @php $options = json_decode($question->options, true) @endphp
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
                    </div>
                @elseif($question->question_type === 'true_false')
                    <div class="options">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question_{{ $question->id }}" disabled>
                            <label class="form-check-label">
                                True
                                @if($question->correct_answer === 'A')
                                    <span class="text-success"><i class="bi bi-check-circle"></i> Correct</span>
                                @endif
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question_{{ $question->id }}" disabled>
                            <label class="form-check-label">
                                False
                                @if($question->correct_answer === 'B')
                                    <span class="text-success"><i class="bi bi-check-circle"></i> Correct</span>
                                @endif
                            </label>
                        </div>
                    </div>
                @endif
                
                <div class="question-meta mt-2">
                    <small class="text-muted">Points: {{ $question->points ?? 1 }}</small>
                </div>
            </div>
        @endforeach
    </div>
</div>
                                {{ chr(65 + $optionIndex) }}. {{ $option }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @elseif($question->question_type === 'true_false')
                <div class="options">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" disabled>
                        <label class="form-check-label">True</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" disabled>
                        <label class="form-check-label">False</label>
                    </div>
                </div>
            @endif
            
            <div class="mt-2">
                <strong>Correct Answer:</strong> {{ $question->correct_answer }}
            </div>
            
            @if($question->explanation)
                <div class="mt-1">
                    <strong>Explanation:</strong> {{ $question->explanation }}
                </div>
            @endif
        </div>
        <hr>
    @endforeach
</div>
