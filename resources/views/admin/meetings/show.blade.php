@extends('admin.admin-dashboard-layout')

@section('title', 'Meeting Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calendar-alt me-2"></i>Meeting Details
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.meetings.index') }}">Meetings</a></li>
                    <li class="breadcrumb-item active">{{ $meeting->title }}</li>
                </ol>
            </nav>
        </div>
        
        <div class="btn-group">
            <a href="{{ route('admin.meetings.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            @if(auth()->user()->role === 'professor' && auth()->id() === $meeting->professor_id)
                <a href="{{ route('admin.meetings.edit', $meeting->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit Meeting
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Meeting Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Meeting Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Meeting Title</label>
                            <p class="fw-bold">{{ $meeting->title }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Date & Time</label>
                            <p class="fw-bold">
                                <i class="fas fa-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('F d, Y') }}
                                <br>
                                <i class="fas fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('h:i A') }}
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Program</label>
                            <p class="fw-bold">{{ $meeting->batch->program->program_name ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Batch</label>
                            <p class="fw-bold">{{ $meeting->batch->batch_name ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Professor</label>
                            <p class="fw-bold">
                                {{ $meeting->professor->first_name ?? 'N/A' }} 
                                {{ $meeting->professor->last_name ?? '' }}
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Created By</label>
                            <p class="fw-bold">
                                {{ $meeting->creator->first_name ?? 'N/A' }} 
                                {{ $meeting->creator->last_name ?? '' }}
                            </p>
                        </div>
                        
                        @if($meeting->description)
                        <div class="col-12">
                            <label class="form-label text-muted">Description</label>
                            <p>{{ $meeting->description }}</p>
                        </div>
                        @endif
                        
                        @if($meeting->meeting_link)
                        <div class="col-12">
                            <label class="form-label text-muted">Meeting Link</label>
                            <p>
                                <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt me-2"></i>Join Meeting
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attendance Management -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Attendance Management
                    </h5>
                    <div>
                        <span class="badge bg-success me-2">{{ $attendedStudents->count() }} Present</span>
                        <span class="badge bg-danger">{{ $absentStudents->count() }} Absent</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(auth()->user()->role === 'professor' && auth()->id() === $meeting->professor_id)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Drag & Drop:</strong> Drag students between "Present" and "Absent" sections to update attendance.
                        </div>
                    @endif

                    <div class="row">
                        <!-- Present Students -->
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-check-circle me-2"></i>Present Students ({{ $attendedStudents->count() }})
                                    </h6>
                                </div>
                                <div class="card-body p-2" 
                                     id="presentStudents" 
                                     ondrop="drop(event, 'present')" 
                                     ondragover="allowDrop(event)"
                                     style="min-height: 200px;">
                                    @foreach($attendedStudents as $student)
                                        <div class="student-card present mb-2 p-2 border rounded bg-light" 
                                             draggable="{{ auth()->user()->role === 'professor' && auth()->id() === $meeting->professor_id ? 'true' : 'false' }}"
                                             ondragstart="drag(event)"
                                             data-student-id="{{ $student->id }}">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-success rounded-circle text-white text-center me-2"
                                                     style="width: 32px; height: 32px; line-height: 32px;">
                                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                    <small class="text-muted">{{ $student->email }}</small>
                                                </div>
                                                @if(auth()->user()->role === 'professor' && auth()->id() === $meeting->professor_id)
                                                    <div class="ms-auto">
                                                        <i class="fas fa-grip-vertical text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($attendedStudents->count() === 0)
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                                            <p>No students marked present</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Absent Students -->
                        <div class="col-md-6">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-times-circle me-2"></i>Absent Students ({{ $absentStudents->count() }})
                                    </h6>
                                </div>
                                <div class="card-body p-2" 
                                     id="absentStudents" 
                                     ondrop="drop(event, 'absent')" 
                                     ondragover="allowDrop(event)"
                                     style="min-height: 200px;">
                                    @foreach($absentStudents as $student)
                                        <div class="student-card absent mb-2 p-2 border rounded bg-light" 
                                             draggable="{{ auth()->user()->role === 'professor' && auth()->id() === $meeting->professor_id ? 'true' : 'false' }}"
                                             ondragstart="drag(event)"
                                             data-student-id="{{ $student->id }}">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-danger rounded-circle text-white text-center me-2"
                                                     style="width: 32px; height: 32px; line-height: 32px;">
                                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                    <small class="text-muted">{{ $student->email }}</small>
                                                </div>
                                                @if(auth()->user()->role === 'professor' && auth()->id() === $meeting->professor_id)
                                                    <div class="ms-auto">
                                                        <i class="fas fa-grip-vertical text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($absentStudents->count() === 0)
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-user-check fa-2x mb-2"></i>
                                            <p>All students are present!</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meeting Statistics -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Meeting Statistics
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $totalStudents = $attendedStudents->count() + $absentStudents->count();
                        $attendanceRate = $totalStudents > 0 ? ($attendedStudents->count() / $totalStudents) * 100 : 0;
                    @endphp
                    
                    <div class="text-center mb-3">
                        <div class="display-4 fw-bold text-primary">{{ number_format($attendanceRate, 1) }}%</div>
                        <p class="text-muted">Attendance Rate</p>
                    </div>
                    
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: {{ $attendanceRate }}%"></div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h4 text-success">{{ $attendedStudents->count() }}</div>
                                <small class="text-muted">Present</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-danger">{{ $absentStudents->count() }}</div>
                            <small class="text-muted">Absent</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meeting Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-status-indicator me-2"></i>Meeting Status
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $now = now();
                        $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                        $isUpcoming = $meetingDate->isFuture();
                        $isToday = $meetingDate->isToday();
                        $isPast = $meetingDate->isPast();
                    @endphp
                    
                    @if($isUpcoming)
                        <div class="alert alert-primary">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Upcoming Meeting</strong><br>
                            <small>{{ $meetingDate->diffForHumans() }}</small>
                        </div>
                    @elseif($isToday)
                        <div class="alert alert-warning">
                            <i class="fas fa-calendar-day me-2"></i>
                            <strong>Today's Meeting</strong><br>
                            <small>{{ $meetingDate->diffForHumans() }}</small>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Completed</strong><br>
                            <small>{{ $meetingDate->diffForHumans() }}</small>
                        </div>
                    @endif

                    @if($meeting->meeting_link)
                        <div class="d-grid">
                            <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-video me-2"></i>Join Meeting
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if(auth()->user()->role === 'professor' && auth()->id() === $meeting->professor_id)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-sm" onclick="markAllPresent()">
                            <i class="fas fa-user-check me-2"></i>Mark All Present
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="markAllAbsent()">
                            <i class="fas fa-user-times me-2"></i>Mark All Absent
                        </button>
                        <a href="{{ route('admin.meetings.edit', $meeting->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-2"></i>Edit Meeting
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
@if(auth()->user()->role === 'professor' && auth()->id() === $meeting->professor_id)
let draggedElement = null;

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    draggedElement = ev.target;
    ev.dataTransfer.setData("text", ev.target.getAttribute('data-student-id'));
}

function drop(ev, status) {
    ev.preventDefault();
    const studentId = ev.dataTransfer.getData("text");
    
    if (draggedElement) {
        // Update attendance status via AJAX
        updateAttendanceStatus(studentId, status);
        
        // Move element to new container
        const targetContainer = status === 'present' ? 
            document.getElementById('presentStudents') : 
            document.getElementById('absentStudents');
        
        // Update visual styling
        draggedElement.className = draggedElement.className.replace(/present|absent/, status);
        draggedElement.querySelector('.avatar-sm').className = 
            `avatar-sm bg-${status === 'present' ? 'success' : 'danger'} rounded-circle text-white text-center me-2`;
        
        targetContainer.appendChild(draggedElement);
        
        // Update counters
        updateCounters();
    }
}

function updateAttendanceStatus(studentId, status) {
    fetch(`{{ route('admin.meetings.updateAttendance', $meeting->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            student_id: studentId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showToast('Attendance updated successfully', 'success');
        } else {
            showToast('Error updating attendance', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating attendance', 'error');
    });
}

function updateCounters() {
    const presentCount = document.querySelectorAll('#presentStudents .student-card').length;
    const absentCount = document.querySelectorAll('#absentStudents .student-card').length;
    
    // Update header badges
    document.querySelector('.card-header .badge.bg-success').textContent = `${presentCount} Present`;
    document.querySelector('.card-header .badge.bg-danger').textContent = `${absentCount} Absent`;
    
    // Update section headers
    document.querySelector('#presentStudents').previousElementSibling
        .querySelector('h6').innerHTML = `<i class="fas fa-check-circle me-2"></i>Present Students (${presentCount})`;
    document.querySelector('#absentStudents').previousElementSibling
        .querySelector('h6').innerHTML = `<i class="fas fa-times-circle me-2"></i>Absent Students (${absentCount})`;
}

function markAllPresent() {
    if (confirm('Mark all students as present?')) {
        const absentStudents = document.querySelectorAll('#absentStudents .student-card');
        const presentContainer = document.getElementById('presentStudents');
        
        absentStudents.forEach(student => {
            const studentId = student.getAttribute('data-student-id');
            updateAttendanceStatus(studentId, 'present');
            
            // Update styling
            student.className = student.className.replace('absent', 'present');
            student.querySelector('.avatar-sm').className = 
                'avatar-sm bg-success rounded-circle text-white text-center me-2';
            
            presentContainer.appendChild(student);
        });
        
        updateCounters();
    }
}

function markAllAbsent() {
    if (confirm('Mark all students as absent?')) {
        const presentStudents = document.querySelectorAll('#presentStudents .student-card');
        const absentContainer = document.getElementById('absentStudents');
        
        presentStudents.forEach(student => {
            const studentId = student.getAttribute('data-student-id');
            updateAttendanceStatus(studentId, 'absent');
            
            // Update styling
            student.className = student.className.replace('present', 'absent');
            student.querySelector('.avatar-sm').className = 
                'avatar-sm bg-danger rounded-circle text-white text-center me-2';
            
            absentContainer.appendChild(student);
        });
        
        updateCounters();
    }
}

function showToast(message, type) {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}
@endif
</script>
@endsection
