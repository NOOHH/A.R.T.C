@extends('professor.layout')

@section('title', 'Attendance Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Attendance Management</h2>
                <a href="{{ route('professor.attendance.reports') }}" class="btn btn-outline-primary">
                    <i class="bi bi-graph-up"></i> View Reports
                </a>
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
@endsection
