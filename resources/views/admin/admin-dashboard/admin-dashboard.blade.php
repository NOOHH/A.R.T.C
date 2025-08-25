@extends('admin.admin-dashboard.admin-dashboard-layout')
@section('title','Admin Dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
@endpush


@section('content')
@if(isset($dbError) && $dbError)
    <div style="background:#ffeaea;color:#b91c1c;padding:14px 18px;border-radius:8px;margin-bottom:18px;font-weight:600;border:1.5px solid #fca5a5;">
        <span style="font-size:1.2em;vertical-align:middle;">&#9888;&#65039;</span> {{ $dbError }}<br>
        <span style="font-weight:400;font-size:0.98em;">Some dashboard features are unavailable until the database is restored.</span>
    </div>
@endif

<!-- Analytics Section -->
<div class="analytics-section">
    <div class="analytics-grid">
        <div class="analytics-card students">
            <div class="analytics-number">{{ $analytics['total_students'] ?? 8 }}</div>
            <div class="analytics-label">Total Students</div>
            <div class="analytics-change">
                <span>📈</span> +{{ $analytics['new_students_this_month'] ?? 4 }} this month
            </div>
        </div>
        
        <div class="analytics-card programs">
            <div class="analytics-number">{{ $analytics['total_programs'] ?? 3 }}</div>
            <div class="analytics-label">Active Programs</div>
            <div class="analytics-change">
                <span>🗂️</span> {{ $analytics['archived_programs'] ?? 2 }} archived
            </div>
        </div>
        
        <div class="analytics-card modules">
            <div class="analytics-number">{{ $analytics['total_modules'] ?? 0 }}</div>
            <div class="analytics-label">Course Modules</div>
            <div class="analytics-change">
                <span>📚</span> {{ $analytics['modules_this_week'] ?? 1 }} added this week
            </div>
        </div>
        
        <div class="analytics-card enrollments">
            <div class="analytics-number">{{ $analytics['total_enrollments'] ?? 11 }}</div>
            <div class="analytics-label">Total Enrollments</div>
            <div class="analytics-change">
                <span>⏳</span> {{ $analytics['pending_registrations'] ?? 2 }} pending approval
            </div>
        </div>
    </div>
</div>

<div class="dashboard-container">
    <div class="left-column">
        <!-- Content Management Section -->
        <div class="module-management">
            <div class="section-header">
                <span class="section-icon">📚</span>
                <h2 class="section-title">Content Management</h2>
            </div>
            
            <div class="module-actions-grid">
                @if(session('preview_tenant') && request('website'))
                    {{-- Preview mode - use tenant-aware URLs --}}
                    <a href="/t/draft/{{ session('preview_tenant') }}/admin/modules?website={{ request('website') }}" class="module-action-card">
                        <span class="icon">📝</span>
                        <div class="title">Create Module</div>
                        <div class="description">Add new learning modules</div>
                    </a>
                    
                    <a href="/t/draft/{{ session('preview_tenant') }}/admin/modules?website={{ request('website') }}" class="module-action-card">
                        <span class="icon">📋</span>
                        <div class="title">Create Quiz</div>
                        <div class="description">Design interactive quizzes</div>
                    </a>
                    
                    <a href="/t/draft/{{ session('preview_tenant') }}/admin/modules?website={{ request('website') }}" class="module-action-card">
                        <span class="icon">📊</span>
                        <div class="title">Create Test</div>
                        <div class="description">Build comprehensive tests</div>
                    </a>
                    
                    <a href="/t/draft/{{ session('preview_tenant') }}/admin/modules?website={{ request('website') }}" class="module-action-card">
                        <span class="icon">🔗</span>
                        <div class="title">Add Link</div>
                        <div class="description">Link external resources</div>
                    </a>
                    
                    <a href="/t/draft/{{ session('preview_tenant') }}/admin/courses/upload?website={{ request('website') }}" class="module-action-card">
                        <span class="icon">📤</span>
                        <div class="title">Batch Upload</div>
                        <div class="description">Upload multiple XML files</div>
                    </a>
                    
                    <a href="/t/draft/{{ session('preview_tenant') }}/admin/modules/archived?website={{ request('website') }}" class="module-action-card">
                        <span class="icon">🗃️</span>
                        <div class="title">Archived Content</div>
                        <div class="description">Manage archived modules</div>
                    </a>
                    
                    <a href="/t/draft/{{ session('preview_tenant') }}/admin/programs?website={{ request('website') }}" class="module-action-card">
                        <span class="icon">🎓</span>
                        <div class="title">Manage Programs</div>
                        <div class="description">Create and edit programs</div>
                    </a>
                    
                    <a href="/t/draft/{{ session('preview_tenant') }}/admin/submissions?website={{ request('website') }}" class="module-action-card">
                        <span class="icon">📋</span>
                        <div class="title">Assignment Submissions</div>
                        <div class="description">Review and grade student work</div>
                    </a>
                    
                    <a href="/t/draft/{{ session('preview_tenant') }}/admin/certificates?website={{ request('website') }}" class="module-action-card">
                        <span class="icon">🏆</span>
                        <div class="title">Certificates</div>
                        <div class="description">Generate student certificates</div>
                    </a>
                @else
                    {{-- Normal mode - use Laravel routes --}}
                    <a href="{{ route('admin.modules.index') }}" class="module-action-card">
                        <span class="icon">📝</span>
                        <div class="title">Create Module</div>
                        <div class="description">Add new learning modules</div>
                    </a>
                    
                    <a href="{{ route('admin.modules.index') }}" class="module-action-card">
                        <span class="icon">📋</span>
                        <div class="title">Create Quiz</div>
                        <div class="description">Design interactive quizzes</div>
                    </a>
                    
                    <a href="{{ route('admin.modules.index') }}" class="module-action-card">
                        <span class="icon">📊</span>
                        <div class="title">Create Test</div>
                        <div class="description">Build comprehensive tests</div>
                    </a>
                    
                    <a href="{{ route('admin.modules.index') }}" class="module-action-card">
                        <span class="icon">🔗</span>
                        <div class="title">Add Link</div>
                        <div class="description">Link external resources</div>
                    </a>
                    
                    <a href="{{ route('admin.modules.index') }}" class="module-action-card">
                        <span class="icon">📤</span>
                        <div class="title">Batch Upload</div>
                        <div class="description">Upload multiple XML files</div>
                    </a>
                    
                    <a href="{{ route('admin.modules.archived') }}" class="module-action-card">
                        <span class="icon">🗃️</span>
                        <div class="title">Archived Content</div>
                        <div class="description">Manage archived modules</div>
                    </a>
                    
                    <a href="{{ route('admin.programs.index') }}" class="module-action-card">
                        <span class="icon">🎓</span>
                        <div class="title">Manage Programs</div>
                        <div class="description">Create and edit programs</div>
                    </a>
                    
                    <a href="{{ route('admin.submissions') }}" class="module-action-card">
                        <span class="icon">📋</span>
                        <div class="title">Assignment Submissions</div>
                        <div class="description">Review and grade student work</div>
                    </a>
                    
                    <a href="{{ route('admin.certificates') }}" class="module-action-card">
                        <span class="icon">🏆</span>
                        <div class="title">Certificates</div>
                        <div class="description">Generate student certificates</div>
                    </a>
                @endif
            </div>
        </div>

        <!-- Analytics Chart Section -->
        <div class="analytics-chart">
            <div class="section-header">
                <span class="section-icon">📈</span>
                <h2 class="section-title">Analytics Overview</h2>
            </div>
            <div class="chart-container">
                <canvas id="analyticsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="right-column">
        <!-- Pending Student Registrations -->
        <div class="pending-panel">
            <div class="panel-title">
                <span>⏳</span> Pending Student Registrations
            </div>
            @if(isset($dbError) && $dbError)
                <div style="color:#b91c1c;padding:12px 0;">Cannot load registrations. Database unavailable.</div>
            @else
                @if($registrations->count() > 0)
                    @foreach($registrations->take(3) as $registration)
                    <div class="registration-item">
                        <div class="student-info">
                            <div class="student-name">{{ $registration->user_firstname ?? $registration->firstname ?? 'First' }} {{ $registration->user_lastname ?? $registration->lastname ?? 'Last' }}</div>
                            <div class="student-date">{{ $registration->created_at ? $registration->created_at->format('M d, Y') : 'Jul 02, 2025' }}</div>
                        </div>
                        <a href="{{ route('admin.student.registration.pending') }}" class="review-btn view-btn">Review</a>
                    </div>
                    @endforeach
                    @if($registrations->count() > 3)
                        <div style="text-align: center; margin-top: 16px; padding-top: 16px; border-top: 1px solid #f0f0f0;">
                            <a href="{{ route('admin.student.registration') }}" style="color: #667eea; text-decoration: none; font-weight: 500; font-size: 0.9rem;">
                                View all {{ $registrations->count() }} pending registrations →
                            </a>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">✅</div>
                        <div style="font-weight: 600; margin-bottom: 4px;">No pending registrations</div>
                        <small>All applications have been processed</small>
                    </div>
                @endif
            @endif
        </div>

        <!-- Recent Activities -->
        <div class="recent-activities">
            <div class="section-header">
                <span class="section-icon">🕒</span>
                <h2 class="section-title">Recent Activities</h2>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon new-student"><span>👤</span></div>
                <div class="activity-content">
                    <div class="activity-title">New student registration</div>
                    <div class="activity-time">2 hours ago</div>
                </div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon new-module"><span>📚</span></div>
                <div class="activity-content">
                    <div class="activity-title">Module "Advanced Calculus" created</div>
                    <div class="activity-time">4 hours ago</div>
                </div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon enrollment"><span>✅</span></div>
                <div class="activity-content">
                    <div class="activity-title">Student enrolled in Nursing Program</div>
                    <div class="activity-time">1 day ago</div>
                </div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon new-student"><span>👤</span></div>
                <div class="activity-content">
                    <div class="activity-title">Student registration approved</div>
                    <div class="activity-time">2 days ago</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Modal -->
<div id="registrationModal" class="modal">
    <div class="modal-content landscape-modal">
        <span class="close">&times;</span>
        <h2>Registration Details</h2>
        <div id="modal-details" class="modal-details-structured landscape-details"></div>
        <div class="modal-actions">
            <form id="approveForm" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="approve-btn btn btn-success btn-sm">Approve</button>
            </form>
            <form id="rejectForm" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="reject-btn btn btn-danger btn-sm">Reject</button>
            </form>
        </div>
    </div>
</div>
@endsection
