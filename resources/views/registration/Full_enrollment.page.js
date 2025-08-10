(() => {
    if (window.__FULL_ENROLLMENT_INIT__) return;
    window.__FULL_ENROLLMENT_INIT__ = true;

    console.log('🚀 Full Enrollment page script loaded');

    // ===============================================
    // FULL ENROLLMENT FUNCTIONALITY
    // ===============================================
    
    // Terms Modal Function
    function showTermsModal() {
        try {
            const modalElement = document.getElementById('termsModal');
            if (!modalElement) {
                console.error('Terms modal element not found');
                return;
            }
            
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                // Fallback: show modal manually if Bootstrap is not available
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                document.body.classList.add('modal-open');
                
                // Add backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        } catch (error) {
            console.error('Error showing terms modal:', error);
        }
    }
    
    // Terms acceptance function
    function acceptTerms() {
        console.log('🔧 Global acceptTerms function called');
        try {
            const termsCheckbox = document.getElementById('termsCheckbox');
            if (termsCheckbox) {
                // Check the checkbox
                termsCheckbox.checked = true;
                console.log('✅ Terms checkbox checked by global function');
                
                // Enable the submit button
                const submitButton = document.getElementById('submitButton');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('disabled');
                    submitButton.style.opacity = '1';
                    console.log('✅ Submit button enabled by global function');
                }
                
                // Also trigger the form validation if available
                if (typeof validateFormForSubmission === 'function') {
                    console.log('🔧 Calling validateFormForSubmission');
                    validateFormForSubmission();
                }
            } else {
                console.error('❌ Terms checkbox not found in global acceptTerms');
            }
        } catch (err) {
            console.error('❌ Error in global acceptTerms:', err);
        }
    }

    // Account selection function
    function selectAccountOption(hasAccount) {
        console.log('Account option selected:', hasAccount ? 'has account' : 'no account');
        
        if (hasAccount) {
            // This will be populated from server-side template
            const loginUrl = document.querySelector('meta[name="login-url"]')?.content || '/login';
            window.location.href = loginUrl;
            return;
        } else {
            // Continue to step 2 (packages)
            console.log('Continuing to package selection');
            if (typeof animateStepTransition === 'function') {
                animateStepTransition('step-content-1', 'step-content-2');
            }
            if (typeof updateStepper === 'function') {
                updateStepper(2);
            }
        }
    }
    
    // Package selection function
    function selectPackage(packageId, packageName, packagePrice) {
        console.log('=== selectPackage called ===');
        console.log('Package ID:', packageId);
        console.log('Package Name:', packageName);
        console.log('Package Price:', packagePrice);
        
        // Remove selection from all package cards
        document.querySelectorAll('.package-card-pro').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Highlight selected package using data attribute
        const selectedCard = document.querySelector(`[data-package-id="${packageId}"]`);
        if (selectedCard) {
            selectedCard.classList.add('selected');
            console.log('Package card highlighted:', selectedCard);
        } else {
            console.error('Selected package card not found for ID:', packageId);
        }
        
        // Store selection in global variable
        window.selectedPackageId = packageId;
        
        // Store package selection in session storage
        sessionStorage.setItem('selectedPackageId', packageId);
        sessionStorage.setItem('selectedPackageName', packageName);
        sessionStorage.setItem('selectedPackagePrice', packagePrice);
        
        // Update hidden form input
        const packageInput = document.querySelector('input[name="package_id"]');
        if (packageInput) {
            packageInput.value = packageId;
            console.log('Package input updated:', packageInput.value);
        } else {
            console.error('Package input field not found');
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
            console.log('Next button enabled successfully');
        } else {
            console.error('Package next button not found');
        }
        
        console.log('Package selection completed successfully');
    }

    // Learning mode selection function
    function selectLearningMode(mode) {
        console.log('Selecting learning mode:', mode);
        
        // Remove selection from all learning mode cards
        document.querySelectorAll('.selection-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Highlight selected learning mode using data attribute
        const selectedCard = document.querySelector(`[data-mode="${mode}"]`);
        if (selectedCard) {
            selectedCard.classList.add('selected');
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
        
        console.log('Learning mode selected:', mode);
    }

    // Expose functions globally for onclick handlers
    window.showTermsModal = showTermsModal;
    window.acceptTerms = acceptTerms;
    window.selectAccountOption = selectAccountOption;
    window.selectPackage = selectPackage;
    window.selectLearningMode = selectLearningMode;

    // Global registration for debugging
    window.FullEnrollmentPage = {
        init: () => console.log('Full Enrollment page initialized'),
        test: () => console.log('Full Enrollment test function'),
        showTermsModal: showTermsModal,
        acceptTerms: acceptTerms,
        selectAccountOption: selectAccountOption,
        selectPackage: selectPackage,
        selectLearningMode: selectLearningMode
    };

    console.log('✅ Full Enrollment page script initialized');

})();
