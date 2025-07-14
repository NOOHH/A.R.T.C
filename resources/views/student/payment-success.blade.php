@extends('student.layouts.master-without-nav')

@section('title')
Payment Successful
@endsection

@section('content')
<div class="account-pages my-5 pt-sm-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card overflow-hidden">
                    <div class="bg-primary bg-soft">
                        <div class="row">
                            <div class="col-7">
                                <div class="text-primary p-4">
                                    <h5 class="text-primary">Payment Successful!</h5>
                                    <p>Your payment has been processed successfully.</p>
                                </div>
                            </div>
                            <div class="col-5 align-self-end">
                                <img src="{{ asset('admin/assets/images/profile-img.png') }}" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="p-2">
                            <div class="text-center mb-4">
                                <div class="avatar-lg mx-auto mb-3">
                                    <span class="avatar-title rounded-circle bg-soft-success text-success h1">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </div>
                                <h4 class="text-success">Payment Completed!</h4>
                                <p class="text-muted">Thank you for your payment. Your enrollment is now being processed.</p>
                            </div>

                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">Payment Details</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Reference Number:</small>
                                            <div class="font-weight-semibold">{{ $reference_number }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Amount Paid:</small>
                                            <div class="font-weight-semibold">₱{{ number_format($amount, 2) }}</div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <small class="text-muted">Date & Time:</small>
                                            <div class="font-weight-semibold">{{ date('M d, Y - h:i A') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-3" role="alert">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <strong>What's Next?</strong><br>
                                        Your payment is being processed. You will receive an email confirmation shortly with your enrollment details and next steps.
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-center">
                                <a href="{{ route('student.dashboard') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                                </a>
                            </div>
                            
                            <div class="mt-2 text-center">
                                <a href="{{ route('unified.login') }}" class="btn btn-link">
                                    <i class="fas fa-home"></i> Back to Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Auto-redirect to dashboard after 10 seconds
    setTimeout(function() {
        window.location.href = "{{ route('student.dashboard') }}";
    }, 10000);
</script>
@endsection
