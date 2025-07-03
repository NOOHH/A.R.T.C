@extends('student.student-dashboard.student-dashboard-layout')

@section('title', $module['title'] ?? 'Module')

@push('styles')
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<!-- Custom Module Styles -->
<style>
    .module-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }
    
    /* Module Header */
    .module-header {
        background: linear-gradient(135deg, #3498db 0%, #8e44ad 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .back-to-course {
        display: inline-block;
        margin-bottom: 20px;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    
    .back-to-course:hover {
        color: white;
        transform: translateX(-3px);
    }
    
    .module-title-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .module-title {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .module-icon {
        font-size: 1.8rem;
    }
    
    .content-type-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        background: rgba(255,255,255,0.2);
    }
    
    .module-meta {
        margin-top: 15px;
        opacity: 0.9;
    }
    
    /* Module Content Area */
    .module-content-container {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .module-description {
        margin-bottom: 30px;
        line-height: 1.6;
        color: #555;
    }
    
    /* Attachment styles */
    .attachment-container {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .attachment-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        font-size: 1.2rem;
        color: #2c3e50;
    }
    
    .attachment-icon {
        color: #3498db;
        font-size: 1.4rem;
    }
    
    .attachment-download {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #3498db;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .attachment-download:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }
    
    /* Assignment specific styles */
    .assignment-container {
        border-top: 1px solid #e9ecef;
        padding-top: 30px;
        margin-top: 30px;
    }
    
    .assignment-heading {
        font-size: 1.4rem;
        margin-bottom: 20px;
        color: #2c3e50;
    }
    
    .assignment-instructions {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .assignment-meta {
        margin-bottom: 30px;
        display: flex;
        gap: 20px;
    }
    
    .assignment-meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #7f8c8d;
    }
    
    .assignment-submit {
        margin-top: 30px;
    }
    
    .submit-btn {
        background: #3498db;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .submit-btn:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }
    
    /* Quiz/test specific styles */
    .quiz-container {
        border-top: 1px solid #e9ecef;
        padding-top: 30px;
        margin-top: 30px;
    }
    
    .quiz-heading {
        font-size: 1.4rem;
        margin-bottom: 20px;
        color: #2c3e50;
    }
    
    .quiz-meta {
        margin-bottom: 30px;
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .quiz-meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #7f8c8d;
    }
    
    .start-quiz-btn {
        background: #3498db;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .start-quiz-btn:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }
    
    /* Link specific styles */
    .external-link-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #3498db;
        color: white;
        padding: 12px 25px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        margin-top: 20px;
    }
    
    .external-link-btn:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }
    
    /* Navigation between modules */
    .module-navigation {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
    }
    
    .nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #f1f2f6;
        color: #555;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .nav-btn:hover {
        background: #e2e5ec;
        transform: translateY(-2px);
    }
    
    .nav-btn.next {
        background: #3498db;
        color: white;
    }
    
    .nav-btn.next:hover {
        background: #2980b9;
    }
    
    /* PDF embed */
    .pdf-embed {
        width: 100%;
        height: 700px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
</style>
@endpush

@section('content')
<div class="module-container">
    <!-- Module Header -->
    <div class="module-header">
        <a href="{{ route('student.course', ['courseId' => $module['program_id']]) }}" class="back-to-course">
            <i class="bi bi-arrow-left"></i> Back to {{ $program->program_name }}
        </a>
        
        <div class="module-title-container">
            <h1 class="module-title">
                @switch($module['type'])
                    @case('module')
                        <i class="bi bi-book module-icon"></i>
                        @break
                    @case('assignment')
                        <i class="bi bi-pencil-square module-icon"></i>
                        @break
                    @case('quiz')
                        <i class="bi bi-question-circle module-icon"></i>
                        @break
                    @case('test')
                        <i class="bi bi-clipboard-check module-icon"></i>
                        @break
                    @case('link')
                        <i class="bi bi-link module-icon"></i>
                        @break
                    @default
                        <i class="bi bi-file-text module-icon"></i>
                @endswitch
                {{ $module['title'] }}
            </h1>
            
            <span class="content-type-badge">
                {{ ucfirst($module['type'] ?? 'Module') }}
            </span>
        </div>
        
        <div class="module-meta">
            <p>{{ $program->program_name }}</p>
        </div>
    </div>
    
    <!-- Module Content -->
    <div class="module-content-container">
        @if($module['description'])
            <div class="module-description">
                <p>{{ $module['description'] }}</p>
            </div>
        @endif
        
        @if($module['attachment'])
            <div class="attachment-container">
                <h3 class="attachment-heading">
                    <i class="bi bi-file-earmark attachment-icon"></i>
                    Learning Material
                </h3>
                
                @php
                    $fileExtension = pathinfo($module['attachment'], PATHINFO_EXTENSION);
                @endphp
                
                @if(strtolower($fileExtension) === 'pdf')
                    <div style="margin-bottom: 20px;">
                        <iframe class="pdf-embed" src="{{ asset('storage/' . $module['attachment']) }}"></iframe>
                    </div>
                @endif
                
                <a href="{{ asset('storage/' . $module['attachment']) }}" class="attachment-download" download>
                    <i class="bi bi-download"></i> Download Material
                </a>
            </div>
        @endif
        
        @switch($module['type'])
            @case('assignment')
                <div class="assignment-container">
                    <h3 class="assignment-heading">{{ $module['content_data']['assignment_title'] ?? 'Assignment' }}</h3>
                    
                    <div class="assignment-instructions">
                        <p>{{ $module['content_data']['assignment_instructions'] ?? 'No instructions provided.' }}</p>
                    </div>
                    
                    <div class="assignment-meta">
                        @if(isset($module['content_data']['due_date']))
                            <div class="assignment-meta-item">
                                <i class="bi bi-calendar"></i>
                                Due: {{ \Carbon\Carbon::parse($module['content_data']['due_date'])->format('M d, Y g:i A') }}
                            </div>
                        @endif
                        
                        @if(isset($module['content_data']['max_points']))
                            <div class="assignment-meta-item">
                                <i class="bi bi-award"></i>
                                Points: {{ $module['content_data']['max_points'] }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="assignment-submit">
                        <button class="submit-btn">
                            <i class="bi bi-upload"></i> Submit Assignment
                        </button>
                    </div>
                </div>
                @break
                
            @case('quiz')
                <div class="quiz-container">
                    <h3 class="quiz-heading">{{ $module['content_data']['quiz_title'] ?? 'Quiz' }}</h3>
                    
                    <p>{{ $module['content_data']['quiz_description'] ?? 'No description provided.' }}</p>
                    
                    <div class="quiz-meta">
                        @if(isset($module['content_data']['time_limit']))
                            <div class="quiz-meta-item">
                                <i class="bi bi-clock"></i>
                                Time Limit: {{ $module['content_data']['time_limit'] }} minutes
                            </div>
                        @endif
                        
                        @if(isset($module['content_data']['question_count']))
                            <div class="quiz-meta-item">
                                <i class="bi bi-question-circle"></i>
                                Questions: {{ $module['content_data']['question_count'] }}
                            </div>
                        @endif
                    </div>
                    
                    <button class="start-quiz-btn">
                        <i class="bi bi-play-fill"></i> Start Quiz
                    </button>
                </div>
                @break
                
            @case('test')
                <div class="quiz-container">
                    <h3 class="quiz-heading">{{ $module['content_data']['test_title'] ?? 'Test' }}</h3>
                    
                    <p>{{ $module['content_data']['test_description'] ?? 'No description provided.' }}</p>
                    
                    <div class="quiz-meta">
                        @if(isset($module['content_data']['test_date']))
                            <div class="quiz-meta-item">
                                <i class="bi bi-calendar"></i>
                                Date: {{ \Carbon\Carbon::parse($module['content_data']['test_date'])->format('M d, Y g:i A') }}
                            </div>
                        @endif
                        
                        @if(isset($module['content_data']['duration']))
                            <div class="quiz-meta-item">
                                <i class="bi bi-clock"></i>
                                Duration: {{ $module['content_data']['duration'] }} minutes
                            </div>
                        @endif
                        
                        @if(isset($module['content_data']['total_marks']))
                            <div class="quiz-meta-item">
                                <i class="bi bi-award"></i>
                                Total Marks: {{ $module['content_data']['total_marks'] }}
                            </div>
                        @endif
                    </div>
                    
                    <button class="start-quiz-btn">
                        <i class="bi bi-play-fill"></i> Start Test
                    </button>
                </div>
                @break
                
            @case('link')
                @if(isset($module['content_data']['external_url']))
                    <div class="link-container">
                        <h3>{{ $module['content_data']['link_title'] ?? 'External Resource' }}</h3>
                        
                        <p>{{ $module['content_data']['link_description'] ?? 'No description provided.' }}</p>
                        
                        <a href="{{ $module['content_data']['external_url'] }}" target="_blank" class="external-link-btn">
                            <i class="bi bi-box-arrow-up-right"></i> Visit External Resource
                        </a>
                    </div>
                @endif
                @break
        @endswitch
    </div>
    
    <!-- Module Navigation -->
    <div class="module-navigation">
        <button class="nav-btn prev" onclick="window.history.back()">
            <i class="bi bi-arrow-left"></i> Previous
        </button>
        
        <a href="{{ route('student.course', ['courseId' => $module['program_id']]) }}" class="nav-btn">
            <i class="bi bi-grid"></i> Course Contents
        </a>
        
        <button class="nav-btn next" id="markCompleteBtn">
            Mark Complete <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const markCompleteBtn = document.getElementById('markCompleteBtn');
        
        // If the module is already completed, update the button
        @if(isset($module['is_completed']) && $module['is_completed'])
            markCompleteBtn.innerHTML = 'Completed <i class="bi bi-check-circle"></i>';
            markCompleteBtn.classList.add('completed');
            markCompleteBtn.disabled = true;
        @endif
        
        if (markCompleteBtn) {
            markCompleteBtn.addEventListener('click', function() {
                // Disable the button during request to prevent multiple submissions
                markCompleteBtn.disabled = true;
                markCompleteBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
                
                // Make AJAX call to mark the module as complete
                fetch('{{ route('student.module.complete', ['moduleId' => $module['id']]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update button to show completed
                        markCompleteBtn.innerHTML = 'Completed <i class="bi bi-check-circle"></i>';
                        markCompleteBtn.classList.add('completed');
                        markCompleteBtn.disabled = true;
                        
                        // Show success message
                        const successMessage = document.createElement('div');
                        successMessage.className = 'alert alert-success mt-3';
                        successMessage.innerHTML = '<i class="bi bi-check-circle"></i> ' + data.message;
                        document.querySelector('.module-content-container').appendChild(successMessage);
                        
                        // Redirect after 2 seconds
                        setTimeout(() => {
                            window.location.href = "{{ route('student.course', ['courseId' => $module['program_id']]) }}";
                        }, 2000);
                    } else {
                        // Handle errors
                        markCompleteBtn.disabled = false;
                        markCompleteBtn.innerHTML = 'Mark Complete <i class="bi bi-arrow-right"></i>';
                        
                        // Show error message
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'alert alert-danger mt-3';
                        errorMessage.innerHTML = '<i class="bi bi-exclamation-triangle"></i> ' + (data.message || 'An error occurred.');
                        document.querySelector('.module-content-container').appendChild(errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Reset button
                    markCompleteBtn.disabled = false;
                    markCompleteBtn.innerHTML = 'Mark Complete <i class="bi bi-arrow-right"></i>';
                    
                    // Show error message
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'alert alert-danger mt-3';
                    errorMessage.innerHTML = '<i class="bi bi-exclamation-triangle"></i> An error occurred while saving your progress.';
                    document.querySelector('.module-content-container').appendChild(errorMessage);
                });
            });
        }
    });
</script>

<style>
    .nav-btn.next.completed {
        background-color: #28a745;
    }
    
    .alert {
        padding: 15px;
        border-radius: 5px;
        margin-top: 20px;
    }
    
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
</style>
@endpush
