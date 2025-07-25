@extends('admin.admin-dashboard-layout')

@section('title', 'Professor Meetings - ' . $professor->professor_name)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">{{ $professor->professor_name }} - Meetings</h2>
                    <p class="text-muted">View and manage professor's meetings</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.professors.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Professors
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMeetingModal"
                            data-professor-id="{{ $professor->professor_id }}"
                            data-professor-name="{{ $professor->professor_name }}">
                        <i class="bi bi-calendar-plus me-2"></i>Create Meeting
                    </button>
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
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock fs-1 me-3"></i>
                                <div>
                                    <h5 class="card-title">Live</h5>
                                    <h2 class="mb-0">{{ $currentMeetings->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                        <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-day fs-1 me-3"></i>
                                <div>
                                    <h5 class="card-title">Current</h5>
                                    <h2 class="mb-0">{{ $todayMeetings->count() }}</h2>
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
                                    <h2 class="mb-0">{{ $finishedMeetings->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meeting Tabs -->
            <div class="card shadow border-0">
                <div class="card-header bg-white border-bottom">
                    <ul class="nav nav-pills card-header-pills" id="meeting-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="current-tab" data-bs-toggle="pill" data-bs-target="#current" type="button" role="tab">
                                <i class="bi bi-broadcast me-2"></i>Live Meetings <span class="badge bg-warning ms-2">{{ $currentMeetings->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                        <button class="nav-link" id="today-tab" data-bs-toggle="pill" data-bs-target="#today" type="button" role="tab">
                                <i class="bi bi-calendar-day me-2"></i>Current Meetings <span class="badge bg-info ms-2">{{ $todayMeetings->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="upcoming-tab" data-bs-toggle="pill" data-bs-target="#upcoming" type="button" role="tab">
                                <i class="bi bi-calendar-plus me-2"></i>Upcoming <span class="badge bg-primary ms-2">{{ $upcomingMeetings->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="finished-tab" data-bs-toggle="pill" data-bs-target="#finished" type="button" role="tab">
                                <i class="bi bi-check-circle me-2"></i>Finished <span class="badge bg-success ms-2">{{ $finishedMeetings->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="meeting-tabContent">
                        <!-- Current Meetings -->
                        <div class="tab-pane fade show active" id="current" role="tabpanel">
                            @if($currentMeetings->count() > 0)
                                <div class="meeting-carousel d-flex flex-row overflow-auto gap-3 pb-2" style="white-space:nowrap;">
                                    @foreach($currentMeetings as $meeting)
                                        <div class="meeting-card card border-warning flex-shrink-0" style="min-width:320px;max-width:340px;">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0">{{ $meeting->title }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">
                                                    <strong>Program:</strong> {{ $meeting->batch->program->program_name }}<br>
                                                    <strong>Batch:</strong> {{ $meeting->batch->batch_name }}<br>
                                                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y h:i A') }}<br>
                                                    <strong>Duration:</strong> {{ $meeting->duration_minutes }} minutes
                                                </p>
                                                @if($meeting->meeting_url)
                                                    <a href="{{ $meeting->meeting_url }}" target="_blank" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-camera-video me-1"></i>Join Meeting
                                                    </a>
                                                @endif
                                                <form action="{{ route('admin.professors.deleteMeeting', [$professor->professor_id, $meeting->meeting_id]) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this meeting?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-broadcast display-1 text-muted"></i>
                                    <h5 class="mt-3">No Current Meetings</h5>
                                    <p class="text-muted">There are no ongoing meetings at the moment.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Today's Meetings -->
                        @php
                            // Build sets of meeting IDs for each tab to avoid overlap
                            $currentIds = $currentMeetings->pluck('meeting_id')->toArray();
                            $todayMeetingsFiltered = $todayMeetings->reject(function($meeting) use ($currentIds) {
                                return in_array($meeting->meeting_id, $currentIds);
                            });
                            $upcomingMeetingsFiltered = $upcomingMeetings->reject(function($meeting) use ($currentIds) {
                                return in_array($meeting->meeting_id, $currentIds);
                            });
                            $finishedMeetingsFiltered = $finishedMeetings->reject(function($meeting) use ($currentIds) {
                                return in_array($meeting->meeting_id, $currentIds);
                            });
                        @endphp
                        <div class="tab-pane fade" id="today" role="tabpanel">
                            @if($todayMeetingsFiltered->count() > 0)
                                <div class="meeting-carousel d-flex flex-row overflow-auto gap-3 pb-2" style="white-space:nowrap;">
                                    @foreach($todayMeetingsFiltered as $meeting)
                                        <div class="meeting-card card border-info flex-shrink-0" style="min-width:320px;max-width:340px;">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0">{{ $meeting->title }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">
                                                    <strong>Program:</strong> {{ $meeting->batch->program->program_name }}<br>
                                                    <strong>Batch:</strong> {{ $meeting->batch->batch_name }}<br>
                                                    <strong>Time:</strong> {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('h:i A') }}<br>
                                                    <strong>Duration:</strong> {{ $meeting->duration_minutes }} minutes<br>
                                                    <strong>Status:</strong> <span class="badge bg-{{ $meeting->status === 'ongoing' ? 'warning' : ($meeting->status === 'completed' ? 'success' : 'secondary') }}">{{ ucfirst($meeting->status) }}</span>
                                                </p>
                                                @if($meeting->description)
                                                    <p class="card-text"><small class="text-muted">{{ $meeting->description }}</small></p>
                                                @endif
                                                @if($meeting->meeting_url)
                                                    <a href="{{ $meeting->meeting_url }}" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="bi bi-camera-video me-1"></i>Meeting Link
                                                    </a>
                                                @endif
                                                <form action="{{ route('admin.professors.deleteMeeting', [$professor->professor_id, $meeting->meeting_id]) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this meeting?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-day display-1 text-muted"></i>
                                    <h5 class="mt-3">No Meetings Today</h5>
                                    <p class="text-muted">There are no meetings scheduled for today.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Upcoming Meetings -->
                        <div class="tab-pane fade" id="upcoming" role="tabpanel">
                            @if($upcomingMeetingsFiltered->count() > 0)
                                <div class="meeting-carousel d-flex flex-row overflow-auto gap-3 pb-2" style="white-space:nowrap;">
                                    @foreach($upcomingMeetingsFiltered as $meeting)
                                        <div class="meeting-card card border-primary flex-shrink-0" style="min-width:320px;max-width:340px;">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">{{ $meeting->title }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">
                                                    <strong>Program:</strong> {{ $meeting->batch->program->program_name }}<br>
                                                    <strong>Batch:</strong> {{ $meeting->batch->batch_name }}<br>
                                                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y h:i A') }}<br>
                                                    <strong>Duration:</strong> {{ $meeting->duration_minutes }} minutes
                                                </p>
                                                @if($meeting->description)
                                                    <p class="card-text"><small class="text-muted">{{ $meeting->description }}</small></p>
                                                @endif
                                                @if($meeting->meeting_url)
                                                    <a href="{{ $meeting->meeting_url }}" target="_blank" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-camera-video me-1"></i>Meeting Link
                                                    </a>
                                                @endif
                                                <form action="{{ route('admin.professors.deleteMeeting', [$professor->professor_id, $meeting->meeting_id]) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this meeting?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-plus display-1 text-muted"></i>
                                    <h5 class="mt-3">No Upcoming Meetings</h5>
                                    <p class="text-muted">There are no meetings scheduled for the future.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Finished Meetings -->
                        <div class="tab-pane fade" id="finished" role="tabpanel">
                            @if($finishedMeetingsFiltered->count() > 0)
                                <div class="meeting-carousel d-flex flex-row overflow-auto gap-3 pb-2" style="white-space:nowrap;">
                                    @foreach($finishedMeetingsFiltered as $meeting)
                                        <div class="meeting-card card border-success flex-shrink-0" style="min-width:320px;max-width:340px;">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0">{{ $meeting->title }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">
                                                    <strong>Program:</strong> {{ $meeting->batch->program->program_name }}<br>
                                                    <strong>Batch:</strong> {{ $meeting->batch->batch_name }}<br>
                                                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y h:i A') }}<br>
                                                    <strong>Duration:</strong> {{ $meeting->duration_minutes }} minutes
                                                </p>
                                                @if($meeting->description)
                                                    <p class="card-text"><small class="text-muted">{{ $meeting->description }}</small></p>
                                                @endif
                                                @if($meeting->actual_start_time && $meeting->actual_end_time)
                                                    <p class="card-text">
                                                        <small class="text-success">
                                                            <i class="bi bi-clock me-1"></i>
                                                            Actual duration: {{ \Carbon\Carbon::parse($meeting->actual_start_time)->diffInMinutes(\Carbon\Carbon::parse($meeting->actual_end_time)) }} minutes
                                                        </small>
                                                    </p>
                                                @endif
                                                <form action="{{ route('admin.professors.deleteMeeting', [$professor->professor_id, $meeting->meeting_id]) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this meeting?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-check-circle display-1 text-muted"></i>
                                    <h5 class="mt-3">No Completed Meetings</h5>
                                    <p class="text-muted">There are no completed meetings yet.</p>
                                </div>
                            @endif
@push('styles')
<style>
.meeting-carousel {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding: 1rem 0;
}
.meeting-card {
    min-width: 300px;
    max-width: 350px;
    flex: 0 0 auto;
    margin-bottom: 1rem;
}
.meeting-card .card {
    height: 100%;
    border: 1px solid #ddd;
}
.meeting-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush

@include('admin.professors.partials.create-meeting-modal')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Meeting Modal -->
@include('admin.professors.partials.create-meeting-modal')

<script>
// Simple dropdown checkbox functionality for meetings page
document.addEventListener('DOMContentLoaded', function() {
    // Prevent dropdown from closing when clicking checkboxes
    document.querySelectorAll('.dropdown-menu').forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    const programCheckboxes = document.querySelectorAll('.program-checkbox');
    const batchCheckboxes = document.querySelectorAll('.batch-checkbox');
    const selectAllPrograms = document.getElementById('selectAllPrograms');
    const selectAllBatches = document.getElementById('selectAllBatches');
    const programSelectionText = document.getElementById('programSelectionText');
    const batchSelectionText = document.getElementById('batchSelectionText');
    
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
});
</script>
@endsection

@push('styles')
<style>
.nav-pills .nav-link {
    color: #495057;
    border-radius: 0.5rem;
}

.nav-pills .nav-link.active {
    background-color: #0d6efd;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.7rem;
}
</style>
@endpush
