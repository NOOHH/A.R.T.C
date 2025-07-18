@extends('student.student-dashboard.student-dashboard-layout')

@section('title', $moduleData['title'])

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="{{ asset('css/student/student-modules.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Module Header -->
    <div class="module-header" style="background: transparent; padding: 20px 0; color: white;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2" style="color: white; font-weight: 700;">{{ $moduleData['title'] }}</h1>
                    <p class="mb-0" style="color: rgba(255, 255, 255, 0.8);">{{ $moduleData['description'] }}</p>
                </div>
                <div>
                    <a href="{{ route('student.course', ['courseId' => $program->program_id]) }}" class="back-btn" style="color: white; text-decoration: none; padding: 8px 16px; border: 1px solid white; border-radius: 5px;">
                        <i class="bi bi-arrow-left"></i> Back to Course
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Content Wrapper -->
    <div class="module-wrapper" style="padding: 20px; max-width: 1200px; margin: 0 auto;">
        @php
            // Get the first content title from available content items
        @endphp
        
        <!-- Content Title Dropdown Style Header -->
        

        <!-- Module Main Content (if any) -->
        @if(!empty($moduleData['content_data']['video_url']) || $moduleData['attachment'])
            <div class="course-card mb-4">
                <h4 class="section-title">Module Content</h4>
                
                @if(!empty($moduleData['content_data']['video_url']))
                    <div class="video-container">
                        <video controls>
                            <source src="{{ $moduleData['content_data']['video_url'] }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @endif

                @if($moduleData['attachment'])
                    <div class="d-flex align-items-center mb-3">
                        <div class="content-icon pdf">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Module Attachment</h6>
                            <small class="text-muted">Download the module materials</small>
                        </div>
                        <button class="content-btn view" onclick="openPdfModal('{{ $moduleData['attachment_url'] }}', 'Module Attachment')">
                            <i class="bi bi-eye"></i> View
                        </button>
                    </div>
                @endif
            </div>
        @endif

        <!-- Courses/Lessons Accordion -->
        @if(count($formattedCourses) > 0)
            @foreach($formattedCourses as $courseIndex => $course)
                <details class="lesson-accordion mb-3">
                    <summary class="lesson-header d-flex align-items-center justify-content-between" style="background: #f8f9fa; color: #333; padding: 15px 20px; border-radius: 12px; font-size: 1rem; font-weight: 500; margin-bottom: 0.5rem; cursor: pointer; border: 1px solid #dee2e6;">
                        <div class="d-flex align-items-center">
                            <div class="lesson-icon me-3">
                                <i class="bi bi-folder" style="color: #6c757d;"></i>
                            </div>
                            <div class="lesson-title">
                                LESSON {{ $courseIndex + 1 }}: {{ strtoupper($course['name'] ?? $course['title'] ?? 'UNTITLED LESSON') }}
                            </div>
                        </div>
                        <div class="toggle-icon">
                            <i class="bi bi-chevron-down" style="color: #6c757d;"></i>
                        </div>
                    </summary>
                    <div class="lesson-content" style="padding: 15px 20px; background: #fff; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 12px 12px;">
                        <!-- Lessons inside this course -->
                        @if(count($course['lessons']) > 0)
                            @foreach($course['lessons'] as $lessonIndex => $lesson)
                                <details class="lesson-accordion mb-2">
                                    <summary class="lesson-header d-flex align-items-center justify-content-between" style="background: #fff; color: #e74c3c; padding: 12px 16px; border-radius: 8px; font-size: 0.95rem; font-weight: 500; margin-bottom: 0.5rem; cursor: pointer; border: 1px solid #e74c3c;">
                                        <div class="d-flex align-items-center">
                                            <div class="lesson-icon me-2">
                                                <i class="bi bi-file-earmark-text" style="color: #e74c3c;"></i>
                                            </div>
                                            <div class="lesson-title">
                                                LESSON {{ $lessonIndex + 1 }}: {{ strtoupper($lesson['name'] ?? $lesson['lesson_name'] ?? 'KANGKONG CHIPS - MAIN LESSON') }}
                                            </div>
                                        </div>
                                        <div class="toggle-icon">
                                            <i class="bi bi-chevron-down" style="color: #e74c3c;"></i>
                                        </div>
                                    </summary>
                                    <div class="lesson-content" style="padding: 10px 16px; background: #fff;">
                                        <div class="d-flex align-items-center justify-content-between" style="padding: 8px 0;">
                                            <div class="d-flex align-items-center">
                                                <div class="lesson-icon me-2">
                                                    <i class="bi bi-file-earmark-text" style="color: #6c757d;"></i>
                                                </div>
                                                <span style="font-size: 0.9rem; color: #333;">LESSONS {{ $lessonIndex + 1 }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @php
                                                    $attachmentUrl = $lesson['attachment_url'] ?? 
                                                                   ($lesson['attachment_path'] ?? null ? asset('storage/' . $lesson['attachment_path']) : null) ??
                                                                   ($lesson['assignment_url'] ?? null);
                                                @endphp
                                                @if($attachmentUrl)
                                                    <button class="btn btn-sm" onclick="openPdfModal('{{ $attachmentUrl }}', 'Lesson {{ $lessonIndex + 1 }}: {{ $lesson['name'] ?? $lesson['lesson_name'] ?? 'Brief History Application' }}')" style="background: #ff8a80; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">
                                                        View
                                                    </button>
                                                @endif
                                                <input type="checkbox" class="form-check-input lesson-checkbox" 
                                                       data-lesson-id="{{ $lesson['id'] ?? $lessonIndex }}" 
                                                       data-content-type="lesson"
                                                       onchange="updateProgress()" 
                                                       style="margin-left: 8px;" />
                                            </div>
                                        </div>
                                    </div>
                                </details>
                                <!-- Assignment Item -->
                                @if((isset($lesson['assignment_url']) && $lesson['assignment_url']) || 
                                    (isset($lesson['assignment_attachment']) && $lesson['assignment_attachment']))
                                    <div class="d-flex align-items-center justify-content-between" style="padding: 8px 0; border-top: 1px solid #f1f1f1;">
                                        <div class="d-flex align-items-center">
                                            <div class="lesson-icon me-2">
                                                <i class="bi bi-clipboard-check" style="color: #6c757d;"></i>
                                            </div>
                                            <span style="font-size: 0.9rem; color: #333;">ASSIGNMENT {{ $lessonIndex + 1 }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @php
                                                $assignmentUrl = $lesson['assignment_url'] ?? 
                                                               ($lesson['assignment_attachment'] ?? null ? asset('storage/' . $lesson['assignment_attachment']) : null);
                                            @endphp
                                            @if($assignmentUrl)
                                                <button class="btn btn-sm" onclick="openPdfModal('{{ $assignmentUrl }}', 'Assignment {{ $lessonIndex + 1 }}')" style="background: #17a2b8; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">
                                                    View
                                                </button>
                                            @endif
                                            <input type="checkbox" class="form-check-input lesson-checkbox" 
                                                   data-lesson-id="{{ $lesson['id'] ?? $lessonIndex }}" 
                                                   data-content-type="assignment"
                                                   onchange="updateProgress()" 
                                                   style="margin-left: 8px;" />
                                        </div>
                                    </div>
                                @endif
                                <!-- Zoom Meeting Item -->
                                @if((isset($lesson['zoom_url']) && $lesson['zoom_url']) || 
                                    (isset($lesson['meeting_url']) && $lesson['meeting_url']))
                                    <div class="d-flex align-items-center justify-content-between" style="padding: 8px 0; border-top: 1px solid #f1f1f1;">
                                        <div class="d-flex align-items-center">
                                            <div class="lesson-icon me-2">
                                                <i class="bi bi-camera-video" style="color: #6c757d;"></i>
                                            </div>
                                            <span style="font-size: 0.9rem; color: #333;">ZOOM MEETING: LESSON {{ $lessonIndex + 2 }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ $lesson['zoom_url'] ?? $lesson['meeting_url'] }}" target="_blank" class="btn btn-sm" style="background: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">
                                                Join
                                            </a>
                                            <input type="checkbox" class="form-check-input lesson-checkbox" 
                                                   data-lesson-id="{{ $lesson['id'] ?? $lessonIndex }}" 
                                                   data-content-type="zoom"
                                                   onchange="updateProgress()" 
                                                   style="margin-left: 8px;" />
                                        </div>
                                    </div>
                                @endif
                                <!-- Video Content Item -->
                                @if((isset($lesson['video_url']) && $lesson['video_url']) ||
                                    (isset($lesson['lesson_video_url']) && $lesson['lesson_video_url']))
                                    <div class="d-flex align-items-center justify-content-between" style="padding: 8px 0; border-top: 1px solid #f1f1f1;">
                                        <div class="d-flex align-items-center">
                                            <div class="lesson-icon me-2">
                                                <i class="bi bi-play-circle" style="color: #6c757d;"></i>
                                            </div>
                                            <span style="font-size: 0.9rem; color: #333;">VIDEO: LESSON {{ $lessonIndex + 2 }} CONCEPT OF FUNCTIONS</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ $lesson['video_url'] ?? $lesson['lesson_video_url'] }}" target="_blank" class="btn btn-sm" style="background: #6f42c1; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">
                                                Visit
                                            </a>
                                            <input type="checkbox" class="form-check-input lesson-checkbox" 
                                                   data-lesson-id="{{ $lesson['id'] ?? $lessonIndex }}" 
                                                   data-content-type="video"
                                                   onchange="updateProgress()" 
                                                   style="margin-left: 8px;" />
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        <!-- Content Items from Course -->
                        @if(count($course['content_items']) > 0)
                            @foreach($course['content_items'] as $itemIndex => $item)
                                <div class="d-flex align-items-center justify-content-between" style="padding: 8px 0; border-top: 1px solid #f1f1f1;">
                                    <div class="d-flex align-items-center">
                                        <div class="lesson-icon me-2">
                                            @switch($item['type'] ?? 'file')
                                                @case('pdf')
                                                @case('file')
                                                    <i class="bi bi-file-earmark-pdf" style="color: #6c757d;"></i>
                                                    @break
                                                @case('video')
                                                    <i class="bi bi-play-circle" style="color: #6c757d;"></i>
                                                    @break
                                                @case('assignment')
                                                    <i class="bi bi-clipboard-check" style="color: #6c757d;"></i>
                                                    @break
                                                @case('quiz')
                                                    <i class="bi bi-question-circle" style="color: #6c757d;"></i>
                                                    @break
                                                @default
                                                    <i class="bi bi-file-earmark-text" style="color: #6c757d;"></i>
                                            @endswitch
                                        </div>
                                        <span style="font-size: 0.9rem; color: #333;">{{ strtoupper($item['title'] ?? $item['content_title'] ?? 'CONTENT ITEM') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $attachmentUrl = $item['attachment_url'] ?? 
                                                           ($item['attachment_path'] ?? null ? asset('storage/' . $item['attachment_path']) : null);
                                        @endphp
                                        @if($attachmentUrl)
                                            @if(in_array($item['type'] ?? 'file', ['pdf', 'file']))
                                                <button class="btn btn-sm" onclick="openPdfModal('{{ $attachmentUrl }}', '{{ $item['title'] ?? $item['content_title'] ?? 'Document' }}')" style="background: #ff8a80; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">
                                                    View
                                                </button>
                                            @else
                                                <a href="{{ $attachmentUrl }}" target="_blank" class="btn btn-sm" style="background: #ff8a80; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">
                                                    View
                                                </a>
                                            @endif
                                        @endif
                                        <input type="checkbox" class="form-check-input lesson-checkbox" 
                                               data-lesson-id="{{ $item['id'] ?? $itemIndex }}" 
                                               data-content-type="{{ $item['type'] ?? 'content' }}"
                                               onchange="updateProgress()" 
                                               style="margin-left: 8px;" />
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </details>
            @endforeach
        @else
            <div class="no-lessons">
                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                <p>No content available for this module yet.</p>
            </div>
        @endif

        <!-- Complete Module Button -->
        <div class="text-center mt-4">
            <button id="completeModuleBtn" class="btn btn-success" data-module-id="{{ $moduleData['id'] }}" 
                    style="background: #4caf50; border: none; padding: 12px 24px; border-radius: 8px; color: white; font-weight: 600;">
                <i class="bi bi-check-circle"></i> Mark Module as Complete
            </button>
        </div>
    </div>

    <!-- Bootstrap PDF Modal -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">PDF Viewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="pdfViewer" src="" width="100%" height="600" style="border: none; min-height: 70vh;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="downloadCurrentPdf()">
                        <i class="bi bi-download"></i> Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Toast -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="progressToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <strong class="me-auto">Progress Updated</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Your progress has been saved!
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Global variables for progress tracking
let currentPdfUrl = '';
let totalItems = 0;
let completedItems = 0;
let moduleProgress = 0;

// Initialize progress tracking
document.addEventListener('DOMContentLoaded', function() {
    initializeProgress();
    loadSavedProgress();
    
    // Handle module completion
    const completeBtn = document.getElementById('completeModuleBtn');
    if (completeBtn) {
        completeBtn.addEventListener('click', function() {
            completeModule();
        });
    }
});

// Initialize progress counting
function initializeProgress() {
    const checkboxes = document.querySelectorAll('.lesson-checkbox');
    totalItems = checkboxes.length;
    updateProgressDisplay();
}

// Load saved progress from localStorage
function loadSavedProgress() {
    const moduleId = {{ $moduleData['id'] }};
    const savedProgress = localStorage.getItem(`module_${moduleId}_progress`);
    
    if (savedProgress) {
        const progressData = JSON.parse(savedProgress);
        
        // Restore checkbox states
        progressData.completed.forEach(itemId => {
            const checkbox = document.querySelector(`[data-lesson-id="${itemId}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
        
        updateProgress();
    }
}

// Save progress to localStorage and server
function saveProgress() {
    const moduleId = {{ $moduleData['id'] }};
    const checkboxes = document.querySelectorAll('.lesson-checkbox');
    const completedItems = [];
    
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            completedItems.push(checkbox.dataset.lessonId);
        }
    });
    
    const progressData = {
        moduleId: moduleId,
        completed: completedItems,
        totalItems: totalItems,
        completedCount: completedItems.length,
        percentage: Math.round((completedItems.length / totalItems) * 100),
        lastUpdated: new Date().toISOString()
    };
    
    // Save to localStorage
    localStorage.setItem(`module_${moduleId}_progress`, JSON.stringify(progressData));
    
    // Send to server (optional)
    fetch('/api/student/module-progress', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(progressData)
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              showProgressToast();
          }
      }).catch(console.error);
}

// Update progress display and save
function updateProgress() {
    const checkboxes = document.querySelectorAll('.lesson-checkbox');
    completedItems = Array.from(checkboxes).filter(cb => cb.checked).length;
    moduleProgress = totalItems > 0 ? Math.round((completedItems / totalItems) * 100) : 0;
    
    updateProgressDisplay();
    saveProgress();
    
    // Enable complete button if all items are checked
    const completeBtn = document.getElementById('completeModuleBtn');
    if (completeBtn) {
        if (moduleProgress === 100) {
            completeBtn.disabled = false;
            completeBtn.innerHTML = '<i class="bi bi-check-circle"></i> Complete Module';
            completeBtn.classList.add('btn-success');
        } else {
            completeBtn.disabled = true;
            completeBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Complete all items (${completedItems}/${totalItems})`;
            completeBtn.classList.remove('btn-success');
        }
    }
}

// Update progress display elements
function updateProgressDisplay() {
    // Update any progress bars or indicators on the page
    const progressText = `${completedItems}/${totalItems} items completed (${moduleProgress}%)`;
    console.log('Progress:', progressText);
}

// Show progress toast notification
function showProgressToast() {
    const toast = new bootstrap.Toast(document.getElementById('progressToast'));
    toast.show();
}

// Complete module function
function completeModule() {
    const moduleId = {{ $moduleData['id'] }};
    
    // Show confirmation
    if (!confirm('Are you sure you want to mark this module as complete?')) {
        return;
    }
    
    const completeBtn = document.getElementById('completeModuleBtn');
    const originalContent = completeBtn.innerHTML;
    completeBtn.disabled = true;
    completeBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Completing...';
    
    // Send AJAX request
    fetch(`/student/module/{{ $moduleData['id'] }}/complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            module_id: moduleId,
            progress_percentage: 100
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success || data.status === 'success') {
            completeBtn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Module Completed';
            completeBtn.className = 'btn btn-success';
            completeBtn.style.background = '#28a745';
            
            // Update localStorage to mark module as completed
            localStorage.setItem(`module_${moduleId}_completed`, 'true');
            
            // Show success message and redirect back to course
            alert('Module completed successfully! Redirecting to course page...');
            
            // Redirect back to the course page to update progress
            setTimeout(() => {
                window.location.href = "{{ route('student.course', ['courseId' => $program->program_id]) }}";
            }, 1500);
            
        } else {
            throw new Error(data.message || 'Failed to complete module');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        completeBtn.disabled = false;
        completeBtn.innerHTML = originalContent;
        alert('Module marked as complete! ' + (error.message || 'Please refresh the page to see the update.'));
        
        // Even on error, redirect to refresh the course page
        setTimeout(() => {
            window.location.href = "{{ route('student.course', ['courseId' => $program->program_id]) }}";
        }, 2000);
    });
}

// PDF Modal Functions with Bootstrap
function openPdfModal(pdfUrl, title) {
    currentPdfUrl = pdfUrl;
    const modal = new bootstrap.Modal(document.getElementById('pdfModal'));
    const modalTitle = document.getElementById('pdfModalLabel');
    const pdfViewer = document.getElementById('pdfViewer');
    
    modalTitle.textContent = title;
    
    // Check if the URL is a direct PDF link or needs to be embedded
    if (pdfUrl.toLowerCase().endsWith('.pdf')) {
        pdfViewer.src = pdfUrl + '#toolbar=1&navpanes=1&scrollbar=1&view=FitH';
    } else {
        // For non-PDF files, try to open in iframe anyway
        pdfViewer.src = pdfUrl;
    }
    
    modal.show();
}

// Download current PDF
function downloadCurrentPdf() {
    if (currentPdfUrl) {
        const link = document.createElement('a');
        link.href = currentPdfUrl;
        link.download = '';
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// Clear PDF viewer when modal is hidden
document.getElementById('pdfModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('pdfViewer').src = '';
    currentPdfUrl = '';
});
</script>
@endpush
