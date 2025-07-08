@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'Student Settings')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* Mobile-First Responsive Design */
    .settings-container {
        padding: 1rem;
    }
    
    .settings-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .settings-card h3 {
        margin-bottom: 1.5rem;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #555;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: border-color 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #6f42c1;
        box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.1);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6f42c1, #8e44ad);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(111, 66, 193, 0.3);
    }
    
    .readonly {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
    
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: -0.5rem;
    }
    
    .col-md-6 {
        flex: 0 0 100%;
        padding: 0.5rem;
    }
    
    /* ==== TABLET DEVICES (768px - 991px) ==== */
    @media (min-width: 768px) {
        .settings-container {
            padding: 2rem;
        }
        
        .col-md-6 {
            flex: 0 0 50%;
        }
        
        .settings-card {
            padding: 2rem;
        }
    }
    
    /* ==== LAPTOP DEVICES (992px - 1199px) ==== */
    @media (min-width: 992px) {
        .settings-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .form-control {
            font-size: 1rem;
        }
    }
    
    /* ==== PC/DESKTOP DEVICES (1200px+) ==== */
    @media (min-width: 1200px) {
        .settings-container {
            max-width: 1200px;
        }
        
        .settings-card {
            padding: 2.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="settings-container">
    <div class="settings-card">
        <h3>Personal Information</h3>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <form action="{{ route('student.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" 
                               class="form-control" 
                               id="firstname" 
                               name="firstname" 
                               value="{{ old('firstname', $student->firstname ?? '') }}" 
                               required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="middlename">Middle Name</label>
                        <input type="text" 
                               class="form-control" 
                               id="middlename" 
                               name="middlename" 
                               value="{{ old('middlename', $student->middlename ?? '') }}">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" 
                               class="form-control" 
                               id="lastname" 
                               name="lastname" 
                               value="{{ old('lastname', $student->lastname ?? '') }}" 
                               required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="student_school">School</label>
                        <input type="text" 
                               class="form-control" 
                               id="student_school" 
                               name="student_school" 
                               value="{{ old('student_school', $student->student_school ?? '') }}">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="street_address">Street Address</label>
                <input type="text" 
                       class="form-control" 
                       id="street_address" 
                       name="street_address" 
                       value="{{ old('street_address', $student->street_address ?? '') }}">
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="state_province">State/Province</label>
                        <input type="text" 
                               class="form-control" 
                               id="state_province" 
                               name="state_province" 
                               value="{{ old('state_province', $student->state_province ?? '') }}">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" 
                               class="form-control" 
                               id="city" 
                               name="city" 
                               value="{{ old('city', $student->city ?? '') }}">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="zipcode">Zip Code</label>
                        <input type="text" 
                               class="form-control" 
                               id="zipcode" 
                               name="zipcode" 
                               value="{{ old('zipcode', $student->zipcode ?? '') }}">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" 
                               class="form-control" 
                               id="contact_number" 
                               name="contact_number" 
                               value="{{ old('contact_number', $student->contact_number ?? '') }}">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="emergency_contact_number">Emergency Contact Number</label>
                <input type="text" 
                       class="form-control" 
                       id="emergency_contact_number" 
                       name="emergency_contact_number" 
                       value="{{ old('emergency_contact_number', $student->emergency_contact_number ?? '') }}">
            </div>
            
            <!-- Document Upload Section -->
            <h4 class="mt-4 mb-3" style="color: #6f42c1;">Document Verification Status</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="good_moral">Good Moral Certificate</label>
                        <input type="file" 
                               class="form-control" 
                               id="good_moral" 
                               name="good_moral" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        @if($student->good_moral)
                            <small class="text-success">✓ Already uploaded</small>
                        @else
                            <small class="text-muted">No file uploaded yet</small>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="PSA">PSA Birth Certificate</label>
                        <input type="file" 
                               class="form-control" 
                               id="PSA" 
                               name="PSA" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        @if($student->PSA)
                            <small class="text-success">✓ Already uploaded</small>
                        @else
                            <small class="text-muted">No file uploaded yet</small>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Course_Cert">Course Certificate</label>
                        <input type="file" 
                               class="form-control" 
                               id="Course_Cert" 
                               name="Course_Cert" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        @if($student->Course_Cert)
                            <small class="text-success">✓ Already uploaded</small>
                        @else
                            <small class="text-muted">No file uploaded yet</small>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="TOR">Transcript of Records (TOR)</label>
                        <input type="file" 
                               class="form-control" 
                               id="TOR" 
                               name="TOR" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        @if($student->TOR)
                            <small class="text-success">✓ Already uploaded</small>
                        @else
                            <small class="text-muted">No file uploaded yet</small>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Cert_of_Grad">Certificate of Graduation</label>
                        <input type="file" 
                               class="form-control" 
                               id="Cert_of_Grad" 
                               name="Cert_of_Grad" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        @if($student->Cert_of_Grad)
                            <small class="text-success">✓ Already uploaded</small>
                        @else
                            <small class="text-muted">No file uploaded yet</small>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="photo_2x2">1x1 Photo</label>
                        <input type="file" 
                               class="form-control" 
                               id="photo_2x2" 
                               name="photo_2x2" 
                               accept=".jpg,.jpeg,.png">
                        @if($student->photo_2x2)
                            <small class="text-success">✓ Already uploaded</small>
                        @else
                            <small class="text-muted">No file uploaded yet</small>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Education Level Section -->
            <h4 class="mt-4 mb-3" style="color: #6f42c1;">Education Level</h4>
            
            <div class="form-group">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" 
                           type="radio" 
                           name="education_level" 
                           id="undergraduate" 
                           value="undergraduate"
                           {{ old('education_level', ($student->Undergraduate ? 'undergraduate' : ($student->Graduate ? 'graduate' : ''))) == 'undergraduate' ? 'checked' : '' }}>
                    <label class="form-check-label" for="undergraduate">
                        Undergraduate
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" 
                           type="radio" 
                           name="education_level" 
                           id="graduate" 
                           value="graduate"
                           {{ old('education_level', ($student->Undergraduate ? 'undergraduate' : ($student->Graduate ? 'graduate' : ''))) == 'graduate' ? 'checked' : '' }}>
                    <label class="form-check-label" for="graduate">
                        Graduate
                    </label>
                </div>
            </div>
            
            <!-- Program and Start Date Section -->
            <h4 class="mt-4 mb-3" style="color: #6f42c1;">Program Information</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="program_name">Program (Read Only)</label>
                        <input type="text" 
                               class="form-control readonly" 
                               id="program_name" 
                               value="{{ $student->program_name ?? 'Not assigned' }}" 
                               readonly>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Start_Date">Start Date</label>
                        <input type="date" 
                               class="form-control" 
                               id="Start_Date" 
                               name="Start_Date" 
                               value="{{ old('Start_Date', isset($student->Start_Date) && $student->Start_Date ? \Carbon\Carbon::parse($student->Start_Date)->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
            
            <!-- Read-only fields -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email (Read Only)</label>
                        <input type="email" 
                               class="form-control readonly" 
                               id="email" 
                               value="{{ $student->email ?? '' }}" 
                               readonly>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="student_id">Student ID (Read Only)</label>
                        <input type="text" 
                               class="form-control readonly" 
                               id="student_id" 
                               value="{{ $student->student_id ?? '' }}" 
                               readonly>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-primary">Update Information</button>
            </div>
        </form>
    </div>
</div>
@endsection
