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

    <div class="analytics-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
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

        <!-- Assignment -->
        <div class="assignment-panel">
            <div class="panel-header">
                <h3>👨‍🎓 Course Assignment</h3>
            </div>
            <form id="courseAssignmentForm" class="assignment-form" action="{{ route('admin.programs.assign') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="student_select">Select Student:</label>
                    <select id="student_select" name="student_id" required>
                        <option value="">Choose a student...</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->student_id }}">{{ $student->firstname ?? $student->first_name }} {{ $student->lastname ?? $student->last_name }} ({{ $student->email ?? 'No email' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="program_select">Select Program:</label>
                    <select id="program_select" name="program_id" required>
                        <option value="">Choose a program...</option>
                        @foreach($programs ?? [] as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="assignment_notes">Notes (optional):</label>
                    <textarea id="assignment_notes" name="notes" rows="3" placeholder="Additional notes about this assignment..."></textarea>
                </div>
                <button type="submit" class="assign-btn">
                    ✓ Assign Program
                </button>
            </form>
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
        <form action="{{ route('admin.programs.store') }}" method="POST">
            @csrf
            <input type="text" name="program_name" placeholder="Program Name" required>
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
    <div class="modal" style="max-width: 600px; width: 90vw;">
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
@endpush

