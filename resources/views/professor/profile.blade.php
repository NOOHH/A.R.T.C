@extends('professor.professor-layouts.professor-layout')

@section('title', 'My Profile')

@section('content')
<div class="row">
    <!-- Main Profile Card -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-person-circle me-2"></i>Profile Information
                </h5>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleEditMode()">
                    <i class="bi bi-pencil me-1"></i><span id="edit-btn-text">Edit Profile</span>
                </button>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('professor.profile.update') }}" id="profileForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Personal Information -->
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-person me-2"></i>Personal Information
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" 
                                       class="form-control profile-input @error('first_name') is-invalid @enderror" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="{{ old('first_name', $professor->professor_first_name) }}" 
                                       readonly
                                       required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" 
                                       class="form-control profile-input @error('last_name') is-invalid @enderror" 
                                       id="last_name" 
                                       name="last_name" 
                                       value="{{ old('last_name', $professor->professor_last_name) }}" 
                                       readonly
                                       required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" 
                                       class="form-control profile-input @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $professor->professor_email) }}" 
                                       readonly
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Fields -->
                    @if($dynamicFields && $dynamicFields->count() > 0)
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">
                        <i class="bi bi-gear me-2"></i>Additional Information
                    </h6>
                    
                    @foreach($dynamicFields as $field)
                        <div class="mb-3">
                            <label for="{{ $field->field_name }}" class="form-label">
                                {{ $field->field_label }}
                                @if($field->is_required) * @endif
                            </label>
                            
                            @if($field->field_type === 'text' || $field->field_type === 'email' || $field->field_type === 'tel')
                                <input type="{{ $field->field_type }}" 
                                       class="form-control profile-input @error($field->field_name) is-invalid @enderror" 
                                       id="{{ $field->field_name }}" 
                                       name="{{ $field->field_name }}" 
                                       value="{{ old($field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}" 
                                       readonly
                                       @if($field->is_required) required @endif>
                            @elseif($field->field_type === 'textarea')
                                <textarea class="form-control profile-input @error($field->field_name) is-invalid @enderror" 
                                          id="{{ $field->field_name }}" 
                                          name="{{ $field->field_name }}" 
                                          readonly
                                          rows="3" 
                                          @if($field->is_required) required @endif>{{ old($field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}</textarea>
                            @elseif($field->field_type === 'select')
                                <select class="form-select profile-input @error($field->field_name) is-invalid @enderror" 
                                        id="{{ $field->field_name }}" 
                                        name="{{ $field->field_name }}" 
                                        disabled
                                        @if($field->is_required) required @endif>
                                    <option value="">Choose...</option>
                                    @if($field->field_options)
                                        @foreach(json_decode($field->field_options) as $option)
                                            <option value="{{ $option }}" 
                                                    {{ old($field->field_name, $professor->dynamic_data[$field->field_name] ?? '') === $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            @endif
                            
                            @error($field->field_name)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end mt-4" id="actionButtons" style="display: none;">
                        <button type="button" class="btn btn-secondary me-2" onclick="cancelEdit()">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <!-- Profile Photo -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-camera me-2"></i>Profile Photo
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-3">
                    @if($professor->profile_photo)
                        <img src="{{ asset('storage/' . $professor->profile_photo) }}" 
                             alt="Profile Photo" 
                             class="rounded-circle" 
                             style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 2.5rem;">
                            <i class="bi bi-person"></i>
                        </div>
                    @endif
                </div>
                <form method="POST" action="{{ route('professor.profile.photo.update') }}" enctype="multipart/form-data" id="photoForm">
                    @csrf
                    <div class="mb-3">
                        <input type="file" 
                               class="form-control @error('profile_photo') is-invalid @enderror" 
                               id="profile_photo" 
                               name="profile_photo" 
                               accept="image/*"
                               onchange="previewPhoto(this)">
                        @error('profile_photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Max file size: 2MB. Allowed: JPG, PNG, GIF</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-upload me-1"></i>Update Photo
                    </button>
                    @if($professor->profile_photo)
                        <button type="button" class="btn btn-outline-danger btn-sm ms-2" onclick="removePhoto()">
                            <i class="bi bi-trash me-1"></i>Remove
                        </button>
                    @endif
                </form>
            </div>
        </div>

        <!-- Account Details -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-shield-check me-2"></i>Account Details
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Professor ID:</small>
                    <div><strong>{{ $professor->professor_id }}</strong></div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Referral Code:</small>
                    <div><strong>{{ $professor->referral_code ?? 'Not set' }}</strong></div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Member Since:</small>
                    <div>{{ $professor->created_at ? $professor->created_at->format('F Y') : 'N/A' }}</div>
                </div>
                <div>
                    <small class="text-muted">Status:</small>
                    <div>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleEditMode() {
    const inputs = document.querySelectorAll('.profile-input');
    const selectInputs = document.querySelectorAll('select.profile-input');
    const actionButtons = document.getElementById('actionButtons');
    const editBtn = document.getElementById('edit-btn-text');
    
    const isReadonly = inputs[0].hasAttribute('readonly');
    
    if (isReadonly) {
        // Enable editing
        inputs.forEach(input => {
            if (input.type !== 'email') { // Keep email readonly for security
                input.removeAttribute('readonly');
            }
        });
        selectInputs.forEach(select => {
            select.removeAttribute('disabled');
        });
        actionButtons.style.display = 'flex';
        editBtn.textContent = 'Cancel Edit';
    } else {
        // Disable editing
        cancelEdit();
    }
}

function cancelEdit() {
    const inputs = document.querySelectorAll('.profile-input');
    const selectInputs = document.querySelectorAll('select.profile-input');
    const actionButtons = document.getElementById('actionButtons');
    const editBtn = document.getElementById('edit-btn-text');
    
    inputs.forEach(input => {
        input.setAttribute('readonly', true);
    });
    selectInputs.forEach(select => {
        select.setAttribute('disabled', true);
    });
    actionButtons.style.display = 'none';
    editBtn.textContent = 'Edit Profile';
    
    // Reset form to original values
    document.getElementById('profileForm').reset();
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Find the current profile image or placeholder
            const currentImg = input.closest('.card-body').querySelector('img, .rounded-circle');
            if (currentImg.tagName === 'IMG') {
                currentImg.src = e.target.result;
            } else {
                // Replace placeholder with actual image
                const imgElement = document.createElement('img');
                imgElement.src = e.target.result;
                imgElement.alt = 'Profile Photo';
                imgElement.className = 'rounded-circle';
                imgElement.style.cssText = 'width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;';
                currentImg.parentNode.replaceChild(imgElement, currentImg);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removePhoto() {
    if (confirm('Are you sure you want to remove your profile photo?')) {
        fetch('{{ route("professor.profile.photo.remove") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error removing photo: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the photo.');
        });
    }
}
</script>
@endpush
