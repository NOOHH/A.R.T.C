@extends('layouts.navbar')

@section('title', 'Modular Enrollment - Multi-Step Form')
@section('hide_footer', true)
@section('body_class', 'registration-page')

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
        }
        
        /* Stepper styles */
        .stepper-progress {
            background: #f8f9fa;
            padding: 2rem;
            border-bottom: 1px solid #e9ecef;
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
        @media (max-width: 600px) {
            .package-card-pro { min-width: 90vw; max-width: 98vw; }
            .next-btn-pro { min-width: 90vw; }
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
                <div class="step" id="step-5">
                    <div class="circle">5</div>
                    <div class="label">Account</div>
                </div>
                <div class="step" id="step-6">
                    <div class="circle">6</div>
                    <div class="label">Form</div>
                </div>
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

        <!-- Step 5: Account Registration (Bootstrap Form) -->
        <div class="step-content" id="content-5">
            <div class="step-header">
                <h2><i class="bi bi-person-plus me-2"></i>Create Your Account</h2>
                <p>Please provide your account information to continue.</p>
            </div>
            <form id="accountRegistrationForm" class="needs-validation" novalidate>
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
                    <button type="button" onclick="nextStep()" id="step5-next" disabled class="btn btn-primary btn-lg">
                        NEXT: COMPLETE FORM <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Step 6: Final Registration Form (Bootstrap Form, similar to Full_enrollment) -->
        <div class="step-content" id="content-6">
            <div class="step-header">
                <h2>Complete Your Registration</h2>
                <p>Fill in your personal and academic information.</p>
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
                        I agree to the <a href="#" id="showTerms">Terms and Conditions</a>
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
<div class="modal fade" id="coursesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Courses in <span id="moduleNameDisplay"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="coursesContainer">
                    <!-- Courses will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Global variables
    let currentStep = 1;
    let totalSteps = 6;
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
        const form = document.getElementById('accountRegistrationForm');
        const nextBtn = document.getElementById('step5-next');
        if (!form || !nextBtn) return;

        function validateAccountForm() {
            const fname = form.user_firstname.value.trim();
            const lname = form.user_lastname.value.trim();
            const email = form.user_email.value.trim();
            const pass = form.password.value;
            const pass2 = form.password_confirmation.value;

            // Basic checks
            const valid = (
                fname.length > 0 &&
                lname.length > 0 &&
                /^[^@]+@[^@]+\.[^@]+$/.test(email) &&
                pass.length >= 8 &&
                pass === pass2
            );

            nextBtn.disabled = !valid;
        }

        // Listen for input changes
        ['input', 'change'].forEach(evt =>
            form.addEventListener(evt, validateAccountForm, true)
        );
    });
    
    // Step navigation
    function nextStep() {
        if (currentStep < totalSteps) {
            currentStep++;
            updateStepper();
            loadStepContent();
        }
    }
    
    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
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
    
    // Setup account form
    function setupAccountForm() {
        // This would load the appropriate account form based on selectedAccountType
        console.log('Setting up account form for type:', selectedAccountType);
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
    
    // Show courses modal
    function showCoursesModal(moduleId, moduleName) {
        document.getElementById('moduleNameDisplay').textContent = moduleName;
        
        const container = document.getElementById('coursesContainer');
        container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading courses...</div>';
        
        // Load courses for the module
        fetch(`/get-module-courses?module_id=${moduleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses) {
                    displayCourses(data.courses);
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
    
    // Display courses in modal
    function displayCourses(courses) {
        const container = document.getElementById('coursesContainer');
        
        if (!courses || courses.length === 0) {
            container.innerHTML = '<div class="alert alert-info">No courses available for this module.</div>';
            return;
        }
        
        let coursesHtml = '<div class="list-group">';
        courses.forEach(course => {
            coursesHtml += `
                <div class="list-group-item">
                    <h6 class="mb-1">${course.name || course.course_name || 'Unnamed Course'}</h6>
                    <p class="mb-1">${course.description || course.course_description || 'No description available'}</p>
                    <small class="text-muted">Duration: ${course.duration || 'Flexible'}</small>
                </div>
            `;
        });
        coursesHtml += '</div>';
        
        container.innerHTML = coursesHtml;
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
                <label>${label} ${req.is_required ? '<span class="required">*</span>' : ''}</label>
                <input type="file" name="${label.replace(/\s+/g, '_').toLowerCase()}" class="form-control" accept="${accept}" ${required}>
                <small class="form-text text-muted">Upload ${label} (${accept.replace(/\./g, '').toUpperCase()} only)</small>
            </div>`;
        });
        requirementsDiv.innerHTML = html;
        requirementsDiv.style.display = 'block';
    }

// --- BEGIN: Ensure all stepper data is copied to final form before submission ---

function copyStepperDataToFinalForm() {
    // Account info (step 5)
    const userFirstname = document.getElementById('user_firstname')?.value || '';
    const userLastname = document.getElementById('user_lastname')?.value || '';
    const userEmail = document.getElementById('user_email')?.value || '';
    const password = document.getElementById('password')?.value || '';
    const passwordConfirmation = document.getElementById('password_confirmation')?.value || '';
    const referralCode = document.getElementById('referral_code')?.value || '';

    // Package, program, modules, learning mode (steps 1-4)
    const packageId = document.getElementById('package_id')?.value || '';
    const programId = document.getElementById('program_id')?.value || '';
    const selectedModules = document.getElementById('selected_modules')?.value || '';
    const learningMode = document.getElementById('learning_mode')?.value || '';

    // Final form hidden fields
    const form = document.getElementById('modularEnrollmentForm');
    if (!form) return;

    // Set or create hidden fields for account info
    setOrCreateHidden(form, 'user_firstname', userFirstname);
    setOrCreateHidden(form, 'user_lastname', userLastname);
    setOrCreateHidden(form, 'email', userEmail);
    setOrCreateHidden(form, 'password', password);
    setOrCreateHidden(form, 'password_confirmation', passwordConfirmation);
    setOrCreateHidden(form, 'referral_code', referralCode);
    // Set or create hidden fields for stepper selections
    setOrCreateHidden(form, 'package_id', packageId);
    setOrCreateHidden(form, 'program_id', programId);
    setOrCreateHidden(form, 'selected_modules', selectedModules);
    setOrCreateHidden(form, 'learning_mode', learningMode);
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
        currentStep++;
        updateStepper();
        loadStepContent();
        // If moving to final step, copy all data
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
            copyStepperDataToFinalForm();
        });
    }
});

// --- END: Ensure all stepper data is copied to final form before submission ---
</script>
@endpush 