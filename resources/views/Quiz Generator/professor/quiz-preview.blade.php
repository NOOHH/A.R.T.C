@extends('professor.professor-layouts.professor-layout')
@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-eye"></i> Quiz Preview</h2>
        <a href="{{ route('professor.quiz-generator') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Quiz Manager
        </a>
    </div>
    
    <!-- Quiz Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="mb-0">{{ $quiz->quiz_title }}</h4>
            @if($quiz->quiz_description)
                <p class="text-muted mb-0 mt-2">{{ $quiz->quiz_description }}</p>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Program:</strong> {{ $quiz->program->program_name ?? 'N/A' }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge 
                            @if($quiz->status === 'draft') bg-warning 
                            @elseif($quiz->status === 'published') bg-success
                            @else bg-secondary @endif">
                            {{ ucfirst($quiz->status) }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Questions:</strong> {{ $quiz->questions->count() }}</p>
                    <p><strong>Time Limit:</strong> {{ $quiz->time_limit }} minutes</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Questions -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list-ol"></i> Questions</h5>
        </div>
        <div class="card-body">
            @forelse($quiz->questions as $index => $question)
                <div class="question-preview mb-4 p-3 border rounded">
                    <h6 class="fw-bold">Question {{ $index + 1 }}</h6>
                    <p class="mb-3">{{ $question->question_text }}</p>
                    
                    @if($question->question_type === 'multiple_choice')
                        <div class="options">
                            @if($question->options && is_array($question->options))
                                @foreach($question->options as $optionIndex => $option)
                                    @php $letter = chr(65 + $optionIndex); @endphp
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="question_{{ $question->id }}" disabled 
                                               @if($question->correct_answer === $letter || $question->correct_answer === $option) checked @endif>
                                        <label class="form-check-label">
                                            <strong>{{ $letter }}.</strong> {{ $option }}
                                            @if($question->correct_answer === $letter || $question->correct_answer === $option)
                                                <span class="badge bg-success ms-2">Correct</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @elseif($question->question_type === 'true_false')
                        <div class="options">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="question_{{ $question->id }}" disabled 
                                       @if(strtolower($question->correct_answer) === 'true' || $question->correct_answer === 'A' || $question->correct_answer === 'True') checked @endif>
                                <label class="form-check-label">
                                    True 
                                    @if(strtolower($question->correct_answer) === 'true' || $question->correct_answer === 'A' || $question->correct_answer === 'True') 
                                        <span class="badge bg-success ms-2">Correct</span> 
                                    @endif
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="question_{{ $question->id }}" disabled 
                                       @if(strtolower($question->correct_answer) === 'false' || $question->correct_answer === 'B' || $question->correct_answer === 'False') checked @endif>
                                <label class="form-check-label">
                                    False 
                                    @if(strtolower($question->correct_answer) === 'false' || $question->correct_answer === 'B' || $question->correct_answer === 'False') 
                                        <span class="badge bg-success ms-2">Correct</span> 
                                    @endif
                                </label>
                            </div>
                        </div>
                    @else
                        <div class="form-group">
                            <label class="form-label"><strong>Expected Answer:</strong></label>
                            <div class="alert alert-info">{{ $question->correct_answer }}</div>
                        </div>
                    @endif
                    
                    @if($question->explanation)
                        <div class="explanation mt-3 p-2 bg-light rounded">
                            <strong>Explanation:</strong> {{ $question->explanation }}
                        </div>
                    @endif
                    
                    <div class="text-muted small mt-2">
                        <i class="bi bi-award"></i> Points: {{ $question->points ?? 1 }}
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox display-4"></i>
                    <p class="mt-2">No questions in this quiz yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
