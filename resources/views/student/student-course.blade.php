@extends('student.student-dashboard-layout')

@section('title', 'Calculus 1')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/student/student-course.css') }}">
@endpush

@section('content')
<div class="course-container">
    <div class="course-header">
        <div class="course-info">
            <h1>Calculus 1</h1>
            <p class="course-description">Introduction to differential and integral calculus</p>
            <div class="course-stats">
                <span class="stat-item">
                    <strong>Progress:</strong> 15% Complete
                </span>
                <span class="stat-item">
                    <strong>Lessons:</strong> 12 of 20
                </span>
                <span class="stat-item">
                    <strong>Assignments:</strong> 3 of 8
                </span>
            </div>
        </div>
        <div class="course-actions">
            <button class="continue-btn">Continue Learning</button>
        </div>
    </div>

    <div class="course-content">
        <div class="course-modules">
            <h2>Course Modules</h2>
            
            <div class="module-list">
                <div class="module-item completed">
                    <div class="module-icon">✓</div>
                    <div class="module-details">
                        <h3>Module 1: Introduction to Limits</h3>
                        <p>Understanding the concept of limits and continuity</p>
                        <div class="module-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 100%"></div>
                            </div>
                            <span class="progress-text">100% Complete</span>
                        </div>
                    </div>
                    <button class="module-btn">Review</button>
                </div>

                <div class="module-item in-progress">
                    <div class="module-icon">📖</div>
                    <div class="module-details">
                        <h3>Module 2: Derivatives</h3>
                        <p>Learning differentiation rules and applications</p>
                        <div class="module-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 60%"></div>
                            </div>
                            <span class="progress-text">60% Complete</span>
                        </div>
                    </div>
                    <button class="module-btn">Continue</button>
                </div>

                <div class="module-item locked">
                    <div class="module-icon">🔒</div>
                    <div class="module-details">
                        <h3>Module 3: Integration</h3>
                        <p>Introduction to integration and antiderivatives</p>
                        <div class="module-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                            <span class="progress-text">Locked</span>
                        </div>
                    </div>
                    <button class="module-btn" disabled>Locked</button>
                </div>

                <div class="module-item locked">
                    <div class="module-icon">🔒</div>
                    <div class="module-details">
                        <h3>Module 4: Applications of Calculus</h3>
                        <p>Real-world applications and problem solving</p>
                        <div class="module-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                            <span class="progress-text">Locked</span>
                        </div>
                    </div>
                    <button class="module-btn" disabled>Locked</button>
                </div>
            </div>
        </div>

        <div class="course-sidebar">
            <div class="assignments-card">
                <h3>Recent Assignments</h3>
                <div class="assignment-list">
                    <div class="assignment-item">
                        <div class="assignment-info">
                            <h4>Limit Problems Set 1</h4>
                            <p>Due: Jan 15, 2025</p>
                        </div>
                        <span class="assignment-status completed">Completed</span>
                    </div>
                    <div class="assignment-item">
                        <div class="assignment-info">
                            <h4>Derivative Practice</h4>
                            <p>Due: Jan 22, 2025</p>
                        </div>
                        <span class="assignment-status pending">Pending</span>
                    </div>
                    <div class="assignment-item">
                        <div class="assignment-info">
                            <h4>Integration Quiz</h4>
                            <p>Due: Jan 29, 2025</p>
                        </div>
                        <span class="assignment-status upcoming">Upcoming</span>
                    </div>
                </div>
            </div>

            <div class="resources-card">
                <h3>Course Resources</h3>
                <div class="resource-list">
                    <a href="#" class="resource-item">
                        <span class="resource-icon">📄</span>
                        <span>Course Syllabus</span>
                    </a>
                    <a href="#" class="resource-item">
                        <span class="resource-icon">📚</span>
                        <span>Textbook (PDF)</span>
                    </a>
                    <a href="#" class="resource-item">
                        <span class="resource-icon">🎥</span>
                        <span>Video Lectures</span>
                    </a>
                    <a href="#" class="resource-item">
                        <span class="resource-icon">🧮</span>
                        <span>Calculator Tools</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
