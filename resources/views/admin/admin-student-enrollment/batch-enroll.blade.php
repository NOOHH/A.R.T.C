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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                                            @if($batch->batch_status === 'available') badge-success
                                            @elseif($batch->batch_status === 'ongoing') badge-warning
                                            @else badge-danger
                                            @endif">
                                            @if($batch->batch_status === 'available')
                                                Available
                                            @elseif($batch->batch_status === 'ongoing')
                                                Ongoing
                                            @else
                                                Closed
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($batch->registration_deadline)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($batch->start_date)->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
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
                    <div class="col-md-6">
                        <h6>Batch Information</h6>
                        <p><strong>Program:</strong> {{ $batch->program->program_name ?? 'N/A' }}</p>
                        <p><strong>Capacity:</strong> {{ $batch->current_capacity }}/{{ $batch->max_capacity }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge {{ $batch->batch_status === 'available' ? 'bg-success' : ($batch->batch_status === 'ongoing' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($batch->batch_status) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Add Students to Batch</h6>
                        <form action="{{ route('admin.batches.add-students', $batch->batch_id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <select name="student_ids[]" class="form-select" multiple required>
                                    <option value="">Select students to add...</option>
                                    <!-- Students will be loaded via AJAX -->
                                </select>
                                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple students</small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Selected Students
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Current Students -->
                <div class="row">
                    <div class="col-12">
                        <h6>Current Students ({{ $batch->current_capacity }})</h6>
                        <div class="table-responsive">
                            <table class="table table-hover batch-students-table" data-batch-id="{{ $batch->batch_id }}">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Enrollment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Students will be loaded via AJAX -->
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <i class="fas fa-spinner fa-spin"></i> Loading students...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.batches.export', $batch->batch_id) }}" class="btn btn-success me-auto">
                    <i class="fas fa-download"></i> Export Student List
                </a>
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
    
    if(registrationDeadlineInput) {
        registrationDeadlineInput.min = today;
        
        // Update start date minimum when registration deadline changes
        registrationDeadlineInput.addEventListener('change', function() {
            if(startDateInput) {
                startDateInput.min = this.value;
            }
        });
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
    const table = document.querySelector(`[data-batch-id="${batchId}"] tbody`);
    
    fetch(`{{ url('admin/batches') }}/${batchId}/students`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let rows = '';
                if (data.students.length === 0) {
                    rows = '<tr><td colspan="5" class="text-center text-muted">No students enrolled yet</td></tr>';
                } else {
                    data.students.forEach(student => {
                        rows += `
                            <tr>
                                <td>${student.firstname} ${student.lastname}</td>
                                <td>${student.email}</td>
                                <td>${new Date(student.enrollment_date).toLocaleDateString()}</td>
                                <td>
                                    <span class="badge ${student.status === 'active' ? 'bg-success' : 'bg-warning'}">
                                        ${student.status}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="removeStudentFromBatch(${batchId}, ${student.user_id})"
                                            title="Remove from batch">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                table.innerHTML = rows;
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            table.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading students</td></tr>';
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
</script>
@endpush

@endsection
