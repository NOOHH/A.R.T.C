@extends('layouts.navbar')

@section('title', 'Modular Enrollment')
@section('hide_footer', true)
@section('body_class', 'registration-page')

@push('styles')
    {!! App\Helpers\UIHelper::getNavbarStyles() !!}

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
      // now these will resolve correctly
      const PREFILL_URL  = "{{ route('registration.userPrefill') }}";
      const VALIDATE_URL = "{{ route('registration.validateFile') }}";
      const CSRF_TOKEN   = "{{ csrf_token() }}";
    </script>

    <link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Full_Enrollment.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<!-- reCAPTCHA -->
@if(env('RECAPTCHA_SITE_KEY'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

<style>
/* OTP Verification Styles */
.email-input-group {
    position: relative;
}

.email-input-group .btn-otp {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    padding: 6px 12px;
    font-size: 12px;
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    border-radius: 4px;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.email-input-group .btn-otp:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    transform: translateY(-50%) translateY(-1px);
}

.email-input-group input {
    padding-right: 100px;
}

.otp-container {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin: 15px 0;
    transition: all 0.3s ease;
}

.otp-container.active {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.otp-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.otp-icon {
    width: 40px;
    height: 40px;
    background: #0d6efd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 12px;
    font-size: 18px;
}

.otp-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #495057;
}

.otp-input {
    font-family: 'Courier New', monospace;
    font-size: 18px;
    font-weight: 600;
    text-align: center;
    letter-spacing: 3px;
    padding: 12px;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.otp-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn-otp {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    border-radius: 6px;
    color: white;
    font-weight: 600;
    padding: 8px 16px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
}

.btn-otp:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

.btn-otp:disabled {
    background: #6c757d;
    transform: none;
    box-shadow: none;
}

.btn-otp.verified {
    background: linear-gradient(135deg, #198754 0%, #157347 100%);
}

.status-message {
    margin-top: 12px;
    padding: 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
}

.status-success {
    background-color: #d1e7dd;
    border: 1px solid #badbcc;
    color: #0f5132;
}

.status-error {
    background-color: #f8d7da;
    border: 1px solid #f5c2c7;
    color: #842029;
}

.step-indicator {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    background: #0d6efd;
    color: white;
    border-radius: 50%;
    font-size: 12px;
    font-weight: 600;
    margin-right: 8px;
}

.step-indicator.completed {
    background: #198754;
}

/* OTP Modal Styles */
#otpModal .modal-dialog {
    max-width: 400px;
}

#otpModal .modal-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

#otpModal .modal-title {
    color: #495057;
    font-weight: 600;
}

#otpModal .otp-digit {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

#otpModal .otp-digit:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    outline: none;
}

#otpModal .status-message {
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
}

#otpModal .status-message.status-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

#otpModal .status-message.status-success {
    background: #d1edff;
    color: #084298;
    border: 1px solid #b6d4fe;
}

#otpModal .btn-primary.verified {
    background: #198754;
    border-color: #198754;
}

#otpModal .modal-footer {
    border-top: 1px solid #dee2e6;
    padding: 1rem;
}
</style>


<!-- Critical JavaScript functions for immediate availability -->
<script>
    
    
// Global variables (declare first for immediate availability)
let currentStep = 1;
let selectedPackageId = null;
let selectedPaymentMethod = null;
let currentPackageIndex = 0;
let packagesPerView = 2;
let totalPackages = <?php echo isset($packages) && is_countable($packages) ? (int)count($packages) : 0; ?>;

// reCAPTCHA configuration
const hasRecaptcha = @if(env('RECAPTCHA_SITE_KEY')) true @else false @endif;

// Plan configuration data
const planConfig = {
    full: {
        enableSynchronous: {{ isset($fullPlan) && $fullPlan->enable_synchronous ? 'true' : 'false' }},
        enableAsynchronous: {!! isset($fullPlan) && $fullPlan->enable_asynchronous ? 'true' : 'false' !!}
    },
    modular: {
        enableSynchronous: {!! isset($modularPlan) && $modularPlan->enable_synchronous ? 'true' : 'false' !!},
        enableAsynchronous: {!! isset($modularPlan) && $modularPlan->enable_asynchronous ? 'true' : 'false' !!}
    }
};

console.log('Plan configuration:', planConfig);

// Check if user is logged in (set from server)
const isUserLoggedIn = @if(session('user_id')) true @else false @endif;
const loggedInUserId = '@if(session("user_id")){{ session("user_id") }}@endif';
const loggedInUserName = '@if(session("user_name")){{ session("user_name") }}@endif';
const loggedInUserFirstname = '@if(session("user_firstname")){{ session("user_firstname") }}@endif';
const loggedInUserLastname = '@if(session("user_lastname")){{ session("user_lastname") }}@endif';
const loggedInUserEmail = '@if(session("user_email")){{ session("user_email") }}@endif';

console.log('Session check:', {
    isUserLoggedIn,
    loggedInUserId,
    loggedInUserName,
    loggedInUserFirstname,
    loggedInUserLastname,
    loggedInUserEmail
});

// Module and program selection variables
let selectedModules = [];
let selectedProgramId = null;

function loadModules(programId) {
    const select = document.getElementById('program_select');
    const selectedOption = select.options[select.selectedIndex];
    
    if (!programId) {
        document.getElementById('moduleSelectionArea').style.display = 'none';
        document.getElementById('selectedProgramName').textContent = 'None';
        selectedProgramId = null;
        selectedPackageId = null;
        selectedModules = [];
        updateModulesSummary();
        return;
    }
    
    selectedProgramId = programId;
    selectedPackageId = selectedOption.getAttribute('data-package-id');
    const packageName = selectedOption.getAttribute('data-package-name');
    const programName = selectedOption.textContent.split(' - ')[0];
    
    document.getElementById('selectedProgramName').textContent = programName;
    document.getElementById('packageIdInput').value = selectedPackageId;
    document.getElementById('hidden_program_id').value = programId;
    
    // Show loading state
    document.getElementById('moduleSelectionArea').style.display = 'block';
    document.getElementById('modulesList').innerHTML = '<div class="col-12 text-center"><i class="bi bi-spinner-border"></i> Loading modules...</div>';
    
    // Fetch modules for the selected program
    fetch(`/api/programs/${programId}/modules`, {
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.modules) {
            displayModules(data.modules);
        } else {
            document.getElementById('modulesList').innerHTML = '<div class="col-12"><div class="alert alert-warning">No modules found for this program.</div></div>';
        }
    })
    .catch(error => {
        console.error('Error loading modules:', error);
        document.getElementById('modulesList').innerHTML = '<div class="col-12"><div class="alert alert-danger">Error loading modules. Please try again.</div></div>';
    });
}

function displayModules(modules) {
    const modulesList = document.getElementById('modulesList');
    let html = '';
    
    modules.forEach(module => {
        html += `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 module-card" data-module-id="${module.module_id}">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input module-checkbox" type="checkbox" 
                                   id="module_${module.module_id}" 
                                   value="${module.module_id}"
                                   onchange="toggleModule(${module.module_id}, '${module.module_name}')">
                            <label class="form-check-label fw-bold" for="module_${module.module_id}">
                                ${module.module_name}
                            </label>
                        </div>
                        <p class="card-text small text-muted">${module.module_description || 'No description available'}</p>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="viewModule(${module.module_id}, '${module.module_name}')">
                            <i class="bi bi-eye me-1"></i>View Content
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    modulesList.innerHTML = html;
}

function toggleModule(moduleId, moduleName) {
    const checkbox = document.getElementById(`module_${moduleId}`);
    
    if (checkbox.checked) {
        if (!selectedModules.includes(moduleId)) {
            selectedModules.push(moduleId);
        }
    } else {
        selectedModules = selectedModules.filter(id => id !== moduleId);
    }
    
    updateModulesSummary();
    checkAllModulesSelected();
    
    // Update hidden input
    document.getElementById('selected_modules').value = JSON.stringify(selectedModules);
}

function updateModulesSummary() {
    document.getElementById('selectedModulesCount').textContent = selectedModules.length;
}

function checkAllModulesSelected() {
    const totalModules = document.querySelectorAll('.module-checkbox').length;
    const warningDiv = document.getElementById('allModulesWarning');
    
    if (selectedModules.length === totalModules && totalModules > 0) {
        warningDiv.style.display = 'block';
    } else {
        warningDiv.style.display = 'none';
    }
}

function viewModule(moduleId, moduleName) {
    // Show modal with module content (implement as needed)
    alert(`Viewing module: ${moduleName}\n\nModule content would be displayed here.`);
}

function showFullEnrollmentSuggestion() {
    if (confirm('Would you like to switch to full enrollment for better value?\n\nThis will take you to the full enrollment page.')) {
        window.location.href = '{{ route("enrollment.full") }}';
    }
}
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
    
    // Control start date field visibility based on learning mode
    const startDateField = document.querySelector('.form-group:has(#start_date_input)');
    if (startDateField) {
        if (mode === 'synchronous') {
            // Hide start date for synchronous mode
            startDateField.style.display = 'none';
            // Clear the start date value since system will control it
            const startDateInput = document.getElementById('start_date_input');
            if (startDateInput) {
                startDateInput.value = '';
                startDateInput.removeAttribute('required');
            }
        } else if (mode === 'asynchronous') {
            // Show start date for asynchronous mode
            startDateField.style.display = 'block';
            // Make start date required for asynchronous mode
            const startDateInput = document.getElementById('start_date_input');
            if (startDateInput) {
                startDateInput.setAttribute('required', 'required');
            }
        }
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
        // Validate module selection for modular enrollment
        if (!selectedProgramId) {
            showWarning('Please select a program first.');
            return;
        }
        if (selectedModules.length === 0) {
            showWarning('Please select at least one module before proceeding.');
            return;
        }
        
        // Store selections
        document.getElementById('packageIdInput').value = selectedPackageId;
        document.getElementById('hidden_program_id').value = selectedProgramId;
        document.getElementById('selected_modules').value = JSON.stringify(selectedModules);
        
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
async function loadBatchesForProgram(programId = null) {
    const programSelect = document.getElementById('programSelect');
    const learningMode = document.getElementById('learning_mode')?.value;
    const batchContainer = document.getElementById('batchSelectionContainer');
    const batchOptions = document.getElementById('batchOptions');
    
    console.log('=== loadBatchesForProgram called ===');
    console.log('Program select element:', programSelect);
    console.log('Program selected:', programId || programSelect?.value);
    console.log('Learning mode:', learningMode);
    console.log('Batch container found:', !!batchContainer);
    console.log('Batch options found:', !!batchOptions);
    
    const selectedProgramId = programId || (programSelect ? programSelect.value : null);
    
    // Only show batches for synchronous mode and when a program is selected
    if (!selectedProgramId || learningMode !== 'synchronous') {
        console.log('Hiding batch container - conditions not met');
        console.log('- Program selected:', !!selectedProgramId);
        console.log('- Learning mode is synchronous:', learningMode === 'synchronous');
        
        if (batchContainer) {
            batchContainer.style.display = 'none';
            console.log('Batch container hidden');
        }
        return;
    }
    
    console.log('Loading batches for program ID:', selectedProgramId);
    
    try {
        // Show batch container and loading state
        if (batchContainer) {
            batchContainer.style.display = 'block';
            console.log('Batch container shown');
        }
        if (batchOptions) {
            batchOptions.innerHTML = '<div class="batch-loading" style="text-align:center; padding:20px; color:#666;"><i class="bi bi-hourglass-split"></i> Loading batches...</div>';
        }
        
        const url = `/batches/by-program?program_id=${selectedProgramId}`;
        console.log('Fetching from URL:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                               document.querySelector('input[name="_token"]')?.value || ''
            }
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (!response.ok) {
            throw new Error(`Failed to fetch batches: ${response.status} ${response.statusText}`);
        }
        
        const response_data = await response.json();
        console.log('Fetched response:', response_data);
        
        // Handle both array (direct batches) and object (with batches property) formats
        let batches = [];
        let auto_create = false;
        let message = '';
        
        if (Array.isArray(response_data)) {
            // Direct array of batches
            batches = response_data;
        } else if (response_data && typeof response_data === 'object') {
            // Object with batches property
            batches = response_data.batches || [];
            auto_create = response_data.auto_create || false;
            message = response_data.message || '';
        }
        
        console.log('Processed batches:', batches);
        console.log('Batches count:', batches.length);
        
        if (batchOptions) {
            if (batches.length === 0 || auto_create) {
                batchOptions.innerHTML = `
                    <div class="no-batches-info" style="text-align:center; padding:20px; background:#e8f4fd; border:2px solid #0066cc; border-radius:12px; margin:10px 0;">
                        <div style="margin-bottom:15px;">
                            <i class="fas fa-magic" style="font-size:2.5rem; color:#0066cc; margin-bottom:10px;"></i>
                        </div>
                        <h5 style="margin:0 0 10px 0; color:#0066cc; font-weight:600;">Auto-Batch Creation Enabled</h5>
                        <p style="margin:0; color:#004499; font-weight:500;">No active batches available for this program.</p>
                        <div style="background:#ffffff; padding:15px; border-radius:8px; margin:15px 0; border-left:4px solid #0066cc;">
                            <p style="margin:0; font-size:0.95rem; color:#333;">
                                <strong><i class="fas fa-sparkles"></i> Good news!</strong> You can continue with your registration. 
                                Our system will automatically create a new batch for you with <span style="color:#0066cc; font-weight:600;">'pending'</span> status.
                            </p>
                            <p style="margin:8px 0 0 0; font-size:0.9rem; color:#666;">
                                <i class="fas fa-clock"></i> An admin will review and activate your batch, then notify you when it's ready to start.
                            </p>
                        </div>
                        <div style="font-size:0.85rem; color:#666; margin-top:10px;">
                            <i class="fas fa-info-circle"></i> This ensures you don't miss enrollment opportunities!
                        </div>
                    </div>
                `;
            } else {
                batchOptions.innerHTML = batches.map(batch => {
                    const availableSlots = batch.available_slots || (batch.max_capacity - batch.current_capacity);
                    const isNearFull = availableSlots <= 3 && availableSlots > 0;
                    const isFull = availableSlots <= 0;
                    
                    // Enhanced status detection
                    const batchStatus = (batch.batch_status || '').toLowerCase();
                    const isOngoing = batchStatus === 'ongoing' || batch.is_ongoing;
                    const isAvailable = batchStatus === 'available' || batchStatus === 'active' || batchStatus === 'open';
                    const isPending = batchStatus === 'pending';
                    const isClosed = batchStatus === 'closed' || batchStatus === 'completed' || isFull;
                    
                    let statusText = '';
                    let statusClass = '';
                    let canEnroll = true;
                    
                    if (isClosed || isFull) {
                        statusText = 'Closed (Full)';
                        statusClass = 'status-closed';
                        canEnroll = false;
                    } else if (isOngoing && availableSlots > 0) {
                        statusText = `Ongoing - ${availableSlots} slots available`;
                        statusClass = 'status-ongoing';
                        canEnroll = true;
                    } else if (isPending) {
                        statusText = `Pending (${availableSlots} slots reserved)`;
                        statusClass = 'status-pending';
                        canEnroll = false;
                    } else if (isAvailable || availableSlots > 0) {
                        if (isNearFull) {
                            statusText = `Available (${availableSlots} slots left)`;
                            statusClass = 'status-limited';
                        } else {
                            statusText = `Available (${availableSlots} slots)`;
                            statusClass = 'status-available';
                        }
                        canEnroll = true;
                    } else {
                        statusText = 'Not Available';
                        statusClass = 'status-closed';
                        canEnroll = false;
                    }
                    
                    console.log(`Batch ${batch.batch_name}:`, {
                        batch_status: batch.batch_status,
                        batchStatus,
                        isOngoing,
                        isAvailable,
                        isPending,
                        isClosed,
                        isFull,
                        availableSlots,
                        canEnroll,
                        statusText
                    });
                    
                    const ongoingBadge = isOngoing ? 
                        `<div class="ongoing-badge">
                            <i class="bi bi-play-circle me-1"></i>
                            Started ${batch.days_started || 0} days ago
                        </div>` : '';
                    
                    return `
                        <div class="batch-option ${!canEnroll ? 'disabled' : ''}" 
                             onclick="${!canEnroll ? '' : `selectBatch(${batch.batch_id}, '${batch.batch_name}', '${batch.batch_status}')`}" 
                             data-batch-id="${batch.batch_id}"
                             data-batch-status="${batch.batch_status}">
                            <div class="batch-header">
                                <div class="batch-name">
                                    ${batch.batch_name}
                                </div>
                                <div class="batch-status ${statusClass}">
                                    ${statusText} ${isOngoing ? '<span class="status-label">STATUS</span>' : ''}
                                </div>
                            </div>
                            <div class="batch-details">
                                <div class="batch-info">
                                    <span class="batch-schedule">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        ${isOngoing ? 'Started' : 'Starts'}: ${batch.start_date}
                                    </span>
                                    <span class="batch-capacity">
                                        <i class="bi bi-people me-1"></i>
                                        Students: ${batch.current_capacity}/${batch.max_capacity}
                                    </span>
                                    ${batch.end_date ? `
                                        <span class="batch-end-date">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            Ends: ${batch.end_date}
                                        </span>
                                    ` : ''}
                                </div>
                                ${ongoingBadge}
                                ${isOngoing ? `
                                    <div class="ongoing-warning">
                                        <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                                        This batch has already started - you can still join but will need to catch up
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                }).join('');
                
                // Add enhanced styling for batch options
                const style = document.createElement('style');
                style.textContent = `
                    .batch-option {
                        border: 2px solid #e9ecef;
                        border-radius: 8px;
                        padding: 15px;
                        margin-bottom: 10px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        background: #ffffff;
                    }
                    .batch-option:hover:not(.disabled) {
                        border-color: #0d6efd;
                        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
                        transform: translateY(-1px);
                    }
                    .batch-option.selected {
                        border-color: #0d6efd;
                        background: #f8f9ff;
                        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
                    }
                    .batch-option.disabled {
                        opacity: 0.6;
                        cursor: not-allowed;
                        background: #f8f9fa;
                    }
                    .batch-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                        margin-bottom: 10px;
                    }
                    .batch-name {
                        font-weight: 600;
                        color: #212529;
                        font-size: 1.1rem;
                    }
                    .ongoing-indicator {
                        font-size: 0.75rem;
                        color: #dc3545;
                        font-weight: 700;
                        margin-left: 8px;
                    }
                    .status-label {
                        font-size: 0.7rem;
                        font-weight: 600;
                        margin-left: 6px;
                        padding: 2px 4px;
                        background: rgba(255, 255, 255, 0.3);
                        border-radius: 3px;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }
                    .batch-status {
                        padding: 4px 8px;
                        border-radius: 4px;
                        font-size: 0.85rem;
                        font-weight: 500;
                    }
                    .status-available {
                        background: #d1edff;
                        color: #084298;
                    }
                    .status-ongoing {
                        background: #fff3cd;
                        color: #664d03;
                    }
                    .status-limited {
                        background: #f8d7da;
                        color: #721c24;
                    }
                    .status-closed {
                        background: #f1f3f4;
                        color: #6c757d;
                    }
                    .batch-info {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 15px;
                        font-size: 0.9rem;
                        color: #6c757d;
                    }
                    .ongoing-badge {
                        margin-top: 8px;
                        padding: 6px 10px;
                        background: #fff3cd;
                        border: 1px solid #ffeaa7;
                        border-radius: 4px;
                        font-size: 0.85rem;
                        color: #664d03;
                        display: inline-block;
                    }
                    .ongoing-warning {
                        margin-top: 8px;
                        padding: 8px 10px;
                        background: #fff3cd;
                        border-left: 4px solid #ffc107;
                        border-radius: 4px;
                        font-size: 0.85rem;
                        color: #664d03;
                    }
                `;
                document.head.appendChild(style);
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
    
    // Check if batch is ongoing and show warning modal
    if (batchStatus === 'ongoing') {
        showOngoingBatchModal(batchId, batchName);
        return;
    }
    
    // Proceed with normal selection
    confirmBatchSelection(batchId, batchName);
}

// Function to show ongoing batch warning modal
function showOngoingBatchModal(batchId, batchName) {
    const modalHtml = `
        <div class="modal fade" id="ongoingBatchModal" tabindex="-1" aria-labelledby="ongoingBatchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="ongoingBatchModalLabel">
                            <i class="bi bi-exclamation-triangle me-2"></i>Ongoing Batch Warning
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning mb-4">
                            <h6 class="alert-heading">
                                <i class="bi bi-clock-history me-2"></i>The batch "${batchName}" has already started
                            </h6>
                            <p class="mb-0">You can still join, but you'll need to catch up with ongoing activities.</p>
                        </div>
                        
                        <h6 class="mb-3">What this means for you:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-warning mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title text-warning">
                                            <i class="bi bi-book me-2"></i>Course Content
                                        </h6>
                                        <ul class="list-unstyled mb-0 small">
                                            <li><i class="bi bi-check2 me-2 text-success"></i>Access to all course materials</li>
                                            <li><i class="bi bi-check2 me-2 text-success"></i>Previous recorded sessions</li>
                                            <li><i class="bi bi-arrow-clockwise me-2 text-info"></i>Start from the beginning</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-danger mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title text-danger">
                                            <i class="bi bi-calendar-x me-2"></i>Assignments & Deadlines
                                        </h6>
                                        <ul class="list-unstyled mb-0 small">
                                            <li><i class="bi bi-exclamation-circle me-2 text-warning"></i>Some deadlines may have passed</li>
                                            <li><i class="bi bi-clock me-2 text-info"></i>Catch up on current activities</li>
                                            <li><i class="bi bi-calendar-plus me-2 text-success"></i>Join upcoming assignments</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-warning" onclick="confirmOngoingBatch(${batchId}, '${batchName}')">
                            <i class="bi bi-check-circle me-2"></i>I Understand, Join Anyway
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('ongoingBatchModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('ongoingBatchModal'));
    modal.show();
}

// Function to confirm ongoing batch selection
function confirmOngoingBatch(batchId, batchName) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('ongoingBatchModal'));
    modal.hide();
    
    // Remove modal from DOM after hiding
    setTimeout(() => {
        const modalElement = document.getElementById('ongoingBatchModal');
        if (modalElement) {
            modalElement.remove();
        }
    }, 300);
    
    // Proceed with batch selection
    confirmBatchSelection(batchId, batchName);
}

// Function to actually select and confirm the batch
function confirmBatchSelection(batchId, batchName) {
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
    console.log('Copying account data to student form...');
    
    if (isUserLoggedIn) {
        // If user is logged in, use session data for step 4 fields
        const firstnameField = document.querySelector('input[name="firstname"]');
        const lastnameField = document.querySelector('input[name="lastname"]');
        const emailField = document.querySelector('input[name="email"]');
        
        if (firstnameField && loggedInUserFirstname) {
            firstnameField.value = loggedInUserFirstname;
            console.log('Filled firstname from session:', loggedInUserFirstname);
        }
        if (lastnameField && loggedInUserLastname) {
            lastnameField.value = loggedInUserLastname;
            console.log('Filled lastname from session:', loggedInUserLastname);
        }
        if (emailField && loggedInUserEmail) {
            emailField.value = loggedInUserEmail;
            console.log('Filled email from session:', loggedInUserEmail);
        }
    } else {
        // Copy from step 3 account registration to step 4 student form
        const step3Fields = {
            'user_firstname': 'firstname',
            'user_lastname': 'lastname', 
            'user_email': 'email'
        };
        
        Object.keys(step3Fields).forEach(step3Field => {
            const step4Field = step3Fields[step3Field];
            const sourceField = document.querySelector(`input[name="${step3Field}"]`);
            const targetField = document.querySelector(`input[name="${step4Field}"]`);
            
            if (sourceField && targetField && sourceField.value) {
                targetField.value = sourceField.value;
                console.log(`Copied ${step3Field} -> ${step4Field}: ${sourceField.value}`);
            }
        });
    }
}

// Helper function to fill logged in user data
function fillLoggedInUserData() {
    if (isUserLoggedIn) {
        // Fetch comprehensive user data from server
        fetch('/registration/user-prefill-data', {
            method: 'GET',
            credentials: 'same-origin',               // ← send Laravel session cookie
            headers: {
                'Accept': 'application/json',
                
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Fill all available fields with fetched data
                Object.keys(data.data).forEach(fieldName => {
                    const value = data.data[fieldName];
                    if (value) {
                        // Try different input selectors
                        let field = document.querySelector(`input[name="${fieldName}"]`) ||
                                   document.querySelector(`select[name="${fieldName}"]`) ||
                                   document.querySelector(`textarea[name="${fieldName}"]`);
                        
                        if (field) {
                            if (field.type === 'checkbox') {
                                field.checked = value == '1' || value === true;
                            } else {
                                field.value = value;
                            }
                        }
                    }
                });
                
                console.log('Filled user data:', data.data);
            } else if (!data.success && data.message === 'Not logged in') {
                // User not logged in - this is normal, just use fallback data
                console.log('User not logged in, using session fallback data');
                // Use the session data we already have
                const firstnameField = document.querySelector('input[name="firstname"]');
                const lastnameField = document.querySelector('input[name="lastname"]');
                const emailField = document.querySelector('input[name="email"]');
                
                if (firstnameField && loggedInUserFirstname) {
                    firstnameField.value = loggedInUserFirstname;
                }
                if (lastnameField && loggedInUserLastname) {
                    lastnameField.value = loggedInUserLastname;
                }
                if (emailField && loggedInUserEmail) {
                    emailField.value = loggedInUserEmail;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching user data:', error);
            // Fallback to basic data
            const firstnameField = document.querySelector('input[name="firstname"]');
            const lastnameField = document.querySelector('input[name="lastname"]');
            
            if (firstnameField && loggedInUserFirstname) {
                firstnameField.value = loggedInUserFirstname;
            }
            if (lastnameField && loggedInUserLastname) {
                lastnameField.value = loggedInUserLastname;
            }
        });
    }
}

// Enhanced file upload with OCR validation
function handleFileUpload(inputElement) {
    const fieldName = inputElement.name;
    const file = inputElement.files[0];
    
    if (!file) return;
    
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
    
    let firstName = loggedInUserFirstname || '';
    let lastName = loggedInUserLastname || '';
    
    // Try to find first name if not already set
    if (!firstName) {
        for (const selector of firstNameSelectors) {
            const element = document.querySelector(selector);
            if (element && element.value.trim()) {
                firstName = element.value.trim();
                break;
            }
        }
    }
    
    // Try to find last name if not already set
    if (!lastName) {
        for (const selector of lastNameSelectors) {
            const element = document.querySelector(selector);
            if (element && element.value.trim()) {
                lastName = element.value.trim();
                break;
            }
        }
    }
    
    console.log('Found names:', firstName, lastName);
    
    if (!firstName || !lastName) {
        // Try harder to find the names by checking if we're in step 4 and can copy from step 3
        if (currentStep >= 3) {
            const step3FirstName = document.querySelector('input[name="user_firstname"]')?.value?.trim();
            const step3LastName = document.querySelector('input[name="user_lastname"]')?.value?.trim();
            
            if (step3FirstName && !firstName) firstName = step3FirstName;
            if (step3LastName && !lastName) lastName = step3LastName;
        }
    }
    
    console.log('Names after fallback check:', firstName, lastName);
    
    if (!firstName || !lastName) {
        showErrorModal('Please enter your first name and last name in the form before uploading documents.');
        inputElement.value = '';
        return;
    }
    
    // Show loading indicator
    showLoadingModal('Validating document...');
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('field_name', fieldName);
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    
    fetch(VALIDATE_URL, {
        method: 'POST',
        credentials: 'same-origin',    // ensure Laravel session cookie is sent
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN, // use the constant defined in your blade
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
        
        console.log('File validation response:', data); // Debug log
        
        if (data.success) {
            showSuccessModal('Document validated successfully!');
            
            if (data.suggestions && data.suggestions.length > 0) {
                showProgramSuggestions(data.suggestions);
            }
            
            if (data.certificate_level) {
                handleEducationLevelDetection(data.certificate_level);
            }
        } else {
            console.error('File validation failed:', data);
            showErrorModal(data.message || 'File validation failed');
            inputElement.value = '';
        }
    })
    .catch(error => {
        closeLoadingModal();
        console.error('File validation error:', error);
        showErrorModal('Network error occurred. Please check your connection and try again.');
        inputElement.value = '';
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

// Handle education level detection
function handleEducationLevelDetection(level) {
    const educationLevelSelect = document.getElementById('educationLevel');
    if (educationLevelSelect) {
        if (level === 'graduate') {
            educationLevelSelect.value = 'Graduate';
        } else if (level === 'undergraduate') {
            educationLevelSelect.value = 'Undergraduate';
        }
        
        // Trigger change event to show/hide graduation certificate field
        if (educationLevelSelect.onchange) {
            educationLevelSelect.onchange();
        }
    }
}

// Modal functions
function showLoadingModal(message) {
    let modal = document.getElementById('loadingModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'loadingModal';
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="loading-spinner"></div>
                <p id="loadingMessage">${message}</p>
            </div>
        `;
        document.body.appendChild(modal);
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

function showModal(title, message, type) {
    let modal = document.getElementById('customModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'customModal';
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modalTitle">${title}</h5>
                    <button type="button" onclick="closeModal()" class="close-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <p id="modalMessage">${message}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-primary">OK</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    
    const modalContent = modal.querySelector('.modal-content');
    modalContent.className = `modal-content modal-${type}`;
    
    modal.style.display = 'flex';
}

function closeModal() {
    const modal = document.getElementById('customModal');
    if (modal) modal.style.display = 'none';
}

// Toggle education level requirements based on selection
function toggleEducationLevelRequirements() {
    const educationLevel = document.getElementById('educationLevel');
    const selectedOption = educationLevel.options[educationLevel.selectedIndex];
    const requirementsContainer = document.getElementById('educationLevelRequirements');
    
    console.log('Education level changed to:', educationLevel.value);
    
    // Clear existing requirements
    requirementsContainer.innerHTML = '';
    requirementsContainer.style.display = 'none';
    
    if (educationLevel.value && selectedOption.dataset.fileRequirements) {
        try {
            const fileRequirements = JSON.parse(selectedOption.dataset.fileRequirements);
            console.log('File requirements:', fileRequirements);
            console.log('File requirements type:', typeof fileRequirements);
            console.log('Is array:', Array.isArray(fileRequirements));
            
            if (fileRequirements && (Array.isArray(fileRequirements) ? fileRequirements.length > 0 : Object.keys(fileRequirements).length > 0)) {
                requirementsContainer.style.display = 'block';
                
                // Handle both array and object formats
                let requirementsArray = fileRequirements;
                if (!Array.isArray(fileRequirements)) {
                    // Convert object to array format
                    requirementsArray = Object.entries(fileRequirements).map(([fieldName, config]) => ({
                        field_name: fieldName.replace(/\s+/g, '_'),
                        display_name: fieldName,
                        is_required: config.required !== undefined ? config.required : true,
                        file_type: config.file_type || config.type || 'any',
                        description: config.description || ''
                    }));
                }
                
                console.log('Final requirements array:', requirementsArray);
                
                requirementsArray.forEach(requirement => {
                    const fieldDiv = document.createElement('div');
                    fieldDiv.className = 'form-group mb-3';
                    
                    const fieldName = requirement.field_name || requirement.display_name || 'Unknown';
                    const displayName = requirement.display_name || requirement.field_name || 'Unknown';
                    const isRequired = requirement.is_required !== undefined ? requirement.is_required : true;
                    const fileType = requirement.file_type || 'any';
                    
                    // Set appropriate file accept types based on education level configuration
                    let acceptTypes = '.jpg,.jpeg,.png,.pdf';
                    if (fileType === 'image') {
                        acceptTypes = '.jpg,.jpeg,.png,.gif';
                    } else if (fileType === 'pdf') {
                        acceptTypes = '.pdf';
                    } else if (fileType === 'document') {
                        acceptTypes = '.pdf,.doc,.docx';
                    }
                    
                    fieldDiv.innerHTML = `
                        <label for="${fieldName}" style="font-weight:700;">
                            <i class="bi bi-file-earmark-arrow-up me-2"></i>${displayName}
                            ${isRequired ? '<span class="required">*</span>' : ''}
                        </label>
                        <input type="file" name="${fieldName}" id="${fieldName}" 
                               class="form-control" accept="${acceptTypes}" 
                               onchange="handleFileUpload(this)" ${isRequired ? 'required' : ''}>
                        <small class="form-text text-muted">
                            Upload your ${displayName} 
                            ${requirement.description ? ' - ' + requirement.description : ''}
                        </small>
                    `;
                    requirementsContainer.appendChild(fieldDiv);
                });
            }
        } catch (error) {
            console.error('Error parsing file requirements:', error);
        }
    }
}

// Legacy function for backward compatibility
function toggleGraduationCertificate() {
    toggleEducationLevelRequirements();
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
            <input type="hidden" name="enrollment_type" value="Modular">
            <input type="hidden" name="package_id" value="" id="packageIdInput">
            <input type="hidden" name="program_id" value="" id="hidden_program_id">
            <input type="hidden" name="plan_id" value="1">
            <input type="hidden" name="learning_mode" id="learning_mode" value="">
            <input type="hidden" name="Start_Date" id="hidden_start_date" value="">
            <input type="hidden" name="selected_modules" id="selected_modules" value="">
            
             <!-- Step 1 -->
            <div class="step active" id="step-1">
                <div class="step-header">
                    <h2>
                        <i class="bi bi-puzzle me-2"></i>
                        Choose Your Modules
                    </h2>
                    <p>First select a program, then choose the specific modules you want to study.</p>
                </div>

                <!-- Program Selection -->
                <div class="mb-4">
                    <label for="program_select" class="form-label fw-bold">
                        <i class="bi bi-mortarboard me-2"></i>Select Program
                    </label>
                    <select class="form-select form-select-lg" id="program_select" onchange="loadModules(this.value)">
                        <option value="">Choose a program first...</option>
                        @foreach($packages as $package)
                            @if($package->program)
                                <option value="{{ $package->program->program_id }}" data-package-id="{{ $package->package_id }}" data-package-name="{{ $package->package_name }}" data-amount="{{ $package->amount }}">
                                    {{ $package->program->program_name }} - {{ $package->package_name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Module Selection Area -->
                <div id="moduleSelectionArea" style="display: none;">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-list-check me-2"></i>Available Modules
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row" id="modulesList">
                                <!-- Modules will be loaded here -->
                            </div>
                            
                            <!-- Warning if all modules selected -->
                            <div id="allModulesWarning" class="alert alert-warning mt-3" style="display: none;">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Consider Full Enrollment:</strong> You've selected all available modules. 
                                <a href="#" onclick="showFullEnrollmentSuggestion()" class="alert-link">
                                    Switch to full enrollment for better value
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="selected-summary mt-3 mb-4" style="text-align:left;">
                    <div class="row">
                        <div class="col-md-6">
                            <span class="ms-3">
                                <i class="bi bi-mortarboard-fill text-success"></i>
                                Selected Program: <strong id="selectedProgramName">None</strong>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <span>
                                <i class="bi bi-puzzle-fill text-info"></i>
                                Selected Modules: <strong id="selectedModulesCount">0</strong>
                            </span>
                        </div>
                    </div>
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
        </div>
    </div>

    <!-- Step 2: Learning Mode Selection -->
    <div class="step" id="step-2">
        <div class="white-step-container">
            <div class="step-header">
                <h2><i class="bi bi-mortarboard me-2"></i>Choose Learning Mode</h2>
                <p>Select how you'd like to take your classes.</p>
            </div>

            <div class="learning-modes horizontal">
                @if(isset($fullPlan) && $fullPlan->enable_synchronous)
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
                @endif
                
                @if(isset($fullPlan) && $fullPlan->enable_asynchronous)
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
                @endif
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
                                       class="form-control" accept=".jpg,.jpeg,.png,.pdf" 
                                       onchange="handleFileUpload(this)" {{ $field->is_required ? 'required' : '' }}>
                                <small class="form-text text-muted">Upload {{ $field->field_label ?: $field->field_name }} (JPG, PNG, PDF only)</small>
                            @elseif($field->field_type === 'select')
                                <select name="{{ $field->field_name }}" id="{{ $field->field_name }}" 
                                        class="form-select" {{ $field->is_required ? 'required' : '' }}>
                                    <option value="">Select {{ $field->field_label ?: $field->field_name }}</option>
                                    @if($field->field_options)
                                        @php
                                            $options = is_string($field->field_options) ? json_decode($field->field_options, true) : $field->field_options;
                                        @endphp
                                        @if(is_array($options))
                                            @foreach($options as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        @endif
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
        <div id="educationLevelRequirements" style="display: none;">
            <!-- File requirements will be dynamically added here -->
        </div>

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
            <input class="form-check-input" type="checkbox" id="termsCheckbox" required>
            <label class="form-check-label" for="termsCheckbox">
                I agree to the <a href="#" id="showTerms">Terms and Conditions</a>
            </label>
        </div>

        <!-- reCAPTCHA -->
        @if(env('RECAPTCHA_SITE_KEY'))
        <div class="form-group mb-4 d-flex justify-content-center">
            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
        </div>
        @endif

        <hr style="margin-bottom: 2.1rem; margin-top: 1.2rem;">

        <div class="form-navigation" style="justify-content: space-between;">
            <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2"></i> Back
            </button>
            <button type="submit" class="btn btn-success btn-lg" id="submitButton">
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
<div id="termsModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 600px;">
        <h3>Terms and Conditions</h3>
        <div style="max-height: 320px; overflow-y: auto; text-align: left; margin-bottom: 1.5rem;">
            <p>
                By registering for this platform, you agree to abide by all policies, privacy guidelines, and usage restrictions as provided by our review center. Please read the full document before accepting.
            </p>
            <!-- Add more actual terms here if you want -->
        </div>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button onclick="acceptTerms()" class="btn btn-primary">Accept</button>
            <button onclick="declineTerms()" class="btn btn-secondary">Decline</button>
        </div>
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
    
    // Enable next button only if all conditions are met INCLUDING email verification
    const allFieldsFilled = isFirstnameFilled && isLastnameFilled && isEmailFilled && isPasswordFilled && isPasswordConfirmFilled;
    const allValidationsPassed = isPasswordValid && isPasswordConfirmValid && !emailHasError && !passwordHasError && !passwordMatchHasError;
    const emailVerified = enrollmentEmailVerified; // OTP verification required
    
    console.log('Step 3 Validation:', {
        allFieldsFilled,
        allValidationsPassed,
        emailVerified,
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
        if (allFieldsFilled && allValidationsPassed && emailVerified) {
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
    console.log('=== FORM SUBMISSION STARTED ===');
    
    try {
        // Prevent default to handle validation first
        event.preventDefault();
        
        const form = event.target;
        console.log('Form element:', form);
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        
        // Comprehensive form validation
        const validationResult = validateFormBeforeSubmission(form);
        if (!validationResult.isValid) {
            console.error('Form validation failed:', validationResult.errors);
            showFormErrors(validationResult.errors);
            return false;
        }
        
        console.log('Form validation passed');
        
        // Check reCAPTCHA (only if enabled)
        if (hasRecaptcha && typeof grecaptcha !== 'undefined') {
            const recaptchaResponse = grecaptcha.getResponse();
            if (!recaptchaResponse) {
                console.error('reCAPTCHA validation failed');
                showFormErrors(['Please complete the CAPTCHA verification.']);
                return false;
            }
            console.log('reCAPTCHA validation passed');
        }
        
        // Add batch_id if selected
        const selectedBatchId = sessionStorage.getItem('selectedBatchId');
        if (selectedBatchId && selectedBatchId !== 'null' && selectedBatchId !== '') {
            let batchInput = form.querySelector('input[name="batch_id"]');
            if (!batchInput) {
                batchInput = document.createElement('input');
                batchInput.type = 'hidden';
                batchInput.name = 'batch_id';
                form.appendChild(batchInput);
            }
            batchInput.value = selectedBatchId;
            console.log('Added batch_id to form:', selectedBatchId);
        }
        
        // Add package_id from session storage if missing
        const packageIdFromSession = sessionStorage.getItem('selectedPackageId');
        if (packageIdFromSession) {
            let packageInput = form.querySelector('input[name="package_id"]');
            if (!packageInput) {
                packageInput = document.createElement('input');
                packageInput.type = 'hidden';
                packageInput.name = 'package_id';
                form.appendChild(packageInput);
            }
            packageInput.value = packageIdFromSession;
            console.log('Added package_id to form:', packageIdFromSession);
        }
        
        // Show loading state
        showFormLoading(true);
        
        // Log all form data before submission
        const formData = new FormData(form);
        console.log('=== FORM DATA BEING SUBMITTED ===');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}:`, value);
        }
        console.log('=== END FORM DATA ===');
        
        // Submit form programmatically
        console.log('Submitting form...');
        form.submit();
        
    } catch (error) {
        console.error('Error in form submission:', error);
        showFormErrors(['An unexpected error occurred. Please try again.']);
        showFormLoading(false);
        return false;
    }
}

// Comprehensive form validation function - checks only existing fields
function validateFormBeforeSubmission(form) {
    const errors = [];
    
    // Get all required fields dynamically from the form
    const requiredInputs = form.querySelectorAll('[required]');
    
    // Check each required field
    requiredInputs.forEach(input => {
        const fieldName = input.name;
        const fieldLabel = input.getAttribute('data-label') || 
                          input.previousElementSibling?.textContent?.replace('*', '').trim() ||
                          fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace('_', ' ');
        
        if (!input.value || input.value.trim() === '') {
            errors.push(`${fieldLabel} is required.`);
        }
    });
    
    // Additional validation for specific field types
    const emailInput = form.querySelector('[name="email"]');
    if (emailInput && emailInput.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value)) {
            errors.push('Please enter a valid email address.');
        }
    }
    
    // Check contact number format if field exists and has value
    const contactInput = form.querySelector('[name="contact_number"]');
    if (contactInput && contactInput.value) {
        const contactRegex = /^[0-9+\-\s()]+$/;
        if (!contactRegex.test(contactInput.value)) {
            errors.push('Please enter a valid contact number.');
        }
    }
    
    // Check batch selection for synchronous mode - only if batches are available
    const learningMode = form.querySelector('[name="learning_mode"]')?.value;
    const batchId = sessionStorage.getItem('selectedBatchId') || form.querySelector('[name="batch_id"]')?.value;
    
    if (learningMode === 'synchronous') {
        // Check if batch container is visible and has batch options
        const batchContainer = document.getElementById('batchSelectionContainer');
        const batchOptions = document.getElementById('batchOptions');
        const hasBatchOptions = batchOptions && batchOptions.querySelector('.batch-option');
        const hasNoBatchesInfo = batchOptions && batchOptions.querySelector('.no-batches-info');
        
        // Only require batch selection if there are actual batch options available
        if (batchContainer && batchContainer.style.display !== 'none' && hasBatchOptions && !hasNoBatchesInfo) {
            if (!batchId || batchId === 'null' || batchId === '') {
                errors.push('Please select a batch for synchronous learning mode.');
            }
        }
        // If no batches available (auto-create mode), allow registration without batch selection
        console.log('Batch validation check:', {
            learningMode,
            batchContainerVisible: batchContainer && batchContainer.style.display !== 'none',
            hasBatchOptions: !!hasBatchOptions,
            hasNoBatchesInfo: !!hasNoBatchesInfo,
            batchId: batchId
        });
    }
    
    // Check terms acceptance
    const termsCheckbox = form.querySelector('[name="terms_accepted"]');
    if (termsCheckbox && !termsCheckbox.checked) {
        errors.push('Please accept the terms and conditions.');
    }
    
    return {
        isValid: errors.length === 0,
        errors: errors
    };
}

// Function to show form errors
function showFormErrors(errors) {
    // Remove existing error display
    const existingErrorContainer = document.getElementById('formErrorContainer');
    if (existingErrorContainer) {
        existingErrorContainer.remove();
    }
    
    // Create error container
    const errorContainer = document.createElement('div');
    errorContainer.id = 'formErrorContainer';
    errorContainer.className = 'alert alert-danger mt-3';
    errorContainer.innerHTML = `
        <h6><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
        <ul class="mb-0">
            ${errors.map(error => `<li>${error}</li>`).join('')}
        </ul>
    `;
    
    // Insert error container before the submit button
    const submitButton = document.getElementById('submitButton');
    if (submitButton) {
        submitButton.parentNode.insertBefore(errorContainer, submitButton);
        
        // Scroll to error container
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Function to show/hide loading state
function showFormLoading(loading) {
    const submitButton = document.getElementById('submitButton');
    if (!submitButton) return;
    
    if (loading) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Submitting Registration...';
        submitButton.classList.add('btn-secondary');
        submitButton.classList.remove('btn-success');
    } else {
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="bi bi-check-circle me-2"></i>Complete Registration';
        submitButton.classList.remove('btn-secondary');
        submitButton.classList.add('btn-success');
    }
}

 document.addEventListener('DOMContentLoaded', function() {
        // Show modal when link is clicked
        const termsLink = document.getElementById('showTerms');
        if (termsLink) {
            termsLink.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('termsModal').classList.add('show');
            });
        }
    });

    function closeTermsModal() {
        document.getElementById('termsModal').classList.remove('show');
    }

function showTermsModal() {
    const modal = document.getElementById('termsModal');
    if (!modal) return;
    // show the modal
    modal.classList.add('show');
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
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }, 300); // match your CSS transition-duration (0.3s)
}

// OTP Functions for Step 3 Account Registration
let enrollmentEmailVerified = false;

async function sendEnrollmentOTP() {
    const email = document.getElementById('user_email').value;
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    
    if (!email) {
        alert('Please enter your email address first.');
        return;
    }

    sendOtpBtn.disabled = true;
    sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    try {
        const response = await fetch('{{ route("signup.send.otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ email: email })
        });

        const data = await response.json();
        
        if (data.success) {
            // Show the modal instead of inline container
            document.getElementById('otpTargetEmail').textContent = email;
            var otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
            otpModal.show();
            
            sendOtpBtn.innerHTML = '<i class="fas fa-redo"></i> Resend OTP';
            showEnrollmentMessage('OTP sent successfully to your email!', 'success');
            
            // Auto focus on first OTP input when modal is shown
            document.getElementById('otpModal').addEventListener('shown.bs.modal', function () {
                document.querySelector('.otp-digit').focus();
            });
        } else {
            showEnrollmentMessage(data.message, 'error');
            sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
        }
    } catch (error) {
        showEnrollmentMessage('Failed to send OTP. Please try again.', 'error');
        sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
    }
    
    sendOtpBtn.disabled = false;
}

async function verifyEnrollmentOTP() {
    const otp = document.getElementById('otp_code').value;
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    
    if (!otp || otp.length !== 6) {
        alert('Please enter a valid 6-digit OTP.');
        return;
    }

    verifyOtpBtn.disabled = true;
    verifyOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

    try {
        const response = await fetch('{{ route("signup.verify.otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ otp: otp })
        });

        const data = await response.json();
        
        if (data.success) {
            enrollmentEmailVerified = true;
            document.getElementById('user_email').readOnly = true;
            
            verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle"></i> ✓ Verified';
            verifyOtpBtn.classList.add('verified');
            
            // Update step indicator
            const stepIndicator = document.querySelector('.step-indicator');
            if (stepIndicator) {
                stepIndicator.classList.add('completed');
                stepIndicator.innerHTML = '✓';
            }
            
            showEnrollmentMessage('Email verified successfully!', 'success');
            
            // Enable the next button if all validations pass
            validateStep3();
        } else {
            showEnrollmentMessage(data.message, 'error');
            verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify Code';
        }
    } catch (error) {
        showEnrollmentMessage('Failed to verify OTP. Please try again.', 'error');
        verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify Code';
    }
    
    verifyOtpBtn.disabled = false;
}

async function verifyEnrollmentOTPModal() {
    // Get OTP from the hidden input that combines all digits
    const otp = document.getElementById('otp_code_modal').value;
    const verifyOtpBtn = document.getElementById('verifyOtpBtnModal');
    
    if (!otp || otp.length !== 6) {
        const statusElement = document.getElementById('otpStatusModal');
        if (statusElement) {
            statusElement.textContent = 'Please enter a valid 6-digit OTP.';
            statusElement.className = 'status-message status-error';
            statusElement.style.display = 'block';
        }
        return;
    }

    verifyOtpBtn.disabled = true;
    verifyOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

    try {
        const response = await fetch('{{ route("signup.verify.otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ otp: otp })
        });

        const data = await response.json();
        
        if (data.success) {
            enrollmentEmailVerified = true;
            document.getElementById('user_email').readOnly = true;
            
            // Update button to show success
            verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verified';
            verifyOtpBtn.classList.add('verified');
            
            // Show success message in modal
            const statusElement = document.getElementById('otpStatusModal');
            if (statusElement) {
                statusElement.textContent = 'Email verified successfully!';
                statusElement.className = 'status-message status-success';
                statusElement.style.display = 'block';
            }
            
            // Close modal after a short delay
            setTimeout(() => {
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('otpModal'));
                if (modalInstance) {
                    modalInstance.hide();
                }
            }, 1500);
            
            // Enable the next button if all validations pass
            validateStep3();
        } else {
            const statusElement = document.getElementById('otpStatusModal');
            if (statusElement) {
                statusElement.textContent = data.message || 'Failed to verify OTP. Please try again.';
                statusElement.className = 'status-message status-error';
                statusElement.style.display = 'block';
            }
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.innerHTML = 'Continue';
        }
    } catch (error) {
        console.error('OTP verification error:', error);
        const statusElement = document.getElementById('otpStatusModal');
        if (statusElement) {
            statusElement.textContent = 'Failed to verify OTP. Please try again.';
            statusElement.className = 'status-message status-error';
            statusElement.style.display = 'block';
        }
        verifyOtpBtn.disabled = false;
        verifyOtpBtn.innerHTML = 'Continue';
    }
}

function resendOTPCode() {
    // Trigger the send OTP function again
    sendEnrollmentOTP();
}

// Handle OTP digit inputs
function initializeOTPInputs() {
  const otpInputs = document.querySelectorAll('.otp-digit');
  const hiddenOtpInput = document.getElementById('otp_code_modal');
  const continueBtn     = document.getElementById('verifyOtpBtnModal');

  otpInputs.forEach((input, idx) => {
    input.addEventListener('input', () => {
      // only digits
      input.value = input.value.replace(/\D/, '');

      // move forward
      if (input.value && idx < otpInputs.length - 1) {
        otpInputs[idx+1].focus();
      }

      // combine them
      const code = Array.from(otpInputs).map(i=>i.value).join('');
      hiddenOtpInput.value = code;

      // once we've got 6 digits, try verifying silently
      if (code.length === 6) {
        // show spinner on button
        continueBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying…';

        fetch('{{ route("signup.verify.otp") }}', {
          method: 'POST',
          headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
          },
          body: JSON.stringify({ otp: code })
        })
        .then(r=>r.json())
        .then(data=>{
          if (data.success) {
            enrollmentEmailVerified = true;
            continueBtn.disabled = false;                 // now they can click Continue
            continueBtn.innerHTML = 'Continue';
            continueBtn.classList.add('verified');
            document.getElementById('otpStatusModal').textContent = 'Code OK, click Continue →';
            document.getElementById('otpStatusModal').className = 'status-message status-success';
            document.getElementById('otpStatusModal').style.display='block';
          } else {
            enrollmentEmailVerified = false;
            continueBtn.disabled = true;
            continueBtn.innerHTML = 'Continue';
            document.getElementById('otpStatusModal').textContent = data.message;
            document.getElementById('otpStatusModal').className = 'status-message status-error';
            document.getElementById('otpStatusModal').style.display='block';
          }
        })
        .catch(()=>{
          continueBtn.disabled = true;
          continueBtn.innerHTML = 'Continue';
        });
      } else {
        // less than 6→ reset state
        enrollmentEmailVerified = false;
        continueBtn.disabled = true;
        document.getElementById('otpStatusModal').style.display='none';
      }
    });

    // backspace moves you back
    input.addEventListener('keydown', e=>{
      if (e.key==='Backspace' && !input.value && idx>0) {
        otpInputs[idx-1].focus();
      }
    });

    // handle paste of full code
    input.addEventListener('paste', e=>{
      e.preventDefault();
      const txt = (e.clipboardData||window.clipboardData).getData('text')
                    .replace(/\D/g,'').slice(0,6);
      txt.split('').forEach((d,i)=>otpInputs[i].value=d);
      otpInputs[Math.min(txt.length,5)].focus();
      input.dispatchEvent(new Event('input')); // re-run the input handler
    });
  });
}


// Initialize OTP inputs when the modal is shown
document.addEventListener('DOMContentLoaded', function() {
    const otpModal = document.getElementById('otpModal');
    if (otpModal) {
        otpModal.addEventListener('shown.bs.modal', function() {
            initializeOTPInputs();
            // Clear all inputs when modal opens
            document.querySelectorAll('.otp-digit').forEach(input => input.value = '');
            document.getElementById('otp_code_modal').value = '';
            document.getElementById('otpStatusModal').style.display = 'none';
        });
    }
});

function showEnrollmentMessage(message, type) {
    // Try modal status first, fallback to regular status if modal doesn't exist
    const statusDiv = document.getElementById('otpStatusModal') || document.getElementById('otpStatus');
    
    if (statusDiv) {
        statusDiv.textContent = message;
        statusDiv.className = `status-message status-${type}`;
        statusDiv.style.display = 'block';
        
        setTimeout(() => {
            statusDiv.style.display = 'none';
        }, 5000);
    }
}

function initializeEmailValidation() {
    const emailInput = document.getElementById('user_email');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    
    if (!emailInput || !sendOtpBtn) return;
    
    let emailCheckTimeout;
    
    // Disable Send OTP initially
    sendOtpBtn.disabled = true;
    sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
    
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(emailCheckTimeout);
        
        // Reset button state
        sendOtpBtn.disabled = true;
        
        // Check if email is valid format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRegex.test(email)) {
            sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
            return;
        }
        
        // Show checking animation
        sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
        
        // Debounce email checking (wait 500ms after user stops typing)
        emailCheckTimeout = setTimeout(() => {
            checkEmailAvailability(email);
        }, 500);
    });
}

async function checkEmailAvailability(email) {
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    
    try {
        const response = await fetch('{{ route("check.email.availability") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ email: email })
        });
        
        const data = await response.json();
        
        if (data.available) {
            // Email is available, enable Send OTP
            sendOtpBtn.disabled = false;
            sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
        } else {
            // Email already exists, disable Send OTP
            sendOtpBtn.disabled = true;
            sendOtpBtn.innerHTML = '<i class="fas fa-times"></i> Email Exists';
            
            // Show error message
            const emailError = document.getElementById('emailError');
            if (emailError) {
                emailError.textContent = 'This email is already registered. Please use a different email or login.';
                emailError.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error checking email:', error);
        // On error, enable the button (fail gracefully)
        sendOtpBtn.disabled = false;
        sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, initializing registration form');
    
    // Initialize form with user data if logged in
    if (isUserLoggedIn) {
        loadUserPrefillData();
    }
    
    // Initialize stepper
    updateStepper(currentStep);
    
    // Initialize email validation for Send OTP button
    initializeEmailValidation();
    
    // Initialize terms modal event handlers
    const termsLink = document.getElementById('showTerms');
    if (termsLink) {
        termsLink.addEventListener('click', e => {
            e.preventDefault();
            showTermsModal();
        });
    }

    // Modal click outside to close
    const termsModal = document.getElementById('termsModal');
    if (termsModal) {
        termsModal.addEventListener('click', e => {
            if (e.target.id === 'termsModal') closeTermsModal();
        });
    }

    // Escape key to close modal
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeTermsModal();
    });
});

// Function to load user prefill data
async function loadUserPrefillData() {
  if (!isUserLoggedIn) {
    console.log('User not logged in, skipping prefill');
    return;
  }
  try {
    const res = await fetch(PREFILL_URL, {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN
      }
    });

    if (!res.ok) {
      console.log(`Prefill fetch failed: HTTP ${res.status}`);
      return;
    }

    const payload = await res.json();
    if (!payload.success) {
      console.log('Prefill response not successful:', payload.message);
      return;
    }

    // populate fields exactly as before…
    Object.entries(payload.data).forEach(([key, value]) => {
      const fld = document.querySelector(
        `input[name="${key}"], select[name="${key}"], textarea[name="${key}"]`
      );
      if (!fld) return;
      if (fld.type === 'checkbox') fld.checked = !!value;
      else fld.value = value;
    });

    console.log('User data prefills complete', payload.data);
  } catch (err) {
    console.error('Error loading user prefill data:', err);
  }
}


// Function to handle program selection change - reset batch selection
function onProgramSelectionChange() {
    const programSelect = document.getElementById('programSelect');
    if (!programSelect) return;
    
    const selectedProgramId = programSelect.value;
    
    // Reset batch selection when program changes
    clearBatchSelection();
    
    // Load new batches for selected program
    if (selectedProgramId) {
        loadBatchesForProgram(selectedProgramId);
    }
    
    // Update hidden program_id field
    updateHiddenProgramId();
}

// Function to clear batch selection
function clearBatchSelection() {
    console.log('Clearing batch selection');
    
    // Clear session storage
    sessionStorage.removeItem('selectedBatchId');
    sessionStorage.removeItem('selectedBatchName');
    
    // Clear UI selection
    document.querySelectorAll('.batch-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Hide selected batch display
    const selectedBatchDisplay = document.getElementById('selectedBatchDisplay');
    if (selectedBatchDisplay) {
        selectedBatchDisplay.style.display = 'none';
    }
    
    // Clear selected batch text
    const selectedBatchName = document.getElementById('selectedBatchName');
    if (selectedBatchName) {
        selectedBatchName.textContent = '';
    }
    
    console.log('Batch selection cleared');
}

// Terms and Conditions modal functions
function acceptTerms() {
    // Check the terms and conditions checkbox
    const termsCheckbox = document.getElementById('terms_conditions');
    if (termsCheckbox) {
        termsCheckbox.checked = true;
    }
    
    // Enable the submit button
    const submitButton = document.getElementById('submitRegistration');
    if (submitButton) {
        submitButton.disabled = false;
        submitButton.classList.remove('disabled');
        submitButton.style.opacity = '1';
    }
    
    // Close the modal
    const termsModal = document.getElementById('termsModal');
    if (termsModal) {
        termsModal.style.display = 'none';
    }
    
    console.log('Terms and conditions accepted');
}

function declineTerms() {
    // Uncheck the terms and conditions checkbox
    const termsCheckbox = document.getElementById('terms_conditions');
    if (termsCheckbox) {
        termsCheckbox.checked = false;
    }
    
    // Disable the submit button
    const submitButton = document.getElementById('submitRegistration');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.classList.add('disabled');
        submitButton.style.opacity = '0.5';
    }
    
    // Close the modal
    const termsModal = document.getElementById('termsModal');
    if (termsModal) {
        termsModal.style.display = 'none';
    }
    
    // Show a message to the user
    showWarning('You must accept the terms and conditions to proceed with registration.');
    
    console.log('Terms and conditions declined');
}

function closeTermsModal() {
    const termsModal = document.getElementById('termsModal');
    if (termsModal) {
        termsModal.style.display = 'none';
    }
}

</script>
@endsection
