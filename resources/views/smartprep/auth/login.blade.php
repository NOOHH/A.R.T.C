@extends('layouts.auth')

@section('content')
<div class="col-lg-6">
    <div class="auth-left">
        <div class="auth-left-content">
            <a href="{{ url('/') }}" class="auth-brand">
                <i class="fas fa-graduation-cap"></i>
                SmartPrep
            </a>
            <h3 class="auth-subtitle">Welcome back to your learning platform</h3>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">
                Access your dashboard and continue your educational journey with our comprehensive learning management system.
            </p>
            <ul class="feature-list">
                <li><i class="fas fa-check"></i> Multi-tenant architecture</li>
                <li><i class="fas fa-check"></i> Advanced analytics & reporting</li>
                <li><i class="fas fa-check"></i> Seamless payment integration</li>
                <li><i class="fas fa-check"></i> Professional customization</li>
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="auth-right">
        <div class="auth-form-header">
            <h2>Sign In</h2>
            <p>Enter your credentials to access your account</p>
        </div>

        @php
            $fields = \App\Models\AuthFormField::forForm('login')->orderBy('sort_order')->get();
            $loginIdentifier = \App\Models\AdminSetting::where('setting_key','login_identifier')->value('setting_value') ?? 'email';
            if ($fields->isEmpty()) {
                // sensible defaults if none configured
                $fields = collect([
                    (object)['field_key' => $loginIdentifier, 'label' => ucfirst($loginIdentifier), 'type' => $loginIdentifier==='email'?'email':'text', 'is_required'=>true, 'is_enabled'=>true],
                    (object)['field_key' => 'password', 'label' => 'Password', 'type' => 'password', 'is_required'=>true, 'is_enabled'=>true],
                ]);
            }
        @endphp

    <form method="POST" action="{{ route('smartprep.login.submit') }}">
            @csrf

            @foreach($fields as $f)
                @if(!$f->is_enabled) @continue @endif
                <div class="form-group">
                    <label class="form-label">{{ $f->label }}</label>
                    <input id="{{ $f->field_key }}" type="{{ $f->type }}" class="form-control @error($f->field_key) is-invalid @enderror" name="{{ $f->field_key }}" 
                        value="{{ old($f->field_key) ?? (($f->field_key == 'email' || $f->field_key == 'username') && isset($autoEmail) ? $autoEmail : '') }}" 
                        {{ $f->is_required ? 'required' : '' }} autocomplete="{{ $f->field_key }}" autofocus>
                    @error($f->field_key)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">{{ __('Keep me signed in') }}</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </button>
        </form>

        <div class="auth-links">
            <p class="text-muted mb-3">Don't have an account? <a href="{{ route('smartprep.register') }}">Create one here</a></p>
            @if (Route::has('password.request'))
                <a href="{{ route('smartprep.password.request') }}">Forgot your password?</a>
            @endif
        </div>
    </div>
</div>

@if(isset($autoEmail) && isset($autoPassword))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find the email/username field
            var emailField = document.querySelector('input[name="email"]') || 
                            document.querySelector('input[name="username"]');
            var passwordField = document.querySelector('input[name="password"]');
            
            if (emailField && passwordField) {
                emailField.value = '{{ $autoEmail }}';
                passwordField.value = '{{ $autoPassword }}';
                
                // Auto submit after a short delay
                setTimeout(function() {
                    document.querySelector('form').submit();
                }, 500);
            }
        });
    </script>
@endif

@endsection
