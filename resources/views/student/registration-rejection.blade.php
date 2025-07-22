@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Registration Status: {{ ucfirst($registration->status) }}
                    </h4>
                    <div>
                        @if($registration->status === 'rejected')
                            <span class="badge badge-danger px-3 py-2">Needs Attention</span>
                        @elseif($registration->status === 'resubmitted')
                            <span class="badge badge-warning px-3 py-2">Under Review</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    @if($registration->status === 'rejected')
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-times-circle me-2"></i>Registration Rejected</h5>
                            <p class="mb-2">Your registration has been rejected for the following reason:</p>
                            <blockquote class="blockquote mb-3">
                                <p class="mb-0">"{{ $registration->rejection_reason }}"</p>
                                <footer class="blockquote-footer mt-2">
                                    Rejected on {{ $registration->rejected_at ? $registration->rejected_at->format('M d, Y \a\t h:i A') : 'Unknown date' }}
                                </footer>
                            </blockquote>
                            
                            @if($rejectedFields && count($rejectedFields) > 0)
                                <h6>Fields that need attention:</h6>
                                <div class="rejected-fields mb-3">
                                    @foreach($rejectedFields as $field)
                                        <span class="badge badge-danger me-1 mb-1">{{ ucwords(str_replace('_', ' ', $field)) }}</span>
                                    @endforeach
                                </div>
                            @endif
                            
                            <p class="mb-0">
                                <strong>What to do next:</strong> Please review the highlighted fields below, make the necessary corrections, and resubmit your application.
                            </p>
                        </div>
                    @elseif($registration->status === 'resubmitted')
                        <div class="alert alert-info">
                            <h5><i class="fas fa-clock me-2"></i>Under Review</h5>
                            <p class="mb-2">
                                Thank you for resubmitting your registration. Your application is currently under review by our admissions team.
                            </p>
                            <p class="mb-0">
                                <strong>Resubmitted on:</strong> {{ $registration->resubmitted_at ? $registration->resubmitted_at->format('M d, Y \a\t h:i A') : 'Recently' }}
                            </p>
                        </div>
                    @endif
                    
                    <!-- Registration Form -->
                    <form id="resubmissionForm" method="POST" action="{{ route('student.registration.resubmit', $registration->registration_id) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user me-2"></i>Personal Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="firstname" class="form-label">
                                                    First Name
                                                    @if(in_array('firstname', $rejectedFields ?? []))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" 
                                                       class="form-control @if(in_array('firstname', $rejectedFields ?? [])) is-invalid @endif" 
                                                       id="firstname" 
                                                       name="firstname" 
                                                       value="{{ old('firstname', $registration->firstname) }}" 
                                                       required>
                                                @if(in_array('firstname', $rejectedFields ?? []))
                                                    <div class="invalid-feedback">This field was marked for correction</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="lastname" class="form-label">
                                                    Last Name
                                                    @if(in_array('lastname', $rejectedFields ?? []))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" 
                                                       class="form-control @if(in_array('lastname', $rejectedFields ?? [])) is-invalid @endif" 
                                                       id="lastname" 
                                                       name="lastname" 
                                                       value="{{ old('lastname', $registration->lastname) }}" 
                                                       required>
                                                @if(in_array('lastname', $rejectedFields ?? []))
                                                    <div class="invalid-feedback">This field was marked for correction</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="contact_number" class="form-label">
                                                    Contact Number
                                                    @if(in_array('contact_number', $rejectedFields ?? []))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="tel" 
                                                       class="form-control @if(in_array('contact_number', $rejectedFields ?? [])) is-invalid @endif" 
                                                       id="contact_number" 
                                                       name="contact_number" 
                                                       value="{{ old('contact_number', $registration->contact_number) }}">
                                                @if(in_array('contact_number', $rejectedFields ?? []))
                                                    <div class="invalid-feedback">This field was marked for correction</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="birthdate" class="form-label">
                                                    Birth Date
                                                    @if(in_array('birthdate', $rejectedFields ?? []))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="date" 
                                                       class="form-control @if(in_array('birthdate', $rejectedFields ?? [])) is-invalid @endif" 
                                                       id="birthdate" 
                                                       name="birthdate" 
                                                       value="{{ old('birthdate', $registration->birthdate) }}">
                                                @if(in_array('birthdate', $rejectedFields ?? []))
                                                    <div class="invalid-feedback">This field was marked for correction</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Address Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-map-marker-alt me-2"></i>Address Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="street_address" class="form-label">
                                                Street Address
                                                @if(in_array('street_address', $rejectedFields ?? []))
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="text" 
                                                   class="form-control @if(in_array('street_address', $rejectedFields ?? [])) is-invalid @endif" 
                                                   id="street_address" 
                                                   name="street_address" 
                                                   value="{{ old('street_address', $registration->street_address) }}">
                                            @if(in_array('street_address', $rejectedFields ?? []))
                                                <div class="invalid-feedback">This field was marked for correction</div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="city" class="form-label">
                                                    City
                                                    @if(in_array('city', $rejectedFields ?? []))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" 
                                                       class="form-control @if(in_array('city', $rejectedFields ?? [])) is-invalid @endif" 
                                                       id="city" 
                                                       name="city" 
                                                       value="{{ old('city', $registration->city) }}">
                                                @if(in_array('city', $rejectedFields ?? []))
                                                    <div class="invalid-feedback">This field was marked for correction</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="state_province" class="form-label">
                                                    State/Province
                                                    @if(in_array('state_province', $rejectedFields ?? []))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" 
                                                       class="form-control @if(in_array('state_province', $rejectedFields ?? [])) is-invalid @endif" 
                                                       id="state_province" 
                                                       name="state_province" 
                                                       value="{{ old('state_province', $registration->state_province) }}">
                                                @if(in_array('state_province', $rejectedFields ?? []))
                                                    <div class="invalid-feedback">This field was marked for correction</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="zipcode" class="form-label">
                                                ZIP Code
                                                @if(in_array('zipcode', $rejectedFields ?? []))
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="text" 
                                                   class="form-control @if(in_array('zipcode', $rejectedFields ?? [])) is-invalid @endif" 
                                                   id="zipcode" 
                                                   name="zipcode" 
                                                   value="{{ old('zipcode', $registration->zipcode) }}">
                                            @if(in_array('zipcode', $rejectedFields ?? []))
                                                <div class="invalid-feedback">This field was marked for correction</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Documents Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-file-alt me-2"></i>Required Documents
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $documents = [
                                            'good_moral' => 'Good Moral Character Certificate',
                                            'PSA' => 'PSA Birth Certificate',
                                            'Course_Cert' => 'Course Certificate',
                                            'TOR' => 'Transcript of Records (TOR)',
                                            'valid_id' => 'Valid ID',
                                        ];
                                    @endphp
                                    
                                    @foreach($documents as $fieldName => $documentLabel)
                                        <div class="col-md-6 mb-3">
                                            <label for="{{ $fieldName }}" class="form-label">
                                                {{ $documentLabel }}
                                                @if(in_array($fieldName, $rejectedFields ?? []))
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            
                                            @if($registration->$fieldName)
                                                <div class="current-file mb-2">
                                                    <div class="alert alert-info py-2">
                                                        <i class="fas fa-file me-1"></i>
                                                        Current file: 
                                                        <a href="{{ $registration->$fieldName }}" target="_blank" class="text-decoration-none">
                                                            View current document
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <input type="file" 
                                                   class="form-control @if(in_array($fieldName, $rejectedFields ?? [])) is-invalid @endif" 
                                                   id="{{ $fieldName }}" 
                                                   name="{{ $fieldName }}" 
                                                   accept=".pdf,.jpg,.jpeg,.png">
                                            @if(in_array($fieldName, $rejectedFields ?? []))
                                                <div class="invalid-feedback">This document was marked for correction - please upload a new file</div>
                                            @else
                                                <div class="form-text">Upload a new file only if you need to replace the current document</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- Program Information (Read-only) -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-graduation-cap me-2"></i>Program Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Program:</strong><br>
                                        <span class="text-muted">{{ $registration->program_name }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Package:</strong><br>
                                        <span class="text-muted">{{ $registration->package_name }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Learning Mode:</strong><br>
                                        <span class="text-muted">{{ ucfirst($registration->learning_mode) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($registration->status === 'rejected')
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Resubmit Application
                                </button>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        By resubmitting, you acknowledge that you have addressed the concerns mentioned above.
                                    </small>
                                </div>
                            </div>
                        @else
                            <div class="text-center">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Your resubmission is being reviewed. You will be notified of the outcome soon.
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resubmissionForm');
    
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        }
    });
    
    // Highlight rejected fields
    const rejectedFields = document.querySelectorAll('.is-invalid');
    rejectedFields.forEach(field => {
        field.addEventListener('focus', function() {
            this.classList.remove('is-invalid');
            const feedback = this.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.style.display = 'none';
            }
        });
    });
});
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.875em;
}

.is-invalid {
    border-color: #dc3545;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.rejected-fields .badge {
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}

.current-file .alert {
    margin-bottom: 0.5rem;
}

.btn-lg {
    padding: 0.75rem 2rem;
    font-size: 1.1rem;
}

.text-danger {
    font-weight: bold;
}
</style>
@endsection
