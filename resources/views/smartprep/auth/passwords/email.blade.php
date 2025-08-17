@extends('layouts.auth')

@section('content')
<div class="col-lg-6">
    <div class="auth-left">
        <div class="auth-left-content">
            <a href="{{ url('/') }}" class="auth-brand">
                <i class="fas fa-graduation-cap"></i>
                SmartPrep
            </a>
            <h3 class="auth-subtitle">Reset your password</h3>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">
                Don't worry! It happens to the best of us. Enter your email address and we'll send you a link to reset your password.
            </p>
            <ul class="feature-list">
                <li><i class="fas fa-shield-alt"></i> Secure password recovery</li>
                <li><i class="fas fa-clock"></i> Quick and easy process</li>
                <li><i class="fas fa-envelope"></i> Email verification</li>
                <li><i class="fas fa-lock"></i> Enhanced security</li>
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="auth-right">
        <div class="auth-form-header">
            <h2>Reset Password</h2>
            <p>Enter your email address to receive a password reset link</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert" style="border-radius: 12px; border: none; background: rgba(5, 150, 105, 0.1); color: #059669; margin-bottom: 30px;">
                <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
            </div>
        @endif

    <form method="POST" action="{{ route('smartprep.password.email') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Send Password Reset Link
            </button>
        </form>

        <div class="auth-links">
            <p class="text-muted mb-0">Remember your password? <a href="{{ route('smartprep.login') }}">Sign in here</a></p>
        </div>
    </div>
</div>
@endsection
