@extends('admin.admin-dashboard-layout')

@section('title', 'Programs')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/admin/admin-programs/admin-programs.css') }}?v={{ time() }}">
@endpush

@section('content')
<!-- Display messages -->
@if(session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="error-message">{{ session('error') }}</div>
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

<!-- Analytics Cards Section -->
<div class="analytics-cards">
    <div class="analytics-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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

    <div class="analytics-card" style="background: linear-gradient(135deg, #635664 0%, #eeeeee 100%);">
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

    <div class="analytics-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
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

    <div class="analytics-card" style="background: rgb(204, 204, 204);">
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
                    <button type="button" class="add-module-btn batch-upload-btn" id="showBatchModal">
                        📤 Batch Upload
                    </button>
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
                                👥 View Enrollees
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
                <h3>🔄 Recent Activities</h3>
                <a href="#" class="view-all-link">View all</a>
            </div>
            <div class="activities-list">
                @forelse($recentActivities ?? [] as $activity)
                    <div class="activity-item">
                        <div class="activity-icon">{{ $activity['icon'] }}</div>
                        <div class="activity-content">
                            <div class="activity-text">{{ $activity['text'] }}</div>
                            <div class="activity-time">{{ $activity['time'] }}</div>
                        </div>
                    </div>
                @empty
                    <div class="activity-item">
                        <div class="activity-icon">📚</div>
                        <div class="activity-content">
                            <div class="activity-text">New program "Advanced Programming" created</div>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">👥</div>
                        <div class="activity-content">
                            <div class="activity-text">25 students enrolled in "Web Development"</div>
                            <div class="activity-time">5 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">🎓</div>
                        <div class="activity-content">
                            <div class="activity-text">Program "Data Science" updated</div>
                            <div class="activity-time">1 day ago</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Add Program Button -->
<!-- Add Program Button -->
<button class="add-program-btn" id="showAddModal">
    <span style="font-size:1.3em;">&#43;</span> Add Program
</button>

<!-- Add Program Modal -->
<div class="modal-bg" id="addModalBg">
    <div class="modal">
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
    <div class="modal">
        <h3>Enrolled Students</h3>
        <div class="loading" id="loadingMessage">Loading enrollments...</div>
        <ul id="enrollmentsList" style="display: none;"></ul>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" id="closeEnrollmentsModal">Close</button>
        </div>
    </div>
</div>

<!-- Batch Upload Modal -->
<div class="modal-bg" id="batchModalBg">
    <div class="modal">
        <h3>Batch Upload Programs</h3>
        <div class="file-upload-info">
            <strong>CSV Format Required:</strong><br>
            Column 1: Program Name (required)<br>
            Column 2: Program Description (optional)<br>
            <em>Example: "Web Development","Learn HTML, CSS, and JavaScript"</em>
        </div>
        
        <form action="{{ route('admin.programs.batch-store') }}"
              method="POST"
              enctype="multipart/form-data"
              id="batchProgramForm">
            @csrf
            
            <div style="margin: 20px 0;">
                <label for="csvFile"><strong>Select CSV File:</strong></label>
                <input type="file" 
                       id="csvFile" 
                       name="csv_file" 
                       accept=".csv"
                       required>
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelBatchModal">Cancel</button>
                <button type="submit" class="add-btn">Upload Programs</button>
            </div>
        </form>
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

