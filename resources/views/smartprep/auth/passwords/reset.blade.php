@extends('layouts.auth')

@section('content')
<div class="col-lg-6">
    <div class="auth-left">
        <div class="auth-left-content">
            <a href="{{ url('/') }}" class="auth-brand">
                <i class="fas fa-graduation-cap"></i>
                SmartPrep
            </a>
            <h3 class="auth-subtitle">Set your new password</h3>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">
                You're almost done! Create a strong new password to secure your account and regain access to your learning platform.
            </p>
            <ul class="feature-list">
                <li><i class="fas fa-key"></i> Strong password protection</li>
                <li><i class="fas fa-user-shield"></i> Account security</li>
                <li><i class="fas fa-check-circle"></i> Instant access restoration</li>
                <li><i class="fas fa-graduation-cap"></i> Continue learning</li>
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="auth-right">
        <div class="auth-form-header">
            <h2>Reset Password</h2>
            <p>Enter your new password below</p>
        </div>

    <form method="POST" action="{{ route('smartprep.password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">New Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key me-2"></i>Reset Password
            </button>
        </form>

        <div class="auth-links">
            <p class="text-muted mb-0">Remember your password? <a href="{{ route('smartprep.login') }}">Sign in here</a></p>
        </div>
    </div>
</div>
@endsection
