@extends('admin.admin-dashboard-layout')

@section('title', 'Batch Enrollment Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Batch Enrollment Management</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBatchModal">
                        <i class="fas fa-plus"></i> Create New Batch
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            @foreach($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Batch Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Batches</h6>
                                            <h3 class="mb-0">{{ $batches->count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-graduation-cap fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Pending</h6>
                                            <h3 class="mb-0">{{ $batches->where('batch_status', 'pending')->count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Available</h6>
                                            <h3 class="mb-0">{{ $batches->where('batch_status', 'available')->count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Ongoing</h6>
                                            <h3 class="mb-0">{{ $batches->where('batch_status', 'ongoing')->count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-play-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Completed</h6>
                                            <h3 class="mb-0">{{ $batches->where('batch_status', 'completed')->count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-trophy fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Closed</h6>
                                            <h3 class="mb-0">{{ $batches->where('batch_status', 'closed')->count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-times-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Batches Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Batch Name</th>
                                    <th>Program</th>
                                    <th>Assigned Professor</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Registration Deadline</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batches as $batch)
                                <tr>
                                    <td>{{ $batch->batch_name }}</td>
                                    <td>{{ $batch->program->program_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($batch->assignedProfessor)
                                            <span class="badge bg-success">
                                                {{ $batch->assignedProfessor->professor_name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $batch->current_capacity > 0 ? ($batch->current_capacity / $batch->max_capacity) * 100 : 0 }}%"
                                                 aria-valuenow="{{ $batch->current_capacity }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="{{ $batch->max_capacity }}">
                                                {{ $batch->current_capacity }}/{{ $batch->max_capacity }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($batch->batch_status === 'pending') bg-info
                                            @elseif($batch->batch_status === 'available') bg-success
                                            @elseif($batch->batch_status === 'ongoing') bg-warning
                                            @elseif($batch->batch_status === 'completed') bg-secondary
                                            @else bg-danger
                                            @endif">
                                            @if($batch->batch_status === 'pending')
                                                Pending Admin Approval
                                            @elseif($batch->batch_status === 'available')
                                                Available
                                            @elseif($batch->batch_status === 'ongoing')
                                                Ongoing
                                            @elseif($batch->batch_status === 'completed')
                                                Completed
                                            @else
                                                Closed
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($batch->registration_deadline)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($batch->start_date)->format('M d, Y') }}</td>
                                    <td>
                                        @if($batch->end_date)
                                            {{ \Carbon\Carbon::parse($batch->end_date)->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">Ongoing</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($batch->batch_status === 'pending')
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="approveBatch({{ $batch->batch_id }})"
                                                        title="Approve Batch">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editBatchModal{{ $batch->batch_id }}"
                                                    title="Edit Batch">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#manageBatchModal{{ $batch->batch_id }}"
                                                    title="Manage Students">
                                                <i class="fas fa-users"></i>
                                            </button>
                                            <a href="{{ route('admin.batches.export', $batch->batch_id) }}" 
                                               class="btn btn-sm btn-warning"
                                               title="Export Enrollments">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="toggleStatus({{ $batch->batch_id }})"
                                                    title="Toggle Status">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteBatch({{ $batch->batch_id }})"
                                                    title="Delete Batch">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No batches found</td>
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

<!-- Create Batch Modal -->
<div class="modal fade" id="createBatchModal" tabindex="-1" aria-labelledby="createBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBatchModalLabel">Create New Batch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.batches.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="batch_name" class="form-label">Batch Name</label>
                        <input type="text" class="form-control" id="batch_name" name="batch_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="program_id" class="form-label">Program</label>
                        <select class="form-control" id="program_id" name="program_id" required>
                            <option value="">Select a program</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="professor_id" class="form-label">Assigned Professor (Optional)</label>
                        <select class="form-control" id="professor_id" name="professor_id">
                            <option value="">Select a professor</option>
                            @foreach($professors as $professor)
                                <option value="{{ $professor->professor_id }}">{{ $professor->professor_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="max_capacity" class="form-label">Maximum Capacity</label>
                        <input type="number" class="form-control" id="max_capacity" name="max_capacity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="registration_deadline" class="form-label">Registration Deadline</label>
                        <input type="date" class="form-control" id="registration_deadline" name="registration_deadline" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date (Optional)</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                        <div class="form-text">Leave empty for ongoing batches. When end date is reached, batch status becomes 'completed'.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Batch Modals -->
@foreach($batches as $batch)
<div class="modal fade" id="editBatchModal{{ $batch->batch_id }}" tabindex="-1" aria-labelledby="editBatchModalLabel{{ $batch->batch_id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBatchModalLabel{{ $batch->batch_id }}">Edit Batch: {{ $batch->batch_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBatchForm{{ $batch->batch_id }}" onsubmit="updateBatch(event, '{{ $batch->batch_id }}')">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_batch_name{{ $batch->batch_id }}" class="form-label">Batch Name</label>
                        <input type="text" class="form-control" id="edit_batch_name{{ $batch->batch_id }}" name="batch_name" value="{{ $batch->batch_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_program_id{{ $batch->batch_id }}" class="form-label">Program</label>
                        <select class="form-control" id="edit_program_id{{ $batch->batch_id }}" name="program_id" required>
                            <option value="">Select a program</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->program_id }}" {{ $batch->program_id == $program->program_id ? 'selected' : '' }}>
                                    {{ $program->program_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_professor_id{{ $batch->batch_id }}" class="form-label">Assigned Professor (Optional)</label>
                        <select class="form-control" id="edit_professor_id{{ $batch->batch_id }}" name="professor_id">
                            <option value="">Select a professor</option>
                            @foreach($professors as $professor)
                                <option value="{{ $professor->professor_id }}" {{ $batch->professor_id == $professor->professor_id ? 'selected' : '' }}>
                                    {{ $professor->professor_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_max_capacity{{ $batch->batch_id }}" class="form-label">Maximum Capacity</label>
                        <input type="number" class="form-control" id="edit_max_capacity{{ $batch->batch_id }}" name="max_capacity" value="{{ $batch->max_capacity }}" min="{{ $batch->current_capacity }}" required>
                        <small class="text-muted">Current enrollment: {{ $batch->current_capacity }} students</small>
                    </div>
                    <div class="mb-3">
                        <label for="edit_registration_deadline{{ $batch->batch_id }}" class="form-label">Registration Deadline</label>
                        <input type="date" class="form-control" id="edit_registration_deadline{{ $batch->batch_id }}" name="registration_deadline" value="{{ $batch->registration_deadline->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_start_date{{ $batch->batch_id }}" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="edit_start_date{{ $batch->batch_id }}" name="start_date" value="{{ $batch->start_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_end_date{{ $batch->batch_id }}" class="form-label">End Date (Optional)</label>
                        <input type="date" class="form-control" id="edit_end_date{{ $batch->batch_id }}" name="end_date" value="{{ $batch->end_date ? $batch->end_date->format('Y-m-d') : '' }}">
                        <div class="form-text">Leave empty for ongoing batches. When end date is reached, batch status becomes 'completed'.</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description{{ $batch->batch_id }}" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="edit_description{{ $batch->batch_id }}" name="description" rows="3">{{ $batch->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Student Management Modals -->
@foreach($batches as $batch)
<!-- Manage Batch Students Modal -->
<div class="modal fade" id="manageBatchModal{{ $batch->batch_id }}" tabindex="-1" aria-labelledby="manageBatchModalLabel{{ $batch->batch_id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageBatchModalLabel{{ $batch->batch_id }}">
                    Manage Students - {{ $batch->batch_name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Batch Info -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6>Batch Information</h6>
                        <p><strong>Program:</strong> {{ $batch->program->program_name ?? 'N/A' }}</p>
                        <p><strong>Capacity:</strong> <span id="currentCapacity{{ $batch->batch_id }}">0</span>/{{ $batch->max_capacity }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge {{ $batch->batch_status === 'available' ? 'bg-success' : ($batch->batch_status === 'ongoing' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($batch->batch_status) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-8">
                        <h6>Available Students (Drag to add to batch)</h6>
                        <div class="border rounded p-3" style="height: 120px; overflow-y: auto; background-color: #f8f9fa;">
                            <div id="availableStudentsList{{ $batch->batch_id }}" class="available-students-list">
                                <div class="text-center text-muted">
                                    <i class="fas fa-spinner fa-spin"></i> Loading available students...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Tables -->
                <div class="row">
                    <!-- Pending Students -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-clock"></i> 
                                    Pending Students 
                                    (<span id="pendingCount{{ $batch->batch_id }}">0</span>)
                                </h6>
                                <small>Registration pending, payment pending, or waiting approval</small>
                            </div>
                            <div class="card-body p-0">
                                <div class="student-drop-zone" id="pendingDropZone{{ $batch->batch_id }}" 
                                     data-batch-id="{{ $batch->batch_id }}" data-target="pending"
                                     style="min-height: 300px; max-height: 400px; overflow-y: auto;">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="25%">Name</th>
                                                    <th width="25%">Email</th>
                                                    <th width="15%">Registration</th>
                                                    <th width="15%">Payment</th>
                                                    <th width="20%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pendingStudentsBody{{ $batch->batch_id }}">
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted p-4">
                                                        <i class="fas fa-spinner fa-spin"></i> Loading...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Students -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle"></i> 
                                    Current Students 
                                    (<span id="currentCount{{ $batch->batch_id }}">0</span>)
                                </h6>
                                <small>Approved registration and paid</small>
                            </div>
                            <div class="card-body p-0">
                                <div class="student-drop-zone" id="currentDropZone{{ $batch->batch_id }}" 
                                     data-batch-id="{{ $batch->batch_id }}" data-target="current"
                                     style="min-height: 300px; max-height: 400px; overflow-y: auto;">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="25%">Name</th>
                                                    <th width="25%">Email</th>
                                                    <th width="15%">Registration</th>
                                                    <th width="15%">Payment</th>
                                                    <th width="20%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="currentStudentsBody{{ $batch->batch_id }}">
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted p-4">
                                                        <i class="fas fa-spinner fa-spin"></i> Loading...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="exportStudentList('{{ $batch->batch_id }}')">
                    <i class="fas fa-download"></i> Export Student List
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
// Set minimum dates for date inputs
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const registrationDeadlineInput = document.getElementById('registration_deadline');
    const startDateInput = document.getElementById('start_date');
    
    // Note: We allow registration deadline to be any date to support ongoing batches
    if(registrationDeadlineInput) {
        // Don't set minimum date for registration deadline - allow flexibility for ongoing batches
        
        // Update start date minimum when registration deadline changes
        registrationDeadlineInput.addEventListener('change', function() {
            if(startDateInput) {
                // Allow start date to be same or before registration deadline for ongoing batches
                // But don't enforce minimum based on registration deadline
            }
        });
    }
    
    // Allow start date to be any date (including past dates for ongoing batches)
    if(startDateInput) {
        // Don't set minimum date for start date - allow flexibility for ongoing batches
    }

    // Load students when manage modal is opened
    document.querySelectorAll('[id^="manageBatchModal"]').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const batchId = this.id.replace('manageBatchModal', '');
            loadBatchStudents(batchId);
        });
    });
});

function loadBatchStudents(batchId) {
    const currentTable = document.querySelector(`[data-batch-id="${batchId}"].batch-students-table tbody`);
    const pendingTable = document.querySelector(`[data-batch-id="${batchId}"].batch-pending-table tbody`);
    
    fetch(`{{ url('admin/batches') }}/${batchId}/students`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update counters
                document.getElementById(`currentCount${batchId}`).textContent = data.total_current || 0;
                document.getElementById(`pendingCount${batchId}`).textContent = data.total_pending || 0;
                
                // Load current students
                let currentRows = '';
                if (data.current_students.length === 0) {
                    currentRows = '<tr><td colspan="9" class="text-center text-muted">No current students</td></tr>';
                } else {
                    data.current_students.forEach(student => {
                        currentRows += generateStudentRow(student, batchId, false);
                    });
                }
                currentTable.innerHTML = currentRows;
                
                // Load pending students
                let pendingRows = '';
                if (data.pending_students.length === 0) {
                    pendingRows = '<tr><td colspan="9" class="text-center text-muted">No pending students</td></tr>';
                } else {
                    data.pending_students.forEach(student => {
                        pendingRows += generateStudentRow(student, batchId, true);
                    });
                }
                pendingTable.innerHTML = pendingRows;
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            currentTable.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error loading students</td></tr>';
            pendingTable.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error loading students</td></tr>';
        });
}

function generateStudentRow(student, batchId, isPending) {
    const approvalBadge = getStatusBadge(student.enrollment_status, 'approval');
    const paymentBadge = getStatusBadge(student.payment_status, 'payment');
    const amount = student.amount ? `₱${parseFloat(student.amount).toLocaleString()}` : 'N/A';
    
    let actions = `
        <button type="button" class="btn btn-sm btn-danger" 
                onclick="removeStudentFromBatch(${batchId}, ${student.user_id})"
                title="Remove from batch">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    if (isPending) {
        if (student.enrollment_status === 'pending') {
            actions += `
                <button type="button" class="btn btn-sm btn-success ms-1" 
                        onclick="approveStudent(${student.enrollment_id})"
                        title="Approve enrollment">
                    <i class="fas fa-check"></i>
                </button>
            `;
        }
        if (student.payment_status === 'pending') {
            actions += `
                <button type="button" class="btn btn-sm btn-primary ms-1" 
                        onclick="markAsPaid(${student.enrollment_id})"
                        title="Mark as paid">
                    <i class="fas fa-dollar-sign"></i>
                </button>
            `;
        }
    }
    
    return `
        <tr>
            <td>${student.firstname} ${student.lastname}</td>
            <td>${student.email}</td>
            <td>${student.program_name}</td>
            <td>${student.package_name}</td>
            <td>${amount}</td>
            <td>${new Date(student.enrollment_date).toLocaleDateString()}</td>
            <td>${approvalBadge}</td>
            <td>${paymentBadge}</td>
            <td>${actions}</td>
        </tr>
    `;
}

function getStatusBadge(status, type) {
    const statusClass = {
        'approved': 'bg-success',
        'pending': 'bg-warning',
        'rejected': 'bg-danger',
        'paid': 'bg-success',
        'failed': 'bg-danger'
    };
    
    const badgeClass = statusClass[status] || 'bg-secondary';
    return `<span class="badge ${badgeClass}">${status}</span>`;
}

function approveStudent(enrollmentId) {
    if (!confirm('Are you sure you want to approve this student enrollment?')) {
        return;
    }
    
    fetch(`{{ url('admin/enrollment') }}/${enrollmentId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Student enrollment approved successfully');
            // Reload students for all open modals
            document.querySelectorAll('[id^="manageBatchModal"]').forEach(modal => {
                if (modal.classList.contains('show')) {
                    const batchId = modal.getAttribute('id').replace('manageBatchModal', '');
                    loadBatchStudents(batchId);
                }
            });
        } else {
            alert(data.message || 'Error approving student');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error approving student');
    });
}

function markAsPaid(enrollmentId) {
    if (!confirm('Are you sure you want to mark this payment as paid?')) {
        return;
    }
    
    fetch(`{{ url('admin/enrollment') }}/${enrollmentId}/mark-paid`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment marked as paid successfully');
            // Reload students for all open modals
            document.querySelectorAll('[id^="manageBatchModal"]').forEach(modal => {
                if (modal.classList.contains('show')) {
                    const batchId = modal.getAttribute('id').replace('manageBatchModal', '');
                    loadBatchStudents(batchId);
                }
            });
        } else {
            alert(data.message || 'Error marking payment as paid');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error marking payment as paid');
    });
}

function removeStudentFromBatch(batchId, studentId) {
    if (!confirm('Are you sure you want to remove this student from the batch?')) {
        return;
    }

    fetch(`{{ url('admin/batches') }}/${batchId}/students/${studentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the students table
            loadBatchStudents(batchId);
            // Show success message
            alert(data.message);
            // Reload the page to update capacity
            window.location.reload();
        } else {
            alert(data.message || 'Error removing student');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error removing student from batch');
    });
}

function approveBatch(batchId) {
    if(confirm('Are you sure you want to approve this batch? This will change its status to "available".')) {
        fetch(`{{ url('admin/batches') }}/${batchId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Error approving batch');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving batch');
        });
    }
}

function deleteBatch(batchId) {
    if(confirm('Are you sure you want to delete this batch? This action cannot be undone.')) {
        fetch(`{{ url('admin/batches') }}/${batchId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Error deleting batch');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting batch');
        });
    }
}

function toggleStatus(batchId) {
    if(confirm('Are you sure you want to toggle the status of this batch?')) {
        fetch(`{{ url('admin/batches') }}/${batchId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Error updating batch status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating batch status');
        });
    }
}

function updateBatch(event, batchId) {
    event.preventDefault();
    
    const form = document.getElementById(`editBatchForm${batchId}`);
    const formData = new FormData(form);
    
    fetch(`{{ url('admin/batches') }}/${batchId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || 'Error updating batch');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating batch');
    });
}

function loadBatchStudents(batchId) {
    fetch(`{{ url('admin/batches') }}/${batchId}/students`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStudentTables(batchId, data);
                loadAvailableStudents(batchId);
                
                // Update capacity display
                document.getElementById(`currentCapacity${batchId}`).textContent = data.total_current;
                document.getElementById(`currentCount${batchId}`).textContent = data.total_current;
                document.getElementById(`pendingCount${batchId}`).textContent = data.total_pending;
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
        });
}

function updateStudentTables(batchId, data) {
    // Update current students table
    const currentBody = document.getElementById(`currentStudentsBody${batchId}`);
    if (data.current_students.length > 0) {
        currentBody.innerHTML = data.current_students.map(student => `
            <tr class="student-row" draggable="true" data-enrollment-id="${student.enrollment_id}" data-student-type="current">
                <td><strong>${student.name}</strong></td>
                <td><small>${student.email}</small></td>
                <td><span class="badge bg-success">${student.enrollment_status}</span></td>
                <td><span class="badge bg-success">${student.payment_status}</span></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="moveStudentToPending('${batchId}', '${student.enrollment_id}')" title="Move to Pending">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="removeStudent('${batchId}', '${student.enrollment_id}')" title="Remove">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    } else {
        currentBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No current students</td></tr>';
    }

    // Update pending students table
    const pendingBody = document.getElementById(`pendingStudentsBody${batchId}`);
    if (data.pending_students.length > 0) {
        pendingBody.innerHTML = data.pending_students.map(student => `
            <tr class="student-row" draggable="true" data-enrollment-id="${student.enrollment_id}" data-student-type="pending">
                <td><strong>${student.name}</strong></td>
                <td><small>${student.email}</small></td>
                <td><span class="badge bg-${student.enrollment_status === 'approved' ? 'success' : 'warning'}">${student.enrollment_status}</span></td>
                <td><span class="badge bg-${student.payment_status === 'paid' ? 'success' : 'warning'}">${student.payment_status}</span></td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="moveStudentToCurrent('${batchId}', '${student.enrollment_id}')" title="Move to Current">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="removeStudent('${batchId}', '${student.enrollment_id}')" title="Remove">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    } else {
        pendingBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No pending students</td></tr>';
    }

    // Setup drag and drop for student rows
    setupDragAndDrop(batchId);
}

function loadAvailableStudents(batchId) {
    const container = document.getElementById(`availableStudentsList${batchId}`);
    
    fetch(`{{ url('admin/batches') }}/${batchId}/students`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.available_students) {
                if (data.available_students.length > 0) {
                    container.innerHTML = data.available_students.map(student => `
                        <div class="available-student-item p-2 mb-2 bg-white border rounded" 
                             draggable="true" 
                             data-enrollment-id="${student.enrollment_id}"
                             data-student-type="available"
                             style="cursor: move;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${student.name}</strong><br>
                                    <small class="text-muted">${student.email}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-${student.enrollment_status === 'approved' ? 'success' : 'warning'}">${student.enrollment_status}</span><br>
                                    <span class="badge bg-${student.payment_status === 'paid' ? 'success' : 'warning'}">${student.payment_status}</span>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="text-center text-muted">No available students</div>';
                }
                
                // Setup drag for available students
                setupAvailableStudentsDrag(batchId);
            }
        })
        .catch(error => {
            console.error('Error loading available students:', error);
            container.innerHTML = '<div class="text-center text-danger">Error loading students</div>';
        });
}

function setupDragAndDrop(batchId) {
    // Setup drag for student rows
    const studentRows = document.querySelectorAll(`#manageBatchModal${batchId} .student-row`);
    studentRows.forEach(row => {
        row.addEventListener('dragstart', handleDragStart);
        row.addEventListener('dragend', handleDragEnd);
    });

    // Setup drop zones
    const dropZones = document.querySelectorAll(`#manageBatchModal${batchId} .student-drop-zone`);
    dropZones.forEach(zone => {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('drop', handleDrop);
        zone.addEventListener('dragenter', handleDragEnter);
        zone.addEventListener('dragleave', handleDragLeave);
    });
}

function setupAvailableStudentsDrag(batchId) {
    const availableItems = document.querySelectorAll(`#availableStudentsList${batchId} .available-student-item`);
    availableItems.forEach(item => {
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragend', handleDragEnd);
    });
}

let draggedElement = null;

function handleDragStart(e) {
    draggedElement = e.target;
    e.dataTransfer.effectAllowed = 'move';
    e.target.style.opacity = '0.4';
}

function handleDragEnd(e) {
    e.target.style.opacity = '1';
    draggedElement = null;
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDragEnter(e) {
    e.target.classList.add('drag-over');
}

function handleDragLeave(e) {
    e.target.classList.remove('drag-over');
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    e.target.classList.remove('drag-over');
    
    if (draggedElement) {
        const batchId = e.target.dataset.batchId;
        const targetType = e.target.dataset.target;
        const enrollmentId = draggedElement.dataset.enrollmentId;
        const studentType = draggedElement.dataset.studentType;
        
        // Don't allow dropping in the same zone
        if ((targetType === 'current' && studentType === 'current') ||
            (targetType === 'pending' && studentType === 'pending')) {
            return false;
        }
        
        // Handle different drop scenarios
        if (studentType === 'available') {
            // Adding from available to either pending or current
            addStudentToBatch(batchId, enrollmentId, targetType);
        } else if (targetType === 'current' && studentType === 'pending') {
            // Moving from pending to current - UPDATE DATABASE to give dashboard access
            moveStudentToCurrent(batchId, enrollmentId);
        } else if (targetType === 'pending' && studentType === 'current') {
            // Moving from current to pending - UPDATE DATABASE to remove dashboard access
            moveStudentToPending(batchId, enrollmentId);
        }
    }
    
    return false;
}

function moveStudentToCurrent(batchId, enrollmentId) {
    fetch(`{{ url('admin/batches') }}/${batchId}/enrollments/${enrollmentId}/move-to-current`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the students table to reflect database changes
            loadBatchStudents(batchId);
            showToast(data.message, 'success');
        } else {
            alert(data.message || 'Error moving student to current');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error moving student to current');
    });
}

function moveStudentToPending(batchId, enrollmentId) {
    fetch(`{{ url('admin/batches') }}/${batchId}/enrollments/${enrollmentId}/move-to-pending`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the students table to reflect database changes
            loadBatchStudents(batchId);
            showToast(data.message, 'success');
        } else {
            alert(data.message || 'Error moving student to pending');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error moving student to pending');
    });
}

function moveStudentVisually(studentElement, targetType, batchId) {
    // Get student data
    const enrollmentId = studentElement.dataset.enrollmentId;
    const studentName = studentElement.querySelector('td:nth-child(2)').textContent;
    const studentEmail = studentElement.querySelector('td:nth-child(3)').textContent;
    const enrollmentStatus = studentElement.querySelector('td:nth-child(4)').textContent;
    const paymentStatus = studentElement.querySelector('td:nth-child(5)').textContent;
    
    // Remove from current location
    studentElement.remove();
    
    // Find target table body
    const targetTableBody = document.querySelector(`#manageBatchModal${batchId} .${targetType}-students-table tbody`);
    
    // Create new row in target location
    const newRow = document.createElement('tr');
    newRow.className = 'student-row';
    newRow.draggable = true;
    newRow.dataset.enrollmentId = enrollmentId;
    newRow.dataset.studentType = targetType;
    
    newRow.innerHTML = `
        <td>
            <i class="fas fa-grip-vertical text-muted" style="cursor: grab;"></i>
        </td>
        <td>${studentName}</td>
        <td>${studentEmail}</td>
        <td>
            <span class="badge badge-${enrollmentStatus === 'approved' ? 'success' : enrollmentStatus === 'pending' ? 'warning' : 'secondary'}">
                ${enrollmentStatus}
            </span>
        </td>
        <td>
            <span class="badge badge-${paymentStatus === 'paid' ? 'success' : paymentStatus === 'pending' ? 'warning' : 'secondary'}">
                ${paymentStatus}
            </span>
        </td>
        <td>
            <button class="btn btn-sm btn-danger" onclick="removeStudentFromBatch(${batchId}, ${enrollmentId})">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `;
    
    // Add drag event listeners to the new row
    newRow.addEventListener('dragstart', handleDragStart);
    newRow.addEventListener('dragend', handleDragEnd);
    
    // Append to target table
    targetTableBody.appendChild(newRow);
    
    // Update counters
    updateStudentCounts(batchId);
    
    // Show success message
    showToast('Student moved successfully! Note: This is a visual change only and does not affect the actual enrollment or payment status in the database.', 'success');
}

function addStudentToBatch(batchId, enrollmentId, targetType) {
    fetch(`{{ url('admin/batches') }}/${batchId}/enrollments/${enrollmentId}/add-to-batch`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ target_type: targetType })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the students table to reflect database changes
            loadBatchStudents(batchId);
            showToast(data.message, 'success');
        } else {
            alert(data.message || 'Error adding student');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding student');
    });
}

function removeStudent(batchId, enrollmentId) {
    if (confirm('Are you sure you want to remove this student from the batch? This will move them back to the available students list.')) {
        // Remove student from batch by setting batch_id to null
        fetch(`{{ url('admin/batches') }}/${batchId}/enrollments/${enrollmentId}/remove-from-batch`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadBatchStudents(batchId);
                showToast(data.message, 'success');
            } else {
                alert(data.message || 'Error removing student');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing student from batch');
        });
    }
}

function exportStudentList(batchId) {
    window.open(`{{ url('admin/batches') }}/${batchId}/export`, '_blank');
}

// Helper functions
function updateStudentCounts(batchId) {
    // Count current and pending students
    const currentCount = document.querySelectorAll(`#manageBatchModal${batchId} .current-students-table tbody tr`).length;
    const pendingCount = document.querySelectorAll(`#manageBatchModal${batchId} .pending-students-table tbody tr`).length;
    
    // Update counter displays
    const currentCounter = document.querySelector(`#manageBatchModal${batchId} .current-count`);
    const pendingCounter = document.querySelector(`#manageBatchModal${batchId} .pending-count`);
    
    if (currentCounter) currentCounter.textContent = currentCount;
    if (pendingCounter) pendingCounter.textContent = pendingCount;
}

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    // Add to body
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

// Load batch students when modals are opened
document.addEventListener('DOMContentLoaded', function() {
    // Load students when manage batch modals are shown
    const manageModals = document.querySelectorAll('[id^="manageBatchModal"]');
    manageModals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const batchId = this.id.replace('manageBatchModal', '');
            loadBatchStudents(batchId);
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.available-student-item {
    transition: all 0.2s ease;
}

.available-student-item:hover {
    background-color: #e9ecef !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.student-row {
    transition: all 0.2s ease;
    cursor: move;
}

.student-row:hover {
    background-color: #f8f9fa;
}

.drag-over {
    background-color: #e3f2fd !important;
    border: 2px dashed #2196f3 !important;
}

.student-drop-zone {
    transition: all 0.2s ease;
}

.student-drop-zone:hover {
    background-color: #f8f9fa;
}

/* Dragging styles */
.student-row[draggable="true"]:active,
.available-student-item[draggable="true"]:active {
    cursor: grabbing;
    opacity: 0.7;
}

/* Badge styles for status */
.badge {
    font-size: 0.75em;
}

/* Sticky header for tables */
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Custom scrollbar for drop zones */
.student-drop-zone::-webkit-scrollbar {
    width: 6px;
}

.student-drop-zone::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.student-drop-zone::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.student-drop-zone::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Animation for adding/removing students */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.student-row {
    animation: slideIn 0.3s ease;
}
</style>
@endpush

@endsection
