@extends('admin.admin-dashboard-layout')

@section('title', 'Archived Content Management')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/admin/admin-modules-archived.css') }}">
@endpush

@section('content')
<div class="main-content-wrapper">
    <!-- Messages -->
    @if(session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="error-message">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error-message">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="modules-container">
        <!-- Header -->
        <div class="modules-header d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-archive me-3"></i>Archived Content</h1>
            <a href="{{ route('admin.modules.index') }}" class="back-to-modules-btn">
                <i class="fas fa-arrow-left"></i>Back to Modules
            </a>
        </div>

        <!-- Statistics Section -->
        <div class="stats-section" id="statsSection">
            <div class="stat-card">
                <div class="stat-number" id="totalArchivedStat">{{ $stats['total_archived'] ?? 0 }}</div>
                <div class="stat-label">Total Archived</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="archivedModulesStat">{{ $stats['archived_modules'] ?? 0 }}</div>
                <div class="stat-label">Archived Modules</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="archivedContentStat">{{ $stats['archived_content'] ?? 0 }}</div>
                <div class="stat-label">Archived Content</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="archivedCoursesStat">{{ $stats['archived_courses'] ?? 0 }}</div>
                <div class="stat-label">Courses with Archives</div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-group">
                <label for="programSelect">Program</label>
                <select id="programSelect" name="program_id" class="form-control">
                    <option value="">All Programs</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->program_id }}"
                            {{ request('program_id') == $program->program_id ? 'selected' : '' }}>
                            {{ $program->program_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="contentTypeFilter">Content Type</label>
                <select id="contentTypeFilter" class="form-control">
                    <option value="">All Types</option>
                    <option value="module">Modules</option>
                    <option value="assignment">Assignments</option>
                    <option value="quiz">Quizzes</option>
                    <option value="test">Tests</option>
                    <option value="link">Links</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="searchFilter">Search</label>
                <input type="text" id="searchFilter" class="form-control" placeholder="Search content...">
            </div>
            <div class="filter-group">
                <label for="sortFilter">Sort By</label>
                <select id="sortFilter" class="form-control">
                    <option value="recent">Recently Archived</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name">Name A-Z</option>
                    <option value="type">Content Type</option>
                </select>
            </div>
        </div>

        <!-- Content Display Area -->
        <div id="contentDisplayArea">
            @if(request('program_id'))
                <!-- Archived Modules Section -->
                @if(isset($archivedModules) && $archivedModules->count() > 0)
                    <div class="course-section expanded">
                        <div class="course-header">
                            <div class="course-title">
                                <span class="course-icon"><i class="fas fa-cube"></i></span>
                                Archived Modules
                            </div>
                            <div class="course-meta">
                                <div class="course-info">
                                    <span class="course-stat">
                                        <i class="fas fa-archive"></i>
                                        {{ $archivedModules->count() }} modules
                                    </span>
                                </div>
                                <div class="course-actions">
                                    <button class="expand-course-btn" onclick="toggleCourseSection(this)">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="course-content">
                            <div class="content-grid">
                                @foreach($archivedModules as $module)
                                    <div class="content-card module-content" data-type="module" data-name="{{ strtolower($module->module_name) }}">
                                        <div class="archived-badge">Archived</div>
                                        <div class="content-header">
                                            <h3 class="content-title">{{ $module->module_name }}</h3>
                                            <span class="content-type-icon module"><i class="fas fa-cube"></i></span>
                                        </div>
                                        <p class="content-description">{{ $module->module_description }}</p>
                                        <div class="content-footer">
                                            <span class="archived-date">
                                                <i class="fas fa-clock"></i>
                                                Archived {{ $module->updated_at->diffForHumans() }}
                                            </span>
                                            <div class="content-actions">
                                                <button class="restore-btn" onclick="restoreModule({{ $module->modules_id }})">
                                                    <i class="fas fa-undo"></i> Restore
                                                </button>
                                                <button class="delete-btn" onclick="deleteModule({{ $module->modules_id }})">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Courses with Archived Content -->
                @if(isset($archivedCourses) && $archivedCourses->count() > 0)
                    @foreach($archivedCourses as $course)
                        <div class="course-section collapsed" id="course-{{ $course->subject_id }}">
                            <div class="course-header">
                                <div class="course-title">
                                    <span class="course-icon"><i class="fas fa-book"></i></span>
                                    {{ $course->subject_name }}
                                </div>
                                <div class="course-meta">
                                    <div class="course-info">
                                        <span class="course-stat">
                                            <i class="fas fa-layer-group"></i>
                                            {{ $course->module->module_name ?? 'No Module' }}
                                        </span>
                                        <span class="course-stat">
                                            <i class="fas fa-archive"></i>
                                            {{ $course->contentItems->count() }} archived items
                                        </span>
                                    </div>
                                    <div class="course-actions">
                                        <button class="bulk-restore-btn" onclick="bulkRestoreCourse({{ $course->subject_id }})">
                                            <i class="fas fa-undo"></i> Restore All
                                        </button>
                                        <button class="expand-course-btn" onclick="toggleCourseSection(this)">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="course-content">
                                <div class="content-grid">
                                    @foreach($course->contentItems as $content)
                                        <div class="content-card {{ $content->content_type }}-content" 
                                             data-type="{{ $content->content_type }}" 
                                             data-name="{{ strtolower($content->content_title) }}">
                                            <div class="content-header">
                                                <h3 class="content-title">{{ $content->content_title }}</h3>
                                                <span class="content-type-icon {{ $content->content_type }}">
                                                    @switch($content->content_type)
                                                        @case('assignment')
                                                            <i class="fas fa-tasks"></i>
                                                            @break
                                                        @case('quiz')
                                                            <i class="fas fa-question-circle"></i>
                                                            @break
                                                        @case('test')
                                                            <i class="fas fa-clipboard-check"></i>
                                                            @break
                                                        @case('link')
                                                            <i class="fas fa-link"></i>
                                                            @break
                                                        @default
                                                            <i class="fas fa-file-alt"></i>
                                                    @endswitch
                                                </span>
                                            </div>
                                            <p class="content-description">{{ $content->content_description }}</p>
                                            
                                            @if($content->content_data)
                                                <div class="content-details">
                                                    @php $data = json_decode($content->content_data, true) ?? [] @endphp
                                                    @if($content->content_type === 'assignment' && !empty($data['due_date']))
                                                        <i class="fas fa-calendar"></i> Due: {{ \Carbon\Carbon::parse($data['due_date'])->format('M d, Y') }}
                                                    @elseif($content->content_type === 'quiz' && !empty($data['time_limit']))
                                                        <i class="fas fa-stopwatch"></i> {{ $data['time_limit'] }} minutes
                                                    @elseif($content->content_type === 'test' && !empty($data['test_date']))
                                                        <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($data['test_date'])->format('M d, Y') }}
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            <div class="content-footer">
                                                <span class="archived-date">
                                                    <i class="fas fa-clock"></i>
                                                    {{ $content->archived_at ? \Carbon\Carbon::parse($content->archived_at)->diffForHumans() : 'Recently archived' }}
                                                </span>
                                                <div class="content-actions">
                                                    @if($content->attachment_path || $content->content_url)
                                                        <button class="preview-btn" onclick="previewContent({{ $content->id }})">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    @endif
                                                    <button class="restore-btn" onclick="restoreContent({{ $content->id }})">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                    <button class="delete-btn" onclick="deleteContent({{ $content->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <!-- No archived content message -->
                @if((!isset($archivedModules) || $archivedModules->count() == 0) && (!isset($archivedCourses) || $archivedCourses->count() == 0))
                    <div class="no-modules">
                        No archived content found for this program.
                    </div>
                @endif
            @else
                <div class="select-program-msg">
                    <strong>Select a program above to view archived content</strong><br>
                    Choose from the dropdown menu to see archived modules and course content.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Loading spinner (hidden by default) -->
<div class="loading-spinner" id="loadingSpinner" style="display: none;">
    Loading archived content...
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Program selector change handler
    $('#programSelect').on('change', function() {
        const programId = $(this).val();
        const url = new URL(window.location);
        
        if (programId) {
            url.searchParams.set('program_id', programId);
        } else {
            url.searchParams.delete('program_id');
        }
        
        window.location.href = url.toString();
    });

    // Content type filter
    $('#contentTypeFilter').on('change', function() {
        filterContent();
    });

    // Search filter
    $('#searchFilter').on('input', function() {
        filterContent();
    });

    // Sort filter
    $('#sortFilter').on('change', function() {
        sortContent();
    });
});

// Toggle course section expand/collapse
function toggleCourseSection(button) {
    const courseSection = button.closest('.course-section');
    const icon = button.querySelector('i');
    
    if (courseSection.classList.contains('expanded')) {
        courseSection.classList.remove('expanded');
        courseSection.classList.add('collapsed');
        icon.className = 'fas fa-chevron-down';
    } else {
        courseSection.classList.remove('collapsed');
        courseSection.classList.add('expanded');
        icon.className = 'fas fa-chevron-up';
    }
}

// Filter content based on type and search
function filterContent() {
    const typeFilter = $('#contentTypeFilter').val();
    const searchFilter = $('#searchFilter').val().toLowerCase();
    const contentCards = $('.content-card');
    
    contentCards.each(function() {
        const card = $(this);
        const cardType = card.data('type');
        const cardName = card.data('name') || '';
        const cardText = card.find('.content-title, .content-description').text().toLowerCase();
        
        let showCard = true;
        
        // Apply type filter
        if (typeFilter && cardType !== typeFilter) {
            showCard = false;
        }
        
        // Apply search filter
        if (searchFilter && !cardText.includes(searchFilter) && !cardName.includes(searchFilter)) {
            showCard = false;
        }
        
        card.toggle(showCard);
    });
    
    // Hide empty course sections
    $('.course-section').each(function() {
        const section = $(this);
        const visibleCards = section.find('.content-card:visible');
        section.toggle(visibleCards.length > 0);
    });
}

// Sort content
function sortContent() {
    const sortBy = $('#sortFilter').val();
    
    $('.course-section .content-grid').each(function() {
        const grid = $(this);
        const cards = grid.find('.content-card').get();
        
        cards.sort(function(a, b) {
            const cardA = $(a);
            const cardB = $(b);
            
            switch(sortBy) {
                case 'name':
                    return cardA.find('.content-title').text().localeCompare(cardB.find('.content-title').text());
                case 'type':
                    return cardA.data('type').localeCompare(cardB.data('type'));
                case 'oldest':
                    // This would need additional data attributes for proper sorting
                    return 0;
                case 'recent':
                default:
                    return 0;
            }
        });
        
        grid.append(cards);
    });
}

// Restore module
function restoreModule(moduleId) {
    if (!confirm('Are you sure you want to restore this module?')) return;
    
    $.ajax({
        url: `/admin/modules/${moduleId}/toggle-archive`,
        method: 'POST',
        data: { 
            is_archived: false,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showMessage('Module restored successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showMessage('Error restoring module', 'error');
            }
        },
        error: function() {
            showMessage('Error restoring module', 'error');
        }
    });
}

// Restore content
function restoreContent(contentId) {
    if (!confirm('Are you sure you want to restore this content?')) return;
    
    $.ajax({
        url: `/admin/modules/content/${contentId}/restore`,
        method: 'POST',
        success: function(response) {
            if (response.success) {
                showMessage('Content restored successfully!', 'success');
                $(`[onclick="restoreContent(${contentId})"]`).closest('.content-card').fadeOut();
                updateStats();
            } else {
                showMessage('Error restoring content', 'error');
            }
        },
        error: function() {
            showMessage('Error restoring content', 'error');
        }
    });
}

// Bulk restore course content
function bulkRestoreCourse(courseId) {
    if (!confirm('Are you sure you want to restore all archived content for this course?')) return;
    
    $.ajax({
        url: `/admin/modules/course/${courseId}/bulk-restore`,
        method: 'POST',
        success: function(response) {
            if (response.success) {
                showMessage(response.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showMessage('Error restoring content', 'error');
            }
        },
        error: function() {
            showMessage('Error restoring content', 'error');
        }
    });
}

// Delete module
function deleteModule(moduleId) {
    if (!confirm('Are you sure you want to permanently delete this module? This action cannot be undone.')) return;
    
    $.ajax({
        url: `/admin/modules/${moduleId}`,
        method: 'DELETE',
        success: function(response) {
            if (response.success) {
                showMessage('Module deleted successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showMessage('Error deleting module', 'error');
            }
        },
        error: function() {
            showMessage('Error deleting module', 'error');
        }
    });
}

// Delete content
function deleteContent(contentId) {
    if (!confirm('Are you sure you want to permanently delete this content? This action cannot be undone.')) return;
    
    $.ajax({
        url: `/admin/modules/content/${contentId}/delete-archived`,
        method: 'DELETE',
        success: function(response) {
            if (response.success) {
                showMessage('Content deleted successfully!', 'success');
                $(`[onclick="deleteContent(${contentId})"]`).closest('.content-card').fadeOut();
                updateStats();
            } else {
                showMessage('Error deleting content', 'error');
            }
        },
        error: function() {
            showMessage('Error deleting content', 'error');
        }
    });
}

// Preview content
function previewContent(contentId) {
    // This would open a modal or new window to preview the content
    console.log('Preview content:', contentId);
    // Implementation depends on your existing preview system
}

// Update statistics
function updateStats() {
    const programId = $('#programSelect').val();
    
    $.ajax({
        url: '/admin/modules/archived-stats',
        method: 'GET',
        data: { program_id: programId },
        success: function(response) {
            if (response.success) {
                const stats = response.stats;
                $('#totalArchivedStat').text(stats.modules + stats.content);
                $('#archivedModulesStat').text(stats.modules);
                $('#archivedContentStat').text(stats.content);
            }
        }
    });
}

// Show message function
function showMessage(message, type) {
    const messageDiv = $(`<div class="${type}-message">${message}</div>`);
    $('.modules-container').prepend(messageDiv);
    
    setTimeout(() => {
        messageDiv.fadeOut(() => messageDiv.remove());
    }, 3000);
}
</script>
@endpush
