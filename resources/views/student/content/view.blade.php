@extends('student.student-dashboard.student-dashboard-layout')

@section('title', ($content->content_title ?? 'Content') . ' - A.R.T.C')

@section('head')
    <!-- Content-specific styles -->
    <link href="{{ asset('css/student/student-course.css') }}" rel="stylesheet">
    <style>
        /* CONTENT VIEW PAGE SPECIFIC FIXES - Only affects this page */
        
        /* Fix horizontal scrollbar for content page only */
        .content-wrapper {
            overflow-x: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
            background: #f8fafc !important;
            min-height: 100vh;
            padding-bottom: 4rem;
            margin: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        
        /* Override red background only for content wrapper */
        .content-wrapper {
            background: #f8fafc !important;
            background-color: #f8fafc !important;
            background-image: none !important;
        }
        
        /* Content page specific styling */
        .content-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            min-height: calc(100vh - 200px);
            position: relative;
            width: 100%;
            box-sizing: border-box;
            overflow-x: hidden;
        }
        
        .content-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .content-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .content-breadcrumb {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .content-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }
        
        .content-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .content-body {
            background: white;
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            min-height: 400px;
        }
        
        .content-actions {
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .video-embed-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            margin-bottom: 1rem;
            background-color: #000;
            border-radius: 0.5rem;
        }
        
        .video-embed-container iframe,
        .video-embed-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            object-fit: contain;
            border-radius: 0.5rem;
        }
        
        .attachment-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-top: 1.5rem;
        }
        
        .attachment-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: white;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .attachment-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 0.375rem;
            font-size: 1.2rem;
        }
        
        .assignment-section {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .submission-history {
            margin-top: 2rem;
        }
        
        .submission-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .submission-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-submitted {
            background: #d4edda;
            color: #155724;
        }
        
        .status-graded {
            background: #cce5ff;
            color: #0056b3;
        }
        
        /* Ensure content page containers respect width constraints */
        .content-page .container-fluid, 
        .content-page .row, 
        .content-page .col, 
        .content-page .col-md-*, 
        .content-page .col-lg-* {
            max-width: 100% !important;
            overflow-x: hidden !important;
        }
        
        /* Fix any potential overflow issues within content page */
        .content-page * {
            box-sizing: border-box;
        }
        
        @media (max-width: 768px) {
            .content-page {
                padding: 1rem;
                min-height: calc(100vh - 150px);
            }
            
            .content-header {
                padding: 1.5rem;
            }
            
            .content-title {
                font-size: 1.5rem;
            }
            
            .content-body {
                padding: 1.5rem;
                min-height: 300px;
            }
            
            .content-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="content-page">
        <!-- Content Header -->
        <div class="content-header">
            <div class="content-breadcrumb">
                <i class="bi bi-house"></i>
                <a href="{{ route('student.dashboard') }}" class="text-white text-decoration-none">Dashboard</a>
                @if($content->program_name)
                    <i class="bi bi-chevron-right mx-2"></i>
                    <a href="{{ route('student.course', ['courseId' => $content->program_id ?? 0]) }}" class="text-white text-decoration-none">{{ $content->program_name }}</a>
                @endif
                @if(isset($content->module_name) && $content->module_name)
                    <i class="bi bi-chevron-right mx-2"></i>
                    <span>{{ $content->module_name }}</span>
                @endif
                @if(isset($course->subject_name) && $course->subject_name)
                    <i class="bi bi-chevron-right mx-2"></i>
                    <span>{{ $course->subject_name }}</span>
                @endif
            </div>
            
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="content-title">{{ $content->content_title ?? 'Content' }}</h1>
                <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left-circle"></i> Back
                </a>
            </div>
            
            @if($content->content_description)
                <p class="mb-0">{{ $content->content_description }}</p>
            @endif
            
            <div class="content-meta">
                <span class="content-badge bg-primary">{{ ucfirst($content->content_type ?? 'lesson') }}</span>
                
                @if($content->due_date)
                    <span class="content-badge bg-warning text-dark">
                        <i class="bi bi-calendar"></i> Due: {{ \Carbon\Carbon::parse($content->due_date)->format('M d, Y') }}
                    </span>
                @endif
                
                @if($isCompleted)
                    <span class="content-badge bg-success">
                        <i class="bi bi-check-circle"></i> Completed
                    </span>
                @endif
                
                @if($content->is_required)
                    <span class="content-badge bg-danger">Required</span>
                @endif
            </div>
        </div>
        
        <!-- Content Actions -->
        <div class="content-actions">
            <div>
                <a href="{{ route('student.course', ['courseId' => $content->program_id ?? 0]) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Course
                </a>
            </div>
            
            <div>
                @if(!$isCompleted)
                    <button class="btn btn-success" onclick="markComplete('content', '{{ $content->id }}', this)">
                        <i class="bi bi-check-circle"></i> Mark Complete
                    </button>
                @endif
            </div>
        </div>
        
        <!-- Content Body -->
        <div class="content-body">
            <!-- Video Content -->
            @if($content->content_type === 'video' && $content->content_url)
                <div class="video-embed-container">
                    @php
                        $url = $content->content_url;
                        $embedHtml = '';
                        
                        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
                            $videoId = '';
                            if (str_contains($url, 'youtube.com/watch?v=')) {
                                $videoId = explode('youtube.com/watch?v=', $url)[1];
                                $videoId = explode('&', $videoId)[0];
                            } elseif (str_contains($url, 'youtu.be/')) {
                                $videoId = explode('youtu.be/', $url)[1];
                                $videoId = explode('?', $videoId)[0];
                            }
                            $embedHtml = '<iframe src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe>';
                        } elseif (str_contains($url, 'vimeo.com')) {
                            $videoId = explode('vimeo.com/', $url)[1];
                            $videoId = explode('/', $videoId)[0];
                            $embedHtml = '<iframe src="https://player.vimeo.com/video/' . $videoId . '" frameborder="0" allowfullscreen></iframe>';
                        } elseif (preg_match('/\.(mp4|webm|ogg|mov|avi|mkv)$/i', $url)) {
                            $embedHtml = '<video controls><source src="' . $url . '" type="video/mp4">Your browser does not support the video tag.</video>';
                        } else {
                            $embedHtml = '<p class="text-center"><a href="' . $url . '" target="_blank" class="btn btn-primary">Open Video</a></p>';
                        }
                    @endphp
                    {!! $embedHtml !!}
                </div>
            @endif
            
            <!-- Content Text -->
            @if(isset($content->content_text) && $content->content_text)
                <div class="content-text">
                    {!! nl2br(e($content->content_text)) !!}
                </div>
            @endif
            
            <!-- Content URL Link -->
            @if($content->content_url && $content->content_type !== 'video')
                <div class="content-link">
                    <a href="{{ $content->content_url }}" target="_blank" class="btn btn-primary">
                        <i class="bi bi-link-45deg"></i> Open Link
                    </a>
                </div>
            @endif
            
            <!-- Attachments -->
            @if(!empty($attachmentUrls))
                <div class="attachment-section">
                    <h5><i class="bi bi-paperclip"></i> Attachments</h5>
                    
                    @foreach($attachmentUrls as $index => $url)
                        @php
                            $fileName = $fileNames[$index] ?? 'Download';
                            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $isPdf = $extension === 'pdf';
                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'avi']);
                        @endphp
                        
                        <div class="attachment-item">
                            <div class="attachment-icon">
                                @if($isPdf)
                                    <i class="bi bi-file-earmark-pdf"></i>
                                @elseif($isImage)
                                    <i class="bi bi-file-earmark-image"></i>
                                @elseif($isVideo)
                                    <i class="bi bi-file-earmark-play"></i>
                                @else
                                    <i class="bi bi-file-earmark"></i>
                                @endif
                            </div>
                            
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $fileName }}</h6>
                                <small class="text-muted">{{ strtoupper($extension) }} File</small>
                            </div>
                            
                            <div class="attachment-actions">
                                @if($isPdf)
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="viewPdfInline('{{ $url }}')">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                @elseif($isImage)
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="viewImageInline('{{ $url }}')">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                @elseif($isVideo)
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="viewVideoInline('{{ $url }}')">
                                        <i class="bi bi-play"></i> Play
                                    </button>
                                @endif
                                
                                <a href="{{ $url }}" download="{{ $fileName }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            </div>
                        </div>
                        
                        @if($isPdf)
                            <div id="pdf-viewer-{{ $index }}" class="pdf-viewer mt-3" style="display: none;">
                                <iframe src="{{ $url }}" width="100%" height="600px" style="border: none; border-radius: 0.375rem;"></iframe>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
            
            <!-- Quiz Section -->
            @if($content->content_type === 'quiz' && isset($quiz))
                <div class="quiz-section">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-question-circle"></i> {{ $quiz->quiz_title }}</h5>
                        </div>
                        <div class="card-body">
                            @if($quiz->quiz_description)
                                <div class="alert alert-info">
                                    <strong>Description:</strong> {{ $quiz->quiz_description }}
                                </div>
                            @endif
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Total Questions:</strong> {{ $quiz->questions->count() }}</p>
                                    <p><strong>Time Limit:</strong> 
                                        @if($quiz->time_limit > 0)
                                            {{ $quiz->time_limit }} minutes
                                        @else
                                            No time limit
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Max Attempts:</strong> {{ $quiz->max_attempts }}</p>
                                    <p><strong>Attempts Used:</strong> {{ $quizAttempts->where('status', 'completed')->count() }} / {{ $quiz->max_attempts }}</p>
                                </div>
                            </div>
                            
                            @if($quizAttempts->where('status', 'completed')->count() > 0)
                                <div class="completed-attempts mb-3">
                                    <h6>Previous Attempts:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Attempt</th>
                                                    <th>Score</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($quizAttempts->where('status', 'completed')->sortByDesc('completed_at') as $attempt)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            @php
                                                                // Ensure score is calculated properly
                                                                if ($attempt->score <= 0 && $attempt->total_questions > 0 && isset($attempt->correct_answers)) {
                                                                    $calculatedScore = ($attempt->correct_answers / $attempt->total_questions) * 100;
                                                                    $displayScore = number_format($calculatedScore, 1);
                                                                } else {
                                                                    $displayScore = number_format($attempt->score, 1);
                                                                }
                                                                $scoreClass = $displayScore >= 75 ? 'success' : 'warning';
                                                            @endphp
                                                            <span class="badge bg-{{ $scoreClass }}">
                                                                {{ $displayScore }}%
                                                            </span>
                                                        </td>
                                                        <td>{{ $attempt->completed_at->format('M j, Y g:i A') }}</td>
                                                        <td>
                                                            <a href="{{ route('student.quiz.results', ['attemptId' => $attempt->attempt_id, 'content_id' => $content->id]) }}" 
                                                               class="btn btn-sm btn-outline-primary">
                                                                View Results
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="quiz-actions">
                                @if($hasActiveAttempt)
                                    <div class="alert alert-warning">
                                        <strong>You have an active quiz attempt!</strong>
                                        <p class="mb-2">Started: {{ $activeAttempt->started_at->format('M j, Y g:i A') }}</p>
                                        @if($quiz->time_limit > 0)
                                            @php
                                                $timeElapsed = $activeAttempt->started_at->diffInMinutes(now());
                                                $timeRemaining = max(0, $quiz->time_limit - $timeElapsed);
                                            @endphp
                                            <p class="mb-0">Time Remaining: 
                                                <span class="badge bg-{{ $timeRemaining > 10 ? 'success' : 'danger' }}">
                                                    {{ $timeRemaining }} minutes
                                                </span>
                                            </p>
                                        @endif
                                    </div>
                                    <a href="{{ route('student.quiz.take', $activeAttempt->attempt_id) }}" 
                                       class="btn btn-success btn-lg">
                                        <i class="bi bi-play-circle"></i> Continue Quiz
                                    </a>
                                @elseif($quizAttempts->where('status', 'completed')->count() >= $quiz->max_attempts)
                                    <div class="alert alert-danger">
                                        <strong>Maximum attempts reached!</strong>
                                        <p class="mb-0">You have used all {{ $quiz->max_attempts }} attempts for this quiz.</p>
                                    </div>
                                @else
                                    <div class="start-quiz-section">
                                        <div class="alert alert-success">
                                            <strong>Ready to start?</strong>
                                            <p class="mb-0">Click the button below to begin your quiz attempt.</p>
                                        </div>
                                        
                                        <button type="button" class="btn btn-primary btn-lg" onclick="confirmStartQuiz()">
                                            <i class="bi bi-play-circle"></i> Start Quiz
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Assignment Section -->
            @if($content->content_type === 'assignment' || $content->enable_submission)
                <div class="assignment-section">
                    <h5><i class="bi bi-clipboard-check"></i> Assignment Submission</h5>
                    
                    @if($content->submission_instructions)
                        <div class="alert alert-info">
                            <strong>Instructions:</strong> {{ $content->submission_instructions }}
                        </div>
                    @endif
                    
                    @php
                        // Check submission status
                        $hasSubmitted = false;
                        $hasDraft = false;
                        $latestSubmission = null;
                        
                        if (!empty($submissions)) {
                            foreach ($submissions as $submission) {
                                if ($submission->status === 'submitted' || $submission->status === 'graded' || $submission->status === 'reviewed') {
                                    $hasSubmitted = true;
                                    $latestSubmission = $submission;
                                    break;
                                } elseif ($submission->status === 'draft') {
                                    $hasDraft = true;
                                    $latestSubmission = $submission;
                                }
                            }
                        }
                    @endphp
                    
                    @if($hasSubmitted)
                        <!-- Assignment Already Submitted -->
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>
                                <strong>Assignment Submitted</strong><br>
                                <small>You have already submitted this assignment. 
                                @if($latestSubmission->status === 'graded')
                                    Your grade: <strong>{{ $latestSubmission->grade ?? 'Pending' }}</strong>
                                @else
                                    Status: <strong>{{ ucfirst($latestSubmission->status) }}</strong>
                                @endif
                                </small>
                            </div>
                        </div>
                        
                        @if($content->allow_multiple_submissions ?? false)
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                This assignment allows multiple submissions. You can submit again if needed.
                            </div>
                            <!-- Show form for multiple submissions -->
                            @include('student.content.partials.assignment-form')
                        @endif
                        
                    @elseif($hasDraft)
                        <!-- Draft Exists - Show Edit Form -->
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="bi bi-pencil-square me-2"></i>
                            <div>
                                <strong>Draft Available</strong><br>
                                <small>You have a draft for this assignment. Complete and submit it, or remove it to start over.</small>
                            </div>
                        </div>
                        
                        <!-- Show form with draft data -->
                        @include('student.content.partials.assignment-form', ['draft' => $latestSubmission])
                        
                    @else
                        <!-- No Submission - Show New Form -->
                        @include('student.content.partials.assignment-form')
                    @endif
                
                <!-- Submission History -->
                @if(!empty($submissions))
                    <div class="submission-history">
                        <h5><i class="bi bi-clock-history"></i> Submission History</h5>
                        
                        @foreach($submissions as $submission)
                            <div class="submission-item">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">Submission #{{ $submission->id }}</h6>
                                        <small class="text-muted">
                                            Submitted: {{ \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y g:i A') }}
                                        </small>
                                    </div>
                                    
                                    <span class="submission-status {{ $submission->grade ? 'status-graded' : 'status-submitted' }}">
                                        @if($submission->grade)
                                            Graded: {{ $submission->grade }}
                                        @else
                                            Submitted
                                        @endif
                                    </span>
                                </div>
                                
                                @if($submission->comments)
                                    <p class="mb-2"><strong>Notes:</strong> {{ $submission->comments }}</p>
                                @endif
                                
                                @if($submission->feedback)
                                    <div class="alert alert-info">
                                        <strong>Feedback:</strong> {{ $submission->feedback }}
                                    </div>
                                @endif
                                
                                @if(!empty($submission->files))
                                    <div class="submission-files">
                                        <strong>Files:</strong>
                                        <ul class="list-unstyled mt-1">
                                            @php
                                                $files = is_string($submission->files) ? json_decode($submission->files, true) : $submission->files;
                                                $files = is_array($files) ? $files : [];
                                            @endphp
                                            @foreach($files as $file)
                                                @php
                                                    $filePath = is_array($file) ? ($file['path'] ?? $file) : $file;
                                                @endphp
                                                <li>
                                                    <i class="bi bi-file-earmark"></i>
                                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank">
                                                        {{ basename($filePath) }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
    
    <!-- Image Viewer Modal -->
    <div class="modal fade" id="imageViewerModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Image Viewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Image" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Video Viewer Modal -->
    <div class="modal fade" id="videoViewerModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Video Player</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <video id="modalVideo" controls style="width: 100%; height: auto;">
                        <source id="modalVideoSource" src="" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Global variables
        window.myId = {{ session('user_id') ?? 'null' }};
        window.myName = @json(session('user_name') ?? 'Guest');
        window.isAuthenticated = {{ session('user_id') ? 'true' : 'false' }};
        window.userRole = @json(session('user_role') ?? 'guest');
        window.csrfToken = @json(csrf_token());
        
        // Handle calendar redirects on page load
        document.addEventListener('DOMContentLoaded', function() {
            handleCalendarRedirects();
        });
        
        // Handle calendar redirects
        function handleCalendarRedirects() {
            const calendarAssignmentId = sessionStorage.getItem('calendarAssignmentId');
            const calendarLessonId = sessionStorage.getItem('calendarLessonId');
            const calendarProgramName = sessionStorage.getItem('calendarProgramName');
            
            if (calendarAssignmentId || calendarLessonId) {
                // Clear the session storage
                sessionStorage.removeItem('calendarAssignmentId');
                sessionStorage.removeItem('calendarProgramName');
                sessionStorage.removeItem('calendarLessonId');
                sessionStorage.removeItem('calendarProgramId');
                sessionStorage.removeItem('calendarModuleId');
                sessionStorage.removeItem('calendarCourseId');
                sessionStorage.removeItem('calendarContentType');
                
                // Show notification about the redirect
                let message = 'Opened from calendar';
                if (calendarAssignmentId) {
                    message = `Assignment opened from calendar in ${calendarProgramName || 'course'}`;
                } else if (calendarLessonId) {
                    message = `Lesson opened from calendar in ${calendarProgramName || 'course'}`;
                }
                
                showNotification(message, 'info');
            }
        }
        
        // Helper function to generate video embed HTML
        function getVideoEmbedHtml(url) {
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                let videoId = '';
                if (url.includes('youtube.com/watch?v=')) {
                    videoId = url.split('youtube.com/watch?v=')[1].split('&')[0];
                } else if (url.includes('youtu.be/')) {
                    videoId = url.split('youtu.be/')[1].split('?')[0];
                }
                return `<iframe src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>`;
            } else if (url.includes('vimeo.com')) {
                const videoId = url.split('vimeo.com/')[1].split('/')[0];
                return `<iframe src="https://player.vimeo.com/video/${videoId}" frameborder="0" allowfullscreen></iframe>`;
            } else if (url.match(/\.(mp4|webm|ogg|mov|avi|mkv)$/i)) {
                return `<video controls><source src="${url}" type="video/mp4">Your browser does not support the video tag.</video>`;
            } else {
                return `<p class="text-center"><a href="${url}" target="_blank" class="btn btn-primary">Open Video</a></p>`;
            }
        }
        
        // Mark content as complete
        function markComplete(type, id, btn) {
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Marking...';
            
            // Prepare payload for content completion
            const payload = {
                content_id: {{ $content->id }},
                course_id: {{ $content->course_id ?? 'null' }},
                module_id: {{ $content->course->module_id ?? 'null' }}
            };
            
            fetch('/student/complete-content', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Completed';
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-success');
                    
                    // Add completion badge to header
                    const metaContainer = document.querySelector('.content-meta');
                    if (metaContainer && !metaContainer.querySelector('.bg-success')) {
                        const completedBadge = document.createElement('span');
                        completedBadge.className = 'content-badge bg-success';
                        completedBadge.innerHTML = '<i class="bi bi-check-circle"></i> Completed';
                        metaContainer.appendChild(completedBadge);
                    }
                    
                    showNotification('Content marked as complete!', 'success');
                } else {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    showNotification('Error marking content as complete', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.innerHTML = originalText;
                btn.disabled = false;
                showNotification('Error marking content as complete', 'error');
            });
        }
        
        // Submit assignment
        function submitAssignment() {
            const form = document.getElementById('assignmentSubmissionForm');
            const formData = new FormData(form);
            const submitBtn = event.target;
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
            
            fetch('/student/assignment/submit', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Assignment submitted successfully!', 'success');
                    form.reset();
                    // Reload page to show new submission
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Error submitting assignment', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error submitting assignment', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
        
        // Save draft
        function saveDraft() {
            const form = document.getElementById('assignmentSubmissionForm');
            const formData = new FormData(form);
            formData.append('is_draft', '1');
            
            fetch('/student/assignment/submit', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Draft saved successfully!', 'success');
                    // Reload page to show draft status
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Error saving draft', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error saving draft', 'error');
            });
        }
        
        // Remove draft
        function removeDraft() {
            if (!confirm('Are you sure you want to remove this draft? This action cannot be undone.')) {
                return;
            }
            
            const form = document.getElementById('assignmentSubmissionForm');
            const submissionId = form.querySelector('input[name="submission_id"]')?.value;
            
            if (!submissionId) {
                showNotification('No draft to remove', 'error');
                return;
            }
            
            fetch('/student/assignment/remove-draft', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ submission_id: submissionId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Draft removed successfully!', 'success');
                    // Reload page to show updated status
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Error removing draft', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error removing draft', 'error');
            });
        }
        
        // View PDF inline
        function viewPdfInline(url) {
            const index = Array.from(document.querySelectorAll('.attachment-item')).findIndex(item => 
                item.querySelector('a[href="' + url + '"]')
            );
            const viewer = document.getElementById(`pdf-viewer-${index}`);
            if (viewer) {
                viewer.style.display = viewer.style.display === 'none' ? 'block' : 'none';
            }
        }
        
        // View image in modal
        function viewImageInline(imageUrl) {
            const modal = new bootstrap.Modal(document.getElementById('imageViewerModal'));
            document.getElementById('modalImage').src = imageUrl;
            modal.show();
        }
        
        // View video in modal
        function viewVideoInline(videoUrl) {
            const modal = new bootstrap.Modal(document.getElementById('videoViewerModal'));
            document.getElementById('modalVideoSource').src = videoUrl;
            document.getElementById('modalVideo').load();
            modal.show();
        }
        
        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
        
        // Quiz Functions
        function confirmStartQuiz() {
            const modal = `
                <div class="modal fade" id="startQuizModal" tabindex="-1" aria-labelledby="startQuizModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="startQuizModalLabel">
                                    <i class="bi bi-question-circle"></i> Start Quiz
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning">
                                    <strong>Important Instructions:</strong>
                                    <ul class="mb-0 mt-2">
                                        @if(isset($quiz) && $quiz->time_limit > 0)
                                            <li>This quiz has a time limit of {{ $quiz->time_limit }} minutes</li>
                                        @endif
                                        <li>Once started, you cannot pause the quiz</li>
                                        <li>Make sure you have a stable internet connection</li>
                                        @if(isset($quiz))
                                            <li>You have {{ $quiz->max_attempts - $quizAttempts->where('status', 'completed')->count() }} attempt(s) remaining</li>
                                        @endif
                                    </ul>
                                </div>
                                <p>Are you ready to start the quiz?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="startQuiz()">
                                    <i class="bi bi-play-circle"></i> Start Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('startQuizModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Add modal to DOM
            document.body.insertAdjacentHTML('beforeend', modal);
            
            // Show modal
            const bootstrapModal = new bootstrap.Modal(document.getElementById('startQuizModal'));
            bootstrapModal.show();
        }
        
        function startQuiz() {
            const quizId = @json($quiz->quiz_id ?? null);
            if (!quizId) {
                showNotification('Quiz ID not found', 'error');
                return;
            }
            
            // Show loading
            const startBtn = document.querySelector('#startQuizModal .btn-primary');
            const originalText = startBtn.innerHTML;
            startBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Starting...';
            startBtn.disabled = true;
            
            fetch(`/student/quiz/${quizId}/start`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('startQuizModal'));
                    modal.hide();
                    
                    // Redirect to quiz
                    window.location.href = data.redirect;
                } else {
                    showNotification(data.message || 'Error starting quiz', 'error');
                    startBtn.innerHTML = originalText;
                    startBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error starting quiz', 'error');
                startBtn.innerHTML = originalText;
                startBtn.disabled = false;
            });
        }
    </script>
@endpush
