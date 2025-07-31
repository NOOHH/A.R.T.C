@extends('professor.professor-layouts.professor-layout')

@section('title', 'Dashboard')

@section('content')
<style>
/* Reset overflow for professor dashboard */
html, body {
  overflow-x: hidden;
  overflow-y: auto !important;
}

.professor-container {
  overflow: visible !important;
  min-height: 100vh;
}

.main-content-area {
  overflow: visible !important;
  min-height: 100vh;
}

.content-wrapper {
  overflow-y: auto !important;
  height: auto !important;
  min-height: 100vh;
  padding: 1rem;
  background: #f8f9fa;
  position: relative;
  width: 100%;
}

/* Ensure the dashboard content can scroll */
.content-wrapper .container-fluid {
  overflow: visible !important;
  height: auto !important;
  padding: 0 15px;
  max-width: 100%;
  margin: 0;
  box-sizing: border-box;
  width: 100%;
}

/* Ensure proper Bootstrap container behavior */
.content-wrapper .container-fluid .row {
  margin-left: -15px;
  margin-right: -15px;
}

.content-wrapper .container-fluid .col-12,
.content-wrapper .container-fluid .col-md-3,
.content-wrapper .container-fluid .col-md-4,
.content-wrapper .container-fluid .col-xl-3 {
  padding-left: 15px;
  padding-right: 15px;
}

/* Override any conflicting navbar container-fluid styles */
.navbar .container-fluid {
  max-width: 100%;
  padding-left: 15px;
  padding-right: 15px;
}

/* Force scrolling on the main content */
.main-content-area .content-wrapper {
  overflow-y: scroll !important;
  max-height: none !important;
}

/* Custom announcement card styling */
.border-left-primary {
  border-left: 4px solid #007bff !important;
}

.card.border-left-primary:hover {
  box-shadow: 0 0.5rem 1rem rgba(0, 123, 255, 0.15) !important;
  transform: translateY(-2px);
  transition: all 0.3s ease;
}

/* Stats card styling */
.stat-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Ensure proper spacing */
.content-wrapper .row {
  margin-bottom: 1.5rem;
}

.content-wrapper .card {
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  margin-bottom: 1rem;
}

.content-wrapper .card:hover {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

/* Ensure proper content spacing */
.content-wrapper {
  padding: 1.5rem;
}

/* Fix any potential margin issues */
.content-wrapper > *:first-child {
  margin-top: 0;
}

.content-wrapper > *:last-child {
  margin-bottom: 0;
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
        $moduleManagementEnabled = \App\Models\AdminSetting::where('setting_key', 'professor_module_management_enabled')->value('setting_value') === '1';
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
                    <i class="bi bi-calendar-event text-success" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Meetings</h5>
                    <p class="card-text">Track and Manage Class Meetings.</p>
                    <a href="{{ route('professor.meetings') }}" class="btn btn-success">Manage Meetings</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-megaphone text-warning" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Announcements</h5>
                    <p class="card-text">View and manage your announcements.</p>
                    <a href="{{ route('professor.announcements.index') }}" class="btn btn-warning">View Announcements</a>
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
        @if($moduleManagementEnabled)
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-journals text-success" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Module Management</h5>
                    <p class="card-text">Create and manage modules for your assigned programs.</p>
                    <a href="{{ route('professor.modules.index') }}" class="btn btn-success">Manage Modules</a>
                </div>
            </div>
        </div>
        @endif
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

    <!-- Announcements Section -->
    @if(isset($announcements) && $announcements->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-megaphone"></i> Announcements</h5>
                    <span class="badge bg-primary">{{ $announcements->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($announcements->take(3) as $announcement)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-left-primary">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">{{ $announcement->title }}</h6>
                                            @if($announcement->priority === 'high')
                                                <span class="badge bg-danger">High Priority</span>
                                            @elseif($announcement->priority === 'medium')
                                                <span class="badge bg-warning">Medium Priority</span>
                                            @else
                                                <span class="badge bg-info">Low Priority</span>
                                            @endif
                                        </div>
                                        <p class="card-text small text-muted mb-2">
                                            {{ Str::limit($announcement->content, 100) }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3"></i>
                                                {{ $announcement->created_at->format('M d, Y') }}
                                            </small>
                                            @if($announcement->target_scope === 'program_specific')
                                                <small class="text-primary">
                                                    <i class="bi bi-bookmark"></i> Program Specific
                                                </small>
                                            @else
                                                <small class="text-success">
                                                    <i class="bi bi-globe"></i> General
                                                </small>
                                            @endif
                                        </div>
                                        @if($announcement->expire_date && $announcement->expire_date->isToday())
                                            <div class="mt-2">
                                                <small class="text-warning">
                                                    <i class="bi bi-exclamation-triangle"></i> Expires today
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($announcements->count() > 3)
                        <div class="text-center mt-3">
                            <button class="btn btn-outline-primary btn-sm" onclick="toggleAllAnnouncements()">
                                <span id="toggleText">Show All Announcements</span>
                                <i class="bi bi-chevron-down" id="toggleIcon"></i>
                            </button>
                        </div>
                        <div id="additionalAnnouncements" style="display: none;" class="mt-3">
                            <div class="row">
                                @foreach($announcements->skip(3) as $announcement)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100 border-left-primary">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0">{{ $announcement->title }}</h6>
                                                    @if($announcement->priority === 'high')
                                                        <span class="badge bg-danger">High Priority</span>
                                                    @elseif($announcement->priority === 'medium')
                                                        <span class="badge bg-warning">Medium Priority</span>
                                                    @else
                                                        <span class="badge bg-info">Low Priority</span>
                                                    @endif
                                                </div>
                                                <p class="card-text small text-muted mb-2">
                                                    {{ Str::limit($announcement->content, 100) }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar3"></i>
                                                        {{ $announcement->created_at->format('M d, Y') }}
                                                    </small>
                                                    @if($announcement->target_scope === 'program_specific')
                                                        <small class="text-primary">
                                                            <i class="bi bi-bookmark"></i> Program Specific
                                                        </small>
                                                    @else
                                                        <small class="text-success">
                                                            <i class="bi bi-globe"></i> General
                                                        </small>
                                                    @endif
                                                </div>
                                                @if($announcement->expire_date && $announcement->expire_date->isToday())
                                                    <div class="mt-2">
                                                        <small class="text-warning">
                                                            <i class="bi bi-exclamation-triangle"></i> Expires today
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function toggleAllAnnouncements() {
    const additionalDiv = document.getElementById('additionalAnnouncements');
    const toggleText = document.getElementById('toggleText');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (additionalDiv.style.display === 'none') {
        additionalDiv.style.display = 'block';
        toggleText.textContent = 'Show Less';
        toggleIcon.className = 'bi bi-chevron-up';
    } else {
        additionalDiv.style.display = 'none';
        toggleText.textContent = 'Show All Announcements';
        toggleIcon.className = 'bi bi-chevron-down';
    }
}
</script>

@endsection
