@extends('admin.admin-dashboard-layout')

@section('title', 'Payment History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Payment History</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.student.registration.payment.pending') }}" class="btn btn-outline-warning">
                        <i class="bi bi-clock"></i> Payment Pending
                    </a>
                    <a href="{{ route('admin.student.registration.history') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-archive"></i> Registration History
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Transaction History</h6>
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
                                        <th>Payment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td>
                                            @if($enrollment->student && $enrollment->student->user)
                                                {{ $enrollment->student->user->user_name ?? ($enrollment->student->firstname . ' ' . $enrollment->student->lastname) }}
                                            @elseif($enrollment->user)
                                                {{ $enrollment->user->user_name }}
                                            @elseif($enrollment->student)
                                                {{ $enrollment->student->firstname ?? 'N/A' }} {{ $enrollment->student->lastname ?? '' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($enrollment->student && $enrollment->student->user)
                                                {{ $enrollment->student->user->user_email ?? $enrollment->student->email }}
                                            @elseif($enrollment->user)
                                                {{ $enrollment->user->user_email }}
                                            @elseif($enrollment->student)
                                                {{ $enrollment->student->email ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $enrollment->program->program_name ?? 'N/A' }}</td>
                                        <td>{{ $enrollment->package->package_name ?? 'N/A' }}</td>
                                        <td>₱{{ number_format($enrollment->package->package_price ?? $enrollment->package->amount ?? 0, 2) }}</td>
                                        <td>{{ $enrollment->created_at->format('M d, Y h:i A') }}</td>
                                        <td>{{ $enrollment->updated_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            @if($enrollment->payment_status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($enrollment->payment_status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($enrollment->payment_status === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @elseif($enrollment->payment_status === 'cancelled')
                                                <span class="badge bg-secondary">Cancelled</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ ucfirst($enrollment->payment_status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="viewDetails({{ $enrollment->enrollment_id }})">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                @if($enrollment->payment_status === 'failed')
                                                    <button type="button" class="btn btn-sm btn-warning" 
                                                            onclick="retryPayment({{ $enrollment->enrollment_id }})">
                                                        <i class="bi bi-arrow-clockwise"></i> Retry
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-receipt" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="mt-3 text-muted">No Payment History</h5>
                            <p class="text-muted">No payment transactions have been completed yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Functions are now available globally from admin-functions.js
// viewDetails() and retryPayment() are defined in the shared admin functions file
console.log('✅ Admin payment history page loaded - functions available globally');
</script>
@endsection
