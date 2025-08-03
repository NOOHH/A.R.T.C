@extends('layouts.admin')

@section('title', 'Enrollment Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="numbers">
                                <p class="card-category">Total Enrollments</p>
                                <h4 class="card-title">{{ $totalEnrollments ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="numbers">
                                <p class="card-category">Active Students</p>
                                <h4 class="card-title">{{ $activeStudents ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="numbers">
                                <p class="card-category">Pending Registrations</p>
                                <h4 class="card-title">{{ $pendingRegistrations ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="numbers">
                                <p class="card-category">Completed Courses</p>
                                <h4 class="card-title">{{ $completedCourses ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Batch Management Section -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Batch Management</h3>
                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#createBatchModal">
                        <i class="fas fa-plus"></i> Create New Batch
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Batch Name</th>
                                    <th>Program</th>
                                    <th>Start Date</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batches ?? [] as $batch)
                                <tr>
                                    <td>{{ $batch->batch_name }}</td>
                                    <td>{{ $batch->program->program_name ?? 'N/A' }}</td>
                                    <td>{{ $batch->start_date->format('M d, Y') }}</td>
                                    <td>{{ $batch->current_capacity }}/{{ $batch->max_capacity }}</td>
                                    <td>
                                        <span class="badge badge-{{ $batch->status === 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($batch->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No batches found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Assignment Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Assign Course to Student</h3>
                </div>
                <div class="card-body">
                    <form id="assignCourseForm" class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Student</label>
                                <select class="form-control" id="studentSelect" required>
                                    <option value="">Choose a student...</option>
                                    @foreach($students ?? [] as $student)
                                    <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Program</label>
                                <select class="form-control" id="programSelect" required>
                                    <option value="">Choose a program...</option>
                                    @foreach($programs ?? [] as $program)
                                    <option value="{{ $program->id }}">{{ $program->program_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Notes (optional)</label>
                                <input type="text" class="form-control" id="notes" placeholder="Additional notes">
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success float-right">
                                <i class="fas fa-check"></i> Assign Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Batch Modal -->
<div class="modal fade" id="createBatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Batch</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="createBatchForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Batch Name</label>
                        <input type="text" class="form-control" name="batch_name" required>
                    </div>
                    <div class="form-group">
                        <label>Program</label>
                        <select class="form-control" name="program_id" required>
                            <option value="">Select Program</option>
                            @foreach($programs ?? [] as $program)
                            <option value="{{ $program->id }}">{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>Maximum Capacity</label>
                        <input type="number" class="form-control" name="max_capacity" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Schedule</label>
                        <input type="text" class="form-control" name="schedule" required placeholder="e.g., MWF 9:00 AM - 12:00 PM">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the create batch form
    $('#createBatchForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('{{ route("admin.batches.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error creating batch');
            }
        });
    });

    // Initialize the assign course form
    $('#assignCourseForm').on('submit', function(e) {
        e.preventDefault();
        const data = {
            student_id: $('#studentSelect').val(),
            program_id: $('#programSelect').val(),
            notes: $('#notes').val()
        };

        fetch('{{ route("admin.enrollments.assign") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error assigning course');
            }
        });
    });
});

function editBatch(id) {
    // Implement batch editing logic
}

function deleteBatch(id) {
    if (confirm('Are you sure you want to delete this batch?')) {
        fetch(`/admin/batches/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error deleting batch');
            }
        });
    }
}
</script>
@endpush
@endsection
