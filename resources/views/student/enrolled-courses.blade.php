@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'My Enrolled Courses - A.R.T.C')

@push('styles')
    {!! App\Helpers\UIHelper::getNavbarStyles() !!}
    <style>
        .enrolled-courses-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .content-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 0 auto;
            max-width: 1200px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #f8f9fa;
        }

        .page-header h1 {
            color: #2d3748;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #718096;
            font-size: 1.1rem;
        }

        .enrollment-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .enrollment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .enrollment-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 2rem;
            position: relative;
        }

        .enrollment-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
        }

        .enrollment-header-content {
            position: relative;
            z-index: 1;
        }

        .program-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .enrollment-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .enrollment-body {
            padding: 2rem;
        }

        .status-badges {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.pending {
            background: #fef3cd;
            color: #92400e;
        }

        .status-badge.paid {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-badge.modular {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-badge.full {
            background: #fce7f3;
            color: #be185d;
        }

        .courses-section h3 {
            color: #2d3748;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }

        .course-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .course-card:hover {
            background: #f1f5f9;
            border-color: #667eea;
            transform: translateY(-1px);
        }

        .course-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .course-description {
            color: #4a5568;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: #718096;
        }

        .module-tag {
            background: #edf2f7;
            color: #4a5568;
            padding: 0.2rem 0.6rem;
            border-radius: 15px;
            font-weight: 500;
        }

        .no-enrollments {
            text-align: center;
            padding: 4rem 2rem;
            color: #718096;
        }

        .no-enrollments i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #cbd5e0;
        }

        .no-enrollments h3 {
            color: #4a5568;
            margin-bottom: 1rem;
        }

        .no-enrollments p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .btn-enroll {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-enroll:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }

        @media (max-width: 768px) {
            .enrollment-meta {
                flex-direction: column;
                gap: 0.5rem;
            }

            .courses-grid {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .content-wrapper {
                margin: 0 1rem;
                padding: 1.5rem;
            }
        }
    </style>
@endpush

@section('content')
<div class="enrolled-courses-container">
    <div class="content-wrapper">
        <div class="page-header">
            <h1><i class="bi bi-journal-bookmark me-2"></i>My Enrolled Courses</h1>
            <p>View all your current course enrollments and track your learning progress</p>
        </div>

        @if(count($enrolledCoursesData) > 0)
            @foreach($enrolledCoursesData as $enrollment)
                <div class="enrollment-card">
                    <div class="enrollment-header">
                        <div class="enrollment-header-content">
                            <div class="program-name">{{ $enrollment['program_name'] }}</div>
                            <div class="enrollment-meta">
                                <div class="meta-item">
                                    <i class="bi bi-calendar-check"></i>
                                    <span>Enrolled: {{ $enrollment['enrolled_at'] }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="bi bi-box-seam"></i>
                                    <span>Package: {{ $enrollment['package_name'] }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="bi bi-clock"></i>
                                    <span>Mode: {{ $enrollment['learning_mode'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="enrollment-body">
                        <div class="status-badges">
                            <span class="status-badge {{ strtolower($enrollment['enrollment_status']) }}">
                                {{ ucfirst($enrollment['enrollment_status']) }}
                            </span>
                            <span class="status-badge {{ strtolower($enrollment['payment_status']) }}">
                                Payment: {{ ucfirst($enrollment['payment_status']) }}
                            </span>
                            <span class="status-badge {{ strtolower($enrollment['enrollment_type']) }}">
                                {{ $enrollment['enrollment_type'] }} Enrollment
                            </span>
                        </div>

                        <div class="courses-section">
                            <h3>
                                <i class="bi bi-book-half"></i>
                                Enrolled Courses
                                <span class="badge bg-primary">{{ count($enrollment['courses']) }}</span>
                            </h3>

                            @if(count($enrollment['courses']) > 0)
                                <div class="courses-grid">
                                    @foreach($enrollment['courses'] as $course)
                                        <div class="course-card">
                                            <div class="course-name">{{ $course['course_name'] }}</div>
                                            <div class="course-description">
                                                {{ $course['course_description'] ?: 'No description available.' }}
                                            </div>
                                            <div class="course-meta">
                                                <span class="module-tag">{{ $course['module_name'] }}</span>
                                                <small>Added: {{ $course['enrolled_at'] }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No specific courses found for this enrollment. This might be a pending enrollment or the courses are being set up.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="no-enrollments">
                <i class="bi bi-journal-x"></i>
                <h3>No Course Enrollments Found</h3>
                <p>You haven't enrolled in any courses yet. Start your learning journey today!</p>
                <a href="{{ route('enrollment.modular') }}" class="btn-enroll">
                    <i class="bi bi-plus-circle me-2"></i>Enroll in a Course
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth animations to cards
        const cards = document.querySelectorAll('.enrollment-card, .course-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = `all 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });
    });
</script>
@endpush
