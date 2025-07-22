@extends('admin.admin-dashboard-layout')

@section('title', 'Payment Pending')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Payment Pending</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.student.registration.pending') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-person-plus"></i> Registration Pending
                    </a>
                    <a href="{{ route('admin.student.registration.payment.history') }}" class="btn btn-outline-info">
                        <i class="bi bi-clock-history"></i> Payment History
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Students with Pending Payments</h6>
                </div>
                <div class="card-body">
                    @if($enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Package</th>
                                        <th>Amount</th>
                                        <th>Enrollment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->student_name }}</td>
                                        <td>{{ $enrollment->student_email }}</td>
                                        <td>{{ $enrollment->program->program_name ?? 'N/A' }}</td>
                                        <td>{{ $enrollment->package->package_name ?? 'N/A' }}</td>
                                        <td>₱{{ number_format($enrollment->package->amount ?? 0, 2) }}</td>
                                        <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                {{ ucfirst($enrollment->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="markAsPaid({{ $enrollment->enrollment_id }})">
                                                    <i class="bi bi-check-circle"></i> Mark Paid
                                                </button>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="viewDetails({{ $enrollment->enrollment_id }})">
                                                    <i class="bi bi-eye"></i> View Details
                                                </button>
                                                <button type="button" class="btn btn-sm btn-secondary" 
                                                        onclick="viewFiles({{ $enrollment->enrollment_id }})">
                                                    <i class="bi bi-folder"></i> Files
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-credit-card-2-front" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="mt-3 text-muted">No Pending Payments</h5>
                            <p class="text-muted">All students have completed their payments.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- File Viewer Modal -->
<div class="modal fade" id="fileViewerModal" tabindex="-1" aria-labelledby="fileViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileViewerModalLabel">Student Uploaded Files</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="filesList">
                    <p class="text-center text-muted">Loading files...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function markAsPaid(enrollmentId) {
    if (confirm('Are you sure you want to mark this payment as completed?')) {
        // Add AJAX call to update payment status
        fetch(`/admin/enrollment/${enrollmentId}/mark-paid`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating payment status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating payment status');
        });
    }
}

function viewDetails(enrollmentId) {
    // Fetch enrollment details via AJAX
    fetch(`/admin/enrollment/${enrollmentId}/details`)
        .then(response => response.json())
        .then(data => {
            // You can implement a detailed view modal here
            alert(`Student: ${data.student_name}\nProgram: ${data.program_name}\nAmount: ₱${data.amount}`);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading enrollment details');
        });
}

function viewFiles(enrollmentId) {
    // Fetch enrollment details including registration files
    fetch(`/admin/enrollment/${enrollmentId}/details`)
        .then(response => response.json())
        .then(data => {
            const filesList = document.getElementById('filesList');
            
            if (data.registration) {
                const files = [
                    { name: 'School ID', field: 'school_id', path: data.registration.school_id },
                    { name: 'Diploma', field: 'diploma', path: data.registration.diploma },
                    { name: 'Transcript of Records (TOR)', field: 'TOR', path: data.registration.TOR },
                    { name: 'PSA Birth Certificate', field: 'psa_birth_certificate', path: data.registration.psa_birth_certificate },
                    { name: 'Form 137', field: 'form_137', path: data.registration.form_137 }
                ];
                
                let filesHtml = `<div class="row">`;
                let hasFiles = false;
                
                files.forEach(file => {
                    if (file.path) {
                        hasFiles = true;
                        filesHtml += `
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">${file.name}</h6>
                                        <div class="d-flex gap-2">
                                            <a href="${file.path}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <a href="${file.path}" download class="btn btn-sm btn-secondary">
                                                <i class="bi bi-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                });
                
                filesHtml += `</div>`;
                
                if (hasFiles) {
                    filesList.innerHTML = filesHtml;
                } else {
                    filesList.innerHTML = `
                        <div class="text-center py-4">
                            <i class="bi bi-folder-x" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="mt-3 text-muted">No Files Uploaded</h5>
                            <p class="text-muted">This student hasn't uploaded any documents yet.</p>
                        </div>
                    `;
                }
            } else {
                filesList.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-person-x" style="font-size: 3rem; color: #6c757d;"></i>
                        <h5 class="mt-3 text-muted">No Registration Data</h5>
                        <p class="text-muted">No registration information found for this enrollment.</p>
                    </div>
                `;
            }
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('fileViewerModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('filesList').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Error loading files. Please try again.
                </div>
            `;
            const modal = new bootstrap.Modal(document.getElementById('fileViewerModal'));
            modal.show();
        });
}
</script>
@endsection
