@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'Student Settings')

@push('styles')
<style>
.settings-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.settings-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 2rem;
    margin-bottom: 2rem;
}

.settings-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 1rem;
    margin-bottom: 2rem;
}

.settings-header h2 {
    color: #2c3e50;
    font-weight: 600;
    margin: 0;
}

.info-group {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f3f4;
}

.info-group:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.info-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    display: block;
}

.info-value {
    color: #6c757d;
    font-size: 1rem;
    background: #f8f9fa;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.section-title {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.2rem;
    margin: 2rem 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #3498db;
}

.badge-status {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.badge-approved {
    background: #d4edda;
    color: #155724;
}

.badge-pending {
    background: #fff3cd;
    color: #856404;
}

.badge-inactive {
    background: #f8d7da;
    color: #721c24;
}

@media (max-width: 768px) {
    .settings-container {
        padding: 1rem;
    }
    
    .settings-card {
        padding: 1.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="settings-container">
    <div class="settings-card">
        <div class="settings-header">
            <h2><i class="bi bi-person-circle me-2"></i>Student Profile Settings</h2>
        </div>

        {{-- Personal Information --}}
        <div class="section-title">
            <i class="bi bi-person me-2"></i>Personal Information
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="info-group">
                    <label class="info-label">First Name</label>
                    <div class="info-value">{{ $student->firstname ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-group">
                    <label class="info-label">Middle Name</label>
                    <div class="info-value">{{ $student->middlename ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-group">
                    <label class="info-label">Last Name</label>
                    <div class="info-value">{{ $student->lastname ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Student ID</label>
                    <div class="info-value">{{ $student->student_id ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Email Address</label>
                    <div class="info-value">{{ $student->email ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        {{-- Contact Information --}}
        <div class="section-title">
            <i class="bi bi-telephone me-2"></i>Contact Information
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Contact Number</label>
                    <div class="info-value">{{ $student->contact_number ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Emergency Contact</label>
                    <div class="info-value">{{ $student->emergency_contact_number ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        {{-- Address Information --}}
        <div class="section-title">
            <i class="bi bi-geo-alt me-2"></i>Address Information
        </div>
        
        <div class="info-group">
            <label class="info-label">Street Address</label>
            <div class="info-value">{{ $student->street_address ?? 'N/A' }}</div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="info-group">
                    <label class="info-label">City</label>
                    <div class="info-value">{{ $student->city ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-group">
                    <label class="info-label">State/Province</label>
                    <div class="info-value">{{ $student->state_province ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-group">
                    <label class="info-label">Zip Code</label>
                    <div class="info-value">{{ $student->zipcode ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        {{-- Academic Information --}}
        <div class="section-title">
            <i class="bi bi-mortarboard me-2"></i>Academic Information
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">School</label>
                    <div class="info-value">{{ $student->student_school ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Program</label>
                    <div class="info-value">{{ $student->program_name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Package</label>
                    <div class="info-value">{{ $student->package_name ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Plan</label>
                    <div class="info-value">{{ $student->plan_name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        {{-- Enrollment Status --}}
        <div class="section-title">
            <i class="bi bi-clipboard-check me-2"></i>Enrollment Status
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Start Date</label>
                    <div class="info-value">{{ $student->Start_Date ? \Carbon\Carbon::parse($student->Start_Date)->format('M d, Y') : 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Date Approved</label>
                    <div class="info-value">
                        @if($student->date_approved)
                            {{ \Carbon\Carbon::parse($student->date_approved)->format('M d, Y') }}
                            <span class="badge badge-approved ms-2">Approved</span>
                        @else
                            <span class="badge badge-pending">Pending Approval</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Document Status --}}
        <div class="section-title">
            <i class="bi bi-file-earmark-check me-2"></i>Document Status
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Good Moral Certificate</label>
                    <div class="info-value">
                        @if($student->good_moral)
                            <span class="badge badge-approved">Submitted</span>
                        @else
                            <span class="badge badge-pending">Not Submitted</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">PSA Birth Certificate</label>
                    <div class="info-value">
                        @if($student->PSA)
                            <span class="badge badge-approved">Submitted</span>
                        @else
                            <span class="badge badge-pending">Not Submitted</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Course Certificate</label>
                    <div class="info-value">
                        @if($student->Course_Cert)
                            <span class="badge badge-approved">Submitted</span>
                        @else
                            <span class="badge badge-pending">Not Submitted</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Transcript of Records (TOR)</label>
                    <div class="info-value">
                        @if($student->TOR)
                            <span class="badge badge-approved">Submitted</span>
                        @else
                            <span class="badge badge-pending">Not Submitted</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">Certificate of Graduation</label>
                    <div class="info-value">
                        @if($student->Cert_of_Grad)
                            <span class="badge badge-approved">Submitted</span>
                        @else
                            <span class="badge badge-pending">Not Submitted</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">2x2 Photo</label>
                    <div class="info-value">
                        @if($student->photo_2x2)
                            <span class="badge badge-approved">Submitted</span>
                        @else
                            <span class="badge badge-pending">Not Submitted</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
