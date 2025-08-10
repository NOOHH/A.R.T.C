@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Certificate Management')

@section('content')
<div class="container py-4">
    <h1 class="mb-4"><i class="fas fa-certificate me-2"></i>Certificate Management</h1>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Student Certificate Access:</strong> Students can view and download their certificates directly from their dashboard once their enrollment is approved and completed.
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Generate Certificate for Specific Student</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('certificate.show') }}" target="_blank" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="student_select" class="form-label">Select Student</label>
                    <select class="form-select" id="student_select" name="user_id" required>
                        <option value="">-- Select Student --</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->user_id }}">
                                {{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }} 
                                ({{ $student->email ?? $student->user->email ?? 'No email' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="enrollment_select" class="form-label">Select Enrollment</label>
                    <select class="form-select" id="enrollment_select" name="enrollment_id">
                        <option value="">-- Select Student First --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-eye me-1"></i>Preview Certificate
                        </button>
                        <button type="submit" formaction="{{ route('certificate.download') }}" class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>Download PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Enrollments Eligible for Certificates</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Program</th>
                            <th>Enrollment Type</th>
                            <th>Status</th>
                            <th>Completion Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eligibleEnrollments ?? [] as $enrollment)
                            <tr>
                                <td>
                                    @if($enrollment->student)
                                        {{ $enrollment->student->firstname }} {{ $enrollment->student->middlename }} {{ $enrollment->student->lastname }}
                                    @elseif($enrollment->user)
                                        {{ $enrollment->user->user_firstname }} {{ $enrollment->user->user_lastname }}
                                    @else
                                        Unknown Student
                                    @endif
                                </td>
                                <td>{{ $enrollment->program->program_name ?? 'Unknown Program' }}</td>
                                <td>
                                    <span class="badge bg-{{ $enrollment->enrollment_type === 'Modular' ? 'info' : 'primary' }}">
                                        {{ $enrollment->enrollment_type }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $enrollment->enrollment_status === 'approved' ? 'success' : 'warning' }}">
                                        {{ ucfirst($enrollment->enrollment_status) }}
                                    </span>
                                </td>
                                <td>{{ $enrollment->completed_at ? $enrollment->completed_at->format('M d, Y') : 'In Progress' }}</td>
                                <td>
                                    @if($enrollment->enrollment_status === 'approved')
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('certificate.show', ['user_id' => $enrollment->user_id, 'enrollment_id' => $enrollment->enrollment_id]) }}" 
                                               class="btn btn-outline-success" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('certificate.download', ['user_id' => $enrollment->user_id, 'enrollment_id' => $enrollment->enrollment_id]) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    @else
                                        <small class="text-muted">Not eligible</small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No eligible enrollments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('student_select').addEventListener('change', function() {
    const userId = this.value;
    const enrollmentSelect = document.getElementById('enrollment_select');
    
    // Clear enrollment options
    enrollmentSelect.innerHTML = '<option value="">Loading...</option>';
    
    if (!userId) {
        enrollmentSelect.innerHTML = '<option value="">-- Select Student First --</option>';
        return;
    }
    
    // Fetch enrollments for selected student
    fetch(`/admin/student-enrollments/${userId}`)
        .then(response => response.json())
        .then(data => {
            enrollmentSelect.innerHTML = '<option value="">-- Select Enrollment --</option>';
            
            if (data.success && data.enrollments) {
                data.enrollments.forEach(enrollment => {
                    const option = document.createElement('option');
                    option.value = enrollment.enrollment_id;
                    option.textContent = `${enrollment.program_name} - ${enrollment.enrollment_type} (${enrollment.enrollment_status})`;
                    enrollmentSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching enrollments:', error);
            enrollmentSelect.innerHTML = '<option value="">Error loading enrollments</option>';
        });
});
</script>
@endsection 
