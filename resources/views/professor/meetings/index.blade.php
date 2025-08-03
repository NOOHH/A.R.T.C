@extends('professor.professor-layouts.professor-layout')

@section('title', 'My Meetings')

@php
    // Build meetingData for all meetings before any HTML
    $meetingData = [];
    foreach ($meetings as $meeting) {
        $meetingData[$meeting->meeting_id] = [
            'status' => $meeting->status,
            'actual_start_time' => $meeting->actual_start_time,
            'actual_end_time' => $meeting->actual_end_time
        ];
    }
@endphp
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">My Meetings</h1>
                    <p class="text-muted">Manage your class meetings and attendance</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- View Toggle -->
                    
                    <!-- Program Filter -->
                    <select class="form-select" id="programFilter" style="width: 200px;">
                        <option value="">All Programs</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                    @if($canCreateMeetings)
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMeetingModal">
                            <i class="bi bi-calendar-plus me-2"></i>Create Meeting
                        </button>
                    @else
                        <button type="button" class="btn btn-secondary" disabled title="Meeting creation is disabled by administrator">
                            <i class="bi bi-lock me-2"></i>Create Meeting (Disabled)
                        </button>
                    @endif
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-event fs-1 me-3"></i>
                                <div>
                                    <h5 class="card-title">Total Meetings</h5>
                                    <h2 class="mb-0">{{ $meetings->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle fs-1 me-3"></i>
                                <div>
                                    <h5 class="card-title">Completed</h5>
                                    <h2 class="mb-0">{{ $meetings->where('status', 'completed')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock fs-1 me-3"></i>
                                <div>
                                    <h5 class="card-title">Upcoming</h5>
                                    <h2 class="mb-0">{{ $meetings->where('meeting_date', '>', now())->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-people fs-1 me-3"></i>
                                <div>
                                    <h5 class="card-title">My Batches</h5>
                                    <h2 class="mb-0">{{ $batches->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card View -->
            <div id="cardView" class="view-section">
                {{-- Only use $professorPrograms and $batches as provided by the controller. Do not fetch or display any other programs or batches. --}}
                @if($professorPrograms->count() > 0)
                    @foreach($professorPrograms as $program)
                        <div class="program-section" data-program-id="{{ $program->program_id }}" style="margin-bottom: 2rem;">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">{{ $program->program_name }}</h5>
                                </div>
                                <div class="card-body">
@foreach($program->batches as $batch)
                                        <div class="batch-section mb-4">
                                            <h6 class="text-muted mb-3">{{ $batch->batch_name }}</h6>
                                            
                                            <!-- Meeting Tabs -->
                                            <ul class="nav nav-pills mb-3" id="pills-tab-{{ $program->program_id }}-{{ $batch->batch_id }}" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="pills-current-tab-{{ $program->program_id }}-{{ $batch->batch_id }}" data-bs-toggle="pill" data-bs-target="#pills-current-{{ $program->program_id }}-{{ $batch->batch_id }}" type="button" role="tab">
                                                        Current Meetings
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-today-tab-{{ $program->program_id }}-{{ $batch->batch_id }}" data-bs-toggle="pill" data-bs-target="#pills-today-{{ $program->program_id }}-{{ $batch->batch_id }}" type="button" role="tab">
                                                        Today's Meetings
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-upcoming-tab-{{ $program->program_id }}-{{ $batch->batch_id }}" data-bs-toggle="pill" data-bs-target="#pills-upcoming-{{ $program->program_id }}-{{ $batch->batch_id }}" type="button" role="tab">
                                                        Upcoming
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-finished-tab-{{ $program->program_id }}-{{ $batch->batch_id }}" data-bs-toggle="pill" data-bs-target="#pills-finished-{{ $program->program_id }}-{{ $batch->batch_id }}" type="button" role="tab">
                                                        Finished
                                                    </button>
                                                </li>
                                            </ul>
                                            
                                            <!-- Meeting Tab Content -->
                                            <div class="tab-content" id="pills-tabContent-{{ $program->program_id }}-{{ $batch->batch_id }}">
                                                @php
                                                    $batchMeetings = $meetings->where('batch_id', $batch->batch_id);
                                                    // Current: status ongoing
                                                    $currentMeetings = $batchMeetings->where('status', 'ongoing');
                                                    $currentIds = $currentMeetings->pluck('meeting_id')->toArray();
                                                    // Today's: meeting_date is today, not in current
                                                    $todayMeetings = $batchMeetings->filter(function($meeting) use ($currentIds) {
                                                        return \Carbon\Carbon::parse($meeting->meeting_date)->isToday() && !in_array($meeting->meeting_id, $currentIds);
                                                    });
                                                    // Upcoming: meeting_date in future and not completed, not in current
                                                    $upcomingMeetings = $batchMeetings->filter(function($meeting) use ($currentIds) {
                                                        return \Carbon\Carbon::parse($meeting->meeting_date)->isFuture() && $meeting->status != 'completed' && !in_array($meeting->meeting_id, $currentIds);
                                                    });
                                                    // Finished: status completed, not in current
                                                    $finishedMeetings = $batchMeetings->where('status', 'completed')->reject(function($meeting) use ($currentIds) {
                                                        return in_array($meeting->meeting_id, $currentIds);
                                                    });
                                                @endphp
                                                
                                                <!-- Current Meetings -->
                                                <div class="tab-pane fade show active" id="pills-current-{{ $program->program_id }}-{{ $batch->batch_id }}" role="tabpanel">
                                                    <div class="meeting-carousel">
                                                        @forelse($currentMeetings as $meeting)
                                                            <div class="meeting-card">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <h6 class="card-title">{{ $meeting->title }}</h6>
                                                                        <p class="card-text"><small>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y h:i A') }}</small></p>
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <span class="badge bg-warning">Ongoing</span>
                                                                            <div class="btn-group btn-group-sm">
                                                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="event.stopPropagation(); openMeetingModal('{{ $meeting->meeting_id }}', '{{ $meeting->title }}', '{{ $meeting->meeting_url }}')">
                                                                                    <i class="bi bi-play-circle"></i>
                                                                                </button>
                                                                                </button>
                                                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#editMeetingModal"
                                                                                    data-meeting-id="{{ $meeting->meeting_id }}"
                                                                                    data-meeting-title="{{ $meeting->title }}"
                                                                                    data-meeting-date="{{ $meeting->meeting_date }}"
                                                                                    data-meeting-link="{{ $meeting->meeting_url }}"
                                                                                    data-meeting-description="{{ $meeting->description }}"
                                                                                    data-meeting-status="{{ $meeting->status }}">
                                                                                    <i class="bi bi-pencil"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <p class="text-muted">No current meetings</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                                
                                                <!-- Today's Meetings -->
                                                <div class="tab-pane fade" id="pills-today-{{ $program->program_id }}-{{ $batch->batch_id }}" role="tabpanel">
                                                    <div class="meeting-carousel">
                                                        @forelse($todayMeetings as $meeting)
                                                            <div class="meeting-card">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <h6 class="card-title">{{ $meeting->title }}</h6>
                                                                        <p class="card-text"><small>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('h:i A') }}</small></p>
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <span class="badge bg-info">Today</span>
                                                                            <div class="btn-group btn-group-sm">
                                                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="event.stopPropagation(); openMeetingModal('{{ $meeting->meeting_id }}', '{{ $meeting->title }}', '{{ $meeting->meeting_url }}')">
                                                                                    <i class="bi bi-play-circle"></i>
                                                                                </button>
                                                                                </button>
                                                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#editMeetingModal"
                                                                                    data-meeting-id="{{ $meeting->meeting_id }}"
                                                                                    data-meeting-title="{{ $meeting->title }}"
                                                                                    data-meeting-date="{{ $meeting->meeting_date }}"
                                                                                    data-meeting-link="{{ $meeting->meeting_url }}"
                                                                                    data-meeting-description="{{ $meeting->description }}"
                                                                                    data-meeting-status="{{ $meeting->status }}">
                                                                                    <i class="bi bi-pencil"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <p class="text-muted">No meetings today</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                                
                                                <!-- Upcoming Meetings -->
                                                <div class="tab-pane fade" id="pills-upcoming-{{ $program->program_id }}-{{ $batch->batch_id }}" role="tabpanel">
                                                    <div class="meeting-carousel">
                                                        @forelse($upcomingMeetings as $meeting)
                                                            <div class="meeting-card">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <h6 class="card-title">{{ $meeting->title }}</h6>
                                                                        <p class="card-text"><small>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y h:i A') }}</small></p>
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <span class="badge bg-primary">Upcoming</span>
                                                                            <div class="btn-group btn-group-sm">
                                                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#editMeetingModal"
                                                                                    data-meeting-id="{{ $meeting->meeting_id }}"
                                                                                    data-meeting-title="{{ $meeting->title }}"
                                                                                    data-meeting-date="{{ $meeting->meeting_date }}"
                                                                                    data-meeting-link="{{ $meeting->meeting_url }}"
                                                                                    data-meeting-description="{{ $meeting->description }}"
                                                                                    data-meeting-status="{{ $meeting->status }}">
                                                                                    <i class="bi bi-pencil"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <p class="text-muted">No upcoming meetings</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                                
                                                <!-- Finished Meetings -->
                                                <div class="tab-pane fade" id="pills-finished-{{ $program->program_id }}-{{ $batch->batch_id }}" role="tabpanel">
                                                    <div class="meeting-carousel">
                                                        @forelse($finishedMeetings as $meeting)
                                                            <div class="meeting-card">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <h6 class="card-title">{{ $meeting->title }}</h6>
                                                                        <p class="card-text"><small>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y h:i A') }}</small></p>
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <span class="badge bg-success">Completed</span>
                                                                            <div class="btn-group btn-group-sm">
                                                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#editMeetingModal"
                                                                                    data-meeting-id="{{ $meeting->meeting_id }}"
                                                                                    data-meeting-title="{{ $meeting->title }}"
                                                                                    data-meeting-date="{{ $meeting->meeting_date }}"
                                                                                    data-meeting-link="{{ $meeting->meeting_url }}"
                                                                                    data-meeting-description="{{ $meeting->description }}"
                                                                                    data-meeting-status="{{ $meeting->status }}">
                                                                                    <i class="bi bi-pencil"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <p class="text-muted">No finished meetings</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No programs assigned</h5>
                        <p class="text-muted">Contact your administrator to get assigned to programs!</p>
                    </div>
                @endif
            </div>

            <!-- Table View -->
            <div id="tableView" class="view-section" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Meetings</h5>
                    </div>
                    <div class="card-body">
                        @if($meetings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Program</th>
                                            <th>Batch</th>
                                            <th>Date & Time</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($meetings as $meeting)
                                            <tr>
                                                <td>{{ $meeting->title }}</td>
                                                <td>{{ $meeting->batch && $meeting->batch->program ? $meeting->batch->program->program_name : 'Unknown Program' }}</td>
                                                <td>{{ $meeting->batch ? $meeting->batch->batch_name : 'Unknown Batch' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y h:i A') }}</td>
                                                <td>
                                                    @if($meeting->status == 'ongoing')
                                                        <span class="badge bg-warning">Ongoing</span>
                                                    @elseif($meeting->status == 'completed')
                                                        <span class="badge bg-success">Completed</span>
                                                    @elseif(\Carbon\Carbon::parse($meeting->meeting_date)->isToday())
                                                        <span class="badge bg-info">Today</span>
                                                    @elseif(\Carbon\Carbon::parse($meeting->meeting_date)->isFuture())
                                                        <span class="badge bg-primary">Upcoming</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($meeting->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        @if($meeting->status == 'ongoing')
                                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="openMeetingModal('{{ $meeting->meeting_id }}', '{{ $meeting->title }}', '{{ $meeting->meeting_url }}')">
                                                                <i class="bi bi-play-circle"></i>
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editMeetingModal"
                                                            data-meeting-id="{{ $meeting->meeting_id }}"
                                                            data-meeting-title="{{ $meeting->title }}"
                                                            data-meeting-date="{{ $meeting->meeting_date }}"
                                                            data-meeting-link="{{ $meeting->meeting_url }}"
                                                            data-meeting-description="{{ $meeting->description }}"
                                                            data-meeting-status="{{ $meeting->status }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
                                <h5 class="text-muted">No meetings found</h5>
                                <p class="text-muted">Create your first meeting to get started!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($canCreateMeetings)
<!-- Create Meeting Modal -->
<div class="modal fade" id="createMeetingModal" tabindex="-1" aria-labelledby="createMeetingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createMeetingModalLabel">
                    <i class="bi bi-calendar-plus me-2"></i>Create New Meeting
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                                    <form action="{{ route('professor.meetings.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="meeting_title" class="form-label">Meeting Title</label>
                            <input type="text" class="form-control" id="meeting_title" name="meeting_title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="meeting_date" class="form-label">Date & Time</label>
                            <input type="datetime-local" class="form-control" id="meeting_date" name="meeting_date" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="program_ids" class="form-label">Programs *</label>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="programDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span id="programSelectionText">Select Programs</span>
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="programDropdown">
                                    <li>
                                        <div class="form-check px-3 py-2">
                                            <input class="form-check-input" type="checkbox" id="selectAllPrograms">
                                            <label class="form-check-label fw-bold" for="selectAllPrograms">
                                                Select All Programs
                                            </label>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    @foreach($programs as $program)
                                        <li>
                                            <div class="form-check px-3 py-2">
                                                <input class="form-check-input program-checkbox" type="checkbox" 
                                                       name="program_ids[]" value="{{ $program->program_id }}" 
                                                       id="program_{{ $program->program_id }}"
                                                       data-video-link="{{ $program->pivot->video_link ?? '' }}">
                                                <label class="form-check-label" for="program_{{ $program->program_id }}">
                                                    {{ $program->program_name }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="batch_ids" class="form-label">Batches *</label>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="batchDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span id="batchSelectionText">Select Programs First</span>
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="batchDropdown">
                                    <li>
                                        <div class="form-check px-3 py-2">
                                            <input class="form-check-input" type="checkbox" id="selectAllBatches">
                                            <label class="form-check-label fw-bold" for="selectAllBatches">
                                                Select All Visible Batches
                                            </label>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    @foreach($batches as $batch)
                                        <li class="batch-option" data-program-id="{{ $batch->program_id }}" style="display: none;">
                                            <div class="form-check px-3 py-2">
                                                <input class="form-check-input batch-checkbox" type="checkbox" 
                                                       name="batch_ids[]" value="{{ $batch->batch_id }}" 
                                                       id="batch_{{ $batch->batch_id }}">
                                                <label class="form-check-label" for="batch_{{ $batch->batch_id }}">
                                                    <span class="badge bg-primary me-2">{{ $batch->program ? $batch->program->program_name : 'Unknown Program' }}</span>
                                                    {{ $batch->batch_name }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="form-text">Batches are grouped by program. Select programs first to see available batches.</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="meeting_link" class="form-label">Meeting Link</label>
                            <input type="url" class="form-control" id="meeting_link" name="meeting_link" required>
                            <div class="form-text">This will be auto-filled from your program meeting link</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-calendar-plus me-2"></i>Create Meeting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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
            <form id="editMeetingForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_meeting_title" class="form-label">Meeting Title</label>
                            <input type="text" class="form-control" id="edit_meeting_title" name="meeting_title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_meeting_date" class="form-label">Date & Time</label>
                            <input type="datetime-local" class="form-control" id="edit_meeting_date" name="meeting_date" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_meeting_link" class="form-label">Meeting Link</label>
                        <input type="url" class="form-control" id="edit_meeting_link" name="meeting_link" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="scheduled">Scheduled</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Update Meeting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Meeting Control Modal -->
<div class="modal fade" id="meetingControlModal" tabindex="-1" aria-labelledby="meetingControlModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="meetingControlModalLabel">
                    <i class="bi bi-camera-video me-2"></i>Meeting Control
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6 id="modalMeetingTitle">Meeting Title</h6>
                        <div class="meeting-timer mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock me-2"></i>
                                <span id="meetingTimer" class="fw-bold ms-2">00:00:00</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-success" id="startMeetingBtn">
                                <i class="bi bi-play-circle me-2"></i>Start Meeting
                            </button>
                            <button type="button" class="btn btn-primary" id="openMeetingBtn" style="display: none;">
                                <i class="bi bi-camera-video me-2"></i>Open Meeting Link
                            </button>
                            <button type="button" class="btn btn-danger" id="finishMeetingBtn" style="display: none;">
                                <i class="bi bi-stop-circle me-2"></i>Finish Meeting
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Quick Stats</h6>
                            </div>
                            <div class="card-body">
                                <p><small>Students in batch: <span id="totalStudents">0</span></small></p>
                                <p><small>Joined meeting: <span id="joinedStudents">0</span></small></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Attendance Section -->
                <div class="attendance-section mt-4" style="display: none;">
                    <h6>Meeting Attendance</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Meeting Status</th>
                                    <th>Attendance</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="manageAttendanceBtn" style="display: none;">
                    Manage Attendance
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.view-section {
    transition: all 0.3s ease;
}

.meeting-carousel {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding: 1rem 0;
}

.meeting-card {
    min-width: 250px;
    cursor: pointer;
    transition: transform 0.2s;
}

.meeting-card:hover {
    transform: translateY(-2px);
}

.meeting-card .card {
    height: 100%;
    border: 1px solid #ddd;
}

.program-section {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    overflow: hidden;
}

.batch-section {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 1rem;
}

.batch-section:last-child {
    border-bottom: none;
}

.nav-pills .nav-link {
    background-color: #f8f9fa;
    color: #495057;
    border: 1px solid #dee2e6;
    margin-right: 0.25rem;
}

.nav-pills .nav-link.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.meeting-timer {
    background-color: #f8f9fa;
    padding: 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
}

.meeting-card {
    transition: transform 0.2s;
    border: 1px solid #dee2e6;
    margin-bottom: 1rem;
}

.meeting-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.badge-meeting-status {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.carousel-control-prev,
.carousel-control-next {
    width: 5%;
}

.carousel-item {
    padding: 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
}
</style>
@endpush

@push('scripts')
<script>
// Pass batches data to JavaScript
window.allBatches = {!! json_encode($batches) !!};
// Pass all meeting status data to JavaScript (accumulated from all programs/batches)
window.meetingData = {!! json_encode($meetingData) !!};
</script>
<script>
let meetingTimer;
let startTime;
let currentMeetingId;
let currentMeetingLink;

document.addEventListener('DOMContentLoaded', function() {
    // Ensure all elements exist before adding event listeners
    const programFilter = document.getElementById('programFilter');
    const programCheckboxes = document.querySelectorAll('.program-checkbox');
    const batchCheckboxes = document.querySelectorAll('.batch-checkbox');
    const selectAllPrograms = document.getElementById('selectAllPrograms');
    const selectAllBatches = document.getElementById('selectAllBatches');
    const meetingLinkInput = document.getElementById('meeting_link');
    const programSelectionText = document.getElementById('programSelectionText');
    const batchSelectionText = document.getElementById('batchSelectionText');
    
    // View toggle functionality
    const viewModeRadios = document.querySelectorAll('input[name="viewMode"]');
    const cardView = document.getElementById('cardView');
    const tableView = document.getElementById('tableView');
    
    viewModeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'card') {
                cardView.style.display = 'block';
                tableView.style.display = 'none';
            } else {
                cardView.style.display = 'none';
                tableView.style.display = 'block';
            }
        });
    });
    
    // Program filter functionality
    if (programFilter) {
        programFilter.addEventListener('change', function() {
            const selectedProgram = this.value;
            const programSections = document.querySelectorAll('.program-section');
            
            programSections.forEach(section => {
                if (selectedProgram === '' || section.dataset.programId === selectedProgram) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    }
    
    // Prevent dropdown from closing when clicking checkboxes
    document.querySelectorAll('.dropdown-menu').forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    // Handle "Select All Programs" checkbox
    if (selectAllPrograms) {
        selectAllPrograms.addEventListener('change', function() {
            programCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBatchVisibility();
            updateSelectionTexts();
        });
    }
    
    // Handle "Select All Batches" checkbox
    if (selectAllBatches) {
        selectAllBatches.addEventListener('change', function() {
            const visibleBatches = document.querySelectorAll('.batch-option:not([style*="display: none"]) .batch-checkbox');
            visibleBatches.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectionTexts();
        });
    }

    // Handle program checkbox changes
    programCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBatchVisibility();
            updateMeetingLink();
            updateSelectionTexts();
            
            // Update "Select All Programs" state
            const allChecked = Array.from(programCheckboxes).every(cb => cb.checked);
            const noneChecked = Array.from(programCheckboxes).every(cb => !cb.checked);
            selectAllPrograms.checked = allChecked;
            selectAllPrograms.indeterminate = !allChecked && !noneChecked;
        });
    });
    
    // Handle batch checkbox changes
    batchCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectionTexts();
            
            // Update "Select All Batches" state
            const visibleBatches = document.querySelectorAll('.batch-option:not([style*="display: none"]) .batch-checkbox');
            const allChecked = Array.from(visibleBatches).every(cb => cb.checked);
            const noneChecked = Array.from(visibleBatches).every(cb => !cb.checked);
            selectAllBatches.checked = allChecked;
            selectAllBatches.indeterminate = !allChecked && !noneChecked;
        });
    });
    
    function updateSelectionTexts() {
        // Update program selection text
        const selectedPrograms = Array.from(programCheckboxes).filter(cb => cb.checked);
        if (selectedPrograms.length === 0) {
            programSelectionText.textContent = 'Select Programs';
        } else if (selectedPrograms.length === 1) {
            programSelectionText.textContent = selectedPrograms[0].nextElementSibling.textContent;
        } else {
            programSelectionText.textContent = `${selectedPrograms.length} Programs Selected`;
        }
        
        // Update batch selection text
        const selectedBatches = Array.from(batchCheckboxes).filter(cb => cb.checked);
        if (selectedBatches.length === 0) {
            batchSelectionText.textContent = selectedPrograms.length > 0 ? 'Select Batches' : 'Select Programs First';
        } else if (selectedBatches.length === 1) {
            const batchLabel = selectedBatches[0].nextElementSibling.textContent.replace(/^\s*\w+\s*/, ''); // Remove badge text
            batchSelectionText.textContent = batchLabel.trim();
        } else {
            batchSelectionText.textContent = `${selectedBatches.length} Batches Selected`;
        }
    }
    
    function updateBatchVisibility() {
        const selectedPrograms = Array.from(programCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        // Show/hide batch options based on selected programs
        document.querySelectorAll('.batch-option').forEach(batchOption => {
            const programId = batchOption.getAttribute('data-program-id');
            if (selectedPrograms.includes(programId)) {
                batchOption.style.display = 'block';
            } else {
                batchOption.style.display = 'none';
                // Uncheck hidden batches
                const checkbox = batchOption.querySelector('.batch-checkbox');
                if (checkbox) checkbox.checked = false;
            }
        });
        
        // Update "Select All Batches" state
        const visibleBatches = document.querySelectorAll('.batch-option:not([style*="display: none"]) .batch-checkbox');
        const allChecked = Array.from(visibleBatches).every(cb => cb.checked);
        const noneChecked = Array.from(visibleBatches).every(cb => !cb.checked);
        selectAllBatches.checked = allChecked;
        selectAllBatches.indeterminate = !allChecked && !noneChecked;
    }
    
    function updateMeetingLink() {
        const selectedPrograms = Array.from(programCheckboxes).filter(cb => cb.checked);
        
        if (selectedPrograms.length === 1 && meetingLinkInput && meetingLinkInput !== null) {
            const videoLink = selectedPrograms[0].getAttribute('data-video-link');
            if (videoLink && videoLink.trim() !== '') {
                meetingLinkInput.value = videoLink;
            }
        }
    }
    
    // Form validation
    const createMeetingForm = document.querySelector('#createMeetingModal form');
    if (createMeetingForm) {
        console.log('Form found:', createMeetingForm);
        createMeetingForm.addEventListener('submit', function(event) {
            console.log('Form submission triggered');
            const selectedPrograms = Array.from(programCheckboxes).filter(cb => cb.checked);
            const selectedBatches = Array.from(batchCheckboxes).filter(cb => cb.checked);
            
            // Debug: Log what's being selected
            console.log('Selected programs:', selectedPrograms.map(p => ({ id: p.value, name: p.nextElementSibling.textContent })));
            console.log('Selected batches:', selectedBatches.map(b => ({ id: b.value, name: b.nextElementSibling.textContent })));
            
            // Clear previous error messages
            document.querySelectorAll('.validation-error').forEach(el => el.remove());
            
            let hasErrors = false;
            
            if (selectedPrograms.length === 0) {
                showValidationError('program_ids', 'Please select at least one program.');
                hasErrors = true;
            }
            
            if (selectedBatches.length === 0) {
                showValidationError('batch_ids', 'Please select at least one batch.');
                hasErrors = true;
            }
            
            // Ensure hidden checkboxes are unchecked to avoid sending empty arrays
            if (!hasErrors) {
                // Uncheck all hidden program checkboxes
                programCheckboxes.forEach(checkbox => {
                    if (!checkbox.checked) {
                        checkbox.disabled = true;
                    }
                });
                
                // Uncheck all hidden batch checkboxes
                batchCheckboxes.forEach(checkbox => {
                    if (!checkbox.checked) {
                        checkbox.disabled = true;
                    }
                });
                
                console.log('Form validation passed, submitting...');
            }
            
            if (hasErrors) {
                event.preventDefault();
                console.log('Form validation failed, preventing submission');
                return false;
            }
        });
    } else {
        console.error('Create meeting form not found!');
        // Try alternative selectors
        const altForm1 = document.querySelector('form[action*="professor.meetings.store"]');
        const altForm2 = document.querySelector('form[action*="meetings"]');
        if (altForm1) {
            console.log('Found form with alternative selector 1:', altForm1);
        } else if (altForm2) {
            console.log('Found form with alternative selector 2:', altForm2);
        }
    }
    
    function showValidationError(fieldName, message) {
        const container = fieldName === 'program_ids' 
            ? document.getElementById('programDropdown').parentElement
            : document.getElementById('batchDropdown').parentElement;
            
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error text-danger small mt-1';
        errorDiv.textContent = message;
        container.appendChild(errorDiv);
    }

    // Meeting control functionality
    const startMeetingBtn = document.getElementById('startMeetingBtn');
    const openMeetingBtn = document.getElementById('openMeetingBtn');
    const finishMeetingBtn = document.getElementById('finishMeetingBtn');
    const manageAttendanceBtn = document.getElementById('manageAttendanceBtn');
    const attendanceSection = document.querySelector('.attendance-section');

    if (startMeetingBtn) {
        startMeetingBtn.addEventListener('click', function() {
            startMeeting();
        });
    }

    if (openMeetingBtn) {
        openMeetingBtn.addEventListener('click', function() {
            if (currentMeetingLink) {
                window.open(currentMeetingLink, '_blank');
            }
        });
    }

    if (finishMeetingBtn) {
        finishMeetingBtn.addEventListener('click', function() {
            finishMeeting();
        });
    }

    if (manageAttendanceBtn) {
        manageAttendanceBtn.addEventListener('click', function() {
            if (attendanceSection) {
                attendanceSection.style.display = attendanceSection.style.display === 'none' ? 'block' : 'none';
                this.textContent = attendanceSection.style.display === 'none' ? 'Manage Attendance' : 'Hide Attendance';
            }
        });
    }

    // Edit meeting modal
    const editMeetingModal = document.getElementById('editMeetingModal');
    if (editMeetingModal) {
        editMeetingModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const meetingId = button.getAttribute('data-meeting-id');
            const meetingTitle = button.getAttribute('data-meeting-title');
            const meetingDate = button.getAttribute('data-meeting-date');
            const meetingLink = button.getAttribute('data-meeting-link');
            const meetingDescription = button.getAttribute('data-meeting-description');
            const meetingStatus = button.getAttribute('data-meeting-status');
            
            document.getElementById('edit_meeting_title').value = meetingTitle;
            document.getElementById('edit_meeting_date').value = meetingDate.slice(0, 16); // Format for datetime-local
            document.getElementById('edit_meeting_link').value = meetingLink;
            document.getElementById('edit_description').value = meetingDescription || '';
            document.getElementById('edit_status').value = meetingStatus;
            
            document.getElementById('editMeetingForm').action = `/professor/meetings/${meetingId}`;
        });
    }
});

function openMeetingModal(meetingId, meetingTitle, meetingLink) {
    currentMeetingId = meetingId;
    currentMeetingLink = meetingLink;
    document.getElementById('modalMeetingTitle').textContent = meetingTitle;
    document.getElementById('meetingTimer').textContent = '00:00:00';

    // Get meeting status info
    let meetingInfo = (window.meetingData && window.meetingData[meetingId]) ? window.meetingData[meetingId] : {};
    console.log('openMeetingModal: meetingId', meetingId, 'meetingInfo', meetingInfo); // DEBUG
    let status = meetingInfo.status;
    let actualStart = meetingInfo.actual_start_time;
    let actualEnd = meetingInfo.actual_end_time;

    // Set button states based on meeting status
    if (status === 'ongoing' && actualStart && !actualEnd) {
        document.getElementById('startMeetingBtn').style.display = 'none';
        document.getElementById('openMeetingBtn').style.display = 'block';
        document.getElementById('finishMeetingBtn').style.display = 'block';
        document.getElementById('manageAttendanceBtn').style.display = 'block';
    } else if ((status === 'scheduled' || !status) && !actualStart) {
        document.getElementById('startMeetingBtn').style.display = 'block';
        document.getElementById('openMeetingBtn').style.display = 'none';
        document.getElementById('finishMeetingBtn').style.display = 'none';
        document.getElementById('manageAttendanceBtn').style.display = 'none';
    } else if (status === 'completed' || actualEnd) {
        document.getElementById('startMeetingBtn').style.display = 'none';
        document.getElementById('openMeetingBtn').style.display = 'none';
        document.getElementById('finishMeetingBtn').style.display = 'none';
        document.getElementById('manageAttendanceBtn').style.display = 'block';
    } else {
        // Fallback: hide all except start
        document.getElementById('startMeetingBtn').style.display = 'block';
        document.getElementById('openMeetingBtn').style.display = 'none';
        document.getElementById('finishMeetingBtn').style.display = 'none';
        document.getElementById('manageAttendanceBtn').style.display = 'none';
    }
    document.querySelector('.attendance-section').style.display = 'none';

    // Fetch student stats for this meeting
    fetchMeetingStats(meetingId);

    // Show modal
    new bootstrap.Modal(document.getElementById('meetingControlModal')).show();
}

function fetchMeetingStats(meetingId) {
    fetch(`/professor/meetings/${meetingId}/stats`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalStudents').textContent = data.total_students || 0;
            document.getElementById('joinedStudents').textContent = data.joined_students || 0;
            
            // Update window.meetingData with latest meeting status
            if (data.meeting && window.meetingData) {
                window.meetingData[meetingId] = {
                    status: data.meeting.status,
                    actual_start_time: data.meeting.actual_start_time,
                    actual_end_time: data.meeting.actual_end_time
                };
            }
        } else {
            console.error('Failed to fetch meeting stats:', data.message);
            document.getElementById('totalStudents').textContent = '0';
            document.getElementById('joinedStudents').textContent = '0';
        }
    })
    .catch(error => {
        console.error('Error fetching meeting stats:', error);
        document.getElementById('totalStudents').textContent = '0';
        document.getElementById('joinedStudents').textContent = '0';
    });
}

function startMeeting() {
    // Update button states
    document.getElementById('startMeetingBtn').style.display = 'none';
    document.getElementById('openMeetingBtn').style.display = 'block';
    document.getElementById('finishMeetingBtn').style.display = 'block';
    document.getElementById('manageAttendanceBtn').style.display = 'block';
    
    // Start timer
    startTime = new Date();
    meetingTimer = setInterval(updateTimer, 1000);
    
    // Update meeting status on server
    fetch(`/professor/meetings/${currentMeetingId}/start`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              console.log('Meeting started successfully');
              // Refresh meeting stats to get updated status
              fetchMeetingStats(currentMeetingId);
          }
      });
}

function finishMeeting() {
    // Stop timer
    if (meetingTimer) {
        clearInterval(meetingTimer);
    }
    
    // Update meeting status on server
    fetch(`/professor/meetings/${currentMeetingId}/finish`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              console.log('Meeting finished successfully');
              // Refresh page or update UI
              location.reload();
          }
      });
}

function updateTimer() {
    const now = new Date();
    const diff = now - startTime;
    
    const hours = Math.floor(diff / 3600000);
    const minutes = Math.floor((diff % 3600000) / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);
    
    const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    document.getElementById('meetingTimer').textContent = timeString;
}
</script>
@endpush
