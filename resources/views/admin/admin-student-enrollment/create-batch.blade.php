@extends('layouts.admin')

@section('title', 'Create New Batch')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Create New Batch
                    </h4>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.batches.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="program_id" class="form-label">Program *</label>
                                <select class="form-select" id="program_id" name="program_id" required>
                                    <option value="">Select Program</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->program_id }}" 
                                                {{ old('program_id') == $program->program_id ? 'selected' : '' }}>
                                            {{ $program->program_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="batch_name" class="form-label">Batch Name *</label>
                                <input type="text" class="form-control" id="batch_name" name="batch_name" 
                                       value="{{ old('batch_name') }}" required>
                                <div class="form-text">Example: "Batch 1", "Morning Class", "Engineering Batch A"</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="batch_description" class="form-label">Description</label>
                            <textarea class="form-control" id="batch_description" name="batch_description" 
                                      rows="3">{{ old('batch_description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="batch_capacity" class="form-label">Capacity *</label>
                                <input type="number" class="form-control" id="batch_capacity" name="batch_capacity" 
                                       value="{{ old('batch_capacity', 10) }}" min="1" max="100" required>
                                <div class="form-text">Maximum number of students</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="batch_status" class="form-label">Status *</label>
                                <select class="form-select" id="batch_status" name="batch_status" required>
                                    <option value="available" {{ old('batch_status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="ongoing" {{ old('batch_status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="closed" {{ old('batch_status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="completed" {{ old('batch_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="enrollment_deadline" class="form-label">Enrollment Deadline</label>
                                <input type="datetime-local" class="form-control" id="enrollment_deadline" 
                                       name="enrollment_deadline" value="{{ old('enrollment_deadline') }}">
                                <div class="form-text">When enrollment closes</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="datetime-local" class="form-control" id="start_date" 
                                       name="start_date" value="{{ old('start_date') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="datetime-local" class="form-control" id="end_date" 
                                       name="end_date" value="{{ old('end_date') }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.batches.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Batches
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check me-1"></i>Create Batch
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-generate batch name based on program selection
    document.getElementById('program_id').addEventListener('change', function() {
        const programSelect = this;
        const batchNameInput = document.getElementById('batch_name');
        
        if (programSelect.value && !batchNameInput.value) {
            const programText = programSelect.options[programSelect.selectedIndex].text;
            // Get existing batch count for this program (you could implement this via AJAX)
            const batchNumber = Math.floor(Math.random() * 10) + 1; // Simplified
            batchNameInput.value = `${programText} - Batch ${batchNumber}`;
        }
    });

    // Validate end date is after start date
    document.getElementById('start_date').addEventListener('change', validateDates);
    document.getElementById('end_date').addEventListener('change', validateDates);

    function validateDates() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (startDate && endDate && startDate >= endDate) {
            document.getElementById('end_date').setCustomValidity('End date must be after start date');
        } else {
            document.getElementById('end_date').setCustomValidity('');
        }
    }
</script>
@endsection
