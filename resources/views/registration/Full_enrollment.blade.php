@extends('layouts.navbar')

@section('title', 'Student Registration')
@section('hide_footer', true)
@section('body_class', 'registration-page')

@push('styles')
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<meta name="csrf-token" content="{{ csrf_token() }}">
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
        // Check if user is logged in
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
        // Validate account registration
        if (!validateStep3()) {
            showWarning('Please fill in all required fields correctly.');
            return;
        }
        // Go to student registration
        copyAccountDataToStudentForm();
        animateStepTransition('step-3', 'step-4');
        currentStep = 4;
        // Auto-fill user data
        setTimeout(() => {
            copyAccountDataToStudentForm();
        }, 300);
    }
}


function prevStep() {
    console.log('prevStep called, current step:', currentStep);

    if (currentStep === 4) {
        // From student registration, check if user is logged in
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
</style>
@endpush

@section('content')
<!-- Validation Errors Display -->
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 20px auto; max-width: 1200px;">
        <h6><i class="bi bi-exclamation-triangle"></i> Please correct the following errors:</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- SINGLE CENTERED CONTAINER - No nested layers -->
<div class="registration-container">
    <form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" class="registration-form" id="enrollmentForm" novalidate>
        @csrf
        <input type="hidden" name="enrollment_type" value="full">
        <input type="hidden" name="package_id" value="">
        <input type="hidden" name="plan_id" value="1">

    {{-- STEP 1: PACKAGE SELECTION --}}
    <div class="step active" id="step-1">
        <h2 style="text-align:center; margin-bottom:30px; font-weight:700; letter-spacing:1px;">
            SELECT YOUR PACKAGE
        </h2>
        
        <!-- Bootstrap Horizontal Scrolling Package Carousel -->
        <div class="packages-carousel-container">
            <div class="packages-carousel" id="packagesCarousel">
                @foreach($packages as $package)
                <div class="package-card-wrapper">
                    <div class="card package-card h-100 shadow-lg" onclick="selectPackage('{{ $package->package_id }}', '{{ $package->package_name }}', '{{ $package->amount }}')" data-package-id="{{ $package->package_id }}" data-package-price="{{ $package->amount }}">
                        <div class="package-image-header">
                            <div class="package-icon">📦</div>
                            @if($loop->first)
                                <div class="package-badge">Popular</div>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title package-title">{{ $package->package_name }}</h5>
                            <p class="card-text package-description flex-grow-1" title="{{ $package->description ?? 'Complete package with all features included.' }}">
                                {{ $package->description ?? 'Complete package with all features included.' }}
                            </p>
                            <div class="mt-auto">
                                <div class="package-price">₱{{ number_format($package->amount, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Navigation Arrows -->
            @if($packages->count() > 2)
                <button type="button" class="carousel-nav prev-btn" onclick="scrollPackages('left')" id="prevPackageBtn">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="carousel-nav next-btn" onclick="scrollPackages('right')" id="nextPackageBtn">
                    <i class="bi bi-chevron-right"></i>
                </button>

            @endif
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <div id="selectedPackageDisplay" style="display: none; margin-bottom: 20px; padding: 15px; background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%); border-radius: 12px; border: 2px solid #4caf50; max-width: 400px; margin: 0 auto 20px;">
                <strong style="color: #2e7d2e; font-size: 1.1rem;">Selected Package: <span id="selectedPackageName"></span></strong>
                <div style="color: #2e7d2e; font-size: 1.2rem; font-weight: bold; margin-top: 8px;">
                    Price: <span id="selectedPackagePrice"></span>
                </div>
            </div>
            <button type="button" onclick="nextStep()" id="packageNextBtn" disabled
                    class="btn btn-primary btn-lg" style="opacity: 0.5;">
                Next<i class="bi bi-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    {{-- STEP 2: LEARNING MODE SELECTION --}}
    <div class="step" id="step-2">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            LEARNING MODE SELECTION
        </h2>
        
        <div style="max-width: 600px; margin: 0 auto;">
            <h3 style="margin-bottom: 20px; text-align: center;">Choose Your Learning Mode</h3>
            
            <div class="learning-mode-container" style="display: flex; gap: 30px; justify-content: center; margin-bottom: 30px; flex-wrap: wrap;">
                <div class="learning-mode-card" onclick="selectLearningMode('synchronous')" data-mode="synchronous"
                     style="background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%); border-radius: 15px; padding: 30px 20px; width: 250px; cursor: pointer; 
                            transition: all 0.3s ease; border: 3px solid transparent; text-align: center; color: white;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">🕐</div>
                    <h4 style="margin: 0 0 10px 0; color: #fff;">Synchronous</h4>
                    <p style="margin: 0; color: #ccc; font-size: 14px;">Real-time classes with live interaction, scheduled sessions, and immediate feedback.</p>
                </div>
                
                <div class="learning-mode-card" onclick="selectLearningMode('asynchronous')" data-mode="asynchronous"
                     style="background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%); border-radius: 15px; padding: 30px 20px; width: 250px; cursor: pointer; 
                            transition: all 0.3s ease; border: 3px solid transparent; text-align: center; color: white;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">🎯</div>
                    <h4 style="margin: 0 0 10px 0; color: #fff;">Asynchronous</h4>
                    <p style="margin: 0; color: #ccc; font-size: 14px;">Self-paced learning with recorded materials, flexible schedule, and individual progress.</p>
                </div>
            </div>
            
            <div id="selectedLearningModeDisplay" style="display: none; margin: 20px 0; padding: 15px; background: #e8f5e8; border-radius: 8px; text-align: center;">
                <strong>Selected Learning Mode: <span id="selectedLearningModeName"></span></strong>
            </div>
            
            <input type="hidden" name="learning_mode" id="learning_mode" value="">
            
            <div style="display:flex; gap:16px; justify-content:center; margin-top: 30px;">
                <button type="button" onclick="prevStep()" class="back-btn"
                        style="padding:12px 30px; border:none; border-radius:8px; background:#ccc; cursor:pointer;">
                    Back
                </button>
                <button type="button" onclick="nextStep()" id="learningModeNextBtn" disabled
                        style="background:linear-gradient(90deg,#a259c6,#6a82fb); color:#fff; border:none; 
                               border-radius:8px; padding:12px 40px; font-size:1.1rem; cursor:pointer; opacity: 0.5;">
                    Next
                </button>
            </div>
        </div>
    </div>

    {{-- STEP 3: ACCOUNT REGISTRATION --}}
    <div class="step" id="step-3">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            ACCOUNT REGISTRATION
        </h2>
        <div style="display:flex; flex-direction:column; gap:18px; align-items:center;">
            <div style="display:flex; gap:16px; width:100%; max-width:500px;">
                <input type="text" name="user_firstname" id="user_firstname" placeholder="First Name" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
                <input type="text" name="user_lastname" id="user_lastname" placeholder="Last Name" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
            </div>
            <input type="email" name="email" id="user_email" placeholder="Email" required
                   style="width:100%; max-width:500px; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
            <div id="emailError" style="display: none; color: #dc3545; font-size: 14px; margin-top: -10px; text-align: center;">
                This email is already registered. Please use a different email.
            </div>
            <div style="display:flex; gap:16px; width:100%; max-width:500px;">
                <input type="password" name="password" id="password" placeholder="Password"
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password"
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
            </div>
            <div id="passwordError" style="color: #dc3545; font-size: 14px; margin-top: -10px; text-align: center; min-height: 20px; display: none;">
                Password must be at least 8 characters long.
            </div>
            <div id="passwordMatchError" style="color: #dc3545; font-size: 14px; margin-top: -10px; text-align: center; min-height: 20px; display: none;">
                Passwords do not match.
            </div>
            <div style="text-align: center; margin-top: -10px;">
                <p style="color: #666; font-size: 14px; margin: 0;">
                    Already have an account? 
                    <a href="#" onclick="loginWithPackage()" style="color: #1c2951; text-decoration: underline; font-weight: 600;">
                        Click here to login
                    </a>
                </p>
            </div>
            <div style="display:flex; gap:16px; justify-content:center;">
                <button type="button" onclick="prevStep()" class="back-btn"
                        style="padding:12px 30px; border:none; border-radius:8px; background:#ccc; cursor:pointer;">
                    Back
                </button>
                <button type="button" onclick="nextStep()" id="step3NextBtn"
                        style="background:linear-gradient(90deg,#a259c6,#6a82fb); color:#fff;
                               border:none; border-radius:8px; padding:12px 40px; font-size:1.1rem; font-weight:600;
                               box-shadow:0 2px 8px rgba(160,89,198,0.08); cursor:not-allowed; opacity: 0.5;" disabled>
                    Next
                </button>
            </div>
        </div>
    </div>

    {{-- STEP 4: FULL STUDENT REGISTRATION --}}
    <div class="step" id="step-4">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            STUDENT FULL PROGRAM REGISTRATION
        </h2>

        @if($student)
        <div class="alert alert-info" style="background-color:#e7f3ff; border:1px solid #b3d9ff; color:#0066cc; padding:12px; border-radius:6px; margin-bottom:20px; text-align:center;">
            <i class="bi bi-info-circle"></i> Your existing information has been pre-filled. You can update any field as needed.
        </div>
        @endif

        {{-- Dynamic Form Fields (includes all sections) --}}
        <div id="dynamic-fields-container">
            <x-dynamic-enrollment-form :requirements="$formRequirements" />
        </div>

        <h3><i class="bi bi-book me-2"></i>Program</h3>
        <div class="input-row">
            <select name="program_id" class="form-select" required id="programSelect">
                <option value="">Select Program</option>
                @foreach($programs as $program)
                    <option value="{{ $program->program_id }}"
                        {{ old('program_id', $programId ?? '') == $program->program_id ? 'selected' : '' }}>
                        {{ $program->program_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <h3><i class="bi bi-calendar-event me-2"></i>Start Date</h3>
        <div class="course-box" style="margin-bottom:20px;">
            <input type="date" name="Start_Date" class="form-control"
                   value="{{ $student->start_date ?? old('Start_Date') }}" required>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="termsCheckbox" required>
            <label class="form-check-label" for="termsCheckbox">
                I agree to the 
                <a href="#" id="showTerms" class="text-primary text-decoration-underline">
                  Terms and Conditions
                </a>
            </label>
        </div>

        <div class="d-flex gap-3 justify-content-center flex-column flex-md-row">
            <!-- Mobile: Full width buttons, Tablet & PC: Side by side -->
            <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg order-2 order-md-1">
                <i class="bi bi-arrow-left me-2"></i>Back
            </button>
            <button type="submit" class="btn btn-primary btn-lg order-1 order-md-2" id="enrollBtn">
                <i class="bi bi-check-circle me-2"></i>Enroll Now
            </button>
        </div>
    </div>    </form>
</div> <!-- END SINGLE CENTERED CONTAINER -->

{{-- Terms and Conditions Modal --}}
<div id="termsModal"
     style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh;
            background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
  <div style="background:#fff; padding:30px; border-radius:16px; max-width:600px; width:90%;">
    <h2>Terms and Conditions</h2>
    <div style="max-height:300px; overflow-y:auto; margin:20px 0;">
      <p>
        By registering, you agree to abide by the rules and regulations of the review center.
        You consent to the processing of your personal data for enrollment and communication.
        All fees paid are non-refundable once the review program has started.
      </p>
    </div>
    <button id="agreeBtn" type="button"
            style="background:#1c2951; color:#fff; border:none; border-radius:8px;
                   padding:10px 30px; font-size:1rem; cursor:pointer;">
      Agree and Continue
    </button>
  </div>
</div>

{{-- Success Modal - Only show for registration completion messages --}}
@if(session('success') && str_contains(session('success'), 'registration'))
  <div id="successModal"
       style="display:flex; position:fixed; top:0; left:0; width:100vw; height:100vh;
              background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center;">
    <div class="success-modal-content" style="background:white; border-radius:20px; max-width:500px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideIn 0.3s ease-out;">
      <!-- Success Icon -->
      <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding:40px 20px 20px; color:white;">
        <div style="width:80px; height:80px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; backdrop-filter:blur(10px);">
          <i class="bi bi-check-circle-fill" style="font-size:2.5rem; color:white;"></i>
        </div>
        <h2 style="margin:0; font-size:1.8rem; font-weight:700; color:white;">Registration Successful!</h2>
      </div>
      
      <!-- Content -->
      <div style="padding:30px;">
        <p style="color:#666; font-size:1.1rem; margin:0 0 30px; line-height:1.5;">{{ session('success') }}</p>
        
        <!-- Buttons -->
        <div style="display:flex; gap:15px; justify-content:center, flex-wrap:wrap;">
          <button id="successOk" type="button" class="btn btn-primary btn-lg"
                 style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); border:none; padding:12px 30px; border-radius:10px; color:white; font-weight:600; cursor:pointer; transition:all 0.3s ease;">
            <i class="bi bi-house-door me-2"></i>Go to Homepage
          </button>
          <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary btn-lg" 
             style="padding:12px 30px; border-radius:10px; text-decoration:none; transition:all 0.3s ease;">
            <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
          </a>
        </div>
      </div>
    </div>
  </div>
  
  <style>
    @keyframes modalSlideIn {
      from { opacity: 0; transform: translateY(-50px) scale(0.9); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .success-modal-content button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
  </style>
@endif

{{-- Login Success Modal - Shows welcome back message when returning from login --}}
@if(session('success') && str_contains(session('success'), 'Welcome back'))
  <div id="loginSuccessModal" 
       style="position:fixed; top:20px; right:20px; background:#fff; padding:15px 20px; 
              border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:1000; 
              max-width:300px; animation: slideIn 0.5s ease-out, fadeOut 0.5s ease-out 5s forwards;">
    <p style="margin:0; color:#333;"><strong>{{ session('success') }}</strong></p>
  </div>
  <style>
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes fadeOut {
      from { opacity: 1; }
      to { opacity: 0; visibility: hidden; }
    }
  </style>
@endif

{{-- Warning Modal - Shows validation warnings --}}
<div id="warningModal"
     style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); 
            display:none; justify-content:center; align-items:center; z-index:10000;">
  <div class="warning-modal-content" style="background:white; border-radius:20px; max-width:500px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideIn 0.3s ease-out;">
    <div style="background:linear-gradient(135deg, #FFA726 0%, #FF9800 100%); padding:30px; color:white;">
      <i class="bi bi-exclamation-triangle" style="font-size:3rem; margin-bottom:15px;"></i>
      <h3 style="margin:0; font-weight:600;">Warning</h3>
    </div>
    <div style="padding:30px;">
      <p id="warningMessage" style="margin:0 0 25px 0; font-size:16px; color:#555; line-height:1.5;"></p>
      <button onclick="closeWarningModal()" 
              style="background:linear-gradient(135deg, #FFA726 0%, #FF9800 100%); color:white; border:none; 
                     padding:12px 30px; border-radius:25px; font-size:16px; font-weight:600; 
                     cursor:pointer; transition:all 0.3s ease; box-shadow:0 4px 15px rgba(255,152,0,0.3);">
        OK
      </button>
    </div>
  </div>
</div>

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

// Initialize carousel
document.addEventListener('DOMContentLoaded', function() {
    // Hide Step 2 if user is logged in and remove required attributes
    if (isUserLoggedIn) {
        const step2 = document.getElementById('step-2');
        if (step2) {
            step2.style.display = 'none';
            
            // Remove required attributes from Step 2 fields to prevent form validation errors
            const step2Fields = step2.querySelectorAll('input[required]');
            step2Fields.forEach(field => {
                field.removeAttribute('required');
                console.log('Removed required attribute from:', field.name);
            });
        }
        console.log('User is logged in - Step 2 (Account Registration) hidden and validation disabled');
    }
    
    // Check if we're returning from login with a package selection
    const continueEnrollment = sessionStorage.getItem('continueEnrollment');
    const skipToPayment = sessionStorage.getItem('skipToPayment');
    const savedPackageId = sessionStorage.getItem('selectedPackageId');
    const savedPackageName = sessionStorage.getItem('selectedPackageName');
    
    if (continueEnrollment === 'true' && savedPackageId && savedPackageName) {
        // Clear the session flags
        sessionStorage.removeItem('continueEnrollment');
        sessionStorage.removeItem('skipToPayment');
        
        // Auto-select the saved package
        selectedPackageId = savedPackageId;
        
        // Find and highlight the package card
        const packageCard = document.querySelector(`[data-package-id="${savedPackageId}"]`);
        if (packageCard) {
            packageCard.classList.add('selected');
        }
        
        // Update the form
        const packageInput = document.querySelector('input[name="package_id"]');
        if (packageInput) {
            packageInput.value = savedPackageId;
        }
        
        // Show selected package display
        document.getElementById('selectedPackageName').textContent = savedPackageName;
        document.getElementById('selectedPackageDisplay').style.display = 'block';
        
        // Enable next button
        const nextBtn = document.getElementById('packageNextBtn');
        nextBtn.disabled = false;
        nextBtn.style.opacity = '1';
        
        // If user logged in from step 2 (account registration), skip to learning mode step (step 3)
        if (skipToPayment === 'true') {
            setTimeout(() => {
                // Go to step 3 (learning mode)
                animateStepTransition('step-1', 'step-3');
                currentStep = 3;
            }, 500);
        }
    }
    
    // Fill logged-in user data on page load
    if (isUserLoggedIn) {
        fillLoggedInUserData();
    }
    
    // Add program selection handler to update hidden input
    const programSelectField = document.getElementById('programSelect');
    if (programSelectField) {
        programSelectField.addEventListener('change', function() {
            const hiddenProgramInput = document.querySelector('input[name="program_id"]');
            if (hiddenProgramInput) {
                hiddenProgramInput.value = this.value;
                console.log('Updated hidden program_id input to:', this.value);
            }
        });
    }
    
    // Initialize carousel first
    updateArrowStates();
    
    // Adjust for responsive
    function adjustCarousel() {
        const slider = document.querySelector('.package-slider');
        if (slider) {
            if (window.innerWidth <= 768) {
                packagesPerView = 1;
                slider.style.width = '340px';
            } else {
                packagesPerView = 2;
                slider.style.width = '700px';
            }
            // Reset position when switching views
            currentPackageIndex = 0;
            const track = document.getElementById('packageTrack');
            if (track) {
                track.style.transform = 'translateX(0px)';
            }
            updateArrowStates();
        }
    }
    
    adjustCarousel();
    window.addEventListener('resize', adjustCarousel);

    // Email validation
    const emailField = document.getElementById('user_email');
    if (emailField) {
        emailField.addEventListener('blur', validateEmail);
        emailField.addEventListener('input', function() {
            // Reset styling when user starts typing
            this.style.borderColor = '#ccc';
            document.getElementById('emailError').style.display = 'none';
            // Validate all fields when email changes
            setTimeout(validateStep3, 100);
        });
    }

    // First name and last name validation
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    
    if (firstnameField) {
        firstnameField.addEventListener('input', function() {
            // Validate all fields when first name changes
            setTimeout(validateStep3, 100);
        });
    }
    
    if (lastnameField) {
        lastnameField.addEventListener('input', function() {
            // Validate all fields when last name changes
            setTimeout(validateStep3, 100);
        });
    }

    // Password validation
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    
    if (passwordField) {
        passwordField.addEventListener('blur', validatePassword);
        passwordField.addEventListener('input', function() {
            // Reset styling when user starts typing
            this.style.borderColor = '#ccc';
            // Don't hide the error here - let validatePassword handle it
            // Also validate confirmation when password changes
            setTimeout(validatePassword, 50);
            setTimeout(validatePasswordConfirmation, 100);
            setTimeout(validateStep3, 200);
        });
    }
    
    if (passwordConfirmField) {
        passwordConfirmField.addEventListener('blur', validatePasswordConfirmation);
        passwordConfirmField.addEventListener('input', function() {
            // Reset styling when user starts typing
            this.style.borderColor = '#ccc';
            document.getElementById('passwordMatchError').style.display = 'none';
            setTimeout(validateStep3, 100);
        });
    }

    // Initial validation on page load
    setTimeout(validateStep3, 500);

    // Terms & Conditions
    const showTerms = document.getElementById('showTerms');
    const termsModal = document.getElementById('termsModal');
    const agreeBtn = document.getElementById('agreeBtn');
    const termsCheckbox = document.getElementById('termsCheckbox');
    const enrollBtn = document.getElementById('enrollBtn');

    if (termsCheckbox && enrollBtn) {
        // For logged-in users doing multiple enrollments, enable the button immediately
        if (isUserLoggedIn) {
            termsCheckbox.disabled = false;
            termsCheckbox.checked = true;
            enrollBtn.disabled = false;
        } else {
            // For new users, require terms agreement
            termsCheckbox.disabled = true;
            enrollBtn.disabled = true;
        }

        if (showTerms) {
            showTerms.addEventListener('click', function(e) {
                e.preventDefault();
                agreeBtn.disabled = false;
                termsModal.style.display = 'flex';
            });
        }

        if (agreeBtn) {
            agreeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                termsModal.style.display = 'none';
                termsCheckbox.disabled = false;
                termsCheckbox.checked = true;
                enrollBtn.disabled = false;
            });
        }

        }

        // Add event listener for checkbox change
        termsCheckbox.addEventListener('change', function() {
            enrollBtn.disabled = !this.checked;
        });

        window.addEventListener('click', function(e) {
            if (e.target === termsModal) {
                termsModal.style.display = 'none';
            }
        });
    }

    // Handle program selection
    const programSelect = document.getElementById('programSelect');
    if (programSelect) {
        programSelect.addEventListener('change', function() {
            // Update the hidden program_id input
            const hiddenProgramInput = document.querySelector('input[name="program_id"]');
            if (hiddenProgramInput) {
                hiddenProgramInput.value = this.value;
            }
            console.log('Program selected:', this.value);
        });
    }

    // Add form submission debugging
    const enrollmentForm = document.getElementById('enrollmentForm');
    if (enrollmentForm) {
        enrollmentForm.addEventListener('submit', function(e) {
            console.log('Form submission attempt detected');
            console.log('User logged in:', isUserLoggedIn);
            console.log('Selected package ID:', selectedPackageId);
            console.log('Learning mode:', document.getElementById('learning_mode')?.value);
            console.log('Payment method:', selectedPaymentMethod);
            console.log('Terms checked:', document.getElementById('termsCheckbox')?.checked);
            
            // Check if required fields are filled
            const programSelect = document.querySelector('select[name="program_id"]');
            const startDate = document.querySelector('input[name="Start_Date"]');
            
            console.log('Program selected:', programSelect?.value);
            console.log('Start date:', startDate?.value);
            
            // For debugging - don't prevent submission, just log
            // e.preventDefault();
        });
    }

    // Add form submission debugging
    const formElement = document.getElementById('enrollmentForm');
    if (formElement) {
        formElement.addEventListener('submit', function(e) {
            console.log('Form submission attempted...');
            
            // Check all required fields
            const requiredFields = formElement.querySelectorAll('[required]');
            let missingFields = [];
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    missingFields.push(field.name || field.id);
                }
            });
            
            if (missingFields.length > 0) {
                console.error('Missing required fields:', missingFields);
                e.preventDefault();
                showWarning('Please fill in all required fields: ' + missingFields.join(', '));
                return;
            }
            
            // Check if program is selected
            const programSelect = document.getElementById('programSelect');
            if (programSelect && !programSelect.value) {
                console.error('No program selected');
                e.preventDefault();
                showWarning('Please select a program');
                return;
            }
            
            // Check if package is selected
            const packageInput = document.querySelector('input[name="package_id"]');
            if (packageInput && !packageInput.value) {
                console.error('No package selected');
                e.preventDefault();
                showWarning('Please select a package');
                return;
            }
            
            // Check if learning mode is selected
            const learningModeInput = document.getElementById('learning_mode');
            if (learningModeInput && !learningModeInput.value) {
                console.error('No learning mode selected');
                e.preventDefault();
                showWarning('Please select a learning mode');
                return;
            }
            
            console.log('Form validation passed, submitting...');
            console.log('Program ID:', programSelect ? programSelect.value : 'not found');
            console.log('Package ID:', packageInput ? packageInput.value : 'not found');
            console.log('Learning Mode:', learningModeInput ? learningModeInput.value : 'not found');
        });
    }

    // Success Modal
    const successModal = document.getElementById('successModal');
    const successOk = document.getElementById('successOk');
    if (successModal) {
        successModal.style.display = 'flex';
        if (successOk) {
            successOk.addEventListener('click', function() {
                window.location.href = '{{ route("home") }}';
            });
        }
    }
});

// Form validation before submission
document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default submission
    
    console.log('Form submission attempted');
    
    // Check if we're on the final step
    if (currentStep !== 4) {
        // Show warning modal instead of alert
        showWarning('Please complete all steps before enrolling.');
        return false;
    }
    
    // Validate required fields based on user login status
    let missingFields = [];
    
    // Always required fields
    const packageId = document.querySelector('input[name="package_id"]').value;
    const programId = document.querySelector('select[name="program_id"]').value;
    const startDate = document.querySelector('input[name="Start_Date"]').value;
    const termsAccepted = document.querySelector('#termsCheckbox').checked;
    
    if (!packageId) missingFields.push('Package selection');
    if (!programId) missingFields.push('Program selection');
    if (!startDate) missingFields.push('Start date');
    if (!termsAccepted) missingFields.push('Terms and conditions agreement');
    
    // Check password fields only if user is not logged in
    if (!isUserLoggedIn) {
        const password = document.querySelector('#password').value;
        const passwordConfirm = document.querySelector('#password_confirmation').value;
        const email = document.querySelector('#user_email').value;
        const firstName = document.querySelector('#user_firstname').value;
        const lastName = document.querySelector('#user_lastname').value;
        
        if (!email) missingFields.push('Email');
        if (!firstName) missingFields.push('First name');
        if (!lastName) missingFields.push('Last name');
        if (!password) missingFields.push('Password');
        if (!passwordConfirm) missingFields.push('Password confirmation');
        if (password !== passwordConfirm) missingFields.push('Password confirmation (passwords must match)');
    }
    
    if (missingFields.length > 0) {
        showWarning('Please fill in the following required fields:\n• ' + missingFields.join('\n• '));
        return false;
    }
    
    // If validation passes, submit the form
    console.log('Form validation passed, submitting...');
    this.submit();
});

// Initialize prefill on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing prefill logic');
    
    // If user is logged in and we're on step 4, fill their data
    if (isUserLoggedIn && currentStep === 4) {
        fillLoggedInUserData();
    }
    
    // Auto-fill step 4 when step becomes active (for transitions)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const target = mutation.target;
                if (target.id === 'step-4' && target.classList.contains('active')) {
                    setTimeout(() => {
                        fillLoggedInUserData();
                        copyAccountDataToStudentForm();
                    }, 100);
                }
            }
        });
    });
    
    const step4Element = document.getElementById('step-4');
    if (step4Element) {
        observer.observe(step4Element, { attributes: true });
    }
});
</script>
@endsection
