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
                                                    <i class="bi bi-eye"></i> View
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

<script>
// Functions are now available globally from admin-functions.js
// markAsPaid() and viewDetails() are defined in the shared admin functions file
console.log('✅ Admin payment pending page loaded - functions available globally');
</script>
@endsection
