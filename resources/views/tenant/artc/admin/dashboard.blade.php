<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $client->name }} - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/css/admin/artc-theme.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%); min-height: 100vh; }
        .main-header { background: var(--gradient-primary); color: white; padding: 2rem 0; margin-bottom: 2rem; }
        .stats-card { background: white; border-radius: 15px; padding: 1.5rem; text-align: center; box-shadow: var(--shadow-md); border: 1px solid rgba(102, 126, 234, 0.1); transition: var(--transition); }
        .stats-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
        .stats-number { font-size: 2.5rem; font-weight: 800; color: var(--primary-color); }
        .stats-label { color: #6c757d; font-weight: 600; margin-top: 0.5rem; }
        .admin-nav { background: white; padding: 1rem 0; margin-bottom: 2rem; box-shadow: var(--shadow-sm); }
    </style>
</head>
<body>
    <div class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">
                        <i class="fas fa-tachometer-alt me-3"></i>{{ $client->name }} Admin
                    </h1>
                    <p class="mb-0 opacity-90">Manage your training center administration</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="/t/{{ $client->slug }}" class="btn btn-light btn-lg me-2">
                        <i class="fas fa-home me-2"></i>Website
                    </a>
                    <a href="/" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-external-link-alt me-2"></i>SmartPrep
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Navigation -->
    <div class="admin-nav">
        <div class="container">
            <ul class="nav nav-sp nav-pills justify-content-center">
                <li class="nav-item">
                    <a class="nav-link active" href="/t/{{ $client->slug }}/admin/dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/t/{{ $client->slug }}/admin/programs">
                        <i class="fas fa-graduation-cap me-2"></i>Programs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/t/{{ $client->slug }}/admin/students">
                        <i class="fas fa-user-graduate me-2"></i>Students
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/t/{{ $client->slug }}/admin/professors">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Professors
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/t/{{ $client->slug }}/admin/announcements">
                        <i class="fas fa-bullhorn me-2"></i>Announcements
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container pb-5">
        @if(session('success'))
            <div class="alert alert-sp alert-success alert-dismissible fade show mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['total_programs'] }}</div>
                    <div class="stats-label">Programs</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['total_students'] }}</div>
                    <div class="stats-label">Students</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['total_professors'] }}</div>
                    <div class="stats-label">Professors</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['total_enrollments'] }}</div>
                    <div class="stats-label">Enrollments</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Students -->
            <div class="col-lg-6">
                <div class="card-sp">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Recent Students</h5>
                    </div>
                    <div class="card-body">
                        @if($recentStudents->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentStudents as $student)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $student->first_name }} {{ $student->last_name }}</div>
                                        <small class="text-muted">{{ $student->student_id }} • {{ $student->email }}</small>
                                    </div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($student->created_at)->diffForHumans() }}</small>
                                </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3">
                                <a href="/t/{{ $client->slug }}/admin/students" class="btn btn-sp-outline">
                                    <i class="fas fa-users me-2"></i>Manage Students
                                </a>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-user-graduate text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                <h6 class="text-muted mt-2">No students yet</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Enrollments -->
            <div class="col-lg-6">
                <div class="card-sp">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Recent Enrollments</h5>
                    </div>
                    <div class="card-body">
                        @if($recentEnrollments->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentEnrollments as $enrollment)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $enrollment->first_name }} {{ $enrollment->last_name }}</div>
                                        <small class="text-muted">{{ $enrollment->program_name }}</small>
                                    </div>
                                    <span class="badge badge-sp badge-sp-{{ $enrollment->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                <h6 class="text-muted mt-2">No enrollments yet</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card-sp">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="/t/{{ $client->slug }}/admin/programs" class="btn btn-sp w-100">
                                    <i class="fas fa-plus me-2"></i>Add Program
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="/t/{{ $client->slug }}/admin/students" class="btn btn-sp-outline w-100">
                                    <i class="fas fa-user-plus me-2"></i>Add Student
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="/t/{{ $client->slug }}/admin/professors" class="btn btn-sp-outline w-100">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>Add Professor
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="/t/{{ $client->slug }}/admin/announcements" class="btn btn-sp-outline w-100">
                                    <i class="fas fa-bullhorn me-2"></i>New Announcement
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
