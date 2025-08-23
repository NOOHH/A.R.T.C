@extends('professor.professor-layouts.professor-layout')

@section('title', 'Meeting Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Meeting Details</h1>
                    <p class="text-muted">{{ $meeting->meeting_title }}</p>
                </div>
                <div class="d-flex gap-2">
                    @php
                        // Check if we're in tenant preview mode
                        $tenantSlug = request()->route('tenant') ?? session('preview_tenant');
                        $routePrefix = $tenantSlug ? 'tenant.draft.' : '';
                        $routeParams = $tenantSlug ? ['tenant' => $tenantSlug] : [];
                        $meetingsRoute = $tenantSlug ? $routePrefix . 'professor.meetings' : 'professor.meetings';
                    @endphp
                    <a href="{{ route($meetingsRoute, $routeParams) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Meetings
                    </a>
                    @if(!$tenantSlug)
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editMeetingModal">
                            <i class="bi bi-pencil me-2"></i>Edit Meeting
                        </button>
                    @endif
                </div>
            </div>

            <!-- Meeting Information Card -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>Meeting Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Meeting Title:</label>
                                        <p class="mb-0">{{ $meeting->meeting_title }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Date & Time:</label>
                                        <p class="mb-0">
                                            <i class="bi bi-calendar-event me-2"></i>
                                            {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('F j, Y') }}
                                            <br>
                                            <i class="bi bi-clock me-2"></i>
                                            {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('g:i A') }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Program:</label>
                                        <p class="mb-0">{{ $meeting->batch->program->program_name }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Batch:</label>
                                        <p class="mb-0">{{ $meeting->batch->batch_name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Meeting Link:</label>
                                        <p class="mb-0">
                                            <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-link-45deg me-2"></i>Join Meeting
                                            </a>
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Professor:</label>
                                        <p class="mb-0">{{ $meeting->professor->user_firstname }} {{ $meeting->professor->user_lastname }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status:</label>
                                        <p class="mb-0">
                                            @php
                                                $now = \Carbon\Carbon::now();
                                                $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                                            @endphp
                                            @if($meetingDate->isFuture())
                                                <span class="badge bg-warning">Upcoming</span>
                                            @elseif($meetingDate->isToday())
                                                <span class="badge bg-success">Today</span>
                                            @else
                                                <span class="badge bg-secondary">Completed</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Created:</label>
                                        <p class="mb-0">{{ $meeting->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                            @if($meeting->description)
                            <div class="mt-3">
                                <label class="form-label fw-bold">Description:</label>
                                <p class="mb-0">{{ $meeting->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-people me-2"></i>Attendance Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $totalStudents = $meeting->batch->enrollments->where('enrollment_status', 'approved')->count();
                                $attendedCount = $meeting->attendanceLogs->count();
                                $attendanceRate = $totalStudents > 0 ? round(($attendedCount / $totalStudents) * 100, 1) : 0;
                            @endphp
                            <div class="text-center mb-3">
                                <div class="display-4 fw-bold text-primary">{{ $attendanceRate }}%</div>
                                <p class="text-muted mb-0">Attendance Rate</p>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Students:</span>
                                <span class="fw-bold">{{ $totalStudents }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Attended:</span>
                                <span class="fw-bold text-success">{{ $attendedCount }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Absent:</span>
                                <span class="fw-bold text-danger">{{ $totalStudents - $attendedCount }}</span>
                            </div>
                            <div class="progress mt-3" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $attendanceRate }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Details -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-list-check me-2"></i>Detailed Attendance
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Status</th>
                                            <th>Joined At</th>
                                            <th>IP Address</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($meeting->batch->enrollments->where('enrollment_status', 'approved') as $enrollment)
                                            @php
                                                $attendanceLog = $meeting->attendanceLogs->where('student_id', $enrollment->student_id)->first();
                                            @endphp
                                            <tr>
                                                <td>{{ $enrollment->student_id }}</td>
                                                <td>{{ $enrollment->student->user_firstname }} {{ $enrollment->student->user_lastname }}</td>
                                                <td>
                                                    @if($attendanceLog)
                                                        <span class="badge bg-success">Present</span>
                                                    @else
                                                        <span class="badge bg-danger">Absent</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($attendanceLog && $attendanceLog->joined_at)
                                                        {{ \Carbon\Carbon::parse($attendanceLog->joined_at)->format('M j, Y g:i A') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $attendanceLog->ip_address ?? '-' }}</td>
                                                <td>{{ $attendanceLog->notes ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No students enrolled in this batch</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Meeting Modal -->
<div class="modal fade" id="editMeetingModal" tabindex="-1" aria-labelledby="editMeetingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMeetingModalLabel">
                    <i class="bi bi-pencil me-2"></i>Edit Meeting
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if(!$tenantSlug)
                <form action="{{ route('professor.meetings.update', $meeting->meeting_id) }}" method="POST">
            @else
                <div class="alert alert-info m-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Meeting editing is disabled in preview mode.
                </div>
                <form disabled>
            @endif
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="meeting_title" class="form-label">Meeting Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="meeting_title" name="meeting_title" 
                                       value="{{ $meeting->meeting_title }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="meeting_date" class="form-label">Meeting Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="meeting_date" name="meeting_date" 
                                       value="{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="meeting_link" class="form-label">Meeting Link <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="meeting_link" name="meeting_link" 
                               value="{{ $meeting->meeting_link }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ $meeting->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Update Meeting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today for datetime input
    const meetingDateInput = document.getElementById('meeting_date');
    if (meetingDateInput) {
        const now = new Date();
        const minDateTime = now.toISOString().slice(0, 16);
        meetingDateInput.min = minDateTime;
    }
});
</script>
@endpush
