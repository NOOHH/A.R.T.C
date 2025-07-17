@extends('professor.layout')

@section('title', 'Attendance Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Attendance Management</h2>
                <div class="btn-group">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createMeetingModal">
                        <i class="bi bi-plus-circle"></i> Create Meeting
                    </button>
                    <a href="{{ route('professor.attendance.reports') }}" class="btn btn-outline-primary">
                        <i class="bi bi-graph-up"></i> View Reports
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('professor.attendance') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="program_id" class="form-label">Select Program</label>
                            <select name="program_id" id="program_id" class="form-select" required>
                                <option value="">Choose a program...</option>
                                @foreach($assignedPrograms as $program)
                                    <option value="{{ $program->program_id }}" 
                                            {{ $selectedProgramId == $program->program_id ? 'selected' : '' }}>
                                        {{ $program->program_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" 
                                   value="{{ $selectedDate }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Load Attendance</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($selectedProgramId && $students->count() > 0)
                <!-- Attendance Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Record Attendance for {{ $selectedDate }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('professor.attendance.store') }}">
                            @csrf
                            <input type="hidden" name="program_id" value="{{ $selectedProgramId }}">
                            <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Student ID</th>
                                            <th>Status</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $index => $student)
                                            @php
                                                $existingAttendance = $attendanceRecords->get($student->student_id);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $student->full_name }}</strong><br>
                                                    <small class="text-muted">{{ $student->email }}</small>
                                                </td>
                                                <td>{{ $student->student_id }}</td>
                                                <td>
                                                    <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->student_id }}">
                                                    
                                                    <div class="btn-group" role="group">
                                                        <input type="radio" class="btn-check" 
                                                               name="attendance[{{ $index }}][status]" 
                                                               id="present_{{ $student->student_id }}" 
                                                               value="present"
                                                               {{ ($existingAttendance && $existingAttendance->status == 'present') ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-success btn-sm" 
                                                               for="present_{{ $student->student_id }}">Present</label>

                                                        <input type="radio" class="btn-check" 
                                                               name="attendance[{{ $index }}][status]" 
                                                               id="late_{{ $student->student_id }}" 
                                                               value="late"
                                                               {{ ($existingAttendance && $existingAttendance->status == 'late') ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-warning btn-sm" 
                                                               for="late_{{ $student->student_id }}">Late</label>

                                                        <input type="radio" class="btn-check" 
                                                               name="attendance[{{ $index }}][status]" 
                                                               id="absent_{{ $student->student_id }}" 
                                                               value="absent"
                                                               {{ ($existingAttendance && $existingAttendance->status == 'absent') || !$existingAttendance ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-danger btn-sm" 
                                                               for="absent_{{ $student->student_id }}">Absent</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" 
                                                           name="attendance[{{ $index }}][notes]"
                                                           value="{{ $existingAttendance ? $existingAttendance->notes : '' }}"
                                                           placeholder="Optional notes...">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    <small>Total Students: {{ $students->count() }}</small>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Save Attendance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif($selectedProgramId)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">No Students Found</h5>
                        <p class="text-muted">No students are enrolled in the selected program.</p>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar-check text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Select a Program</h5>
                        <p class="text-muted">Please select a program and date to manage attendance.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when program or date changes
    const programSelect = document.getElementById('program_id');
    const dateInput = document.getElementById('date');
    
    programSelect.addEventListener('change', function() {
        if (this.value) {
            this.closest('form').submit();
        }
    });
    
    dateInput.addEventListener('change', function() {
        if (programSelect.value) {
            this.closest('form').submit();
        }
    });
});
</script>

<!-- Create Meeting Modal -->
<div class="modal fade" id="createMeetingModal" tabindex="-1" aria-labelledby="createMeetingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createMeetingForm" action="{{ route('admin.meetings.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createMeetingModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Create New Meeting
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="meetingTitle" class="form-label">Meeting Title *</label>
                            <input type="text" class="form-control" id="meetingTitle" name="title" required 
                                   placeholder="e.g., Weekly Progress Review">
                        </div>
                        
                        <div class="col-12">
                            <label for="meetingDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="meetingDescription" name="description" rows="3" 
                                      placeholder="Brief description of the meeting agenda..."></textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="meetingDate" class="form-label">Meeting Date *</label>
                            <input type="date" class="form-control" id="meetingDate" name="meeting_date" required 
                                   min="{{ date('Y-m-d') }}">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="meetingTime" class="form-label">Meeting Time *</label>
                            <input type="time" class="form-control" id="meetingTime" name="meeting_time" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="programSelect" class="form-label">Program *</label>
                            <select class="form-select" id="programSelect" name="program_id" required>
                                <option value="">Select Program</option>
                                @foreach($assignedPrograms as $program)
                                    <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="batchSelect" class="form-label">Batch *</label>
                            <select class="form-select" id="batchSelect" name="batch_id" required disabled>
                                <option value="">Select Program First</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label for="meetingLink" class="form-label">Meeting Link (Optional)</label>
                            <input type="url" class="form-control" id="meetingLink" name="meeting_link" 
                                   placeholder="https://zoom.us/j/... or Google Meet link">
                            <div class="form-text">Students will see this link when they view the meeting</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-calendar-plus me-2"></i>Create Meeting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Meeting creation modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const programSelect = document.getElementById('programSelect');
    const batchSelect = document.getElementById('batchSelect');
    
    if (programSelect) {
        programSelect.addEventListener('change', function() {
            const programId = this.value;
            
            if (programId) {
                batchSelect.disabled = true;
                batchSelect.innerHTML = '<option value="">Loading...</option>';
                
                fetch(`/admin/programs/${programId}/batches`)
                    .then(response => response.json())
                    .then(data => {
                        let options = '<option value="">Select Batch</option>';
                        data.batches.forEach(batch => {
                            options += `<option value="${batch.id}">${batch.batch_name}</option>`;
                        });
                        batchSelect.innerHTML = options;
                        batchSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading batches:', error);
                        batchSelect.innerHTML = '<option value="">Error loading batches</option>';
                    });
            } else {
                batchSelect.disabled = true;
                batchSelect.innerHTML = '<option value="">Select Program First</option>';
            }
        });
    }
});
</script>

@endsection
