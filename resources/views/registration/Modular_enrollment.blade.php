@extends('layouts.navbar')

@section('title', 'Modular Enrollment - Individual Module Selection')
@section('hide_footer', true)
@section('body_class', 'registration-page')

@push('styles')
    {!! App\Helpers\UIHelper::getNavbarStyles() !!}

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        // URL constants
        const PREFILL_URL  = "{{ route('registration.userPrefill') }}";
        const VALIDATE_URL = "{{ route('registration.validateFile') }}";
        const CSRF_TOKEN   = "{{ csrf_token() }}";
        const MODULES_URL  = "/get-program-modules";
    </script>

    <link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Modular_enrollment.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .stepper-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .stepper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            position: relative;
        }

        .stepper::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 50px;
            right: 50px;
            height: 3px;
            background: #e0e0e0;
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            background: white;
            padding: 0 15px;
            min-width: 120px;
        }

        .step-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            border: 3px solid #e0e0e0;
        }

        .step.active .step-circle {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .step.completed .step-circle {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }

        .step-label {
            font-size: 14px;
            font-weight: 500;
            color: #666;
            text-align: center;
        }

        .step.active .step-label {
            color: #007bff;
            font-weight: 600;
        }

        .step.completed .step-label {
            color: #28a745;
            font-weight: 600;
        }

        .step-content {
            display: none;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            max-height: 80vh;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .step-content.active {
            display: block;
        }

        /* Step 6 specific styling for dynamic content */
        #content-6 {
            max-height: 85vh;
            overflow-y: auto;
            overflow-x: hidden;
        }

        #dynamicFormFields {
            max-height: 50vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #f8f9fa;
            margin-bottom: 20px;
        }

        #educationLevelRequirements {
            max-height: 30vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #f8f9fa;
            margin-bottom: 20px;
        }

        .step-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .step-header h2 {
            color: #333;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .step-header p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .selection-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .selection-card:hover {
            border-color: #007bff;
            box-shadow: 0 8px 25px rgba(0,123,255,0.15);
            transform: translateY(-2px);
        }

        .selection-card.selected {
            border-color: #007bff;
            background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
            box-shadow: 0 8px 25px rgba(0,123,255,0.2);
        }

        .selection-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .selection-card .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .selection-card .card-price {
            font-size: 1.2rem;
            font-weight: 600;
            color: #007bff;
        }

        .selection-card .card-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .selection-card .card-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .selection-card .card-features li {
            color: #555;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .selection-card .card-features li:before {
            content: '✓';
            color: #28a745;
            font-weight: 600;
            position: absolute;
            left: 0;
        }

        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .module-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .module-card:hover {
            border-color: #007bff;
            box-shadow: 0 6px 20px rgba(0,123,255,0.15);
            transform: translateY(-2px);
        }

        .module-card.selected {
            border-color: #007bff;
            background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
            box-shadow: 0 6px 20px rgba(0,123,255,0.2);
        }

        .module-card .module-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .module-card .module-checkbox {
            margin-right: 15px;
            width: 20px;
            height: 20px;
        }

        .module-card .module-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .module-card .module-description {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .module-card .module-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #777;
        }

        .module-card .module-actions {
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .module-card .module-actions .btn {
            border-radius: 20px;
            font-size: 0.85rem;
            padding: 6px 16px;
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .btn-nav {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-nav:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .alert {
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .learning-mode-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .mode-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .mode-card:hover {
            border-color: #007bff;
            box-shadow: 0 8px 25px rgba(0,123,255,0.15);
            transform: translateY(-3px);
        }

        .mode-card.selected {
            border-color: #007bff;
            background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
            box-shadow: 0 8px 25px rgba(0,123,255,0.2);
        }

        .mode-card .mode-icon {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 20px;
        }

        .mode-card .mode-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .mode-card .mode-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .mode-card .mode-features li {
            color: #555;
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
        }

        .mode-card .mode-features li:before {
            content: '✓';
            color: #28a745;
            font-weight: 600;
            position: absolute;
            left: 0;
        }

        @media (max-width: 768px) {
            .stepper {
                flex-direction: column;
                gap: 15px;
            }

            .stepper::before {
                display: none;
            }

            .step {
                flex-direction: row;
                width: 100%;
                justify-content: flex-start;
            }

            .step-circle {
                margin-right: 15px;
                margin-bottom: 0;
            }

            .card-grid, .module-grid, .learning-mode-grid {
                grid-template-columns: 1fr;
            }

            .step-header h2 {
                font-size: 2rem;
            }

            .navigation-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .btn-nav {
                width: 100%;
                order: 1;
            }

            .navigation-buttons .btn-secondary {
                order: 2;
            }
        }
    </style>

    <!-- reCAPTCHA -->
    @if(env('RECAPTCHA_SITE_KEY'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    <!-- Critical JavaScript functions -->
    <script>
        // Global variables
        let currentStep = 1;
        let selectedPackageId = null;
        let selectedProgramId = null;
        let selectedModules = [];
        let additionalModules = [];
        let selectedLearningMode = null;
        let packageModuleLimit = 0;
        let totalSteps = 6;

        // Plan configuration data
        const planConfig = {
            full: {
                enableSynchronous: {{ isset($fullPlan) && $fullPlan->enable_synchronous ? 'true' : 'false' }},
                enableAsynchronous: {{ isset($fullPlan) && $fullPlan->enable_asynchronous ? 'true' : 'false' }}
            },
            modular: {
                enableSynchronous: {{ isset($modularPlan) && $modularPlan->enable_synchronous ? 'true' : 'false' }},
                enableAsynchronous: {{ isset($modularPlan) && $modularPlan->enable_asynchronous ? 'true' : 'false' }}
            }
        };

        // User session data
        @php
            $userLoggedIn = session('user_id') || (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']));
            $userId = session('user_id') ?: (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '');
            $userFirstname = session('user_firstname') ?: (isset($_SESSION['user_firstname']) ? $_SESSION['user_firstname'] : '');
            $userLastname = session('user_lastname') ?: (isset($_SESSION['user_lastname']) ? $_SESSION['user_lastname'] : '');
            $userEmail = session('user_email') ?: (isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '');
        @endphp

        const isUserLoggedIn = {{ $userLoggedIn ? 'true' : 'false' }};
        const loggedInUserFirstname = '{{ $userFirstname }}';
        const loggedInUserLastname = '{{ $userLastname }}';
        const loggedInUserEmail = '{{ $userEmail }}';

        // Form requirements data
        const formRequirements = @json($formRequirements ?? []);

        // Education levels data
        const educationLevels = @json($educationLevels ?? []);

        console.log('Modular enrollment system initialized');
    </script>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Alert Container -->
    <div id="alertContainer" class="alert-container mb-4"></div>
    
    <!-- Stepper -->
    <div class="stepper-container">
        <div class="stepper">
            <div class="step active" id="step-1">
                <div class="step-circle">1</div>
                <div class="step-label">Packages</div>
            </div>
            <div class="step" id="step-2">
                <div class="step-circle">2</div>
                <div class="step-label">Programs</div>
            </div>
            <div class="step" id="step-3">
                <div class="step-circle">3</div>
                <div class="step-label">Modules</div>
            </div>
            <div class="step" id="step-4">
                <div class="step-circle">4</div>
                <div class="step-label">Learning Mode</div>
            </div>
            <div class="step" id="step-5">
                <div class="step-circle">5</div>
                <div class="step-label">Account</div>
            </div>
            <div class="step" id="step-6">
                <div class="step-circle">6</div>
                <div class="step-label">Form</div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('enrollment.modular.submit') }}" method="POST" enctype="multipart/form-data" id="enrollmentForm">
        @csrf
        
        <!-- Hidden inputs -->
        <input type="hidden" name="enrollment_type" value="Modular">
        <input type="hidden" name="package_id" id="package_id" value="">
        <input type="hidden" name="program_id" id="program_id" value="">
        <input type="hidden" name="selected_modules" id="selected_modules" value="">
        <input type="hidden" name="learning_mode" id="learning_mode" value="">
        <input type="hidden" name="sync_async_mode" id="sync_async_mode" value="">
        <input type="hidden" name="Start_Date" id="Start_Date" value="">
        <input type="hidden" name="education_level" id="education_level" value="">

        <!-- Step 1: Package Selection -->
        <div class="step-content active" id="content-1">
            <div class="step-header">
                <h2>Choose Your Package</h2>
                <p>Select a learning package that suits your needs</p>
            </div>
            
            <div class="card-grid">
                @if($packages && count($packages) > 0)
                    @foreach($packages as $package)
                        <div class="selection-card" data-package-id="{{ $package->package_id }}" 
                             data-program-id="{{ $package->program_id }}" 
                             data-module-count="{{ $package->module_count }}"
                             onclick="selectPackage({{ $package->package_id }}, {{ $package->program_id }}, {{ $package->module_count }})">
                            <div class="card-header">
                                <h3 class="card-title">{{ $package->package_name }}</h3>
                                <div class="card-price">₱{{ number_format($package->price ?? $package->amount, 2) }}</div>
                            </div>
                            <div class="card-description">
                                {{ $package->description ?? 'Flexible modular learning package' }}
                            </div>
                            <ul class="card-features">
                                <li>{{ $package->module_count }} modules included</li>
                                <li>Self-paced learning</li>
                                <li>Certificate upon completion</li>
                                <li>Flexible scheduling</li>
                            </ul>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        No packages available at the moment. Please contact the administrator.
                    </div>
                @endif
            </div>
            
            <div class="navigation-buttons">
                <div></div>
                <button type="button" class="btn-nav btn-primary" onclick="nextStep()" disabled id="step1-next">
                    Next: Select Program <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Program Selection -->
        <div class="step-content" id="content-2">
            <div class="step-header">
                <h2>Select Your Program</h2>
                <p>Choose the program that aligns with your career goals</p>
            </div>
            
            <div class="card-grid" id="programsGrid">
                <!-- Programs will be loaded here -->
            </div>
            
            <div class="navigation-buttons">
                <button type="button" class="btn-nav btn-secondary" onclick="prevStep()">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button type="button" class="btn-nav btn-primary" onclick="nextStep()" disabled id="step2-next">
                    Next: Select Modules <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Module Selection -->
        <div class="step-content" id="content-3">
            <div class="step-header">
                <h2>Select Your Modules</h2>
                <p>Choose up to <span id="moduleLimit">0</span> modules from the available options</p>
            </div>
            
            <div class="module-grid" id="modulesGrid">
                <!-- Modules will be loaded here -->
            </div>
            
            <!-- Additional Modules Section -->
            <div class="mt-4">
                <h4>Add Additional Modules</h4>
                <p class="text-muted">Want to add more modules? Select additional modules with separate charges.</p>
                <button type="button" class="btn btn-outline-primary" onclick="showAdditionalModules()">
                    <i class="fas fa-plus"></i> Add More Modules
                </button>
            </div>
            
            <div class="navigation-buttons">
                <button type="button" class="btn-nav btn-secondary" onclick="prevStep()">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button type="button" class="btn-nav btn-primary" onclick="nextStep()" disabled id="step3-next">
                    Next: Choose Learning Mode <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Step 4: Learning Mode Selection -->
        <div class="step-content" id="content-4">
            <div class="step-header">
                <h2>Choose Your Learning Mode</h2>
                <p>Select the learning style that works best for you</p>
            </div>
            
            <div class="learning-mode-grid">
                @if(isset($modularPlan) && $modularPlan->enable_synchronous)
                <div class="mode-card" onclick="selectLearningMode('synchronous')">
                    <div class="mode-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3 class="mode-title">Synchronous</h3>
                    <div class="mode-description">Live classes with real-time interaction</div>
                    <ul class="mode-features">
                        <li>Live video sessions</li>
                        <li>Real-time Q&A</li>
                        <li>Interactive discussions</li>
                        <li>Scheduled class times</li>
                    </ul>
                </div>
                @endif
                
                @if(isset($modularPlan) && $modularPlan->enable_asynchronous)
                <div class="mode-card" onclick="selectLearningMode('asynchronous')">
                    <div class="mode-icon">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <h3 class="mode-title">Asynchronous</h3>
                    <div class="mode-description">Self-paced learning with recorded content</div>
                    <ul class="mode-features">
                        <li>Pre-recorded videos</li>
                        <li>Study at your own pace</li>
                        <li>24/7 access to materials</li>
                        <li>Flexible scheduling</li>
                    </ul>
                </div>
                @endif
            </div>
            
            <!-- Start Date Selection for Asynchronous Mode -->
            <div id="startDateContainer" class="mt-4" style="display: none;">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="startDate" class="form-label">
                                <i class="fas fa-calendar-alt"></i> Preferred Start Date
                                <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="startDate" name="start_date" 
                                   min="{{ date('Y-m-d') }}" required>
                            <small class="form-text text-muted">
                                Choose when you'd like to begin your learning journey.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="navigation-buttons">
                <button type="button" class="btn-nav btn-secondary" onclick="prevStep()">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button type="button" class="btn-nav btn-primary" onclick="nextStep()" disabled id="step4-next">
                    Next: Account Setup <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Step 5: Account Registration -->
        <div class="step-content" id="content-5">
            <div class="step-header">
                <h2>Account Information</h2>
                <p>Create your account or use existing login information</p>
            </div>
            
            <!-- Logged in user notice -->
            <div id="loginNotice" class="alert alert-info" style="display: none;">
                <i class="fas fa-info-circle"></i>
                You are logged in as <strong id="loggedInUserName"></strong>. 
                Your account information will be used automatically.
            </div>
            
            <!-- Account creation form -->
            <div id="accountCreationForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="user_firstname" class="form-label">
                                First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="user_firstname" 
                                   name="user_firstname" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="user_lastname" class="form-label">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="user_lastname" 
                                   name="user_lastname" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="email" class="form-label">
                        Email Address <span class="text-danger">*</span>
                    </label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="password" 
                                   name="password" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="navigation-buttons">
                <button type="button" class="btn-nav btn-secondary" onclick="prevStep()">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button type="button" class="btn-nav btn-primary" onclick="nextStep()" id="step5-next">
                    Next: Complete Form <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Step 6: Form & Education Level -->
        <div class="step-content" id="content-6">
            <div class="step-header">
                <h2>Complete Your Information</h2>
                <p>Please provide the required information to complete your enrollment</p>
            </div>
            
            <!-- Education Level Selection -->
            <div class="form-group mb-4">
                <label for="educationLevelSelect" class="form-label">
                    <i class="fas fa-graduation-cap"></i> Education Level
                    <span class="text-danger">*</span>
                </label>
                <select name="education_level_form" id="educationLevelSelect" class="form-select" required onchange="handleEducationLevelChange()">
                    <option value="">Select Education Level</option>
                    @if(isset($educationLevels) && $educationLevels->count() > 0)
                        @foreach($educationLevels as $level)
                            <option value="{{ $level->level_name }}" 
                                    data-file-requirements="{{ json_encode($level->getFileRequirementsForPlan('general')) }}">
                                {{ $level->level_name }}
                            </option>
                        @endforeach
                    @else
                        <option value="Undergraduate">Undergraduate</option>
                        <option value="Graduate">Graduate</option>
                    @endif
                </select>
            </div>
            
            <!-- Dynamic Education Level Requirements -->
            <div id="educationLevelRequirements" style="display: none;">
                <!-- Requirements will be loaded here -->
            </div>
            
            <!-- Dynamic Form Fields -->
            <div id="dynamicFormFields">
                <!-- Dynamic fields will be loaded here -->
            </div>
            
            <div class="navigation-buttons">
                <button type="button" class="btn-nav btn-secondary" onclick="prevStep()">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button type="submit" class="btn-nav btn-primary" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Submit Registration
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Courses Modal -->
<div class="modal fade" id="coursesModal" tabindex="-1" aria-labelledby="coursesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="coursesModalLabel">
                    <i class="fas fa-book"></i> Courses in <span id="moduleNameDisplay"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="coursesContainer">
                    <!-- Courses will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Additional Modules Modal -->
<div class="modal fade" id="additionalModulesModal" tabindex="-1" aria-labelledby="additionalModulesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="additionalModulesModalLabel">
                    <i class="fas fa-plus-circle"></i> Additional Modules
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Additional modules will be charged separately and can be added to your current package.
                </div>
                <div id="additionalModulesContainer">
                    <!-- Additional modules will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addSelectedAdditionalModules()">
                    <i class="fas fa-plus"></i> Add Selected Modules
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Step navigation functions
function nextStep() {
    if (currentStep < totalSteps) {
        if (validateCurrentStep()) {
            currentStep++;
            updateStepDisplay();
            loadStepContent();
        }
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStepDisplay();
        loadStepContent();
    }
}

function updateStepDisplay() {
    // Update stepper visual state
    for (let i = 1; i <= totalSteps; i++) {
        const step = document.getElementById(`step-${i}`);
        const content = document.getElementById(`content-${i}`);
        
        if (step) {
            step.classList.remove('active', 'completed');
            if (i < currentStep) {
                step.classList.add('completed');
            } else if (i === currentStep) {
                step.classList.add('active');
            }
        }
        
        if (content) {
            content.classList.remove('active');
            if (i === currentStep) {
                content.classList.add('active');
            }
        }
    }
}

function validateCurrentStep() {
    switch (currentStep) {
        case 1:
            if (!selectedPackageId) {
                showAlert('Please select a package first.', 'warning');
                return false;
            }
            return true;
        case 2:
            if (!selectedProgramId) {
                showAlert('Please select a program.', 'warning');
                return false;
            }
            return true;
        case 3:
            if (!selectedModules || selectedModules.length === 0) {
                showAlert('Please select at least one module.', 'warning');
                return false;
            }
            return true;
        case 4:
            if (!selectedLearningMode) {
                showAlert('Please select a learning mode.', 'warning');
                return false;
            }
            if (selectedLearningMode === 'asynchronous') {
                const startDate = document.getElementById('startDate');
                if (!startDate.value) {
                    showAlert('Please select a start date for asynchronous learning.', 'warning');
                    return false;
                }
            }
            return true;
        case 5:
            return validateAccountInfo();
        case 6:
            return validateFormInfo();
        default:
            return true;
    }
}

function loadStepContent() {
    switch (currentStep) {
        case 2:
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
    document.querySelectorAll('.selection-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to clicked card
    event.target.closest('.selection-card').classList.add('selected');
    
    // Store selection
    selectedPackageId = packageId;
    selectedProgramId = programId;
    packageModuleLimit = moduleCount;
    
    // Update hidden inputs
    document.getElementById('package_id').value = packageId;
    document.getElementById('program_id').value = programId;
    
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
    grid.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading programs...</div>';
    
    // For now, we'll use the selected program from package
    // In a real scenario, you might want to load all programs
    const programs = @json($programs);
    
    let programsHtml = '';
    programs.forEach(program => {
        programsHtml += `
            <div class="selection-card ${program.program_id == selectedProgramId ? 'selected' : ''}" 
                 onclick="selectProgram(${program.program_id})">
                <div class="card-header">
                    <h3 class="card-title">${program.program_name}</h3>
                </div>
                <div class="card-description">
                    ${program.program_description || 'Comprehensive professional program'}
                </div>
                <ul class="card-features">
                    <li>Professional certification</li>
                    <li>Industry-relevant skills</li>
                    <li>Expert instructors</li>
                    <li>Career advancement</li>
                </ul>
            </div>
        `;
    });
    
    grid.innerHTML = programsHtml;
    
    // If program is pre-selected, enable next button
    if (selectedProgramId) {
        document.getElementById('step2-next').disabled = false;
    }
}

// Load modules
function loadModules() {
    const grid = document.getElementById('modulesGrid');
    const limitSpan = document.getElementById('moduleLimit');
    
    grid.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading modules...</div>';
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
            <div class="module-card" data-module-id="${module.id}">
                <div class="module-header">
                    <input type="checkbox" class="module-checkbox" id="module_${module.id}" 
                           value="${module.id}" onchange="handleModuleSelection(this)">
                    <label for="module_${module.id}" class="module-title">${moduleName}</label>
                </div>
                <div class="module-description">
                    ${moduleDesc}
                </div>
                <div class="module-meta">
                    <span class="module-duration">
                        <i class="fas fa-clock"></i> ${module.duration || 'Flexible'}
                    </span>
                    <span class="module-level">${module.level || 'All Levels'}</span>
                </div>
                <div class="module-actions">
                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                            onclick="showCoursesModal(${module.id}, '${moduleName}')">
                        <i class="fas fa-list"></i> View Courses
                    </button>
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
            showAlert(`You can only select up to ${packageModuleLimit} modules.`, 'warning');
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

// Show courses modal
function showCoursesModal(moduleId, moduleName) {
    document.getElementById('moduleNameDisplay').textContent = moduleName;
    
    const container = document.getElementById('coursesContainer');
    container.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading courses...</div>';
    
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
    
    let coursesHtml = '<div class="row">';
    courses.forEach(course => {
        coursesHtml += `
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">${course.course_name}</h6>
                        <p class="card-text">${course.course_description || 'No description available'}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">Order: ${course.course_order}</span>
                            ${course.is_required ? '<span class="badge bg-danger">Required</span>' : '<span class="badge bg-success">Optional</span>'}
                        </div>
                        ${course.course_price ? `<div class="mt-2"><strong>Price: ₱${parseFloat(course.course_price).toFixed(2)}</strong></div>` : ''}
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-info" 
                                    onclick="showCourseLessons(${course.course_id}, '${course.course_name}')">
                                <i class="fas fa-eye"></i> View Lessons
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    coursesHtml += '</div>';
    
    container.innerHTML = coursesHtml;
}

// Show additional modules modal
function showAdditionalModules() {
    const container = document.getElementById('additionalModulesContainer');
    container.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading additional modules...</div>';
    
    // Load all modules for the program (not just selected package modules)
    fetch(`/get-program-modules?program_id=${selectedProgramId}&all=true`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.modules) {
                displayAdditionalModules(data.modules);
            } else {
                container.innerHTML = '<div class="alert alert-info">No additional modules available.</div>';
            }
        })
        .catch(error => {
            console.error('Error loading additional modules:', error);
            container.innerHTML = '<div class="alert alert-danger">Error loading modules. Please try again.</div>';
        });
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('additionalModulesModal'));
    modal.show();
}

// Display additional modules
function displayAdditionalModules(modules) {
    const container = document.getElementById('additionalModulesContainer');
    
    // Filter out already selected modules
    const selectedModuleIds = selectedModules.map(m => m.id);
    const availableModules = modules.filter(module => !selectedModuleIds.includes(module.id.toString()));
    
    if (availableModules.length === 0) {
        container.innerHTML = '<div class="alert alert-info">All available modules are already selected.</div>';
        return;
    }
    
    let modulesHtml = '<div class="row">';
    availableModules.forEach(module => {
        const moduleName = module.name || module.module_name || 'Unnamed Module';
        const moduleDesc = module.description || module.module_description || 'No description available';
        
        modulesHtml += `
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="${module.id}" 
                                   id="additional_module_${module.id}">
                            <label class="form-check-label" for="additional_module_${module.id}">
                                <h6 class="card-title">${moduleName}</h6>
                            </label>
                        </div>
                        <p class="card-text">${moduleDesc}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-secondary">${module.level || 'All Levels'}</span>
                            <span class="text-muted"><i class="fas fa-clock"></i> ${module.duration || 'Flexible'}</span>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-info" 
                                    onclick="showCoursesModal(${module.id}, '${moduleName}')">
                                <i class="fas fa-list"></i> View Courses
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    modulesHtml += '</div>';
    
    container.innerHTML = modulesHtml;
}

// Add selected additional modules
function addSelectedAdditionalModules() {
    const checkboxes = document.querySelectorAll('#additionalModulesContainer input[type="checkbox"]:checked');
    
    if (checkboxes.length === 0) {
        showAlert('Please select at least one module to add.', 'warning');
        return;
    }
    
    checkboxes.forEach(checkbox => {
        const moduleId = checkbox.value;
        const moduleCard = checkbox.closest('.card');
        const moduleTitle = moduleCard.querySelector('.card-title').textContent;
        
        // Add to additional modules array
        additionalModules.push({
            id: moduleId,
            name: moduleTitle,
            isAdditional: true
        });
    });
    
    // Update display or UI as needed
    showAlert(`${checkboxes.length} additional module(s) added successfully.`, 'success');
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('additionalModulesModal'));
    modal.hide();
    
    console.log('Additional modules added:', additionalModules);
}
</script>
@endsection
