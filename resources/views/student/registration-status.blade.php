@extends('layouts.student')

@section('title', 'Registration Status')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Registration Status</h3>
                </div>
                
                <div class="card-body">
                    @if($registration->status === 'rejected')
                        <div class="alert alert-danger">
                            <h4><i class="fas fa-exclamation-triangle"></i> Registration Rejected</h4>
                            <p>Your registration has been rejected. Please review the issues below and resubmit with corrections.</p>
                        </div>
                        
                        @if($latestRejection)
                            <div class="card mb-4">
                                <div class="card-header bg-danger text-white">
                                    <h5>Rejection Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Rejection Date:</strong> {{ $latestRejection->created_at->format('M d, Y g:i A') }}</p>
                                    <p><strong>Reason:</strong> {{ $latestRejection->reason }}</p>
                                    
                                    @if($latestRejection->rejected_fields && is_array($latestRejection->rejected_fields))
                                        <p><strong>Fields that need correction:</strong></p>
                                        <ul class="list-group list-group-flush">
                                            @foreach($latestRejection->rejected_fields as $field)
                                                <li class="list-group-item list-group-item-danger">
                                                    <i class="fas fa-times text-danger"></i> {{ ucfirst(str_replace('_', ' ', $field)) }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <div class="text-center">
                            <a href="{{ route('student.registration.resubmit', $registration->id) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-edit"></i> Resubmit Registration
                            </a>
                            <button type="button" class="btn btn-secondary btn-lg ms-2" onclick="showAbortModal()">
                                <i class="fas fa-ban"></i> Abort Process
                            </button>
                        </div>
                        
                    @elseif($registration->status === 'resubmitted')
                        <div class="alert alert-info">
                            <h4><i class="fas fa-clock"></i> Resubmission Under Review</h4>
                            <p>Thank you for resubmitting your registration. It is currently being reviewed by our admissions team.</p>
                            <p><strong>Resubmitted on:</strong> {{ $registration->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                        
                        <div class="text-center">
                            <button type="button" class="btn btn-secondary" onclick="showAbortModal()">
                                <i class="fas fa-ban"></i> Abort Process
                            </button>
                        </div>
                        
                    @elseif($registration->status === 'pending')
                        <div class="alert alert-warning">
                            <h4><i class="fas fa-clock"></i> Registration Pending</h4>
                            <p>Your registration is currently being reviewed by our admissions team.</p>
                            <p><strong>Submitted on:</strong> {{ $registration->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                        
                        <div class="text-center">
                            <button type="button" class="btn btn-secondary" onclick="showAbortModal()">
                                <i class="fas fa-ban"></i> Abort Process
                            </button>
                        </div>
                        
                    @elseif($registration->status === 'approved')
                        <div class="alert alert-success">
                            <h4><i class="fas fa-check-circle"></i> Registration Approved</h4>
                            <p>Congratulations! Your registration has been approved.</p>
                            <p><strong>Approved on:</strong> {{ $registration->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                        
                        <div class="text-center">
                            <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                            </a>
                        </div>
                    @endif
                    
                    <!-- Registration Details -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Your Registration Details</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr class="{{ in_array('first_name', $rejectedFields ?? []) ? 'table-danger' : '' }}">
                                        <td><strong>First Name:</strong></td>
                                        <td>
                                            {{ $registration->first_name }}
                                            @if(in_array('first_name', $rejectedFields ?? []))
                                                <i class="fas fa-exclamation-triangle text-danger ms-2" title="This field needs correction"></i>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="{{ in_array('last_name', $rejectedFields ?? []) ? 'table-danger' : '' }}">
                                        <td><strong>Last Name:</strong></td>
                                        <td>
                                            {{ $registration->last_name }}
                                            @if(in_array('last_name', $rejectedFields ?? []))
                                                <i class="fas fa-exclamation-triangle text-danger ms-2" title="This field needs correction"></i>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="{{ in_array('email', $rejectedFields ?? []) ? 'table-danger' : '' }}">
                                        <td><strong>Email:</strong></td>
                                        <td>
                                            {{ $registration->email }}
                                            @if(in_array('email', $rejectedFields ?? []))
                                                <i class="fas fa-exclamation-triangle text-danger ms-2" title="This field needs correction"></i>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="{{ in_array('phone', $rejectedFields ?? []) ? 'table-danger' : '' }}">
                                        <td><strong>Phone:</strong></td>
                                        <td>
                                            {{ $registration->phone }}
                                            @if(in_array('phone', $rejectedFields ?? []))
                                                <i class="fas fa-exclamation-triangle text-danger ms-2" title="This field needs correction"></i>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="{{ in_array('education_level', $rejectedFields ?? []) ? 'table-danger' : '' }}">
                                        <td><strong>Education Level:</strong></td>
                                        <td>
                                            {{ $registration->education_level }}
                                            @if(in_array('education_level', $rejectedFields ?? []))
                                                <i class="fas fa-exclamation-triangle text-danger ms-2" title="This field needs correction"></i>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="{{ in_array('course', $rejectedFields ?? []) ? 'table-danger' : '' }}">
                                        <td><strong>Course:</strong></td>
                                        <td>
                                            {{ $registration->course }}
                                            @if(in_array('course', $rejectedFields ?? []))
                                                <i class="fas fa-exclamation-triangle text-danger ms-2" title="This field needs correction"></i>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    @if($registration->rejections && $registration->rejections->count() > 1)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Rejection History</h5>
                            <div class="accordion" id="rejectionHistory">
                                @foreach($registration->rejections as $index => $rejection)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rejection{{ $index }}">
                                                Rejection #{{ $loop->iteration }} - {{ $rejection->created_at->format('M d, Y') }}
                                            </button>
                                        </h2>
                                        <div id="rejection{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#rejectionHistory">
                                            <div class="accordion-body">
                                                <p><strong>Reason:</strong> {{ $rejection->reason }}</p>
                                                @if($rejection->rejected_fields && is_array($rejection->rejected_fields))
                                                    <p><strong>Rejected Fields:</strong></p>
                                                    <ul>
                                                        @foreach($rejection->rejected_fields as $field)
                                                            <li>{{ ucfirst(str_replace('_', ' ', $field)) }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Abort Confirmation Modal -->
<div class="modal fade" id="abortModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Abort Registration Process</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone.
                </div>
                <p>Are you sure you want to abort your registration process? This will:</p>
                <ul>
                    <li>Cancel your current registration</li>
                    <li>Remove all submitted information</li>
                    <li>Require you to start over if you wish to register again</li>
                </ul>
                <p><strong>Please confirm by typing "ABORT" below:</strong></p>
                <input type="text" class="form-control" id="abortConfirmation" placeholder="Type ABORT to confirm">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmAbortBtn" disabled onclick="abortRegistration()">
                    <i class="fas fa-ban"></i> Abort Registration
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showAbortModal() {
    new bootstrap.Modal(document.getElementById('abortModal')).show();
}

// Enable abort button only when user types "ABORT"
document.getElementById('abortConfirmation').addEventListener('input', function() {
    const confirmBtn = document.getElementById('confirmAbortBtn');
    if (this.value.toUpperCase() === 'ABORT') {
        confirmBtn.disabled = false;
    } else {
        confirmBtn.disabled = true;
    }
});

function abortRegistration() {
    const confirmation = document.getElementById('abortConfirmation').value;
    if (confirmation.toUpperCase() !== 'ABORT') {
        alert('Please type "ABORT" to confirm');
        return;
    }
    
    fetch(`/student/registration/{{ $registration->id }}/abort`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/student/dashboard';
        } else {
            alert('Error aborting registration: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error aborting registration');
    });
}
</script>
@endsection
