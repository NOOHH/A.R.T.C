@extends('professor.layout')

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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control profile-input @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $professor->dynamic_data['phone'] ?? '') }}" 
                                       readonly
                                       placeholder="+1 (555) 123-4567">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">
                        <i class="bi bi-briefcase me-2"></i>Professional Information
                    </h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Professional Title</label>
                                <input type="text" 
                                       class="form-control profile-input @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', $professor->dynamic_data['title'] ?? '') }}" 
                                       readonly
                                       placeholder="e.g., Associate Professor, Senior Lecturer">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="specialization" class="form-label">Specialization</label>
                                <input type="text" 
                                       class="form-control profile-input @error('specialization') is-invalid @enderror" 
                                       id="specialization" 
                                       name="specialization" 
                                       value="{{ old('specialization', $professor->dynamic_data['specialization'] ?? '') }}" 
                                       readonly
                                       placeholder="e.g., Computer Science, Mathematics">
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="experience_years" class="form-label">Years of Experience</label>
                                <input type="number" 
                                       class="form-control profile-input @error('experience_years') is-invalid @enderror" 
                                       id="experience_years" 
                                       name="experience_years" 
                                       value="{{ old('experience_years', $professor->dynamic_data['experience_years'] ?? '') }}" 
                                       readonly
                                       min="0" 
                                       max="50">
                                @error('experience_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="education" class="form-label">Highest Education</label>
                                <select class="form-select profile-input @error('education') is-invalid @enderror" 
                                        id="education" 
                                        name="education" 
                                        disabled>
                                    <option value="">Select Education Level</option>
                                    <option value="bachelor" {{ old('education', $professor->dynamic_data['education'] ?? '') === 'bachelor' ? 'selected' : '' }}>Bachelor's Degree</option>
                                    <option value="master" {{ old('education', $professor->dynamic_data['education'] ?? '') === 'master' ? 'selected' : '' }}>Master's Degree</option>
                                    <option value="doctorate" {{ old('education', $professor->dynamic_data['education'] ?? '') === 'doctorate' ? 'selected' : '' }}>Doctorate/PhD</option>
                                    <option value="other" {{ old('education', $professor->dynamic_data['education'] ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('education')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Social Links -->
                    <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">
                        <i class="bi bi-link-45deg me-2"></i>Contact & Social Links
                    </h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="linkedin" class="form-label">LinkedIn Profile</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-linkedin"></i></span>
                                    <input type="url" 
                                           class="form-control profile-input @error('linkedin') is-invalid @enderror" 
                                           id="linkedin" 
                                           name="linkedin" 
                                           value="{{ old('linkedin', $professor->dynamic_data['linkedin'] ?? '') }}" 
                                           readonly
                                           placeholder="https://linkedin.com/in/username">
                                </div>
                                @error('linkedin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="website" class="form-label">Personal Website</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                    <input type="url" 
                                           class="form-control profile-input @error('website') is-invalid @enderror" 
                                           id="website" 
                                           name="website" 
                                           value="{{ old('website', $professor->dynamic_data['website'] ?? '') }}" 
                                           readonly
                                           placeholder="https://yourwebsite.com">
                                </div>
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Professional Bio</label>
                        <textarea class="form-control profile-input @error('bio') is-invalid @enderror" 
                                  id="bio" 
                                  name="bio" 
                                  rows="4" 
                                  readonly
                                  placeholder="Brief description of your professional background, expertise, and teaching philosophy...">{{ old('bio', $professor->dynamic_data['bio'] ?? '') }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                    <div class="d-flex justify-content-end mt-4" id="actionButtons" style="display: none !important;">
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
        <!-- Quick Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>Quick Stats
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-0">{{ $professor->programs->count() }}</h4>
                            <small class="text-muted">Programs</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0">{{ $professor->batches->count() }}</h4>
                        <small class="text-muted">Batches</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Programs -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-collection me-2"></i>Assigned Programs
                </h6>
            </div>
            <div class="card-body">
                @if($professor->programs->count() > 0)
                    @foreach($professor->programs as $program)
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary me-2">{{ $program->program_name }}</span>
                            @if($program->pivot && $program->pivot->video_link)
                                <a href="{{ $program->pivot->video_link }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-success ms-auto"
                                   title="Meeting Link">
                                    <i class="bi bi-camera-video"></i>
                                </a>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-muted mb-0">No programs assigned yet.</p>
                @endif
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
</script>
@endpush
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $professor->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dynamic Additional Fields -->
                        @if(isset($dynamicFields) && $dynamicFields->count() > 0)
                        <hr>
                        <h6 class="mb-3">Additional Information</h6>
                        
                        @foreach($dynamicFields as $field)
                            <div class="mb-3">
                                <label for="dynamic_{{ $field->field_name }}" class="form-label">
                                    {{ $field->display_name }} 
                                    @if($field->is_required) * @endif
                                </label>
                                
                                @if($field->field_type === 'text')
                                    <input type="text" 
                                           class="form-control @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                           id="dynamic_{{ $field->field_name }}" 
                                           name="dynamic[{{ $field->field_name }}]" 
                                           value="{{ old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->field_type === 'textarea')
                                    <textarea class="form-control @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                              id="dynamic_{{ $field->field_name }}" 
                                              name="dynamic[{{ $field->field_name }}]" 
                                              rows="3"
                                              {{ $field->is_required ? 'required' : '' }}>{{ old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}</textarea>
                                @elseif($field->field_type === 'select')
                                    <select class="form-select @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                            id="dynamic_{{ $field->field_name }}" 
                                            name="dynamic[{{ $field->field_name }}]"
                                            {{ $field->is_required ? 'required' : '' }}>
                                        <option value="">Choose...</option>
                                        @if($field->field_options)
                                            @foreach(json_decode($field->field_options, true) as $option)
                                                <option value="{{ $option }}" 
                                                        {{ (old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') == $option) ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                @elseif($field->field_type === 'email')
                                    <input type="email" 
                                           class="form-control @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                           id="dynamic_{{ $field->field_name }}" 
                                           name="dynamic[{{ $field->field_name }}]" 
                                           value="{{ old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->field_type === 'phone')
                                    <input type="tel" 
                                           class="form-control @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                           id="dynamic_{{ $field->field_name }}" 
                                           name="dynamic[{{ $field->field_name }}]" 
                                           value="{{ old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->field_type === 'date')
                                    <input type="date" 
                                           class="form-control @error('dynamic.'.$field->field_name) is-invalid @enderror" 
                                           id="dynamic_{{ $field->field_name }}" 
                                           name="dynamic[{{ $field->field_name }}]" 
                                           value="{{ old('dynamic.'.$field->field_name, $professor->dynamic_data[$field->field_name] ?? '') }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                @endif
                                
                                @if($field->help_text)
                                    <div class="form-text">{{ $field->help_text }}</div>
                                @endif
                                
                                @error('dynamic.'.$field->field_name)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                        @endif
                        
                        <hr>
                        
                        <h6 class="mb-3">Change Password</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password">
                                    <div class="form-text">Leave empty to keep current password</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-person-fill text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="mt-3 mb-1">{{ $professor->full_name }}</h5>
                        <small class="text-muted">Professor</small>
                    </div>
                    
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Email:</strong><br>
                            <small class="text-muted">{{ $professor->email }}</small>
                        </li>
                        <li class="mb-2">
                            <strong>Professor ID:</strong><br>
                            <small class="text-muted">#{{ $professor->professor_id }}</small>
                        </li>
                        <li class="mb-2">
                            <strong>Assigned Programs:</strong><br>
                            <small class="text-muted">{{ $professor->programs->count() }} program(s)</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
