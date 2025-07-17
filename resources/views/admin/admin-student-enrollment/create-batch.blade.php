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
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="max_capacity" class="form-label">Capacity *</label>
                                <input type="number" class="form-control" id="max_capacity" name="max_capacity" 
                                       value="{{ old('max_capacity', 10) }}" min="1" max="100" required>
                                <div class="form-text">Maximum number of students</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="batch_status" class="form-label">Status * <small class="text-muted">(Auto-set based on start date)</small></label>
                                <select class="form-select" id="batch_status" name="batch_status" required>
                                    <option value="available" {{ old('batch_status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="ongoing" {{ old('batch_status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="closed" {{ old('batch_status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="completed" {{ old('batch_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="registration_deadline" class="form-label">Registration Deadline</label>
                                <input type="datetime-local" class="form-control" id="registration_deadline" 
                                       name="registration_deadline" value="{{ old('registration_deadline') }}">
                                <div class="form-text">When enrollment closes</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="datetime-local" class="form-control" id="start_date" 
                                       name="start_date" value="{{ old('start_date') }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="duration_weeks" class="form-label">Duration (Weeks)</label>
                                <input type="number" class="form-control" id="duration_weeks" 
                                       name="duration_weeks" value="{{ old('duration_weeks', 12) }}" 
                                       min="1" max="52" step="1">
                                <div class="form-text">Automatically calculates end date</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="datetime-local" class="form-control" id="end_date" 
                                       name="end_date" value="{{ old('end_date') }}" readonly>
                                <div class="form-text">Auto-calculated from start date + duration</div>
                            </div>
                        </div>

                        <!-- Professor Assignment Section -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="professor_id" class="form-label">Assign Professor (Optional)</label>
                                <select class="form-select" id="professor_id" name="professor_id">
                                    <option value="">Select a professor</option>
                                    @if(isset($professors) && $professors->count() > 0)
                                        @foreach($professors as $professor)
                                            <option value="{{ $professor->professor_id }}" 
                                                    {{ old('professor_id') == $professor->professor_id ? 'selected' : '' }}>
                                                {{ $professor->professor_first_name }} {{ $professor->professor_last_name }}
                                                @if($professor->professor_specialization)
                                                    - {{ $professor->professor_specialization }}
                                                @endif
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Assign a professor to this batch. You can change this later if needed.
                                </div>
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
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            
            batchNameInput.value = `${programText} - Batch ${month}/${year}`;
        }
    });

    // Auto-set status based on start date
    function updateStatusBasedOnDate() {
        const startDateInput = document.getElementById('start_date');
        const statusSelect = document.getElementById('batch_status');
        
        if (startDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Reset time for accurate date comparison
            startDate.setHours(0, 0, 0, 0);
            
            if (startDate.getTime() === today.getTime()) {
                // If start date is today, set to ongoing
                statusSelect.value = 'ongoing';
                showStatusChangeNotification('Status automatically set to "Ongoing" (starts today)');
            } else if (startDate < today) {
                // If start date is in the past, set to ongoing
                statusSelect.value = 'ongoing';
                showStatusChangeNotification('Status automatically set to "Ongoing" (started in the past)');
            } else {
                // If start date is in the future, set to available
                statusSelect.value = 'available';
                showStatusChangeNotification('Status automatically set to "Available" (starts in the future)');
            }
        }
    }

    // Show notification when status is automatically changed
    function showStatusChangeNotification(message) {
        // Remove existing notification
        const existingNotification = document.querySelector('.status-notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Create new notification
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show status-notification mt-2';
        notification.innerHTML = `
            <i class="bi bi-info-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert after the status field
        const statusField = document.getElementById('batch_status').closest('.col-md-4');
        statusField.insertAdjacentElement('afterend', notification);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (notification && notification.parentNode) {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification && notification.parentNode) {
                        notification.remove();
                    }
                }, 150);
            }
        }, 5000);
    }

    // Add event listener to start date input
    document.getElementById('start_date').addEventListener('change', updateStatusBasedOnDate);
    document.getElementById('start_date').addEventListener('blur', updateStatusBasedOnDate);

    // Validate professor selection
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const professorCheckboxes = document.querySelectorAll('input[name="professor_ids[]"]:checked');
                if (professorCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one professor for this batch.');
                    return false;
                }
            });
        }
    });
</script>

@endsection
                    e.preventDefault();
                    alert('Please select at least one professor for this batch.');
                    return false;
                }
            });
        }
    });
            // Get existing batch count for this program (you could implement this via AJAX)
            const batchNumber = Math.floor(Math.random() * 10) + 1; // Simplified
            batchNameInput.value = `${programText} - Batch ${batchNumber}`;
        }
    });

    // Duration Calculator
    function calculateEndDate() {
        const startDate = document.getElementById('start_date').value;
        const durationWeeks = parseInt(document.getElementById('duration_weeks').value);
        
        if (startDate && durationWeeks && durationWeeks > 0) {
            const start = new Date(startDate);
            const end = new Date(start);
            end.setDate(start.getDate() + (durationWeeks * 7));
            
            // Format to datetime-local input format
            const endDateString = end.toISOString().slice(0, 16);
            document.getElementById('end_date').value = endDateString;
        }
    }

    // Event listeners for duration calculation
    document.getElementById('start_date').addEventListener('change', calculateEndDate);
    document.getElementById('duration_weeks').addEventListener('input', calculateEndDate);

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
