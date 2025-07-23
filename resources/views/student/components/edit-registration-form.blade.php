<form id="editRegistrationForm" enctype="multipart/form-data">
    @csrf
    
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Editing Registration:</strong> Please correct the highlighted fields and resubmit your registration.
    </div>
    
    @if(!empty($rejectedFields))
    <div class="alert alert-warning">
        <h6><i class="bi bi-exclamation-triangle me-2"></i>Fields that need correction:</h6>
        <ul class="mb-0">
            @foreach($rejectedFields as $field)
            <li><strong>{{ str_replace('_', ' ', strtoupper($field)) }}</strong></li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-12">
            <h6 class="border-bottom pb-2 mb-3">Personal Information</h6>
        </div>
        
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="firstname" class="form-label">
                    First Name <span class="text-danger">*</span>
                    @if(in_array('firstname', $rejectedFields))
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    @endif
                </label>
                <input type="text" 
                       id="firstname" 
                       name="firstname" 
                       class="form-control {{ in_array('firstname', $rejectedFields) ? 'border-danger' : '' }}" 
                       value="{{ $enrollment->firstname ?? $user->user_firstname ?? '' }}" 
                       required>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="lastname" class="form-label">
                    Last Name <span class="text-danger">*</span>
                    @if(in_array('lastname', $rejectedFields))
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    @endif
                </label>
                <input type="text" 
                       id="lastname" 
                       name="lastname" 
                       class="form-control {{ in_array('lastname', $rejectedFields) ? 'border-danger' : '' }}" 
                       value="{{ $enrollment->lastname ?? $user->user_lastname ?? '' }}" 
                       required>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label for="middlename" class="form-label">
                    Middle Name
                    @if(in_array('middlename', $rejectedFields))
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    @endif
                </label>
                <input type="text" 
                       id="middlename" 
                       name="middlename" 
                       class="form-control {{ in_array('middlename', $rejectedFields) ? 'border-danger' : '' }}" 
                       value="{{ $enrollment->middlename ?? '' }}">
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="email" class="form-label">
                    Email Address <span class="text-danger">*</span>
                    @if(in_array('email', $rejectedFields))
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    @endif
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control {{ in_array('email', $rejectedFields) ? 'border-danger' : '' }}" 
                       value="{{ $enrollment->email ?? $user->user_email ?? '' }}" 
                       required>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="contact_number" class="form-label">
                    Contact Number <span class="text-danger">*</span>
                    @if(in_array('contact_number', $rejectedFields))
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    @endif
                </label>
                <input type="tel" 
                       id="contact_number" 
                       name="contact_number" 
                       class="form-control {{ in_array('contact_number', $rejectedFields) ? 'border-danger' : '' }}" 
                       value="{{ $enrollment->contact_number ?? '' }}" 
                       required>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label for="address" class="form-label">
                    Address <span class="text-danger">*</span>
                    @if(in_array('address', $rejectedFields))
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    @endif
                </label>
                <textarea id="address" 
                          name="address" 
                          class="form-control {{ in_array('address', $rejectedFields) ? 'border-danger' : '' }}" 
                          rows="3" 
                          required>{{ $enrollment->address ?? '' }}</textarea>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="education_level" class="form-label">
                    Education Level <span class="text-danger">*</span>
                    @if(in_array('education_level', $rejectedFields))
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    @endif
                </label>
                <select id="education_level" 
                        name="education_level" 
                        class="form-select {{ in_array('education_level', $rejectedFields) ? 'border-danger' : '' }}" 
                        required>
                    <option value="">Select Education Level</option>
                    <option value="elementary" {{ ($enrollment->education_level ?? '') == 'elementary' ? 'selected' : '' }}>Elementary Graduate</option>
                    <option value="high_school" {{ ($enrollment->education_level ?? '') == 'high_school' ? 'selected' : '' }}>High School Graduate</option>
                    <option value="college_undergraduate" {{ ($enrollment->education_level ?? '') == 'college_undergraduate' ? 'selected' : '' }}>College Undergraduate</option>
                    <option value="college_graduate" {{ ($enrollment->education_level ?? '') == 'college_graduate' ? 'selected' : '' }}>College Graduate</option>
                    <option value="vocational" {{ ($enrollment->education_level ?? '') == 'vocational' ? 'selected' : '' }}>Vocational Graduate</option>
                    <option value="masters" {{ ($enrollment->education_level ?? '') == 'masters' ? 'selected' : '' }}>Master's Degree</option>
                    <option value="doctorate" {{ ($enrollment->education_level ?? '') == 'doctorate' ? 'selected' : '' }}>Doctorate Degree</option>
                </select>
            </div>
        </div>
        
        <!-- Document Uploads -->
        <div class="col-md-12 mt-4">
            <h6 class="border-bottom pb-2 mb-3">Required Documents</h6>
            <p class="text-muted small">Please upload clear, readable copies of the required documents. Accepted formats: JPG, PNG, PDF (Max 5MB each)</p>
        </div>
        
        @php
            $fileFields = [
                'tor' => 'Transcript of Records (TOR)',
                'psa_birth_certificate' => 'PSA Birth Certificate', 
                'good_moral_certificate' => 'Good Moral Certificate',
                'certificate' => 'Certificate/Diploma',
                'photo' => 'ID Photo (2x2)',
            ];
        @endphp
        
        @foreach($fileFields as $fieldName => $fieldLabel)
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="{{ $fieldName }}" class="form-label">
                    {{ $fieldLabel }}
                    @if(in_array($fieldName, $rejectedFields))
                        <span class="text-danger">*</span>
                        <span class="badge bg-danger ms-1">Needs Correction</span>
                    @endif
                </label>
                <input type="file" 
                       id="{{ $fieldName }}" 
                       name="{{ $fieldName }}" 
                       class="form-control {{ in_array($fieldName, $rejectedFields) ? 'border-danger' : '' }}" 
                       accept=".jpg,.jpeg,.png,.pdf">
                
                @if($enrollment->{$fieldName})
                <div class="mt-2">
                    <small class="text-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Current file: {{ basename($enrollment->{$fieldName}) }}
                    </small>
                    <br>
                    <small class="text-muted">Upload a new file to replace the current one</small>
                </div>
                @endif
                
                @if(in_array($fieldName, $rejectedFields))
                <div class="mt-1">
                    <small class="text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        This document needs to be corrected or replaced
                    </small>
                </div>
                @endif
            </div>
        </div>
        @endforeach
        
        <!-- Additional Fields based on Form Requirements -->
        @foreach($formRequirements as $requirement)
            @if($requirement->field_type === 'file' && !in_array($requirement->field_name, array_keys($fileFields)))
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="{{ $requirement->field_name }}" class="form-label">
                        {{ $requirement->field_label }}
                        @if($requirement->is_required || in_array($requirement->field_name, $rejectedFields))
                            <span class="text-danger">*</span>
                        @endif
                        @if(in_array($requirement->field_name, $rejectedFields))
                            <span class="badge bg-danger ms-1">Needs Correction</span>
                        @endif
                    </label>
                    <input type="file" 
                           id="{{ $requirement->field_name }}" 
                           name="{{ $requirement->field_name }}" 
                           class="form-control {{ in_array($requirement->field_name, $rejectedFields) ? 'border-danger' : '' }}" 
                           accept=".jpg,.jpeg,.png,.pdf"
                           {{ $requirement->is_required ? 'required' : '' }}>
                    
                    @if($enrollment->{$requirement->field_name})
                    <div class="mt-2">
                        <small class="text-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Current file: {{ basename($enrollment->{$requirement->field_name}) }}
                        </small>
                    </div>
                    @endif
                </div>
            </div>
            @elseif($requirement->field_type === 'text' && !in_array($requirement->field_name, ['firstname', 'lastname', 'middlename', 'email', 'contact_number', 'address']))
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="{{ $requirement->field_name }}" class="form-label">
                        {{ $requirement->field_label }}
                        @if($requirement->is_required || in_array($requirement->field_name, $rejectedFields))
                            <span class="text-danger">*</span>
                        @endif
                        @if(in_array($requirement->field_name, $rejectedFields))
                            <span class="badge bg-danger ms-1">Needs Correction</span>
                        @endif
                    </label>
                    <input type="text" 
                           id="{{ $requirement->field_name }}" 
                           name="{{ $requirement->field_name }}" 
                           class="form-control {{ in_array($requirement->field_name, $rejectedFields) ? 'border-danger' : '' }}" 
                           value="{{ $enrollment->{$requirement->field_name} ?? '' }}"
                           {{ $requirement->is_required ? 'required' : '' }}>
                </div>
            </div>
            @endif
        @endforeach
    </div>
    
    <div class="alert alert-warning mt-4">
        <h6><i class="bi bi-exclamation-triangle me-2"></i>Important Notes:</h6>
        <ul class="mb-0">
            <li>Please ensure all information is accurate and complete</li>
            <li>Upload clear, readable copies of all required documents</li>
            <li>Your registration will be reviewed again by administrators</li>
            <li>You will be notified of the review result via email</li>
        </ul>
    </div>
</form>
