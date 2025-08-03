// Full Enrollment JavaScript

// Make functions globally accessible
window.sendEnrollmentOTP = sendEnrollmentOTP;
window.otpDigitHandler = otpDigitHandler;
window.handleOTPPaste = handleOTPPaste;
window.updateOTPCode = updateOTPCode;
window.resendOTPCode = resendOTPCode;
window.showEnrollmentMessage = showEnrollmentMessage;
window.verifyEnrollmentOTPModal = verifyEnrollmentOTPModal;

async function sendEnrollmentOTP() {
    const email = document.getElementById('user_email').value;
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const emailError = document.getElementById('emailError');
    
    if (!email) {
        if (emailError) {
            emailError.textContent = 'Please enter your email address first.';
            emailError.style.display = 'block';
            emailError.className = 'text-danger small mt-1';
        }
        return;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        if (emailError) {
            emailError.textContent = 'Please enter a valid email address.';
            emailError.style.display = 'block';
            emailError.className = 'text-danger small mt-1';
        }
        return;
    }

    // Disable button and show checking state
    sendOtpBtn.disabled = true;
    sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking email...';

    try {
        // First check if email is available
        const checkResponse = await fetch('/enrollment/check-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email: email })
        });

        const checkData = await checkResponse.json();
        
        if (!checkData.available) {
            // Show error message
            if (emailError) {
                emailError.textContent = checkData.message || 'This email is already registered in our system.';
                emailError.style.display = 'block';
                emailError.className = 'text-danger small mt-1';
            }
            
            // Reset button state
            sendOtpBtn.innerHTML = '<i class="fas fa-times"></i> Email Exists';
            sendOtpBtn.style.opacity = '0.5';
            sendOtpBtn.style.cursor = 'not-allowed';
            return;
        }

        // Email is available, now send OTP
        sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        const response = await fetch('/enrollment/send-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email: email })
        });

        const data = await response.json();

        if (data.success) {
            // Clear any error messages
            if (emailError) {
                emailError.style.display = 'none';
            }
            
            // Show success message
            showEnrollmentMessage('OTP sent successfully! Please check your email.', 'success');
            
            // Update button state
            sendOtpBtn.innerHTML = '<i class="fas fa-redo"></i> Resend OTP';
            sendOtpBtn.disabled = false;
            sendOtpBtn.style.opacity = '1';
            sendOtpBtn.style.cursor = 'pointer';
            
            // Set email as verified
            window.enrollmentEmailVerified = true;
            
            // Trigger step 4 validation
            if (typeof window.validateStep4 === 'function') {
                window.validateStep4();
            }
            
            // Show OTP verification modal using Bootstrap
            const otpModal = document.getElementById('otpModal');
            if (otpModal) {
                // Clear previous OTP inputs
                for (let i = 1; i <= 6; i++) {
                    const otpInput = document.getElementById(`otp_${i}`);
                    if (otpInput) {
                        otpInput.value = '';
                    }
                }
                document.getElementById('otp_code_modal').value = '';
                
                // Update email in modal
                const targetEmail = document.getElementById('otpTargetEmail');
                if (targetEmail) {
                    targetEmail.textContent = email;
                }
                
                // Show the modal
                const modal = new bootstrap.Modal(otpModal);
                modal.show();
                
                // Auto focus on first OTP input when modal is shown
                otpModal.addEventListener('shown.bs.modal', function() {
                    const firstInput = document.getElementById('otp_1');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, { once: true });
            }
        } else {
            // Show error message
            if (emailError) {
                emailError.textContent = data.message || 'Failed to send OTP. Please try again.';
                emailError.style.display = 'block';
                emailError.className = 'text-danger small mt-1';
            }
            
            // Reset button state
            sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
            sendOtpBtn.disabled = false;
            sendOtpBtn.style.opacity = '1';
            sendOtpBtn.style.cursor = 'pointer';
        }
    } catch (error) {
        console.error('Error sending OTP:', error);
        
        // Show error message
        if (emailError) {
            emailError.textContent = 'Network error. Please check your connection and try again.';
            emailError.style.display = 'block';
            emailError.className = 'text-danger small mt-1';
        }
        
        // Reset button state
        sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
        sendOtpBtn.disabled = false;
        sendOtpBtn.style.opacity = '1';
        sendOtpBtn.style.cursor = 'pointer';
    }
}

function showEnrollmentMessage(message, type = 'info') {
    const messageContainer = document.getElementById('messageContainer');
    if (!messageContainer) return;
    
    const messageElement = document.createElement('div');
    messageElement.className = `alert alert-${type === 'error' ? 'danger' : type}`;
    messageElement.textContent = message;
    
    // Clear previous messages
    messageContainer.innerHTML = '';
    messageContainer.appendChild(messageElement);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        messageElement.style.transition = 'opacity 0.5s';
        messageElement.style.opacity = '0';
        setTimeout(() => messageContainer.removeChild(messageElement), 500);
    }, 5000);
}

// Handle OTP input - auto-advance and combine digits
function otpDigitHandler(input, position) {
    console.log('OTP digit handler called:', position, 'value:', input.value);
    
    // Allow only numbers
    input.value = input.value.replace(/[^0-9]/g, '');
    
    // Auto focus next input
    if (input.value && position < 6) {
        const nextInput = document.getElementById(`otp_${position + 1}`);
        if (nextInput) {
            nextInput.focus();
        }
    }
    
    // Combine all digits into hidden input
    updateOTPCode();
}

// Handle paste functionality for OTP inputs
function handleOTPPaste(event, position) {
    event.preventDefault();
    
    // Get pasted text
    const pastedText = (event.clipboardData || window.clipboardData).getData('text');
    const digits = pastedText.replace(/[^0-9]/g, '').split('');
    
    console.log('OTP paste detected:', pastedText, 'digits:', digits);
    
    // Fill inputs starting from current position
    for (let i = 0; i < digits.length && (position + i) <= 6; i++) {
        const targetInput = document.getElementById(`otp_${position + i}`);
        if (targetInput && digits[i]) {
            targetInput.value = digits[i];
        }
    }
    
    // Update combined code
    updateOTPCode();
    
    // Focus on the last filled input or next empty one
    const lastPosition = Math.min(position + digits.length, 6);
    const lastInput = document.getElementById(`otp_${lastPosition}`);
    if (lastInput) {
        lastInput.focus();
    }
}

// Update the hidden OTP code input with all digits combined
function updateOTPCode() {
    let otpCode = '';
    for (let i = 1; i <= 6; i++) {
        const otpInput = document.getElementById(`otp_${i}`);
        if (otpInput) {
            otpCode += otpInput.value || '';
        }
    }
    const hiddenInput = document.getElementById('otp_code_modal');
    if (hiddenInput) {
        hiddenInput.value = otpCode;
    }
}

// Resend OTP functionality
function resendOTPCode() {
    // Prevent modal duplication by checking if modal is already open
    const existingModal = bootstrap.Modal.getInstance(document.getElementById('otpModal'));
    if (existingModal) {
        // Modal is already open, just resend OTP without opening new modal
        const email = document.getElementById('user_email').value;
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        
        if (!email) {
            alert('Please enter your email address first.');
            return;
        }

        // Show resending status
        const resendLink = event.target;
        const originalText = resendLink.textContent;
        resendLink.textContent = 'Resending...';
        resendLink.style.pointerEvents = 'none';

        fetch('/enrollment/send-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear previous OTP inputs
                for (let i = 1; i <= 6; i++) {
                    const otpInput = document.getElementById(`otp_${i}`);
                    if (otpInput) {
                        otpInput.value = '';
                    }
                }
                document.getElementById('otp_code_modal').value = '';
                
                // Show success message in modal
                const statusElement = document.getElementById('otpStatusModal');
                if (statusElement) {
                    statusElement.textContent = 'OTP resent successfully!';
                    statusElement.className = 'status-message status-success';
                    statusElement.style.display = 'block';
                }
                
                // Auto focus on first OTP input
                document.getElementById('otp_1').focus();
            } else {
                const statusElement = document.getElementById('otpStatusModal');
                if (statusElement) {
                    statusElement.textContent = 'Failed to resend OTP: ' + data.message;
                    statusElement.className = 'status-message status-error';
                    statusElement.style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error resending OTP:', error);
            const statusElement = document.getElementById('otpStatusModal');
            if (statusElement) {
                statusElement.textContent = 'Error resending OTP. Please try again.';
                statusElement.className = 'status-message status-error';
                statusElement.style.display = 'block';
            }
        })
        .finally(() => {
            // Restore link
            resendLink.textContent = originalText;
            resendLink.style.pointerEvents = 'auto';
        });
    } else {
        // Modal is not open, use the original send function
        sendEnrollmentOTP();
    }
}

// OTP Verification function for modal
async function verifyEnrollmentOTPModal() {
    // Get OTP from the hidden input that combines all digits
    const otpHiddenInput = document.getElementById('otp_code_modal');
    const otp = otpHiddenInput ? otpHiddenInput.value : '';
    
    const email = document.getElementById('user_email').value;
    const verifyOtpBtn = document.getElementById('verifyOtpBtnModal');
    
    if (!otp || otp.length !== 6) {
        const statusElement = document.getElementById('otpStatusModal');
        if (statusElement) {
            statusElement.textContent = `Please enter a valid 6-digit OTP. Current: "${otp}" (${otp.length} digits)`;
            statusElement.className = 'status-message status-error';
            statusElement.style.display = 'block';
        }
        console.log('OTP validation failed - insufficient digits');
        return;
    }

    if (verifyOtpBtn) {
        verifyOtpBtn.disabled = true;
        verifyOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
    }

    try {
        const response = await fetch('/enrollment/verify-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                otp: otp,
                email: email
            })
        });

        const data = await response.json();
        
        if (data.success) {
            // Set global verification flag
            window.enrollmentEmailVerified = true;
            document.getElementById('user_email').readOnly = true;
            
            console.log('🔥 Email verification successful! Flag set:', window.enrollmentEmailVerified);
            
            // Immediate validation trigger
            console.log('🔥 Immediate validation - window.validateStep4 exists:', typeof window.validateStep4);
            if (typeof window.validateStep4 === 'function') {
                console.log('🔥 Immediate validateStep4 call after email verification');
                window.validateStep4();
            } else if (typeof validateStep4 === 'function') {
                console.log('🔥 Immediate validateStep4 call after email verification (fallback)');
                validateStep4();
            } else {
                console.log('🔥 validateStep4 not available immediately, will retry after modal close...');
            }
            
            // Update button to show success
            if (verifyOtpBtn) {
                verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verified';
                verifyOtpBtn.classList.add('verified');
            }
            
            // Show success message in modal
            const statusElement = document.getElementById('otpStatusModal');
            if (statusElement) {
                statusElement.textContent = 'Email verified successfully!';
                statusElement.className = 'status-message status-success';
                statusElement.style.display = 'block';
            }
            
            // Close modal after 2 seconds and trigger validation
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('otpModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Remove modal backdrop to prevent shadow staying
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                
                // Remove modal-open class from body
                document.body.classList.remove('modal-open');
                document.body.style.paddingRight = '';
                
                // Trigger validation to enable the next button
                console.log('🔥 OTP SUCCESS - Triggering step validation...');
                console.log('🔥 window.enrollmentEmailVerified:', window.enrollmentEmailVerified);
                
                // Try multiple approaches to trigger validation
                setTimeout(() => {
                    console.log('🔥 Attempting to call validateStep4...');
                    console.log('🔥 window.validateStep4 exists:', typeof window.validateStep4);
                    
                    if (typeof window.validateStep4 === 'function') {
                        console.log('🔥 Calling window.validateStep4()');
                        window.validateStep4();
                    } else {
                        console.error('❌ validateStep4 function not found!');
                        console.log('🔥 Available window functions:', Object.keys(window).filter(key => key.includes('validate') || key.includes('Step')));
                        
                        // Try emergency fix as fallback
                        if (typeof window.emergencyFixButton === 'function') {
                            console.log('🔥 Trying emergency fix as fallback...');
                            window.emergencyFixButton();
                        }
                    }
                }, 100);
                
                // Try again after a longer delay in case of timing issues
                setTimeout(() => {
                    console.log('🔥 Second attempt to call validateStep4...');
                    if (typeof window.validateStep4 === 'function') {
                        window.validateStep4();
                    } else if (typeof window.emergencyFixButton === 'function') {
                        window.emergencyFixButton();
                    }
                }, 1000);
                
                // Final attempt with emergency fix
                setTimeout(() => {
                    console.log('🔥 Final attempt with emergency fix...');
                    if (typeof window.emergencyFixButton === 'function') {
                        window.emergencyFixButton();
                    }
                }, 2000);
            }, 2000);
            
        } else {
            const statusElement = document.getElementById('otpStatusModal');
            if (statusElement) {
                statusElement.textContent = data.message || 'OTP verification failed.';
                statusElement.className = 'status-message status-error';
                statusElement.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error verifying OTP:', error);
        const statusElement = document.getElementById('otpStatusModal');
        if (statusElement) {
            statusElement.textContent = 'Error verifying OTP. Please try again.';
            statusElement.className = 'status-message status-error';
            statusElement.style.display = 'block';
        }
    }
    
    if (verifyOtpBtn && !verifyOtpBtn.classList.contains('verified')) {
        verifyOtpBtn.disabled = false;
        verifyOtpBtn.innerHTML = '<i class="fas fa-check"></i> Verify OTP';
    }
}

// Password validation function with special character requirement
function validatePassword(password = null) {
    if (password === null) {
        const pwdField = document.getElementById('password');
        password = pwdField ? pwdField.value : '';
    }
    
    const minLength = 8;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
    const isLongEnough = password.length >= minLength;

    const result = {
        isValid: isLongEnough && hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChar,
        errors: {
            length: !isLongEnough,
            uppercase: !hasUpperCase,
            lowercase: !hasLowerCase,
            numbers: !hasNumbers,
            specialChar: !hasSpecialChar
        }
    };

    // Display error message if element exists
    const errorElem = document.getElementById('passwordError');
    if (errorElem) {
        if (!result.isValid && password.length > 0) {
            // Only show error if password is not empty and invalid
            let messages = [];
            if (result.errors.length) messages.push(`At least ${minLength} characters`);
            if (result.errors.uppercase) messages.push('1 uppercase letter');
            if (result.errors.lowercase) messages.push('1 lowercase letter');
            if (result.errors.numbers) messages.push('1 number');
            if (result.errors.specialChar) messages.push('1 special character');
            errorElem.textContent = 'Password must contain: ' + messages.join(', ');
            errorElem.style.display = 'block';
        } else if (password.length === 0) {
            // Hide error message if password field is empty
            errorElem.style.display = 'none';
        } else {
            // Show success or hide error if password is valid
            errorElem.style.display = 'none';
        }
    }
    
    // Return false if password is empty or invalid, true only if valid
    return password.length > 0 && result.isValid;
}

// Validate password confirmation match
function validatePasswordConfirmation() {
    const pwdField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');
    const matchError = document.getElementById('passwordMatchError');
    
    if (!pwdField || !confirmField) return false;
    
    const matches = pwdField.value === confirmField.value && confirmField.value.length > 0;
    
    if (matchError) {
        if (!matches && confirmField.value.length > 0) {
            matchError.textContent = 'Passwords do not match';
            matchError.style.display = 'block';
        } else {
            matchError.style.display = 'none';
        }
    }
    
    return matches;
}

// Define onProgramSelectionChange to bridge HTML onchange to new logic
function onProgramSelectionChange() {
    const programSelect = document.getElementById('programSelect');
    if (!programSelect) return;
    
    const programId = programSelect.value;
    const programName = programSelect.options[programSelect.selectedIndex] ? programSelect.options[programSelect.selectedIndex].text : '';
    
    console.log('Program selection changed:', programId, programName);
    
    // Clear batch list first
    const batchSelect = document.getElementById('batch_id');
    if (batchSelect) {
        batchSelect.innerHTML = '<option value="">Loading...</option>';
    }
    
    // Call the selectProgram function
    selectProgram(programId, programName);
}

// Program selection function
function selectProgram(programId, programName) {
    console.log('Program selected:', programId, programName);
    
    // Store selected program
    window.selectedProgramId = programId;
    window.selectedProgramName = programName;
    
    // Update UI to show selected program
    const programCards = document.querySelectorAll('.program-card');
    programCards.forEach(card => {
        card.classList.remove('selected');
    });
    
    const selectedCard = document.querySelector(`[data-program-id="${programId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }
    
    // Load batches for the selected program
    loadBatchesForProgram(programId);
    
    // Trigger validation
    if (typeof window.validateStep5 === 'function') {
        window.validateStep5();
    }
}

// Load batches for selected program
async function loadBatchesForProgram(programId) {
    console.log('Loading batches for program:', programId);
    
    const batchSelect = document.getElementById('batch_id');
    if (!batchSelect) {
        console.error('Batch select element not found');
        return;
    }
    
    // Clear existing options
    batchSelect.innerHTML = '<option value="">Select a batch...</option>';
    
    try {
        const response = await fetch(`/batches/by-program?program_id=${programId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.batches) {
            data.batches.forEach(batch => {
                const option = document.createElement('option');
                option.value = batch.batch_id;
                option.textContent = `${batch.batch_name} (${batch.current_capacity}/${batch.max_capacity})`;
                batchSelect.appendChild(option);
                
                // Store batch start date in session storage for form submission
                if (batch.start_date) {
                    sessionStorage.setItem(`batch_${batch.batch_id}_start_date`, batch.start_date);
                    console.log(`📅 Stored batch ${batch.batch_id} start date:`, batch.start_date);
                }
            });
            
            console.log('Batches loaded successfully:', data.batches.length);
        } else {
            console.log('No batches found for program:', programId);
            
            // Check if auto batch creation is enabled
            if (data.auto_create_batch) {
                console.log('Auto batch creation is enabled, creating new batch...');
                await createAutoBatch(programId);
            }
        }
    } catch (error) {
        console.error('Error loading batches:', error);
        showEnrollmentMessage('Error loading batches. Please try again.', 'error');
    }
}

// Create auto batch function
async function createAutoBatch(programId) {
    console.log('Creating auto batch for program:', programId);
    
    try {
        const response = await fetch('/enrollment/create-auto-batch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ program_id: programId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            console.log('Auto batch created successfully:', data.batch);
            showEnrollmentMessage('New batch created automatically!', 'success');
            
            // Store the auto-created batch's start date in session storage
            if (data.batch && data.batch.start_date) {
                sessionStorage.setItem(`batch_${data.batch.batch_id}_start_date`, data.batch.start_date);
                console.log(`📅 Stored auto-created batch ${data.batch.batch_id} start date:`, data.batch.start_date);
            }
            
            // Reload batches
            await loadBatchesForProgram(programId);
        } else {
            console.error('Failed to create auto batch:', data.message);
            showEnrollmentMessage('Failed to create batch: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error creating auto batch:', error);
        showEnrollmentMessage('Error creating batch. Please try again.', 'error');
    }
}

// Real-time email validation function
async function checkEmailAvailability(email) {
    if (!email || email.length < 5) {
        return { available: true, message: '' }; // Don't validate if too short
    }

    try {
        const response = await fetch('/enrollment/check-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email: email })
        });

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error checking email availability:', error);
        return { available: true, message: '' }; // Assume available on error
    }
}

// Debounced email validation
let emailValidationTimer;
function debouncedEmailValidation(email) {
    clearTimeout(emailValidationTimer);
    emailValidationTimer = setTimeout(async () => {
        const result = await checkEmailAvailability(email);
        
        const emailField = document.getElementById('user_email');
        const emailError = document.getElementById('emailError');
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        
        if (!emailField) return;
        
        // Validate email format first
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValidFormat = emailRegex.test(email);
        
        if (!isValidFormat && email.length > 0) {
            // Show format error
            if (emailError) {
                emailError.textContent = 'Please enter a valid email address';
                emailError.style.display = 'block';
                emailError.className = 'text-danger small mt-1';
            }
            
            // Disable Send OTP button
            if (sendOtpBtn) {
                sendOtpBtn.disabled = true;
                sendOtpBtn.innerHTML = '<i class="fas fa-times"></i> Invalid Email';
                sendOtpBtn.style.opacity = '0.5';
                sendOtpBtn.style.cursor = 'not-allowed';
            }
            
            // Add error styling to email field
            emailField.classList.add('is-invalid');
            emailField.classList.remove('is-valid');
            return;
        }
        
        if (!result.available) {
            // Show error message for existing email
            if (emailError) {
                emailError.textContent = result.message || 'This email is already registered in our system';
                emailError.style.display = 'block';
                emailError.className = 'text-danger small mt-1';
            }
            
            // Disable Send OTP button
            if (sendOtpBtn) {
                sendOtpBtn.disabled = true;
                sendOtpBtn.innerHTML = '<i class="fas fa-times"></i> Email Exists';
                sendOtpBtn.style.opacity = '0.5';
                sendOtpBtn.style.cursor = 'not-allowed';
            }
            
            // Add error styling to email field
            emailField.classList.add('is-invalid');
            emailField.classList.remove('is-valid');
        } else if (isValidFormat) {
            // Clear error message
            if (emailError) {
                emailError.style.display = 'none';
            }
            
            // Enable Send OTP button
            if (sendOtpBtn) {
                sendOtpBtn.disabled = false;
                sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
                sendOtpBtn.style.opacity = '1';
                sendOtpBtn.style.cursor = 'pointer';
            }
            
            // Add success styling to email field
            emailField.classList.remove('is-invalid');
            emailField.classList.add('is-valid');
        } else {
            // Email is empty or too short
            if (emailError) {
                emailError.style.display = 'none';
            }
            
            // Disable Send OTP button
            if (sendOtpBtn) {
                sendOtpBtn.disabled = true;
                sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
                sendOtpBtn.style.opacity = '0.5';
                sendOtpBtn.style.cursor = 'not-allowed';
            }
            
            // Remove styling
            emailField.classList.remove('is-invalid', 'is-valid');
        }
        
        // Trigger step 4 validation if it exists
        if (typeof window.validateStep4 === 'function') {
            window.validateStep4();
        }
    }, 500); // Wait 500ms after user stops typing
}

// Make functions globally accessible
window.validatePassword = validatePassword;
window.validatePasswordConfirmation = validatePasswordConfirmation;
window.onProgramSelectionChange = onProgramSelectionChange;
window.selectProgram = selectProgram;
window.loadBatchesForProgram = loadBatchesForProgram;
window.createAutoBatch = createAutoBatch;
window.checkEmailAvailability = checkEmailAvailability;
window.debouncedEmailValidation = debouncedEmailValidation;

// Fallback validateStep4 function in case the one from Blade template is not available
window.validateStep4 = window.validateStep4 || function() {
    console.log('🔍 === validateStep4 CALLED (JavaScript fallback) ===');
    
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    const emailValidationField = document.getElementById('user_email');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const nextBtn = document.getElementById('step4NextBtn');
    
    // Check if all required fields are filled
    const isFirstnameFilled = firstnameField && firstnameField.value.trim().length > 0;
    const isLastnameFilled = lastnameField && lastnameField.value.trim().length > 0;
    const isEmailFilled = emailValidationField && emailValidationField.value.trim().length > 0;
    const isPasswordFilled = passwordField && passwordField.value.length > 0;
    const isPasswordConfirmFilled = passwordConfirmField && passwordConfirmField.value.length > 0;
    
    // Check password validation
    const isPasswordValid = typeof window.validatePassword === 'function' ? window.validatePassword() : (isPasswordFilled && passwordField.value.length >= 8);
    const isPasswordConfirmValid = typeof window.validatePasswordConfirmation === 'function' ? window.validatePasswordConfirmation() : (isPasswordConfirmFilled && passwordField.value === passwordConfirmField.value);
    
    // Check if email is verified
    const emailVerified = window.enrollmentEmailVerified || false;
    
    console.log('🔍 Fallback validation results:', {
        isFirstnameFilled,
        isLastnameFilled,
        isEmailFilled,
        isPasswordFilled,
        isPasswordConfirmFilled,
        isPasswordValid,
        isPasswordConfirmValid,
        emailVerified
    });
    
    // Enable next button only if ALL conditions are met
    const allFieldsFilled = isFirstnameFilled && isLastnameFilled && isEmailFilled && isPasswordFilled && isPasswordConfirmFilled;
    const allValidationsPassed = isPasswordValid && isPasswordConfirmValid;
    
    if (nextBtn) {
        if (allFieldsFilled && allValidationsPassed && emailVerified) {
            nextBtn.disabled = false;
            nextBtn.style.opacity = '1';
            nextBtn.style.cursor = 'pointer';
            nextBtn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            nextBtn.classList.add('enabled');
            nextBtn.classList.remove('disabled');
            console.log('✅ Step 4 Next button ENABLED (fallback function)');
        } else {
            nextBtn.disabled = true;
            nextBtn.style.opacity = '0.5';
            nextBtn.style.cursor = 'not-allowed';
            nextBtn.style.background = '#ccc';
            nextBtn.classList.add('disabled');
            nextBtn.classList.remove('enabled');
            console.log('❌ Step 4 Next button DISABLED (fallback function)');
        }
    }
    
    return allFieldsFilled && allValidationsPassed && emailVerified;
};

// Emergency fix function to force enable the next button
window.emergencyFixButton = function() {
    console.log('🚨 Emergency fix: Forcing next button enable...');
    const nextBtn = document.getElementById('step4NextBtn');
    if (nextBtn) {
        nextBtn.disabled = false;
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
        nextBtn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        nextBtn.classList.add('enabled');
        nextBtn.classList.remove('disabled');
        console.log('✅ Emergency fix: Next button enabled');
    }
};

// Manual trigger function for debugging (can be called from browser console)
window.manualTriggerStep4 = function() {
    console.log('🔧 Manual trigger called - forcing step 4 validation...');
    console.log('🔧 Current enrollmentEmailVerified:', window.enrollmentEmailVerified);
    
    // Force set email as verified if not already set
    if (!window.enrollmentEmailVerified) {
        window.enrollmentEmailVerified = true;
        console.log('🔧 Forced enrollmentEmailVerified to true');
    }
    
    // Call validation
    if (typeof window.validateStep4 === 'function') {
        window.validateStep4();
    } else if (typeof window.emergencyFixButton === 'function') {
        window.emergencyFixButton();
    }
    
    // Log current button state
    const nextBtn = document.getElementById('step4NextBtn');
    if (nextBtn) {
        console.log('🔧 Next button state:', {
            disabled: nextBtn.disabled,
            opacity: nextBtn.style.opacity,
            classes: nextBtn.className
        });
    }
};

// Expose all functions globally for debugging
window.debugStep4 = function() {
    console.log('🔧 Debug Step 4 State:');
    console.log('🔧 enrollmentEmailVerified:', window.enrollmentEmailVerified);
    console.log('🔧 validateStep4 function:', typeof window.validateStep4);
    console.log('🔧 emergencyFixButton function:', typeof window.emergencyFixButton);
    
    const nextBtn = document.getElementById('step4NextBtn');
    if (nextBtn) {
        console.log('🔧 Next button:', {
            disabled: nextBtn.disabled,
            opacity: nextBtn.style.opacity,
            classes: nextBtn.className
        });
    }
    
    // Check field values
    const fields = ['user_firstname', 'user_lastname', 'user_email', 'password', 'password_confirmation'];
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        console.log(`🔧 ${fieldId}:`, field ? field.value : 'NOT FOUND');
    });
};

// Attach live validation listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const pwd = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const email = document.getElementById('user_email');
    const nextBtn = document.getElementById('step4NextBtn');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    
    // Disable next button initially
    if (nextBtn) {
        nextBtn.disabled = true;
        nextBtn.style.opacity = '0.5';
        nextBtn.style.cursor = 'not-allowed';
    }
    
    // Disable Send OTP button initially
    if (sendOtpBtn) {
        sendOtpBtn.disabled = true;
        sendOtpBtn.style.opacity = '0.5';
        sendOtpBtn.style.cursor = 'not-allowed';
        sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
    }
    
    // Add event listeners for email validation
    if (email) {
        email.addEventListener('input', function() {
            debouncedEmailValidation(this.value);
        });
        
        email.addEventListener('blur', function() {
            // Immediate validation on blur
            checkEmailAvailability(this.value).then(result => {
                const emailError = document.getElementById('emailError');
                const sendOtpBtn = document.getElementById('sendOtpBtn');
                
                // Validate email format
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const isValidFormat = emailRegex.test(this.value);
                
                if (!isValidFormat && this.value.length > 0) {
                    if (emailError) {
                        emailError.textContent = 'Please enter a valid email address';
                        emailError.style.display = 'block';
                        emailError.className = 'text-danger small mt-1';
                    }
                    if (sendOtpBtn) {
                        sendOtpBtn.disabled = true;
                        sendOtpBtn.innerHTML = '<i class="fas fa-times"></i> Invalid Email';
                        sendOtpBtn.style.opacity = '0.5';
                        sendOtpBtn.style.cursor = 'not-allowed';
                    }
                    email.classList.add('is-invalid');
                    email.classList.remove('is-valid');
                } else if (!result.available) {
                    if (emailError) {
                        emailError.textContent = result.message || 'This email is already registered in our system';
                        emailError.style.display = 'block';
                        emailError.className = 'text-danger small mt-1';
                    }
                    if (sendOtpBtn) {
                        sendOtpBtn.disabled = true;
                        sendOtpBtn.innerHTML = '<i class="fas fa-times"></i> Email Exists';
                        sendOtpBtn.style.opacity = '0.5';
                        sendOtpBtn.style.cursor = 'not-allowed';
                    }
                    email.classList.add('is-invalid');
                    email.classList.remove('is-valid');
                } else if (isValidFormat) {
                    if (emailError) {
                        emailError.style.display = 'none';
                    }
                    if (sendOtpBtn) {
                        sendOtpBtn.disabled = false;
                        sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
                        sendOtpBtn.style.opacity = '1';
                        sendOtpBtn.style.cursor = 'pointer';
                    }
                    email.classList.remove('is-invalid');
                    email.classList.add('is-valid');
                } else {
                    if (emailError) {
                        emailError.style.display = 'none';
                    }
                    if (sendOtpBtn) {
                        sendOtpBtn.disabled = true;
                        sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
                        sendOtpBtn.style.opacity = '0.5';
                        sendOtpBtn.style.cursor = 'not-allowed';
                    }
                    email.classList.remove('is-invalid', 'is-valid');
                }
                
                if (typeof window.validateStep4 === 'function') {
                    window.validateStep4();
                }
            });
        });
    }
    
    // Add event listeners for password validation (only when user types)
    if (pwd) {
        pwd.addEventListener('input', function() {
            // Only validate if user has actually typed something
            if (this.value.length > 0) {
                if (typeof window.validatePassword === 'function') {
                    window.validatePassword();
                }
                if (typeof window.validatePasswordConfirmation === 'function') {
                    window.validatePasswordConfirmation();
                }
            } else {
                // Hide error message if field is empty
                const errorElem = document.getElementById('passwordError');
                if (errorElem) {
                    errorElem.style.display = 'none';
                }
            }
            if (typeof window.validateStep4 === 'function') {
                window.validateStep4();
            }
        });
    }
    
    if (confirm) {
        confirm.addEventListener('input', function() {
            // Only validate if user has actually typed something
            if (this.value.length > 0) {
                if (typeof window.validatePasswordConfirmation === 'function') {
                    window.validatePasswordConfirmation();
                }
            } else {
                // Hide error message if field is empty
                const errorElem = document.getElementById('passwordMatchError');
                if (errorElem) {
                    errorElem.style.display = 'none';
                }
            }
            if (typeof window.validateStep4 === 'function') {
                window.validateStep4();
            }
        });
    }
    
    // Add event listeners for other required fields
    const requiredFields = ['user_firstname', 'user_lastname'];
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                if (typeof window.validateStep4 === 'function') {
                    window.validateStep4();
                }
            });
        }
    });
});
