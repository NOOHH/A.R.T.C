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
     <link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Modular_enrollment.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>

    </style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-wrapper">
        <!-- Stepper Progress -->
        <div class="stepper-progress">
            <div class="stepper">
                <div class="bar">
                    <div class="progress" id="progressBar" style="width: {{ $isUserLoggedIn ? '20%' : '14.28%' }};"></div>
                </div>
                <div class="step {{ !$isUserLoggedIn ? 'active' : '' }}" id="step-1">
                    <div class="circle">1</div>
                    <div class="label">Account Check</div>
                </div>
                <div class="step {{ $isUserLoggedIn ? 'active' : '' }}" id="step-2">
                    <div class="circle">2</div>
                    <div class="label">Packages</div>
                </div>
                <div class="step" id="step-3">
                    <div class="circle">3</div>
                    <div class="label">Programs</div>
                </div>
                <div class="step" id="step-4">
                    <div class="circle">4</div>
                    <div class="label">Modules</div>
                </div>
                <div class="step" id="step-5">
                    <div class="circle">5</div>
                    <div class="label">Learning Mode</div>
                </div>
                @if(!$isUserLoggedIn)
                <div class="step" id="step-6">
                    <div class="circle">6</div>
                    <div class="label">Account</div>
                </div>
                <div class="step" id="step-7">
                    <div class="circle">7</div>
                    <div class="label">Form</div>
                </div>
                @else
                <div class="step" id="step-6">
                    <div class="circle">6</div>
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

        <!-- Step 1: Account Check -->
        <div class="step-content {{ !$isUserLoggedIn ? 'active' : '' }}" id="content-1">
            <div class="step-header mb-4">
                <h2 class="fw-bold text-center" style="font-size:2.5rem;">Welcome to Modular Enrollment</h2>
                <p class="text-center text-muted" style="font-size:1.15rem;">Let's get you started with your modular enrollment</p>
            </div>
            
            <div class="account-check-container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="question-section text-center mb-4">
                            <h4 class="fw-bold mb-3">Do you already have an account with us?</h4>
                            <p class="text-muted">Choose the option that applies to you</p>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="account-option-card" onclick="selectAccountOption(true)">
                                    <div class="card h-100 p-4 border-2" style="cursor: pointer; transition: all 0.3s ease;">
                                        <div class="card-body text-center">
                                            <div class="icon-container mb-3">
                                                <i class="fas fa-user-check" style="font-size: 3rem; color: #28a745;"></i>
                                            </div>
                                            <h5 class="card-title fw-bold mb-3">Yes, I have an account</h5>
                                            <p class="card-text text-muted mb-4">I already registered before and want to log in to my existing account</p>
                                            <div class="features-list text-start">
                                                <small class="text-muted">
                                                    <i class="fas fa-check text-success me-2"></i>Access your previous information<br>
                                                    <i class="fas fa-check text-success me-2"></i>Continue existing enrollments<br>
                                                    <i class="fas fa-check text-success me-2"></i>View your enrollment history
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="account-option-card" onclick="selectAccountOption(false)">
                                    <div class="card h-100 p-4 border-2" style="cursor: pointer; transition: all 0.3s ease;">
                                        <div class="card-body text-center">
                                            <div class="icon-container mb-3">
                                                <i class="fas fa-user-plus" style="font-size: 3rem; color: #007bff;"></i>
                                            </div>
                                            <h5 class="card-title fw-bold mb-3">No, I'm new here</h5>
                                            <p class="card-text text-muted mb-4">I'm enrolling for the first time and need to create a new account</p>
                                            <div class="features-list text-start">
                                                <small class="text-muted">
                                                    <i class="fas fa-check text-success me-2"></i>Create a new account<br>
                                                    <i class="fas fa-check text-success me-2"></i>Start fresh enrollment<br>
                                                    <i class="fas fa-check text-success me-2"></i>Get started immediately
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Package Selection (Bootstrap Carousel) -->
        <div class="step-content {{ $isUserLoggedIn ? 'active' : '' }}" id="content-2">
            <div class="step-header mb-4">
                <h2 class="fw-bold text-center" style="font-size:2.5rem;">Choose Your Package</h2>
                <p class="text-center text-muted" style="font-size:1.15rem;">Select a learning package that suits your needs</p>
            </div>
            
            <!-- Bootstrap Carousel for Packages -->
            <div id="packageCarousel" class="carousel slide package-carousel-container" data-bs-ride="false">
                <div class="carousel-inner">
                    @php $chunkSize = 2; @endphp
                    @foreach($packages->chunk($chunkSize) as $index => $packageChunk)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <div class="d-flex justify-content-center gap-4 flex-wrap">
                                @foreach($packageChunk as $package)
                                    <div class="package-card-pro card p-4 mb-3"
                                         onclick="selectPackage({{ $package->package_id }}, {{ $package->program_id }}, {{ $package->module_count ?? $package->modules_count ?? 3 }}, '{{ $package->selection_mode ?? 'modules' }}', {{ $package->course_count ?? 0 }})"
                                         data-package-id="{{ $package->package_id }}">
                                        <div class="card-body text-center">
                                            <h4 class="fw-bold mb-2">{{ $package->package_name }}</h4>
                                            <div class="text-primary fw-bold" style="font-size:2rem;">₱{{ number_format($package->amount, 2) }}</div>
                                            <p class="text-muted mb-3" style="min-height:2rem;">{{ $package->description ?? 'No description yet.' }}</p>
                                            <ul class="list-unstyled text-start mx-auto" style="max-width:220px;">
                                                @if($package->selection_mode === 'courses')
                                                    <li><i class="bi bi-check2 text-success"></i> {{ $package->course_count ?? 'All' }} courses included</li>
                                                @else
                                                    <li><i class="bi bi-check2 text-success"></i> {{ $package->module_count ?? $package->modules_count ?? 3 }} modules included</li>
                                                @endif
                                                <li><i class="bi bi-check2 text-success"></i> Self-paced learning</li>
                                                <li><i class="bi bi-check2 text-success"></i> Certificate upon completion</li>
                                                <li><i class="bi bi-check2 text-success"></i> Flexible scheduling</li>
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Carousel Controls -->
                @if($packages->count() > $chunkSize)
                    <button class="carousel-control-prev" type="button" data-bs-target="#packageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#packageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
                
                <!-- Carousel Indicators -->
                @if($packages->chunk($chunkSize)->count() > 1)
                    <div class="carousel-indicators">
                        @foreach($packages->chunk($chunkSize) as $index => $chunk)
                            <button type="button" data-bs-target="#packageCarousel" data-bs-slide-to="{{ $index }}" 
                                    class="{{ $index == 0 ? 'active' : '' }}" aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Immediate Script for Package Selection -->
            <script>
                // Package selection function
                function selectPackage(packageId, programId, moduleCount, selectionMode = 'modules', courseCount = 0) {
                    console.log('Package selected:', { packageId, programId, moduleCount, selectionMode, courseCount });
                    
                    // Remove selection from all cards
                    document.querySelectorAll('.package-card-pro').forEach(card => {
                        card.classList.remove('selected');
                    });
                    
                    // Add selection to clicked card
                    event.target.closest('.package-card-pro').classList.add('selected');
                    
                    // Store selection
                    selectedPackageId = packageId;
                    selectedProgramId = programId; // Also store program ID
                    packageSelectionMode = selectionMode;
                    
                    if (selectionMode === 'courses') {
                        packageCourseLimit = courseCount;
                        packageModuleLimit = null; // No module limit for course-based packages
                    } else {
                        packageModuleLimit = moduleCount;
                        packageCourseLimit = null; // No course limit for module-based packages
                    }
                    
                    // Update hidden inputs - CRITICAL: Set both package_id AND program_id
                    if (document.getElementById('package_id')) {
                        document.getElementById('package_id').value = packageId;
                        console.log('Set package_id to:', packageId);
                    }
                    if (document.getElementById('program_id')) {
                        document.getElementById('program_id').value = programId;
                        console.log('Set program_id to:', programId);
                    }
                    // Also update the final form hidden inputs
                    if (document.getElementById('packageIdInput')) {
                        document.getElementById('packageIdInput').value = packageId;
                    }
                    if (document.getElementById('hidden_program_id')) {
                        document.getElementById('hidden_program_id').value = programId;
                    }
                    
                    // Enable next button
                    const nextBtn = document.getElementById('step1-next');
                    if (nextBtn) {
                        nextBtn.disabled = false;
                    }
                }
                
                // Make function globally available
                window.selectPackage = selectPackage;
            </script>
            
            <div class="d-flex justify-content-center mt-4">
                <button type="button" class="btn btn-lg btn-primary next-btn-pro" onclick="nextStep()" disabled id="step1-next">
                    NEXT: SELECT PROGRAM <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Program Selection (Bootstrap Carousel) -->
        <div class="step-content" id="content-3">
            <div class="step-header">
                <h2>Select Your Program*</h2>
                <p>Choose the program that aligns with your career goals</p>
            </div>
            
            <!-- Bootstrap Carousel for Programs -->
            <div id="programCarousel" class="carousel slide program-carousel-container" data-bs-ride="false">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row" id="programsGrid">
                            <!-- Programs will be loaded here -->
                        </div>
                    </div>
                </div>
                
                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#programCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#programCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                
                <!-- Carousel Indicators -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#programCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                </div>
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

        <!-- Step 4: Module Selection (Bootstrap Carousel) -->
        <div class="step-content" id="content-4">
            <div class="step-header">
                <h2>Select Your Modules</h2>
                <p>Choose the modules you want to enroll in (up to <span id="moduleLimit">3</span> modules)</p>
            </div>
            
            <!-- Bootstrap Carousel for Modules -->
            <div id="moduleCarousel" class="carousel slide module-carousel-container" data-bs-ride="false">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row" id="modulesGrid">
                            <!-- Modules will be loaded here -->
                        </div>
                    </div>
                </div>
                
                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#moduleCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#moduleCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                
                <!-- Carousel Indicators -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#moduleCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                </div>
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

        <!-- Step 5: Learning Mode Selection (Bootstrap cards) -->
        <div class="step-content" id="content-5">
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

        <!-- Step 6: Account Registration (Enhanced with OTP and Referral validation) - Only show for non-logged-in users -->
        @if(!$isUserLoggedIn)
        <div class="step-content" id="content-6">
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
                    <button type="button" onclick="nextStep()" id="step6NextBtn" disabled class="btn btn-primary btn-lg">
                        Next <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Step 7: Final Registration Form (for non-logged-in users) OR Step 6: Final Registration Form (for logged-in users) -->
        <div class="step-content" id="content-{{ $isUserLoggedIn ? '6' : '7' }}">
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
                    <label for="programSelect" style="font-size:1.17rem;font-weight:700;"><i class="bi bi-book me-2"></i>Program <span class="text-danger">*</span></label>
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
                {!! nl2br(e(\App\Models\AdminSetting::getValue('modular_enrollment_terms', 'Terms and Conditions for Modular Enrollment:

1. Module-Based Learning
You are enrolling in selected modules of our program. Each module is a standalone unit with its own requirements.

2. Flexible Schedule
Modular enrollment allows you to complete modules at your own pace within the specified timeframes.

3. Payment Terms
Payment is required per module or package selected. Payment plans may be available for multiple modules.

4. Module Completion
You must complete all activities and assessments within each enrolled module to receive certification.

5. Prerequisites
Some modules may have prerequisites. Please ensure you meet all requirements before enrolling.

6. Module Access
Access to module materials is granted upon payment confirmation and remains active for the duration specified.

7. Certification
Certificates are awarded upon successful completion of each module. Full program certification requires completion of all core modules.

8. Refund Policy
Module-specific refund policies apply. Please review the refund terms for each module before enrollment.

By proceeding with modular enrollment, you acknowledge that you have read, understood, and agree to these terms and conditions.'))) !!}
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
    // Global variables - consolidated to avoid temporal dead zone issues
    let currentStep = {{ $isUserLoggedIn ? 2 : 1 }}; // Start at step 2 for logged-in users, step 1 for new users
    let totalSteps = {{ $isUserLoggedIn ? 6 : 7 }}; // Updated total steps: 1 (Account Check) + 5 original steps
    let isUserLoggedIn = @json($isUserLoggedIn);
    
    // Package selection variables (moved from earlier script block)
    let selectedPackageId = null;
    let packageSelectionMode = 'modules';
    let packageModuleLimit = null;
    let packageCourseLimit = null;
    
    // Other form variables
    let selectedProgramId = null;
    let selectedModules = [];
    let selectedLearningMode = null;
    let selectedAccountType = null;
    
    // Course selection variables
    let currentModuleId = null;
    let selectedCourses = {};
    let extraModulePrice = 0;
    
    // CSRF token
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Initialize the form
    document.addEventListener('DOMContentLoaded', function() {
        updateStepper();
        loadStepContent();

        // Account Registration Step validation (updated for new step structure)
        const nextBtn = document.getElementById('step6NextBtn'); // Updated ID for step 6
        if (nextBtn) {
            // Initial validation
            validateStep6(); // Updated function name
        }
    });
    
    // Account selection function for step 1
    function selectAccountOption(hasAccount) {
        console.log('Account option selected:', hasAccount ? 'has account' : 'no account');
        
        if (hasAccount) {
            // Redirect to login page
            window.location.href = "{{ route('login') }}";
            return;
        } else {
            // Continue to step 2 (packages)
            console.log('Continuing to package selection');
            currentStep = 2;
            updateStepper();
            loadStepContent();
        }
    }
    
    // Step navigation
    function nextStep() {
        if (currentStep < totalSteps) {
            // Handle step transitions based on new structure
            if (currentStep === 1) {
                // This should not be reached since step 1 uses selectAccountOption
                console.log('Step 1 should use selectAccountOption');
                return;
            } else if (isUserLoggedIn && currentStep === 5) {
                // Skip from step 5 (learning mode) directly to step 6 (form) for logged-in users
                currentStep = 6;
            } else if (!isUserLoggedIn && currentStep === 6) {
                // For non-logged-in users, go from step 6 (account) to step 7 (form)
                currentStep = 7;
                copyStepperDataToFinalForm(); // Copy account data to final form
            } else {
                // Normal progression
                currentStep++;
            }
            
            // Copy data when moving to final step
            if ((isUserLoggedIn && currentStep === 6) || (!isUserLoggedIn && currentStep === 7)) {
                copyStepperDataToFinalForm();
            }
            
            updateStepper();
            loadStepContent();
        }
    }
    
    function prevStep() {
        if (currentStep > 1) {
            // Handle stepping back based on new structure
            if (isUserLoggedIn && currentStep === 6) {
                // Go back from step 6 (form) to step 5 (learning mode) for logged-in users
                currentStep = 5;
            } else if (isUserLoggedIn && currentStep === 2) {
                // Don't go back to step 1 (account check) for logged-in users
                return;
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
            const stepElement = document.getElementById(`step-${i}`);
            const contentElement = document.getElementById(`content-${i}`);
            
            if (stepElement) {
                if (i < currentStep) {
                    stepElement.classList.add('completed');
                    stepElement.classList.remove('active');
                } else if (i === currentStep) {
                    stepElement.classList.add('active');
                    stepElement.classList.remove('completed');
                } else {
                    stepElement.classList.remove('active', 'completed');
                }
            }

            if (contentElement) {
                if (i === currentStep) {
                    contentElement.classList.add('active');
                } else {
                    contentElement.classList.remove('active');
                }
            }
        }
        
        // Update progress bar
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        const progressBar = document.getElementById('progressBar');
        if (progressBar) {
            progressBar.style.width = progress + '%';
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
    
    // Load programs from the database using the /api/programs endpoint
    function loadPrograms() {
        const grid = document.getElementById('programsGrid');
        grid.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin fa-2x"></i> Loading programs...</div>';
        fetch('/api/programs', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        })
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                // Some endpoints return array directly
                displayPrograms(data);
            } else if (data.success && (data.data || data.programs)) {
                displayPrograms(data.data || data.programs);
            } else {
                grid.innerHTML = '<div class="alert alert-info">No programs available. Please contact the administrator.</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching programs:', error);
            grid.innerHTML = '<div class="alert alert-danger">Error loading programs. Please try again.</div>';
        });
    }
    // Display programs in the grid (update to show real data)
    function displayPrograms(programs) {
        const grid = document.getElementById('programsGrid');
        if (!programs || programs.length === 0) {
            grid.innerHTML = '<div class="alert alert-info">No programs available. Please contact the administrator.</div>';
            return;
        }
        
        // Clear existing content
        const carouselInner = document.querySelector('#programCarousel .carousel-inner');
        carouselInner.innerHTML = '';
        
        // Group programs into chunks for carousel slides (2 programs per slide)
        const chunkSize = 2;
        const programChunks = [];
        for (let i = 0; i < programs.length; i += chunkSize) {
            programChunks.push(programs.slice(i, i + chunkSize));
        }
        
        // Create carousel slides
        programChunks.forEach((chunk, index) => {
            const isActive = index === 0 ? 'active' : '';
            let slideHtml = `<div class="carousel-item ${isActive}">
                <div class="row justify-content-center">`;
                
            chunk.forEach(program => {
                slideHtml += `
                    <div class="col-md-5 mb-4">
                        <div class="card selection-card h-100 ${program.program_id == selectedProgramId ? 'selected' : ''}" 
                             onclick="selectProgram(${program.program_id})" style="cursor:pointer;">
                            <div class="card-body">
                                <h4 class="card-title">${program.program_name}</h4>
                                <p class="card-text">${program.program_description || 'No description available.'}</p>
                                <div id="modules-for-program-${program.program_id}"></div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            slideHtml += `</div></div>`;
            carouselInner.innerHTML += slideHtml;
        });
        
        // Update carousel indicators if there are multiple slides
        const indicatorsContainer = document.querySelector('#programCarousel .carousel-indicators');
        if (programChunks.length > 1) {
            indicatorsContainer.innerHTML = '';
            programChunks.forEach((chunk, index) => {
                const isActive = index === 0 ? 'active' : '';
                indicatorsContainer.innerHTML += `
                    <button type="button" data-bs-target="#programCarousel" data-bs-slide-to="${index}" 
                            class="${isActive}" aria-label="Slide ${index + 1}"></button>
                `;
            });
            indicatorsContainer.style.display = 'block';
            
            // Show/hide carousel controls
            document.querySelector('#programCarousel .carousel-control-prev').style.display = 'block';
            document.querySelector('#programCarousel .carousel-control-next').style.display = 'block';
        } else {
            indicatorsContainer.style.display = 'none';
            document.querySelector('#programCarousel .carousel-control-prev').style.display = 'none';
            document.querySelector('#programCarousel .carousel-control-next').style.display = 'none';
        }
        
        // Optionally, fetch and display modules for each program
        programs.forEach(program => {
            fetch(`/api/programs/${program.program_id}/modules`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.modules && data.modules.length > 0) {
                        const modulesDiv = document.getElementById(`modules-for-program-${program.program_id}`);
                        if (modulesDiv) {
                            let modulesHtml = '<ul class="small text-muted">';
                            data.modules.slice(0, 3).forEach(module => { // Show only first 3 modules
                                modulesHtml += `<li>${module.module_name}</li>`;
                            });
                            if (data.modules.length > 3) {
                                modulesHtml += `<li class="text-muted">...and ${data.modules.length - 3} more</li>`;
                            }
                            modulesHtml += '</ul>';
                            modulesDiv.innerHTML = modulesHtml;
                        }
                    }
                });
        });
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
        const carousel = document.getElementById('moduleCarousel');
        const carouselInner = carousel.querySelector('.carousel-inner');
        const indicators = carousel.querySelector('.carousel-indicators');
        
        if (!modules || modules.length === 0) {
            carouselInner.innerHTML = '<div class="carousel-item active"><div class="alert alert-info">No modules available for this program.</div></div>';
            indicators.innerHTML = '';
            return;
        }
        
        // Clear existing content
        carouselInner.innerHTML = '';
        indicators.innerHTML = '';
        
        // Group modules into slides (2 modules per slide)
        const modulesPerSlide = 2;
        const slides = [];
        for (let i = 0; i < modules.length; i += modulesPerSlide) {
            slides.push(modules.slice(i, i + modulesPerSlide));
        }
        
        // Create carousel slides
        slides.forEach((slideModules, slideIndex) => {
            const isActive = slideIndex === 0 ? 'active' : '';
            
            let modulesHtml = '';
            slideModules.forEach(module => {
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
            
            const slideHtml = `
                <div class="carousel-item ${isActive}">
                    <div class="row" id="modulesGrid${slideIndex === 0 ? '' : slideIndex}">
                        ${modulesHtml}
                    </div>
                </div>
            `;
            
            carouselInner.innerHTML += slideHtml;
            
            // Create indicator
            const indicator = `
                <button type="button" data-bs-target="#moduleCarousel" data-bs-slide-to="${slideIndex}" 
                        class="${isActive}" ${isActive ? 'aria-current="true"' : ''} aria-label="Slide ${slideIndex + 1}"></button>
            `;
            indicators.innerHTML += indicator;
        });
        
        // If only one slide, hide carousel controls and indicators
        const controls = carousel.querySelectorAll('.carousel-control-prev, .carousel-control-next');
        if (slides.length <= 1) {
            controls.forEach(control => control.style.display = 'none');
            indicators.style.display = 'none';
        } else {
            controls.forEach(control => control.style.display = 'block');
            indicators.style.display = 'flex';
        }
    }
    
    // Handle module selection
    function handleModuleSelection(checkbox) {
        const moduleId = checkbox.value;
        const moduleCard = checkbox.closest('.module-card');
        const moduleTitle = moduleCard.querySelector('.module-title').textContent;
        
        if (checkbox.checked) {
            // Check limits based on selection mode
            if (packageSelectionMode === 'modules' && packageModuleLimit && selectedModules.length >= packageModuleLimit) {
                alert(`You can only select up to ${packageModuleLimit} modules.`);
                checkbox.checked = false;
                return;
            } else if (packageSelectionMode === 'courses' && packageCourseLimit) {
                // For course-based packages, we need to check total course count, not module count
                // This will be handled when courses are selected, so allow module selection
            }
            
            // Add to selection with course information if available
            const moduleData = {
                id: moduleId,
                name: moduleTitle
            };
            
            // Check if this module has course selections
            if (selectedCourses[moduleId] && selectedCourses[moduleId].length > 0) {
                moduleData.selected_courses = selectedCourses[moduleId];
            }
            
            selectedModules.push(moduleData);
            moduleCard.classList.add('selected');
        } else {
            // Remove from selection
            selectedModules = selectedModules.filter(m => m.id !== moduleId);
            moduleCard.classList.remove('selected');
            
            // Also remove any course selections for this module
            if (selectedCourses[moduleId]) {
                delete selectedCourses[moduleId];
            }
        }
        
        // Update modules with current course selections
        updateSelectedModulesWithCourses();
        
        // Enable/disable next button based on selection mode
        if (packageSelectionMode === 'courses') {
            // For course-based packages, check total course count across all modules
            let totalSelectedCourses = 0;
            Object.values(selectedCourses).forEach(courses => {
                totalSelectedCourses += courses.length;
            });
            document.getElementById('step3-next').disabled = totalSelectedCourses === 0 || (packageCourseLimit && totalSelectedCourses < packageCourseLimit);
        } else {
            // For module-based packages, check module count
            document.getElementById('step3-next').disabled = selectedModules.length === 0;
        }
        
        console.log('Selected modules with courses:', selectedModules);
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
        console.log('Setting up account form for step 6'); // Updated step number
        
        @if($isUserLoggedIn)
        // If user is logged in, skip account data collection and use existing user data
        console.log('User is logged in, skipping account data collection');
        return;
        @endif
        
        // Add validation listeners for Step 6 (updated from Step 5)
        const firstnameField = document.getElementById('user_firstname');
        const lastnameField = document.getElementById('user_lastname');
        const emailField = document.getElementById('user_email');
        const passwordField = document.getElementById('password');
        const passwordConfirmField = document.getElementById('password_confirmation');
        const nextBtn = document.getElementById('step6NextBtn'); // Updated button ID
        
        // Debounce timer for validation
        let validationTimer = null;
        
        function debouncedValidateStep6() { // Updated function name
            clearTimeout(validationTimer);
            validationTimer = setTimeout(validateStep6, 500); // Updated function call
        }
        
        if (firstnameField) firstnameField.addEventListener('input', debouncedValidateStep6);
        if (lastnameField) lastnameField.addEventListener('input', debouncedValidateStep6);
        if (emailField) {
            emailField.addEventListener('input', function() {
                setTimeout(validateEmail, 300);
                debouncedValidateStep6();
            });
        }
        if (passwordField) {
            passwordField.addEventListener('input', function() {
                setTimeout(validatePassword, 50);
                debouncedValidateStep6();
            });
        }
        if (passwordConfirmField) {
            passwordConfirmField.addEventListener('input', function() {
                setTimeout(validatePasswordConfirmation, 50);
                debouncedValidateStep6();
            });
        }
    }

    // Validate Step 6 (Account Registration) - Updated from Step 5
    function validateStep6() { // Updated function name
        const firstname = document.getElementById('user_firstname')?.value.trim() || '';
        const lastname = document.getElementById('user_lastname')?.value.trim() || '';
        const email = document.getElementById('user_email')?.value.trim() || '';
        const password = document.getElementById('password')?.value || '';
        const passwordConfirm = document.getElementById('password_confirmation')?.value || '';
        const referralCode = document.getElementById('referral_code')?.value.trim() || '';
        const nextBtn = document.getElementById('step6NextBtn'); // Updated button ID

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
        .then(async response => {
            let data;
            try {
                data = await response.json();
            } catch (e) {
                throw new Error('Server error: Invalid response format.');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Show OTP modal
                document.getElementById('otpTargetEmail').textContent = email;
                const otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
                otpModal.show();
                setupOTPInputHandlers();
                if (emailError) emailError.style.display = 'none';
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
                    
                    // Re-validate step 6 (updated from step 5)
                    validateStep6();
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
        .then(async response => {
            let data;
            try {
                data = await response.json();
            } catch (e) {
                throw new Error('Server error: Invalid response format.');
            }
            return data;
        })
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
            validateStep6();
        })
        .catch(error => {
            console.error('Error validating referral code:', error);
            if (errorDiv) {
                errorDiv.textContent = 'Validation failed. Please try again.';
                errorDiv.style.display = 'block';
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
        console.log('Loading dynamic form fields');
        
        @if($isUserLoggedIn)
        // Auto-fill user data for logged-in users
        console.log('User is logged in, skipping account data collection');
        
        // Auto-fill firstname/lastname fields in the form
        const firstnameField = document.querySelector('input[name="firstname"]');
        const lastnameField = document.querySelector('input[name="lastname"]');
        
        if (firstnameField && !firstnameField.value) {
            firstnameField.value = '{{ $loggedInUser->user_firstname ?? "" }}';
        }
        if (lastnameField && !lastnameField.value) {
            lastnameField.value = '{{ $loggedInUser->user_lastname ?? "" }}';
        }
        
        // Auto-fill program selection if we have the program from stepper data
        const programSelect = document.getElementById('programSelect');
        if (programSelect && selectedProgramId) {
            programSelect.value = selectedProgramId;
            onProgramSelectionChange(); // Trigger any dependent logic
        }
        @endif
    }
    
    // Copy stepper data to final form
    function copyStepperDataToFinalForm() {
        @if($isUserLoggedIn)
        console.log('User is logged in, skipping account data collection');
        @else
        console.log('User is not logged in, copying account data');
        @endif
        
        console.log('🔍 FORM DATA DEBUG:', {
            selectedPackageId,
            selectedProgramId,
            selectedModules,
            selectedLearningMode,
            packageSelectionMode,
            selectedCourses
        });
        
        // Update hidden inputs with current selections
        const packageIdInput = document.getElementById('packageIdInput');
        const programIdInput = document.getElementById('hidden_program_id');
        const selectedModulesInput = document.getElementById('selected_modules');
        const learningModeInput = document.getElementById('learning_mode');
        const educationLevelInput = document.getElementById('education_level');
        
        // Create hidden inputs if they don't exist
        if (!packageIdInput) {
            createHiddenInput('package_id', selectedPackageId, 'packageIdInput');
            console.log('✅ Created hidden input: package_id');
        } else {
            packageIdInput.value = selectedPackageId;
        }
        console.log(`🔧 Set package_id = ${selectedPackageId} (type: ${typeof selectedPackageId})`);
        
        if (!programIdInput) {
            createHiddenInput('program_id', selectedProgramId, 'hidden_program_id');
            console.log('✅ Created hidden input: program_id');
        } else {
            programIdInput.value = selectedProgramId;
        }
        console.log(`🔧 Set program_id = ${selectedProgramId} (type: ${typeof selectedProgramId})`);
        
        if (!selectedModulesInput) {
            createHiddenInput('selected_modules', JSON.stringify(selectedModules), 'selected_modules');
            console.log('✅ Created hidden input: selected_modules');
        } else {
            selectedModulesInput.value = JSON.stringify(selectedModules);
        }
        console.log(`🔧 Set selected_modules = ${JSON.stringify(selectedModules)} (type: ${typeof JSON.stringify(selectedModules)})`);
        
        if (!learningModeInput) {
            createHiddenInput('learning_mode', selectedLearningMode, 'learning_mode');
            console.log('✅ Created hidden input: learning_mode');
        } else {
            learningModeInput.value = selectedLearningMode;
        }
        console.log(`🔧 Set learning_mode = ${selectedLearningMode} (type: ${typeof selectedLearningMode})`);
        
        // Set education level if it exists but is empty
        if (educationLevelInput && !educationLevelInput.value) {
            const educationLevelSelect = document.getElementById('educationLevel');
            if (educationLevelSelect && educationLevelSelect.value) {
                educationLevelInput.value = educationLevelSelect.value;
                console.log(`🔧 Set education_level = ${educationLevelSelect.value} (type: ${typeof educationLevelSelect.value})`);
            } else {
                createHiddenInput('education_level', '', 'education_level');
                console.log('✅ Created hidden input: education_level');
                console.log(`🔧 Set education_level =  (type: ${typeof ''})`);
            }
        }
        
        @if(!$isUserLoggedIn)
        // Copy account data for non-logged-in users
        const accountData = {
            user_firstname: document.getElementById('user_firstname')?.value || '',
            user_lastname: document.getElementById('user_lastname')?.value || '',
            email: document.getElementById('user_email')?.value || '',
            password: document.getElementById('password')?.value || '',
            password_confirmation: document.getElementById('password_confirmation')?.value || ''
        };
        
        // Add account data to form
        Object.keys(accountData).forEach(key => {
            if (accountData[key] && !document.querySelector(`input[name="${key}"]`)) {
                createHiddenInput(key, accountData[key]);
            }
        });
        @endif
    }
    
    // Helper function to create hidden inputs
    function createHiddenInput(name, value, id = null) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value || '';
        if (id) input.id = id;
        
        // Add to the form
        const form = document.getElementById('modularEnrollmentForm');
        if (form) {
            form.appendChild(input);
        }
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
            // Check course limit for course-based packages
            if (packageSelectionMode === 'courses' && packageCourseLimit) {
                let totalSelectedCourses = 0;
                Object.values(selectedCourses).forEach(courses => {
                    totalSelectedCourses += courses.length;
                });
                
                if (totalSelectedCourses >= packageCourseLimit) {
                    alert(`You can only select up to ${packageCourseLimit} courses.`);
                    checkbox.checked = false;
                    return;
                }
            }
            
            // Add course to selection
            selectedCourses[moduleId].push(courseId);
        } else {
            // Remove course from selection
            selectedCourses[moduleId] = selectedCourses[moduleId].filter(id => id !== courseId);
        }
        
        // Update visual feedback
        updateExtraChargesDisplay();
        
        // Update next button state
        updateStep3NextButton();
        
        console.log('Course selection updated:', selectedCourses);
    }
    window.handleCourseSelection = handleCourseSelection;
    
    // Update step 3 next button based on selection mode
    function updateStep3NextButton() {
        if (packageSelectionMode === 'courses') {
            // For course-based packages, check total course count across all modules
            let totalSelectedCourses = 0;
            Object.values(selectedCourses).forEach(courses => {
                totalSelectedCourses += courses.length;
            });
            document.getElementById('step3-next').disabled = totalSelectedCourses === 0 || (packageCourseLimit && totalSelectedCourses < packageCourseLimit);
        } else {
            // For module-based packages, check module count
            document.getElementById('step3-next').disabled = selectedModules.length === 0;
        }
    }
    
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
        
        // CRITICAL FIX: Ensure the module exists in selectedModules array
        const moduleIndex = selectedModules.findIndex(m => m.id == moduleId);
        const moduleTitle = moduleCard ? moduleCard.querySelector('.module-title')?.textContent || `Module ${moduleId}` : `Module ${moduleId}`;
        
        if (moduleIndex === -1) {
            // Module doesn't exist in selectedModules, add it
            const moduleData = {
                id: moduleId,
                name: moduleTitle,
                selected_courses: selectedCourses[moduleId]
            };
            selectedModules.push(moduleData);
            console.log('✅ Added module to selectedModules:', moduleData);
        } else {
            // Module exists, update it with course selections
            selectedModules[moduleIndex].selected_courses = selectedCourses[moduleId];
            console.log('✅ Updated existing module with courses:', selectedModules[moduleIndex]);
        }
        
        // Update the selected_modules data to include course selections
        updateSelectedModulesWithCourses();
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('coursesModal'));
        modal.hide();
        
        console.log('Course selection saved for module', moduleId, ':', selectedCourses[moduleId]);
        console.log('Updated selected modules:', selectedModules);
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
        
        // Update the hidden input field
        document.getElementById('selected_modules').value = JSON.stringify(modulesWithCourses);
        console.log('Updated modules with course selections:', modulesWithCourses);
        
        return modulesWithCourses;
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
            const requiredClass = req.is_required ? 'border-warning' : '';
            let accept = '';
            switch (req.file_type) {
                case 'image': accept = '.jpg,.jpeg,.png,.gif'; break;
                case 'pdf': accept = '.pdf'; break;
                case 'document': accept = '.pdf,.doc,.docx'; break;
                default: accept = '*'; break;
            }
            html += `<div class="form-group mb-3">
                <label class="form-label fw-bold">${label} ${req.is_required ? '<span class="text-danger">*</span>' : '<span class="text-muted">(Optional)</span>'}</label>
                <input type="file" name="${label.replace(/\s+/g, '_').toLowerCase()}" class="form-control ${requiredClass}" accept="${accept}" ${required} onchange="handleFileUpload(this)">
                <div class="form-text">
                    <i class="fas fa-info-circle text-info me-1"></i>
                    Upload ${label} (${accept.replace(/\./g, '').toUpperCase()} files only, max 10MB)
                    ${req.is_required ? '<br><small class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>This file is required for your education level</small>' : ''}
                </div>
            </div>`;
        });
        requirementsDiv.innerHTML = html;
        requirementsDiv.style.display = 'block';
    }

    // Enhanced form submission with better validation
    document.getElementById('modularEnrollmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        @if($isUserLoggedIn)
        console.log('User is logged in, skipping account data collection');
    }

    // Package, program, modules, learning mode (steps 1-4)
    const packageId = document.getElementById('package_id')?.value || '';
    const programId = document.getElementById('program_id')?.value || '';
    const selectedModules = document.getElementById('selected_modules')?.value || '';
    const learningMode = document.getElementById('learning_mode')?.value || '';
    const educationLevel = document.getElementById('educationLevel')?.value || ''; // Get education level

    console.log('🔍 FORM DATA DEBUG:', {
        packageId, 
        programId, 
        selectedModules, 
        learningMode,
        educationLevel,
        hasPackageId: !!packageId,
        hasProgramId: !!programId,
        hasEducationLevel: !!educationLevel
    });

    // Final form hidden fields
    const form = document.getElementById('modularEnrollmentForm');
    if (!form) {
        console.error('❌ Form not found: modularEnrollmentForm');
        return;
    }

    // Set or create hidden fields for account info (only for non-logged-in users)
    if (!isUserLoggedIn) {
        setOrCreateHidden(form, 'user_firstname', userFirstname);
        setOrCreateHidden(form, 'user_lastname', userLastname);
        setOrCreateHidden(form, 'email', userEmail);
        setOrCreateHidden(form, 'password', password);
        setOrCreateHidden(form, 'password_confirmation', passwordConfirmation);
        setOrCreateHidden(form, 'referral_code', referralCode);
        
        // Also populate the dynamic form fields if they exist
        const firstnameField = form.querySelector('input[name="firstname"]');
        if (firstnameField) {
            firstnameField.value = userFirstname;
            console.log('✅ Populated firstname field with:', userFirstname);
        } else {
            console.log('⚠️ No firstname field found in form');
        }
        
        const lastnameField = form.querySelector('input[name="lastname"]');
        if (lastnameField) {
            lastnameField.value = userLastname;
            console.log('✅ Populated lastname field with:', userLastname);
        } else {
            console.log('⚠️ No lastname field found in form');
        }
        
        // Also try alternate field names (first_name, last_name)
        const firstNameField = form.querySelector('input[name="first_name"]');
        if (firstNameField) {
            firstNameField.value = userFirstname;
            console.log('✅ Populated first_name field with:', userFirstname);
        }
        
        const lastNameField = form.querySelector('input[name="last_name"]');
        if (lastNameField) {
            lastNameField.value = userLastname;
            console.log('✅ Populated last_name field with:', userLastname);
        }
        
        // Debug: Show all available name-related fields in the form
        const allNameFields = form.querySelectorAll('input[name*="name"], input[name*="Name"]');
        console.log('🔍 All name-related fields found in form:', Array.from(allNameFields).map(f => f.name));
    }
    
    // Set or create hidden fields for stepper selections - CRITICAL: These must have valid database IDs
    setOrCreateHidden(form, 'package_id', packageId);
    setOrCreateHidden(form, 'program_id', programId);
    setOrCreateHidden(form, 'selected_modules', selectedModules);
    setOrCreateHidden(form, 'learning_mode', learningMode);
    setOrCreateHidden(form, 'education_level', educationLevel); // Ensure education_level is set
    
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
        console.log(`✅ Created hidden input: ${name}`);
    }
    input.value = value;
    console.log(`🔧 Set ${name} = ${value} (type: ${typeof value})`);
    
    // Validate critical fields
    if ((name === 'package_id' || name === 'program_id') && (!value || value === '')) {
        console.error(`❌ CRITICAL: ${name} is empty! This will cause validation to fail.`);
    }
}

// Hook into step navigation and form submission
function nextStep() {
    if (currentStep < totalSteps) {
        // Copy data when leaving account registration step
        if (!isUserLoggedIn && currentStep === 6) {
            // For non-logged-in users: step 6 is account registration
            copyStepperDataToFinalForm();
        } else if (isUserLoggedIn && currentStep === 5) {
            // For logged-in users: copy data when leaving step 5 (learning mode)
            copyStepperDataToFinalForm();
        }
        
        // Handle step transitions based on new structure
        if (currentStep === 1) {
            // This should not be reached since step 1 uses selectAccountOption
            console.log('Step 1 should use selectAccountOption');
            return;
        } else if (isUserLoggedIn && currentStep === 5) {
            // Skip from step 5 (learning mode) directly to step 6 (form) for logged-in users
            currentStep = 6;
        } else if (!isUserLoggedIn && currentStep === 6) {
            // For non-logged-in users, go from step 6 (account) to step 7 (form)
            currentStep = 7;
            copyStepperDataToFinalForm(); // Copy account data to final form
        } else {
            // Normal progression
            currentStep++;
        }
        
        // Copy data when moving to final step
        if ((isUserLoggedIn && currentStep === 6) || (!isUserLoggedIn && currentStep === 7)) {
            copyStepperDataToFinalForm();
        }
        
        updateStepper();
        loadStepContent();
    }
}

// Also copy data right before form submission (in case user edits fields in step 6)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('modularEnrollmentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default submission
            
            // DUPLICATE PREVENTION: Check if form is already being submitted
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton && submitButton.disabled) {
                console.warn('⚠️ Form submission blocked - already submitted');
                return;
            }
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Submitting...';
            }
            
            // Copy all stepper data to the form
            copyStepperDataToFinalForm();
            
            // Validate that required data is present
            const requiredFields = ['package_id', 'program_id', 'selected_modules', 'learning_mode', 'education_level'];
            
            // Add account fields to validation only for non-logged-in users
            if (!isUserLoggedIn) {
                requiredFields.push('user_firstname', 'user_lastname', 'email');
            }
            
            const missingFields = [];
            const invalidFields = [];
            
            requiredFields.forEach(field => {
                const input = form.querySelector(`input[name="${field}"]`);
                if (!input || !input.value.trim()) {
                    missingFields.push(field);
                } else {
                    // Additional validation for database IDs
                    if (field === 'program_id') {
                        const programId = parseInt(input.value);
                        if (isNaN(programId) || programId <= 0) {
                            invalidFields.push(`program_id (${input.value}) is invalid`);
                        }
                    }
                    if (field === 'package_id') {
                        const packageId = parseInt(input.value);
                        if (isNaN(packageId) || packageId <= 0) {
                            invalidFields.push(`package_id (${input.value}) is invalid - must be a positive integer`);
                        }
                    }
                }
            });
            
            if (missingFields.length > 0) {
                alert('❌ Missing required fields: ' + missingFields.join(', '));
                console.error('Missing required fields:', missingFields);
                // Re-enable submit button on error
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Complete Registration';
                }
                return;
            }
            
            if (invalidFields.length > 0) {
                alert('❌ Invalid database IDs: ' + invalidFields.join(', '));
                console.error('Invalid database IDs:', invalidFields);
                // Re-enable submit button on error
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Complete Registration';
                }
                return;
            }
            
            console.log('✅ All validation checks passed, submitting form...');
            
            // CRITICAL DEBUG: Log all form data before submission
            console.log('=== FORM SUBMISSION DEBUG ===');
            
            // Submit via AJAX to handle the response properly
            const formData = new FormData(form);
            
            // Double-check that Start_Date is not empty in FormData
            if (!formData.get('Start_Date') || formData.get('Start_Date') === '') {
                const today = new Date().toISOString().split('T')[0];
                formData.set('Start_Date', today);
                console.log('📅 Force-set Start_Date in FormData to:', today);
            }
            
            // Debug: Log all form data (safely hiding passwords)
            const formDataObject = {};
            const fileFields = []; // Track file fields specifically
            for (let [key, value] of formData.entries()) {
                if (key === 'password' || key === 'password_confirmation') {
                    formDataObject[key] = value ? '[HIDDEN - ' + value.length + ' chars]' : '[EMPTY]';
                } else if (value instanceof File) {
                    formDataObject[key] = `[FILE: ${value.name}, size: ${value.size} bytes]`;
                    fileFields.push(key);
                } else {
                    formDataObject[key] = value;
                }
            }
            console.log('📋 Complete FormData being submitted:', formDataObject);
            console.log('📎 File fields found:', fileFields);
            
            // Validate critical fields one more time before submission
            const criticalFields = ['package_id', 'program_id', 'selected_modules', 'learning_mode', 'education_level'];
            const missingCriticalFields = [];
            
            criticalFields.forEach(field => {
                const value = formData.get(field);
                if (!value || value === '' || value === 'null' || value === 'undefined') {
                    missingCriticalFields.push(field);
                }
            });
            
            if (missingCriticalFields.length > 0) {
                console.error('❌ CRITICAL FIELDS MISSING OR EMPTY:', missingCriticalFields);
                alert('Critical form fields are missing: ' + missingCriticalFields.join(', ') + '. Please refresh the page and try again.');
                return;
            }
            
            console.log('✅ All critical fields validated');
            console.log('🚀 Submitting to:', form.action);
            
            // Ensure CSRF token is in the FormData
            if (!formData.has('_token')) {
                formData.append('_token', CSRF_TOKEN);
            }
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Server response status:', response.status);
                console.log('Server response headers:', Object.fromEntries(response.headers));
                return response.json();
            })
            .then(data => {
                console.log('Server response data:', data);
                if (data.success) {
                    alert('Registration completed successfully!');
                    // Redirect to success page or login
                    window.location.href = '/login?message=registration_success';
                } else {
                    // Enhanced error handling for validation errors
                    let errorMessage = 'Registration failed';
                    
                    if (data.errors) {
                        console.error('Validation errors:', data.errors);
                        
                        // Check for file-related errors specifically
                        const fileErrors = [];
                        const otherErrors = [];
                        
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const isFileField = field.includes('tor') || field.includes('psa') || field.includes('good_moral') || 
                                              field.includes('certificate') || field.includes('transcript') || field.includes('diploma');
                            
                            if (isFileField) {
                                fileErrors.push(`${field.replace(/_/g, ' ').toUpperCase()}: ${messages.join(', ')}`);
                            } else {
                                otherErrors.push(`${field}: ${messages.join(', ')}`);
                            }
                        }
                        
                        if (fileErrors.length > 0) {
                            errorMessage += '\n\nMissing required files for your education level:\n' + fileErrors.join('\n');
                            errorMessage += '\n\nPlease upload the required documents in the form above and try again.';
                        }
                        
                        if (otherErrors.length > 0) {
                            errorMessage += '\n\nOther errors:\n' + otherErrors.join('\n');
                        }
                    });
                    alert(errorMessage);
                } else {
                    alert('Registration failed: ' + (data.message || 'Unknown error'));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Registration failed due to a network error. Please try again.');
        });
    });
    
    // Auto-set program when user is logged in and has made selections
    function onProgramSelectionChange() {
        const programSelect = document.getElementById('programSelect');
        if (programSelect && programSelect.value) {
            selectedProgramId = programSelect.value;
            
            // Update hidden input
            const hiddenProgramInput = document.getElementById('hidden_program_id');
            if (hiddenProgramInput) {
                hiddenProgramInput.value = selectedProgramId;
            }
            
            console.log('Program selection changed to:', selectedProgramId);
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
                                   onchange="handleFileUpload(this)"
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
                // Remove aria-hidden when modal is opening and handle focus
                coursesModal.removeAttribute('aria-hidden');
                // Ensure focus is properly managed
                setTimeout(() => {
                    const focusableElements = coursesModal.querySelectorAll('button, input, select, textarea, [href], [tabindex]:not([tabindex="-1"])');
                    if (focusableElements.length > 0) {
                        focusableElements[0].focus();
                    }
                }, 150);
            });
            
            coursesModal.addEventListener('hidden.bs.modal', function () {
                // Add aria-hidden when modal is completely closed
                coursesModal.setAttribute('aria-hidden', 'true');
            });
        }
        
        const termsModal = document.getElementById('termsModal');
        if (termsModal) {
            termsModal.addEventListener('show.bs.modal', function () {
                // Remove aria-hidden when modal is opening and handle focus
                termsModal.removeAttribute('aria-hidden');
                // Ensure focus is properly managed
                setTimeout(() => {
                    const focusableElements = termsModal.querySelectorAll('button, input, select, textarea, [href], [tabindex]:not([tabindex="-1"])');
                    if (focusableElements.length > 0) {
                        focusableElements[0].focus();
                    }
                }, 150);
            });
            
            termsModal.addEventListener('hidden.bs.modal', function () {
                // Add aria-hidden when modal is completely closed
                termsModal.setAttribute('aria-hidden', 'true');
            });
        }
    });

    // Enhanced file upload with OCR validation (similar to Full_enrollment)
    function handleFileUpload(inputElement) {
        const fieldName = inputElement.name;
        const file = inputElement.files[0];
        
        if (!file) return;
        
        // Validate file type
        const allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (!allowedTypes.includes(fileExtension)) {
            showErrorModal('Invalid file type. Only PDF, JPG, JPEG, and PNG files are allowed.');
            inputElement.value = '';
            return;
        }
        
        // Validate file size (max 10MB)
        if (file.size > 10485760) {
            showErrorModal('File size exceeds 10MB limit. Please choose a smaller file.');
            inputElement.value = '';
            return;
        }
        
        // Get user's name for validation - check all possible field names
        const firstNameSelectors = [
            'input[name="firstname"]',
            'input[name="user_firstname"]',
            'input[name="first_name"]',
            'input[name="First_Name"]',
            'input[name="FirstName"]',
            'input[id="firstname"]',
            'input[id="user_firstname"]',
            'input[id="first_name"]',
            'input[id="First_Name"]'
        ];
        
        const lastNameSelectors = [
            'input[name="lastname"]',
            'input[name="user_lastname"]', 
            'input[name="last_name"]',
            'input[name="Last_Name"]',
            'input[name="LastName"]',
            'input[id="lastname"]',
            'input[id="user_lastname"]',
            'input[id="last_name"]',
            'input[id="Last_Name"]'
        ];
        
        let firstName = '';
        let lastName = '';
        
        // Try to find first name
        for (const selector of firstNameSelectors) {
            const element = document.querySelector(selector);
            if (element && element.value.trim()) {
                firstName = element.value.trim();
                break;
            }
        }
        
        // Try to find last name
        for (const selector of lastNameSelectors) {
            const element = document.querySelector(selector);
            if (element && element.value.trim()) {
                lastName = element.value.trim();
                break;
            }
        }
        
        console.log('Found names for OCR validation:', firstName, lastName);
        
        // Only require names if we're on the actual form step
        const isOnFormStep = isUserLoggedIn ? (currentStep === 6) : (currentStep === 7);
        
        if (!firstName || !lastName) {
            if (!isOnFormStep) {
                console.log('File upload triggered but not on form step - allowing without name validation');
                // Continue without name validation if we're not on the actual form step
                firstName = 'temp_user';
                lastName = 'temp_user';
            } else {
                showErrorModal('Please enter your first name and last name in the form before uploading documents.');
                inputElement.value = '';
                return;
            }
        }
        
        // Show loading indicator
        showLoadingModal('Processing document with OCR...');
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('field_name', fieldName);
        formData.append('first_name', firstName);
        formData.append('last_name', lastName);
        
        fetch('/registration/validate-file', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
            closeLoadingModal();
            
            let data;
            try {
                data = await response.json();
            } catch (jsonError) {
                const rawText = await response.text();
                console.error('JSON parse error:', jsonError);
                console.error('Raw response:', rawText);
                showErrorModal('Server returned invalid response. Check console for details.');
                inputElement.value = '';
                return;
            }
            
            console.log('File validation response:', data);
            
            if (data.success) {
                showSuccessModal('Document validated successfully!');
                
                // CRITICAL FIX: Store the file path for form submission
                if (data.file_path) {
                    let hiddenFileInput = document.querySelector(`input[name="${fieldName}_path"]`);
                    if (!hiddenFileInput) {
                        hiddenFileInput = document.createElement('input');
                        hiddenFileInput.type = 'hidden';
                        hiddenFileInput.name = fieldName + '_path';
                        inputElement.parentNode.appendChild(hiddenFileInput);
                    }
                    hiddenFileInput.value = data.file_path;
                    console.log('Stored file path for', fieldName, ':', data.file_path);
                }
                
                // Handle education level detection
                if (data.certificate_level) {
                    handleEducationLevelDetection(data.certificate_level);
                }
                
                if (data.suggestions && data.suggestions.length > 0) {
                    showProgramSuggestions(data.suggestions);
                }
            } else {
                console.error('File validation failed:', data);
                
                // FALLBACK: Even if OCR validation fails, we still want to allow file upload
                // Show a warning but don't block the user
                showWarningModal(data.message || 'File validation failed. However, your file has been uploaded and will be processed manually if needed.');
                
                // Add warning styling but still mark as valid for form submission
                inputElement.classList.add('is-warning');
                inputElement.classList.remove('is-invalid', 'is-valid');
                
                // CRITICAL: Even if validation fails, keep the file for submission
                // The backend controller will handle it directly
                console.log('File kept in input despite validation error - will be processed by backend');
            }
        })
        .catch(error => {
            closeLoadingModal();
            console.error('OCR processing error:', error);
            
            // FALLBACK: Even on network error, allow file upload to proceed
            showWarningModal('Network error occurred during file validation. Your file will still be uploaded and processed manually if needed.');
            
            // Add warning styling but still allow form submission
            inputElement.classList.add('is-warning');
            inputElement.classList.remove('is-invalid', 'is-valid');
            
            console.log('File kept in input despite network error - will be processed by backend');
        });
    }

    // Show program suggestions in dropdown
    function showProgramSuggestions(suggestions) {
        const programSelect = document.getElementById('programSelect');
        if (!programSelect) return;
        
        // Clear existing suggestions
        const existingSuggestions = programSelect.querySelectorAll('.suggestion-option');
        existingSuggestions.forEach(option => option.remove());
        
        // Add suggestion header
        if (suggestions.length > 0) {
            const headerOption = document.createElement('option');
            headerOption.disabled = true;
            headerOption.textContent = '--- Suggested Programs ---';
            headerOption.className = 'suggestion-header';
            programSelect.insertBefore(headerOption, programSelect.children[1]);
            
            // Add suggestions
            suggestions.forEach(suggestion => {
                const option = document.createElement('option');
                option.value = suggestion.program.program_id;
                option.textContent = `⭐ ${suggestion.program.program_name} (Match: ${suggestion.score})`;
                option.className = 'suggestion-option';
                option.style.backgroundColor = '#e3f2fd';
                programSelect.insertBefore(option, programSelect.children[programSelect.children.length]);
            });
            
            // Show notification
            showInfoModal(`We found ${suggestions.length} program(s) that match your uploaded certificate. Check the suggested programs at the top of the dropdown.`);
        }
    }

    // Modal functions for OCR feedback
    function showLoadingModal(message) {
        let modal = document.getElementById('loadingModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'loadingModal';
            modal.className = 'modal-overlay';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            `;
            modal.innerHTML = `
                <div class="modal-content" style="
                    background: white;
                    padding: 2rem;
                    border-radius: 8px;
                    text-align: center;
                    min-width: 300px;
                ">
                    <div class="loading-spinner" style="
                        border: 4px solid #f3f3f3;
                        border-top: 4px solid #3498db;
                        border-radius: 50%;
                        width: 40px;
                        height: 40px;
                        animation: spin 2s linear infinite;
                        margin: 0 auto 1rem;
                    "></div>
                    <p id="loadingMessage">${message}</p>
                </div>
            `;
            document.body.appendChild(modal);
            
            // Add CSS animation for spinner
            if (!document.getElementById('spinner-style')) {
                const style = document.createElement('style');
                style.id = 'spinner-style';
                style.textContent = `
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(style);
            }
        }
        document.getElementById('loadingMessage').textContent = message;
        modal.style.display = 'flex';
    }

    function closeLoadingModal() {
        const modal = document.getElementById('loadingModal');
        if (modal) modal.style.display = 'none';
    }

    function showErrorModal(message) {
        showModal('Error', message, 'error');
    }

    function showSuccessModal(message) {
        showModal('Success', message, 'success');
    }

    function showInfoModal(message) {
        showModal('Information', message, 'info');
    }

    function showWarningModal(message) {
        showModal('Warning', message, 'warning');
    }

    function showModal(title, message, type) {
        let modal = document.getElementById('customModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'customModal';
            modal.className = 'modal-overlay';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            `;
            modal.innerHTML = `
                <div class="modal-content" style="
                    background: white;
                    padding: 0;
                    border-radius: 8px;
                    min-width: 400px;
                    max-width: 500px;
                ">
                    <div class="modal-header" style="
                        padding: 1rem 1.5rem;
                        border-bottom: 1px solid #dee2e6;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    ">
                        <h5 id="modalTitle" style="margin: 0;">${title}</h5>
                        <button type="button" onclick="closeModal()" class="close-btn" style="
                            background: none;
                            border: none;
                            font-size: 1.5rem;
                            cursor: pointer;
                        ">&times;</button>
                    </div>
                    <div class="modal-body" style="padding: 1.5rem;">
                        <p id="modalMessage">${message}</p>
                    </div>
                    <div class="modal-footer" style="
                        padding: 1rem 1.5rem;
                        border-top: 1px solid #dee2e6;
                        text-align: right;
                    ">
                        <button type="button" onclick="closeModal()" class="btn btn-primary" style="
                            background: #007bff;
                            border: 1px solid #007bff;
                            color: white;
                            padding: 0.375rem 0.75rem;
                            border-radius: 0.25rem;
                            cursor: pointer;
                        ">OK</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalMessage').textContent = message;
        
        const modalContent = modal.querySelector('.modal-content');
        // Add type-specific styling
        if (type === 'error') {
            modalContent.style.borderLeft = '4px solid #dc3545';
        } else if (type === 'success') {
            modalContent.style.borderLeft = '4px solid #28a745';
        } else if (type === 'info') {
            modalContent.style.borderLeft = '4px solid #17a2b8';
        }
        
        modal.style.display = 'flex';
    }

    function closeModal() {
        const modal = document.getElementById('customModal');
        if (modal) modal.style.display = 'none';
    }

    // Handle education level detection from OCR (similar to Full_enrollment)
    function handleEducationLevelDetection(detectedLevel) {
        console.log('Education level detected:', detectedLevel);
        
        // Find the education level dropdown
        const educationSelect = document.getElementById('educationLevel');
        if (!educationSelect) {
            console.warn('Education level dropdown not found');
            return;
        }
        
        // Try to find and select the detected education level
        let matchFound = false;
        const options = educationSelect.options;
        
        for (let i = 0; i < options.length; i++) {
            const optionText = options[i].textContent.toLowerCase();
            const detectedLower = detectedLevel.toLowerCase();
            
            // Check for exact match or partial match
            if (optionText.includes(detectedLower) || detectedLower.includes(optionText)) {
                educationSelect.selectedIndex = i;
                matchFound = true;
                break;
            }
        }
        
        if (matchFound) {
            // Trigger the change event to update form requirements
            educationSelect.dispatchEvent(new Event('change'));
            
            // Show confirmation modal
            showEducationLevelModal(detectedLevel, true);
        } else {
            // Show options modal if no match found
            showEducationLevelModal(detectedLevel, false);
        }
    }

    // Show education level detection modal
    function showEducationLevelModal(detectedLevel, matchFound) {
        let modal = document.getElementById('educationLevelModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'educationLevelModal';
            modal.className = 'modal-overlay';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            `;
            document.body.appendChild(modal);
        }
        
        if (matchFound) {
            modal.innerHTML = `
                <div class="modal-content" style="
                    background: white;
                    padding: 0;
                    border-radius: 8px;
                    min-width: 400px;
                    max-width: 500px;
                    border-left: 4px solid #28a745;
                ">
                    <div class="modal-header" style="
                        padding: 1rem 1.5rem;
                        border-bottom: 1px solid #dee2e6;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    ">
                        <h5 style="margin: 0; color: #28a745;">
                            <i class="bi bi-check-circle-fill me-2"></i>Education Level Detected
                        </h5>
                        <button type="button" onclick="closeEducationLevelModal()" class="close-btn" style="
                            background: none;
                            border: none;
                            font-size: 1.5rem;
                            cursor: pointer;
                        ">&times;</button>
                    </div>
                    <div class="modal-body" style="padding: 1.5rem;">
                        <p>We detected "<strong>${detectedLevel}</strong>" from your document and have automatically selected it in the education level field.</p>
                        <p>If this is incorrect, you can change it manually in the form.</p>
                    </div>
                    <div class="modal-footer" style="
                        padding: 1rem 1.5rem;
                        border-top: 1px solid #dee2e6;
                        text-align: right;
                    ">
                        <button type="button" onclick="closeEducationLevelModal()" class="btn btn-success" style="
                            background: #28a745;
                            border: 1px solid #28a745;
                            color: white;
                            padding: 0.375rem 0.75rem;
                            border-radius: 0.25rem;
                            cursor: pointer;
                        ">Understood</button>
                    </div>
                </div>
            `;
        } else {
            modal.innerHTML = `
                <div class="modal-content" style="
                    background: white;
                    padding: 0;
                    border-radius: 8px;
                    min-width: 400px;
                    max-width: 500px;
                    border-left: 4px solid #ffc107;
                ">
                    <div class="modal-header" style="
                        padding: 1rem 1.5rem;
                        border-bottom: 1px solid #dee2e6;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    ">
                        <h5 style="margin: 0; color: #856404;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Education Level Detected
                        </h5>
                        <button type="button" onclick="closeEducationLevelModal()" class="close-btn" style="
                            background: none;
                            border: none;
                            font-size: 1.5rem;
                            cursor: pointer;
                        ">&times;</button>
                    </div>
                    <div class="modal-body" style="padding: 1.5rem;">
                        <p>We detected "<strong>${detectedLevel}</strong>" from your document, but couldn't automatically match it to our available options.</p>
                        <p>Please manually select your education level from the dropdown in the form.</p>
                    </div>
                    <div class="modal-footer" style="
                        padding: 1rem 1.5rem;
                        border-top: 1px solid #dee2e6;
                        text-align: right;
                    ">
                        <button type="button" onclick="closeEducationLevelModal()" class="btn btn-warning" style="
                            background: #ffc107;
                            border: 1px solid #ffc107;
                            color: #212529;
                            padding: 0.375rem 0.75rem;
                            border-radius: 0.25rem;
                            cursor: pointer;
                        ">OK</button>
                    </div>
                </div>
            `;
        }
        
        modal.style.display = 'flex';
    }

    function closeEducationLevelModal() {
        const modal = document.getElementById('educationLevelModal');
        if (modal) modal.style.display = 'none';
    }

    // Name sync logic between Account and Form steps
    function syncNamesBetweenSteps() {
        // Account step fields
        const accFirst = document.getElementById('user_firstname');
        const accLast = document.getElementById('user_lastname');
        // Form step fields (fallback fields)
        const formFirst = document.getElementById('firstname');
        const formLast = document.getElementById('lastname');
        if (!accFirst || !accLast || !formFirst || !formLast) return;
        // Copy Account -> Form
        formFirst.value = accFirst.value;
        formLast.value = accLast.value;
        // Listen for changes in either and sync
        accFirst.addEventListener('input', () => { formFirst.value = accFirst.value; });
        accLast.addEventListener('input', () => { formLast.value = accLast.value; });
        formFirst.addEventListener('input', () => { accFirst.value = formFirst.value; });
        formLast.addEventListener('input', () => { accLast.value = formLast.value; });
    }
    document.addEventListener('DOMContentLoaded', syncNamesBetweenSteps);

    // Program autofill and disable in Form step
    function autofillProgramInForm() {
        const programSelect = document.getElementById('programSelect');
        const hiddenProgramId = document.getElementById('hidden_program_id');
        if (programSelect && hiddenProgramId && hiddenProgramId.value) {
            programSelect.value = hiddenProgramId.value;
            programSelect.disabled = true;
        }
    }
    document.addEventListener('DOMContentLoaded', autofillProgramInForm);

    // Duplicate functions removed - using the enhanced OCR functions above
</script>

<style>
.form-control.is-warning {
    border-color: #ffc107;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%23ffc107' d='M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

// Data persistence functions to maintain data when navigating between steps
function saveFormData() {
    if (!isUserLoggedIn) {
        const formData = {
            user_firstname: document.getElementById('user_firstname')?.value || '',
            user_lastname: document.getElementById('user_lastname')?.value || '',
            user_email: document.getElementById('user_email')?.value || '',
            password: document.getElementById('password')?.value || '',
            password_confirmation: document.getElementById('password_confirmation')?.value || '',
            referral_code: document.getElementById('referral_code')?.value || ''
        };
        
        // Save to sessionStorage
        sessionStorage.setItem('enrollmentFormData', JSON.stringify(formData));
        console.log('📱 Saved form data to session:', formData);
    }
}

function restoreFormData() {
    if (!isUserLoggedIn) {
        const savedData = sessionStorage.getItem('enrollmentFormData');
        if (savedData) {
            try {
                const formData = JSON.parse(savedData);
                
                // Restore account step fields
                const firstnameField = document.getElementById('user_firstname');
                const lastnameField = document.getElementById('user_lastname');
                const emailField = document.getElementById('user_email');
                const passwordField = document.getElementById('password');
                const passwordConfirmField = document.getElementById('password_confirmation');
                const referralField = document.getElementById('referral_code');
                
                if (firstnameField && formData.user_firstname) firstnameField.value = formData.user_firstname;
                if (lastnameField && formData.user_lastname) lastnameField.value = formData.user_lastname;
                if (emailField && formData.user_email) emailField.value = formData.user_email;
                if (passwordField && formData.password) passwordField.value = formData.password;
                if (passwordConfirmField && formData.password_confirmation) passwordConfirmField.value = formData.password_confirmation;
                if (referralField && formData.referral_code) referralField.value = formData.referral_code;
                
                console.log('📱 Restored form data from session:', formData);
                
                // Re-validate the step
                if (typeof validateStep6 === 'function') {
                    validateStep6();
                }
            } catch (e) {
                console.error('Error restoring form data:', e);
            }
        }
    }
}

// Add event listeners to save data when fields change
document.addEventListener('DOMContentLoaded', function() {
    if (!isUserLoggedIn) {
        // Restore data when page loads
        restoreFormData();
        
        // Save data when fields change
        const fieldsToWatch = ['user_firstname', 'user_lastname', 'user_email', 'password', 'password_confirmation', 'referral_code'];
        fieldsToWatch.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', saveFormData);
                field.addEventListener('change', saveFormData);
            }
        });
    }
});

// Override the nextStep function to save data before navigation
const originalNextStep = nextStep;
nextStep = function() {
    saveFormData(); // Save current form data
    return originalNextStep.apply(this, arguments);
};

// Override the prevStep function to restore data after navigation
const originalPrevStep = prevStep;
prevStep = function() {
    const result = originalPrevStep.apply(this, arguments);
    setTimeout(restoreFormData, 100); // Restore data after step change
    return result;
};
</style>
@endpush