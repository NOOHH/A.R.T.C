@extends('professor.professor-layouts.professor-layout')

@section('title', 'Archived Modules')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --primary-color: #2563eb;
    --secondary-color: #64748b;
    --accent-color: #f59e0b;
    --danger-color: #ef4444;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --info-color: #06b6d4;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --gray-900: #0f172a;
    --border-radius: 12px;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

* {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.modules-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    background: linear-gradient(135deg, var(--gray-50) 0%, white 100%);
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 2.5rem;
    margin-bottom: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--info-color), var(--success-color));
}

.page-title {
    color: var(--gray-800);
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.025em;
}

.page-subtitle {
    color: var(--gray-600);
    font-size: 1.125rem;
    font-weight: 400;
    margin: 0;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.action-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: white;
    color: var(--gray-700);
    padding: 0.75rem 1.25rem;
    font-weight: 500;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    transition: all 0.2s ease;
    text-decoration: none;
    box-shadow: var(--shadow-sm);
    font-size: 0.875rem;
}

.back-btn:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
    color: var(--gray-800);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}

.info-alert {
    background: linear-gradient(135deg, var(--info-color) 0%, #0891b2 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: var(--shadow-md);
    border: none;
}

.info-alert i {
    font-size: 1.25rem;
    opacity: 0.9;
}

.controls-section {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-sm);
}

.control-group {
    display: flex;
    align-items: end;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.form-group {
    flex: 1;
    min-width: 300px;
}

.form-label {
    display: block;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    background: white;
    color: var(--gray-800);
    font-size: 0.875rem;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-sm);
}

.form-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.help-text {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray-500);
    font-size: 0.8rem;
    margin-top: 0.5rem;
}

.content-section {
    margin-bottom: 3rem;
}

.section-title {
    color: var(--gray-800);
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--gray-200);
}

.modules-grid,
.courses-grid,
.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.archived-module,
.archived-course,
.archived-content {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.2s ease;
    position: relative;
}

.archived-module::before,
.archived-course::before,
.archived-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--danger-color);
}

.archived-module:hover,
.archived-course:hover,
.archived-content:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.module-header {
    padding: 1.5rem 2rem;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}

.module-title-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.module-title {
    color: var(--gray-800);
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.4;
}

.module-description {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin: 0;
    line-height: 1.5;
}

.module-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.badge-mode {
    background: var(--primary-color);
    color: white;
}

.badge-batch {
    background: var(--info-color);
    color: white;
}

.badge-archived {
    background: var(--danger-color);
    color: white;
}

.module-footer,
.course-footer,
.content-footer {
    padding: 1rem 2rem;
    background: #fef2f2;
    border-top: 1px solid #fecaca;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.archived-timestamp {
    color: var(--danger-color);
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Course specific styles */
.course-header,
.content-header {
    padding: 1.5rem 2rem;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}

.course-title-row,
.content-title-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.course-title,
.content-title {
    color: var(--gray-800);
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.4;
}

.course-description,
.content-description {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin: 0.5rem 0;
    line-height: 1.5;
}

.course-meta,
.content-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 0.75rem;
}

.meta-item {
    color: var(--gray-600);
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    background: var(--gray-100);
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
}

.course-badges,
.content-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.badge-type {
    background: var(--info-color);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
    font-size: 2rem;
}

.empty-state-title {
    color: var(--gray-800);
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
}

.empty-state-description {
    color: var(--gray-600);
    font-size: 1rem;
    margin: 0 0 2rem 0;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--border-radius);
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-sm);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    background: #1d4ed8;
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .modules-container {
        padding: 1rem;
    }
    
    .page-header {
        padding: 2rem 1.5rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .action-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .control-group {
        flex-direction: column;
    }
    
    .form-group {
        min-width: auto;
    }
    
    .module-title-row {
        flex-direction: column;
        gap: 1rem;
    }
    
    .module-badges {
        justify-content: flex-start;
    }
}
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Alert Messages -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please correct the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="modules-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-archive"></i>
            Archived Modules
        </h1>
        <p class="page-subtitle">
            View and manage archived modules from your assigned programs with comprehensive details and easy navigation
        </p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <a href="{{ route('professor.modules.index') }}" class="back-btn">
            <i class="bi bi-arrow-left"></i>
            Back to Active Modules
        </a>
    </div>

    <!-- Information Alert -->
    <div class="info-alert">
        <i class="bi bi-info-circle"></i>
        <div>
            <strong>Professor Access:</strong> You can view archived modules for programs you are assigned to as a professor.
        </div>
    </div>

    <!-- Controls Section -->
    <div class="controls-section">
        <div class="control-group">
            <div class="form-group">
                <label for="programSelect" class="form-label">
                    <i class="bi bi-collection"></i>
                    Select Program
                </label>
                <select id="programSelect" name="program_id" class="form-select">
                    <option value="">-- Choose a program to view archived modules --</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->program_id }}"
                            {{ request('program_id') == $program->program_id ? 'selected' : '' }}>
                            {{ $program->program_name }}
                        </option>
                    @endforeach
                </select>
                <div class="help-text">
                    <i class="bi bi-lightbulb"></i>
                    Select a program to view its archived modules and related information
                </div>
            </div>
        </div>
    </div>

    <!-- Archived Content Display Area -->
    <div id="archivedContentArea">
        @if(request('program_id') && isset($modules))
            <!-- Archived Modules Section -->
            @if($modules->count() > 0)
                <div class="content-section">
                    <h2 class="section-title">
                        <i class="bi bi-collection"></i>
                        Archived Modules ({{ $modules->count() }})
                    </h2>
                    <div class="modules-grid">
                        @foreach($modules as $module)
                            <div class="archived-module" data-module-id="{{ $module->module_id }}">
                                <div class="module-header">
                                    <div class="module-title-row">
                                        <div class="module-info">
                                            <h3 class="module-title">{{ $module->module_name }}</h3>
                                            @if($module->module_description)
                                                <p class="module-description">{{ $module->module_description }}</p>
                                            @endif
                                        </div>
                                        <div class="module-badges">
                                            <span class="badge badge-archived">
                                                <i class="bi bi-archive"></i>
                                                Archived
                                            </span>
                                            <span class="badge badge-mode">
                                                <i class="bi bi-mortarboard"></i>
                                                {{ $module->learning_mode }}
                                            </span>
                                            @if($module->batch)
                                                <span class="badge badge-batch">
                                                    <i class="bi bi-people"></i>
                                                    {{ $module->batch->batch_name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="module-footer">
                                    <div class="archived-timestamp">
                                        <i class="bi bi-clock-history"></i>
                                        Archived on {{ $module->updated_at->format('M d, Y \a\t g:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Archived Courses Section -->
            @if($archivedCourses->count() > 0)
                <div class="content-section">
                    <h2 class="section-title">
                        <i class="bi bi-book"></i>
                        Archived Courses ({{ $archivedCourses->count() }})
                    </h2>
                    <div class="courses-grid">
                        @foreach($archivedCourses as $course)
                            <div class="archived-course" data-course-id="{{ $course->subject_id }}">
                                <div class="course-header">
                                    <div class="course-title-row">
                                        <div class="course-info">
                                            <h3 class="course-title">{{ $course->subject_name }}</h3>
                                            @if($course->subject_description)
                                                <p class="course-description">{{ $course->subject_description }}</p>
                                            @endif
                                            <div class="course-meta">
                                                <span class="meta-item">
                                                    <i class="bi bi-collection"></i>
                                                    Module: {{ $course->module->module_name }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="course-badges">
                                            <span class="badge badge-archived">
                                                <i class="bi bi-archive"></i>
                                                Archived Course
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="course-footer">
                                    <div class="archived-timestamp">
                                        <i class="bi bi-clock-history"></i>
                                        Archived on {{ $course->updated_at->format('M d, Y \a\t g:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Archived Content Items Section -->
            @if($archivedContent->count() > 0)
                <div class="content-section">
                    <h2 class="section-title">
                        <i class="bi bi-file-earmark-text"></i>
                        Archived Content Items ({{ $archivedContent->count() }})
                    </h2>
                    <div class="content-grid">
                        @foreach($archivedContent as $content)
                            <div class="archived-content" data-content-id="{{ $content->id }}">
                                <div class="content-header">
                                    <div class="content-title-row">
                                        <div class="content-info">
                                            <h3 class="content-title">{{ $content->title }}</h3>
                                            @if($content->description)
                                                <p class="content-description">{{ $content->description }}</p>
                                            @endif
                                            <div class="content-meta">
                                                <span class="meta-item">
                                                    <i class="bi bi-book"></i>
                                                    Course: {{ $content->course->subject_name }}
                                                </span>
                                                <span class="meta-item">
                                                    <i class="bi bi-collection"></i>
                                                    Module: {{ $content->course->module->module_name }}
                                                </span>
                                                <span class="meta-item">
                                                    <i class="bi bi-tag"></i>
                                                    Type: {{ ucfirst($content->content_type) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="content-badges">
                                            <span class="badge badge-archived">
                                                <i class="bi bi-archive"></i>
                                                Archived Content
                                            </span>
                                            <span class="badge badge-type">
                                                <i class="bi bi-file-earmark"></i>
                                                {{ ucfirst($content->content_type) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="content-footer">
                                    <div class="archived-timestamp">
                                        <i class="bi bi-clock-history"></i>
                                        Archived on {{ $content->updated_at->format('M d, Y \a\t g:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Empty State if no archived content -->
            @if($modules->count() == 0 && $archivedCourses->count() == 0 && $archivedContent->count() == 0)
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-archive"></i>
                    </div>
                    <h3 class="empty-state-title">No Archived Content Found</h3>
                    <p class="empty-state-description">
                        This program currently has no archived modules, courses, or content items. All content is active and available for use.
                    </p>
                    <a href="{{ route('professor.modules.index') }}?program_id={{ request('program_id') }}" class="btn-primary">
                        <i class="bi bi-plus-circle"></i>
                        View Active Content
                    </a>
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-collection"></i>
                </div>
                <h3 class="empty-state-title">Select a Program</h3>
                <p class="empty-state-description">
                    Choose a program from the dropdown above to view its archived modules, courses, and content items.
                </p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Program selector change handler with loading state
    const programSelect = document.getElementById('programSelect');
    const archivedContentArea = document.getElementById('archivedContentArea');
    
    programSelect.addEventListener('change', function() {
        const programId = this.value;
        
        // Show loading state
        if (programId) {
            archivedContentArea.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-arrow-clockwise" style="animation: spin 1s linear infinite;"></i>
                    </div>
                    <h3 class="empty-state-title">Loading Archived Content...</h3>
                    <p class="empty-state-description">
                        Please wait while we fetch the archived modules, courses, and content items for the selected program.
                    </p>
                </div>
            `;
            
            // Add CSS for loading animation if not exists
            if (!document.querySelector('#loading-animation-css')) {
                const style = document.createElement('style');
                style.id = 'loading-animation-css';
                style.textContent = `
                    @keyframes spin {
                        from { transform: rotate(0deg); }
                        to { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(style);
            }
            
            // Navigate to the filtered page
            setTimeout(() => {
                window.location.href = `{{ route('professor.modules.archived') }}?program_id=${programId}`;
            }, 500);
        } else {
            // Reset to default state
            window.location.href = `{{ route('professor.modules.archived') }}`;
        }
    });
    
    // Add smooth transitions for all archived content cards
    const archivedCards = document.querySelectorAll('.archived-module, .archived-course, .archived-content');
    archivedCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Enhanced accessibility
    programSelect.addEventListener('focus', function() {
        this.style.boxShadow = '0 0 0 3px rgba(37, 99, 235, 0.1)';
    });
    
    programSelect.addEventListener('blur', function() {
        this.style.boxShadow = 'var(--shadow-sm)';
    });
});
</script>
@endsection
