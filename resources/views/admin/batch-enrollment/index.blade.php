@extends('layouts.admin')

@section('title', 'Batch Enrollment Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Batch Enrollment Management</h3>
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#createBatchModal">
                        Create New Batch
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Batch Name</th>
                                    <th>Program</th>
                                    <th>Start Date</th>
                                    <th>Schedule</th>
                                    <th>Capacity</th>
                                    <th>Assigned Professor</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($batches as $batch)
                                <tr>
                                    <td>{{ $batch->batch_name }}</td>
                                    <td>{{ $batch->program->program_name }}</td>
                                    <td>{{ $batch->start_date->format('M d, Y') }}</td>
                                    <td>{{ $batch->schedule }}</td>
                                    <td>{{ $batch->current_capacity }}/{{ $batch->max_capacity }}</td>
                                    <td>
                                        @if($batch->professors && $batch->professors->count() > 0)
                                            @foreach($batch->professors as $professor)
                                                <span class="badge bg-info me-1">{{ $professor->professor_name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No professor assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $batch->status === 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($batch->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editBatchModal-{{ $batch->id }}">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteBatch({{ $batch->id }})">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $batches->links() }}
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
            <form action="{{ route('admin.batches.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Batch Name</label>
                        <input type="text" class="form-control" name="batch_name" required>
                    </div>
                    <div class="form-group">
                        <label>Program</label>
                        <select class="form-control" name="program_id" required>
                            <option value="">Select Program</option>
                            @foreach($programs as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
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
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
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

@foreach($batches as $batch)
<!-- Edit Batch Modal -->
<div class="modal fade" id="editBatchModal-{{ $batch->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Batch</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form class="edit-batch-form" data-batch-id="{{ $batch->id }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Batch Name</label>
                        <input type="text" class="form-control" name="batch_name" value="{{ $batch->batch_name }}" required>
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $batch->start_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Maximum Capacity</label>
                        <input type="number" class="form-control" name="max_capacity" value="{{ $batch->max_capacity }}" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Schedule</label>
                        <input type="text" class="form-control" name="schedule" value="{{ $batch->schedule }}" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="active" {{ $batch->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $batch->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
function deleteBatch(batchId) {
    if (confirm('Are you sure you want to delete this batch?')) {
        fetch(`/admin/batches/${batchId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }
}

// AJAX for Edit Batch forms

document.querySelectorAll('.edit-batch-form').forEach(form => {
    form.onsubmit = function(e) {
        e.preventDefault();
        const batchId = form.getAttribute('data-batch-id');
        const formData = new FormData(form);
        fetch(`/admin/batches/${batchId}`, {
            method: 'POST', // Laravel expects POST with _method=PUT
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                // DO NOT set 'Content-Type' here!
            },
            body: formData
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                return response.text().then(text => { throw new Error(text); });
            }
        })
        .catch(error => {
            alert('Update failed: ' + error.message);
        });
    };
});
</script>
@endpush
@endsection
