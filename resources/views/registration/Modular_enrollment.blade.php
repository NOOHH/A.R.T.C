@extends('layouts.navbar')

@section('title', 'Modular Enrollment - Multi-Step Form')
@section('hide_footer', true)
@section('body_class', 'registration-page')

@php
    // Check if user is already logged in
    $isUserLoggedIn = auth()->check() || session('user_id');
    $loggedInUser = auth()->check() ? auth()->user() : (session('user_id') ? \App\Models\User::find(session('user_id')) : null);
@endphp

@push('styles')
    {!! App\Helpers\UIHelper::getNavbarStyles() !!}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Multi-step form styles */
        .form-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 0;
        }
        
        .form-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
        }
        
        /* Stepper styles */
        .stepper-progress {
            background: #f8f9fa;
            padding: 2rem;
            border-bottom: 1px solid #e9ecef;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .stepper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        
        .stepper .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
            background: white;
            padding: 0.5rem;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            transition: all 0.3s ease;
        }
        
        /* Step Content Container - Bootstrap 5 Compatible */
        .step-content {
            display: none;
            padding: 2rem;
            min-height: 600px;
            opacity: 0;
            transform: translateX(50px);
            transition: all 0.4s ease;
        }
        
        .step-content.active {
            display: block;
            opacity: 1;
            transform: translateX(0);
        }
        
        /* Bootstrap Grid Enhancements */
        .row {
            margin-left: -0.75rem;
            margin-right: -0.75rem;
        }
        
        .col, .col-md-6, .col-lg-4, .col-xl-3 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        
        /* Card Layout Consistency */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .selection-card {
            cursor: pointer;
            border: 2px solid transparent;
            position: relative;
        }
        
        .selection-card.selected {
            border-color: #6a82fb;
            background: linear-gradient(135deg, #6a82fb 0%, #fc466b 100%);
            color: white;
        }
        
        .selection-card.selected::after {
            content: '✓';
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 30px;
            height: 30px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6a82fb;
            font-weight: bold;
            font-size: 1.2rem;
        }
            position: relative;
        }
        
        .stepper .step.active {
            background: #6a82fb;
            color: white;
        }
        
        .stepper .step.completed {
            background: #28a745;
            color: white;
        }
        
        .stepper .step .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .stepper .step .label {
            font-size: 0.8rem;
            margin-top: 0.5rem;
            text-align: center;
            font-weight: 500;
        }
        
        .stepper .bar {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 4px;
            background: #e9ecef;
            transform: translateY(-50%);
            z-index: 1;
        }
        
        .stepper .bar .progress {
            height: 100%;
            background: #6a82fb;
            transition: width 0.3s ease;
        }
        
        /* Step content styles */
        .step-content {
            display: none;
            padding: 3rem;
            min-height: 500px;
        }
        
        .step-content.active {
            display: block;
        }
        
        .step-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .step-header h2 {
            color: #333;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .step-header p {
            color: #666;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Card grid styles */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .selection-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .selection-card:hover {
            border-color: #6a82fb;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(106, 130, 251, 0.2);
        }
        
        .selection-card.selected {
            border-color: #6a82fb;
            background: linear-gradient(135deg, #6a82fb 0%, #8b5cf6 100%);
            color: white;
        }
        
        .selection-card.selected::before {
            content: '✓';
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: white;
            color: #6a82fb;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .card-header {
            margin-bottom: 1.5rem;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .card-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #6a82fb;
        }
        
        .selection-card.selected .card-price {
            color: white;
        }
        
        .card-description {
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .card-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .card-features li {
            padding: 0.5rem 0;
            position: relative;
            padding-left: 1.5rem;
        }
        
        .card-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }
        
        .selection-card.selected .card-features li::before {
            color: white;
        }
        
        /* Navigation buttons */
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }
        
        .btn-nav {
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: #6a82fb;
            color: white;
        }
        
        .btn-primary:hover:not(:disabled) {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        /* Loading states */
        .loading-spinner {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: #6a82fb;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Hidden inputs */
        .hidden-inputs {
            display: none;
        }

        .package-card-pro {
            border: 2px solid #e9ecef;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(106,130,251,0.08);
            transition: box-shadow 0.2s, border-color 0.2s, transform 0.2s;
            cursor: pointer;
            min-width: 270px;
            max-width: 320px;
            background: #fff;
            position: relative;
        }
        .package-card-pro:hover, .package-card-pro.selected {
            border-color: #6a82fb;
            box-shadow: 0 8px 32px rgba(106,130,251,0.18);
            transform: translateY(-4px) scale(1.03);
        }
        .package-card-pro.selected::after {
            content: '';
            position: absolute;
            top: 12px; right: 12px;
            width: 22px; height: 22px;
            background: #6a82fb;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #6a82fb;
        }
        .next-btn-pro {
            min-width: 320px;
            font-size: 1.15rem;
            font-weight: 600;
            box-shadow: 0 2px 12px rgba(106,130,251,0.10);
            border-radius: 12px;
            letter-spacing: 0.03em;
        }

        /* Account Step Card */
        .account-step-card {
            padding: 3rem;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Form Grid for Step 5 */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Email and Referral Input Groups */
        .email-input-group, .referral-input-group {
            display: flex;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            overflow: hidden;
            transition: border-color 0.3s ease;
        }

        .email-input-group:focus-within, .referral-input-group:focus-within {
            border-color: #6a82fb;
        }

        .email-input-group input, .referral-input-group input {
            flex: 1;
            border: none;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            outline: none;
            background: transparent;
        }

        .btn-otp, .btn-validate-referral {
            background: #6a82fb;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            white-space: nowrap;
        }

        .btn-otp:hover, .btn-validate-referral:hover {
            background: #5a6fd8;
        }

        .btn-otp:disabled, .btn-validate-referral:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Error and Success Messages */
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .success-message {
            color: #28a745;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Login Prompt */
        .login-prompt {
            text-align: center;
            margin: 2rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .login-prompt a {
            color: #6a82fb;
            text-decoration: none;
            font-weight: 600;
        }

        .login-prompt a:hover {
            text-decoration: underline;
        }

        /* Form Navigation */
        .form-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }

        @media (max-width: 600px) {
            .package-card-pro { min-width: 90vw; max-width: 98vw; }
            .next-btn-pro { min-width: 90vw; }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .account-step-card {
                padding: 2rem 1rem;
            }
        }
    </style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-wrapper">
        <!-- Stepper Progress -->
        <div class="stepper-progress">
            <div class="stepper">
                <div class="bar">
                    <div class="progress" id="progressBar" style="width: 16.67%;"></div>
                </div>
                <div class="step active" id="step-1">
                    <div class="circle">1</div>
                    <div class="label">Packages</div>
                </div>
                <div class="step" id="step-2">
                    <div class="circle">2</div>
                    <div class="label">Programs</div>
                </div>
                <div class="step" id="step-3">
                    <div class="circle">3</div>
                    <div class="label">Modules</div>
                </div>
                <div class="step" id="step-4">
                    <div class="circle">4</div>
                    <div class="label">Learning Mode</div>
                </div>
                @if(!$isUserLoggedIn)
                <div class="step" id="step-5">
                    <div class="circle">5</div>
                    <div class="label">Account</div>
                </div>
                <div class="step" id="step-6">
                    <div class="circle">6</div>
                    <div class="label">Form</div>
                </div>
                @else
                <div class="step" id="step-5">
                    <div class="circle">5</div>
                    <div class="label">Form</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Hidden inputs for form data -->
        <div class="hidden-inputs">
            <input type="hidden" id="package_id" name="package_id" value="">
            <input type="hidden" id="program_id" name="program_id" value="">
            <input type="hidden" id="selected_modules" name="selected_modules" value="">
            <input type="hidden" id="learning_mode" name="learning_mode" value="">
        </div>

        <!-- Step 1: Package Selection (Bootstrap Cards) -->
        <div class="step-content active" id="content-1">
            <div class="step-header mb-4">
                <h2 class="fw-bold text-center" style="font-size:2.5rem;">Choose Your Package</h2>
                <p class="text-center text-muted" style="font-size:1.15rem;">Select a learning package that suits your needs</p>
            </div>
            <div class="d-flex justify-content-center gap-4 flex-wrap mb-5">
                @foreach($packages as $package)
                    <div class="package-card-pro card p-4 mb-3"
                         onclick="selectPackage({{ $package->package_id }}, {{ $package->program_id }}, {{ $package->modules_count ?? 3 }})"
                         data-package-id="{{ $package->package_id }}">
                        <div class="card-body text-center">
                            <h4 class="fw-bold mb-2">{{ $package->package_name }}</h4>
                            <div class="text-primary fw-bold" style="font-size:2rem;">₱{{ number_format($package->amount, 2) }}</div>
                            <p class="text-muted mb-3" style="min-height:2rem;">{{ $package->description ?? 'No description yet.' }}</p>
                            <ul class="list-unstyled text-start mx-auto" style="max-width:220px;">
                                <li><i class="bi bi-check2 text-success"></i> {{ $package->modules_count ?? 3 }} modules included</li>
                                <li><i class="bi bi-check2 text-success"></i> Self-paced learning</li>
                                <li><i class="bi bi-check2 text-success"></i> Certificate upon completion</li>
                                <li><i class="bi bi-check2 text-success"></i> Flexible scheduling</li>
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-4">
                <button type="button" class="btn btn-lg btn-primary next-btn-pro" onclick="nextStep()" disabled id="step1-next">
                    NEXT: SELECT PROGRAM <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Program Selection (unchanged, but use Bootstrap grid) -->
        <div class="step-content" id="content-2">
            <div class="step-header">
                <h2>Select Your Program</h2>
                <p>Choose the program that aligns with your career goals</p>
            </div>
            <div class="row" id="programsGrid">
                <!-- Programs will be loaded here -->
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="prevStep()">
                    <i class="bi bi-arrow-left me-2"></i> Previous
                </button>
                <button type="button" class="btn btn-primary btn-lg" onclick="nextStep()" disabled id="step2-next">
                    NEXT: SELECT MODULES <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Module Selection (unchanged, but use Bootstrap grid) -->
        <div class="step-content" id="content-3">
            <div class="step-header">
                <h2>Select Your Modules</h2>
                <p>Choose the modules you want to enroll in (up to <span id="moduleLimit">3</span> modules)</p>
            </div>
            <div class="row" id="modulesGrid">
                <!-- Modules will be loaded here -->
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="prevStep()">
                    <i class="bi bi-arrow-left me-2"></i> Previous
                </button>
                <button type="button" class="btn btn-primary btn-lg" onclick="nextStep()" disabled id="step3-next">
                    NEXT: LEARNING MODE <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 4: Learning Mode Selection (Bootstrap cards) -->
        <div class="step-content" id="content-4">
            <div class="step-header">
                <h2>Choose Learning Mode</h2>
                <p>Select how you'd like to take your classes</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-5 mb-4">
                    <div class="card selection-card h-100" onclick="selectLearningMode('synchronous')" style="cursor:pointer;">
                        <div class="card-body">
                            <h4 class="card-title">Synchronous Learning</h4>
                            <p class="card-text">Learn in real-time with live classes and instructor interaction</p>
                            <ul class="list-unstyled mt-3 mb-0">
                                <li><i class="bi bi-check2 text-success"></i> Live online classes</li>
                                <li><i class="bi bi-check2 text-success"></i> Real-time instructor interaction</li>
                                <li><i class="bi bi-check2 text-success"></i> Structured schedule</li>
                                <li><i class="bi bi-check2 text-success"></i> Group discussions</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mb-4">
                    <div class="card selection-card h-100" onclick="selectLearningMode('asynchronous')" style="cursor:pointer;">
                        <div class="card-body">
                            <h4 class="card-title">Asynchronous Learning</h4>
                            <p class="card-text">Learn at your own pace with pre-recorded materials</p>
                            <ul class="list-unstyled mt-3 mb-0">
                                <li><i class="bi bi-check2 text-success"></i> Self-paced learning</li>
                                <li><i class="bi bi-check2 text-success"></i> Pre-recorded materials</li>
                                <li><i class="bi bi-check2 text-success"></i> Flexible schedule</li>
                                <li><i class="bi bi-check2 text-success"></i> 24/7 access</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="prevStep()">
                    <i class="bi bi-arrow-left me-2"></i> Previous
                </button>
                <button type="button" class="btn btn-primary btn-lg" onclick="nextStep()" disabled id="step4-next">
                    NEXT: ACCOUNT <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 5: Account Registration (Enhanced with OTP and Referral validation) - Only show for non-logged-in users -->
        @if(!$isUserLoggedIn)
        <div class="step-content" id="content-5">
            <div class="account-step-card">
                <div class="step-header">
                    <h2><i class="bi bi-person-plus me-2"></i>Create Your Account</h2>
                    <p>Please provide your account information to continue.</p>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="user_firstname">First Name</label>
                        <input type="text" id="user_firstname" name="user_firstname" class="form-control" required>
                        <div id="user_firstnameError" class="error-message" style="display: none;"></div>
                    </div>
                    <div class="form-group">
                        <label for="user_lastname">Last Name</label>
                        <input type="text" id="user_lastname" name="user_lastname" class="form-control" required>
                        <div id="user_lastnameError" class="error-message" style="display: none;"></div>
                    </div>
                    <div class="form-group" style="grid-column: 1 / span 2;">
                        <label for="user_email">Email Address</label>
                        <div class="email-input-group">
                            <input type="email" id="user_email" name="email" class="form-control" required>
                            <button type="button" id="sendOtpBtn" class="btn-otp" onclick="sendEnrollmentOTP()">
                                <i class="fas fa-paper-plane"></i> Send OTP
                            </button>
                        </div>
                        <div id="emailError" class="error-message" style="display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <div id="passwordError" class="error-message" style="display: none;"></div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        <div id="passwordMatchError" class="error-message" style="display: none;"></div>
                    </div>
                    
                    @if(DB::table('admin_settings')->where('setting_key', 'referral_enabled')->value('setting_value') === '1')
                    <div class="form-group" style="grid-column: 1 / span 2;">
                        <label for="referral_code">Referral Code @if(DB::table('admin_settings')->where('setting_key', 'referral_required')->value('setting_value') === '1') <span class="text-danger">*</span> @endif</label>
                        <div class="referral-input-group">
                            <input type="text" id="referral_code" name="referral_code" class="form-control" 
                                   placeholder="Enter referral code (e.g., PROF01JDOE or DIR01SMITH)"
                                   @if(DB::table('admin_settings')->where('setting_key', 'referral_required')->value('setting_value') === '1') required @endif>
                            <button type="button" id="validateReferralBtn" class="btn-validate-referral" onclick="validateReferralCode()">
                                <i class="fas fa-check"></i> Validate
                            </button>
                        </div>
                        <div id="referralCodeError" class="error-message" style="display: none;"></div>
                        <div id="referralCodeSuccess" class="success-message" style="display: none;"></div>
                        <div class="form-text">@if(DB::table('admin_settings')->where('setting_key', 'referral_required')->value('setting_value') === '1') Required: @endif Enter the referral code provided by your professor or director</div>
                    </div>
                    @endif
                </div>

                <div class="login-prompt">
                    <p>Already have an account? <a href="{{ route('login') }}">Click here to login</a></p>
                </div>

                <div class="form-navigation">
                    <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i> Back
                    </button>
                    <button type="button" onclick="nextStep()" id="step5NextBtn" disabled class="btn btn-primary btn-lg">
                        Next <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Step 6: Final Registration Form (for non-logged-in users) OR Step 5: Final Registration Form (for logged-in users) -->
        <div class="step-content" id="content-{{ $isUserLoggedIn ? '5' : '6' }}">
            <div class="step-header">
                <h2>Complete Your Registration</h2>
                @if($isUserLoggedIn)
                <p>Welcome back, {{ $loggedInUser->user_firstname ?? 'User' }}! Complete your modular enrollment below.</p>
                @else
                <p>Fill in your personal and academic information.</p>
                @endif
            </div>
            <form action="{{ route('enrollment.modular.submit') }}" method="POST" enctype="multipart/form-data" class="registration-form" id="modularEnrollmentForm" novalidate>
                @csrf
                <!-- Hidden inputs for form data -->
                <input type="hidden" name="enrollment_type" value="Modular">
                <input type="hidden" name="package_id" value="" id="packageIdInput">
                <input type="hidden" name="program_id" value="" id="hidden_program_id">
                <input type="hidden" name="plan_id" value="2">
                <input type="hidden" name="learning_mode" id="learning_mode" value="">
                <input type="hidden" name="Start_Date" id="hidden_start_date" value="">
                <input type="hidden" name="selected_modules" id="selected_modules" value="">

                <!-- Dynamic Form Fields -->
                @if(isset($formRequirements) && $formRequirements->count() > 0)
                    @php 
                        $currentSection = null;
                    @endphp
                    @foreach($formRequirements as $field)
                        @if($field->field_type === 'section')
                            @php 
                                $currentSection = $field->section_name ?: $field->field_label;
                            @endphp
                            <h3 style="margin-top:2.1rem; margin-bottom:1rem; color:#2c3e50; border-bottom:2px solid #3498db; padding-bottom:0.5rem;">
                                <i class="bi bi-folder me-2"></i>{{ $currentSection }}
                            </h3>
                        @else
                            @if($field->field_name !== 'Cert_of_Grad')
                                <div class="form-group">
                                    @if($currentSection)
                                        <div class="section-indicator" style="font-size:0.9rem; color:#6c757d; margin-bottom:0.5rem;">
                                            {{ $currentSection }}
                                        </div>
                                    @endif
                                    <label for="{{ $field->field_name }}" @if($field->is_bold) style="font-weight:bold;" @endif>
                                        {{ $field->field_label ?: $field->field_name }}
                                        @if($field->is_required) <span class="required">*</span> @endif
                                    </label>
                                    @if($field->field_type === 'text')
                                        <input type="text" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                            class="form-control" value="{{ old($field->field_name, $student->{$field->field_name} ?? '') }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                    @elseif($field->field_type === 'email')
                                        <input type="email" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                            class="form-control" value="{{ old($field->field_name, $student->{$field->field_name} ?? '') }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                    @elseif($field->field_type === 'number')
                                        <input type="number" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                            class="form-control" value="{{ old($field->field_name, $student->{$field->field_name} ?? '') }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                    @elseif($field->field_type === 'date')
                                        <input type="date" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                            class="form-control" value="{{ old($field->field_name, $student->{$field->field_name} ?? '') }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                    @elseif($field->field_type === 'file')
                                        <input type="file" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                            class="form-control" accept=".jpg,.jpeg,.png,.pdf" 
                                            onchange="handleFileUpload(this)" {{ $field->is_required ? 'required' : '' }}>
                                        @if(isset($student) && $student->{$field->field_name})
                                            <div class="existing-file-info mt-2">
                                                <small class="text-success">
                                                    <i class="bi bi-check-circle"></i> File already uploaded: {{ $student->{$field->field_name} }}
                                                </small>
                                            </div>
                                        @endif
                                        <small class="form-text text-muted">Upload {{ $field->field_label ?: $field->field_name }} (JPG, PNG, PDF only)</small>
                                    @elseif($field->field_type === 'select')
                                        <select name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                                class="form-select" {{ $field->is_required ? 'required' : '' }}>
                                            <option value="">Select {{ $field->field_label ?: $field->field_name }}</option>
                                            @if($field->field_options)
                                                @php
                                                    $options = is_string($field->field_options) ? json_decode($field->field_options, true) : $field->field_options;
                                                    $selectedValue = old($field->field_name, $student->{$field->field_name} ?? '');
                                                @endphp
                                                @if(is_array($options))
                                                    @foreach($options as $option)
                                                        <option value="{{ $option }}" {{ $selectedValue == $option ? 'selected' : '' }}>{{ $option }}</option>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </select>
                                    @elseif($field->field_type === 'textarea')
                                        <textarea name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                                class="form-control" rows="3" {{ $field->is_required ? 'required' : '' }}>{{ old($field->field_name, $student->{$field->field_name} ?? '') }}</textarea>
                                    @elseif($field->field_type === 'checkbox')
                                        @php
                                            $isChecked = old($field->field_name, $student->{$field->field_name} ?? false);
                                        @endphp
                                        <div class="form-check">
                                            <input type="checkbox" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                                class="form-check-input" value="1" {{ $isChecked ? 'checked' : '' }}
                                                {{ $field->is_required ? 'required' : '' }}>
                                            <label class="form-check-label" for="{{ $field->field_name }}">
                                                {{ $field->field_label ?: $field->field_name }}
                                            </label>
                                        </div>
                                    @endif
                                    @if(isset($field->help_text) && $field->help_text)
                                        <small class="form-text text-muted">{{ $field->help_text }}</small>
                                    @endif
                                </div>
                            @endif
                        @endif
                    @endforeach
                @else
                    <!-- Fallback fields if no dynamic fields are configured -->
                    <div class="form-group">
                        <label for="firstname" style="font-weight:700;">
                            <i class="bi bi-person me-2"></i>First Name
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="firstname" id="firstname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname" style="font-weight:700;">
                            <i class="bi bi-person me-2"></i>Last Name
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="lastname" id="lastname" class="form-control" required>
                    </div>
                @endif
                <!-- Education Level Selection (after dynamic fields) -->
                <div class="form-group" style="margin-bottom:2rem;">
                    <label for="educationLevel" style="font-size:1.17rem;font-weight:700;">
                        <i class="bi bi-mortarboard me-2"></i>Education Level
                        <span class="required">*</span>
                    </label>
                    <select name="education_level" id="educationLevel" class="form-select" required onchange="toggleEducationLevelRequirements()">
                        <option value="">Select Education Level</option>
                        @if(isset($educationLevels) && $educationLevels->count() > 0)
                            @foreach($educationLevels as $level)
                                <option value="{{ $level->level_name }}" 
                                        data-file-requirements="{{ json_encode($level->getFileRequirementsForPlan($enrollmentType ?? 'modular')) }}">
                                    {{ $level->level_name }}
                                </option>
                            @endforeach
                        @else
                            <!-- No education levels configured - admin needs to set them up -->
                            <option value="" disabled>No education levels configured. Please contact administrator.</option>
                        @endif
                    </select>
                </div>
                <!-- Dynamic Education Level File Requirements -->
                <div id="educationLevelRequirements" style="display: none;"></div>
                <div class="form-group" style="margin-top:2.2rem;">
                    <label for="programSelect" style="font-size:1.17rem;font-weight:700;"><i class="bi bi-book me-2"></i>Program</label>
                    <select name="program_id" class="form-select" required id="programSelect" onchange="onProgramSelectionChange();">
                        <option value="">Select Program</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Batch Selection (only for synchronous learning) -->
                <div id="batchSelectionContainer" style="display: none;">
                    <h3 style="font-size:1.13rem;font-weight:700;"><i class="bi bi-people me-2"></i>Select Batch</h3>
                    <div id="batchOptions" class="batch-options">
                        <!-- Batches will be loaded here -->
                    </div>
                    <!-- Selected Batch Display -->
                    <div id="selectedBatchDisplay" class="selected-display" style="display: none;">
                        <div class="selected-item">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <span>Selected Batch: <strong id="selectedBatchName"></strong></span>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="margin-top:2rem;">
                    <label for="start_date_input" style="font-size:1.17rem;font-weight:700;"><i class="bi bi-calendar-event me-2"></i>Start Date</label>
                    <input type="date" name="Start_Date" id="start_date_input" class="form-control"
                        value="{{ $student->start_date ?? old('Start_Date') }}" 
                        onchange="updateHiddenStartDate()" required>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="termsCheckbox" name="terms_accepted" required>
                    <label class="form-check-label" for="termsCheckbox">
                        I agree to the <a href="#" onclick="showTermsModal()" class="text-decoration-none">Terms and Conditions</a>
                    </label>
                </div>
                <hr style="margin-bottom: 2.1rem; margin-top: 1.2rem;">
                <div class="form-navigation" style="justify-content: space-between;">
                    <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i> Back
                    </button>
                    <button type="submit" class="btn btn-success btn-lg" id="submitButton">
                        <i class="bi bi-check-circle me-2"></i> Complete Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Courses Modal -->
<div class="modal fade" id="coursesModal" tabindex="-1" aria-labelledby="coursesModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="coursesModalLabel">Select Courses in <span id="moduleNameDisplay"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" id="courseSelectionInfo">
                    <i class="bi bi-info-circle"></i> 
                    You can select up to <span id="courseLimitDisplay">2</span> courses based on your package.
                    Additional courses will incur extra charges.
                </div>
                <div id="coursesContainer">
                    <!-- Courses will be loaded here -->
                </div>
                <div class="mt-3" id="extraChargesInfo" style="display: none;">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Extra Charges:</strong> <span id="extraChargesAmount">$0</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCourseSelection()">Save Course Selection</button>
            </div>
        </div>
    </div>
</div>

<!-- OTP Verification Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="otpModalLabel">
                    <i class="fas fa-shield-alt me-2"></i> Verify your email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-3">The verification code has been sent to your email</p>
                <p class="fw-bold text-primary mb-4" id="otpTargetEmail">example@gmail.com</p>
                
                <div class="otp-input-group d-flex justify-content-center mb-3">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control text-center otp-digit" maxlength="1" style="width: 50px; height: 50px; font-size: 1.2rem;">
                        <input type="text" class="form-control text-center otp-digit" maxlength="1" style="width: 50px; height: 50px; font-size: 1.2rem;">
                        <input type="text" class="form-control text-center otp-digit" maxlength="1" style="width: 50px; height: 50px; font-size: 1.2rem;">
                        <input type="text" class="form-control text-center otp-digit" maxlength="1" style="width: 50px; height: 50px; font-size: 1.2rem;">
                        <input type="text" class="form-control text-center otp-digit" maxlength="1" style="width: 50px; height: 50px; font-size: 1.2rem;">
                        <input type="text" class="form-control text-center otp-digit" maxlength="1" style="width: 50px; height: 50px; font-size: 1.2rem;">
                    </div>
                </div>
                
                <input type="hidden" id="otp_code_modal" name="otp_code">
                
                <div id="otpStatusModal" class="status-message mt-3" style="display: none;"></div>
                
                <p class="text-muted small mt-3">
                    Not received yet? <a href="#" onclick="resendOTPCode()" class="text-decoration-none">Resend verification code</a>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" id="verifyOtpBtnModal" class="btn btn-primary w-100" onclick="verifyEnrollmentOTPModal()">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                <h6>1. Enrollment Agreement</h6>
                <p>By enrolling in this program, you agree to follow all institutional policies and procedures.</p>
                
                <h6>2. Payment Terms</h6>
                <p>All fees must be paid according to the schedule provided. Late payments may result in suspension of access to course materials.</p>
                
                <h6>3. Academic Integrity</h6>
                <p>Students are expected to maintain the highest standards of academic honesty and integrity.</p>
                
                <h6>4. Course Completion</h6>
                <p>Students must complete all required modules and assessments to receive certification.</p>
                
                <h6>5. Refund Policy</h6>
                <p>Refunds are available according to the institutional refund policy. Please contact administration for details.</p>
                
                <h6>6. Data Privacy</h6>
                <p>Your personal information will be handled according to our privacy policy and applicable data protection laws.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="acceptTerms()">I Accept</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Global variables
    let currentStep = 1;
    let totalSteps = {{ $isUserLoggedIn ? 5 : 6 }}; // Dynamic total steps based on login status
    let isUserLoggedIn = @json($isUserLoggedIn);
    let selectedPackageId = null;
    let selectedProgramId = null;
    let selectedModules = [];
    let selectedLearningMode = null;
    let selectedAccountType = null;
    let packageModuleLimit = 3;
    
    // CSRF token
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Initialize the form
    document.addEventListener('DOMContentLoaded', function() {
        updateStepper();
        loadStepContent();

        // Account Registration Step 5 validation
        const nextBtn = document.getElementById('step5NextBtn');
        if (nextBtn) {
            // Initial validation
            validateStep5();
        }
    });
    
    // Step navigation
    function nextStep() {
        if (currentStep < totalSteps) {
            // Skip account creation step (step 5) if user is already logged in
            if (isUserLoggedIn && currentStep === 4) {
                // Skip from step 4 (learning mode) directly to step 6 (form) 
                // But we need to renumber the steps for logged in users
                currentStep = 5; // This will be the final form step for logged-in users
            } else {
                currentStep++;
            }
            
            // Copy data when leaving step 5 (account registration) for non-logged-in users
            if (!isUserLoggedIn && currentStep === 6) {
                copyStepperDataToFinalForm();
            }
            
            updateStepper();
            loadStepContent();
            
            // If moving to final step, copy all data again
            if ((isUserLoggedIn && currentStep === 5) || (!isUserLoggedIn && currentStep === 6)) {
                copyStepperDataToFinalForm();
            }
        }
    }
    
    function prevStep() {
        if (currentStep > 1) {
            // Handle stepping back for logged-in users (skip account creation)
            if (isUserLoggedIn && currentStep === 5) {
                // Go back from step 5 (form) to step 4 (learning mode) for logged-in users
                currentStep = 4;
            } else {
                currentStep--;
            }
            updateStepper();
            loadStepContent();
        }
    }
    
    // Update stepper UI
    function updateStepper() {
        // Update step indicators
        for (let i = 1; i <= totalSteps; i++) {
            const step = document.getElementById(`step-${i}`);
            step.classList.remove('active', 'completed');
            
            if (i < currentStep) {
                step.classList.add('completed');
            } else if (i === currentStep) {
                step.classList.add('active');
            }
        }
        
        // Update progress bar
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        document.getElementById('progressBar').style.width = progress + '%';
        
        // Show/hide step content
        for (let i = 1; i <= totalSteps; i++) {
            const content = document.getElementById(`content-${i}`);
            content.classList.remove('active');
        }
        document.getElementById(`content-${currentStep}`).classList.add('active');
    }
    
    // Load content for each step
    function loadStepContent() {
        console.log('Loading content for step:', currentStep);
        switch (currentStep) {
            case 2:
                // Reset program selection when entering step 2
                selectedProgramId = null;
                document.getElementById('program_id').value = '';
                document.getElementById('step2-next').disabled = true;
                loadPrograms();
                break;
            case 3:
                loadModules();
                break;
            case 5:
                setupAccountForm();
                break;
            case 6:
                loadDynamicFormFields();
                break;
        }
    }
    
    // Package selection
    function selectPackage(packageId, programId, moduleCount) {
        console.log('Package selected:', { packageId, programId, moduleCount });
        
        // Remove selection from all cards
        document.querySelectorAll('.package-card-pro').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selection to clicked card
        event.target.closest('.package-card-pro').classList.add('selected');
        
        // Store selection
        selectedPackageId = packageId;
        // Don't auto-select program - let user choose in step 2
        // selectedProgramId = programId;
        packageModuleLimit = moduleCount;
        
        // Update hidden inputs
        document.getElementById('package_id').value = packageId;
        // Don't pre-fill program
        // document.getElementById('program_id').value = programId;
        
        // Enable next button
        document.getElementById('step1-next').disabled = false;
    }
    window.selectPackage = selectPackage;

    // Program selection
    function selectProgram(programId) {
        console.log('Program selected:', programId);
        
        // Remove selection from all cards
        document.querySelectorAll('.selection-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selection to clicked card
        event.target.closest('.selection-card').classList.add('selected');
        
        // Store selection
        selectedProgramId = programId;
        
        // Update hidden input
        document.getElementById('program_id').value = programId;
        
        // Enable next button
        document.getElementById('step2-next').disabled = false;
    }
    window.selectProgram = selectProgram;
    
    // Load programs
    function loadPrograms() {
        const grid = document.getElementById('programsGrid');
        grid.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin fa-2x"></i> Loading programs...</div>';
        
        // Fetch programs from the database via AJAX
        fetch('/get-programs', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.programs && data.programs.length > 0) {
                displayPrograms(data.programs);
            } else {
                grid.innerHTML = '<div class="alert alert-info">No programs available. Please contact the administrator.</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching programs:', error);
            grid.innerHTML = '<div class="alert alert-danger">Error loading programs. Please try again.</div>';
        });
    }
    
    // Display programs in the grid
    function displayPrograms(programs) {
        const grid = document.getElementById('programsGrid');
        
        let programsHtml = '';
        programs.forEach(program => {
            const isSelected = program.program_id == selectedProgramId;
            programsHtml += `
                <div class="col-md-6 mb-4">
                    <div class="card selection-card h-100 ${isSelected ? 'selected' : ''}" 
                         onclick="selectProgram(${program.program_id})">
                        <div class="card-body">
                            <h4 class="card-title">${program.program_name}</h4>
                            <p class="card-text">${program.program_description || 'No description available.'}</p>
                            <ul class="list-unstyled mt-3 mb-0">
                                <li><i class="bi bi-check2 text-success"></i> Professional certification</li>
                                <li><i class="bi bi-check2 text-success"></i> Industry-relevant skills</li>
                                <li><i class="bi bi-check2 text-success"></i> Expert instructors</li>
                                <li><i class="bi bi-check2 text-success"></i> Career advancement</li>
                            </ul>
                        </div>
                    </div>
                </div>
            `;
        });
        
        grid.innerHTML = programsHtml;
        
        // If program is pre-selected from package, enable next button
        if (selectedProgramId) {
            document.getElementById('step2-next').disabled = false;
        }
    }
    
    // Load modules
    function loadModules() {
        const grid = document.getElementById('modulesGrid');
        const limitSpan = document.getElementById('moduleLimit');
        
        grid.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin fa-2x"></i> Loading modules...</div>';
        limitSpan.textContent = packageModuleLimit;
        
        fetch(`/get-program-modules?program_id=${selectedProgramId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Modules loaded:', data);
                
                if (data.success && data.modules) {
                    displayModules(data.modules);
                } else {
                    grid.innerHTML = '<div class="alert alert-danger">Failed to load modules. Please try again.</div>';
                }
            })
            .catch(error => {
                console.error('Error loading modules:', error);
                grid.innerHTML = '<div class="alert alert-danger">Error loading modules. Please try again.</div>';
            });
    }
    
    // Display modules
    function displayModules(modules) {
        const grid = document.getElementById('modulesGrid');
        
        if (!modules || modules.length === 0) {
            grid.innerHTML = '<div class="alert alert-info">No modules available for this program.</div>';
            return;
        }
        
        let modulesHtml = '';
        modules.forEach(module => {
            const moduleName = module.name || module.module_name || 'Unnamed Module';
            const moduleDesc = module.description || module.module_description || 'No description available';
            
            modulesHtml += `
                <div class="col-md-6 mb-4">
                    <div class="module-card" data-module-id="${module.id}">
                        <div class="card module-card h-100">
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input module-checkbox" id="module_${module.id}" 
                                           value="${module.id}" onchange="handleModuleSelection(this)">
                                    <label class="form-check-label module-title" for="module_${module.id}">${moduleName}</label>
                                </div>
                                <p class="card-text module-description">${moduleDesc}</p>
                                <div class="module-meta">
                                    <span class="module-duration">
                                        <i class="bi bi-clock"></i> ${module.duration || 'Flexible'}
                                    </span>
                                    <span class="module-level">${module.level || 'All Levels'}</span>
                                </div>
                                <div class="module-actions">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                            onclick="showCoursesModal(${module.id}, '${moduleName}')">
                                        <i class="bi bi-list"></i> View Courses
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        grid.innerHTML = modulesHtml;
    }
    
    // Handle module selection
    function handleModuleSelection(checkbox) {
        const moduleId = checkbox.value;
        const moduleCard = checkbox.closest('.module-card');
        const moduleTitle = moduleCard.querySelector('.module-title').textContent;
        
        if (checkbox.checked) {
            // Check limit
            if (selectedModules.length >= packageModuleLimit) {
                alert(`You can only select up to ${packageModuleLimit} modules.`);
                checkbox.checked = false;
                return;
            }
            
            // Add to selection
            selectedModules.push({
                id: moduleId,
                name: moduleTitle
            });
            moduleCard.classList.add('selected');
        } else {
            // Remove from selection
            selectedModules = selectedModules.filter(m => m.id !== moduleId);
            moduleCard.classList.remove('selected');
        }
        
        // Update hidden input
        document.getElementById('selected_modules').value = JSON.stringify(selectedModules);
        
        // Enable/disable next button
        document.getElementById('step3-next').disabled = selectedModules.length === 0;
        
        console.log('Selected modules:', selectedModules);
    }
    window.handleModuleSelection = handleModuleSelection;
    
    // Learning mode selection
    function selectLearningMode(mode) {
        selectedLearningMode = mode;
        
        // Remove selection from all cards
        document.querySelectorAll('.selection-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selection to clicked card
        event.target.closest('.selection-card').classList.add('selected');
        
        // Update hidden input
        document.getElementById('learning_mode').value = mode;
        
        // Enable next button
        document.getElementById('step4-next').disabled = false;
    }
    window.selectLearningMode = selectLearningMode;
    
    // Account type selection
    function selectAccountType(type) {
        selectedAccountType = type;
        
        // Remove selection from all cards
        document.querySelectorAll('.selection-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selection to clicked card
        event.target.closest('.selection-card').classList.add('selected');
        
        // Enable next button
        document.getElementById('step5-next').disabled = false;
    }
    window.selectAccountType = selectAccountType;
    
    // Setup account form
    function setupAccountForm() {
        console.log('Setting up account form for step 5');
        
        // Add validation listeners for Step 5
        const firstnameField = document.getElementById('user_firstname');
        const lastnameField = document.getElementById('user_lastname');
        const emailField = document.getElementById('user_email');
        const passwordField = document.getElementById('password');
        const passwordConfirmField = document.getElementById('password_confirmation');
        const nextBtn = document.getElementById('step5NextBtn');
        
        // Debounce timer for validation
        let validationTimer = null;
        
        function debouncedValidateStep5() {
            clearTimeout(validationTimer);
            validationTimer = setTimeout(validateStep5, 500);
        }
        
        if (firstnameField) firstnameField.addEventListener('input', debouncedValidateStep5);
        if (lastnameField) lastnameField.addEventListener('input', debouncedValidateStep5);
        if (emailField) {
            emailField.addEventListener('input', function() {
                setTimeout(validateEmail, 300);
                debouncedValidateStep5();
            });
        }
        if (passwordField) {
            passwordField.addEventListener('input', function() {
                setTimeout(validatePassword, 50);
                debouncedValidateStep5();
            });
        }
        if (passwordConfirmField) {
            passwordConfirmField.addEventListener('input', function() {
                setTimeout(validatePasswordConfirmation, 50);
                debouncedValidateStep5();
            });
        }
    }

    // Validate Step 5 (Account Registration)
    function validateStep5() {
        const firstname = document.getElementById('user_firstname')?.value.trim() || '';
        const lastname = document.getElementById('user_lastname')?.value.trim() || '';
        const email = document.getElementById('user_email')?.value.trim() || '';
        const password = document.getElementById('password')?.value || '';
        const passwordConfirm = document.getElementById('password_confirmation')?.value || '';
        const referralCode = document.getElementById('referral_code')?.value.trim() || '';
        const nextBtn = document.getElementById('step5NextBtn');

        // Check referral requirements
        const referralEnabled = document.getElementById('referral_code') !== null;
        const referralRequired = referralEnabled && document.getElementById('referral_code')?.required;
        let referralValid = true;
        
        if (referralRequired && !referralCode) {
            referralValid = false;
        }

        // Basic validation
        const isValid = firstname.length > 0 &&
                       lastname.length > 0 &&
                       /^[^@]+@[^@]+\.[^@]+$/.test(email) &&
                       password.length >= 8 &&
                       password === passwordConfirm &&
                       referralValid;

        if (nextBtn) {
            nextBtn.disabled = !isValid;
        }

        return isValid;
    }

    // Validate email format and availability
    async function validateEmail() {
        const emailField = document.getElementById('user_email');
        const emailError = document.getElementById('emailError');
        
        if (!emailField || !emailError) return true;
        
        const email = emailField.value.trim();
        
        // Clear previous errors
        emailError.style.display = 'none';
        emailField.classList.remove('error');
        
        if (!email) return false;
        
        // Basic email format validation
        const emailRegex = /^[^@]+@[^@]+\.[^@]+$/;
        if (!emailRegex.test(email)) {
            emailError.textContent = 'Please enter a valid email address';
            emailError.style.display = 'block';
            emailField.classList.add('error');
            return false;
        }
        
        // Check email availability
        try {
            const response = await fetch('/check-email-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ email: email })
            });
            
            const data = await response.json();
            
            if (!data.available) {
                emailError.textContent = 'This email is already registered';
                emailError.style.display = 'block';
                emailField.classList.add('error');
                return false;
            }
        } catch (error) {
            console.error('Error checking email availability:', error);
        }
        
        return true;
    }

    // Validate password
    function validatePassword() {
        const passwordField = document.getElementById('password');
        const passwordError = document.getElementById('passwordError');
        
        if (!passwordField || !passwordError) return true;
        
        const password = passwordField.value;
        
        passwordError.style.display = 'none';
        passwordField.classList.remove('error');
        
        if (password.length < 8) {
            passwordError.textContent = 'Password must be at least 8 characters long';
            passwordError.style.display = 'block';
            passwordField.classList.add('error');
            return false;
        }
        
        return true;
    }

    // Validate password confirmation
    function validatePasswordConfirmation() {
        const passwordField = document.getElementById('password');
        const passwordConfirmField = document.getElementById('password_confirmation');
        const passwordMatchError = document.getElementById('passwordMatchError');
        
        if (!passwordField || !passwordConfirmField || !passwordMatchError) return true;
        
        const password = passwordField.value;
        const passwordConfirm = passwordConfirmField.value;
        
        passwordMatchError.style.display = 'none';
        passwordConfirmField.classList.remove('error');
        
        if (password !== passwordConfirm) {
            passwordMatchError.textContent = 'Passwords do not match';
            passwordMatchError.style.display = 'block';
            passwordConfirmField.classList.add('error');
            return false;
        }
        
        return true;
    }

    // Send OTP for enrollment
    function sendEnrollmentOTP() {
        const emailField = document.getElementById('user_email');
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        const emailError = document.getElementById('emailError');
        
        if (!emailField || !emailField.value.trim()) {
            if (emailError) {
                emailError.textContent = 'Please enter your email address first';
                emailError.style.display = 'block';
            }
            return;
        }
        
        const email = emailField.value.trim();
        
        // Validate email format
        const emailRegex = /^[^@]+@[^@]+\.[^@]+$/;
        if (!emailRegex.test(email)) {
            if (emailError) {
                emailError.textContent = 'Please enter a valid email address';
                emailError.style.display = 'block';
            }
            return;
        }
        
        // Disable button and show loading
        if (sendOtpBtn) {
            sendOtpBtn.disabled = true;
            sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        }
        
        // Send OTP request
        fetch('/enrollment/send-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show OTP modal
                document.getElementById('otpTargetEmail').textContent = email;
                const otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
                otpModal.show();
                
                // Setup OTP input handlers
                setupOTPInputHandlers();
                
                if (emailError) {
                    emailError.style.display = 'none';
                }
            } else {
                if (emailError) {
                    emailError.textContent = data.message || 'Failed to send OTP';
                    emailError.style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error sending OTP:', error);
            if (emailError) {
                emailError.textContent = 'Failed to send OTP. Please try again.';
                emailError.style.display = 'block';
            }
        })
        .finally(() => {
            if (sendOtpBtn) {
                sendOtpBtn.disabled = false;
                sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
            }
        });
    }

    // Setup OTP input handlers
    function setupOTPInputHandlers() {
        const otpInputs = document.querySelectorAll('.otp-digit');
        
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                
                // Only allow digits
                if (!/^\d$/.test(value)) {
                    e.target.value = '';
                    return;
                }
                
                // Move to next input
                if (value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                
                // Update hidden OTP field
                updateHiddenOTPField();
            });
            
            input.addEventListener('keydown', function(e) {
                // Handle backspace
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });
    }

    // Update hidden OTP field
    function updateHiddenOTPField() {
        const otpInputs = document.querySelectorAll('.otp-digit');
        const otpCode = Array.from(otpInputs).map(input => input.value).join('');
        
        const hiddenOTPField = document.getElementById('otp_code_modal');
        if (hiddenOTPField) {
            hiddenOTPField.value = otpCode;
        }
    }

    // Verify OTP in modal
    function verifyEnrollmentOTPModal() {
        const otpCode = document.getElementById('otp_code_modal')?.value;
        const email = document.getElementById('user_email')?.value;
        const verifyBtn = document.getElementById('verifyOtpBtnModal');
        const statusDiv = document.getElementById('otpStatusModal');
        
        if (!otpCode || otpCode.length !== 6) {
            if (statusDiv) {
                statusDiv.textContent = 'Please enter the complete 6-digit code';
                statusDiv.className = 'status-message text-danger mt-3';
                statusDiv.style.display = 'block';
            }
            return;
        }
        
        if (verifyBtn) {
            verifyBtn.disabled = true;
            verifyBtn.textContent = 'Verifying...';
        }
        
        fetch('/enrollment/verify-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ 
                email: email,
                otp_code: otpCode 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (statusDiv) {
                    statusDiv.textContent = 'Email verified successfully!';
                    statusDiv.className = 'status-message text-success mt-3';
                    statusDiv.style.display = 'block';
                }
                
                // Close modal after delay
                setTimeout(() => {
                    const otpModal = bootstrap.Modal.getInstance(document.getElementById('otpModal'));
                    if (otpModal) otpModal.hide();
                    
                    // Mark email as verified
                    const emailField = document.getElementById('user_email');
                    if (emailField) {
                        emailField.setAttribute('data-verified', 'true');
                        emailField.style.borderColor = '#28a745';
                    }
                    
                    // Re-validate step 5
                    validateStep5();
                }, 1500);
            } else {
                if (statusDiv) {
                    statusDiv.textContent = data.message || 'Invalid verification code';
                    statusDiv.className = 'status-message text-danger mt-3';
                    statusDiv.style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error verifying OTP:', error);
            if (statusDiv) {
                statusDiv.textContent = 'Verification failed. Please try again.';
                statusDiv.className = 'status-message text-danger mt-3';
                statusDiv.style.display = 'block';
            }
        })
        .finally(() => {
            if (verifyBtn) {
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Continue';
            }
        });
    }

    // Resend OTP
    function resendOTPCode() {
        const email = document.getElementById('user_email')?.value;
        if (email) {
            sendEnrollmentOTP();
        }
    }

    // Validate referral code
    function validateReferralCode() {
        const referralField = document.getElementById('referral_code');
        const validateBtn = document.getElementById('validateReferralBtn');
        const errorDiv = document.getElementById('referralCodeError');
        const successDiv = document.getElementById('referralCodeSuccess');
        
        if (!referralField || !referralField.value.trim()) {
            if (errorDiv) {
                errorDiv.textContent = 'Please enter a referral code';
                errorDiv.style.display = 'block';
            }
            if (successDiv) successDiv.style.display = 'none';
            return;
        }
        
        const referralCode = referralField.value.trim();
        
        if (validateBtn) {
            validateBtn.disabled = true;
            validateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validating...';
        }
        
        fetch('/enrollment/validate-referral', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ referral_code: referralCode })
        })
        .then(response => response.json())
        .then(data => {
            if (errorDiv) errorDiv.style.display = 'none';
            if (successDiv) successDiv.style.display = 'none';
            
            if (data.success) {
                if (successDiv) {
                    successDiv.textContent = `Valid referral code! Referred by: ${data.referrer_name}`;
                    successDiv.style.display = 'block';
                }
                referralField.setAttribute('data-valid', 'true');
                referralField.style.borderColor = '#28a745';
            } else {
                if (errorDiv) {
                    errorDiv.textContent = data.message || 'Invalid referral code';
                    errorDiv.style.display = 'block';
                }
                referralField.setAttribute('data-valid', 'false');
                referralField.style.borderColor = '#dc3545';
            }
            
            // Re-validate step 5
            validateStep5();
        })
        .catch(error => {
            console.error('Error validating referral code:', error);
            if (errorDiv) {
                errorDiv.textContent = 'Validation failed. Please try again.';
                errorError.style.display = 'block';
            }
        })
        .finally(() => {
            if (validateBtn) {
                validateBtn.disabled = false;
                validateBtn.innerHTML = '<i class="fas fa-check"></i> Validate';
            }
        });
    }
    
    // Load dynamic form fields
    function loadDynamicFormFields() {
        // This would load the final registration form with dynamic fields
        console.log('Loading dynamic form fields');
    }
    
    // Submit enrollment
    function submitEnrollment() {
        console.log('Submitting enrollment with data:', {
            packageId: selectedPackageId,
            programId: selectedProgramId,
            modules: selectedModules,
            learningMode: selectedLearningMode,
            accountType: selectedAccountType
        });
        
        // Here you would submit the form data to the server
        alert('Enrollment submission would happen here!');
    }
    
    // Global variables for course selection
    let currentModuleId = null;
    let selectedCourses = {};
    let packageCourseLimit = 2; // Default limit, will be updated from package data
    let extraModulePrice = 0; // Price per extra course from package data
    
    // Show courses modal with course selection capability
    function showCoursesModal(moduleId, moduleName) {
        currentModuleId = moduleId;
        document.getElementById('moduleNameDisplay').textContent = moduleName;
        
        // Update course limit display from package data
        updateCourseLimitDisplay();
        
        const container = document.getElementById('coursesContainer');
        container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading courses...</div>';
        
        // Load courses for the module
        fetch(`/get-module-courses?module_id=${moduleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses) {
                    displayCoursesWithSelection(data.courses);
                } else {
                    container.innerHTML = '<div class="alert alert-info">No courses available for this module.</div>';
                }
            })
            .catch(error => {
                console.error('Error loading courses:', error);
                container.innerHTML = '<div class="alert alert-danger">Error loading courses. Please try again.</div>';
            });
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('coursesModal'));
        modal.show();
    }
    window.showCoursesModal = showCoursesModal;
    
    // Update course limit display based on selected package
    function updateCourseLimitDisplay() {
        // Get package info to determine course limit
        if (selectedPackageId) {
            fetch(`/get-package-details?package_id=${selectedPackageId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.package) {
                        packageCourseLimit = data.package.allowed_modules || 2;
                        extraModulePrice = data.package.extra_module_price || 0;
                        document.getElementById('courseLimitDisplay').textContent = packageCourseLimit;
                    }
                })
                .catch(error => {
                    console.error('Error loading package details:', error);
                });
        }
        document.getElementById('courseLimitDisplay').textContent = packageCourseLimit;
    }
    
    // Display courses with individual selection checkboxes
    function displayCoursesWithSelection(courses) {
        const container = document.getElementById('coursesContainer');
        
        if (!courses || courses.length === 0) {
            container.innerHTML = '<div class="alert alert-info">No courses available for this module.</div>';
            return;
        }
        
        let coursesHtml = '<div class="courses-selection-list">';
        courses.forEach((course, index) => {
            const courseId = course.course_id || course.subject_id || index;
            const courseName = course.course_name || course.subject_name || 'Unnamed Course';
            const courseDesc = course.course_description || course.subject_description || 'No description available';
            const coursePrice = course.course_price || course.subject_price || 0;
            const isChecked = selectedCourses[currentModuleId] && selectedCourses[currentModuleId].includes(courseId);
            
            coursesHtml += `
                <div class="course-selection-item card mb-3" data-course-id="${courseId}">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="form-check me-3">
                                <input class="form-check-input course-checkbox" 
                                       type="checkbox" 
                                       id="course_${courseId}" 
                                       value="${courseId}"
                                       data-course-name="${courseName}"
                                       data-course-price="${coursePrice}"
                                       ${isChecked ? 'checked' : ''}
                                       onchange="handleCourseSelection(this)">
                                <label class="form-check-label" for="course_${courseId}"></label>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 course-title">${courseName}</h6>
                                <p class="mb-1 text-muted course-description">${courseDesc}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Duration: ${course.duration || 'Flexible'}</small>
                                    ${coursePrice > 0 ? `<span class="badge bg-primary">₱${parseFloat(coursePrice).toFixed(2)}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        coursesHtml += '</div>';
        
        container.innerHTML = coursesHtml;
        updateExtraChargesDisplay();
    }
    
    // Handle individual course selection
    function handleCourseSelection(checkbox) {
        const moduleId = currentModuleId;
        const courseId = checkbox.value;
        const courseName = checkbox.dataset.courseName;
        const coursePrice = parseFloat(checkbox.dataset.coursePrice || 0);
        
        // Initialize module in selectedCourses if not exists
        if (!selectedCourses[moduleId]) {
            selectedCourses[moduleId] = [];
        }
        
        if (checkbox.checked) {
            // Add course to selection
            selectedCourses[moduleId].push(courseId);
        } else {
            // Remove course from selection
            selectedCourses[moduleId] = selectedCourses[moduleId].filter(id => id !== courseId);
        }
        
        // Update visual feedback
        updateExtraChargesDisplay();
        
        console.log('Course selection updated:', selectedCourses);
    }
    window.handleCourseSelection = handleCourseSelection;
    
    // Update extra charges display
    function updateExtraChargesDisplay() {
        const moduleId = currentModuleId;
        if (!selectedCourses[moduleId]) return;
        
        const selectedCount = selectedCourses[moduleId].length;
        const extraCourses = Math.max(0, selectedCount - packageCourseLimit);
        const extraCharges = extraCourses * extraModulePrice;
        
        const extraChargesInfo = document.getElementById('extraChargesInfo');
        const extraChargesAmount = document.getElementById('extraChargesAmount');
        
        if (extraCourses > 0) {
            extraChargesInfo.style.display = 'block';
            extraChargesAmount.textContent = `₱${extraCharges.toFixed(2)} (${extraCourses} extra course${extraCourses > 1 ? 's' : ''})`;
        } else {
            extraChargesInfo.style.display = 'none';
        }
    }
    
    // Save course selection and close modal
    function saveCourseSelection() {
        const moduleId = currentModuleId;
        
        if (!selectedCourses[moduleId] || selectedCourses[moduleId].length === 0) {
            alert('Please select at least one course from this module.');
            return;
        }
        
        // Update the module card to show selected courses count
        const moduleCard = document.querySelector(`[data-module-id="${moduleId}"]`);
        if (moduleCard) {
            const coursesCount = selectedCourses[moduleId].length;
            let coursesBadge = moduleCard.querySelector('.courses-selected-badge');
            if (!coursesBadge) {
                coursesBadge = document.createElement('span');
                coursesBadge.className = 'courses-selected-badge badge bg-success ms-2';
                const moduleTitle = moduleCard.querySelector('.module-title');
                if (moduleTitle) {
                    moduleTitle.appendChild(coursesBadge);
                }
            }
            coursesBadge.textContent = `${coursesCount} course${coursesCount > 1 ? 's' : ''} selected`;
        }
        
        // Update the selected_modules data to include course selections
        updateSelectedModulesWithCourses();
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('coursesModal'));
        modal.hide();
        
        console.log('Course selection saved for module', moduleId, ':', selectedCourses[moduleId]);
    }
    window.saveCourseSelection = saveCourseSelection;
    
    // Update selected modules data to include course selections
    function updateSelectedModulesWithCourses() {
        const modulesWithCourses = selectedModules.map(module => {
            const courseSelection = selectedCourses[module.id] || [];
            return {
                ...module,
                selected_courses: courseSelection
            };
        });
        
        document.getElementById('selected_modules').value = JSON.stringify(modulesWithCourses);
        console.log('Updated modules with course selections:', modulesWithCourses);
    }
    
    // Legacy display courses function (for backward compatibility)
    function displayCourses(courses) {
        displayCoursesWithSelection(courses);
    }

    // Add this function to handle dynamic education level file requirements
    function toggleEducationLevelRequirements() {
        const select = document.getElementById('educationLevel');
        const requirementsDiv = document.getElementById('educationLevelRequirements');
        requirementsDiv.innerHTML = '';
        requirementsDiv.style.display = 'none';
        if (!select.value) return;
        const selectedOption = select.options[select.selectedIndex];
        const fileRequirements = selectedOption.getAttribute('data-file-requirements');
        if (!fileRequirements) return;
        let requirements = [];
        try {
            requirements = JSON.parse(fileRequirements);
        } catch (e) {
            console.error('Invalid file requirements JSON:', fileRequirements);
            return;
        }
        if (!Array.isArray(requirements) || requirements.length === 0) return;
        let html = '';
        requirements.forEach(req => {
            if (!req.available_modular_plan) return; // Only show for modular plan
            const label = req.custom_name || req.field_name || req.document_type;
            const required = req.is_required ? 'required' : '';
            let accept = '';
            switch (req.file_type) {
                case 'image': accept = '.jpg,.jpeg,.png,.gif'; break;
                case 'pdf': accept = '.pdf'; break;
                case 'document': accept = '.pdf,.doc,.docx'; break;
                default: accept = '*'; break;
            }
            html += `<div class="form-group">
                <label>${label} ${req.is_required ? '<span class="text-danger">*</span>' : ''}</label>
                <input type="file" name="${label.replace(/\s+/g, '_').toLowerCase()}" class="form-control" accept="${accept}" ${required}>
                <small class="form-text text-muted">Upload ${label} (${accept.replace(/\./g, '').toUpperCase()} only)</small>
            </div>`;
        });
        requirementsDiv.innerHTML = html;
        requirementsDiv.style.display = 'block';
    }

// --- BEGIN: Ensure all stepper data is copied to final form before submission ---

function copyStepperDataToFinalForm() {
    // Account info (step 5) - only for non-logged-in users
    let userFirstname = '';
    let userLastname = '';
    let userEmail = '';
    let password = '';
    let passwordConfirmation = '';
    let referralCode = '';
    
    if (!isUserLoggedIn) {
        userFirstname = document.getElementById('user_firstname')?.value || '';
        userLastname = document.getElementById('user_lastname')?.value || '';
        userEmail = document.getElementById('user_email')?.value || '';
        password = document.getElementById('password')?.value || '';
        passwordConfirmation = document.getElementById('password_confirmation')?.value || '';
        referralCode = document.getElementById('referral_code')?.value || '';
    } else {
        // For logged-in users, we don't need to collect account data
        console.log('User is logged in, skipping account data collection');
    }

    // Package, program, modules, learning mode (steps 1-4)
    const packageId = document.getElementById('package_id')?.value || '';
    const programId = document.getElementById('program_id')?.value || '';
    const selectedModules = document.getElementById('selected_modules')?.value || '';
    const learningMode = document.getElementById('learning_mode')?.value || '';

    // Final form hidden fields
    const form = document.getElementById('modularEnrollmentForm');
    if (!form) return;

    // Set or create hidden fields for account info (only for non-logged-in users)
    if (!isUserLoggedIn) {
        setOrCreateHidden(form, 'user_firstname', userFirstname);
        setOrCreateHidden(form, 'user_lastname', userLastname);
        setOrCreateHidden(form, 'email', userEmail);
        setOrCreateHidden(form, 'password', password);
        setOrCreateHidden(form, 'password_confirmation', passwordConfirmation);
        setOrCreateHidden(form, 'referral_code', referralCode);
    }
    
    // Set or create hidden fields for stepper selections
    setOrCreateHidden(form, 'package_id', packageId);
    setOrCreateHidden(form, 'program_id', programId);
    setOrCreateHidden(form, 'selected_modules', selectedModules);
    setOrCreateHidden(form, 'learning_mode', learningMode);
    
    // Handle start date - set to today if empty
    const startDateInput = form.querySelector('input[name="Start_Date"]');
    if (startDateInput && (!startDateInput.value || startDateInput.value.trim() === '')) {
        const today = new Date().toISOString().split('T')[0];
        startDateInput.value = today;
        console.log('📅 Set Start_Date to today:', today);
    }
}

function setOrCreateHidden(form, name, value) {
    let input = form.querySelector(`input[name="${name}"]`);
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        form.appendChild(input);
    }
    input.value = value;
}

// Hook into step navigation and form submission
function nextStep() {
    if (currentStep < totalSteps) {
        // Copy data when leaving step 5 (account registration)
        if (currentStep === 5) {
            copyStepperDataToFinalForm();
        }
        
        currentStep++;
        updateStepper();
        loadStepContent();
        
        // If moving to final step, copy all data again
        if (currentStep === 6) {
            copyStepperDataToFinalForm();
        }
    }
}

// Also copy data right before form submission (in case user edits fields in step 6)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('modularEnrollmentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default submission
            
            // Copy all stepper data to the form
            copyStepperDataToFinalForm();
            
            // Validate that required data is present
            const requiredFields = ['package_id', 'program_id', 'selected_modules', 'learning_mode'];
            
            // Add account fields to validation only for non-logged-in users
            if (!isUserLoggedIn) {
                requiredFields.push('user_firstname', 'user_lastname', 'email');
            }
            
            const missingFields = [];
            
            requiredFields.forEach(field => {
                const input = form.querySelector(`input[name="${field}"]`);
                if (!input || !input.value.trim()) {
                    missingFields.push(field);
                }
            });
            
            if (missingFields.length > 0) {
                alert('Missing required fields: ' + missingFields.join(', '));
                console.error('Missing required fields:', missingFields);
                return;
            }
            
            // Submit via AJAX to handle the response properly
            const formData = new FormData(form);
            
            // Double-check that Start_Date is not empty in FormData
            if (!formData.get('Start_Date') || formData.get('Start_Date') === '') {
                const today = new Date().toISOString().split('T')[0];
                formData.set('Start_Date', today);
                console.log('📅 Force-set Start_Date in FormData to:', today);
            }
            
            // Log form data for debugging
            console.log('Submitting form data:', Object.fromEntries(formData));
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registration completed successfully!');
                    // Redirect to success page or login
                    window.location.href = '/login?message=registration_success';
                } else {
                    alert('Registration failed: ' + (data.message || 'Unknown error'));
                    console.error('Registration failed:', data);
                }
            })
            .catch(error => {
                console.error('Form submission error:', error);
                alert('Form submission failed. Please try again.');
            });
        });
    }
});

// --- END: Ensure all stepper data is copied to final form before submission ---

// Missing function: onProgramSelectionChange
function onProgramSelectionChange() {
    const programSelect = document.getElementById('programSelect');
    if (!programSelect) return;
    
    const newSelectedProgramId = programSelect.value;
    selectedProgramId = newSelectedProgramId; // Update global variable
    
    console.log('Program selection changed to:', selectedProgramId);
    
    // Update hidden program_id field
    const hiddenProgramField = document.querySelector('input[name="program_id"]');
    if (hiddenProgramField) {
        hiddenProgramField.value = selectedProgramId;
    }
    
    // Update the main stepper hidden field too
    const stepperProgramField = document.getElementById('program_id');
    if (stepperProgramField) {
        stepperProgramField.value = selectedProgramId;
    }
    
    // Load modules for the selected program if we're in step 3
    if (selectedProgramId && currentStep === 3) {
        loadModules();
    }
}

// Function to load modules for a specific program
function loadModulesForProgram(programId) {
    if (programId) {
        selectedProgramId = programId;
        loadModules();
    }
}

// Missing function: updateHiddenStartDate
function updateHiddenStartDate() {
    const dateInput = document.querySelector('input[type="date"]');
    const hiddenDateField = document.querySelector('input[name="Start_Date"]');
    
    if (dateInput && hiddenDateField) {
        hiddenDateField.value = dateInput.value;
        console.log('Start date updated to:', dateInput.value);
    }
}

// Function to show terms and conditions modal
function showTermsModal() {
    const modal = new bootstrap.Modal(document.getElementById('termsModal'));
    modal.show();
}
window.showTermsModal = showTermsModal;

// Function to accept terms and conditions
function acceptTerms() {
    const termsCheckbox = document.getElementById('termsCheckbox');
    if (termsCheckbox) {
        termsCheckbox.checked = true;
        console.log('Terms and conditions accepted');
    }
}
window.acceptTerms = acceptTerms;

// Function to toggle education level requirements (similar to Full_enrollment)
function toggleEducationLevelRequirements() {
    const educationLevel = document.getElementById('educationLevel'); // Changed from 'education_level' to 'educationLevel'
    const requirementsContainer = document.getElementById('educationLevelRequirements');
    
    if (!educationLevel || !requirementsContainer) return;
    
    const selectedOption = educationLevel.options[educationLevel.selectedIndex];
    
    console.log('Education level changed to:', educationLevel.value);
    
    // Clear existing requirements
    requirementsContainer.innerHTML = '';
    requirementsContainer.style.display = 'none';
    
    if (educationLevel.value && selectedOption.dataset.fileRequirements) {
        try {
            const fileRequirements = JSON.parse(selectedOption.dataset.fileRequirements);
            console.log('File requirements:', fileRequirements);
            
            if (fileRequirements && (Array.isArray(fileRequirements) ? fileRequirements.length > 0 : Object.keys(fileRequirements).length > 0)) {
                requirementsContainer.style.display = 'block';
                
                // Ensure we always have an array of requirement objects
                let requirementsArray = [];
                
                if (Array.isArray(fileRequirements)) {
                    requirementsArray = fileRequirements;
                } else if (typeof fileRequirements === 'object' && fileRequirements !== null) {
                    const keys = Object.keys(fileRequirements);
                    if (keys.length > 0 && typeof fileRequirements[keys[0]] === 'object') {
                        requirementsArray = Object.entries(fileRequirements).map(([fieldName, config]) => ({
                            field_name: fieldName.replace(/\s+/g, '_'),
                            display_name: fieldName,
                            is_required: config.required !== undefined ? config.required : true,
                            type: config.type || 'file',
                            description: config.description || ''
                        }));
                    } else {
                        requirementsArray = [fileRequirements];
                    }
                }
                
                // Create file upload fields for each requirement
                for (let i = 0; i < requirementsArray.length; i++) {
                    const requirement = requirementsArray[i];
                    
                    const fieldDiv = document.createElement('div');
                    fieldDiv.className = 'form-group mb-3';
                    
                    const fieldName = (requirement.field_name || requirement.document_type || 'unknown_' + i).toString();
                    const displayName = (requirement.display_name || requirement.description || requirement.field_name || requirement.document_type || 'Unknown Document').toString();
                    const isRequired = requirement.is_required !== undefined ? requirement.is_required : true;
                    const fileType = requirement.type || 'file';
                    
                    // Set appropriate file accept types
                    let acceptTypes = '.jpg,.jpeg,.png,.pdf';
                    if (fileType === 'image') {
                        acceptTypes = '.jpg,.jpeg,.png';
                    } else if (fileType === 'pdf') {
                        acceptTypes = '.pdf';
                    }
                    
                    fieldDiv.innerHTML = `
                        <label class="form-label" for="${fieldName}">
                            ${displayName} ${isRequired ? '<span class="text-danger">*</span>' : ''}
                        </label>
                        <input type="file" 
                               class="form-control" 
                               id="${fieldName}" 
                               name="${fieldName}" 
                               accept="${acceptTypes}"
                               ${isRequired ? 'required' : ''}>
                        ${requirement.description ? `<div class="form-text">${requirement.description}</div>` : ''}
                    `;
                    
                    requirementsContainer.appendChild(fieldDiv);
                }
            }
        } catch (error) {
            console.error('Error parsing file requirements:', error);
        }
    }
}

// Fix accessibility issues with modal
document.addEventListener('DOMContentLoaded', function() {
    const coursesModal = document.getElementById('coursesModal');
    if (coursesModal) {
        coursesModal.addEventListener('show.bs.modal', function () {
            // Remove aria-hidden when modal is opening
            coursesModal.removeAttribute('aria-hidden');
        });
        
        coursesModal.addEventListener('hidden.bs.modal', function () {
            // Add aria-hidden when modal is completely closed
            coursesModal.setAttribute('aria-hidden', 'true');
        });
    }
    
    const termsModal = document.getElementById('termsModal');
    if (termsModal) {
        termsModal.addEventListener('show.bs.modal', function () {
            // Remove aria-hidden when modal is opening
            termsModal.removeAttribute('aria-hidden');
        });
        
        termsModal.addEventListener('hidden.bs.modal', function () {
            // Add aria-hidden when modal is completely closed
            termsModal.setAttribute('aria-hidden', 'true');
        });
    }
});
</script>
@endpush