// ===== MODULAR ENROLLMENT JAVASCRIPT =====

// Global variables
let currentStep = 1;
let totalSteps = 3; // Package Selection, Account (if not logged in), Student Registration
let selectedPackageId = null;
let selectedModules = [];
let selectedProgramId = null;
let isUserLoggedIn = false;
let loggedInUserFirstname = '';
let loggedInUserLastname = '';
let loggedInUserEmail = '';
let hasRecaptcha = false;
let isOTPVerified = false;

// Add selectPackage function for card selection
function selectPackage(packageId, packageName, packagePrice, programId) {
    // Remove selection from all package cards
    document.querySelectorAll('.package-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Highlight selected package
    if (event && event.target) {
        const card = event.target.closest('.package-card');
        if (card) card.classList.add('selected');
    }

    // Store selection in global variable
    selectedPackageId = packageId;
    selectedProgramId = programId;
    window.selectedPackageId = packageId;

    // Store package selection in session storage
    sessionStorage.setItem('selectedPackageId', packageId);
    sessionStorage.setItem('selectedPackageName', packageName);
    sessionStorage.setItem('selectedPackagePrice', packagePrice);

    // Update hidden form input
    const packageInput = document.getElementById('packageIdInput');
    if (packageInput) {
        packageInput.value = packageId;
    }

    // Update selected package display
    const selectedDisplay = document.getElementById('selectedPackageName');
    if (selectedDisplay) selectedDisplay.textContent = packageName;

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

    // Optionally, load modules for this package if needed
    if (typeof loadModulesForProgram === 'function') {
        loadModulesForProgram(packageId);
    }
}

// URL constants (these should be defined in the Blade file)
const VALIDATE_URL = '/validate-document';
const PREFILL_URL = '/get-user-prefill-data';
const MODULES_URL = '/get-program-modules';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Modular Enrollment JavaScript loaded');
    
    // Initialize user state
    initializeUserState();
    
    // Initialize form
    initializeForm();
    
    // Initialize step navigation
    initializeStepNavigation();
    
    // Initialize dynamic form validation
    initializeDynamicValidation();
    
    // Initialize OTP functionality
    initializeOTPSystem();
    
    // Initialize terms modal
    initializeTermsModal();
    
    // Initialize module selection
    initializeModuleSelection();
    
    // Load user data if logged in
    if (isUserLoggedIn) {
        loadUserPrefillData();
    }
    
    // Initialize reCAPTCHA if available
    if (typeof grecaptcha !== 'undefined') {
        hasRecaptcha = true;
    }
});

// ===== INITIALIZATION FUNCTIONS =====

function initializeUserState() {
    // Check if user is logged in (this should be set by the Blade template)
    if (typeof window.isUserLoggedIn !== 'undefined') {
        isUserLoggedIn = window.isUserLoggedIn;
    }
    
    if (typeof window.loggedInUserFirstname !== 'undefined') {
        loggedInUserFirstname = window.loggedInUserFirstname;
    }
    
    if (typeof window.loggedInUserLastname !== 'undefined') {
        loggedInUserLastname = window.loggedInUserLastname;
    }
    
    if (typeof window.loggedInUserEmail !== 'undefined') {
        loggedInUserEmail = window.loggedInUserEmail;
    }
    
    console.log('User state initialized:', {
        isLoggedIn: isUserLoggedIn,
        firstName: loggedInUserFirstname,
        lastName: loggedInUserLastname,
        email: loggedInUserEmail
    });
}

function initializeForm() {
    // Set enrollment type
    const enrollmentTypeInput = document.querySelector('input[name="enrollment_type"]');
    if (enrollmentTypeInput) {
        enrollmentTypeInput.value = 'Modular';
    }
    
    // Set learning mode to asynchronous for modular enrollment
    const learningModeInput = document.querySelector('input[name="learning_mode"]');
    if (learningModeInput) {
        learningModeInput.value = 'asynchronous';
    }
    
    // Adjust total steps based on user login status
    if (isUserLoggedIn) {
        totalSteps = 2; // Only Package Selection and Student Registration
    } else {
        totalSteps = 3; // Package Selection, Account Registration, Student Registration
    }
    
    // Update stepper
    updateStepper();
}

function initializeStepNavigation() {
    // Set up step navigation buttons
    const nextButtons = document.querySelectorAll('[onclick*="nextStep"]');
    const prevButtons = document.querySelectorAll('[onclick*="prevStep"]');
    
    nextButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            nextStep();
        });
    });
    
    prevButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            prevStep();
        });
    });
}

function initializeDynamicValidation() {
    // Add validation for dynamic form fields
    const inputs = document.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateCurrentStep();
        });
        
        input.addEventListener('change', function() {
            validateCurrentStep();
        });
    });
}

function initializeOTPSystem() {
    // Initialize OTP input handling
    const otpModal = document.getElementById('otpModal');
    if (otpModal) {
        otpModal.addEventListener('shown.bs.modal', function() {
            initializeOTPInputs();
        });
    }
    
    // Initialize email validation
    initializeEmailValidation();
}

function initializeTermsModal() {
    const termsLink = document.getElementById('showTerms');
    if (termsLink) {
        termsLink.addEventListener('click', function(e) {
            e.preventDefault();
            showTermsModal();
        });
    }
    
    // Modal click outside to close
    const termsModal = document.getElementById('termsModal');
    if (termsModal) {
        termsModal.addEventListener('click', function(e) {
            if (e.target === termsModal) {
                closeTermsModal();
            }
        });
    }
    
    // Escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTermsModal();
        }
    });
}

function initializeModuleSelection() {
    // Initialize existing module checkboxes if any
    const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
    moduleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            handleModuleSelection(this);
        });
    });
}

// ===== STEP NAVIGATION FUNCTIONS =====

function nextStep() {
    console.log('Next step called, current step:', currentStep);
    
    if (!validateCurrentStep()) {
        console.log('Current step validation failed');
        return;
    }
    
    // Handle step-specific logic
    if (currentStep === 1) {
        // Step 1: Package/Module Selection
        if (!selectedPackageId) {
            showErrorModal('Please select a package to continue.');
            return;
        }
        if (selectedModules.length === 0) {
            showErrorModal('Please select at least one module to continue.');
            return;
        }
        
        // Update hidden inputs
        updateSelectedModulesInput();
        document.getElementById('packageIdInput').value = selectedPackageId;
        document.getElementById('hidden_program_id').value = selectedProgramId;
        
        // Move to next step
        if (isUserLoggedIn) {
            // Skip account registration for logged-in users
            animateStepTransition('step-1', 'step-3');
            currentStep = 3;
        } else {
            // Go to account registration
            animateStepTransition('step-1', 'step-2');
            currentStep = 2;
        }
        
    } else if (currentStep === 2) {
        // Step 2: Account Registration (only for non-logged users)
        if (!isOTPVerified) {
            showErrorModal('Please verify your email address before proceeding.');
            return;
        }
        
        // Copy account data to student form
        copyAccountDataToStudentForm();
        
        // Move to final step
        animateStepTransition('step-2', 'step-3');
        currentStep = 3;
    }
    
    updateStepper();
    
    // Load user data if moving to final step
    if (currentStep === 3) {
        if (isUserLoggedIn) {
            fillLoggedInUserData();
        }
    }
}

function prevStep() {
    console.log('Previous step called, current step:', currentStep);
    
    if (currentStep > 1) {
        if (currentStep === 3) {
            // From final step, go back to account registration or package selection
            if (isUserLoggedIn) {
                // Skip account registration for logged-in users
                animateStepTransition('step-3', 'step-1', true);
                currentStep = 1;
            } else {
                // Go back to account registration
                animateStepTransition('step-3', 'step-2', true);
                currentStep = 2;
            }
        } else if (currentStep === 2) {
            // From account registration, go back to package selection
            animateStepTransition('step-2', 'step-1', true);
            currentStep = 1;
        }
        
        updateStepper();
    }
}

function showStep(stepNumber) {
    const step = document.getElementById(`step-${stepNumber}`);
    if (step) {
        step.classList.add('active');
        step.style.display = 'block';
    }
}

function hideStep(stepNumber) {
    const step = document.getElementById(`step-${stepNumber}`);
    if (step) {
        step.classList.remove('active');
        step.style.display = 'none';
    }
}

function updateStepper() {
    // Update stepper for 3-step flow
    for (let i = 1; i <= 3; i++) {
        const stepElement = document.getElementById(`stepper-${i}`);
        const barElement = document.querySelector(`.stepper .bar:nth-child(${i * 2 - 1})`);
        
        if (stepElement) {
            if (i < currentStep) {
                stepElement.classList.add('completed');
                stepElement.classList.remove('active');
                stepElement.querySelector('.circle').innerHTML = '✓';
            } else if (i === currentStep) {
                stepElement.classList.add('active');
                stepElement.classList.remove('completed');
                stepElement.querySelector('.circle').innerHTML = i;
            } else {
                stepElement.classList.remove('active', 'completed');
                stepElement.querySelector('.circle').innerHTML = i;
            }
        }
        
        if (barElement) {
            if (i < currentStep) {
                barElement.classList.add('completed');
                barElement.classList.remove('active');
            } else if (i === currentStep) {
                barElement.classList.add('active');
                barElement.classList.remove('completed');
            } else {
                barElement.classList.remove('active', 'completed');
            }
        }
    }
}

// ===== VALIDATION FUNCTIONS =====

function validateCurrentStep() {
    switch (currentStep) {
        case 1:
            return validateModuleSelection();
        case 2:
            return true; // Skip learning mode for modular
        case 3:
            return isUserLoggedIn ? true : validateAccountRegistration();
        case 4:
            return validateStudentRegistration();
        default:
            return true;
    }
}

function validateModuleSelection() {
    const isValid = selectedModules.length > 0;
    const nextButton = document.getElementById('packageNextBtn');
    
    if (nextButton) {
        nextButton.disabled = !isValid;
        nextButton.style.opacity = isValid ? '1' : '0.5';
    }
    
    return isValid;
}

function validateAccountRegistration() {
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    const emailField = document.getElementById('user_email');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const nextBtn = document.getElementById('step3NextBtn');
    
    if (!firstnameField || !lastnameField || !emailField || !passwordField || !passwordConfirmField) {
        return false;
    }
    
    // Check if all required fields are filled
    const isFirstnameFilled = firstnameField.value.trim().length > 0;
    const isLastnameFilled = lastnameField.value.trim().length > 0;
    const isEmailFilled = emailField.value.trim().length > 0;
    const isPasswordFilled = passwordField.value.length >= 8;
    const isPasswordConfirmFilled = passwordConfirmField.value.length > 0;
    
    // Check if validations pass
    const isPasswordValid = validatePassword();
    const isPasswordConfirmValid = validatePasswordConfirmation();
    
    // Check if email field has error
    const emailError = document.getElementById('emailError');
    const emailHasError = emailError && emailError.style.display === 'block';
    
    // Check if password errors are showing
    const passwordError = document.getElementById('passwordError');
    const passwordMatchError = document.getElementById('passwordMatchError');
    const passwordHasError = passwordError && passwordError.style.display === 'block';
    const passwordMatchHasError = passwordMatchError && passwordMatchError.style.display === 'block';
    
    const allFieldsFilled = isFirstnameFilled && isLastnameFilled && isEmailFilled && isPasswordFilled && isPasswordConfirmFilled;
    const allValidationsPassed = isPasswordValid && isPasswordConfirmValid && !emailHasError && !passwordHasError && !passwordMatchHasError;
    const emailVerified = enrollmentEmailVerified;
    
    const isValid = allFieldsFilled && allValidationsPassed && emailVerified;
    
    if (nextBtn) {
        nextBtn.disabled = !isValid;
        nextBtn.style.opacity = isValid ? '1' : '0.5';
    }
    
    return isValid;
}

function validateStudentRegistration() {
    const form = document.getElementById('enrollmentForm');
    const requiredInputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredInputs.forEach(input => {
        if (!input.value || input.value.trim() === '') {
            isValid = false;
        }
    });
    
    // Check if at least one module is selected
    if (selectedModules.length === 0) {
        isValid = false;
    }
    
    // Check terms acceptance
    const termsCheckbox = document.getElementById('termsCheckbox');
    if (termsCheckbox && !termsCheckbox.checked) {
        isValid = false;
    }
    
    const submitButton = document.getElementById('submitButton');
    if (submitButton) {
        submitButton.disabled = !isValid;
        submitButton.style.opacity = isValid ? '1' : '0.5';
    }
    
    return isValid;
}

// ===== MODULE SELECTION FUNCTIONS =====

async function loadModulesForProgram(packageId) {
    const modulesContainer = document.getElementById('modulesContainer');
    if (!modulesContainer) return;
    
    // Show loading state
    modulesContainer.innerHTML = `
        <div class="modules-loading">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading modules...</p>
        </div>
    `;
    
    try {
        const response = await fetch(`${MODULES_URL}?package_id=${packageId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.modules && data.modules.length > 0) {
            renderModules(data.modules);
        } else {
            showNoModulesMessage();
        }
    } catch (error) {
        console.error('Error loading modules:', error);
        showModulesError();
    }
}

function renderModules(modules) {
    const modulesContainer = document.getElementById('modulesContainer');
    if (!modulesContainer) return;
    
    const selectedPackageName = sessionStorage.getItem('selectedPackageName') || 'Selected Package';
    
    const moduleSelectionHtml = `
        <div class="module-selection-header">
            <h3>Select Your Modules</h3>
            <p>Choose one or more modules for self-paced learning (no batch required)</p>
        </div>
        
        <div class="program-selection-notice">
            <h4>Package: ${selectedPackageName}</h4>
            <p>You can select multiple modules from this package</p>
        </div>
        
        <div class="modules-grid">
            ${modules.map(module => `
                <div class="module-card" data-module-id="${module.id}">
                    <div class="module-header">
                        <input type="checkbox" class="module-checkbox" id="module_${module.id}" 
                               onchange="handleModuleSelection(this)" value="${module.id}">
                        <label for="module_${module.id}" class="module-title">${module.name}</label>
                    </div>
                    <div class="module-description">
                        ${module.description || 'No description available'}
                    </div>
                    <div class="module-meta">
                        <span class="module-duration">
                            <i class="fas fa-clock"></i>
                            ${module.duration || 'Self-paced'}
                        </span>
                        <span class="module-level">${module.level || 'All Levels'}</span>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    
    modulesContainer.innerHTML = moduleSelectionHtml;
    
    // Add click handlers to module cards
    const moduleCards = modulesContainer.querySelectorAll('.module-card');
    moduleCards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = card.querySelector('.module-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    handleModuleSelection(checkbox);
                }
            }
        });
    });
}

function handleModuleSelection(checkbox) {
    const moduleId = checkbox.value;
    const moduleCard = checkbox.closest('.module-card');
    const moduleTitle = moduleCard.querySelector('.module-title').textContent;
    
    if (checkbox.checked) {
        // Add module to selection
        if (!selectedModules.find(m => m.id === moduleId)) {
            selectedModules.push({
                id: moduleId,
                name: moduleTitle
            });
        }
        moduleCard.classList.add('selected');
    } else {
        // Remove module from selection
        selectedModules = selectedModules.filter(m => m.id !== moduleId);
        moduleCard.classList.remove('selected');
    }
    
    // Update UI
    updateSelectedModulesDisplay();
    
    // Validate current step
    validateCurrentStep();
    
    // Update hidden input
    updateSelectedModulesInput();
    
    console.log('Selected modules:', selectedModules);
}

function updateSelectedModulesDisplay() {
    const summaryContainer = document.getElementById('selectedModulesSummary');
    if (!summaryContainer) return;
    
    if (selectedModules.length === 0) {
        summaryContainer.style.display = 'none';
        return;
    }
    
    const summaryHtml = `
        <div class="selected-modules-summary">
            <h4>Selected Modules</h4>
            <div class="selected-modules-count">${selectedModules.length}</div>
            <p>Module${selectedModules.length > 1 ? 's' : ''} selected for enrollment</p>
            <div class="selected-modules-list">
                ${selectedModules.map(module => `
                    <span class="selected-module-tag">${module.name}</span>
                `).join('')}
            </div>
        </div>
    `;
    
    summaryContainer.innerHTML = summaryHtml;
    summaryContainer.style.display = 'block';
}

function updateSelectedModulesInput() {
    const hiddenInput = document.getElementById('selected_modules');
    if (hiddenInput) {
        hiddenInput.value = JSON.stringify(selectedModules);
    }
}

function showNoModulesMessage() {
    const modulesContainer = document.getElementById('modulesContainer');
    if (!modulesContainer) return;
    
    modulesContainer.innerHTML = `
        <div class="modules-empty">
            <i class="fas fa-info-circle"></i>
            <h4>No Modules Available</h4>
            <p>This package currently has no modules configured for modular enrollment.</p>
            <p>Please contact the administrator or try selecting a different package.</p>
        </div>
    `;
}

function showModulesError() {
    const modulesContainer = document.getElementById('modulesContainer');
    if (!modulesContainer) return;
    
    modulesContainer.innerHTML = `
        <div class="modules-error">
            <i class="fas fa-exclamation-triangle"></i>
            <h4>Error Loading Modules</h4>
            <p>Unable to load modules for this package. Please try again later.</p>
            <button class="btn btn-primary" onclick="loadModulesForProgram('${selectedPackageId}')">
                <i class="fas fa-redo"></i> Retry
            </button>
        </div>
    `;
}

function hideModuleSelection() {
    const modulesContainer = document.getElementById('modulesContainer');
    if (modulesContainer) {
        modulesContainer.innerHTML = '';
    }
    
    const summaryContainer = document.getElementById('selectedModulesSummary');
    if (summaryContainer) {
        summaryContainer.style.display = 'none';
    }
}

// ===== USER DATA FUNCTIONS =====

async function loadUserPrefillData() {
    if (!isUserLoggedIn) {
        console.log('User not logged in, skipping prefill');
        return;
    }
    
    try {
        const response = await fetch(PREFILL_URL, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });
        
        if (!response.ok) {
            console.log(`Prefill fetch failed: HTTP ${response.status}`);
            return;
        }
        
        const payload = await response.json();
        if (!payload.success) {
            console.log('Prefill response not successful:', payload.message);
            return;
        }
        
        // Populate fields
        Object.entries(payload.data).forEach(([key, value]) => {
            const field = document.querySelector(
                `input[name="${key}"], select[name="${key}"], textarea[name="${key}"]`
            );
            if (!field) return;
            
            if (field.type === 'checkbox') {
                field.checked = !!value;
            } else {
                field.value = value;
            }
        });
        
        console.log('User data prefilled successfully', payload.data);
    } catch (error) {
        console.error('Error loading user prefill data:', error);
    }
}

// ===== OTP FUNCTIONS =====

let enrollmentEmailVerified = false;

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
        
        // Debounce email checking
        emailCheckTimeout = setTimeout(() => {
            checkEmailAvailability(email);
        }, 500);
    });
}

async function checkEmailAvailability(email) {
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    
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
        
        if (data.available) {
            sendOtpBtn.disabled = false;
            sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
        } else {
            sendOtpBtn.disabled = true;
            sendOtpBtn.innerHTML = '<i class="fas fa-times"></i> Email Exists';
            
            const emailError = document.getElementById('emailError');
            if (emailError) {
                emailError.textContent = 'This email is already registered. Please use a different email or login.';
                emailError.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error checking email:', error);
        sendOtpBtn.disabled = false;
        sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
    }
}

async function sendEnrollmentOTP() {
    const email = document.getElementById('user_email').value;
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    
    if (!email) {
        showErrorModal('Please enter your email address first.');
        return;
    }
    
    sendOtpBtn.disabled = true;
    sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    try {
        const response = await fetch('/signup/send-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ email: email })
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('otpTargetEmail').textContent = email;
            const otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
            otpModal.show();
            
            sendOtpBtn.innerHTML = '<i class="fas fa-redo"></i> Resend OTP';
            showStatusMessage('OTP sent successfully to your email!', 'success');
        } else {
            showStatusMessage(data.message, 'error');
            sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
        }
    } catch (error) {
        console.error('Error sending OTP:', error);
        showStatusMessage('Failed to send OTP. Please try again.', 'error');
        sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
    }
    
    sendOtpBtn.disabled = false;
}

function initializeOTPInputs() {
    const otpInputs = document.querySelectorAll('.otp-digit');
    const hiddenOtpInput = document.getElementById('otp_code_modal');
    const continueBtn = document.getElementById('verifyOtpBtnModal');
    
    otpInputs.forEach((input, idx) => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/, '');
            
            if (input.value && idx < otpInputs.length - 1) {
                otpInputs[idx + 1].focus();
            }
            
            const code = Array.from(otpInputs).map(i => i.value).join('');
            hiddenOtpInput.value = code;
            
            if (code.length === 6) {
                continueBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                verifyOTPCode(code);
            } else {
                enrollmentEmailVerified = false;
                continueBtn.disabled = true;
                document.getElementById('otpStatusModal').style.display = 'none';
            }
        });
        
        input.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !input.value && idx > 0) {
                otpInputs[idx - 1].focus();
            }
        });
        
        input.addEventListener('paste', e => {
            e.preventDefault();
            const txt = (e.clipboardData || window.clipboardData).getData('text')
                .replace(/\D/g, '').slice(0, 6);
            txt.split('').forEach((d, i) => {
                if (otpInputs[i]) otpInputs[i].value = d;
            });
            otpInputs[Math.min(txt.length, 5)].focus();
            input.dispatchEvent(new Event('input'));
        });
    });
}

async function verifyOTPCode(code) {
    try {
        const response = await fetch('/signup/verify-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ otp: code })
        });
        
        const data = await response.json();
        const continueBtn = document.getElementById('verifyOtpBtnModal');
        const statusElement = document.getElementById('otpStatusModal');
        
        if (data.success) {
            enrollmentEmailVerified = true;
            continueBtn.disabled = false;
            continueBtn.innerHTML = 'Continue';
            continueBtn.classList.add('verified');
            
            if (statusElement) {
                statusElement.textContent = 'Code verified! Click Continue to proceed.';
                statusElement.className = 'status-message status-success';
                statusElement.style.display = 'block';
            }
            
            // Enable next step validation
            validateCurrentStep();
        } else {
            enrollmentEmailVerified = false;
            continueBtn.disabled = true;
            continueBtn.innerHTML = 'Continue';
            
            if (statusElement) {
                statusElement.textContent = data.message || 'Invalid code. Please try again.';
                statusElement.className = 'status-message status-error';
                statusElement.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error verifying OTP:', error);
        const continueBtn = document.getElementById('verifyOtpBtnModal');
        const statusElement = document.getElementById('otpStatusModal');
        
        continueBtn.disabled = true;
        continueBtn.innerHTML = 'Continue';
        
        if (statusElement) {
            statusElement.textContent = 'Verification failed. Please try again.';
            statusElement.className = 'status-message status-error';
            statusElement.style.display = 'block';
        }
    }
}

async function verifyEnrollmentOTPModal() {
    const otp = document.getElementById('otp_code_modal').value;
    
    if (!otp || otp.length !== 6) {
        showStatusMessage('Please enter a valid 6-digit OTP.', 'error');
        return;
    }
    
    if (enrollmentEmailVerified) {
        // Close modal and proceed
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('otpModal'));
        if (modalInstance) {
            modalInstance.hide();
        }
        
        // Mark email as readonly
        const emailInput = document.getElementById('user_email');
        if (emailInput) {
            emailInput.readOnly = true;
        }
        
        // Validate current step
        validateCurrentStep();
    } else {
        showStatusMessage('Please wait for verification to complete.', 'error');
    }
}

function resendOTPCode() {
    sendEnrollmentOTP();
}

// ===== VALIDATION HELPER FUNCTIONS =====

async function validateEmail() {
    const emailField = document.getElementById('user_email');
    const emailError = document.getElementById('emailError');
    
    if (!emailField) return true;
    
    const email = emailField.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailPattern.test(email)) {
        emailField.style.borderColor = '#dc3545';
        if (emailError) {
            emailError.style.display = 'block';
            emailError.textContent = 'Please enter a valid email address.';
        }
        return false;
    } else if (email) {
        try {
            const response = await fetch('/check-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ email: email })
            });
            
            const data = await response.json();
            if (data.exists) {
                emailField.style.borderColor = '#dc3545';
                if (emailError) {
                    emailError.style.display = 'block';
                    emailError.textContent = 'This email is already registered. Please use a different email.';
                }
                return false;
            } else {
                emailField.style.borderColor = '#28a745';
                if (emailError) {
                    emailError.style.display = 'none';
                }
                return true;
            }
        } catch (error) {
            console.error('Email validation error:', error);
            return true;
        }
    }
    
    return true;
}

function validatePassword() {
    const passwordField = document.getElementById('password');
    const passwordError = document.getElementById('passwordError');
    
    if (!passwordField) return true;
    
    const password = passwordField.value;
    
    if (password.length > 0 && password.length < 8) {
        passwordField.style.borderColor = '#dc3545';
        if (passwordError) {
            passwordError.style.display = 'block';
            passwordError.textContent = 'Password must be at least 8 characters long.';
        }
        return false;
    } else if (password.length >= 8) {
        passwordField.style.borderColor = '#28a745';
        if (passwordError) {
            passwordError.style.display = 'none';
        }
        return true;
    }
    
    return true;
}

function validatePasswordConfirmation() {
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const passwordMatchError = document.getElementById('passwordMatchError');
    
    if (!passwordField || !passwordConfirmField) return true;
    
    const password = passwordField.value;
    const passwordConfirm = passwordConfirmField.value;
    
    if (passwordConfirm.length > 0 && password !== passwordConfirm) {
        passwordConfirmField.style.borderColor = '#dc3545';
        if (passwordMatchError) {
            passwordMatchError.style.display = 'block';
            passwordMatchError.textContent = 'Passwords do not match.';
        }
        return false;
    } else if (passwordConfirm.length > 0 && password === passwordConfirm) {
        passwordConfirmField.style.borderColor = '#28a745';
        if (passwordMatchError) {
            passwordMatchError.style.display = 'none';
        }
        return true;
    }
    
    return true;
}

// ===== FORM SUBMISSION =====

function handleFormSubmission(event) {
    console.log('Form submission started');
    
    try {
        event.preventDefault();
        
        const form = event.target;
        
        // Validate form
        const validationResult = validateFormBeforeSubmission(form);
        if (!validationResult.isValid) {
            console.error('Form validation failed:', validationResult.errors);
            showFormErrors(validationResult.errors);
            return false;
        }
        
        // Check reCAPTCHA
        if (hasRecaptcha && typeof grecaptcha !== 'undefined') {
            const recaptchaResponse = grecaptcha.getResponse();
            if (!recaptchaResponse) {
                showFormErrors(['Please complete the CAPTCHA verification.']);
                return false;
            }
        }
        
        // Ensure selected modules are in the form
        updateSelectedModulesInput();
        
        // Show loading state
        showFormLoading(true);
        
        // Log form data
        const formData = new FormData(form);
        console.log('Form data being submitted:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}:`, value);
        }
        
        // Submit form
        form.submit();
        
    } catch (error) {
        console.error('Error in form submission:', error);
        showFormErrors(['An unexpected error occurred. Please try again.']);
        showFormLoading(false);
        return false;
    }
}

function validateFormBeforeSubmission(form) {
    const errors = [];
    
    // Check required fields
    const requiredInputs = form.querySelectorAll('[required]');
    requiredInputs.forEach(input => {
        const fieldName = input.name;
        const fieldLabel = input.getAttribute('data-label') || 
                          input.previousElementSibling?.textContent?.replace('*', '').trim() ||
                          fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace('_', ' ');
        
        if (!input.value || input.value.trim() === '') {
            errors.push(`${fieldLabel} is required.`);
        }
    });
    
    // Check module selection
    if (selectedModules.length === 0) {
        errors.push('Please select at least one module.');
    }
    
    // Check email format
    const emailInput = form.querySelector('[name="email"]');
    if (emailInput && emailInput.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value)) {
            errors.push('Please enter a valid email address.');
        }
    }
    
    // Check terms acceptance
    const termsCheckbox = form.querySelector('#termsCheckbox');
    if (termsCheckbox && !termsCheckbox.checked) {
        errors.push('Please accept the terms and conditions.');
    }
    
    return {
        isValid: errors.length === 0,
        errors: errors
    };
}

function showFormErrors(errors) {
    const existingErrorContainer = document.getElementById('formErrorContainer');
    if (existingErrorContainer) {
        existingErrorContainer.remove();
    }
    
    const errorContainer = document.createElement('div');
    errorContainer.id = 'formErrorContainer';
    errorContainer.className = 'alert alert-danger mt-3';
    errorContainer.innerHTML = `
        <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
        <ul class="mb-0">
            ${errors.map(error => `<li>${error}</li>`).join('')}
        </ul>
    `;
    
    const submitButton = document.getElementById('submitButton');
    if (submitButton) {
        submitButton.parentNode.insertBefore(errorContainer, submitButton);
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function showFormLoading(loading) {
    const submitButton = document.getElementById('submitButton');
    if (!submitButton) return;
    
    if (loading) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting Registration...';
        submitButton.classList.add('btn-secondary');
        submitButton.classList.remove('btn-success');
    } else {
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-check-circle"></i> Complete Registration';
        submitButton.classList.remove('btn-secondary');
        submitButton.classList.add('btn-success');
    }
}

// ===== TERMS MODAL FUNCTIONS =====

function showTermsModal() {
    const modal = document.getElementById('termsModal');
    if (!modal) return;
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeTermsModal() {
    const modal = document.getElementById('termsModal');
    if (!modal) return;
    
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

function acceptTerms() {
    const termsCheckbox = document.getElementById('termsCheckbox');
    if (termsCheckbox) {
        termsCheckbox.checked = true;
    }
    
    closeTermsModal();
    validateCurrentStep();
}

function declineTerms() {
    const termsCheckbox = document.getElementById('termsCheckbox');
    if (termsCheckbox) {
        termsCheckbox.checked = false;
    }
    
    closeTermsModal();
    showErrorModal('You must accept the terms and conditions to proceed with registration.');
}

// ===== UTILITY FUNCTIONS =====

function showStatusMessage(message, type) {
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
                <h3 id="modalTitle"></h3>
                <p id="modalMessage"></p>
                <button onclick="closeModal()" class="btn btn-primary">OK</button>
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
    if (modal) {
        modal.style.display = 'none';
    }
}

function showWarning(message) {
    showModal('Warning', message, 'warning');
}

// ===== EDUCATION LEVEL FUNCTIONS =====

function toggleEducationLevelRequirements() {
    const educationLevel = document.getElementById('educationLevel');
    const requirementsContainer = document.getElementById('educationLevelRequirements');
    
    if (!educationLevel || !requirementsContainer) return;
    
    const selectedOption = educationLevel.options[educationLevel.selectedIndex];
    console.log('Education level changed to:', educationLevel.value);
    
    // Clear existing requirements
    requirementsContainer.innerHTML = '';
    requirementsContainer.style.display = 'none';
    
    if (educationLevel.value && selectedOption.dataset.fileRequirements) {
        try {
            const requirements = JSON.parse(selectedOption.dataset.fileRequirements);
            
            if (requirements && requirements.length > 0) {
                const requirementsHtml = requirements.map(req => `
                    <div class="form-group">
                        <label for="${req.field_name}">${req.label}</label>
                        <input type="file" name="${req.field_name}" id="${req.field_name}" 
                               class="form-control" ${req.required ? 'required' : ''} 
                               accept="${req.accept || ''}" 
                               onchange="handleFileUpload(this)">
                    </div>
                `).join('');
                
                requirementsContainer.innerHTML = requirementsHtml;
                requirementsContainer.style.display = 'block';
            }
        } catch (error) {
            console.error('Error parsing education level requirements:', error);
        }
    }
}

function handleFileUpload(inputElement) {
    const fieldName = inputElement.name;
    const file = inputElement.files[0];
    
    if (!file) return;
    
    // Show loading
    showLoadingModal('Validating document...');
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('field_name', fieldName);
    formData.append('first_name', loggedInUserFirstname || '');
    formData.append('last_name', loggedInUserLastname || '');
    
    fetch(VALIDATE_URL, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(response => response.json())
    .then(data => {
        closeLoadingModal();
        
        if (data.success) {
            showSuccessModal('Document validated successfully!');
            
            if (data.suggestions && data.suggestions.length > 0) {
                showProgramSuggestions(data.suggestions);
            }
            
            if (data.education_level) {
                handleEducationLevelDetection(data.education_level);
            }
        } else {
            showErrorModal(data.message || 'Document validation failed.');
            inputElement.value = '';
        }
    })
    .catch(error => {
        closeLoadingModal();
        console.error('File upload error:', error);
        showErrorModal('An error occurred while validating the document.');
        inputElement.value = '';
    });
}

function showLoadingModal(message) {
    let modal = document.getElementById('loadingModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'loadingModal';
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
                    <p id="loadingMessage">${message}</p>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    document.getElementById('loadingMessage').textContent = message;
    modal.style.display = 'flex';
}

function closeLoadingModal() {
    const modal = document.getElementById('loadingModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function showProgramSuggestions(suggestions) {
    // For modular enrollment, we might need to suggest packages instead
    // This would need to be adapted based on your backend implementation
    console.log('Program suggestions:', suggestions);
    
    if (suggestions.length > 0) {
        showInfoModal(`We found ${suggestions.length} program(s) that match your uploaded certificate.`);
    }
}

function handleEducationLevelDetection(level) {
    const educationLevelSelect = document.getElementById('educationLevel');
    if (!educationLevelSelect) return;
    
    if (level === 'graduate') {
        educationLevelSelect.value = 'Graduate';
    } else if (level === 'undergraduate') {
        educationLevelSelect.value = 'Undergraduate';
    }
    
    // Trigger change event
    educationLevelSelect.dispatchEvent(new Event('change'));
}

// ===== HELPER FUNCTIONS =====

function updateHiddenStartDate() {
    const startDateInput = document.getElementById('start_date_input');
    const hiddenStartDate = document.getElementById('hidden_start_date');
    
    if (startDateInput && hiddenStartDate) {
        hiddenStartDate.value = startDateInput.value;
    }
}

function updateHiddenProgramId() {
    const programSelect = document.getElementById('programSelect');
    const hiddenProgramId = document.getElementById('hidden_program_id');
    
    if (programSelect && hiddenProgramId) {
        hiddenProgramId.value = programSelect.value;
    }
}

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
        });
    }
    
    // Add validation event listeners for Account Registration step
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    const emailField = document.getElementById('user_email');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    
    if (firstnameField) {
        firstnameField.addEventListener('input', validateAccountRegistration);
    }
    if (lastnameField) {
        lastnameField.addEventListener('input', validateAccountRegistration);
    }
    if (emailField) {
        emailField.addEventListener('input', function() {
            setTimeout(validateEmail, 300);
            setTimeout(validateAccountRegistration, 400);
        });
    }
    if (passwordField) {
        passwordField.addEventListener('input', function() {
            setTimeout(validatePassword, 50);
            setTimeout(validateAccountRegistration, 100);
        });
    }
    if (passwordConfirmField) {
        passwordConfirmField.addEventListener('input', function() {
            setTimeout(validatePasswordConfirmation, 50);
            setTimeout(validateAccountRegistration, 100);
        });
    }
});
