@extends('student.student-dashboard.student-dashboard-layout')

@section('title', $moduleData['title'] ?? 'Module')

@push('styles')
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<!-- Custom Module Styles -->
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .module-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    /* Module Header */
    .module-header {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(20px);
        color: #2c3e50;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.3);
        position: relative;
        overflow: hidden;
    }
    
    .module-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(102,126,234,0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.3; }
        50% { transform: scale(1.1); opacity: 0.5; }
    }
    
    .back-to-course {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        color: #667eea;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 600;
        transition: all 0.3s ease;
        padding: 8px 16px;
        border-radius: 50px;
        background: rgba(102,126,234,0.1);
        position: relative;
        z-index: 2;
    }
    
    .back-to-course:hover {
        color: #5a67d8;
        transform: translateX(-3px);
        background: rgba(102,126,234,0.2);
        text-decoration: none;
    }
    
    .module-title-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 20px;
        position: relative;
        z-index: 2;
    }
    
    .module-title {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 15px;
        color: #2c3e50;
        line-height: 1.2;
    }
    
    .module-icon {
        font-size: 2.2rem;
        color: #667eea;
    }
    
    .content-type-badge {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(102,126,234,0.3);
    }
    
    .module-meta {
        margin-top: 15px;
        opacity: 0.8;
        font-size: 1.1rem;
        font-weight: 500;
        position: relative;
        z-index: 2;
    }
    
    /* Module Content Area */
    .module-content-container {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.3);
        position: relative;
        overflow: hidden;
    }
    
    .module-content-container::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(118,75,162,0.05) 0%, transparent 70%);
        animation: pulse 6s ease-in-out infinite reverse;
    }
    
    .module-description {
        margin-bottom: 30px;
        line-height: 1.8;
        color: #4a5568;
        font-size: 1.1rem;
        position: relative;
        z-index: 2;
    }
    
    /* Attachment styles */
    .attachment-container {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
        position: relative;
        z-index: 2;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    
    .attachment-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        font-size: 1.4rem;
        color: #2d3748;
        font-weight: 700;
    }
    
    .attachment-icon {
        color: #667eea;
        font-size: 1.6rem;
    }
    
    .pdf-embed {
        width: 100%;
        height: 600px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        background: #f8f9fa;
    }
    
    .pdf-embed iframe {
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 12px;
    }
    
    .pdf-error {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 600px;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 20px;
    }
    
    .pdf-error i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #dc3545;
    }
    
    .button-group {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }
    
    .attachment-download {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
        box-shadow: 0 4px 15px rgba(102,126,234,0.3);
    }
    
    .attachment-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102,126,234,0.4);
        color: white;
        text-decoration: none;
    }
    
    .attachment-download.secondary {
        background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
        box-shadow: 0 4px 15px rgba(113,128,150,0.3);
    }
    
    .attachment-download.secondary:hover {
        box-shadow: 0 10px 30px rgba(113,128,150,0.4);
    }
    
    /* Content Type Specific Styles */
    .video-container {
        margin: 20px 0;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        position: relative;
        z-index: 2;
    }
    
    .video-container video {
        width: 100%;
        height: auto;
        min-height: 400px;
    }
    
    .external-link-container {
        background: linear-gradient(135deg, #e6fffa 0%, #b2f5ea 100%);
        border-radius: 15px;
        padding: 30px;
        margin: 20px 0;
        border-left: 5px solid #38b2ac;
        position: relative;
        z-index: 2;
    }
    
    .external-link-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #38b2ac 0%, #319795 100%);
        color: white;
        padding: 15px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-top: 15px;
        font-size: 1rem;
        box-shadow: 0 4px 15px rgba(56,178,172,0.3);
    }
    
    .external-link-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(56,178,172,0.4);
        color: white;
        text-decoration: none;
    }
    
    /* Assignment specific styles */
    .assignment-container {
        background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        border-radius: 15px;
        padding: 30px;
        margin: 20px 0;
        border-left: 5px solid #f56565;
        position: relative;
        z-index: 2;
    }
    
    .assignment-heading {
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: #c53030;
        font-weight: 700;
    }
    
    .assignment-instructions {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .assignment-meta {
        margin-bottom: 30px;
        display: flex;
        gap: 25px;
        flex-wrap: wrap;
    }
    
    .assignment-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #c53030;
        font-weight: 600;
    }
    
    /* Quiz/test specific styles */
    .quiz-container {
        background: linear-gradient(135deg, #faf5ff 0%, #e9d8fd 100%);
        border-radius: 15px;
        padding: 30px;
        margin: 20px 0;
        border-left: 5px solid #805ad5;
        position: relative;
        z-index: 2;
    }
    
    .quiz-heading {
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: #553c9a;
        font-weight: 700;
    }
    
    .quiz-meta {
        margin-bottom: 30px;
        display: flex;
        gap: 25px;
        flex-wrap: wrap;
    }
    
    .quiz-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #553c9a;
        font-weight: 600;
    }
    
    .start-quiz-btn {
        background: linear-gradient(135deg, #805ad5 0%, #6b46c1 100%);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
        box-shadow: 0 4px 15px rgba(128,90,213,0.3);
    }
    
    .start-quiz-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(128,90,213,0.4);
    }
    
    /* Navigation between modules */
    .module-navigation {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 40px;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 25px 35px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.3);
    }
    
    .nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%);
        color: #4a5568;
        padding: 12px 24px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
        box-shadow: 0 4px 15px rgba(226,232,240,0.5);
    }
    
    .nav-btn:hover {
        background: linear-gradient(135deg, #cbd5e0 0%, #a0aec0 100%);
        transform: translateY(-2px);
        color: #2d3748;
        text-decoration: none;
        box-shadow: 0 8px 25px rgba(203,213,224,0.6);
    }
    
    .nav-btn.next {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(72,187,120,0.3);
    }
    
    .nav-btn.next:hover {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        box-shadow: 0 10px 30px rgba(72,187,120,0.4);
        color: white;
    }
    
    .nav-btn.next.completed {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 15px rgba(102,126,234,0.3);
    }
    
    .nav-btn.next.completed:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        box-shadow: 0 10px 30px rgba(102,126,234,0.4);
    }
    
    /* Alert styles */
    .alert {
        padding: 20px;
        border-radius: 12px;
        margin-top: 25px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        z-index: 2;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
        border: 1px solid #9ae6b4;
        color: #22543d;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        border: 1px solid #feb2b2;
        color: #742a2a;
    }
    
    /* Loading states */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        border: 2px solid #667eea;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transform: translate(-50%, -50%);
    }
    
    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .module-container {
            padding: 15px;
        }
        
        .module-header {
            padding: 25px;
        }
        
        .module-title {
            font-size: 2rem;
        }
        
        .module-content-container {
            padding: 25px;
        }
        
        .module-navigation {
            flex-direction: column;
            gap: 15px;
        }
        
        .button-group {
            flex-direction: column;
        }
        
        .assignment-meta,
        .quiz-meta {
            flex-direction: column;
            gap: 15px;
        }
        
        .pdf-embed {
            height: 400px;
        }
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
                @switch($moduleData['type'])
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
                {{ $moduleData['title'] }}
            </h1>
            
            <span class="content-type-badge">
                {{ ucfirst($moduleData['type'] ?? 'Module') }}
            </span>
        </div>
        
        <div class="module-meta">
            <p>{{ $program->program_name }}</p>
        </div>
    </div>
    
    <!-- Module Content -->
    <div class="module-content-container">
        @if($moduleData['description'])
            <div class="module-description">
                <p>{{ $moduleData['description'] }}</p>
            </div>
        @endif
        
        @if($moduleData['attachment'])
            <div class="attachment-container">
                <h3 class="attachment-heading">
                    <i class="bi bi-file-earmark attachment-icon"></i>
                    Learning Material
                </h3>
                
                @php
                    $fileExtension = strtolower(pathinfo($moduleData['attachment'], PATHINFO_EXTENSION));
                    // Create the correct file URL
                    $fileUrl = asset('storage/' . $moduleData['attachment']);
                    
                    // Debug alternative paths
                    $alternativeUrl = url('storage/' . basename($moduleData['attachment']));
                    $directPath = url('storage/modules/' . basename($moduleData['attachment']));
                @endphp
                
                @if($fileExtension === 'pdf')
                    <div class="pdf-embed">
                        <iframe src="{{ $fileUrl }}" 
                                onload="markPDFAsViewed()" 
                                onerror="showPDFError(this)">
                        </iframe>
                    </div>
                    <div class="button-group">
                        <a href="{{ $fileUrl }}" class="attachment-download" download>
                            <i class="bi bi-download"></i> Download PDF
                        </a>
                        <a href="{{ $fileUrl }}" class="attachment-download secondary" target="_blank">
                            <i class="bi bi-eye"></i> View in New Tab
                        </a>
                    </div>
                @elseif(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <div class="image-container">
                        <img src="{{ $fileUrl }}" alt="Module Image" style="width: 100%; max-width: 800px; height: auto; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    </div>
                    <div class="button-group">
                        <a href="{{ $fileUrl }}" class="attachment-download" download>
                            <i class="bi bi-download"></i> Download Image
                        </a>
                        <a href="{{ $fileUrl }}" class="attachment-download secondary" target="_blank">
                            <i class="bi bi-eye"></i> View Full Size
                        </a>
                    </div>
                @elseif(in_array($fileExtension, ['mp4', 'webm', 'ogg']))
                    <div class="video-container">
                        <video controls>
                            <source src="{{ $fileUrl }}" type="video/{{ $fileExtension }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <div class="button-group">
                        <a href="{{ $fileUrl }}" class="attachment-download" download>
                            <i class="bi bi-download"></i> Download Video
                        </a>
                    </div>
                @else
                    <div class="file-info">
                        <i class="bi bi-file-earmark-text" style="font-size: 3rem; color: #667eea; margin-bottom: 15px;"></i>
                        <p>{{ basename($moduleData['attachment']) }}</p>
                        <p class="text-muted">{{ strtoupper($fileExtension) }} File</p>
                    </div>
                    <div class="button-group">
                        <a href="{{ $fileUrl }}" class="attachment-download" download>
                            <i class="bi bi-download"></i> Download File
                        </a>
                    </div>
                @endif
            </div>
        @endif
        
        @if(isset($moduleData['video_url']) && $moduleData['video_url'])
            <div class="video-container">
                <h3 class="attachment-heading">
                    <i class="bi bi-play-circle attachment-icon"></i>
                    Video Content
                </h3>
                
                @php
                    $videoUrl = $moduleData['video_url'];
                    $isYouTube = str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be');
                    
                    if ($isYouTube) {
                        // Extract YouTube video ID
                        $embedUrl = '';
                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $matches)) {
                            $embedUrl = 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0&modestbranding=1&controls=1&disablekb=1&fs=0';
                        }
                    }
                @endphp
                
                @if($isYouTube && !empty($embedUrl))
                    <div class="video-embed-container">
                        <iframe 
                            src="{{ $embedUrl }}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen
                            onload="videoLoaded()"
                            style="width: 100%; height: 450px; border-radius: 10px;">
                        </iframe>
                    </div>
                @else
                    <div class="video-player-container">
                        <video controls controlsList="nodownload" oncontextmenu="return false;">
                            <source src="{{ $videoUrl }}" type="video/mp4">
                            <source src="{{ $videoUrl }}" type="video/webm">
                            <source src="{{ $videoUrl }}" type="video/ogg">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @endif
            </div>
        @endif
        
        @switch($moduleData['type'])
            @case('assignment')
                <div class="assignment-container">
                    <h3 class="assignment-heading">
                        <i class="bi bi-pencil-square"></i>
                        {{ $moduleData['content_data']['assignment_title'] ?? 'Assignment' }}
                    </h3>
                    
                    <div class="assignment-instructions">
                        <p>{{ $moduleData['content_data']['assignment_instructions'] ?? 'No instructions provided.' }}</p>
                    </div>
                    
                    <div class="assignment-meta">
                        @if(isset($moduleData['content_data']['due_date']))
                            <div class="assignment-meta-item">
                                <i class="bi bi-calendar"></i>
                                Due: {{ \Carbon\Carbon::parse($moduleData['content_data']['due_date'])->format('M d, Y g:i A') }}
                            </div>
                        @endif
                        
                        @if(isset($moduleData['content_data']['max_points']))
                            <div class="assignment-meta-item">
                                <i class="bi bi-award"></i>
                                Points: {{ $moduleData['content_data']['max_points'] }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="assignment-submit">
                        <form id="assignmentSubmissionForm" enctype="multipart/form-data">
                            @csrf
                            <div class="file-upload-area" id="fileUploadArea">
                                <div class="upload-content">
                                    <i class="bi bi-cloud-upload upload-icon"></i>
                                    <p>Drag and drop your assignment file here or click to browse</p>
                                    <input type="file" id="assignmentFile" name="assignment_file" accept=".pdf,.doc,.docx,.txt,.zip,.jpg,.jpeg,.png" hidden>
                                    <small class="text-muted">Supported formats: PDF, DOC, DOCX, TXT, ZIP, JPG, PNG (Max: 10MB)</small>
                                </div>
                                <div class="upload-progress" id="uploadProgress" style="display: none;">
                                    <div class="progress-bar" id="progressBar"></div>
                                    <div class="progress-text" id="progressText">0%</div>
                                </div>
                                <div class="uploaded-file" id="uploadedFile" style="display: none;">
                                    <i class="bi bi-file-earmark-check"></i>
                                    <span id="fileName"></span>
                                    <button type="button" class="btn-remove" id="removeFile">×</button>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button type="submit" class="submit-assignment-btn" id="submitAssignmentBtn" disabled>
                                    <i class="bi bi-upload"></i> Submit Assignment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @break
                
            @case('quiz')
                <div class="quiz-container">
                    <h3 class="quiz-heading">
                        <i class="bi bi-question-circle"></i>
                        {{ $moduleData['content_data']['quiz_title'] ?? 'Quiz' }}
                    </h3>
                    
                    <p>{{ $moduleData['content_data']['quiz_description'] ?? 'No description provided.' }}</p>
                    
                    <div class="quiz-meta">
                        @if(isset($moduleData['content_data']['time_limit']))
                            <div class="quiz-meta-item">
                                <i class="bi bi-clock"></i>
                                Time Limit: {{ $moduleData['content_data']['time_limit'] }} minutes
                            </div>
                        @endif
                        
                        @if(isset($moduleData['content_data']['question_count']))
                            <div class="quiz-meta-item">
                                <i class="bi bi-question-circle"></i>
                                Questions: {{ $moduleData['content_data']['question_count'] }}
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
                    <h3 class="quiz-heading">
                        <i class="bi bi-clipboard-check"></i>
                        {{ $moduleData['content_data']['test_title'] ?? 'Test' }}
                    </h3>
                    
                    <p>{{ $moduleData['content_data']['test_description'] ?? 'No description provided.' }}</p>
                    
                    <div class="quiz-meta">
                        @if(isset($moduleData['content_data']['test_date']))
                            <div class="quiz-meta-item">
                                <i class="bi bi-calendar"></i>
                                Date: {{ \Carbon\Carbon::parse($moduleData['content_data']['test_date'])->format('M d, Y g:i A') }}
                            </div>
                        @endif
                        
                        @if(isset($moduleData['content_data']['duration']))
                            <div class="quiz-meta-item">
                                <i class="bi bi-clock"></i>
                                Duration: {{ $moduleData['content_data']['duration'] }} minutes
                            </div>
                        @endif
                        
                        @if(isset($moduleData['content_data']['total_marks']))
                            <div class="quiz-meta-item">
                                <i class="bi bi-award"></i>
                                Total Marks: {{ $moduleData['content_data']['total_marks'] }}
                            </div>
                        @endif
                    </div>
                    
                    <button class="start-quiz-btn">
                        <i class="bi bi-play-fill"></i> Start Test
                    </button>
                </div>
                @break
                
            @case('link')
                <div class="external-link-container">
                    <h3 class="quiz-heading" style="color: #38b2ac;">
                        <i class="bi bi-link"></i>
                        @if(isset($moduleData['content_data']['link_title']))
                            {{ $moduleData['content_data']['link_title'] }}
                        @elseif(isset($moduleData['content_data']['external_url']))
                            External Resource
                        @else
                            Link Content
                        @endif
                    </h3>
                    
                    @if(isset($moduleData['content_data']['link_description']))
                        <p>{{ $moduleData['content_data']['link_description'] }}</p>
                    @else
                        <p>Click the link below to access the external resource.</p>
                    @endif
                    
                    @if(isset($moduleData['content_data']['external_url']) && $moduleData['content_data']['external_url'])
                        <a href="{{ $moduleData['content_data']['external_url'] }}" target="_blank" class="external-link-btn">
                            <i class="bi bi-box-arrow-up-right"></i> Visit External Resource
                        </a>
                    @else
                        <div style="padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 10px; color: #721c24;">
                            <i class="bi bi-exclamation-triangle"></i> External URL not configured
                            <p style="margin-top: 10px; margin-bottom: 0; font-size: 0.9rem;">
                                Please contact your instructor to set up the external link.
                            </p>
                        </div>
                    @endif
                </div>
                @break
        @endswitch
    </div>
    
    <!-- Module Navigation -->
    <div class="module-navigation">
        <button class="nav-btn prev" onclick="window.history.back()">
            <i class="bi bi-arrow-left"></i> Previous
        </button>
        
        <a href="{{ route('student.course', ['courseId' => $moduleData['program_id']]) }}" class="nav-btn">
            <i class="bi bi-grid"></i> Course Contents
        </a>
        
        <button class="nav-btn next" id="markCompleteBtn">
            <i class="bi bi-hourglass-split" id="completeBtnIcon"></i>
            <span id="completeBtnText">Mark Complete</span>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let pdfViewed = false;
    
    // Track PDF viewing
    function markPDFAsViewed() {
        pdfViewed = true;
        console.log('PDF viewed');
        // Auto-complete the module after 5 seconds of viewing
        setTimeout(() => {
            if (pdfViewed) {
                showAutoCompleteNotification();
            }
        }, 5000);
    }
    
    // Show PDF error and fallback
    function showPDFError(iframe) {
        const container = iframe.parentElement;
        container.innerHTML = `
            <div class="pdf-error">
                <i class="bi bi-exclamation-triangle"></i>
                <p>Unable to display PDF</p>
                <p>The PDF file could not be loaded. Please try downloading it instead.</p>
            </div>
        `;
    }
    
    // Show auto-complete notification
    function showAutoCompleteNotification() {
        const markCompleteBtn = document.getElementById('markCompleteBtn');
        if (markCompleteBtn && !markCompleteBtn.classList.contains('completed')) {
            // Add a subtle notification
            const notification = document.createElement('div');
            notification.className = 'alert alert-info';
            notification.innerHTML = '<i class="bi bi-info-circle"></i> You can now mark this module as complete!';
            document.querySelector('.module-content-container').appendChild(notification);
            
            // Highlight the button
            markCompleteBtn.style.animation = 'pulse 2s ease-in-out 3';
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const markCompleteBtn = document.getElementById('markCompleteBtn');
        const completeBtnIcon = document.getElementById('completeBtnIcon');
        const completeBtnText = document.getElementById('completeBtnText');
        
        // Check if module is already completed
        @if(isset($moduleData['is_completed']) && $moduleData['is_completed'])
            markCompleteBtn.classList.add('completed');
            completeBtnIcon.className = 'bi bi-check-circle';
            completeBtnText.textContent = 'Completed';
            markCompleteBtn.disabled = true;
        @else
            // Initialize button state
            completeBtnIcon.className = 'bi bi-check-circle';
            completeBtnText.textContent = 'Mark Complete';
        @endif
        
        if (markCompleteBtn) {
            markCompleteBtn.addEventListener('click', function() {
                // Prevent multiple clicks
                if (markCompleteBtn.disabled) return;
                
                // Show loading state
                markCompleteBtn.disabled = true;
                markCompleteBtn.classList.add('loading');
                completeBtnIcon.className = 'bi bi-hourglass-split';
                completeBtnText.textContent = 'Saving...';
                
                // Make AJAX call to mark the module as complete
                const moduleId = {{ $moduleData['id'] }};
                const completionUrl = `/student/module/${moduleId}/complete`;
                
                console.log('Attempting to complete module:', moduleId);
                console.log('URL:', completionUrl);
                
                fetch(completionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update button to show completed state
                        markCompleteBtn.classList.remove('loading');
                        markCompleteBtn.classList.add('completed');
                        completeBtnIcon.className = 'bi bi-check-circle';
                        completeBtnText.textContent = 'Completed';
                        markCompleteBtn.disabled = true;
                        
                        // Show success message
                        showNotification(data.message || 'Module completed successfully!', 'success');
                        
                        // Update progress bar if available
                        if (data.progress_percentage) {
                            updateProgressBar(data.progress_percentage);
                        }
                        
                        // Redirect after 2 seconds
                        setTimeout(() => {
                            window.location.href = "{{ route('student.course', ['courseId' => $moduleData['program_id']]) }}";
                        }, 2000);
                    } else {
                        // Handle API errors
                        handleError(data.message || 'Failed to complete module');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    handleError('An error occurred while saving your progress');
                });
            });
        }
        
        // Assignment submission functionality
        const assignmentForm = document.getElementById('assignmentSubmissionForm');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const assignmentFile = document.getElementById('assignmentFile');
        const submitBtn = document.getElementById('submitAssignmentBtn');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const uploadedFile = document.getElementById('uploadedFile');
        const fileName = document.getElementById('fileName');
        const removeFile = document.getElementById('removeFile');
        
        if (fileUploadArea && assignmentFile) {
            // Click to browse files
            fileUploadArea.addEventListener('click', function() {
                assignmentFile.click();
            });
            
            // Drag and drop functionality
            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                fileUploadArea.classList.add('drag-over');
            });
            
            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                fileUploadArea.classList.remove('drag-over');
            });
            
            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                fileUploadArea.classList.remove('drag-over');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileSelection(files[0]);
                }
            });
            
            // File input change
            assignmentFile.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleFileSelection(e.target.files[0]);
                }
            });
            
            // Remove file
            if (removeFile) {
                removeFile.addEventListener('click', function() {
                    assignmentFile.value = '';
                    uploadedFile.style.display = 'none';
                    fileUploadArea.querySelector('.upload-content').style.display = 'block';
                    submitBtn.disabled = true;
                });
            }
        }
        
        // Handle assignment form submission
        if (assignmentForm) {
            assignmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!assignmentFile.files[0]) {
                    showNotification('Please select a file to submit', 'error');
                    return;
                }
                
                const formData = new FormData();
                formData.append('assignment_file', assignmentFile.files[0]);
                formData.append('module_id', {{ $moduleData['id'] }});
                formData.append('_token', '{{ csrf_token() }}');
                
                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';
                
                // Show progress
                uploadProgress.style.display = 'block';
                
                fetch('/student/assignment/submit', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Assignment submitted successfully!', 'success');
                        
                        // Auto-complete the module
                        setTimeout(() => {
                            if (markCompleteBtn && !markCompleteBtn.classList.contains('completed')) {
                                markCompleteBtn.click();
                            }
                        }, 1000);
                    } else {
                        showNotification(data.message || 'Failed to submit assignment', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while submitting your assignment', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-upload"></i> Submit Assignment';
                    uploadProgress.style.display = 'none';
                });
            });
        }
        
        function handleFileSelection(file) {
            // Validate file size (10MB limit)
            if (file.size > 10 * 1024 * 1024) {
                showNotification('File size must be less than 10MB', 'error');
                return;
            }
            
            // Show selected file
            fileName.textContent = file.name;
            uploadedFile.style.display = 'flex';
            fileUploadArea.querySelector('.upload-content').style.display = 'none';
            submitBtn.disabled = false;
        }
        
        function updateProgressBar(percentage) {
            // Update any progress bars on the page
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                bar.style.width = percentage + '%';
            });
            
            // Update progress text
            const progressTexts = document.querySelectorAll('.progress-text');
            progressTexts.forEach(text => {
                text.textContent = percentage + '%';
            });
        }
        
        function handleError(message) {
            // Reset button state
            markCompleteBtn.disabled = false;
            markCompleteBtn.classList.remove('loading');
            completeBtnIcon.className = 'bi bi-check-circle';
            completeBtnText.textContent = 'Mark Complete';
            
            // Show error message
            showNotification(message, 'error');
        }
        
        function showNotification(message, type) {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.alert');
            existingNotifications.forEach(notification => {
                if (notification.classList.contains('alert-success') || notification.classList.contains('alert-danger')) {
                    notification.remove();
                }
            });
            
            // Create new notification
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
            notification.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}`;
            
            // Add to page
            document.querySelector('.module-content-container').appendChild(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
        
        // Handle file download buttons
        const downloadButtons = document.querySelectorAll('.attachment-download');
        downloadButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Add loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-hourglass-split"></i> Loading...';
                this.disabled = true;
                
                // Reset after 2 seconds
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            });
        });
        
        // Handle video play events
        const videos = document.querySelectorAll('video');
        videos.forEach(video => {
            video.addEventListener('play', function() {
                console.log('Video started playing');
            });
            
            video.addEventListener('ended', function() {
                console.log('Video finished');
                showAutoCompleteNotification();
            });
            
            // Prevent video skipping
            video.addEventListener('seeking', function() {
                if (video.currentTime > video.duration * 0.9) {
                    console.log('Video watched sufficiently');
                    showAutoCompleteNotification();
                }
            });
        });
        
        // Track YouTube video events
        window.videoLoaded = function() {
            console.log('YouTube video loaded');
            // Auto-complete after 30 seconds of video being loaded
            setTimeout(() => {
                showAutoCompleteNotification();
            }, 30000);
        };
    });
    
    // Add CSS for loading states and assignment submission
    const style = document.createElement('style');
    style.textContent = `
        .alert-info {
            background: linear-gradient(135deg, #e6f3ff 0%, #b3d9ff 100%);
            border: 1px solid #7db8e8;
            color: #1a5490;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .loading {
            position: relative;
            pointer-events: none;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            transform: translate(-50%, -50%);
        }
        
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .file-info {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .image-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .video-embed-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            margin-bottom: 20px;
        }
        
        .video-embed-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 10px;
        }
        
        .video-player-container {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .video-player-container video {
            width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .file-upload-area {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            position: relative;
        }
        
        .file-upload-area:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .file-upload-area.drag-over {
            border-color: #667eea;
            background: #f0f4ff;
            transform: scale(1.02);
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .upload-progress {
            width: 100%;
            height: 20px;
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .progress-text {
            text-align: center;
            font-weight: bold;
            color: #667eea;
        }
        
        .uploaded-file {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px solid #28a745;
        }
        
        .uploaded-file i {
            color: #28a745;
            font-size: 1.5rem;
        }
        
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 1.2rem;
            line-height: 1;
        }
        
        .submit-assignment-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }
        
        .submit-assignment-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }
        
        .submit-assignment-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .submit-section {
            text-align: center;
        }
    `;
    document.head.appendChild(style);
</script>
@endpush
