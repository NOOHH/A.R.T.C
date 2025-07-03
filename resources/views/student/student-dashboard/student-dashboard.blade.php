@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/student/student-dashboard.css') }}">
<style>
    /* Enhanced course cards */
    .courses-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%);
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .card-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .completion-badge {
        background: rgba(255,255,255,0.2);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    .courses-list {
        padding: 20px;
    }
    
    .course-item {
        display: flex;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .course-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .course-thumbnail {
        width: 120px;
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .course-placeholder {
        font-size: 2.5rem;
        opacity: 0.5;
    }
    
    .course-details {
        padding: 15px;
        flex: 1;
    }
    
    .course-details h3 {
        margin: 0 0 10px 0;
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .course-details p {
        margin: 0 0 15px 0;
        color: #7f8c8d;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    .progress-bar {
        height: 10px;
        background: #e9ecef;
        border-radius: 5px;
        overflow: hidden;
        position: relative;
    }
    
    .progress-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: var(--progress, 0%);
        background: linear-gradient(90deg, #3498db, #9b59b6);
        border-radius: 5px;
        transition: width 1s ease;
    }
    
    .progress-text {
        display: block;
        margin-top: 5px;
        font-size: 0.85rem;
        color: #7f8c8d;
        text-align: right;
    }
    
    .resume-btn {
        background: #3498db;
        color: white;
        border: none;
        padding: 10px 20px;
        margin: 15px;
        align-self: center;
        border-radius: 5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        white-space: nowrap;
    }
    
    .resume-btn:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }
    
    .no-courses {
        padding: 40px 20px;
        text-align: center;
        color: #7f8c8d;
    }
    
    .no-courses::before {
        content: '📚';
        display: block;
        font-size: 3rem;
        margin-bottom: 10px;
        opacity: 0.5;
    }
    
    /* Module icons */
    .course-thumbnail::before {
        content: '📚';
        font-size: 2.5rem;
    }
</style>
@endpush

@section('content')
<div class="dashboard-grid">
    <!-- My Programs Section -->
    <div class="dashboard-card courses-card">
        <div class="card-header">
            <h2>My Programs</h2>
            <span class="completion-badge">{{ count($courses) > 0 ? floor(array_sum(array_column($courses, 'progress')) / count($courses)) : '0' }}% overall progress</span>
        </div>
        <div class="courses-list">
            @forelse($courses as $course)
                <div class="course-item">
                    <div class="course-thumbnail">
                        <div class="course-placeholder">📚</div>
                    </div>
                    <div class="course-details">
                        <h3>{{ $course['name'] }}</h3>
                        <p>{{ $course['description'] }}</p>
                        <div class="progress-bar" style="--progress: {{ $course['progress'] }}%">
                            <span class="progress-text">{{ $course['progress'] }}% complete</span>
                        </div>
                        <div class="course-meta" style="margin-top: 10px; font-size: 0.9rem; color: #7f8c8d;">
                            <span>{{ $course['completed_modules'] ?? 0 }} / {{ $course['total_modules'] }} modules complete</span>
                        </div>
                    </div>
                    <a href="{{ route('student.course', ['courseId' => $course['id']]) }}" class="resume-btn">Resume</a>
                </div>
            @empty
                <div class="no-courses">
                    <p>You are not enrolled in any programs yet.</p>
                    <p>Please contact your administrator to get enrolled in courses.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Deadlines Section -->
    <div class="dashboard-card deadlines-card">
        <div class="card-header">
            <h2>Deadlines</h2>
        </div>
        <div class="deadlines-content">
            <p style="padding: 30px 20px; text-align: center; color: #7f8c8d;">No upcoming deadlines at this time.</p>
        </div>
    </div>

    <!-- My Progress Section -->
    <div class="dashboard-card progress-card">
        <div class="card-header">
            <h2>My Progress</h2>
        </div>
        <div class="progress-content" style="padding: 30px 20px; text-align: center;">
            <p style="color: #7f8c8d;">Progress chart will be displayed here</p>
            <p style="font-size: 0.9rem; color: #95a5a6;">Welcome back, {{ explode(' ', session('user_name'))[0] ?? 'student' }}!</p>
        </div>
    </div>

    <!-- Announcements Section -->
    <div class="dashboard-card announcements-card">
        <div class="card-header">
            <h2>Announcement</h2>
        </div>
        <div class="announcement-content" style="padding: 20px;">
            <p style="margin-bottom: 0;">Welcome to your student dashboard! Here you can access your courses, track your progress, and stay updated with important announcements.</p>
        </div>
    </div>
</div>
@endsection
