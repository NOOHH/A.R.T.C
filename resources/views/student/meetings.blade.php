@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'My Meetings')

@section('content')
<style>
.blink {
    animation: blink-animation 1s steps(5, start) infinite;
}

@keyframes blink-animation {
    to {
        visibility: hidden;
    }
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-0">
                        <i class="bi bi-camera-video me-2"></i>My Meetings
                    </h2>
                    <p class="text-muted mb-0">View your upcoming class meetings and join sessions</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Current/Live Meetings -->
            @if($currentMeetings->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-broadcast me-2"></i>Live Meetings
                                <span class="badge bg-light text-danger ms-2">{{ $currentMeetings->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($currentMeetings as $meeting)
                                    @php
                                        $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                                    @endphp
                                    <div class="list-group-item d-flex justify-content-between align-items-start bg-danger-subtle">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{ $meeting->title }}</div>
                                            <p class="mb-1">{{ $meeting->description ?? 'No description provided' }}</p>
                                            <small class="text-muted">
                                                <i class="bi bi-mortarboard me-1"></i>{{ $meeting->batch->program->program_name ?? 'N/A' }}
                                                •
                                                <i class="bi bi-people me-1"></i>{{ $meeting->batch->batch_name ?? 'N/A' }}
                                                •
                                                <i class="bi bi-person-workspace me-1"></i>{{ $meeting->professor->professor_name ?? 'N/A' }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="mb-2">
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-dot text-white blink"></i>LIVE NOW
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>{{ $meetingDate->format('h:i A') }}</strong>
                                                <br>
                                                <small class="text-muted">Started {{ $meetingDate->diffForHumans() }}</small>
                                            </div>
                                            @if($meeting->meeting_url)
                                                <a href="{{ $meeting->meeting_url }}" 
                                                   target="_blank" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="logMeetingAccess({{ $meeting->meeting_id }})">
                                                    <i class="bi bi-camera-video me-1"></i>Join Now
                                                </a>
                                            @else
                                                <span class="text-muted small">No link available</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Upcoming Meetings -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-week me-2"></i>Upcoming Meetings
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($upcomingMeetings->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($upcomingMeetings as $meeting)
                                        @php
                                            $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                                            $now = \Carbon\Carbon::now();
                                            $isToday = $meetingDate->isToday();
                                            $isTomorrow = $meetingDate->isTomorrow();
                                            $isNow = $meetingDate->isPast() && $meetingDate->diffInMinutes($now) <= ($meeting->duration_minutes ?? 60);
                                        @endphp
                                        <div class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold">{{ $meeting->title }}</div>
                                                <p class="mb-1">{{ $meeting->description ?? 'No description provided' }}</p>
                                                <small class="text-muted">
                                                    <i class="bi bi-mortarboard me-1"></i>{{ $meeting->batch->program->program_name ?? 'N/A' }}
                                                    •
                                                    <i class="bi bi-people me-1"></i>{{ $meeting->batch->batch_name ?? 'N/A' }}
                                                    •
                                                    <i class="bi bi-person-workspace me-1"></i>{{ $meeting->professor->professor_name ?? 'N/A' }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <div class="mb-2">
                                                    @if($isNow)
                                                        <span class="badge bg-danger">Live Now</span>
                                                    @elseif($isToday)
                                                        <span class="badge bg-success">Today</span>
                                                    @elseif($isTomorrow)
                                                        <span class="badge bg-warning">Tomorrow</span>
                                                    @else
                                                        <span class="badge bg-primary">{{ $meetingDate->format('M d') }}</span>
                                                    @endif
                                                </div>
                                                <div class="mb-2">
                                                    <strong>{{ $meetingDate->format('h:i A') }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $meetingDate->diffForHumans() }}</small>
                                                </div>
                                                @if($meeting->meeting_url)
                                                    <a href="{{ $meeting->meeting_url }}" 
                                                       target="_blank" 
                                                       class="btn btn-primary btn-sm"
                                                       onclick="logMeetingAccess({{ $meeting->meeting_id }})">
                                                        <i class="bi bi-camera-video me-1"></i>Join Meeting
                                                    </a>
                                                @else
                                                    <span class="text-muted small">No link available</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">No Upcoming Meetings</h5>
                                    <p class="text-muted">You don't have any meetings scheduled yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Past Meetings -->
                    @if($pastMeetings->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-clock-history me-2"></i>Past Meetings
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($pastMeetings->take(5) as $meeting)
                                    @php
                                        $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                                        $attended = $meeting->attendanceLogs->where('student_id', $studentId)->isNotEmpty();
                                    @endphp
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{ $meeting->title }}</div>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>{{ $meetingDate->format('M d, Y h:i A') }}
                                                •
                                                <i class="bi bi-mortarboard me-1"></i>{{ $meeting->batch->program->program_name ?? 'N/A' }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            @if($attended)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Attended
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Absent
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Today's Schedule -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-calendar-day me-2"></i>Today's Schedule
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($todaysMeetings->count() > 0)
                                @foreach($todaysMeetings as $meeting)
                                    @php $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date); @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                                        <div>
                                            <div class="fw-bold small">{{ $meeting->title }}</div>
                                            <small class="text-muted">{{ $meetingDate->format('h:i A') }}</small>
                                        </div>
                                        @if($meeting->meeting_url)
                                            <a href="{{ $meeting->meeting_url }}" 
                                               target="_blank" 
                                               class="btn btn-outline-primary btn-sm"
                                               onclick="logMeetingAccess({{ $meeting->meeting_id }})">
                                                Join
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">No meetings scheduled for today</p>
                            @endif
                        </div>
                    </div>

                    <!-- Meeting Statistics -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-bar-chart me-2"></i>My Attendance
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                                $totalMeetings = $upcomingMeetings->count() + $pastMeetings->count();
                                $attendedMeetings = $pastMeetings->filter(function($meeting) use ($studentId) {
                                    return $meeting->attendanceLogs->where('student_id', $studentId)->isNotEmpty();
                                })->count();
                                // Fix division by zero error
                                $attendanceRate = $pastMeetings->count() > 0 ? ($attendedMeetings / $pastMeetings->count()) * 100 : 0;
                            @endphp
                            
                            <div class="text-center">
                                <div class="display-6 fw-bold text-primary">{{ number_format($attendanceRate, 1) }}%</div>
                                <p class="text-muted mb-0">Attendance Rate</p>
                            </div>
                            
                            <div class="progress mt-3" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $attendanceRate }}%"></div>
                            </div>
                            
                            <div class="row text-center mt-3">
                                <div class="col-6">
                                    <div class="h6 text-success">{{ $attendedMeetings }}</div>
                                    <small class="text-muted">Attended</small>
                                </div>
                                <div class="col-6">
                                    <div class="h6 text-danger">{{ $pastMeetings->count() - $attendedMeetings }}</div>
                                    <small class="text-muted">Missed</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-lightning me-2"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('student.calendar') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-calendar3 me-2"></i>View Calendar
                                </a>
                                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-house me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function logMeetingAccess(meetingId) {
    // Log that the student accessed the meeting link
    fetch(`{{ route('student.meetings.access', ['id' => ':meetingId']) }}`.replace(':meetingId', meetingId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            access_time: new Date().toISOString()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Meeting access logged successfully');
        }
    })
    .catch(error => {
        console.error('Error logging meeting access:', error);
    });
}
</script>
@endsection
