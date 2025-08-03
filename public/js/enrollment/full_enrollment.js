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
    
    if (!email) {
        alert('Please enter your email address first.');
        return;
    }

    sendOtpBtn.disabled = true;
    sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    try {
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
            // Clear previous OTP inputs
            for (let i = 1; i <= 6; i++) {
                const otpInput = document.getElementById(`otp_${i}`);
                if (otpInput) {
                    otpInput.value = '';
                }
            }
            document.getElementById('otp_code_modal').value = '';
            
            // Show the modal instead of inline container
            document.getElementById('otpTargetEmail').textContent = email;
            var otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
            otpModal.show();
            
            sendOtpBtn.innerHTML = '<i class="fas fa-redo"></i> Resend OTP';
            showEnrollmentMessage('OTP sent successfully to your email!', 'success');
            
            // Auto focus on first OTP input when modal is shown
            document.getElementById('otpModal').addEventListener('shown.bs.modal', function () {
                document.getElementById('otp_1').focus();
            });
        } else {
            sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
            showEnrollmentMessage('Failed to send OTP: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error sending OTP:', error);
        sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
        showEnrollmentMessage('Error sending OTP. Please try again.', 'error');
    }
    
    sendOtpBtn.disabled = false;
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
                
                setTimeout(() => {
                    console.log('🔥 Attempting to call validateStep4...');
                    console.log('🔥 window.validateStep4 exists:', typeof window.validateStep4);
                    console.log('🔥 validateStep4 exists:', typeof validateStep4);
                    
                    if (typeof window.validateStep4 === 'function') {
                        console.log('🔥 Calling window.validateStep4()');
                        window.validateStep4();
                    } else if (typeof validateStep4 === 'function') {
                        console.log('🔥 Calling validateStep4()');
                        validateStep4();
                    } else {
                        console.error('❌ validateStep4 function not found!');
                        console.log('🔥 Available window functions:', Object.keys(window).filter(key => key.includes('validate') || key.includes('Step')));
                        
                        // Try emergency fix as fallback
                        if (typeof window.emergencyFixButton === 'function') {
                            console.log('🔥 Trying emergency fix as fallback...');
                            window.emergencyFixButton();
                        }
                    }
                }, 500);
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
