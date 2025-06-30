@extends('student.student-dashboard-layout')

@section('title', 'Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/student/student-dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-grid">
    <!-- My Courses Section -->
    <div class="dashboard-card courses-card">
        <div class="card-header">
            <h2>My Courses</h2>
            <span class="completion-badge">{{ count($courses) > 0 ? '25' : '0' }}% complete</span>
        </div>
        <div class="courses-list">
            @forelse($courses as $course)
                <div class="course-item">
                    <div class="course-thumbnail">
                        <div class="course-placeholder"></div>
                    </div>
                    <div class="course-details">
                        <h3>{{ $course['name'] }}</h3>
                        <p>{{ $course['description'] }}</p>
                        <div class="progress-bar">
                            <span class="progress-text">{{ $course['progress'] }}% complete</span>
                        </div>
                    </div>
                    <a href="{{ route('student.courses.calculus1') }}" class="resume-btn">Resume</a>
                </div>
            @empty
                <div class="no-courses">
                    <p>No courses enrolled yet. Contact your administrator to get enrolled in courses.</p>
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
            <p>No upcoming deadlines at this time.</p>
        </div>
    </div>

    <!-- My Progress Section -->
    <div class="dashboard-card progress-card">
        <div class="card-header">
            <h2>My Progress</h2>
        </div>
        <div class="progress-content">
            <div class="progress-chart">
                <div class="progress-placeholder">
                    <p>Progress chart will be displayed here</p>
                    <p><small>Welcome back, {{ $user->user_firstname }}!</small></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcement Section -->
    <div class="dashboard-card announcement-card">
        <div class="card-header">
            <h2>Announcement</h2>
        </div>
        <div class="announcement-content">
            <p>Welcome to your student dashboard! Here you can access your courses, track your progress, and stay updated with important announcements.</p>
        </div>
    </div>
</div>
@endsection
