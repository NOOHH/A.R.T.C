@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'Quiz Results - ' . $quiz->quiz_title . ' - A.R.T.C')

@section('head')
    <link href="{{ asset('css/student/student-course.css') }}" rel="stylesheet">
    <link href="{{ asset('css/student/student-navbar.css') }}" rel="stylesheet">
    <style>
        /* Header fixed at top, respecting sidebar width */
.main-header {
  position: fixed;
  top: 0;
  left: 0; /* will be adjusted via the collapsed state below */
  right: 0;
  height: 60px;
  display: flex;
  align-items: center;
  padding: 0 1rem;
  background: white;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  z-index: 1050;
  transition: left .3s;
}

/* Push content down so it isn’t hidden under header */
.results-container {
  padding-top: calc(60px + 20px); /* header height + breathing room */
}

:root {
  --sidebar-expanded-width: 250px;
  --sidebar-collapsed-width: 80px;
}

/* Sidebar width control */
.professional-sidebar {
  width: var(--sidebar-expanded-width);
  transition: width .3s;
}
body.sidebar-collapsed .professional-sidebar {
  width: var(--sidebar-collapsed-width);
}

/* Main content shifts based on sidebar state */
.main-content-area {
  margin-left: var(--sidebar-expanded-width);
  transition: margin-left .3s;
}
body.sidebar-collapsed .main-content-area {
  margin-left: var(--sidebar-collapsed-width);
}

        body {
            background: #f8fafc !important;
            height: auto !important;
            overflow-y: auto !important;
        }
        
        .results-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
            min-height: calc(100vh - 200px);
        }
        
        .results-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .score-display {
            font-size: 3rem;
            font-weight: bold;
            margin: 1rem 0;
        }
        
        .score-badge {
            font-size: 1.25rem;
            padding: 0.5rem 1.5rem;
            border-radius: 2rem;
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .summary-card {
            background: white;
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .summary-card .icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .summary-card .value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c5aa0;
        }
        
        .summary-card .label {
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .questions-review {
            background: white;
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .question-item {
            border-bottom: 1px solid #f1f3f4;
            padding: 1.5rem 0;
        }
        
        .question-item:last-child {
            border-bottom: none;
        }
        
        .question-number {
            background: #667eea;
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
        }
        
        .question-text {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        .answer-section {
            margin-left: 3rem;
        }
        
        .answer-item {
            padding: 0.5rem 1rem;
            margin: 0.5rem 0;
            border-radius: 0.375rem;
            border: 1px solid #e3e6f0;
        }
        
        .answer-item.correct {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .answer-item.incorrect {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        .answer-item.student-choice {
            font-weight: 500;
        }
        
        .result-badge {
            float: right;
            margin-top: -0.5rem;
        }
        
        .action-buttons {
            text-align: center;
            padding: 1rem 0;
        }
        
        .action-buttons .btn {
            margin: 0 0.5rem 0.5rem 0.5rem;
        }
        
        @media (max-width: 768px) {
            .results-container {
                padding: 1rem;
            }
            
            .score-display {
                font-size: 2rem;
            }
            
            .summary-cards {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .answer-section {
                margin-left: 1rem;
            }
        }

        /* make sure the content doesn't get hidden under the navbar */
.results-container {
  padding-top: 80px; /* navbar height + breathing room */
  margin-top: 0;
}

    </style>
@endsection

@section('content')
@include('components.student-navbar')

<div class="results-container">
    <!-- Results Header -->
    <div class="results-header">
        <h2>{{ $quiz->quiz_title }}</h2>
        <div class="score-display">
            @php
                // Ensure score is calculated properly - if we have a total_questions value
                if ($attempt->score <= 0 && $attempt->total_questions > 0 && isset($attempt->correct_answers)) {
                    $calculatedScore = ($attempt->correct_answers / $attempt->total_questions) * 100;
                    echo number_format($calculatedScore, 1) . '%';
                } else {
                    echo number_format($attempt->score, 1) . '%';
                }
            @endphp
        </div>
        <div class="score-badge badge bg-{{ $attempt->score >= 75 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }}">
            @if($attempt->score >= 75)
                Excellent! 🎉
            @elseif($attempt->score >= 60)
                Good Job! 👍
            @else
                Keep Trying! 💪
            @endif
        </div>
        <p class="mt-3 mb-0">
            Completed on {{ $attempt->completed_at->format('F j, Y \a\t g:i A') }}
        </p>
    </div>
    
    <!-- Action Buttons -->
    <div class="action-buttons mb-4">
        <a href="{{ route('student.dashboard') }}" class="btn btn-secondary btn-lg">
            <i class="bi bi-house"></i> Back to Dashboard
        </a>
        
        @php
            // contentId is now passed from the controller
            // $contentId = request()->query('content_id');
            // if (!$contentId) {
            //     // Try to find content by quiz_id
            //     $content = \App\Models\ContentItem::where('content_type', 'quiz')
            //         ->whereRaw("JSON_EXTRACT(content_data, '$.quiz_id') = ?", [$quiz->quiz_id])
            //         ->first();
            //     $contentId = $content->id ?? null;
            // }
        @endphp
        
        @if($contentId)
            <a href="{{ route('student.content.view', $contentId) }}" class="btn btn-primary btn-lg">
                <i class="bi bi-arrow-left"></i> Back to Content
            </a>
        @endif
        
        <button onclick="goBack()" class="btn btn-info btn-lg">
            <i class="bi bi-arrow-left-circle"></i> Back
        </button>
        
        <!-- Retake Quiz button removed as requested due to causing bugs -->
    </div>
    
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="icon text-success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="value">{{ $attempt->correct_answers }}</div>
            <div class="label">Correct Answers</div>
        </div>
        
        <div class="summary-card">
            <div class="icon text-danger">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="value">{{ $attempt->total_questions - $attempt->correct_answers }}</div>
            <div class="label">Incorrect Answers</div>
        </div>
        
        <div class="summary-card">
            <div class="icon text-primary">
                <i class="bi bi-list-ol"></i>
            </div>
            <div class="value">{{ $attempt->total_questions }}</div>
            <div class="label">Total Questions</div>
        </div>
        
        <div class="summary-card">
            <div class="icon text-info">
                <i class="bi bi-clock"></i>
            </div>
            <div class="value">{{ $attempt->time_taken ?? 0 }} min</div>
            <div class="label">Time Taken</div>
        </div>
    </div>
    
    <!-- Questions Review -->
    <div class="questions-review">
        <h4 class="mb-4">
            <i class="bi bi-eye"></i> Detailed Review
        </h4>
        
        @foreach($results as $index => $result)
            <div class="question-item">
                <div class="d-flex align-items-start">
                    <div class="question-number">{{ (int)$index + 1 }}</div>
                    <div class="flex-grow-1">
                        <div class="question-text">{{ $result['question']->question_text }}</div>
                        
                        <div class="answer-section">
                            @if($result['question']->question_type === 'multiple_choice')
                                @php
                                    $options = is_array($result['question']->options) ? 
                                              $result['question']->options : 
                                              (is_string($result['question']->options) ? 
                                               json_decode($result['question']->options, true) : []);
                                @endphp
                                
                                @if($options)
                                    @foreach($options as $optionIndex => $option)
                                        @php
                                            $optionLetter = chr(65 + (int)$optionIndex);
                                            $isCorrect = $optionLetter === $result['correct_answer'];
                                            $isStudentChoice = $optionLetter === $result['student_answer'];
                                        @endphp
                                        
                                        <div class="answer-item 
                                                    {{ $isCorrect ? 'correct' : '' }}
                                                    {{ $isStudentChoice && !$isCorrect ? 'incorrect' : '' }}
                                                    {{ $isStudentChoice ? 'student-choice' : '' }}">
                                            <strong>{{ $optionLetter }}.</strong> {{ $option }}
                                            
                                            @if($isCorrect)
                                                <i class="bi bi-check-circle text-success float-end"></i>
                                            @endif
                                            
                                            @if($isStudentChoice && !$isCorrect)
                                                <i class="bi bi-x-circle text-danger float-end"></i>
                                            @endif
                                            
                                            @if($isStudentChoice)
                                                <small class="badge bg-primary float-end me-2">Your Answer</small>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                                
                            @elseif($result['question']->question_type === 'true_false')
                                <div class="answer-item 
                                            {{ $result['correct_answer'] === 'True' ? 'correct' : '' }}
                                            {{ $result['student_answer'] === 'True' && $result['correct_answer'] !== 'True' ? 'incorrect' : '' }}
                                            {{ $result['student_answer'] === 'True' ? 'student-choice' : '' }}">
                                    <strong>True</strong>
                                    
                                    @if($result['correct_answer'] === 'True')
                                        <i class="bi bi-check-circle text-success float-end"></i>
                                    @endif
                                    
                                    @if($result['student_answer'] === 'True' && $result['correct_answer'] !== 'True')
                                        <i class="bi bi-x-circle text-danger float-end"></i>
                                    @endif
                                    
                                    @if($result['student_answer'] === 'True')
                                        <small class="badge bg-primary float-end me-2">Your Answer</small>
                                    @endif
                                </div>
                                
                                <div class="answer-item 
                                            {{ $result['correct_answer'] === 'False' ? 'correct' : '' }}
                                            {{ $result['student_answer'] === 'False' && $result['correct_answer'] !== 'False' ? 'incorrect' : '' }}
                                            {{ $result['student_answer'] === 'False' ? 'student-choice' : '' }}">
                                    <strong>False</strong>
                                    
                                    @if($result['correct_answer'] === 'False')
                                        <i class="bi bi-check-circle text-success float-end"></i>
                                    @endif
                                    
                                    @if($result['student_answer'] === 'False' && $result['correct_answer'] !== 'False')
                                        <i class="bi bi-x-circle text-danger float-end"></i>
                                    @endif
                                    
                                    @if($result['student_answer'] === 'False')
                                        <small class="badge bg-primary float-end me-2">Your Answer</small>
                                    @endif
                                </div>
                            @endif
                            
                            @if(!$result['student_answer'])
                                <div class="answer-item" style="border-color: #ffc107; background: #fff3cd;">
                                    <em class="text-muted">No answer provided</em>
                                    <i class="bi bi-exclamation-triangle text-warning float-end"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="result-badge">
                        @if($result['is_correct'])
                            <span class="badge bg-success">
                                <i class="bi bi-check"></i> Correct
                            </span>
                        @elseif($result['student_answer'])
                            <span class="badge bg-danger">
                                <i class="bi bi-x"></i> Incorrect
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="bi bi-dash"></i> Unanswered
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    // The retakeQuiz function has been removed as the button was causing bugs
    
    // Function to handle back button correctly
    function goBack() {
        // Navigate directly to content page if available, otherwise to dashboard
        @if(isset($contentId) && $contentId)
            window.location.href = "{{ route('student.content.view', $contentId ?? 0) }}";
        @else
            window.location.href = "{{ route('student.dashboard') }}";
        @endif
    }
</script>
@endpush
