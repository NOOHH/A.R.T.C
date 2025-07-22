@extends('layouts.student')

@section('title', 'Resubmit Registration')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Resubmit Registration</h3>
                    <p class="text-muted mb-0">Please correct the highlighted fields and resubmit your registration.</p>
                </div>
                
                <div class="card-body">
                    @if($latestRejection)
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Rejection Details</h5>
                            <p><strong>Reason:</strong> {{ $latestRejection->reason }}</p>
                            @if($latestRejection->rejected_fields && is_array($latestRejection->rejected_fields))
                                <p><strong>Fields that need correction:</strong></p>
                                <ul>
                                    @foreach($latestRejection->rejected_fields as $field)
                                        <li class="text-danger">
                                            <i class="fas fa-times"></i> {{ ucfirst(str_replace('_', ' ', $field)) }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('student.registration.resubmit.store', $registration->id) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">Personal Information</h5>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    First Name
                                    @if(in_array('first_name', $rejectedFields))
                                        <span class="text-danger">*</span>
                                        <i class="fas fa-exclamation-triangle text-danger" title="This field was rejected"></i>
                                    @endif
                                </label>
                                <input type="text" 
                                       class="form-control {{ in_array('first_name', $rejectedFields) ? 'is-invalid' : '' }}" 
                                       name="first_name" 
                                       value="{{ old('first_name', $registration->first_name) }}" 
                                       required>
                                @if(in_array('first_name', $rejectedFields))
                                    <div class="invalid-feedback">This field needs to be corrected</div>
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Last Name
                                    @if(in_array('last_name', $rejectedFields))
                                        <span class="text-danger">*</span>
                                        <i class="fas fa-exclamation-triangle text-danger" title="This field was rejected"></i>
                                    @endif
                                </label>
                                <input type="text" 
                                       class="form-control {{ in_array('last_name', $rejectedFields) ? 'is-invalid' : '' }}" 
                                       name="last_name" 
                                       value="{{ old('last_name', $registration->last_name) }}" 
                                       required>
                                @if(in_array('last_name', $rejectedFields))
                                    <div class="invalid-feedback">This field needs to be corrected</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Email
                                    @if(in_array('email', $rejectedFields))
                                        <span class="text-danger">*</span>
                                        <i class="fas fa-exclamation-triangle text-danger" title="This field was rejected"></i>
                                    @endif
                                </label>
                                <input type="email" 
                                       class="form-control {{ in_array('email', $rejectedFields) ? 'is-invalid' : '' }}" 
                                       name="email" 
                                       value="{{ old('email', $registration->email) }}" 
                                       required>
                                @if(in_array('email', $rejectedFields))
                                    <div class="invalid-feedback">This field needs to be corrected</div>
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Phone
                                    @if(in_array('phone', $rejectedFields))
                                        <span class="text-danger">*</span>
                                        <i class="fas fa-exclamation-triangle text-danger" title="This field was rejected"></i>
                                    @endif
                                </label>
                                <input type="text" 
                                       class="form-control {{ in_array('phone', $rejectedFields) ? 'is-invalid' : '' }}" 
                                       name="phone" 
                                       value="{{ old('phone', $registration->phone) }}" 
                                       required>
                                @if(in_array('phone', $rejectedFields))
                                    <div class="invalid-feedback">This field needs to be corrected</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Date of Birth
                                    @if(in_array('date_of_birth', $rejectedFields))
                                        <span class="text-danger">*</span>
                                        <i class="fas fa-exclamation-triangle text-danger" title="This field was rejected"></i>
                                    @endif
                                </label>
                                <input type="date" 
                                       class="form-control {{ in_array('date_of_birth', $rejectedFields) ? 'is-invalid' : '' }}" 
                                       name="date_of_birth" 
                                       value="{{ old('date_of_birth', $registration->date_of_birth ? $registration->date_of_birth->format('Y-m-d') : '') }}">
                                @if(in_array('date_of_birth', $rejectedFields))
                                    <div class="invalid-feedback">This field needs to be corrected</div>
                                @endif
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Gender
                                    @if(in_array('gender', $rejectedFields))
                                        <span class="text-danger">*</span>
                                        <i class="fas fa-exclamation-triangle text-danger" title="This field was rejected"></i>
                                    @endif
                                </label>
                                <select class="form-control {{ in_array('gender', $rejectedFields) ? 'is-invalid' : '' }}" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $registration->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $registration->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $registration->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @if(in_array('gender', $rejectedFields))
                                    <div class="invalid-feedback">This field needs to be corrected</div>
                                @endif
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Civil Status</label>
                                <select class="form-control" name="civil_status">
                                    <option value="">Select Civil Status</option>
                                    <option value="single" {{ old('civil_status', $registration->civil_status) === 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="married" {{ old('civil_status', $registration->civil_status) === 'married' ? 'selected' : '' }}>Married</option>
                                    <option value="divorced" {{ old('civil_status', $registration->civil_status) === 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="widowed" {{ old('civil_status', $registration->civil_status) === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Address Information -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">Address Information</h5>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">
                                    Address
                                    @if(in_array('address', $rejectedFields))
                                        <span class="text-danger">*</span>
                                        <i class="fas fa-exclamation-triangle text-danger" title="This field was rejected"></i>
                                    @endif
                                </label>
                                <textarea class="form-control {{ in_array('address', $rejectedFields) ? 'is-invalid' : '' }}" 
                                          name="address" 
                                          rows="2">{{ old('address', $registration->address) }}</textarea>
                                @if(in_array('address', $rejectedFields))
                                    <div class="invalid-feedback">This field needs to be corrected</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    City
                                    @if(in_array('city', $rejectedFields))
                                        <span class="text-danger">*</span>
                                        <i class="fas fa-exclamation-triangle text-danger" title="This field was rejected"></i>
                                    @endif
                                </label>
                                <input type="text" 
                                       class="form-control {{ in_array('city', $rejectedFields) ? 'is-invalid' : '' }}" 
                                       name="city" 
                                       value="{{ old('city', $registration->city) }}">
                                @if(in_array('city', $rejectedFields))
                                    <div class="invalid-feedback">This field needs to be corrected</div>
                                @endif
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label class="form-label">State/Province</label>
                                <input type="text" class="form-control" name="state" value="{{ old('state', $registration->state) }}">
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" class="form-control" name="country" value="{{ old('country', $registration->country) }}">
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code', $registration->postal_code) }}">
                            </div>
                        </div>
                        
                        <!-- Education Information -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">Education Information</h5>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Education Level
                                    @if(in_array('education_level', $rejectedFields))
                                        <span class="text-danger">*</span>
                                        <i class="fas fa-exclamation-triangle text-danger" title="This field was rejected"></i>
                                    @endif
                                </label>
                                <select class="form-control {{ in_array('education_level', $rejectedFields) ? 'is-invalid' : '' }}" name="education_level" required>
                                    <option value="">Select Education Level</option>
                                    <option value="high_school" {{ old('education_level', $registration->education_level) === 'high_school' ? 'selected' : '' }}>High School</option>
                                    <option value="college" {{ old('education_level', $registration->education_level) === 'college' ? 'selected' : '' }}>College</option>
                                    <option value="graduate" {{ old('education_level', $registration->education_level) === 'graduate' ? 'selected' : '' }}>Graduate</option>
                                </select>
                                @if(in_array('education_level', $rejectedFields))
                                    <div class="invalid-feedback">This field needs to be corrected</div>
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Course
                                    @if(in_array('course', $rejectedFields))
                                        <span class="text-danger">*</span>
                                        <i class="fas fa-exclamation-triangle text-danger" title="This field was rejected"></i>
                                    @endif
                                </label>
                                <input type="text" 
                                       class="form-control {{ in_array('course', $rejectedFields) ? 'is-invalid' : '' }}" 
                                       name="course" 
                                       value="{{ old('course', $registration->course) }}" 
                                       required>
                                @if(in_array('course', $rejectedFields))
                                    <div class="invalid-feedback">This field needs to be corrected</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Program</label>
                                <input type="text" class="form-control" name="program" value="{{ old('program', $registration->program) }}">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Learning Mode</label>
                                <select class="form-control" name="learning_mode">
                                    <option value="">Select Learning Mode</option>
                                    <option value="online" {{ old('learning_mode', $registration->learning_mode) === 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="offline" {{ old('learning_mode', $registration->learning_mode) === 'offline' ? 'selected' : '' }}>Offline</option>
                                    <option value="hybrid" {{ old('learning_mode', $registration->learning_mode) === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- File Upload -->
                        @if(in_array('uploaded_files', $rejectedFields))
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">
                                    Required Documents
                                    <span class="text-danger">*</span>
                                    <i class="fas fa-exclamation-triangle text-danger" title="Your uploaded files were rejected"></i>
                                </h5>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Your previously uploaded files were rejected. Please upload new, corrected documents.
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Upload New Documents</label>
                                <input type="file" 
                                       class="form-control is-invalid" 
                                       name="uploaded_files[]" 
                                       multiple 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       required>
                                <div class="invalid-feedback">Please upload corrected documents</div>
                                <small class="form-text text-muted">
                                    Accepted formats: PDF, DOC, DOCX, JPG, PNG. Maximum size: 10MB per file.
                                </small>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Terms and Conditions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agreeTerms" name="agree_terms" required>
                                    <label class="form-check-label" for="agreeTerms">
                                        I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane"></i> Resubmit Registration
                                </button>
                                <a href="{{ route('student.registration.status', $registration->id) }}" class="btn btn-secondary btn-lg ms-2">
                                    <i class="fas fa-arrow-left"></i> Back to Status
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="terms-content" style="max-height: 400px; overflow-y: auto;">
                    <h6>Registration Terms and Conditions</h6>
                    <p>By submitting this registration, you agree to the following terms:</p>
                    <ul>
                        <li>All information provided is accurate and truthful</li>
                        <li>You consent to the processing of your personal data</li>
                        <li>You understand that false information may result in registration denial</li>
                        <li>You agree to comply with all institution policies</li>
                        <li>You understand that fees are non-refundable after confirmation</li>
                    </ul>
                    <!-- Add more terms as needed -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Add visual indicators for rejected fields
document.addEventListener('DOMContentLoaded', function() {
    const rejectedFields = @json($rejectedFields);
    
    rejectedFields.forEach(field => {
        const element = document.querySelector(`[name="${field}"]`);
        if (element) {
            element.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
        }
    });
});
</script>
@endsection
