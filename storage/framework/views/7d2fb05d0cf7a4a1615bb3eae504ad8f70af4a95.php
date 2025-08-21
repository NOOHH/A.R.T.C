

<?php $__env->startSection('title', 'Modular Enrollment - Multi-Step Form'); ?>
<?php $__env->startSection('hide_footer', true); ?>
<?php $__env->startSection('body_class', 'registration-page'); ?>

<?php
    // Check if user is already logged in
    $isUserLoggedIn = auth()->check() || session('user_id');
    $loggedInUser = auth()->check() ? auth()->user() : (session('user_id') ? \App\Models\User::find(session('user_id')) : null);
?>

<?php $__env->startPush('styles'); ?>
    <?php echo App\Helpers\UIHelper::getNavbarStyles(); ?>

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <script>
        // Define constants for consistency with Full_enrollment
        const CSRF_TOKEN = "<?php echo e(csrf_token()); ?>";
        const VALIDATE_URL = "<?php echo e(route('registration.validateFile')); ?>";
        const PREFILL_URL = "<?php echo e(route('registration.userPrefill')); ?>";
    </script>
     <link rel="stylesheet" href="<?php echo e(asset('css/ENROLLMENT/Modular_enrollment.css')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .already-enrolled {
            background-color: #f8f9fa !important;
            border-color: #dee2e6 !important;
            opacity: 0.7;
        }
        
        .already-enrolled .card-body {
            position: relative;
        }
        
        .already-enrolled .form-check-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .already-enrolled::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255, 193, 7, 0.1) 10px,
                rgba(255, 193, 7, 0.1) 20px
            );
            pointer-events: none;
            z-index: 1;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="form-container">
    <div class="form-wrapper">
        <!-- Stepper Progress -->
        <div class="stepper-progress">
            <div class="stepper">
                <div class="bar">
                    <div class="progress" id="progressBar" style="width: <?php echo e($isUserLoggedIn ? '20%' : '14.28%'); ?>;"></div>
                </div>
                <div class="step <?php echo e(!$isUserLoggedIn ? 'active' : ''); ?>" id="step-1">
                    <div class="circle">1</div>
                    <div class="label">Account Check</div>
                </div>
                <div class="step <?php echo e($isUserLoggedIn ? 'active' : ''); ?>" id="step-2">
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
                <?php if(!$isUserLoggedIn): ?>
                <div class="step" id="step-6">
                    <div class="circle">6</div>
                    <div class="label">Account</div>
                </div>
                <div class="step" id="step-7">
                    <div class="circle">7</div>
                    <div class="label">Form</div>
                </div>
                <?php else: ?>
                <div class="step" id="step-6">
                    <div class="circle">6</div>
                    <div class="label">Form</div>
                </div>
                <?php endif; ?>
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
        <div class="step-content <?php echo e(!$isUserLoggedIn ? 'active' : ''); ?>" id="content-1">
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
        <div class="step-content <?php echo e($isUserLoggedIn ? 'active' : ''); ?>" id="content-2">
            <div class="step-header mb-4">
                <h2 class="fw-bold text-center" style="font-size:2.5rem;">Choose Your Package</h2>
                <p class="text-center text-muted" style="font-size:1.15rem;">Select a learning package that suits your needs</p>
            </div>
            
            <!-- Bootstrap Carousel for Packages -->
            <div id="packageCarousel" class="carousel slide package-carousel-container" data-bs-ride="false">
                <div class="carousel-inner">
                    <?php $chunkSize = 2; ?>
                    <?php $__currentLoopData = $packages->chunk($chunkSize); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $packageChunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="carousel-item <?php echo e($index == 0 ? 'active' : ''); ?>">
                            <div class="d-flex justify-content-center gap-4 flex-wrap">
                                <?php $__currentLoopData = $packageChunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="package-card-pro card p-4 mb-3"
                                         onclick="selectPackage(<?php echo e(json_encode($package->package_id)); ?>, <?php echo e(json_encode($package->program_id)); ?>, <?php echo e(json_encode($package->module_count ?? $package->modules_count ?? 3)); ?>, <?php echo e(json_encode($package->selection_mode ?? 'modules')); ?>, <?php echo e(json_encode($package->course_count ?? 0)); ?>)"
                                         data-package-id="<?php echo e($package->package_id); ?>">
                                        <div class="card-body text-center">
                                            <h4 class="fw-bold mb-2"><?php echo e($package->package_name); ?></h4>
                                            <div class="text-primary fw-bold" style="font-size:2rem;">₱<?php echo e(number_format($package->amount, 2)); ?></div>
                                            <p class="text-muted mb-3" style="min-height:2rem;"><?php echo e($package->description ?? 'No description yet.'); ?></p>
                                            <ul class="list-unstyled text-start mx-auto" style="max-width:220px;">
                                                <?php if($package->selection_mode === 'courses'): ?>
                                                    <li><i class="bi bi-check2 text-success"></i> <?php echo e($package->course_count ?? 'All'); ?> courses included</li>
                                                <?php else: ?>
                                                    <li><i class="bi bi-check2 text-success"></i> <?php echo e($package->module_count ?? $package->modules_count ?? 3); ?> modules included</li>
                                                <?php endif; ?>
                                                <li><i class="bi bi-check2 text-success"></i> Self-paced learning</li>
                                                <li><i class="bi bi-check2 text-success"></i> Certificate upon completion</li>
                                                <li><i class="bi bi-check2 text-success"></i> Flexible scheduling</li>
                                                <?php
                                                    $periodParts = [];
                                                    if (!empty($package->access_period_years)) $periodParts[] = $package->access_period_years . ' Year' . ($package->access_period_years > 1 ? 's' : '');
                                                    if (!empty($package->access_period_months)) $periodParts[] = $package->access_period_months . ' Month' . ($package->access_period_months > 1 ? 's' : '');
                                                    if (!empty($package->access_period_days)) $periodParts[] = $package->access_period_days . ' Day' . ($package->access_period_days > 1 ? 's' : '');
                                                ?>
                                                <?php if(count($periodParts) > 0): ?>
                                                    <li><span class="badge bg-info text-dark">Access Period: <?php echo e(implode(' ', $periodParts)); ?></span></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <!-- Carousel Controls -->
                <?php if($packages->count() > $chunkSize): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#packageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#packageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                <?php endif; ?>
                
                <!-- Carousel Indicators -->
                <?php if($packages->chunk($chunkSize)->count() > 1): ?>
                    <div class="carousel-indicators">
                        <?php $__currentLoopData = $packages->chunk($chunkSize); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" data-bs-target="#packageCarousel" data-bs-slide-to="<?php echo e($index); ?>" 
                                    class="<?php echo e($index == 0 ? 'active' : ''); ?>" aria-label="Slide <?php echo e($index + 1); ?>"></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
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
                    <!-- Indicators will be dynamically generated -->
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="prevStep()">
                    <i class="bi bi-arrow-left me-2"></i> Previous
                </button>
                <button type="button" class="btn btn-primary btn-lg" onclick="nextStep()" disabled id="step3-next">
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
                <button type="button" class="btn btn-primary btn-lg" onclick="nextStep()" disabled id="step4-modules-next">
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
        <?php if(!$isUserLoggedIn): ?>
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
                            <input type="email" id="user_email" name="user_email" class="form-control" required>
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
                    
                    <?php if(DB::table('admin_settings')->where('setting_key', 'referral_enabled')->value('setting_value') === '1'): ?>
                    <div class="form-group" style="grid-column: 1 / span 2;">
                        <label for="referral_code">Referral Code <?php if(DB::table('admin_settings')->where('setting_key', 'referral_required')->value('setting_value') === '1'): ?> <span class="text-danger">*</span> <?php endif; ?></label>
                        <div class="referral-input-group">
                            <input type="text" id="referral_code" name="referral_code" class="form-control" 
                                   placeholder="Enter referral code (e.g., PROF01JDOE or DIR01SMITH)"
                                   <?php if(DB::table('admin_settings')->where('setting_key', 'referral_required')->value('setting_value') === '1'): ?> required <?php endif; ?>>
                            <button type="button" id="validateReferralBtn" class="btn-validate-referral" onclick="validateReferralCode()">
                                <i class="fas fa-check"></i> Validate
                            </button>
                        </div>
                        <div id="referralCodeError" class="error-message" style="display: none;"></div>
                        <div id="referralCodeSuccess" class="success-message" style="display: none;"></div>
                        <div class="form-text"><?php if(DB::table('admin_settings')->where('setting_key', 'referral_required')->value('setting_value') === '1'): ?> Required: <?php endif; ?> Enter the referral code provided by your professor or director</div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="login-prompt">
                    <p>Already have an account? <a href="<?php echo e(route('login')); ?>">Click here to login</a></p>
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
        <?php endif; ?>

        <!-- Step 7: Final Registration Form (for non-logged-in users) OR Step 6: Final Registration Form (for logged-in users) -->
        <div class="step-content" id="content-<?php echo e($isUserLoggedIn ? '6' : '7'); ?>">
            <div class="step-header">
                <h2>Complete Your Registration</h2>
                <?php if($isUserLoggedIn): ?>
                <p>Welcome back, <?php echo e($loggedInUser->user_firstname ?? 'User'); ?>! Complete your modular enrollment below.</p>
                <?php else: ?>
                <p>Fill in your personal and academic information.</p>
                <?php endif; ?>
            </div>
            <form action="<?php echo e(route('enrollment.modular.submit')); ?>" method="POST" enctype="multipart/form-data" class="registration-form" id="modularEnrollmentForm" novalidate>
                <?php echo csrf_field(); ?>
                <!-- Hidden inputs for form data -->
                <input type="hidden" name="enrollment_type" value="Modular">
                <input type="hidden" name="package_id" value="" id="packageIdInput">
                <input type="hidden" name="program_id" value="" id="hidden_program_id">
                <input type="hidden" name="plan_id" value="2">
                <input type="hidden" name="learning_mode" id="learning_mode" value="">
                <input type="hidden" name="selected_modules" id="selected_modules" value="">

                <!-- Dynamic Form Fields -->
                <?php if(isset($formRequirements) && $formRequirements->count() > 0): ?>
                    <?php 
                        $currentSection = null;
                        $hasFirstNameField = false;
                        $hasLastNameField = false;
                        
                        // Check if firstname and lastname fields exist in dynamic fields
                        foreach($formRequirements as $field) {
                            if(in_array($field->field_name, ['firstname', 'first_name', 'FirstName'])) {
                                $hasFirstNameField = true;
                            }
                            if(in_array($field->field_name, ['lastname', 'last_name', 'LastName'])) {
                                $hasLastNameField = true;
                            }
                        }
                    ?>
                    
                    <!-- Always show firstname and lastname fields first if they're not in dynamic fields -->
                    <?php if(!$hasFirstNameField || !$hasLastNameField): ?>
                            <?php if(!$hasFirstNameField): ?>
                                <div class="form-group">
                                    <label for="firstname" style="font-weight:700;">
                                        <i class="bi bi-person me-2"></i>First Name
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" name="firstname" id="firstname" class="form-control" required>
                                </div>
                            <?php endif; ?>
                            <?php if(!$hasLastNameField): ?>
                                <div class="form-group">
                                    <label for="lastname" style="font-weight:700;">
                                        <i class="bi bi-person me-2"></i>Last Name
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" name="lastname" id="lastname" class="form-control" required>
                                </div>
                            <?php endif; ?>

                    <?php endif; ?>
                    
                    <?php 
                        $currentSection = null;
                    ?>
                    <?php $__currentLoopData = $formRequirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($field->field_type === 'section'): ?>
                            <?php 
                                $currentSection = $field->section_name ?: $field->field_label;
                            ?>
                            <h3 style="margin-top:2.1rem; margin-bottom:1rem; color:#2c3e50; border-bottom:2px solid #3498db; padding-bottom:0.5rem;">
                                <i class="bi bi-folder me-2"></i><?php echo e($currentSection); ?>

                            </h3>
                        <?php else: ?>
                            <?php if($field->field_name !== 'Cert_of_Grad'): ?>
                                <div class="form-group">
                                    <?php if($currentSection): ?>
                                        <div class="section-indicator" style="font-size:0.9rem; color:#6c757d; margin-bottom:0.5rem;">
                                            <?php echo e($currentSection); ?>

                                        </div>
                                    <?php endif; ?>
                                    <label for="<?php echo e($field->field_name); ?>" <?php if($field->is_bold): ?> style="font-weight:bold;" <?php endif; ?>>
                                        <?php echo e($field->field_label ?: $field->field_name); ?>

                                        <?php if($field->is_required): ?> <span class="required">*</span> <?php endif; ?>
                                    </label>
                                    <?php if($field->field_type === 'text'): ?>
                                        <input type="text" name="<?php echo e($field->field_name); ?>" id="<?php echo e($field->field_name); ?>" 
                                            class="form-control" value="<?php echo e(old($field->field_name, $student->{$field->field_name} ?? '')); ?>"
                                            <?php echo e($field->is_required ? 'required' : ''); ?>>
                                    <?php elseif($field->field_type === 'email'): ?>
                                        <input type="email" name="<?php echo e($field->field_name); ?>" id="<?php echo e($field->field_name); ?>" 
                                            class="form-control" value="<?php echo e(old($field->field_name, $student->{$field->field_name} ?? '')); ?>"
                                            <?php echo e($field->is_required ? 'required' : ''); ?>>
                                    <?php elseif($field->field_type === 'number'): ?>
                                        <input type="number" name="<?php echo e($field->field_name); ?>" id="<?php echo e($field->field_name); ?>" 
                                            class="form-control" value="<?php echo e(old($field->field_name, $student->{$field->field_name} ?? '')); ?>"
                                            <?php echo e($field->is_required ? 'required' : ''); ?>>
                                    <?php elseif($field->field_type === 'date'): ?>
                                        <input type="date" name="<?php echo e($field->field_name); ?>" id="<?php echo e($field->field_name); ?>" 
                                            class="form-control" value="<?php echo e(old($field->field_name, $student->{$field->field_name} ?? '')); ?>"
                                            <?php echo e($field->is_required ? 'required' : ''); ?>>
                                    <?php elseif($field->field_type === 'file'): ?>
                                        <input type="file" name="<?php echo e($field->field_name); ?>" id="<?php echo e($field->field_name); ?>" 
                                            class="form-control" accept=".jpg,.jpeg,.png,.pdf" 
                                            onchange="handleFileUpload(this)" <?php echo e($field->is_required ? 'required' : ''); ?>>
                                        <?php if(isset($student) && $student->{$field->field_name}): ?>
                                            <div class="existing-file-info mt-2">
                                                <small class="text-success">
                                                    <i class="bi bi-check-circle"></i> File already uploaded: <?php echo e($student->{$field->field_name}); ?>

                                                </small>
                                            </div>
                                        <?php endif; ?>
                                        <small class="form-text text-muted">Upload <?php echo e($field->field_label ?: $field->field_name); ?> (JPG, PNG, PDF only)</small>
                                    <?php elseif($field->field_type === 'select'): ?>
                                        <select name="<?php echo e($field->field_name); ?>" id="<?php echo e($field->field_name); ?>" 
                                                class="form-select" <?php echo e($field->is_required ? 'required' : ''); ?>>
                                            <option value="">Select <?php echo e($field->field_label ?: $field->field_name); ?></option>
                                            <?php if($field->field_options): ?>
                                                <?php
                                                    $options = [];
                                                    if (is_string($field->field_options)) {
                                                        $decoded = json_decode($field->field_options, true);
                                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                            $options = $decoded;
                                                        } else {
                                                            // fallback: treat as comma-separated string if not valid JSON
                                                            $options = array_filter(array_map('trim', explode(',', $field->field_options)));
                                                        }
                                                    } elseif (is_array($field->field_options)) {
                                                        $options = $field->field_options;
                                                    }
                                                    $selectedValue = old($field->field_name, $student->{$field->field_name} ?? '');
                                                ?>
                                                <?php if(is_array($options)): ?>
                                                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($option !== ''): ?>
                                                            <option value="<?php echo e($option); ?>" <?php echo e($selectedValue == $option ? 'selected' : ''); ?>><?php echo e($option); ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </select>
                                    <?php elseif($field->field_type === 'textarea'): ?>
                                        <textarea name="<?php echo e($field->field_name); ?>" id="<?php echo e($field->field_name); ?>" 
                                                class="form-control" rows="3" <?php echo e($field->is_required ? 'required' : ''); ?>><?php echo e(old($field->field_name, $student->{$field->field_name} ?? '')); ?></textarea>
                                    <?php elseif($field->field_type === 'checkbox'): ?>
                                        <?php
                                            $isChecked = old($field->field_name, $student->{$field->field_name} ?? false);
                                        ?>
                                        <div class="form-check">
                                            <input type="checkbox" name="<?php echo e($field->field_name); ?>" id="<?php echo e($field->field_name); ?>" 
                                                class="form-check-input" value="1" <?php echo e($isChecked ? 'checked' : ''); ?>

                                                <?php echo e($field->is_required ? 'required' : ''); ?>>
                                            <label class="form-check-label" for="<?php echo e($field->field_name); ?>">
                                                <?php echo e($field->field_label ?: $field->field_name); ?>

                                            </label>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($field->help_text) && $field->help_text): ?>
                                        <small class="form-text text-muted"><?php echo e($field->help_text); ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
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
                <?php endif; ?>
                <!-- Education Level Selection (after dynamic fields) -->
                <div class="form-group" style="margin-bottom:2rem;">
                    <label for="educationLevel" style="font-size:1.17rem;font-weight:700;">
                        <i class="bi bi-mortarboard me-2"></i>Education Level
                        <span class="required">*</span>
                    </label>
                    <select name="education_level" id="educationLevel" class="form-select" required onchange="toggleEducationLevelRequirements()">
                        <option value="">Select Education Level</option>
                        <?php if(isset($educationLevels) && $educationLevels->count() > 0): ?>
                            <?php $__currentLoopData = $educationLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($level->level_name); ?>" 
                                        data-file-requirements="<?php echo e(json_encode($level->getFileRequirementsForPlan($enrollmentType ?? 'modular'))); ?>">
                                    <?php echo e($level->level_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <!-- No education levels configured - admin needs to set them up -->
                            <option value="" disabled>No education levels configured. Please contact administrator.</option>
                        <?php endif; ?>
                    </select>
                </div>
                <!-- Dynamic Education Level File Requirements -->
                <div id="educationLevelRequirements" style="display: none;"></div>
                <div class="form-group" style="margin-top:2.2rem;">
                    <label for="programSelect" style="font-size:1.17rem;font-weight:700;"><i class="bi bi-book me-2"></i>Program <span class="text-danger">*</span></label>
                    <select name="program_id" class="form-select" required id="programSelect" onchange="onProgramSelectionChange();">
                        <option value="">Select Program</option>
                        <!-- Options will be dynamically populated by JS using getAvailableProgramsForStudent() -->
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
<?php echo nl2br(e(\App\Models\AdminSetting::getValue('modular_enrollment_terms', 'By registering for this modular program, you agree to abide by all policies, privacy guidelines, and usage restrictions as provided by our review center. Please read the full document before accepting.'))); ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="acceptTerms()">I Accept</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Global variables - consolidated to avoid temporal dead zone issues
    let currentStep = <?php echo e($isUserLoggedIn ? 2 : 1); ?>; // Start at step 2 for logged-in users, step 1 for new users
    let totalSteps = <?php echo e($isUserLoggedIn ? 6 : 7); ?>; // Updated total steps: 1 (Account Check) + 5 original steps
    let isUserLoggedIn = <?php echo json_encode($isUserLoggedIn, 15, 512) ?>;
    
    // Package selection variables (moved from earlier script block)
    let selectedPackageId = null;
    let packageSelectionMode = 'modules';
    let packageModuleLimit = null;
    let packageCourseLimit = null;
    
    // Other form variables
    let selectedProgramId = null;
    let selectedProgramIds = []; // Array for multi-selection support
    let selectedModules = [];
    let selectedLearningMode = null;
    let selectedAccountType = null;
    
    // Course selection variables
    let currentModuleId = null;
    let selectedCourses = {};
    let extraModulePrice = 0;
    
    // Timeout management
    let loadProgramsTimeoutId = null;
    
    // CSRF token is now defined in the head section as a constant
    
    // Data from controller
    window.programs = <?php echo json_encode($programs ?? [], 15, 512) ?>;
    window.packages = <?php echo json_encode($packages ?? [], 15, 512) ?>;
    window.educationLevels = <?php echo json_encode($educationLevels ?? [], 15, 512) ?>;
    window.formRequirements = <?php echo json_encode($formRequirements ?? [], 15, 512) ?>;
    window.student = <?php echo json_encode($student ?? null, 15, 512) ?>;
    window.modularPlan = <?php echo json_encode($modularPlan ?? null, 15, 512) ?>;
    
    // Debug: Log controller data
    console.log('Controller Data:', {
        programs: window.programs.length,
        packages: window.packages.length,
        educationLevels: window.educationLevels.length,
        student: !!window.student,
        modularPlan: !!window.modularPlan
    });
    
    // User session data for API calls
    const CURRENT_USER_ID = <?php echo json_encode(session('user_id'), 15, 512) ?>;
    const CURRENT_USER_NAME = <?php echo json_encode(session('user_name'), 15, 512) ?>;
    const CURRENT_USER_ROLE = <?php echo json_encode(session('user_role'), 15, 512) ?>;
    
    // Debug: Log session data
    console.log('Modular Enrollment Session Data:', {
        CURRENT_USER_ID,
        CURRENT_USER_NAME,
        CURRENT_USER_ROLE,
        globalMyId: window.myId,
        globalIsAuthenticated: window.isAuthenticated
    });
    
    // Use global variables as fallback if session data is not available
    const EFFECTIVE_USER_ID = CURRENT_USER_ID || window.myId;
    const EFFECTIVE_USER_NAME = CURRENT_USER_NAME || window.myName;
    
    console.log('Effective User Data:', {
        EFFECTIVE_USER_ID,
        EFFECTIVE_USER_NAME
    });
    
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
            window.location.href = "<?php echo e(route('login')); ?>";
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
        console.log('nextStep called, current step before:', currentStep);
        console.log('totalSteps:', totalSteps);
        
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
            
            console.log('current step after transition:', currentStep);
            
            // Copy data when moving to final step
            if ((isUserLoggedIn && currentStep === 6) || (!isUserLoggedIn && currentStep === 7)) {
                copyStepperDataToFinalForm();
            }
            
            updateStepper();
            loadStepContent();
        } else {
            console.log('Already at last step, cannot proceed');
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
                    contentElement.style.display = 'block';
                    contentElement.style.visibility = 'visible';
                    contentElement.style.opacity = '1';
                } else {
                    contentElement.classList.remove('active');
                    contentElement.style.display = 'none';
                    contentElement.style.visibility = 'hidden';
                    contentElement.style.opacity = '0';
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
    
    // Load content for each step
    function loadStepContent() {
        console.log('Loading content for step:', currentStep);
        
        // First, ensure the current step content is visible
        updateStepper();
        
        // Debug: Check step visibility after updateStepper
        const currentContent = document.getElementById(`content-${currentStep}`);
        console.log(`Step ${currentStep} content element:`, currentContent);
        console.log(`Step ${currentStep} content display:`, currentContent ? currentContent.style.display : 'N/A');
        console.log(`Step ${currentStep} content classes:`, currentContent ? currentContent.className : 'N/A');
        
        // Then load the specific content for the step
        switch (currentStep) {
            case 2:
                // Package selection step - no programs to load here
                console.log('Step 2: Package selection - no programs to load');
                break;
            case 3:
                // Program selection step - load programs here where programsGrid exists
                console.log('Step 3: Program selection - loading programs');
                
                // Reset program selection when entering step 3
                selectedProgramId = null;
                const programIdField = document.getElementById('program_id');
                if (programIdField) {
                    programIdField.value = '';
                }
                const step3NextBtn = document.getElementById('step3-next');
                if (step3NextBtn) {
                    step3NextBtn.disabled = true;
                }
                
                // Check if programCarousel exists before calling loadPrograms
                const programCarousel = document.getElementById('programCarousel');
                console.log('programCarousel exists before setTimeout:', !!programCarousel);
                
                // Delay loading programs to ensure DOM is ready and step is visible
                setTimeout(() => {
                    console.log('Step 3 setTimeout callback - about to call loadPrograms');
                    console.log('Current step in setTimeout:', currentStep);
                    
                    // Double-check that we're still on step 3 before loading programs
                    if (currentStep !== 3) {
                        console.log('Step changed during setTimeout, not loading programs');
                        return;
                    }
                    
                    const carouselAfterDelay = document.getElementById('programCarousel');
                    console.log('programCarousel exists after delay:', !!carouselAfterDelay);
                    loadPrograms();
                }, 200);
                break;
            case 4:
                // Module selection step - load modules for selected program
                console.log('Step 4: Module selection - loading modules');
                
                // Check button state when entering step 4
                const step4NextBtn = document.getElementById('step4-modules-next');
                if (step4NextBtn) {
                    console.log('step4-modules-next button state when entering step 4:', {
                        disabled: step4NextBtn.disabled,
                        selectedModulesLength: selectedModules.length,
                        packageSelectionMode: packageSelectionMode
                    });
                }
                
                setTimeout(() => {
                    console.log('Step 4 setTimeout callback - about to call loadModules');
                    console.log('Current step in setTimeout:', currentStep);
                    
                    // Double-check that we're still on step 4 before loading modules
                    if (currentStep !== 4) {
                        console.log('Step changed during setTimeout, not loading modules');
                        return;
                    }
                    
                    loadModules();
                }, 200);
                break;
            case 5:
                setupAccountForm();
                break;
            case 6:
                loadDynamicFormFields();
                // For logged-in users, populate form fields immediately
                if (isUserLoggedIn) {
                    setTimeout(copyStepperDataToFinalForm, 100);
                }
                break;
            case 7:
                // For non-logged-in users, load dynamic form fields and populate them
                loadDynamicFormFields();
                setTimeout(function() {
                    copyStepperDataToFinalForm();
                    console.log('Step 7 loaded: copying data to final form after delay');
                    
                    // Additional attempt to populate visible fields after longer delay
                    setTimeout(function() {
                        forcePopulateVisibleFields();
                        console.log('🔧 Force populate attempt completed');
                        // One more attempt with even longer delay for slow-loading dynamic forms
                        setTimeout(function() {
                            forcePopulateVisibleFields();
                            console.log('🔧 Final force populate attempt completed');
                        }, 1000);
                    }, 500);
                }, 100);
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
        
        // Store selection (backward compatibility)
        selectedProgramId = programId;
        
        // Also update multi-selection array for new functionality
        selectedProgramIds = [programId];
        
        // Update hidden input
        document.getElementById('program_id').value = programId;
        
        // Enable next button
        document.getElementById('step3-next').disabled = false;
        
        console.log('Updated selectedProgramIds:', selectedProgramIds);
    }
    window.selectProgram = selectProgram;
    
    // Multi-selection function for program selection
    function toggleProgramSelection(programId) {
        if (event) event.stopPropagation();
        
        // Toggle selection in array
        const index = selectedProgramIds.indexOf(programId);
        if (index > -1) {
            selectedProgramIds.splice(index, 1);
        } else {
            selectedProgramIds.push(programId);
        }
        
        // Update UI - card styling
        const card = document.querySelector(`[onclick*="toggleProgramSelection(${programId})"]`);
        if (card) {
            if (selectedProgramIds.includes(programId)) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        }
        
        // Update checkbox
        const checkbox = document.getElementById(`program_${programId}`);
        if (checkbox) {
            checkbox.checked = selectedProgramIds.includes(programId);
        }
        
        // Update hidden input with comma-separated IDs
        const hiddenInput = document.getElementById('program_id');
        if (hiddenInput) {
            hiddenInput.value = selectedProgramIds.join(',');
        }
        
        // Enable/disable next button based on selection
        const nextButton = document.getElementById('step3-next');
        if (nextButton) {
            nextButton.disabled = selectedProgramIds.length === 0;
        }
        
        // Also update the first selected program for backward compatibility
        selectedProgramId = selectedProgramIds.length > 0 ? selectedProgramIds[0] : null;
        
        console.log('Selected programs:', selectedProgramIds);
    }
    window.toggleProgramSelection = toggleProgramSelection;
    
    // Load programs from the database using the filtered API endpoint
    function loadPrograms() {
        console.log('loadPrograms called');
        console.log('Current step:', currentStep);
        console.log('DOM ready state:', document.readyState);
        
        // If we are not on step 3, stop trying to load programs
        if (currentStep !== 3) {
            console.log('loadPrograms: Not on step 3, stopping further retries.');
            if (loadProgramsTimeoutId) {
                clearTimeout(loadProgramsTimeoutId);
                loadProgramsTimeoutId = null;
            }
            return;
        }
        
        // Add retry counter to prevent infinite loops
        if (!window.loadProgramsRetryCount) {
            window.loadProgramsRetryCount = 0;
        }
        
        // Wait for DOM to be ready if programCarousel doesn't exist yet
        const carousel = document.getElementById('programCarousel');
        console.log('programCarousel element found:', !!carousel);
        console.log('programCarousel element:', carousel);
        
        if (!carousel) {
            window.loadProgramsRetryCount++;
            console.log('programCarousel element not found, waiting for DOM... (attempt ' + window.loadProgramsRetryCount + ')');
            
            // Debug: Check if step 3 content is visible
            const step3Content = document.getElementById('content-3');
            console.log('Step 3 content element:', step3Content);
            console.log('Step 3 content display:', step3Content ? step3Content.style.display : 'N/A');
            console.log('Step 3 content classes:', step3Content ? step3Content.className : 'N/A');
            
            // Debug: Check all elements with 'program' in the ID
            const allProgramElements = document.querySelectorAll('[id*="program"]');
            console.log('All elements with "program" in ID:', Array.from(allProgramElements).map(el => el.id));
            
            // Limit retries to prevent infinite loop
            if (window.loadProgramsRetryCount > 50) { // 5 seconds max
                console.error('programCarousel element not found after 50 attempts, stopping retry');
                return;
            }
            
            loadProgramsTimeoutId = setTimeout(loadPrograms, 100);
            return;
        }
        
        // Reset retry counter on success
        window.loadProgramsRetryCount = 0;
        
        // Show loading spinner in the carousel
        const carouselInner = carousel.querySelector('.carousel-inner');
        if (carouselInner) {
            carouselInner.innerHTML = '<div class="carousel-item active"><div class="loading-spinner"><i class="fas fa-spinner fa-spin fa-2x"></i> Loading programs...</div></div>';
        }
        
        // Fetch filtered programs based on student's current enrollments
        const apiUrl = '/api/enrollment/available-programs' + (EFFECTIVE_USER_ID ? `?user_id=${EFFECTIVE_USER_ID}` : '');
        console.log('API URL:', apiUrl);
        
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-User-ID': EFFECTIVE_USER_ID || '',
                'Content-Type': 'application/json'
            },
            credentials: 'include' // Include session cookies
        })
        .then(response => response.json())
        .then(data => {
            console.log('Available programs response:', data);
            if (data.success && data.programs) {
                window.availableProgramsForModular = data.programs;
                displayPrograms(data.programs);
            } else if (Array.isArray(data)) {
                // Some endpoints return array directly
                window.availableProgramsForModular = data;
                displayPrograms(data);
            } else {
                const carouselInner = carousel.querySelector('.carousel-inner');
                if (carouselInner) {
                    carouselInner.innerHTML = '<div class="carousel-item active"><div class="alert alert-info">No programs available for enrollment.</div></div>';
                }
            }
        })
        .catch(error => {
            console.error('Error fetching available programs:', error);
            const carouselInner = carousel.querySelector('.carousel-inner');
            if (carouselInner) {
                carouselInner.innerHTML = '<div class="carousel-item active"><div class="alert alert-danger">Error loading programs. Please try again.</div></div>';
            }
        });
    }
    // Display programs in the carousel (MAIN FUNCTION - processes API data correctly)
    function displayPrograms(programs) {
        console.log('displayPrograms called with:', programs);
        console.log('programs length:', programs.length);
        console.log('programs type:', typeof programs);
        console.log('programs is array:', Array.isArray(programs));
        
        const carousel = document.getElementById('programCarousel');
        console.log('programCarousel element found:', !!carousel);
        
        if (!programs || programs.length === 0) {
            console.log('No programs to display');
            const carouselInner = carousel.querySelector('.carousel-inner');
            if (carouselInner) {
                carouselInner.innerHTML = '<div class="carousel-item active"><div class="alert alert-info">No programs available for enrollment.</div></div>';
            }
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
        
        console.log('Program chunks created:', programChunks);
        console.log('Number of chunks:', programChunks.length);
        
        // Create carousel slides with multi-selection support
        programChunks.forEach((chunk, index) => {
            console.log(`Creating slide ${index} with programs:`, chunk);
            const isActive = index === 0 ? 'active' : '';
            let slideHtml = `<div class="carousel-item ${isActive}">
                <div class="row justify-content-center">`;
                
            chunk.forEach(program => {
                const isSelected = selectedProgramIds.includes(program.program_id);
                slideHtml += `
                    <div class="col-md-5 mb-4">
                        <div class="card selection-card h-100 ${isSelected ? 'selected' : ''}" 
                             onclick="toggleProgramSelection(${program.program_id})" style="cursor:pointer;">
                            <div class="card-body text-center position-relative">
                                <div class="form-check position-absolute" style="top: 10px; right: 15px;">
                                    <input class="form-check-input" type="checkbox" 
                                           id="program_${program.program_id}" 
                                           ${isSelected ? 'checked' : ''}
                                           onchange="toggleProgramSelection(${program.program_id})"
                                           style="transform: scale(1.2);">
                                </div>
                                <h4 class="card-title mt-2">${program.program_name}</h4>
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
        console.log('Indicators container found:', !!indicatorsContainer);
        console.log('Program chunks length:', programChunks.length);
        
        if (programChunks.length > 1) {
            console.log('Multiple slides detected, showing controls');
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
            const prevControl = document.querySelector('#programCarousel .carousel-control-prev');
            const nextControl = document.querySelector('#programCarousel .carousel-control-next');
            console.log('Prev control found:', !!prevControl);
            console.log('Next control found:', !!nextControl);
            
            if (prevControl) prevControl.style.display = 'block';
            if (nextControl) nextControl.style.display = 'block';
        } else {
            console.log('Single slide detected, hiding controls');
            indicatorsContainer.style.display = 'none';
            const prevControl = document.querySelector('#programCarousel .carousel-control-prev');
            const nextControl = document.querySelector('#programCarousel .carousel-control-next');
            if (prevControl) prevControl.style.display = 'none';
            if (nextControl) nextControl.style.display = 'none';
        }
        
        console.log('displayPrograms completed successfully');
        console.log('Final carousel HTML length:', carouselInner.innerHTML.length);
        console.log('Final carousel HTML preview:', carouselInner.innerHTML.substring(0, 200) + '...');
        
        // Initialize Bootstrap carousel if available
        if (typeof bootstrap !== 'undefined' && bootstrap.Carousel) {
            console.log('Initializing Bootstrap carousel');
            const carouselElement = document.getElementById('programCarousel');
            if (carouselElement) {
                new bootstrap.Carousel(carouselElement, {
                    interval: false,
                    wrap: false
                });
            }
        } else {
            console.log('Bootstrap carousel not available');
        }
        
        // Add inline styles to ensure carousel controls are visible
        if (programChunks.length > 1) {
            const carouselContainer = document.getElementById('programCarousel');
            if (carouselContainer) {
                carouselContainer.style.marginLeft = '60px';
                carouselContainer.style.marginRight = '60px';
            }
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
    console.log('loadModules called');
    console.log('Current step:', currentStep);
    console.log('selectedPackageId:', selectedPackageId);
    console.log('selectedProgramId:', selectedProgramId);
    console.log('window.selectedPackageId:', window.selectedPackageId);
    console.log('window.selectedProgramId:', window.selectedProgramId);
    
    // If we are not on step 4, stop trying to load modules
    if (currentStep !== 4) {
        console.log('loadModules: Not on step 4, stopping.');
        return;
    }
    
    const carousel = document.getElementById('moduleCarousel');
    const limitSpan = document.getElementById('moduleLimit');
    
    console.log('moduleCarousel element found:', !!carousel);
    console.log('moduleCarousel element:', carousel);
    
    if (!carousel) {
        console.error('moduleCarousel element not found');
        return;
    }
    
    // Show loading spinner in the carousel
    const carouselInner = carousel.querySelector('.carousel-inner');
    if (carouselInner) {
        carouselInner.innerHTML = '<div class="carousel-item active"><div class="loading-spinner"><i class="fas fa-spinner fa-spin fa-2x"></i> Loading modules...</div></div>';
    }
    
    if (limitSpan) {
        limitSpan.textContent = packageModuleLimit || 3;
    }

    // Get the selected program ID (this is the key fix)
    const programId = selectedProgramId || window.selectedProgramId;
    
    console.log('Final programId to use:', programId);
    
    if (!programId) {
        console.error('No program selected');
        const carouselInner = carousel.querySelector('.carousel-inner');
        if (carouselInner) {
            carouselInner.innerHTML = '<div class="carousel-item active"><div class="alert alert-warning">Please select a program first.</div></div>';
        }
        return;
    }
    
    console.log('Loading modules for program ID:', programId);
    
    // Fetch modules for the selected program (not package)
    console.log('Making API call to:', `/api/programs/${programId}/modules`);
    
    fetch(`/api/programs/${programId}/modules`)
        .then(response => {
            console.log('API response status:', response.status);
            console.log('API response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Modules API response:', data);
            console.log('Response success:', data.success);
            console.log('Response modules count:', data.modules ? data.modules.length : 'N/A');
            console.log('Response modules:', data.modules);
            
            if (data.success && data.modules && data.modules.length > 0) {
                const modules = data.modules.map(module => ({
                    ...module,
                    module_id: module.module_id || module.modules_id || module.id,
                    module_name: module.module_name || module.name,
                    description: module.description || module.module_description
                }));
                
                console.log('Processed modules:', modules);
                console.log('selectedModules before processAndDisplayModules:', selectedModules);
                processAndDisplayModules(modules);
            } else {
                console.log('No modules found or API returned error');
                if (data.debug_info) {
                    console.log('Debug info:', data.debug_info);
                }
                const carouselInner = carousel.querySelector('.carousel-inner');
                if (carouselInner) {
                    carouselInner.innerHTML = '<div class="carousel-item active"><div class="alert alert-info">No modules available for this program.</div></div>';
                }
            }
        })
        .catch(error => {
            console.error('Error loading modules:', error);
            const carouselInner = carousel.querySelector('.carousel-inner');
            if (carouselInner) {
                carouselInner.innerHTML = '<div class="carousel-item active"><div class="alert alert-danger">Error loading modules. Please try again.</div></div>';
            }
        });
}

    
    function processAndDisplayModules(allModules) {
        console.log('processAndDisplayModules called with:', allModules);
        console.log('allModules length:', allModules.length);
        console.log('allModules type:', typeof allModules);
        console.log('allModules is array:', Array.isArray(allModules));
        
        if (allModules.length === 0) {
            console.log('No modules to process, showing empty message');
            const carousel = document.getElementById('moduleCarousel');
            if (carousel) {
                const carouselInner = carousel.querySelector('.carousel-inner');
                if (carouselInner) {
                    carouselInner.innerHTML = '<div class="carousel-item active"><div class="alert alert-info">No modules available for the selected program(s).</div></div>';
                }
            } else {
                console.error('moduleCarousel element not found in processAndDisplayModules');
            }
            return;
        }
        
        // Debug: Log all module IDs to see if there are duplicates
        console.log('All module IDs:', allModules.map(m => m.module_id));
        console.log('All module names:', allModules.map(m => m.module_name));
        
        // Remove duplicates based on module_id (but keep all modules since they have different IDs)
        const uniqueModules = allModules.filter((module, index, self) => 
            index === self.findIndex(m => m.module_id === module.module_id)
        );
        
        console.log('Unique module IDs:', uniqueModules.map(m => m.module_id));
        console.log('Unique module names:', uniqueModules.map(m => m.module_name));
        
        // Additional debug: Check if any modules are being filtered out incorrectly
        if (uniqueModules.length !== allModules.length) {
            console.warn('WARNING: Modules were filtered out!');
            console.warn('Original count:', allModules.length);
            console.warn('Filtered count:', uniqueModules.length);
            
            const originalIds = allModules.map(m => m.module_id);
            const filteredIds = uniqueModules.map(m => m.module_id);
            const removedIds = originalIds.filter(id => !filteredIds.includes(id));
            console.warn('Removed module IDs:', removedIds);
        }
        
        console.log('Unique modules after filtering:', uniqueModules);
        console.log('Unique modules length:', uniqueModules.length);
        
        console.log('About to call displayModules with:', uniqueModules);
        displayModules(uniqueModules);
    }
    
    // Display modules
    function displayModules(modules) {
        console.log('displayModules called with:', modules);
        console.log('modules length:', modules.length);
        console.log('modules type:', typeof modules);
        console.log('modules is array:', Array.isArray(modules));
        
        const carousel = document.getElementById('moduleCarousel');
        console.log('moduleCarousel element found:', !!carousel);
        console.log('moduleCarousel element:', carousel);
        
        if (!carousel) {
            console.error('moduleCarousel element not found');
            return;
        }
        
        const carouselInner = carousel.querySelector('.carousel-inner');
        const indicators = carousel.querySelector('.carousel-indicators');
        
        console.log('carouselInner found:', !!carouselInner);
        console.log('indicators found:', !!indicators);
        
        if (!modules || modules.length === 0) {
            console.log('No modules to display, showing empty message');
            if (carouselInner) {
                carouselInner.innerHTML = '<div class="carousel-item active"><div class="alert alert-info">No modules available for this program.</div></div>';
            }
            if (indicators) {
                indicators.innerHTML = '';
            }
            return;
        }
        
        // Clear existing content
        carouselInner.innerHTML = '';
        indicators.innerHTML = '';
        
        console.log('Cleared carousel content, about to create slides');
        
        // Group modules into slides (2 modules per slide)
        const modulesPerSlide = 2;
        const slides = [];
        for (let i = 0; i < modules.length; i += modulesPerSlide) {
            slides.push(modules.slice(i, i + modulesPerSlide));
        }
        
        console.log('Created slides array:', slides);
        console.log('Number of slides:', slides.length);
        
        // Create carousel slides
        slides.forEach((slideModules, slideIndex) => {
            console.log(`Creating slide ${slideIndex} with modules:`, slideModules);
            const isActive = slideIndex === 0 ? 'active' : '';
            
            let modulesHtml = '';
            slideModules.forEach(module => {
                const moduleName = module.module_name || 'Unnamed Module';
                const moduleDesc = module.description || 'No description available';
                const programInfo = module.program_name ? `<small class="text-muted">From: ${module.program_name}</small><br>` : '';
                
                modulesHtml += `
                    <div class="col-md-6 mb-4">
                        <div class="module-card" data-module-id="${module.module_id}">
                            <div class="card module-card h-100">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input module-checkbox" id="module_${module.module_id}" 
                                               value="${module.module_id}" onchange="handleModuleSelection(this)">
                                        <label class="form-check-label module-title" for="module_${module.module_id}">${moduleName}</label>
                                    </div>
                                    ${programInfo}
                                    <p class="card-text module-description">${moduleDesc}</p>
                                    <div class="module-meta">
                                        <span class="module-duration">
                                            <i class="bi bi-clock"></i> ${module.duration || 'Flexible'}
                                        </span>
                                        <span class="module-level">${module.level || 'All Levels'}</span>
                                    </div>
                                    <div class="module-actions">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                onclick="showCoursesModal(${module.module_id}, '${moduleName}')">
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
        
        console.log('displayModules completed successfully');
        console.log('Final carousel HTML length:', carouselInner.innerHTML.length);
        console.log('Final carousel HTML preview:', carouselInner.innerHTML.substring(0, 200) + '...');
        console.log('selectedModules after displayModules:', selectedModules);
        
        // Check button state after modules are displayed
        const step4NextBtn = document.getElementById('step4-modules-next');
        if (step4NextBtn) {
            console.log('step4-modules-next button state after displayModules:', {
                disabled: step4NextBtn.disabled,
                selectedModulesLength: selectedModules.length,
                packageSelectionMode: packageSelectionMode
            });
        }
        
        // Add inline styles to ensure carousel controls are visible
        if (slides.length > 1) {
            const carouselContainer = document.getElementById('moduleCarousel');
            if (carouselContainer) {
                carouselContainer.style.marginLeft = '60px';
                carouselContainer.style.marginRight = '60px';
            }
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
        const step4NextBtn = document.getElementById('step4-modules-next');
        console.log('step4-modules-next button found:', !!step4NextBtn);
        console.log('Button state variables:', {
            packageSelectionMode: packageSelectionMode,
            packageModuleLimit: packageModuleLimit,
            packageCourseLimit: packageCourseLimit,
            selectedModulesLength: selectedModules.length,
            selectedCourses: selectedCourses
        });
        
        if (packageSelectionMode === 'courses') {
            // For course-based packages, check total course count across all modules
            let totalSelectedCourses = 0;
            Object.values(selectedCourses).forEach(courses => {
                totalSelectedCourses += courses.length;
            });
            const shouldDisable = totalSelectedCourses === 0 || (packageCourseLimit && totalSelectedCourses < packageCourseLimit);
            if (step4NextBtn) {
                step4NextBtn.disabled = shouldDisable;
                console.log('Course mode: step4-modules-next disabled =', shouldDisable, 'totalSelectedCourses =', totalSelectedCourses);
            }
        } else {
            // For module-based packages, check module count
            const shouldDisable = selectedModules.length === 0;
            if (step4NextBtn) {
                step4NextBtn.disabled = shouldDisable;
                console.log('Module mode: step4-modules-next disabled =', shouldDisable, 'selectedModules.length =', selectedModules.length);
            }
        }
        
        console.log('Selected modules with courses:', selectedModules);
        console.log('selectedModules array length:', selectedModules.length);
        console.log('selectedModules array content:', JSON.stringify(selectedModules));
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
                // Check if response is OK first
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('HTTP Error:', response.status, errorText);
                    throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 100)}`);
                }
                data = await response.json();
            } catch (e) {
                console.error('Response parsing error:', e);
                throw new Error('Server error: ' + e.message);
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
        
        <?php if($isUserLoggedIn): ?>
        // Auto-fill user data for logged-in users
        console.log('User is logged in, skipping account data collection');
        
        // Auto-fill firstname/lastname fields in the form
        const firstnameField = document.querySelector('input[name="firstname"]');
        const lastnameField = document.querySelector('input[name="lastname"]');
        
        if (firstnameField && !firstnameField.value) {
            firstnameField.value = '<?php echo e($loggedInUser->user_firstname ?? ""); ?>';
            console.log('✅ Pre-filled firstname for logged-in user:', firstnameField.value);
        }
        if (lastnameField && !lastnameField.value) {
            lastnameField.value = '<?php echo e($loggedInUser->user_lastname ?? ""); ?>';
            console.log('✅ Pre-filled lastname for logged-in user:', lastnameField.value);
        }
        
        // Auto-fill program selection if we have the program from stepper data
        const programSelect = document.getElementById('programSelect');
        if (programSelect && selectedProgramId) {
            programSelect.value = selectedProgramId;
            onProgramSelectionChange(); // Trigger any dependent logic
        }
        <?php else: ?>
        // For non-logged-in users, the form fields will be populated by copyStepperDataToFinalForm
        // which is called after this function with a delay
        console.log('User is not logged in, form will be populated by copyStepperDataToFinalForm');
        <?php endif; ?>
        
        // Always populate the program dropdown with selected program
        setTimeout(() => {
            populateProgramDropdown();
            // Also ensure education level is properly synchronized
            syncEducationLevelField();
        }, 100);
    }
    
    // Function to synchronize education level field between dropdown and hidden field
    function syncEducationLevelField() {
        const educationLevelDropdown = document.getElementById('educationLevel');
        const hiddenEducationField = document.querySelector('input[name="education_level"]');
        
        if (!educationLevelDropdown || !hiddenEducationField) return;
        
        // If hidden field has value but dropdown doesn't, try to set dropdown
        if (hiddenEducationField.value && !educationLevelDropdown.value) {
            educationLevelDropdown.value = hiddenEducationField.value;
            console.log('✅ Synced education level dropdown from hidden field:', hiddenEducationField.value);
        }
        // If dropdown has value but hidden field doesn't, update hidden field
        else if (educationLevelDropdown.value && !hiddenEducationField.value) {
            hiddenEducationField.value = educationLevelDropdown.value;
            console.log('✅ Synced hidden education level from dropdown:', educationLevelDropdown.value);
        }
    }
    
    // Force populate visible fields - additional safety net
    function forcePopulateVisibleFields() {
        if (isUserLoggedIn) return; // Only for non-logged-in users
        
        console.log('🔧 Force populating visible fields...');
        
        // Get stored form data from multiple sources
        let formData = null;
        
        // Try sessionStorage first
        const savedData = sessionStorage.getItem('enrollmentFormData');
        if (savedData) {
            try {
                formData = JSON.parse(savedData);
            } catch (e) {
                console.error('Error parsing saved data:', e);
            }
        }
        
        // If no saved data, try to get from account form fields directly
        if (!formData) {
            const userFirstname = document.getElementById('user_firstname')?.value || '';
            const userLastname = document.getElementById('user_lastname')?.value || '';
            const userEmail = document.getElementById('user_email')?.value || '';
            
            if (userFirstname || userLastname || userEmail) {
                formData = {
                    user_firstname: userFirstname,
                    user_lastname: userLastname,
                    user_email: userEmail
                };
                console.log('🔧 Created form data from account fields:', formData);
            }
        }
        
        if (!formData || (!formData.user_firstname && !formData.user_lastname)) {
            console.log('⚠️ No form data found or empty names');
            return;
        }
        
        const form = document.getElementById('modularEnrollmentForm');
        if (!form) {
            console.log('⚠️ Form not found');
            return;
        }
        
        console.log('🔧 Using form data:', formData);
        
        // DEBUGGING: List all form fields to help identify the actual field names
        const allFormInputs = form.querySelectorAll('input, select, textarea');
        console.log('🔍 ALL FORM FIELDS DEBUG:');
        allFormInputs.forEach((input, index) => {
            console.log(`  ${index}: tag=${input.tagName}, type=${input.type}, name="${input.name}", id="${input.id}", placeholder="${input.placeholder}", value="${input.value}"`);
        });
        
        // Find and populate all possible name fields with comprehensive patterns
        const allInputs = form.querySelectorAll('input[type="text"], input[type="email"], input');
        console.log('🔧 Found total inputs:', allInputs.length);
        
        let populatedCount = 0;
        
        allInputs.forEach((input, index) => {
            const name = (input.name || '').toLowerCase();
            const id = (input.id || '').toLowerCase();
            const placeholder = (input.placeholder || '').toLowerCase();
            
            console.log(`🔍 Checking input ${index}: name="${input.name}", id="${input.id}", placeholder="${input.placeholder}", value="${input.value}"`);
            
            // Enhanced firstname patterns
            if (name.includes('firstname') || name.includes('first_name') || name === 'firstname' ||
                id.includes('firstname') || id.includes('first_name') || id === 'firstname' ||
                placeholder.includes('first') || placeholder.includes('firstname')) {
                if (!input.value && formData.user_firstname) {
                    input.value = formData.user_firstname;
                    input.dataset.populated = 'force';
                    populatedCount++;
                    console.log('✅ Force populated firstname field:', input.name || input.id, '=', formData.user_firstname);
                    // Trigger change event to ensure form validation
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
            
            // Enhanced lastname patterns
            if (name.includes('lastname') || name.includes('last_name') || name === 'lastname' ||
                id.includes('lastname') || id.includes('last_name') || id === 'lastname' ||
                placeholder.includes('last') || placeholder.includes('lastname')) {
                if (!input.value && formData.user_lastname) {
                    input.value = formData.user_lastname;
                    input.dataset.populated = 'force';
                    populatedCount++;
                    console.log('✅ Force populated lastname field:', input.name || input.id, '=', formData.user_lastname);
                    // Trigger change event to ensure form validation
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
            
            // Enhanced email patterns
            if (name.includes('email') || id.includes('email') || input.type === 'email' ||
                placeholder.includes('email')) {
                if (!input.value && formData.user_email) {
                    input.value = formData.user_email;
                    input.dataset.populated = 'force';
                    populatedCount++;
                    console.log('✅ Force populated email field:', input.name || input.id, '=', formData.user_email);
                    // Trigger change event to ensure form validation
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        });
        
        console.log(`🔧 Force population complete. Fields populated: ${populatedCount}`);
        
        // If we still haven't populated any fields, try a more aggressive approach
        if (populatedCount === 0) {
            console.log('🔧 No fields populated, trying field creation...');
            
            // Create firstname field if it doesn't exist
            if (formData.user_firstname) {
                let firstnameField = form.querySelector('input[name="firstname"], input[id="firstname"]');
                if (!firstnameField) {
                    // Create the firstname field
                    const firstnameGroup = document.createElement('div');
                    firstnameGroup.className = 'form-group';
                    firstnameGroup.style.marginBottom = '1rem';
                    
                    const firstnameLabel = document.createElement('label');
                    firstnameLabel.htmlFor = 'firstname';
                    firstnameLabel.innerHTML = '<i class="bi bi-person me-2"></i>First Name <span class="required" style="color: red;">*</span>';
                    firstnameLabel.style.fontWeight = '700';
                    
                    const firstnameInput = document.createElement('input');
                    firstnameInput.type = 'text';
                    firstnameInput.name = 'firstname';
                    firstnameInput.id = 'firstname';
                    firstnameInput.className = 'form-control';
                    firstnameInput.value = formData.user_firstname;
                    firstnameInput.required = true;
                    firstnameInput.dataset.populated = 'force-created';
                    
                    firstnameGroup.appendChild(firstnameLabel);
                    firstnameGroup.appendChild(firstnameInput);
                    
                    // Insert at the top of the form (after hidden fields)
                    const formBody = form.querySelector('.card-body') || form;
                    const existingGroups = formBody.querySelectorAll('.form-group');
                    if (existingGroups.length > 0) {
                        formBody.insertBefore(firstnameGroup, existingGroups[0]);
                    } else {
                        formBody.appendChild(firstnameGroup);
                    }
                    
                    populatedCount++;
                    console.log('🔧 Created and populated firstname field:', formData.user_firstname);
                    
                    // Trigger events
                    firstnameInput.dispatchEvent(new Event('change', { bubbles: true }));
                    firstnameInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
            
            // Create lastname field if it doesn't exist
            if (formData.user_lastname) {
                let lastnameField = form.querySelector('input[name="lastname"], input[id="lastname"]');
                if (!lastnameField) {
                    // Create the lastname field
                    const lastnameGroup = document.createElement('div');
                    lastnameGroup.className = 'form-group';
                    lastnameGroup.style.marginBottom = '1rem';
                    
                    const lastnameLabel = document.createElement('label');
                    lastnameLabel.htmlFor = 'lastname';
                    lastnameLabel.innerHTML = '<i class="bi bi-person me-2"></i>Last Name <span class="required" style="color: red;">*</span>';
                    lastnameLabel.style.fontWeight = '700';
                    
                    const lastnameInput = document.createElement('input');
                    lastnameInput.type = 'text';
                    lastnameInput.name = 'lastname';
                    lastnameInput.id = 'lastname';
                    lastnameInput.className = 'form-control';
                    lastnameInput.value = formData.user_lastname;
                    lastnameInput.required = true;
                    lastnameInput.dataset.populated = 'force-created';
                    
                    lastnameGroup.appendChild(lastnameLabel);
                    lastnameGroup.appendChild(lastnameInput);
                    
                    // Insert after firstname field or at the top
                    const formBody = form.querySelector('.card-body') || form;
                    const firstnameGroup = form.querySelector('div:has(input[name="firstname"], input[id="firstname"])');
                    
                    if (firstnameGroup) {
                        firstnameGroup.insertAdjacentElement('afterend', lastnameGroup);
                    } else {
                        const existingGroups = formBody.querySelectorAll('.form-group');
                        if (existingGroups.length > 0) {
                            formBody.insertBefore(lastnameGroup, existingGroups[0]);
                        } else {
                            formBody.appendChild(lastnameGroup);
                        }
                    }
                    
                    populatedCount++;
                    console.log('🔧 Created and populated lastname field:', formData.user_lastname);
                    
                    // Trigger events
                    lastnameInput.dispatchEvent(new Event('change', { bubbles: true }));
                    lastnameInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
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
        const courseApiUrl = `/get-module-courses?module_id=${moduleId}` + (EFFECTIVE_USER_ID ? `&user_id=${EFFECTIVE_USER_ID}` : '');
        console.log('Loading courses from:', courseApiUrl);
        
        fetch(courseApiUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-User-ID': EFFECTIVE_USER_ID || '',
                'Content-Type': 'application/json'
            },
            credentials: 'include'
        })
            .then(response => response.json())
            .then(data => {
                console.log('Courses response for module', moduleId, ':', data);
                if (data.success && data.courses) {
                    console.log('Processing', data.courses.length, 'courses...');
                    data.courses.forEach(course => {
                        console.log(`Course: ${course.course_name} (ID: ${course.course_id}) - Already Enrolled: ${course.already_enrolled ? 'YES' : 'NO'}`);
                    });
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
            const alreadyEnrolled = course.already_enrolled || false;
            
            // Add styling and disable checkbox for already enrolled courses
            const cardClass = alreadyEnrolled ? 'course-selection-item card mb-3 already-enrolled' : 'course-selection-item card mb-3';
            const checkboxDisabled = alreadyEnrolled ? 'disabled' : '';
            const enrolledBadge = alreadyEnrolled ? '<span class="badge bg-warning text-dark ms-2">Already Enrolled</span>' : '';
            
            coursesHtml += `
                <div class="${cardClass}" data-course-id="${courseId}">
                    <div class="card-body ${alreadyEnrolled ? 'opacity-75' : ''}">
                        <div class="d-flex align-items-start">
                            <div class="form-check me-3">
                                <input class="form-check-input course-checkbox" 
                                       type="checkbox" 
                                       id="course_${courseId}" 
                                       value="${courseId}"
                                       data-course-name="${courseName}"
                                       data-course-price="${coursePrice}"
                                       ${isChecked ? 'checked' : ''}
                                       ${checkboxDisabled}
                                       onchange="handleCourseSelection(this)"
                                       ${alreadyEnrolled ? 'title="You are already enrolled in this course"' : ''}>
                                <label class="form-check-label" for="course_${courseId}"></label>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 course-title">
                                    ${courseName}
                                    ${enrolledBadge}
                                </h6>
                                <p class="mb-1 text-muted course-description">${courseDesc}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Duration: ${course.duration || 'Flexible'}</small>
                                    ${coursePrice > 0 ? `<span class="badge bg-primary">₱${parseFloat(coursePrice).toFixed(2)}</span>` : ''}
                                </div>
                                ${alreadyEnrolled ? '<small class="text-warning"><i class="bi bi-info-circle"></i> You cannot enroll in this course again</small>' : ''}
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
        
        // Check if this course is already enrolled (disabled checkbox)
        if (checkbox.disabled) {
            checkbox.checked = false;
            alert('You are already enrolled in this course and cannot enroll again.');
            return;
        }
        
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
        // Remove duplicates from selectedModules first
        const uniqueModules = [];
        const seenModuleIds = new Set();
        
        selectedModules.forEach(module => {
            if (!seenModuleIds.has(module.id)) {
                seenModuleIds.add(module.id);
                const courseSelection = selectedCourses[module.id] || [];
                uniqueModules.push({
                    ...module,
                    selected_courses: courseSelection
                });
            }
        });
        
        // Update the global selectedModules array to remove duplicates
        selectedModules.length = 0;
        selectedModules.push(...uniqueModules);
        
        // Update the hidden input field
        document.getElementById('selected_modules').value = JSON.stringify(uniqueModules);
        console.log('Updated modules with course selections (duplicates removed):', uniqueModules);
        
        return uniqueModules;
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
        // Get values from the actual form fields, not just the email field
        userFirstname = document.getElementById('user_firstname')?.value || '';
        userLastname = document.getElementById('user_lastname')?.value || '';
        userEmail = document.getElementById('user_email')?.value || '';
        password = document.getElementById('password')?.value || '';
        passwordConfirmation = document.getElementById('password_confirmation')?.value || '';
        referralCode = document.getElementById('referral_code')?.value || '';
        
        // Store the form data in sessionStorage for later use
        const formDataToStore = {
            user_firstname: userFirstname,
            user_lastname: userLastname,
            user_email: userEmail,
            referral_code: referralCode
        };
        sessionStorage.setItem('enrollmentFormData', JSON.stringify(formDataToStore));
        console.log('💾 Stored form data in sessionStorage:', formDataToStore);
        
        // Debug logging (only show when fields actually have problematic values)
        const hasProblematicValues = userFirstname === userEmail && userEmail !== '' && userFirstname !== '';
        if (hasProblematicValues) {
            console.error('❌ CRITICAL ERROR: Form fields have duplicate values! This indicates a form field mapping issue.');
            console.error('Field values:', {
                userFirstname,
                userLastname,
                userEmail,
                password: password ? '[HIDDEN]' : '[EMPTY]',
                passwordConfirmation: passwordConfirmation ? '[HIDDEN]' : '[EMPTY]'
            });
            
            // If firstname and lastname are same as email, prompt user to enter correct values
            if (userFirstname === userEmail) {
                alert('Error: First name field appears to have email value. Please refresh the page and enter your actual first name.');
                return; // Stop form submission
            }
        }
        
        // Additional validation: ensure required fields are not empty
        if (!userFirstname.trim()) {
            alert('Error: First name is required.');
            document.getElementById('user_firstname')?.focus();
            return;
        }
        
        if (!userLastname.trim()) {
            alert('Error: Last name is required.');
            document.getElementById('user_lastname')?.focus();
            return;
        }
        
        if (!userEmail.trim()) {
            alert('Error: Email is required.');
            document.getElementById('user_email')?.focus();
            return;
        }
        
        if (!password.trim()) {
            alert('Error: Password is required.');
            document.getElementById('password')?.focus();
            return;
        }
        
        if (password !== passwordConfirmation) {
            alert('Error: Passwords do not match.');
            document.getElementById('password_confirmation')?.focus();
            return;
        }
        
        // Debug: Log the actual values being collected
        console.log('🔍 COLLECTING ACCOUNT DATA:', {
            userFirstname: userFirstname,
            userLastname: userLastname, 
            userEmail: userEmail,
            hasPassword: !!password,
            hasPasswordConfirmation: !!passwordConfirmation,
            referralCode: referralCode
        });
    } else {
        // For logged-in users, we don't need to collect account data
        console.log('User is logged in, skipping account data collection');
    }

    // Package, program, modules, learning mode (steps 1-4)
    const packageId = document.getElementById('package_id')?.value || '';
    const programId = document.getElementById('program_id')?.value || selectedProgramId || '';
    const selectedModules = document.getElementById('selected_modules')?.value || '';
    const learningMode = document.getElementById('learning_mode')?.value || '';
    
    // Get education level from the dropdown in the final form
    const educationLevelDropdown = document.getElementById('educationLevel');
    let educationLevel = educationLevelDropdown?.value || '';
    
    // If education level is empty, try to get it from hidden field
    if (!educationLevel) {
        const hiddenEducationLevel = document.querySelector('input[name="education_level"]');
        educationLevel = hiddenEducationLevel?.value || '';
    }

    console.log('🔍 FORM DATA DEBUG:', {
        packageId, 
        programId, 
        selectedModules, 
        learningMode,
        educationLevel,
        selectedProgramId,
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
        
        // Also populate the dynamic form fields if they exist - enhanced field finder
        const firstNameSelectors = [
            'input[name="firstname"]',
            'input[name="first_name"]',
            'input[name="First_Name"]',
            'input[name="FirstName"]',
            'input[id="firstname"]',
            'input[id="first_name"]'
        ];
        
        let firstnameField = null;
        for (const selector of firstNameSelectors) {
            firstnameField = form.querySelector(selector);
            if (firstnameField) break;
        }
        
        if (firstnameField) {
            firstnameField.value = userFirstname;
            console.log('✅ Populated firstname field with:', userFirstname);
            console.log('✅ Firstname field name:', firstnameField.name, 'id:', firstnameField.id);
            console.log('✅ Firstname field visibility:', window.getComputedStyle(firstnameField).display, 'opacity:', window.getComputedStyle(firstnameField).opacity);
            // Mark field as populated to avoid confusion
            firstnameField.dataset.populated = 'true';
            // Trigger events to ensure form validation
            firstnameField.dispatchEvent(new Event('change', { bubbles: true }));
            firstnameField.dispatchEvent(new Event('input', { bubbles: true }));
        } else {
            console.log('⚠️ No firstname field found in form using any selector');
            // Force create a firstname field if it doesn't exist
            const firstnameInput = document.createElement('input');
            firstnameInput.type = 'text';
            firstnameInput.name = 'firstname';
            firstnameInput.id = 'firstname';
            firstnameInput.className = 'form-control';
            firstnameInput.value = userFirstname;
            firstnameInput.required = true;
            firstnameInput.dataset.populated = 'force-created';
            
            // Create a label and wrapper
            const firstnameGroup = document.createElement('div');
            firstnameGroup.className = 'form-group';
            const firstnameLabel = document.createElement('label');
            firstnameLabel.htmlFor = 'firstname';
            firstnameLabel.innerHTML = '<i class="bi bi-person me-2"></i>First Name <span class="required" style="color: red;">*</span>';
            firstnameLabel.style.fontWeight = '700';
            
            firstnameGroup.appendChild(firstnameLabel);
            firstnameGroup.appendChild(firstnameInput);
            
            // Insert at the beginning of the form
            const formContent = form.querySelector('.card-body');
            if (formContent && formContent.firstChild) {
                formContent.insertBefore(firstnameGroup, formContent.firstChild);
                console.log('✅ Created and populated firstname field with:', userFirstname);
            }
        }

        const lastNameSelectors = [
            'input[name="lastname"]',
            'input[name="last_name"]',
            'input[name="Last_Name"]', 
            'input[name="LastName"]',
            'input[id="lastname"]',
            'input[id="last_name"]'
        ];
        
        let lastnameField = null;
        for (const selector of lastNameSelectors) {
            lastnameField = form.querySelector(selector);
            if (lastnameField) break;
        }
        
        if (lastnameField) {
            lastnameField.value = userLastname;
            console.log('✅ Populated lastname field with:', userLastname);
            console.log('✅ Lastname field name:', lastnameField.name, 'id:', lastnameField.id);
            console.log('✅ Lastname field visibility:', window.getComputedStyle(lastnameField).display, 'opacity:', window.getComputedStyle(lastnameField).opacity);
            // Mark field as populated to avoid confusion
            lastnameField.dataset.populated = 'true';
            // Trigger events to ensure form validation
            lastnameField.dispatchEvent(new Event('change', { bubbles: true }));
            lastnameField.dispatchEvent(new Event('input', { bubbles: true }));
        } else {
            console.log('⚠️ No lastname field found in form using any selector');
            // Force create a lastname field if it doesn't exist
            const lastnameInput = document.createElement('input');
            lastnameInput.type = 'text';
            lastnameInput.name = 'lastname';
            lastnameInput.id = 'lastname';
            lastnameInput.className = 'form-control';
            lastnameInput.value = userLastname;
            lastnameInput.required = true;
            lastnameInput.dataset.populated = 'force-created';
            
            // Create a label and wrapper
            const lastnameGroup = document.createElement('div');
            lastnameGroup.className = 'form-group';
            const lastnameLabel = document.createElement('label');
            lastnameLabel.htmlFor = 'lastname';
            lastnameLabel.innerHTML = '<i class="bi bi-person me-2"></i>Last Name <span class="required" style="color: red;">*</span>';
            lastnameLabel.style.fontWeight = '700';
            
            lastnameGroup.appendChild(lastnameLabel);
            lastnameGroup.appendChild(lastnameInput);
            
            // Insert after firstname field or at the beginning
            const firstnameGroup = form.querySelector('div:has(input[name="firstname"], input[id="firstname"])') || 
                                 form.querySelector('.form-group');
            const formContent = form.querySelector('.card-body');
            
            if (firstnameGroup && firstnameGroup.nextSibling) {
                formContent.insertBefore(lastnameGroup, firstnameGroup.nextSibling);
            } else if (formContent && formContent.firstChild) {
                formContent.insertBefore(lastnameGroup, formContent.firstChild);
            }
            console.log('✅ Created and populated lastname field with:', userLastname);
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

        // Also populate user_firstname and user_lastname fields if they exist in the final form
        const userFirstnameField = form.querySelector('input[name="user_firstname"]');
        if (userFirstnameField) {
            userFirstnameField.value = userFirstname;
            console.log('✅ Populated user_firstname field with:', userFirstname);
        }

        const userLastnameField = form.querySelector('input[name="user_lastname"]');
        if (userLastnameField) {
            userLastnameField.value = userLastname;
            console.log('✅ Populated user_lastname field with:', userLastname);
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
    
    // Sync the visible dropdowns with the hidden fields
    setTimeout(() => {
        populateProgramDropdown();
        syncEducationLevelField();
    }, 50);
    
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

// Hook into form submission (removed duplicate nextStep function)

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
            
            // CRITICAL FIX: Ensure all uploaded file paths are included in form submission
            const fileInputs = form.querySelectorAll('input[type="file"]');
            fileInputs.forEach(fileInput => {
                const fieldName = fileInput.name;
                const uploadedFilePath = fileInput.dataset.uploadedFilePath;
                
                if (uploadedFilePath) {
                    // Add file paths for both original case and lowercase to ensure backend compatibility
                    const fieldNameVariations = [fieldName, fieldName.toLowerCase()];
                    
                    fieldNameVariations.forEach(variation => {
                        formData.append(variation + '_path', uploadedFilePath);
                        console.log(`✅ Added file path for ${variation}: ${uploadedFilePath}`);
                    });
                }
            });
            
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
                    } else if (data.message) {
                        errorMessage += ': ' + data.message;
                    }
                    
                    alert(errorMessage);
                    console.error('Registration failed:', data);
                    // Re-enable submit button on error
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Complete Registration';
                    }
                }
            })
            .catch(error => {
                console.error('Form submission error:', error);
                alert('Form submission failed. Please check your connection and try again.');
                // Re-enable submit button on error
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Complete Registration';
                }
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

// Function to populate the program dropdown in the final form
function populateProgramDropdown() {
    const programSelect = document.getElementById('programSelect');
    if (!programSelect || !selectedProgramId) return;
    
    console.log('Populating program dropdown with selected program:', selectedProgramId);
    
    // Check if the option already exists
    let optionExists = false;
    for (let option of programSelect.options) {
        if (option.value === selectedProgramId.toString()) {
            option.selected = true;
            optionExists = true;
            break;
        }
    }
    
    // If option doesn't exist, create it
    if (!optionExists && window.controllerData && window.controllerData.allPrograms) {
        const program = window.controllerData.allPrograms.find(p => p.id == selectedProgramId);
        if (program) {
            const option = document.createElement('option');
            option.value = program.id;
            option.textContent = program.program_name || program.name || `Program ${program.id}`;
            option.selected = true;
            programSelect.appendChild(option);
            console.log('✅ Added and selected program option:', option.textContent);
        }
    }
    
    // Trigger the change event to update hidden fields
    onProgramSelectionChange();
}

// Function to handle education level selection change
function onEducationLevelChange() {
    const educationLevel = document.getElementById('educationLevel');
    if (!educationLevel) return;
    
    const selectedValue = educationLevel.value;
    console.log('Education level changed to:', selectedValue);
    
    // Update the hidden education_level field
    const hiddenEducationField = document.querySelector('input[name="education_level"]');
    if (hiddenEducationField) {
        hiddenEducationField.value = selectedValue;
    }
    
    // Trigger the file requirements toggle
    toggleEducationLevelRequirements();
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
    const selectedValue = educationLevel.value;
    
    console.log('Education level changed to:', selectedValue);
    
    // Update the hidden education_level field
    const hiddenEducationField = document.querySelector('input[name="education_level"]');
    if (hiddenEducationField) {
        hiddenEducationField.value = selectedValue;
        console.log('✅ Updated hidden education_level field to:', selectedValue);
    }
    
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
    console.log('Form data being sent for validation:', {
        firstName: firstName,
        lastName: lastName,
        fieldName: fieldName,
        fileName: file.name,
        fileSize: file.size
    });
    
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
    
            fetch(VALIDATE_URL, {
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
                // Store in dataset for form submission
                inputElement.dataset.uploadedFilePath = data.file_path;
                
                // Create hidden fields for both original case and lowercase to ensure compatibility
                const fieldNameVariations = [fieldName, fieldName.toLowerCase()];
                
                fieldNameVariations.forEach(variation => {
                    let hiddenFileInput = document.querySelector(`input[name="${variation}_path"]`);
                    if (!hiddenFileInput) {
                        hiddenFileInput = document.createElement('input');
                        hiddenFileInput.type = 'hidden';
                        hiddenFileInput.name = variation + '_path';
                        inputElement.parentNode.appendChild(hiddenFileInput);
                    }
                    hiddenFileInput.value = data.file_path;
                    console.log('✅ Stored file path for', variation, ':', data.file_path);
                });
            }
            
            // File uploaded successfully - no need for education level detection or program suggestions
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

// Program suggestions removed - users can manually select their program

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
// Education level detection removed - users can manually select their education level

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

try {
    window.studentEnrollments = <?php echo json_encode($studentEnrollments ?? [], 15, 512) ?>;
} catch (e) {
    console.error('Error parsing studentEnrollments JSON:', e);
    window.studentEnrollments = [];
}
try {
    window.contentStructure = <?php echo json_encode($contentStructure ?? [], 15, 512) ?>;
} catch (e) {
    console.error('Error parsing contentStructure JSON:', e);
    window.contentStructure = [];
}
// Ensure contentStructure is always an array
if (window.contentStructure && !Array.isArray(window.contentStructure)) {
    // Convert object to array if possible
    window.contentStructure = Object.values(window.contentStructure);
}
console.log('contentStructure:', window.contentStructure);
// Filtering logic and UI update functions will be added here

// --- Filtering Logic ---
// Fetch available programs for modular enrollment from backend API
async function fetchAvailableProgramsForStudent() {
    try {
        const apiUrl = '/api/enrollment/available-programs' + (EFFECTIVE_USER_ID ? `?user_id=${EFFECTIVE_USER_ID}` : '');
        const response = await fetch(apiUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-User-ID': EFFECTIVE_USER_ID || '',
                'Content-Type': 'application/json'
            },
            credentials: 'include' // Include session cookies
        });
        if (!response.ok) throw new Error('Failed to fetch available programs');
        const data = await response.json();
        return data.programs || [];
    } catch (error) {
        console.error('Error fetching available programs:', error);
        return [];
    }
}

// Store available programs in a global variable
window.availableProgramsForModular = [];

// Update the program select dropdown
function updateProgramSelect(availablePrograms, selectedProgramId = null) {
    const select = document.getElementById('programSelect');
    if (!select) return;
    select.innerHTML = '<option value="">Select Program</option>';
    availablePrograms.forEach(program => {
        const option = document.createElement('option');
        option.value = program.program_id;
        option.textContent = program.program_name;
        if (selectedProgramId && program.program_id == selectedProgramId) option.selected = true;
        select.appendChild(option);
    });
}

// Update modules and courses UI for a selected program
function updateModulesAndCoursesUI(programId) {
    console.log('updateModulesAndCoursesUI called with programId:', programId);
    
    // Find the program in available programs
    const program = window.availableProgramsForModular.find(p => p.program_id == programId);
    console.log('Found program:', program);
    
    const modulesGrid = document.getElementById('modulesGrid');
    if (!modulesGrid) {
        console.error('modulesGrid element not found');
        return;
    }
    
    if (!program) {
        console.error('Program not found for ID:', programId);
        modulesGrid.innerHTML = '<div class="alert alert-warning">Program not found.</div>';
        return;
    }
    
    // Check if program has modules property
    if (!program.modules || !Array.isArray(program.modules)) {
        console.log('Program has no modules array, fetching modules...');
        // Fetch modules for this program
        fetch(`/api/programs/${programId}/modules`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.modules) {
                    displayModules(data.modules);
                } else {
                    modulesGrid.innerHTML = '<div class="alert alert-info">No modules available for this program.</div>';
                }
            })
            .catch(error => {
                console.error('Error fetching modules:', error);
                modulesGrid.innerHTML = '<div class="alert alert-danger">Error loading modules.</div>';
            });
        return;
    }
    
    // Display modules if they exist
    if (program.modules.length > 0) {
        displayModules(program.modules);
    } else {
        modulesGrid.innerHTML = '<div class="alert alert-info">No modules available for this program.</div>';
    }
}

    // When a package is selected, update the program select and modules/courses UI
    async function onPackageSelected(packageId, programId) {
        console.log('onPackageSelected called with:', { packageId, programId });
        
        if (!window.availableProgramsForModular.length) {
            window.availableProgramsForModular = await fetchAvailableProgramsForStudent();
        }
        
        updateProgramSelect(window.availableProgramsForModular, programId);
        
        if (programId) {
            const programSelect = document.getElementById('programSelect');
            if (programSelect) {
                programSelect.dispatchEvent(new Event('change'));
            }
            
            // Only call updateModulesAndCoursesUI if we're on the modules step
            if (currentStep === 4) {
                updateModulesAndCoursesUI(programId);
            }
        }
    }

// Patch the selectPackage function to call onPackageSelected
const origSelectPackage = window.selectPackage;
window.selectPackage = async function(packageId, programId, moduleCount, selectionMode = 'modules', courseCount = 0) {
    origSelectPackage(packageId, programId, moduleCount, selectionMode, courseCount);
    await onPackageSelected(packageId, programId);
};

// On program select change, update modules/courses UI
const programSelect = document.getElementById('programSelect');
if (programSelect) {
    programSelect.addEventListener('change', function() {
        updateModulesAndCoursesUI(this.value);
    });
}

// On page load, fetch and update program select
window.addEventListener('DOMContentLoaded', async function() {
    window.availableProgramsForModular = await fetchAvailableProgramsForStudent();
    updateProgramSelect(window.availableProgramsForModular);
    // Optionally, update modules/courses UI for the first program
    if (window.availableProgramsForModular.length > 0) {
        updateModulesAndCoursesUI(window.availableProgramsForModular[0].program_id);
    }
});

// REMOVED: Duplicate displayPrograms function that was causing conflicts

// Fallback function to display programs as simple grid
// REMOVED: displayProgramsAsGrid function - no longer needed

// Function to select a program
function selectProgram(programId) {
    console.log('Selected program:', programId);
    
    // Store selected program
    selectedProgramId = programId;
    
    // Update hidden field
    const hiddenField = document.getElementById('program_id');
    if (hiddenField) {
        hiddenField.value = programId;
    }
    
    // Update program select dropdown if exists
    const programSelect = document.getElementById('programSelect');
    if (programSelect) {
        programSelect.value = programId;
    }
    
    // Move to next step
    if (typeof nextStep === 'function') {
        nextStep();
    }
}

// Global function assignments
window.selectProgram = selectProgram;
// REMOVED: Event listener that was calling the duplicate displayPrograms function

    // Function to handle step transitions with animation
    function animateStepTransition(fromStep, toStep, isBack = false) {
        const from = document.getElementById(fromStep);
        const to = document.getElementById(toStep);
        
        console.log('Animating transition:', fromStep, '->', toStep);
        console.log('From element found:', !!from);
        console.log('To element found:', !!to);
        
        if (!from || !to) {
            console.error('Step elements not found:', fromStep, toStep);
            // Fallback: just hide/show without animation
            const allSteps = document.querySelectorAll('.step-content');
            allSteps.forEach(step => {
                step.style.display = 'none';
                step.classList.remove('active');
            });
            if (to) {
                to.style.display = 'block';
                to.classList.add('active');
                // Don't override CSS transition styles
            }
            return;
        }
        
        console.log('Starting transition animation...');
        
        // Hide all other steps
        const allSteps = document.querySelectorAll('.step-content');
        allSteps.forEach(step => {
            if (step !== to) {
                step.classList.remove('active');
                // Let CSS handle the display/opacity transition
            }
        });
        
        // Show the target step - let CSS handle the transition
        to.classList.add('active');
        
        console.log('Step transition completed - target step should now be animating in');
        
        // Check if the transition worked after the CSS animation completes
        setTimeout(() => {
            const toStepVisible = window.getComputedStyle(to).display !== 'none' && to.classList.contains('active');
            const toStepOpacity = window.getComputedStyle(to).opacity;
            console.log('Post-transition check - target step visible:', toStepVisible);
            console.log('Target step computed opacity:', toStepOpacity);
            console.log('Target step classes:', to.className);
        }, 450); // Wait for CSS transition to complete (400ms + buffer)
    }

    // Helper function to update hidden start date field
    function updateHiddenStartDate() {
        // No longer needed since we removed the duplicate hidden field
        // The visible date input field will be used directly
        console.log('Hidden start date field removed - using visible date input');
    }

    // Helper function to update hidden program_id field  
    function updateHiddenProgramId() {
        const programSelect = document.getElementById('programSelect');
        const hiddenProgramInput = document.getElementById('hidden_program_id');
        
        if (programSelect && hiddenProgramInput) {
            hiddenProgramInput.value = programSelect.value;
            console.log('Updated hidden program_id:', programSelect.value);
        }
    }
</script>

<style>
.form-control.is-warning {
    border-color: #ffc107;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%23ffc107' d='M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}
</style>

<script>
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
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\registration\Modular_enrollment.blade.php ENDPATH**/ ?>