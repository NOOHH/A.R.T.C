@extends('student.student-dashboard.student-dashboard-layout')

@section('title', $program->program_name ?? 'Course')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  /* Student Course Layout - Based on Admin Module Structure */
  .student-course-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 0;
  }

  /* Header Section */
  .course-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 2rem;
    margin-bottom: 2rem;
  }

  .course-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
  }

  .course-header p {
    font-size: 1.1rem;
    margin: 0;
    opacity: 0.9;
  }

  /* Main Layout - Split View */
  .course-main-layout {
    display: flex;
    gap: 2rem;
    padding: 0 2rem;
    max-width: 1400px;
    margin: 0 auto;
    min-height: calc(100vh - 200px);
  }

  /* Left Panel - Module Navigation */
  .modules-panel {
    flex: 0 0 45%;
    background: white;
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    height: fit-content;
  }

  .modules-panel-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    font-weight: 600;
    font-size: 1.1rem;
  }

  /* Right Panel - Content Viewer */
  .content-viewer-panel {
    flex: 1;
    background: white;
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 600px;
  }

  .content-viewer-header {
    background: linear-gradient(135deg, #17a2b8 0%, #6610f2 100%);
    color: white;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .content-viewer-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
  }

  .content-navigation {
    display: flex;
    gap: 0.5rem;
  }

  .nav-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-size: 0.875rem;
  }

  .nav-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
  }

  .nav-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .content-viewer-body {
    flex: 1;
    padding: 2rem;
    overflow-y: auto;
  }

  /* Override System Styles for Students */
  .locked-item {
    opacity: 0.6;
    position: relative;
    pointer-events: none;
    cursor: not-allowed;
  }

  .locked-item::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(220, 53, 69, 0.1);
    border: 2px dashed #dc3545;
    border-radius: 8px;
    pointer-events: none;
    z-index: 1;
  }

  .lock-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(220, 53, 69, 0.9);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  }

  .lock-overlay.scheduled {
    background: rgba(255, 193, 7, 0.9);
    color: #000;
  }

  .lock-overlay.prerequisite {
    background: rgba(108, 117, 125, 0.9);
    color: white;
  }

  .locked-item .module-header {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
  }

  .locked-item .course-header {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
  }

  .locked-item .content-item {
    background: #f8f9fa !important;
    border-color: #6c757d !important;
  }

  .content-placeholder {
    text-align: center;
    color: #6c757d;
    padding: 4rem 2rem;
  }

  .content-placeholder i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.3;
  }

  /* Module Structure - Same as Admin */
  .modules-hierarchy {
    padding: 0;
  }

  .module-container {
    border-bottom: 1px solid #e1e5e9;
    background: white;
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .module-container:last-child {
    border-bottom: none;
  }

  .module-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
  }

  .module-header:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
  }

  .module-header.active {
    background: linear-gradient(135deg, #4c63d2 0%, #5e3a7e 100%);
  }

  .module-title {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .module-number {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
  }

  .module-number.completed {
    background: #28a745;
  }

  .module-info h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
  }

  .module-info p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
  }

  .module-toggle {
    transition: transform 0.3s ease;
  }

  .module-toggle.expanded {
    transform: rotate(90deg);
  }

  /* Course Content */
  .module-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
  }

  .module-content.expanded {
    max-height: 1000px;
  }

  .courses-container {
    padding: 0;
  }

  .course-item {
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s ease;
  }

  .course-item:last-child {
    border-bottom: none;
  }

  .course-header {
    padding: 1.5rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
    transition: all 0.2s ease;
  }

  .course-header:hover {
    background: #e9ecef;
  }

  .course-header.active {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
  }

  .course-info {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .course-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #2196f3;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
  }

  .course-details h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #333;
  }

  .course-details small {
    color: #6c757d;
    font-size: 0.875rem;
  }

  .course-toggle {
    transition: transform 0.3s ease;
    color: #6c757d;
  }

  .course-toggle.expanded {
    transform: rotate(90deg);
  }

  /* Content Items */
  .content-list {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    background: #f8f9fa;
  }

  .content-list.expanded {
    max-height: 800px;
  }

  .content-item {
    padding: 1rem 2rem 1rem 4rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .content-item:hover {
    background: #e9ecef;
  }

  .content-item.active {
    background: #d4edda;
    border-left: 4px solid #28a745;
  }

  .content-item:last-child {
    border-bottom: none;
  }

  .content-type-icon {
    width: 1.5rem;
    height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .content-type-badge {
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
  }

  .content-type-badge.video {
    background: #e3f2fd;
    color: #1976d2;
  }

  .content-type-badge.pdf {
    background: #fff3e0;
    color: #f57c00;
  }

  .content-type-badge.assignment {
    background: #f3e5f5;
    color: #7b1fa2;
  }

  .content-type-badge.lesson {
    background: #e8f5e8;
    color: #388e3c;
  }

  .content-type-badge.quiz {
    background: #fff8e1;
    color: #f9a825;
  }

  .content-type-badge.test {
    background: #ffebee;
    color: #d32f2f;
  }

  .content-title {
    flex: 1;
    font-size: 0.9rem;
    font-weight: 500;
    color: #333;
  }

  .content-status {
    font-size: 1rem;
    color: #28a745;
  }

  /* Loading States */
  .loading-indicator {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
  }

  .loading-indicator i {
    font-size: 2rem;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  /* Content Viewer Styles */
  .lesson-content {
    line-height: 1.6;
  }

  .lesson-content h1 {
    color: #333;
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }

  .video-container {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
  }

  .pdf-viewer {
    height: 600px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .pdf-viewer iframe {
    width: 100%;
    height: 100%;
    border: none;
  }

  .assignment-details {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 1rem;
  }

  .instructions {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #dee2e6;
  }

  /* Progress Indicators */
  .progress-ring {
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .progress-ring.completed {
    border-color: #28a745;
    background: #28a745;
    color: white;
  }

  .progress-ring i {
    font-size: 0.7rem;
  }

  /* Responsive Design */
  @media (max-width: 992px) {
    .course-main-layout {
      flex-direction: column;
      gap: 1rem;
    }

    .modules-panel {
      flex: none;
    }

    .content-viewer-panel {
      min-height: 400px;
    }
  }

  @media (max-width: 768px) {
    .course-main-layout {
      padding: 0 1rem;
    }

    .course-header {
      padding: 1.5rem 1rem;
    }

    .module-header {
      padding: 1rem;
    }

    .course-header {
      padding: 1rem;
    }

    .content-item {
      padding: 0.75rem 1rem 0.75rem 2rem;
    }
  }

/* Left Sidebar - Course Outline */
.course-outline {
    width: 320px;
    background: #2c3e50;
    color: white;
    border-right: 1px solid #34495e;
    overflow-y: auto;
    flex-shrink: 0;
}

.course-outline-header {
    padding: 1.5rem;
    background: #34495e;
    border-bottom: 1px solid #4a5f7a;
}

.course-outline-header h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #ecf0f1;
}

.course-outline-header p {
    margin: 0.5rem 0 0 0;
    font-size: 0.9rem;
    color: #bdc3c7;
}

/* Module List */
.modules-list {
    padding: 0;
}

.module-item {
    border-bottom: 1px solid #34495e;
}

.module-header {
    padding: 1rem 1.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background-color 0.2s;
    user-select: none;
}

.module-header:hover {
    background: #34495e;
}

.module-header.active {
    background: #3498db;
}

.module-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.module-number {
    background: #3498db;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
    flex-shrink: 0;
}

.module-number.completed {
    background: #27ae60;
}

.module-text {
    flex: 1;
}

.module-text h6 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
    color: #ecf0f1;
}

.module-text small {
    color: #bdc3c7;
    font-size: 0.8rem;
}

.module-toggle {
    color: #bdc3c7;
    transition: transform 0.2s;
}

.module-toggle.expanded {
    transform: rotate(90deg);
}

/* Course List */
.courses-list {
    background: #2c3e50;
    border-top: 1px solid #34495e;
    display: none;
}

.courses-list.expanded {
    display: block;
}

.course-item {
    padding: 0.75rem 1.5rem 0.75rem 3rem;
    cursor: pointer;
    border-bottom: 1px solid #34495e;
    transition: background-color 0.2s;
}

.course-item:hover {
    background: #34495e;
}

.course-item.active {
    background: #2980b9;
    border-left: 3px solid #3498db;
}

.course-item h6 {
    margin: 0;
    font-size: 0.85rem;
    color: #ecf0f1;
    font-weight: 500;
}

.course-item small {
    color: #bdc3c7;
    font-size: 0.75rem;
}

/* Content List */
.content-list {
    padding-left: 1rem;
    display: none;
}

.content-list.expanded {
    display: block;
}

.content-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: background-color 0.2s;
    font-size: 0.8rem;
}

.content-item:hover {
    background: #34495e;
}

.content-item.active {
    background: #2980b9;
    color: #3498db;
}

.content-icon {
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.content-status {
    margin-left: auto;
    color: #27ae60;
}

/* Main Content Area */
.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.content-header {
    background: white;
    padding: 1rem 2rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.content-title h3 {
    margin: 0;
    font-size: 1.4rem;
    color: #2c3e50;
}

.content-title p {
    margin: 0.25rem 0 0 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.content-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.action-btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
}

.prev-btn {
    background: #6c757d;
    color: white;
}

.prev-btn:hover {
    background: #5a6268;
    color: white;
}

.next-btn {
    background: #3498db;
    color: white;
}

.next-btn:hover {
    background: #2980b9;
    color: white;
}

.submit-btn {
    background: #27ae60;
    color: white;
}

.submit-btn:hover {
    background: #219a52;
    color: white;
}

/* Content Viewer */
.content-viewer {
    flex: 1;
    background: white;
    overflow: auto;
    padding: 0;
}

.content-frame {
    width: 100%;
    height: 100%;
    border: none;
    background: white;
}

.video-container {
    position: relative;
    width: 100%;
    height: 100%;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.video-frame {
    width: 100%;
    height: 100%;
    border: none;
}

.pdf-viewer {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lesson-content {
    padding: 2rem;
    max-width: 800px;
    margin: 0 auto;
}

.lesson-content h1 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.lesson-content h2 {
    color: #34495e;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.lesson-content p {
    line-height: 1.6;
    color: #555;
    margin-bottom: 1rem;
}

/* Welcome/Empty State */
.welcome-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    text-align: center;
    color: #6c757d;
}

.welcome-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}

.welcome-state h3 {
    margin-bottom: 0.5rem;
    color: #495057;
}

/* Progress Indicators */
.progress-circle {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #bdc3c7;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto;
}

.progress-circle.completed {
    background: #27ae60;
    border-color: #27ae60;
    color: white;
}

.progress-circle.in-progress {
    border-color: #3498db;
    background: #3498db;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .learning-platform {
        flex-direction: column;
        height: auto;
    }
    
    .course-outline {
        width: 100%;
        height: auto;
        max-height: 40vh;
    }
    
    .main-content {
        height: 60vh;
    }
}

/* Loading States */
.loading-spinner {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.spinner {
    width: 2rem;
    height: 2rem;
    border: 3px solid #e9ecef;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Content Type Badges */
.content-type-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-weight: 600;
    text-transform: uppercase;
    margin-right: 0.5rem;
}

.content-type-badge.video {
    background: #e74c3c;
    color: white;
}

.content-type-badge.pdf {
    background: #e67e22;
    color: white;
}

.content-type-badge.lesson {
    background: #3498db;
    color: white;
}

.content-type-badge.assignment {
    background: #9b59b6;
    color: white;
}

.content-type-badge.quiz {
    background: #f39c12;
    color: white;
}

.content-type-badge.link {
    background: #1abc9c;
    color: white;
}

  /* Lesson Section */
  .lesson-section {
    margin-bottom: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #007bff;
  }

  .lesson-title {
    font-size: 1rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .lesson-description {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
  }

  /* Progress Section */
  .progress-section {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  }
  
  .progress-bar-container {
    height: 12px;
    background: #e9ecef;
    border-radius: 6px;
    overflow: hidden;
    margin: 1rem 0;
  }
  
  .progress-bar-fill {
    height: 100%;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    transition: width 0.6s ease;
  }

  /* Additional Admin-Style CSS */
  .modules-hierarchy {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  .module-container {
    border: 2px solid #e1e5e9;
    border-radius: 15px;
    background: white;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }

  .module-container:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
  }

  /* Modal Styles */
  .content-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: transparent;
  }

  .modal-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 90vw;
    max-height: 90vh;
    overflow: hidden;
    position: relative;
    z-index: 10000;
  }

  .submission-modal {
    width: 600px;
  }

  .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: background-color 0.2s;
  }

  .modal-close:hover {
    background: rgba(255, 255, 255, 0.2);
  }

  .modal-content {
    padding: 2rem;
  }

  .submission-section {
    margin-bottom: 1.5rem;
  }

  .section-label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #333;
  }

  .file-upload-area {
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: border-color 0.2s;
  }

  .file-upload-area:hover {
    border-color: #007bff;
  }

  .upload-placeholder {
    color: #666;
  }

  .modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
  }

  .btn-secondary {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s;
  }

  .btn-secondary:hover {
    background: #5a6268;
  }

  .btn-primary {
    background: #007bff;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s;
  }

  .btn-primary:hover {
    background: #0056b3;
  }
</style>

@endpush

@section('content')
<div class="student-course-container">
    <!-- Header Section -->
    <div class="course-header">
        <h1><i class="bi bi-journal-bookmark"></i> {{ $program->program_name }}</h1>
        <p>Navigate through modules and view content in the viewer panel</p>
    </div>

    <!-- Main Layout - Split View -->
    <div class="course-main-layout">
        <!-- Left Panel - Module Navigation (Admin-style hierarchy) -->
        <div class="modules-panel">
            <div class="modules-panel-header">
                <i class="bi bi-list-nested"></i> Course Modules
            </div>
            
            <div class="modules-hierarchy">
                @if(isset($course['modules']) && count($course['modules']) > 0)
                    @foreach($course['modules'] as $index => $module)
                    @php
                        $isAccessible = $module['is_accessible'] ?? true;
                        $lockReason = $module['lock_reason'] ?? null;
                    @endphp
                    <div class="module-container {{ !$isAccessible ? 'locked-item' : '' }}" data-module-id="{{ $module['id'] ?? $index }}">
                        <div class="module-header" onclick="{{ $isAccessible ? "toggleModule('" . ($module['id'] ?? $index) . "')" : 'return false;' }}">
                            <div class="module-title">
                                <div class="module-number {{ isset($module['is_completed']) && $module['is_completed'] ? 'completed' : '' }}">
                                    {{ $index + 1 }}
                                    @if(!$isAccessible)
                                        <i class="bi bi-lock position-absolute" style="font-size: 0.7rem; top: -2px; right: -2px;"></i>
                                    @endif
                                </div>
                                <div class="module-info">
                                    <h3>{{ $module['name'] ?? $module['title'] ?? 'Module ' . ($index + 1) }}</h3>
                                    <p>{{ $module['description'] ?? 'Click to view courses and content' }}</p>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right module-toggle" id="module-toggle-{{ $module['id'] ?? $index }}"></i>
                        </div>
                        
                        @if(!$isAccessible && $lockReason)
                        <div class="lock-overlay">
                            <i class="bi bi-lock"></i>
                            <span>{{ $lockReason }}</span>
                        </div>
                        @endif
                        
                        <div class="module-content" id="module-content-{{ $module['id'] ?? $index }}">
                            <div class="loading-indicator" id="loading-{{ $module['id'] ?? $index }}" style="display: none;">
                                <i class="bi bi-arrow-clockwise"></i>
                                <p>Loading courses...</p>
                            </div>
                            
                            <div class="courses-container" id="courses-content-{{ $module['id'] ?? $index }}">
                                <!-- Courses will be dynamically loaded here -->
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="no-modules-message">
                        <div class="text-center p-4">
                            <i class="bi bi-journal-x" style="font-size: 3rem; color: #6c757d; margin-bottom: 1rem;"></i>
                            <h5>No Modules Available</h5>
                            <p class="text-muted">This course doesn't have any modules yet.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Panel - Content Viewer -->
        <div class="content-viewer-panel">
            <div class="content-viewer-header">
                <div>
                    <h3 id="content-title">Select Content to View</h3>
                    <small id="content-subtitle">Choose any content from the left panel</small>
                </div>
                <div class="content-navigation">
                    <button class="nav-btn" id="prev-btn" onclick="navigatePrevious()" style="display: none;">
                        <i class="bi bi-chevron-left"></i> Previous
                    </button>
                    <button class="nav-btn" id="next-btn" onclick="navigateNext()" style="display: none;">
                        Next <i class="bi bi-chevron-right"></i>
                    </button>
                    <button class="nav-btn nav-btn-primary" id="submit-btn" onclick="submitAssignment()" style="display: none;">
                        <i class="bi bi-upload"></i> Submit
                    </button>
                </div>
            </div>
            
            <div class="content-viewer-body">
                <div id="content-viewer" class="content-placeholder">
                    <i class="bi bi-play-circle"></i>
                    <h2>Welcome to Your Course</h2>
                    <p>Select any module from the left panel to start learning.<br>
                    Content will appear here including videos, PDFs, assignments, and more.</p>
                </div>
            </div>
        </div>
    </div>
</div>





<!-- Student-Style Modals for Student Interface -->
<div id="videoModal" class="content-modal" style="display: none;">
    <div class="modal-backdrop" onclick="closeVideoModal()"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h5 id="videoModalTitle">Video Content</h5>
            <button class="modal-close" onclick="closeVideoModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-content">
            <div class="video-player">
                <iframe id="videoFrame" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Student-Style Assignment Submission Modal -->
<div id="submissionModal" class="content-modal" style="display: none;">
    <div class="modal-backdrop" onclick="closeSubmissionModal()"></div>
    <div class="modal-container submission-modal">
        <div class="modal-header">
            <h5 id="submissionModalTitle">Submit Assignment</h5>
            <button class="modal-close" onclick="closeSubmissionModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-content">
            <form id="submissionForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="submission_content_id" name="content_id" value="">
                
                <div class="submission-section">
                    <label class="section-label">
                        <i class="bi bi-cloud-upload"></i>
                        Upload Your Work
                    </label>
                    <div class="file-upload-area">
                        <input type="file" id="submission_file" name="file" required>
                        <div class="upload-placeholder">
                            <i class="bi bi-file-earmark-arrow-up"></i>
                            <span>Choose file or drag & drop</span>
                        </div>
                    </div>
                    <div class="file-info" id="submission_file_info">
                        Loading submission requirements...
                    </div>
                </div>
                
                <div class="submission-section">
                    <label class="section-label" for="submission_comments">
                        <i class="bi bi-chat-left-text"></i>
                        Comments (Optional)
                    </label>
                    <textarea id="submission_comments" name="comments" rows="4" placeholder="Add any comments about your submission..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeSubmissionModal()">Cancel</button>
                    <button type="submit" id="submitAssignmentBtn" class="btn-primary">
                        <i class="bi bi-upload"></i>
                        Submit Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎓 Admin-Style Student Learning Platform - Initializing...');
    
    // Global variables
    let currentContentId = null;
    let currentModuleId = null;
    let currentCourseId = null;
    let contentHistory = [];
    let currentContentIndex = 0;
    
    // Module Management - Admin-style toggle
    window.toggleModule = function(moduleId) {
        console.log('📚 Toggling module:', moduleId);
        
        const moduleContent = document.getElementById(`module-content-${moduleId}`);
        const moduleToggle = document.getElementById(`module-toggle-${moduleId}`);
        const moduleHeader = document.querySelector(`[data-module-id="${moduleId}"] .module-header`);
        
        if (!moduleContent) return;
        
        const isExpanding = !moduleContent.classList.contains('expanded');
        
        // Close all other modules (admin-style behavior)
        document.querySelectorAll('.module-content.expanded').forEach(el => {
            if (el.id !== `module-content-${moduleId}`) {
                el.classList.remove('expanded');
                const otherModuleId = el.id.replace('module-content-', '');
                const otherToggle = document.getElementById(`module-toggle-${otherModuleId}`);
                const otherHeader = document.querySelector(`[data-module-id="${otherModuleId}"] .module-header`);
                if (otherToggle) otherToggle.classList.remove('expanded');
                if (otherHeader) otherHeader.classList.remove('active');
            }
        });
        
        // Toggle current module
        moduleContent.classList.toggle('expanded');
        if (moduleToggle) moduleToggle.classList.toggle('expanded');
        if (moduleHeader) moduleHeader.classList.toggle('active');
        
        if (isExpanding) {
            loadModuleCourses(moduleId);
            currentModuleId = moduleId;
        }
    };
    
    // Load module courses
    function loadModuleCourses(moduleId) {
        const loadingIndicator = document.getElementById(`loading-${moduleId}`);
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        
        if (!loadingIndicator || !coursesContent) return;
        
        loadingIndicator.style.display = 'block';
        coursesContent.style.display = 'none';
        
        fetch(`/student/module/${moduleId}/courses`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses) {
                    displayCourses(moduleId, data.courses);
                } else {
                    showNoCoursesMessage(moduleId);
                }
            })
            .catch(error => {
                console.error('Error loading courses:', error);
                showErrorMessage(moduleId);
            })
            .finally(() => {
                loadingIndicator.style.display = 'none';
                coursesContent.style.display = 'block';
            });
    }
    
    // Display courses in admin-style hierarchical structure
    function displayCourses(moduleId, courses) {
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        
        let coursesHtml = '';
        courses.forEach((course, index) => {
            const courseId = course.course_id || course.subject_id;
            const isAccessible = course.is_accessible !== false;
            const lockReason = course.lock_reason || null;
            
            // Check for content in both lessons and direct content items
            const hasLessonContent = course.lessons && course.lessons.some(lesson => lesson.content_items && lesson.content_items.length > 0);
            const hasDirectContent = course.direct_content_items && course.direct_content_items.length > 0;
            const hasContent = hasLessonContent || hasDirectContent;
            
            const lockedClass = !isAccessible ? 'locked-item' : '';
            const clickHandler = isAccessible ? `toggleCourse('${moduleId}', '${courseId}')` : 'return false;';
            
            coursesHtml += `
                <div class="course-item ${lockedClass}" data-course-id="${courseId}">
                    <div class="course-header" onclick="${clickHandler}">
                        <div class="course-info">
                            <div class="course-icon">
                                <i class="bi bi-book"></i>
                                ${!isAccessible ? '<i class="bi bi-lock position-absolute" style="font-size: 0.6rem; top: -2px; right: -2px; color: #dc3545;"></i>' : ''}
                            </div>
                            <div class="course-details">
                                <h5>${course.course_name || course.subject_name}</h5>
                                <small>${course.course_description || course.subject_description || 'Course materials and activities'}</small>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right course-toggle" id="course-toggle-${moduleId}-${courseId}"></i>
                    </div>
                    
                    ${!isAccessible && lockReason ? `
                    <div class="lock-overlay ${getLockType(lockReason)}">
                        <i class="bi bi-lock"></i>
                        <span>${lockReason}</span>
                    </div>
                    ` : ''}
                    
                    <div class="content-list" id="content-list-${moduleId}-${courseId}">
                        ${hasContent ? generateContentListHtml(course, moduleId, courseId) : '<div style="padding: 1rem; color: #6c757d; text-align: center;">No content available</div>'}
                    </div>
                </div>
            `;
        });
        
        coursesContent.innerHTML = coursesHtml;
    }

    // Helper function to determine lock type for styling
    function getLockType(lockReason) {
        if (lockReason.includes('Available on') || lockReason.includes('available on')) {
            return 'scheduled';
        } else if (lockReason.includes('Complete') || lockReason.includes('complete')) {
            return 'prerequisite';
        }
        return '';
    }
    
    // Toggle course content - admin-style
    window.toggleCourse = function(moduleId, courseId) {
        const contentList = document.getElementById(`content-list-${moduleId}-${courseId}`);
        const courseHeader = document.querySelector(`#content-list-${moduleId}-${courseId}`).previousElementSibling;
        const courseToggle = document.getElementById(`course-toggle-${moduleId}-${courseId}`);
        
        // Close other course contents in this module
        document.querySelectorAll(`[id^="content-list-${moduleId}-"]`).forEach(el => {
            if (el.id !== `content-list-${moduleId}-${courseId}`) {
                el.classList.remove('expanded');
                const otherCourseId = el.id.replace(`content-list-${moduleId}-`, '');
                const otherToggle = document.getElementById(`course-toggle-${moduleId}-${otherCourseId}`);
                const otherHeader = el.previousElementSibling;
                if (otherToggle) otherToggle.classList.remove('expanded');
                if (otherHeader) otherHeader.classList.remove('active');
            }
        });
        
        // Toggle current course
        if (contentList) contentList.classList.toggle('expanded');
        if (courseHeader) courseHeader.classList.toggle('active');
        if (courseToggle) courseToggle.classList.toggle('expanded');
        
        currentCourseId = courseId;
    };
    
    // Generate content list HTML - admin-style structure
    function generateContentListHtml(course, moduleId, courseId) {
        let html = '';
        
        // Add lessons and their content items
        if (course.lessons && course.lessons.length > 0) {
            course.lessons.forEach((lesson, lessonIndex) => {
                if (lesson.content_items && lesson.content_items.length > 0) {
                    // Add lesson header
                    html += `
                        <div class="lesson-section">
                            <div class="lesson-title">
                                <i class="bi bi-journal-text"></i>
                                ${lesson.lesson_name}
                            </div>
                            ${lesson.lesson_description ? `<div class="lesson-description">${lesson.lesson_description}</div>` : ''}
                        </div>
                    `;
                    
                    // Add lesson content items
                    lesson.content_items.forEach((item, index) => {
                        const typeIcon = getContentTypeIcon(item.content_type);
                        const statusIcon = item.completed ? '<div class="progress-ring completed"><i class="bi bi-check"></i></div>' : '<div class="progress-ring"></div>';
                        const isAccessible = item.is_accessible !== false;
                        const lockReason = item.lock_reason || null;
                        const lockedClass = !isAccessible ? 'locked-item' : '';
                        const clickHandler = isAccessible ? `loadContent('${item.id}', '${item.content_type}', '${item.content_title}')` : 'return false;';
                        
                        html += `
                            <div class="content-item ${item.completed ? 'completed' : ''} ${lockedClass}" onclick="${clickHandler}">
                                <div class="content-type-icon">
                                    <i class="bi ${typeIcon}"></i>
                                    ${!isAccessible ? '<i class="bi bi-lock position-absolute" style="font-size: 0.6rem; top: -2px; right: -2px; color: #dc3545;"></i>' : ''}
                                </div>
                                <span class="content-type-badge ${(item.content_type || 'content').toLowerCase()}">${(item.content_type || 'CONTENT').toUpperCase()}</span>
                                <span class="content-title">${item.content_title}</span>
                                <div class="content-status">${statusIcon}</div>
                                ${!isAccessible && lockReason ? `
                                <div class="lock-overlay ${getLockType(lockReason)}">
                                    <i class="bi bi-lock"></i>
                                    <span>${lockReason}</span>
                                </div>
                                ` : ''}
                            </div>
                        `;
                    });
                }
            });
        }
        
        // Add direct content items (not in lessons)
        if (course.direct_content_items && course.direct_content_items.length > 0) {
            course.direct_content_items.forEach((item, index) => {
                const typeIcon = getContentTypeIcon(item.content_type);
                const statusIcon = item.completed ? '<div class="progress-ring completed"><i class="bi bi-check"></i></div>' : '<div class="progress-ring"></div>';
                const isAccessible = item.is_accessible !== false;
                const lockReason = item.lock_reason || null;
                const lockedClass = !isAccessible ? 'locked-item' : '';
                const clickHandler = isAccessible ? `loadContent('${item.id}', '${item.content_type}', '${item.content_title}')` : 'return false;';
                
                html += `
                    <div class="content-item ${item.completed ? 'completed' : ''} ${lockedClass}" onclick="${clickHandler}">
                        <div class="content-type-icon">
                            <i class="bi ${typeIcon}"></i>
                            ${!isAccessible ? '<i class="bi bi-lock position-absolute" style="font-size: 0.6rem; top: -2px; right: -2px; color: #dc3545;"></i>' : ''}
                        </div>
                        <span class="content-type-badge ${(item.content_type || 'content').toLowerCase()}">${(item.content_type || 'CONTENT').toUpperCase()}</span>
                        <span class="content-title">${item.content_title}</span>
                        <div class="content-status">${statusIcon}</div>
                        ${!isAccessible && lockReason ? `
                        <div class="lock-overlay ${getLockType(lockReason)}">
                            <i class="bi bi-lock"></i>
                            <span>${lockReason}</span>
                        </div>
                        ` : ''}
                    </div>
                `;
            });
        }
        
        return html;
    }
    
    // Get content type icon
    function getContentTypeIcon(type) {
        const icons = {
            'video': 'bi-play-circle',
            'pdf': 'bi-file-pdf',
            'lesson': 'bi-journal-text',
            'assignment': 'bi-pencil-square',
            'quiz': 'bi-question-circle',
            'test': 'bi-clipboard-check',
            'link': 'bi-link-45deg'
        };
        return icons[type.toLowerCase()] || 'bi-file-text';
    }
    
    // Load content in main viewer
    window.loadContent = function(contentId, contentType, contentTitle) {
        console.log('📖 Loading content:', contentId, contentType, contentTitle);
        
        // Update header
        document.getElementById('content-title').textContent = contentTitle;
        document.getElementById('content-subtitle').textContent = `${contentType.toUpperCase()} • Module ${currentModuleId}`;
        
        // Show navigation buttons
        updateNavigationButtons();
        
        // Load content based on type
        const viewer = document.getElementById('content-viewer');
        
        switch(contentType.toLowerCase()) {
            case 'video':
                loadVideoContent(contentId, viewer);
                break;
            case 'pdf':
                loadPdfContent(contentId, viewer);
                break;
            case 'assignment':
                loadAssignmentContent(contentId, viewer);
                break;
            case 'lesson':
                loadLessonContent(contentId, viewer);
                break;
            case 'link':
                loadLinkContent(contentId, viewer);
                break;
            default:
                loadDefaultContent(contentId, viewer);
        }
        
        // Update current content tracking
        currentContentId = contentId;
        
        // Mark content as active
        document.querySelectorAll('.content-item.active').forEach(el => el.classList.remove('active'));
        document.querySelector(`[onclick*="${contentId}"]`).classList.add('active');
    };
    
    // Load video content
    function loadVideoContent(contentId, viewer) {
        fetch(`/student/content/${contentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const videoUrl = data.content.content_url || data.content.attachment_path;
                    viewer.innerHTML = `
                        <div class="video-container">
                            <iframe class="video-frame" src="${videoUrl}" allowfullscreen></iframe>
                        </div>
                    `;
                }
            });
    }
    
    // Load PDF content
    function loadPdfContent(contentId, viewer) {
        fetch(`/student/content/${contentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const pdfUrl = `/storage/${data.content.attachment_path}`;
                    viewer.innerHTML = `
                        <div class="pdf-viewer">
                            <iframe class="content-frame" src="${pdfUrl}"></iframe>
                        </div>
                    `;
                }
            });
    }
    
    // Load assignment content
    function loadAssignmentContent(contentId, viewer) {
        console.log('📖 Loading assignment content:', contentId);
        document.getElementById('submit-btn').style.display = 'inline-block';
        
        fetch(`/student/content/${contentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const assignment = data.content;
                    let contentHtml = `
                        <div class="lesson-content">
                            <h1>${assignment.content_title}</h1>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> This is an assignment. Complete the requirements and submit your work.
                            </div>
                            <div class="assignment-details">
                                <p><strong>Description:</strong> ${assignment.content_description || 'No description provided'}</p>
                                ${assignment.due_date ? `<p><strong>Due Date:</strong> ${new Date(assignment.due_date).toLocaleDateString()}</p>` : ''}
                                ${assignment.submission_instructions ? `<div class="instructions"><h4>Instructions:</h4><p>${assignment.submission_instructions}</p></div>` : ''}
                    `;
                    
                    // Add file attachment if available
                    if (assignment.attachment_path) {
                        const fileName = assignment.attachment_path.split('/').pop();
                        contentHtml += `
                            <div class="attachment-section mt-3">
                                <h5><i class="bi bi-paperclip"></i> Attachment</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <a href="/storage/${assignment.attachment_path}" target="_blank" class="btn btn-outline-primary">
                                            <i class="bi bi-download"></i> Download ${fileName}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    contentHtml += `
                            </div>
                        </div>
                    `;
                    
                    viewer.innerHTML = contentHtml;
                } else {
                    viewer.innerHTML = `<div class="alert alert-danger">Error loading assignment: ${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error loading assignment:', error);
                viewer.innerHTML = `<div class="alert alert-danger">Error loading assignment: ${error.message}</div>`;
            });
    }
    
    // Load lesson content
    function loadLessonContent(contentId, viewer) {
        console.log('📖 Loading lesson content:', contentId);
        
        fetch(`/student/content/${contentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const lesson = data.content;
                    let contentHtml = `
                        <div class="lesson-content">
                            <h1>${lesson.content_title}</h1>
                            <div class="lesson-body">
                                ${lesson.content_description ? `<p>${lesson.content_description}</p>` : ''}
                                ${lesson.content_text ? `<div>${lesson.content_text}</div>` : ''}
                            </div>
                    `;
                    
                    // Add file attachment if available
                    if (lesson.attachment_path) {
                        const fileName = lesson.attachment_path.split('/').pop();
                        const fileExt = fileName.split('.').pop().toLowerCase();
                        const fileUrl = `/storage/${lesson.attachment_path}`;
                        
                        contentHtml += `
                            <div class="attachment-section mt-3">
                                <h5><i class="bi bi-paperclip"></i> Lesson Material</h5>
                                <div class="card">
                                    <div class="card-body">
                        `;
                        
                        // Handle different file types
                        if (['pdf'].includes(fileExt)) {
                            contentHtml += `
                                <div class="pdf-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0"><i class="bi bi-file-pdf text-danger"></i> ${fileName}</h6>
                                            <small class="text-muted">PDF Document</small>
                                        </div>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                    <div class="pdf-container" style="border: 2px solid #dee2e6; border-radius: 6px; overflow: hidden; background: white;">
                                        <iframe src="${fileUrl}" 
                                                width="100%" 
                                                height="700px" 
                                                style="border: none; display: block;"
                                                frameborder="0">
                                            <p>Your browser does not support PDF viewing. <a href="${fileUrl}" target="_blank">Download the PDF</a></p>
                                        </iframe>
                                    </div>
                                </div>
                            `;
                        } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExt)) {
                            contentHtml += `
                                <div class="image-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0"><i class="bi bi-image text-primary"></i> ${fileName}</h6>
                                            <small class="text-muted">Image File</small>
                                        </div>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                    <div class="text-center">
                                        <img src="${fileUrl}" class="img-fluid" alt="${fileName}" 
                                             style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 5px; max-height: 600px;">
                                    </div>
                                </div>
                            `;
                        } else if (['mp4', 'webm', 'ogg'].includes(fileExt)) {
                            contentHtml += `
                                <div class="video-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0"><i class="bi bi-camera-video text-success"></i> ${fileName}</h6>
                                            <small class="text-muted">Video File</small>
                                        </div>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                    <video controls style="width: 100%; max-height: 500px; border: 1px solid #ddd; border-radius: 5px;">
                                        <source src="${fileUrl}" type="video/${fileExt}">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            `;
                        } else if (['mp3', 'wav', 'ogg'].includes(fileExt)) {
                            contentHtml += `
                                <div class="audio-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0"><i class="bi bi-music-note text-info"></i> ${fileName}</h6>
                                            <small class="text-muted">Audio File</small>
                                        </div>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                    <audio controls style="width: 100%; border: 1px solid #ddd; border-radius: 5px;">
                                        <source src="${fileUrl}" type="audio/${fileExt}">
                                        Your browser does not support the audio tag.
                                    </audio>
                                </div>
                            `;
                        } else if (['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'].includes(fileExt)) {
                            contentHtml += `
                                <div class="document-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="text-center p-4 border rounded bg-white">
                                        <i class="bi bi-file-earmark-word text-primary" style="font-size: 3rem;"></i>
                                        <h6 class="mt-3 mb-2">${fileName}</h6>
                                        <p class="text-muted mb-3">Document file - Click download to view</p>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                            <i class="bi bi-download"></i> Download Document
                                        </a>
                                    </div>
                                </div>
                            `;
                        } else {
                            contentHtml += `
                                <div class="file-viewer mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="text-center p-4 border rounded bg-white">
                                        <i class="bi bi-file-earmark text-muted" style="font-size: 3rem;"></i>
                                        <h6 class="mt-3 mb-2">${fileName}</h6>
                                        <p class="text-muted mb-3">File preview not available</p>
                                        <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary">
                                            <i class="bi bi-download"></i> Download File
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                        
                        contentHtml += `
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Add external link if available
                    if (lesson.content_url) {
                        contentHtml += `
                            <div class="link-section mt-3">
                                <h5><i class="bi bi-link-45deg"></i> External Resource</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <a href="${lesson.content_url}" target="_blank" class="btn btn-primary">
                                            <i class="bi bi-box-arrow-up-right"></i> Open Link
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    contentHtml += `</div>`;
                    viewer.innerHTML = contentHtml;
                } else {
                    viewer.innerHTML = `<div class="alert alert-danger">Error loading lesson: ${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error loading lesson:', error);
                viewer.innerHTML = `<div class="alert alert-danger">Error loading lesson: ${error.message}</div>`;
            });
    }
    
    // Navigation functions
    function updateNavigationButtons() {
        document.getElementById('prev-btn').style.display = 'inline-block';
        document.getElementById('next-btn').style.display = 'inline-block';
        document.getElementById('submit-btn').style.display = 'none';
    }
    
    window.navigatePrevious = function() {
        // Implementation for previous content
        console.log('Navigate to previous content');
    };
    
    window.navigateNext = function() {
        // Implementation for next content  
        console.log('Navigate to next content');
    };
    
    window.submitAssignment = function() {
        if (currentContentId) {
            openSubmissionModal(currentContentId, document.getElementById('content-title').textContent);
        }
    };
    
    // Modal functions
    window.openVideoModal = function(videoUrl, title) {
        const modal = document.getElementById('videoModal');
        const frame = document.getElementById('videoFrame');
        const titleElement = document.getElementById('videoModalTitle');
        
        if (modal && frame && titleElement) {
            titleElement.textContent = title;
            frame.src = videoUrl;
            modal.style.display = 'block';
        }
    };
    
    window.closeVideoModal = function() {
        const modal = document.getElementById('videoModal');
        const frame = document.getElementById('videoFrame');
        
        if (modal) modal.style.display = 'none';
        if (frame) frame.src = '';
    };
    
    window.openSubmissionModal = function(contentId, contentTitle) {
        const modal = document.getElementById('submissionModal');
        const titleElement = document.getElementById('submissionModalTitle');
        const contentIdInput = document.getElementById('submission_content_id');
        
        if (modal && titleElement && contentIdInput) {
            titleElement.textContent = `Submit: ${contentTitle}`;
            contentIdInput.value = contentId;
            modal.style.display = 'block';
        }
    };
    
    window.closeSubmissionModal = function() {
        const modal = document.getElementById('submissionModal');
        if (modal) modal.style.display = 'none';
    };
    
    // Handle submission form
    document.getElementById('submissionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = document.getElementById('submitAssignmentBtn');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';
        
        fetch('/student/submit-assignment', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assignment submitted successfully! 🎉');
                closeSubmissionModal();
            } else {
                alert('Error: ' + (data.message || 'Failed to submit assignment'));
            }
        })
        .catch(error => {
            console.error('Error submitting assignment:', error);
            alert('An error occurred while submitting. Please try again.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
    
    // Helper functions
    function showNoCoursesMessage(moduleId) {
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        coursesContent.innerHTML = '<div style="padding: 1rem; color: #6c757d; text-align: center;">No courses available</div>';
    }
    
    function showErrorMessage(moduleId) {
        const coursesContent = document.getElementById(`courses-content-${moduleId}`);
        coursesContent.innerHTML = '<div style="padding: 1rem; color: #dc3545; text-align: center;">Error loading courses</div>';
    }
    
    console.log('✅ Student Learning Platform - Ready!');
});
</script>
@endpush
