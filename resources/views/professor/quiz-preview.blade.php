<div class="quiz-preview">
    <h4>{{ $quiz->quiz_title }}</h4>
    @if($quiz->instructions)
        <div class="alert alert-info">
            <strong>Instructions:</strong> {{ $quiz->instructions }}
        </div>
    @endif
    
    <div class="quiz-meta mb-3">
        <small class="text-muted">
            Difficulty: {{ ucfirst($quiz->difficulty ?? 'medium') }} | 
            Total Questions: {{ $questions->count() }} |
            Total Points: {{ $questions->sum('points') }}
        </small>
    </div>
    
    @foreach($questions as $index => $question)
        <div class="question-item mb-4">
            <h6>Question {{ $index + 1 }}:</h6>
            <p>{{ $question->question_text }}</p>
            
            @if($question->question_type === 'multiple_choice' && $question->options)
                @php
                    $options = json_decode($question->options, true);
                @endphp
                <div class="options">
                    @foreach($options as $optionIndex => $option)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" disabled>
                            <label class="form-check-label">
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
