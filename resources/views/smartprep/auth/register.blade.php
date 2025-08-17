@extends('layouts.auth')

@section('content')
<div class="col-lg-6">
    <div class="auth-left">
        <div class="auth-left-content">
            <a href="{{ url('/') }}" class="auth-brand">
                <i class="fas fa-graduation-cap"></i>
                SmartPrep
            </a>
            <h3 class="auth-subtitle">Start your educational journey today</h3>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">
                Join thousands of educators and institutions who trust SmartPrep to deliver exceptional learning experiences.
            </p>
            <ul class="feature-list">
                <li><i class="fas fa-rocket"></i> Quick setup & deployment</li>
                <li><i class="fas fa-palette"></i> Full customization control</li>
                <li><i class="fas fa-users-cog"></i> Advanced user management</li>
                <li><i class="fas fa-shield-alt"></i> Enterprise-grade security</li>
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="auth-right">
        <div class="auth-form-header">
            <h2>Create Account</h2>
            <p>Get started with your professional learning platform</p>
        </div>

        @php
            $fields = \App\Models\AuthFormField::forForm('register')->orderBy('sort_order')->get();
            if ($fields->isEmpty()) {
                $fields = collect([
                    (object)['field_key' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required'=>true, 'is_enabled'=>true],
                    (object)['field_key' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required'=>true, 'is_enabled'=>true],
                    (object)['field_key' => 'password', 'label' => 'Password', 'type' => 'password', 'is_required'=>true, 'is_enabled'=>true],
                    (object)['field_key' => 'password_confirmation', 'label' => 'Confirm Password', 'type' => 'password', 'is_required'=>true, 'is_enabled'=>true],
                ]);
            }
        @endphp

    <form method="POST" action="{{ route('smartprep.register.submit') }}">
            @csrf

            @foreach($fields as $f)
                @if(!$f->is_enabled) @continue @endif
                <div class="form-group">
                    <label class="form-label">{{ $f->label }}</label>
                    <input id="{{ $f->field_key }}" type="{{ $f->type }}" class="form-control @error($f->field_key) is-invalid @enderror" name="{{ $f->field_key }}" value="{{ old($f->field_key) }}" {{ $f->is_required ? 'required' : '' }} autocomplete="{{ $f->field_key }}">
                    @error($f->field_key)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" style="color: var(--primary-color); text-decoration: none;">Terms of Service</a> and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" style="color: var(--primary-color); text-decoration: none;">Privacy Policy</a>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Create Account
            </button>
        </form>

        <div class="auth-links">
            <p class="text-muted mb-0">Already have an account? <a href="{{ route('smartprep.login') }}">Sign in here</a></p>
        </div>
    </div>
</div>
@endsection
