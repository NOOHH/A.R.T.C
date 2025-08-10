@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Programs')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/admin/admin-programs/admin-programs.css') }}?v={{ time() }}">
@endpush

@push('scripts')
<script>
    // Pass session data to JavaScript
    window.sessionSuccess = @json(session('success'));
    window.sessionError = @json(session('error'));
</script>
@endpush

@section('content')
<!-- Analytics Cards Section -->
<div class="analytics-cards">
    <div class="analytics-card" style="background: #e3f2fd;">
        <div class="card-icon">🎓</div>
        <div class="card-content">
            <div class="card-number">{{ $totalPrograms ?? 0 }}</div>
            <div class="card-label">Total Programs</div>
            <div class="card-trend">
                @if(($newProgramsThisMonth ?? 0) > 0)
                    <span class="trend-up">↗ +{{ $newProgramsThisMonth }} this month</span>
                @else
                    <span class="trend-neutral">→ No change</span>
                @endif
            </div>
        </div>
    </div>

    <div class="analytics-card" style="background: #f3e5f5;">
        <div class="card-icon">👥</div>
        <div class="card-content">
            <div class="card-number">{{ $totalEnrollments ?? 0 }}</div>
            <div class="card-label">Total Enrollments</div>
            <div class="card-trend">
                @if(($newEnrollmentsThisWeek ?? 0) > 0)
                    <span class="trend-up">↗ +{{ $newEnrollmentsThisWeek }} this week</span>
                @else
                    <span class="trend-neutral">→ No change</span>
                @endif
            </div>
        </div>
    </div>

    <div class="analytics-card" style="background: #e8f5e8;">
        <div class="card-icon">📚</div>
        <div class="card-content">
            <div class="card-number">{{ $activePrograms ?? 0 }}</div>
            <div class="card-label">Active Programs</div>
            <div class="card-trend">
                @if(($archivedPrograms ?? 0) > 0)
                    <span class="trend-down">📁 {{ $archivedPrograms }} archived</span>
                @else
                    <span class="trend-neutral">→ All active</span>
                @endif
            </div>
        </div>
    </div>

    <div class="analytics-card" style="background: #fff3e0;">
        <div class="card-icon">📈</div>
        <div class="card-content">
            <div class="card-number">{{ number_format($avgEnrollmentPerProgram ?? 0, 1) }}</div>
            <div class="card-label">Avg Enrollment/Program</div>
            <div class="card-trend">
                @if(($completionRate ?? 0) > 0)
                    <span class="trend-up">✓ {{ $completionRate }}% completion</span>
                @else
                    <span class="trend-neutral">→ No data</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="main-dashboard-grid">
    <!-- Left Column -->
    <div class="left-column">
        <!-- Programs Management -->
        <div class="programs-container">
            <div class="programs-header">
                <h1>Programs Management</h1>
                <div class="header-buttons">
                    <a href="{{ route('admin.programs.archived') }}" class="view-archived-btn">
                        📁 View Archived
                    </a>
                </div>
            </div>

            <!-- Programs Grid -->
            <div class="programs-grid">
                @forelse($programs as $program)
                    <div class="program-card">
                        <div class="program-title">{{ $program->program_name }}</div>
                        
                        <div class="program-stats">
                            <div class="enrollment-count">
                                Enrolled Students: {{ $program->enrollments->count() }}
                            </div>
                        </div>

                        <div class="program-actions">
                            <button type="button" class="view-enrollees-btn" data-program-id="{{ $program->program_id }}">
                                👥 <span style="color: black;">View Enrollees</span>
                            </button>
                            <button type="button" class="archive-btn" data-program-id="{{ $program->program_id }}">
                                📁 Archive
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="no-programs">
                        No programs found.<br>
                        <small>Click "Add Program" to create your first program.</small>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="right-column">
        <!-- Quick Stats -->
        <div class="quick-stats-panel">
            <div class="panel-header">
                <h3>📊 Quick Stats</h3>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value">{{ $mostPopularProgram->program_name ?? 'N/A' }}</div>
                    <div class="stat-label">Most Popular Program</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $mostPopularProgram->enrollments_count ?? 0 }}</div>
                    <div class="stat-label">Enrollments</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $recentProgramsCount ?? 0 }}</div>
                    <div class="stat-label">Added This Week</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ number_format($avgProgramRating ?? 0, 1) }}</div>
                    <div class="stat-label">Avg Rating</div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="chart-panel">
            <div class="panel-header">
                <h3>📈 Program Analytics</h3>
                <div class="chart-controls">
                    <button class="chart-btn active" data-chart="enrollments">Enrollments</button>
                    <button class="chart-btn" data-chart="completion">Completion</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="programChart"></canvas>
            </div>
        </div>

        <!-- Activities -->
        <div class="activities-panel">
            <div class="panel-header">
        {{-- Inline script removed: moved to admin-programs.page.js --}}
    <div class="custom-modal">
        <h3>Create New Program</h3>
        <form action="{{ route('admin.programs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" name="program_name" placeholder="Program Name" required>
            <textarea name="program_description" placeholder="Program Description" rows="4" style="width: 100%; margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
            
            <!-- Program Image Upload -->
            <div class="image-upload-section" style="margin: 15px 0;">
                <label for="program_image" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">
                    Program Image (Optional)
                </label>
                <input type="file" 
                       name="program_image" 
                       id="program_image" 
                       accept="image/*"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">
                <small style="color: #666; font-size: 0.85em;">Recommended: 400x300px, max 2MB (JPG, PNG, WEBP)</small>
                
                <!-- Image Preview -->
                <div id="imagePreview" style="margin-top: 10px; display: none;">
                    <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                    <button type="button" id="removeImage" style="display: block; margin-top: 5px; background: #dc3545; color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; cursor: pointer;">Remove Image</button>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelAddModal">Cancel</button>
                <button type="submit" class="add-btn">Add Program</button>
            </div>
        </form>
    </div>
</div>

<!-- Enrollments Modal -->
<div class="modal-bg" id="enrollmentsModal">
    <div class="custom-modal">
        <h3>Enrolled Students</h3>
        <div class="loading" id="loadingMessage">Loading enrollments...</div>
        <ul id="enrollmentsList" style="display: none;"></ul>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" id="closeEnrollmentsModal">Close</button>
        </div>
    </div>
</div>




@endsection
@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('admin/admin-programs.js') }}?v={{ time() }}"></script>

<script>
// Image preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('program_image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImage');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    e.target.value = '';
                    return;
                }
                
                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Image size must be less than 2MB.');
                    e.target.value = '';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });

        // Remove image functionality
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imagePreview.style.display = 'none';
            previewImg.src = '';
        });
    }
});
</script>
@endpush

