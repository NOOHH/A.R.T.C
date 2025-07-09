@extends('layouts.navbar')

@section('title', 'Student Registration')
@section('hide_footer', true)
@section('body_class', 'registration-page')

@push('styles')
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Full_Enrollment.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<!-- Critical JavaScript functions for immediate availability -->
<script>
    
// Global variables (declare first for immediate availability)
let currentStep = 1;
let selectedPackageId = null;
let selectedPaymentMethod = null;
let currentPackageIndex = 0;
let packagesPerView = 2;
let totalPackages = <?php echo isset($packages) && is_countable($packages) ? (int)count($packages) : 0; ?>;

// Check if user is logged in (set from server)
const isUserLoggedIn = @if(session('user_id')) true @else false @endif;
const loggedInUserName = '@if(session("user_name")){{ session("user_name") }}@endif';
const loggedInUserFirstname = '@if(session("user_firstname")){{ session("user_firstname") }}@endif';
const loggedInUserLastname = '@if(session("user_lastname")){{ session("user_lastname") }}@endif';
const loggedInUserEmail = '@if(session("user_email")){{ session("user_email") }}@endif';

// Define critical functions immediately for onclick handlers
function selectPackage(packageId, packageName, packagePrice) {
    // Remove selection from all package cards
    document.querySelectorAll('.package-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Highlight selected package
    if (event && event.target) {
        event.target.closest('.package-card').classList.add('selected');
    }
    
    // Store selection in global variable
    selectedPackageId = packageId;
    window.selectedPackageId = packageId;
    
    console.log('Package selected:', packageId, packageName); // Debug log
    
    // Store package selection in session storage
    sessionStorage.setItem('selectedPackageId', packageId);
    sessionStorage.setItem('selectedPackageName', packageName);
    sessionStorage.setItem('selectedPackagePrice', packagePrice);
    
    // Update hidden form input
    const packageInput = document.querySelector('input[name="package_id"]');
    if (packageInput) {
        packageInput.value = packageId;
    }
    
    // Show selected package display
    const selectedDisplay = document.getElementById('selectedPackageName');
    const selectedPackageDisplay = document.getElementById('selectedPackageDisplay');
    if (selectedDisplay) selectedDisplay.textContent = packageName;
    if (selectedPackageDisplay) selectedPackageDisplay.style.display = 'block';
    
    // Enable next button
    const nextBtn = document.getElementById('packageNextBtn');
    if (nextBtn) {
        nextBtn.disabled = false;
        nextBtn.classList.add('enabled');
        nextBtn.classList.remove('disabled');
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
        nextBtn.style.pointerEvents = 'auto';
    }
}

function scrollPackages(direction) {
    const carousel = document.getElementById('packagesCarousel');
    if (!carousel) return;
    
    const scrollAmount = 340; // Package card width + gap
    
    if (direction === 'left') {
        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}

function selectLearningMode(mode) {
    console.log('Selecting learning mode:', mode);
    
    // Remove selection from all learning mode cards
    document.querySelectorAll('.learning-mode-card').forEach(card => {
        card.style.border = '3px solid transparent';
        card.style.boxShadow = 'none';
    });
    
    // Highlight selected learning mode using data attribute
    const selectedCard = document.querySelector(`[data-mode="${mode}"]`);
    if (selectedCard) {
        selectedCard.style.border = '3px solid #667eea';
        selectedCard.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.3)';
        console.log('Selected card highlighted:', selectedCard);
    } else {
        console.error('No card found for mode:', mode);
    }
    
    // Update hidden input
    const learningModeInput = document.getElementById('learning_mode');
    if (learningModeInput) {
        learningModeInput.value = mode;
        console.log('Learning mode input updated:', mode);
    }
    
    // Update display
    const modeNames = {
        'synchronous': 'Synchronous (Live Classes)',
        'asynchronous': 'Asynchronous (Self-Paced)'
    };
    
    const selectedDisplay = document.getElementById('selectedLearningModeName');
    const displayContainer = document.getElementById('selectedLearningModeDisplay');
    
    if (selectedDisplay) selectedDisplay.textContent = modeNames[mode] || mode;
    if (displayContainer) displayContainer.style.display = 'block';
    
    // Learning mode selection complete - batch selection will be handled in Step 4
    console.log('Learning mode selected:', mode);
    
    // Enable next button with proper styling
    const nextBtn = document.getElementById('learningModeNextBtn');
    if (nextBtn) {
        nextBtn.disabled = false;
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
        nextBtn.style.background = 'linear-gradient(90deg,#a259c6,#6a82fb)';
        nextBtn.classList.add('enabled');
        console.log('Next button enabled and styled');
    } else {
        console.error('Next button not found');
    }
}


function nextStep() {
    console.log('nextStep called, current step:', currentStep);

    if (currentStep === 1) {
        const packageInput = document.querySelector('input[name="package_id"]');
        const sessionPackageId = sessionStorage.getItem('selectedPackageId');
        if (!selectedPackageId && !window.selectedPackageId && !packageInput?.value && !sessionPackageId) {
            showWarning('Please select a package before proceeding.');
            return;
        }
        if (!selectedPackageId && (window.selectedPackageId || packageInput?.value || sessionPackageId)) {
            selectedPackageId = window.selectedPackageId || packageInput?.value || sessionPackageId;
        }
        animateStepTransition('step-1', 'step-2');
        currentStep = 2;
        updateStepper(currentStep);
    } else if (currentStep === 2) {
        const learningModeValue = document.getElementById('learning_mode')?.value;
        if (!learningModeValue) {
            showWarning('Please select a learning mode before proceeding.');
            return;
        }
        if (isUserLoggedIn) {
            animateStepTransition('step-2', 'step-4');
            currentStep = 4;
            updateStepper(currentStep);
            setTimeout(() => {
                fillLoggedInUserData();
            }, 300);
        } else {
            animateStepTransition('step-2', 'step-3');
            currentStep = 3;
            updateStepper(currentStep);
        }
    } else if (currentStep === 3) {
        if (!validateStep3()) {
            showWarning('Please fill in all required fields correctly.');
            return;
        }
        copyAccountDataToStudentForm();
        animateStepTransition('step-3', 'step-4');
        currentStep = 4;
        updateStepper(currentStep);
        setTimeout(() => {
            copyAccountDataToStudentForm();
        }, 300);
    }
    // updateProgressBar(); <-- Only keep this in animateStepTransition
}


function prevStep() {
    console.log('prevStep called, current step:', currentStep);

    if (currentStep === 4) {
        // From student registration, check if user is logged in and learning mode
        const learningMode = document.getElementById('learning_mode')?.value;

        if (isUserLoggedIn) {
            // Skip account registration and go back to learning mode
            console.log('User logged in - going back to learning mode');
            animateStepTransition('step-4', 'step-2', true);
            currentStep = 2;
            updateStepper(currentStep);
        } else {
            // User not logged in, go back to account registration
            console.log('User not logged in - going back to account registration');
            animateStepTransition('step-4', 'step-3', true);
            currentStep = 3;
            updateStepper(currentStep);
            // Trigger validation after going back to step 3
            setTimeout(() => {
                validateStep3();
                console.log('Step 3 validation triggered after going back');
            }, 500);
        }
    } else if (currentStep === 3) {
        // From account registration, go back to learning mode
        animateStepTransition('step-3', 'step-2', true);
        currentStep = 2;
        updateStepper(currentStep);
    } else if (currentStep === 2) {
        // From learning mode back to package selection
        animateStepTransition('step-2', 'step-1', true);
        currentStep = 1;
        updateStepper(currentStep);
    }
}

function updateStepper(currentStep) {
  for (let i = 1; i <= 4; i++) {
    let step = document.getElementById('stepper-' + i);
    if (!step) continue;
    if (i <= currentStep) {
      step.classList.add('active');
    } else {
      step.classList.remove('active');
    }
  }
}

// Helper function to show warning messages
function showWarning(message) {
    const warningModal = document.getElementById('warningModal');
    const warningMessage = document.getElementById('warningMessage');
    if (warningModal && warningMessage) {
        warningMessage.textContent = message;
        warningModal.style.display = 'flex';
    } else {
        alert(message); // Fallback
    }
}

// Helper function to close warning modal
function closeWarningModal() {
    const warningModal = document.getElementById('warningModal');
    if (warningModal) {
        warningModal.style.display = 'none';
    }
}

// Function to update progress bar based on current step
function updateProgressBar() {
    const progressBar = document.querySelector('.progress-bar');
    if (!progressBar) return;
    
    let percentage = 0;
    switch(currentStep) {
        case 1: percentage = 25; break;  // Package Selection
        case 2: percentage = 50; break;  // Learning Mode
        case 3: percentage = 75; break;  // Account Registration
        case 4: percentage = 100; break; // Student Registration
        default: percentage = 25;
    }
    
    progressBar.style.width = percentage + '%';
    progressBar.setAttribute('aria-valuenow', percentage);
}

// Function to handle step transitions with animation
function animateStepTransition(fromStep, toStep, isBack = false) {
    const from = document.getElementById(fromStep);
    const to = document.getElementById(toStep);
    
    if (!from || !to) {
        console.error('Step elements not found:', fromStep, toStep);
        return;
    }
    
    // Update progress bar
    updateProgressBar();
    
    // Add transition classes
    from.style.transition = 'all 0.3s ease-in-out';
    to.style.transition = 'all 0.3s ease-in-out';
    
    // Hide current step
    from.style.opacity = '0';
    from.style.transform = isBack ? 'translateX(50px)' : 'translateX(-50px)';
    
    setTimeout(() => {
        // Hide current step completely
        from.style.display = 'none';
        from.classList.remove('active');
        
        // Show new step
        to.style.display = 'block';
        to.style.opacity = '0';
        to.style.transform = isBack ? 'translateX(-50px)' : 'translateX(50px)';
        to.classList.add('active');
        
        // Animate in new step
        setTimeout(() => {
            to.style.opacity = '1';
            to.style.transform = 'translateX(0)';
        }, 50);
        
        // Reset transforms after animation
        setTimeout(() => {
            from.style.transform = '';
            to.style.transform = '';
        }, 350);
    }, 300);
}

// Helper function to update hidden start date field
function updateHiddenStartDate() {
    const dateInput = document.getElementById('start_date_input');
    const hiddenDateInput = document.getElementById('hidden_start_date');
    
    if (dateInput && hiddenDateInput) {
        hiddenDateInput.value = dateInput.value;
        console.log('Updated hidden start date:', dateInput.value);
    }
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

// Function to load batches for the selected program in Step 4
async function loadBatchesForProgram() {
    const programSelect = document.getElementById('programSelect');
    const learningMode = document.getElementById('learning_mode')?.value;
    const batchContainer = document.getElementById('batchSelectionContainer');
    const batchOptions = document.getElementById('batchOptions');
    
    // Only show batches for synchronous mode and when a program is selected
    if (!programSelect || !programSelect.value || learningMode !== 'synchronous') {
        if (batchContainer) batchContainer.style.display = 'none';
        return;
    }
    
    const programId = programSelect.value;
    console.log('Loading batches for program:', programId, 'learning mode:', learningMode);
    
    try {
        // Show batch container and loading state
        if (batchContainer) batchContainer.style.display = 'block';
        if (batchOptions) {
            batchOptions.innerHTML = '<div class="batch-loading">Loading batches...</div>';
        }
        
        const response = await fetch(`{{ route('public.batches.by-program') }}?program_id=${programId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                               document.querySelector('input[name="_token"]')?.value
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to fetch batches');
        }
        
        const batches = await response.json();
        console.log('Fetched batches:', batches);
        
        if (batchOptions) {
            if (batches.length === 0) {
                batchOptions.innerHTML = '<div class="no-batches">No active batches available for this program.</div>';
            } else {
                batchOptions.innerHTML = batches.map(batch => {
                    const availableSlots = batch.max_capacity - batch.current_capacity;
                    const isNearFull = availableSlots <= 3 && availableSlots > 0;
                    const isFull = availableSlots <= 0;
                    const isOngoing = batch.batch_status === 'ongoing';
                    
                    let statusText = '';
                    let statusClass = '';
                    
                    if (isFull) {
                        statusText = 'Closed (Full)';
                        statusClass = 'status-closed';
                    } else if (isOngoing && availableSlots > 0) {
                        statusText = 'Ongoing - Available to Join';
                        statusClass = 'status-ongoing';
                    } else if (isNearFull) {
                        statusText = `Available (${availableSlots} slots left)`;
                        statusClass = 'status-limited';
                    } else {
                        statusText = `Available (${availableSlots} slots)`;
                        statusClass = 'status-available';
                    }
                    
                    return `
                        <div class="batch-option ${isFull ? 'disabled' : ''}" 
                             onclick="${isFull ? '' : `selectBatch(${batch.batch_id}, '${batch.batch_name}', '${batch.batch_status}')`}" 
                             data-batch-id="${batch.batch_id}"
                             data-batch-status="${batch.batch_status}">
                            <div class="batch-header">
                                <div class="batch-name">${batch.batch_name}</div>
                                <div class="batch-status ${statusClass}">${statusText}</div>
                            </div>
                            <div class="batch-details">
                                <div class="batch-info">
                                    <span class="batch-schedule">Start: ${batch.start_date}</span>
                                    <span class="batch-capacity">Students: ${batch.current_capacity}/${batch.max_capacity}</span>
                                </div>
                                ${isOngoing ? '<div class="ongoing-warning">⚠️ This batch has already started</div>' : ''}
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }
    } catch (error) {
        console.error('Error loading batches:', error);
        if (batchOptions) {
            batchOptions.innerHTML = '<div class="batch-error">Error loading batches. Please try again.</div>';
        }
    }
}

// Function to select a batch
function selectBatch(batchId, batchName, batchStatus) {
    console.log('Selecting batch:', batchId, batchName, batchStatus);
    
    // Check if batch is ongoing and show warning
    if (batchStatus === 'ongoing') {
        const confirmOngoing = confirm(
            `Warning: The batch "${batchName}" has already started.\n\n` +
            `If you join this ongoing batch, you will need to:\n` +
            `• Start from the beginning of the course\n` +
            `• Catch up with current activities and assignments\n` +
            `• Meet deadlines that may have already passed\n\n` +
            `Are you sure you want to join this ongoing batch?`
        );
        
        if (!confirmOngoing) {
            return; // User cancelled, don't select the batch
        }
    }
    
    // Remove selection from all batch options
    document.querySelectorAll('.batch-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Highlight selected batch
    const selectedOption = document.querySelector(`[data-batch-id="${batchId}"]`);
    if (selectedOption) {
        selectedOption.classList.add('selected');
    }
    
    // Store selected batch information in sessionStorage for form submission
    sessionStorage.setItem('selectedBatchId', batchId);
    sessionStorage.setItem('selectedBatchName', batchName);
    console.log('Batch selected and stored in sessionStorage:', batchId);
    
    // Update display
    const selectedDisplay = document.getElementById('selectedBatchName');
    const displayContainer = document.getElementById('selectedBatchDisplay');
    
    if (selectedDisplay) selectedDisplay.textContent = batchName;
    if (displayContainer) displayContainer.style.display = 'block';
}

// Helper function to copy account data to student form
function copyAccountDataToStudentForm() {
    if (isUserLoggedIn) {
        // If user is logged in, use session data
        const firstnameField = document.querySelector('input[name="user_firstname"]');
        const lastnameField = document.querySelector('input[name="user_lastname"]');
        
        if (firstnameField && loggedInUserFirstname) {
            firstnameField.value = loggedInUserFirstname;
        }
        if (lastnameField && loggedInUserLastname) {
            lastnameField.value = loggedInUserLastname;
        }
    } else {
        // Copy from account registration to student form
        const sourceFirstname = document.getElementById('user_firstname');
        const sourceLastname = document.getElementById('user_lastname');
        const sourceEmail = document.getElementById('user_email');
        
        const targetFirstname = document.querySelector('input[name="user_firstname"]');
        const targetLastname = document.querySelector('input[name="user_lastname"]');
        const targetEmail = document.querySelector('input[name="email"]');
        
        if (sourceFirstname && targetFirstname) {
            targetFirstname.value = sourceFirstname.value;
        }
        if (sourceLastname && targetLastname) {
            targetLastname.value = sourceLastname.value;
        }
        if (sourceEmail && targetEmail) {
            targetEmail.value = sourceEmail.value;
        }
    }
}

// Helper function to fill logged in user data
function fillLoggedInUserData() {
    if (isUserLoggedIn) {
        const firstnameField = document.querySelector('input[name="user_firstname"]');
        const lastnameField = document.querySelector('input[name="user_lastname"]');
        
        if (firstnameField && loggedInUserFirstname) {
            firstnameField.value = loggedInUserFirstname;
        }
        if (lastnameField && loggedInUserLastname) {
            lastnameField.value = loggedInUserLastname;
        }
        
        console.log('Filled logged in user data:', loggedInUserFirstname, loggedInUserLastname);
    }
}

</script>
@endpush

@section('content')
<div class="form-container">
    <div class="form-wrapper">
        <!-- Progress Bar -->
<!-- Stepper Progress Bar -->
<div class="stepper-progress">
  <div class="stepper">
    <div class="step" id="stepper-1">
      <div class="circle">1</div>
      <div class="label">Package</div>
    </div>
    <div class="bar"></div>
    <div class="step" id="stepper-2">
      <div class="circle">2</div>
      <div class="label">Mode</div>
    </div>
    <div class="bar"></div>
    <div class="step" id="stepper-3">
      <div class="circle">3</div>
      <div class="label">Account</div>
    </div>
    <div class="bar"></div>
    <div class="step" id="stepper-4">
      <div class="circle">4</div>
      <div class="label">Finish</div>
    </div>
  </div>
</div>

        <form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" class="registration-form" id="enrollmentForm" novalidate onsubmit="return handleFormSubmission(event)">
            @csrf
            
            <!-- Hidden inputs for form data -->
            <input type="hidden" name="enrollment_type" value="Full">
            <input type="hidden" name="package_id" value="" id="packageIdInput">
            <input type="hidden" name="program_id" value="" id="hidden_program_id">
            <input type="hidden" name="plan_id" value="1">
            <input type="hidden" name="learning_mode" id="learning_mode" value="">
            <input type="hidden" name="Start_Date" id="hidden_start_date" value="">
            
             <!-- Step 1 -->
            <div class="step active" id="step-1">
                <div class="step-header">
                    <h2>
                        <i class="bi bi-box-seam me-2"></i>
                        Choose Your Package
                    </h2>
                    <p>Select the package that best fits your learning goals.</p>
                </div>

                <div class="package-carousel-wrapper" style="position:relative;width:100%;display:flex;justify-content:center;align-items:center;">
                    <!-- Left Arrow -->
                    <button type="button" class="carousel-arrow left" onclick="scrollPackages('left')">&lt;</button>

                    <!-- Cards Container (Horizontal Scroll/Flex) -->
                    <div class="package-cards-container" id="packagesCarousel">
                        @foreach($packages as $package)
                        <div 
                            class="package-card"
                            onclick="selectPackage('{{ $package->package_id }}','{{ addslashes($package->package_name) }}','{{ $package->amount }}')"
                        >
                            <div class="card-body">
                                <h5 class="card-title">{{ $package->package_name }}</h5>
                                <p class="card-text">{{ $package->description ?? 'No description yet.' }}</p>
                                <div class="package-price">₱{{ number_format($package->amount, 2) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Right Arrow -->
                    <button type="button" class="carousel-arrow right" onclick="scrollPackages('right')">&gt;</button>
                </div>

                <div class="selected-package-summary mt-3 mb-4" style="text-align:left;">
                    <span class="ms-3">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Selected Package: <strong id="selectedPackageName">None</strong>
                    </span>
                </div>

                <div class="form-navigation">
                    <button
                        type="button"
                        onclick="nextStep()"
                        id="packageNextBtn"
                        disabled
                        class="btn btn-primary btn-lg"
                    >
                        Next <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

<div class="step" id="step-2">
    <div class="white-step-container">
        <div class="step-header">
            <h2><i class="bi bi-mortarboard me-2"></i>Choose Learning Mode</h2>
            <p>Select how you'd like to take your classes.</p>
        </div>

        <div class="learning-modes horizontal">
            <div class="learning-mode-card" onclick="selectLearningMode('synchronous')" data-mode="synchronous">
                <div class="mode-icon"><i class="bi bi-camera-video"></i></div>
                <h4>Synchronous</h4>
                <p>Live classes with real-time interaction</p>
                <ul>
                    <li>Live video sessions</li>
                    <li>Real-time Q&A</li>
                    <li>Interactive discussions</li>
                    <li>Scheduled class times</li>
                </ul>
            </div>
            <div class="learning-mode-card" onclick="selectLearningMode('asynchronous')" data-mode="asynchronous">
                <div class="mode-icon"><i class="bi bi-play-circle"></i></div>
                <h4>Asynchronous</h4>
                <p>Self-paced learning with recorded content</p>
                <ul>
                    <li>Pre-recorded videos</li>
                    <li>Study at your own pace</li>
                    <li>24/7 access to materials</li>
                    <li>Flexible scheduling</li>
                </ul>
            </div>
        </div>

        <div id="selectedLearningModeDisplay" class="selected-display" style="display: none;">
            <div class="selected-item">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span>Selected Mode: <strong id="selectedLearningModeName"></strong></span>
            </div>
        </div>

        <div class="form-navigation">
            <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2"></i> Back
            </button>
            <button type="button" onclick="nextStep()" id="learningModeNextBtn" disabled class="btn btn-primary btn-lg">
                Next <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </div>
    </div>
</div>

<!-- Step 3: Account Registration (only for non-logged users) -->
<div class="step" id="step-3">
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
                <input type="email" id="user_email" name="email" class="form-control" required>
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
        </div>

        <div class="login-prompt">
            <p>Already have an account? <a href="{{ route('login') }}">Click here to login</a></p>
        </div>

        <div class="form-navigation">
            <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2"></i> Back
            </button>
            <button type="button" onclick="nextStep()" id="step3NextBtn" disabled class="btn btn-primary btn-lg">
                Next <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </div>
    </div>
</div>


<!-- Step 4: Student Registration -->
<div class="step" id="step-4">
    <div class="student-step-card">
        <div class="step-header">
            <h2><i class="bi bi-person-lines-fill me-2"></i>Complete Your Registration</h2>
            <p>Fill in your personal and academic information.</p>
        </div>

        <!-- Dynamic Form Fields -->
        @if(isset($formRequirements) && $formRequirements->count() > 0)
            @foreach($formRequirements as $field)
                @if($field->field_type === 'section')
                    <h3 style="margin-top:2.1rem"><i class="bi bi-folder me-2"></i>{{ $field->label }}</h3>
                @else
                    <div class="form-group">
                        <label for="{{ $field->field_name }}">
                            {{ $field->label }}
                            @if($field->is_required) <span class="required">*</span> @endif
                        </label>
                        
                        @if($field->field_type === 'text')
                            <input type="text" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                   class="form-control" {{ $field->is_required ? 'required' : '' }}>
                        @elseif($field->field_type === 'email')
                            <input type="email" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                   class="form-control" {{ $field->is_required ? 'required' : '' }}>
                        @elseif($field->field_type === 'number')
                            <input type="number" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                   class="form-control" {{ $field->is_required ? 'required' : '' }}>
                        @elseif($field->field_type === 'date')
                            <input type="date" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                   class="form-control" {{ $field->is_required ? 'required' : '' }}>
                        @elseif($field->field_type === 'file')
                            <input type="file" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                   class="form-control" {{ $field->is_required ? 'required' : '' }}>
                        @elseif($field->field_type === 'select')
                            <select name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                    class="form-select" {{ $field->is_required ? 'required' : '' }}>
                                <option value="">Select {{ $field->label }}</option>
                                @if($field->options)
                                    @foreach(json_decode($field->options, true) as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                @endif
                            </select>
                        @elseif($field->field_type === 'textarea')
                            <textarea name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                      class="form-control" rows="3" {{ $field->is_required ? 'required' : '' }}></textarea>
                        @elseif($field->field_type === 'checkbox')
                            <div class="form-check">
                                <input type="checkbox" name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                       class="form-check-input" value="1" {{ $field->is_required ? 'required' : '' }}>
                                <label class="form-check-label" for="{{ $field->field_name }}">
                                    {{ $field->label }}
                                </label>
                            </div>
                        @endif
                        
                        @if($field->help_text)
                            <small class="form-text text-muted">{{ $field->help_text }}</small>
                        @endif
                    </div>
                @endif
            @endforeach
        @endif

        <div class="form-group" style="margin-top:2.2rem;">
            <label for="programSelect" style="font-size:1.17rem;font-weight:700;"><i class="bi bi-book me-2"></i>Program</label>
            <select name="program_id" class="form-select" required id="programSelect" onchange="loadBatchesForProgram(); updateHiddenProgramId();">
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
            <input class="form-check-input" type="checkbox" id="termsCheckbox" required>
            <label class="form-check-label" for="termsCheckbox">
                I agree to the <a href="#" id="showTerms">Terms and Conditions</a>
            </label>
        </div>

        <hr style="margin-bottom: 2.1rem; margin-top: 1.2rem;">

        <div class="form-navigation" style="justify-content: space-between;">
            <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2"></i> Back
            </button>
            <button type="submit" class="btn btn-success btn-lg">
                <i class="bi bi-check-circle me-2"></i> Complete Registration
            </button>
        </div>
    </div>
</div>


        </form>
    </div>
</div>

<!-- Warning Modal -->
<div id="warningModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h3>Attention Required</h3>
        <p id="warningMessage"></p>
        <button onclick="closeWarningModal()" class="btn btn-primary">OK</button>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div id="termsModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 600px;">
        <h3>Terms and Conditions</h3>
        <div style="max-height: 320px; overflow-y: auto; text-align: left; margin-bottom: 1.5rem;">
            <p>
                By registering for this platform, you agree to abide by all policies, privacy guidelines, and usage restrictions as provided by our review center. Please read the full document before accepting.
            </p>
            <!-- Add more actual terms here if you want -->
        </div>
        <button onclick="closeTermsModal()" class="btn btn-secondary">Close</button>
    </div>
</div>


<!-- JavaScript for form validation and functionality -->
<script>
// Update hidden fields when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateHiddenStartDate();
    updateHiddenProgramId();
    updateProgressBar(); // Initialize progress bar
    
    // Add event listeners for changes
    const programSelect = document.getElementById('programSelect');
    if (programSelect) {
        programSelect.addEventListener('change', function() {
            updateHiddenProgramId();
            loadBatchesForProgram();
        });
    }
    
    // Add validation event listeners for Step 3
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    const emailField = document.getElementById('user_email');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    
    if (firstnameField) {
        firstnameField.addEventListener('input', validateStep3);
    }
    if (lastnameField) {
        lastnameField.addEventListener('input', validateStep3);
    }
    if (emailField) {
        emailField.addEventListener('input', function() {
            setTimeout(validateEmail, 300);
            setTimeout(validateStep3, 400);
        });
    }
    if (passwordField) {
        passwordField.addEventListener('input', function() {
            setTimeout(validatePassword, 50);
            setTimeout(validateStep3, 100);
        });
    }
    if (passwordConfirmField) {
        passwordConfirmField.addEventListener('input', function() {
            setTimeout(validatePasswordConfirmation, 50);
            setTimeout(validateStep3, 100);
        });
    }
});

// Function to validate email on blur
async function validateEmail() {
    const emailField = document.getElementById('user_email');
    const emailError = document.getElementById('emailError');
    
    if (!emailField) return true;
    
    const email = emailField.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailPattern.test(email)) {
        emailField.style.borderColor = '#dc3545';
        emailError.style.display = 'block';
        emailError.textContent = 'Please enter a valid email address.';
        return false;
    } else if (email) {
        // Check for existing email via AJAX
        try {
            const response = await fetch('/check-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ email: email })
            });
            
            const data = await response.json();
            if (data.exists) {
                emailField.style.borderColor = '#dc3545';
                emailError.style.display = 'block';
                emailError.textContent = 'This email is already registered. Please use a different email.';
                return false;
            } else {
                emailField.style.borderColor = '#28a745';
                emailError.style.display = 'none';
                return true;
            }
        } catch (error) {
            console.error('Email validation error:', error);
            emailField.style.borderColor = '#ccc';
            emailError.style.display = 'none';
            return true;
        }
    } else {
        emailField.style.borderColor = '#ccc';
        emailError.style.display = 'none';
        return true;
    }
}

// Function to validate password length
function validatePassword() {
    const passwordField = document.getElementById('password');
    const passwordError = document.getElementById('passwordError');
    
    if (!passwordField || !passwordError) return true;
    
    const password = passwordField.value;
    
    if (password.length > 0 && password.length < 8) {
        passwordField.style.borderColor = '#dc3545';
        passwordError.style.display = 'block';
        passwordError.textContent = 'Password must be at least 8 characters long.';
        return false;
    } else if (password.length >= 8) {
        passwordField.style.borderColor = '#28a745';
        passwordError.style.display = 'none';
        return true;
    } else {
        passwordField.style.borderColor = '#ccc';
        passwordError.style.display = 'none';
        return true;
    }
}

// Function to validate password confirmation
function validatePasswordConfirmation() {
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const passwordMatchError = document.getElementById('passwordMatchError');
    
    if (!passwordField || !passwordConfirmField || !passwordMatchError) return true;
    
    const password = passwordField.value;
    const passwordConfirm = passwordConfirmField.value;
    
    if (passwordConfirm.length > 0 && password !== passwordConfirm) {
        passwordConfirmField.style.borderColor = '#dc3545';
        passwordMatchError.style.display = 'block';
        passwordMatchError.textContent = 'Passwords do not match.';
        return false;
    } else if (passwordConfirm.length > 0 && password === passwordConfirm) {
        passwordConfirmField.style.borderColor = '#28a745';
        passwordMatchError.style.display = 'none';
        return true;
    } else {
        passwordConfirmField.style.borderColor = '#ccc';
        passwordMatchError.style.display = 'none';
        return true;
    }
}

// Function to validate all Step 3 (Account Registration) fields
function validateStep3() {
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    const emailField = document.getElementById('user_email');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const nextBtn = document.getElementById('step3NextBtn');
    
    // Don't validate if we're not on step 3 or if the step is not visible
    const step3Element = document.getElementById('step-3');
    if (!step3Element || step3Element.style.display === 'none' || !step3Element.classList.contains('active')) {
        return false;
    }
    
    // Check if all required fields are filled
    const isFirstnameFilled = firstnameField && firstnameField.value.trim().length > 0;
    const isLastnameFilled = lastnameField && lastnameField.value.trim().length > 0;
    const isEmailFilled = emailField && emailField.value.trim().length > 0;
    const isPasswordFilled = passwordField && passwordField.value.length >= 8;
    const isPasswordConfirmFilled = passwordConfirmField && passwordConfirmField.value.length > 0;
    
    // Check if validations pass
    const isPasswordValid = validatePassword();
    const isPasswordConfirmValid = validatePasswordConfirmation();
    
    // Check if email field has error by looking at error message visibility
    const emailError = document.getElementById('emailError');
    const emailHasError = emailError && emailError.style.display === 'block';
    
    // Check if password errors are showing
    const passwordError = document.getElementById('passwordError');
    const passwordMatchError = document.getElementById('passwordMatchError');
    const passwordHasError = passwordError && passwordError.style.display === 'block';
    const passwordMatchHasError = passwordMatchError && passwordMatchError.style.display === 'block';
    
    // Enable next button only if all conditions are met
    const allFieldsFilled = isFirstnameFilled && isLastnameFilled && isEmailFilled && isPasswordFilled && isPasswordConfirmFilled;
    const allValidationsPassed = isPasswordValid && isPasswordConfirmValid && !emailHasError && !passwordHasError && !passwordMatchHasError;
    
    console.log('Step 3 Validation:', {
        allFieldsFilled,
        allValidationsPassed,
        isFirstnameFilled,
        isLastnameFilled,
        isEmailFilled,
        isPasswordFilled,
        isPasswordConfirmFilled,
        isPasswordValid,
        isPasswordConfirmValid,
        emailHasError,
        passwordHasError,
        passwordMatchHasError
    });
    
    if (nextBtn) {
        if (allFieldsFilled && allValidationsPassed) {
            nextBtn.disabled = false;
            nextBtn.style.opacity = '1';
            nextBtn.style.cursor = 'pointer';
        } else {
            nextBtn.disabled = true;
            nextBtn.style.opacity = '0.5';
            nextBtn.style.cursor = 'not-allowed';
        }
    }
    
    return allFieldsFilled && allValidationsPassed;
}

// Handle form submission and add batch_id if selected
function handleFormSubmission(event) {
    const selectedBatchId = sessionStorage.getItem('selectedBatchId');
    
    // If a batch is selected, add it to the form before submission
    if (selectedBatchId && selectedBatchId !== 'null' && selectedBatchId !== '') {
        const form = event.target;
        const batchInput = document.createElement('input');
        batchInput.type = 'hidden';
        batchInput.name = 'batch_id';
        batchInput.value = selectedBatchId;
        form.appendChild(batchInput);
        console.log('Added batch_id to form submission:', selectedBatchId);
    }
    
    return true; // Allow form to submit
}

 document.addEventListener('DOMContentLoaded', function() {
        // Show modal when link is clicked
        const termsLink = document.getElementById('showTerms');
        if (termsLink) {
            termsLink.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('termsModal').style.display = 'flex';
            });
        }
    });

    function closeTermsModal() {
        document.getElementById('termsModal').style.display = 'none';
    }
function showTermsModal() {
  const modal = document.getElementById('termsModal');
  if (!modal) return;
  // make it take up the screen
  modal.style.display = 'flex';
  // give the browser a tick to apply that, then fade in
  requestAnimationFrame(() => modal.classList.add('active'));
  // lock background scrolling
  document.body.style.overflow = 'hidden';
}

function closeTermsModal() {
  const modal = document.getElementById('termsModal');
  if (!modal) return;
  // fade out
  modal.classList.remove('active');
  // after the fade, remove it from flow
  setTimeout(() => {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }, 300); // match your CSS transition-duration (0.3s)
}

document.addEventListener('DOMContentLoaded', () => {
  // click the link
  document.getElementById('showTerms')
          .addEventListener('click', e => {
    e.preventDefault();
    showTermsModal();
  });

  // click outside the content to close
  document.getElementById('termsModal')
          .addEventListener('click', e => {
    if (e.target.id === 'termsModal') closeTermsModal();
  });

  // escape key
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeTermsModal();
  });
});

</script>
@endsection
