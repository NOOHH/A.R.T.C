@extends('admin.admin-dashboard-layout')

@section('title', 'Edit Professor')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Edit Professor</h2>
                    <p class="text-muted">Update professor information and program assignments</p>
                </div>
                <div>
                    <a href="{{ route('admin.professors.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Professors
                    </a>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-workspace me-2"></i>
                        Edit: {{ $professor->full_name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.professors.update', $professor->professor_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" value="{{ old('first_name', $professor->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" name="last_name" value="{{ old('last_name', $professor->last_name) }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $professor->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password">
                                    <div class="form-text">Leave empty to keep current password</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="referral_code" class="form-label">Referral Code</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('referral_code') is-invalid @enderror" 
                                               id="referral_code" name="referral_code" 
                                               value="{{ old('referral_code', $professor->referral_code) }}" 
                                               data-professor-id="{{ $professor->professor_id }}"
                                               placeholder="Auto-generated if empty">
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateReferralCodeProf()" title="Generate New Code">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Referral code for student registration (e.g., PROF01NAME)</div>
                                    @error('referral_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Program Assignments</label>
                            <div class="row">
                                @foreach($programs as $program)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="programs[]" value="{{ $program->program_id }}" 
                                                   id="program_{{ $program->program_id }}"
                                                   {{ $professor->programs->contains('program_id', $program->program_id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="program_{{ $program->program_id }}">
                                                {{ $program->program_name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('programs')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Program Meeting Links -->
                        @if($professor->programs->count() > 0)
                            <div class="mb-4">
                                <label class="form-label">Program Meeting Links</label>
                                <div class="accordion" id="videoAccordion">
                                    @foreach($professor->programs as $index => $program)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading{{ $index }}">
                                                <button class="accordion-button collapsed" type="button" 
                                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}">
                                                    <i class="bi bi-play-circle me-2"></i>
                                                    {{ $program->program_name }}
                                                    @if($program->pivot->video_link)
                                                        <span class="badge bg-success ms-2">Video Added</span>
                                                    @endif
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $index }}" class="accordion-collapse collapse" 
                                                 data-bs-parent="#videoAccordion">
                                                <div class="accordion-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Video Link</label>
                                                        <input type="url" class="form-control video-link" 
                                                               data-professor-id="{{ $professor->id }}"
                                                               data-program-id="{{ $program->program_id }}"
                                                               value="{{ $program->pivot->video_link }}"
                                                               placeholder="https://youtube.com/watch?v=...">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Video Description</label>
                                                        <textarea class="form-control video-description" 
                                                                  data-professor-id="{{ $professor->id }}"
                                                                  data-program-id="{{ $program->program_id }}"
                                                                  rows="2" placeholder="Brief description of the video content">{{ $program->pivot->video_description }}</textarea>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-success update-video-btn"
                                                            data-professor-id="{{ $professor->id }}"
                                                            data-program-id="{{ $program->program_id }}">
                                                        <i class="bi bi-check-circle"></i> Update Video
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.professors.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Professor</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Batch Assignment Section -->
            <div class="card shadow border-0 mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-people-fill me-2"></i>
                        Batch Assignments
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Assign New Batch -->
                    <div class="mb-4">
                        <h6 class="mb-3">Assign New Batch</h6>
                        <form action="{{ route('admin.professors.assign-batch', $professor->professor_id) }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-8">
                                <select name="batch_id" class="form-select" required>
                                    <option value="">Select a batch to assign...</option>
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch->batch_id }}">
                                            {{ $batch->batch_name }} - {{ $batch->program->program_name ?? 'N/A' }} 
                                            ({{ $batch->current_capacity }}/{{ $batch->max_capacity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-plus-circle"></i> Assign Batch
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Current Batch Assignments -->
                    <div>
                        <h6 class="mb-3">Current Assignments</h6>
                        @if($assignedBatches->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Batch Name</th>
                                            <th>Program</th>
                                            <th>Capacity</th>
                                            <th>Status</th>
                                            <th>Start Date</th>
                                            <th>Assigned Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assignedBatches as $batch)
                                            <tr>
                                                <td>
                                                    <strong>{{ $batch->batch_name }}</strong>
                                                </td>
                                                <td>{{ $batch->program->program_name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $batch->current_capacity }}/{{ $batch->max_capacity }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $batch->batch_status === 'available' ? 'bg-success' : ($batch->batch_status === 'ongoing' ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ ucfirst($batch->batch_status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $batch->start_date->format('M d, Y') }}</td>
                                                <td>{{ $batch->professor_assigned_at ? $batch->professor_assigned_at->format('M d, Y') : 'N/A' }}</td>
                                                <td>
                                                    <form action="{{ route('admin.professors.unassign-batch', [$professor->professor_id, $batch->batch_id]) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Are you sure you want to unassign this batch?')">
                                                            <i class="bi bi-x-circle"></i> Unassign
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                No batches assigned to this professor yet.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle video link updates
    document.querySelectorAll('.update-video-btn').forEach(button => {
        button.addEventListener('click', function() {
            const professorId = this.getAttribute('data-professor-id');
            const programId = this.getAttribute('data-program-id');
            const videoLink = document.querySelector(`input.video-link[data-professor-id="${professorId}"][data-program-id="${programId}"]`).value;
            const videoDescription = document.querySelector(`textarea.video-description[data-professor-id="${professorId}"][data-program-id="${programId}"]`).value;
            
            // Show loading state
            this.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm"></i> Updating...';
            this.disabled = true;
            
            fetch(`/admin/professors/${professorId}/programs/${programId}/video`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    video_link: videoLink,
                    video_description: videoDescription
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button state
                    this.innerHTML = '<i class="bi bi-check-circle"></i> Updated!';
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-success');
                    
                    // Reset after 2 seconds
                    setTimeout(() => {
                        this.innerHTML = '<i class="bi bi-check-circle"></i> Update Video';
                        this.classList.remove('btn-outline-success');
                        this.classList.add('btn-success');
                        this.disabled = false;
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Update failed');
                }
            })
            .catch(error => {
                this.innerHTML = '<i class="bi bi-exclamation-circle"></i> Error';
                this.classList.remove('btn-success');
                this.classList.add('btn-danger');
                
                setTimeout(() => {
                    this.innerHTML = '<i class="bi bi-check-circle"></i> Update Video';
                    this.classList.remove('btn-danger');
                    this.classList.add('btn-success');
                    this.disabled = false;
                }, 2000);
            });
        });
    });
});

// Referral code generation function
function generateReferralCodeProf() {
    const firstName = document.getElementById('first_name').value || '';
    const lastName = document.getElementById('last_name').value || '';
    
    if (!firstName.trim() || !lastName.trim()) {
        return;
    }
    
    // Generate code based on name
    const cleanFirstName = firstName.replace(/[^A-Za-z]/g, '').toUpperCase();
    const cleanLastName = lastName.replace(/[^A-Za-z]/g, '').toUpperCase();
    
    // Get professor ID from data attribute
    const referralField = document.getElementById('referral_code');
    const professorId = referralField.getAttribute('data-professor-id') || Math.floor(Math.random() * 99) + 1;
    const paddedId = String(professorId).padStart(2, '0');
    
    // Generate code: PROF + ID + NAME_INITIALS
    const nameCode = cleanFirstName.substring(0, 1) + cleanLastName.substring(0, 3);
    const referralCode = 'PROF' + paddedId + nameCode;
    
    referralField.value = referralCode;
}
</script>
@endpush
