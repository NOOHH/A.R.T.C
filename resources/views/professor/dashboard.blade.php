@extends('professor.layout')

@section('title', 'Dashboard')

@section('content')
<style>
/* 0) Reset & full viewport */
html, body, .admin-container, .main-wrapper {
  height: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
}

/* 1) Fixed header */
.main-header {
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  z-index: 1000;
  height: 56px; /* match your header height */
}

/* 2) Sidebar under header */
.main-sidebar {
  position: fixed;
  top: 56px;      /* header height */
  bottom: 0;
  width: 250px;   /* default expanded width */
  overflow-y: auto;
  background: #343a40;
  z-index: 999;
  transition: width .3s ease;
}

/* 3) Main content flex container */
.content-below-search {
  display: flex;
  height: calc(100% - 56px); /* fill below header */
  margin-top: 56px;
  margin-bottom: 5px;
}

/* 4) Scrollable page-content */
.page-content {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
  overflow: hidden; /* we'll scroll the inner wrapper */
}

/* 5) YOUR real scrolling area */
.content-wrapper {
  flex: 1 1 auto;
  overflow-y: auto;
  padding: 1rem;      /* your container-fluid padding */
  background: #fff;
  margin-left: 0;
}

/* 6) Collapse sidebar toggling */
body.sidebar-collapse .main-sidebar {
  width: 60px !important;
}
body.sidebar-collapse .main-header {
  left: 60px;
  width: calc(100% - 60px);
}
body.sidebar-collapse .content-below-search {
  margin-left: 60px;
}

</style>
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="bi bi-collection display-4 mb-3"></i>
                    <h3 class="display-4">{{ $totalPrograms }}</h3>
                    <p class="mb-0">Assigned Programs</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                <div class="card-body text-center">
                    <i class="bi bi-people display-4 mb-3"></i>
                    <h3 class="display-4">{{ $totalStudents }}</h3>
                    <p class="mb-0">Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: white;">
                <div class="card-body text-center">
                    <i class="bi bi-book display-4 mb-3"></i>
                    <h3 class="display-4">{{ $totalModules }}</h3>
                    <p class="mb-0">Total Modules</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body text-center">
                    <i class="bi bi-play-circle display-4 mb-3"></i>
                    <h3 class="display-4">{{ $assignedPrograms->where('pivot.video_link', '!=', null)->count() }}</h3>
                    <p class="mb-0">Videos Added</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Welcome back, {{ $professor->full_name }}!</h4>
                    <p class="card-text text-muted">
                        Manage your assigned programs, upload video content, and track student progress from your dashboard.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    @php
        $gradingEnabled = \App\Models\AdminSetting::where('setting_key', 'grading_enabled')->value('setting_value') !== 'false';
    @endphp
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-collection text-primary" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">View Programs</h5>
                    <p class="card-text">Access your assigned programs and manage content.</p>
                    <a href="{{ route('professor.programs') }}" class="btn btn-primary">View Programs</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-check2-square text-success" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Meetings</h5>
                    <p class="card-text">Track and Manage Class Meetings.</p>
                    <a href="{{ route('professor.meetings') }}" class="btn btn-success">Manage Meetings</a>
                </div>
            </div>
        </div>
        @if($gradingEnabled)
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-award text-warning" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Grading</h5>
                    <p class="card-text">Evaluate and assign grades to students.</p>
                    <a href="{{ route('professor.grading') }}" class="btn btn-warning">Manage Grades</a>
                </div>
            </div>
        </div>
        @endif
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-person-circle text-info" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Profile</h5>
                    <p class="card-text">Update your profile information and settings.</p>
                    <a href="{{ route('professor.profile') }}" class="btn btn-info">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Tools -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-camera-video text-secondary" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Upload Videos</h5>
                    <p class="card-text">Add or update video content for your programs.</p>
                    <a href="{{ route('professor.programs') }}" class="btn btn-secondary">Manage Videos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-people text-dark" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Students</h5>
                    <p class="card-text">View and manage your students.</p>
                    <a href="{{ route('professor.students.index') }}" class="btn btn-dark">View Students</a>
                </div>
            </div>
        </div>
        @if($aiQuizEnabled ?? false)
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-robot text-danger" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">AI Quiz Generator</h5>
                    <p class="card-text">Generate quizzes from uploaded documents using AI.</p>
                    <a href="{{ route('professor.quiz-generator') }}" class="btn btn-danger">Generate Quiz</a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Recent Programs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your Programs</h5>
                    <a href="{{ route('professor.programs') }}" class="btn btn-outline-primary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    {{-- Only use $assignedPrograms as provided by the controller. Do not fetch or display any other programs. --}}
                    @if($assignedPrograms->count() > 0)
                        <div class="row">
                            @foreach($assignedPrograms->take(3) as $program)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $program->program_name }}</h6>
                                            <p class="card-text small text-muted">
                                                {{ Str::limit($program->program_description, 80) }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    {{ $program->students->count() }} students
                                                </small>
                                                @if($program->pivot->video_link)
                                                    <span class="badge bg-success">Video Added</span>
                                                @else
                                                    <span class="badge bg-warning">No Video</span>
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                <a href="{{ route('professor.program.details', $program->program_id) }}" 
                                                   class="btn btn-primary btn-sm">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-collection text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">No Programs Assigned</h5>
                            <p class="text-muted">You haven't been assigned to any programs yet. Contact your administrator.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
