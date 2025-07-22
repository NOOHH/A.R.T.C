    @extends('layouts.navbar')

    @section('title', 'Student Registration')
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Full_Enrollment.css') }}">
    <!-- reCAPTCHA -->
    @if(env('RECAPTCHA_SITE_KEY'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif


    <!-- Critical JavaScript functions for immediate availability -->
    <script>
        
    // User authentication state - check both Laravel session and PHP session (MOVED TO TOP)
    @php
        $userLoggedIn = session('user_id') || (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']));
        $userId = session('user_id') ?: (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '');
        $userName = session('user_name') ?: (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '');
        $userFirstname = session('user_firstname') ?: (isset($_SESSION['user_firstname']) ? $_SESSION['user_firstname'] : '');
        $userLastname = session('user_lastname') ?: (isset($_SESSION['user_lastname']) ? $_SESSION['user_lastname'] : '');
        $userEmail = session('user_email') ?: (isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '');
    @endphp

    // Declare isUserLoggedIn FIRST to avoid temporal dead zone
    const isUserLoggedIn = {{ $userLoggedIn ? 'true' : 'false' }};
    const loggedInUserId = '{{ $userId }}';
    const loggedInUserName = '{{ $userName }}';
    const loggedInUserFirstname = '{{ $userFirstname }}';
    const loggedInUserLastname = '{{ $userLastname }}';
    const loggedInUserEmail = '{{ $userEmail }}';

    console.log('Session check:', {
        isUserLoggedIn,
        loggedInUserId,
        loggedInUserName,
        loggedInUserFirstname,
        loggedInUserLastname,
        loggedInUserEmail
    });
        
    // Global variables (declare after isUserLoggedIn to avoid temporal dead zone)
    let currentStep = isUserLoggedIn ? 1 : 1;  // Start at step 1 for both, but logged-in users skip account check step
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

    // Define critical functions immediately for onclick handlers
    
    // New function to handle account selection in Step 1
    function selectAccountOption(hasAccount) {
        console.log('Account option selected:', hasAccount ? 'has account' : 'no account');
        
        if (hasAccount) {
            // Redirect to login page
            window.location.href = "{{ route('login') }}";
            return;
        } else {
            // Continue to step 2 (packages)
            console.log('Continuing to package selection');
            animateStepTransition('step-content-1', 'step-content-2');
            currentStep = 2;
            updateStepper(currentStep);
        }
    }
    
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
        
        // Learning mode selection complete - batch selection will be handled in Step 4
        console.log('Learning mode selected:', mode);
        
        // Update batch visibility immediately if on form step
        const batchContainer = document.getElementById('batchSelectionContainer');
        if (batchContainer) {
            const programSelect = document.querySelector('select[name="program_id"]');
            if (programSelect && programSelect.value) {
                loadBatchesForProgram(programSelect.value);
            }
        }
        
        // Enable next button with proper styling
        const nextBtn = document.getElementById('learningModeNextBtn');
        if (nextBtn) {
            nextBtn.disabled = false;
            nextBtn.style.opacity = '1';
            nextBtn.style.cursor = 'pointer';
            nextBtn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            nextBtn.classList.add('enabled');
            console.log('Next button enabled and styled');
        } else {
            console.error('Next button not found');
        }
    }


    function nextStep() {
        console.log('=== nextStep called, current step:', currentStep, '===');
        console.log('isUserLoggedIn:', isUserLoggedIn);

        if (currentStep === 1) {
            if (isUserLoggedIn) {
                // For logged-in users, step 1 is package selection
                const packageInput = document.querySelector('input[name="package_id"]');
                const sessionPackageId = sessionStorage.getItem('selectedPackageId');
                console.log('Checking package selection:', {
                    selectedPackageId,
                    windowSelectedPackageId: window.selectedPackageId,
                    packageInputValue: packageInput?.value,
                    sessionPackageId
                });
                
                if (!selectedPackageId && !window.selectedPackageId && !packageInput?.value && !sessionPackageId) {
                    showWarning('Please select a package before proceeding.');
                    return;
                }
                if (!selectedPackageId && (window.selectedPackageId || packageInput?.value || sessionPackageId)) {
                    selectedPackageId = window.selectedPackageId || packageInput?.value || sessionPackageId;
                }
                
                console.log('Logged-in user: transitioning from step 1 (packages) to step 2 (learning mode)');
                animateStepTransition('step-content-1','step-content-2');
                currentStep = 2;
                updateStepper(currentStep);
            } else {
                // For non-logged-in users, step 1 is account check - handled by selectAccountOption
                console.log('Step 1 should be handled by selectAccountOption');
                return;
            }
        } else if (currentStep === 2) {
            if (isUserLoggedIn) {
                // For logged-in users, step 2 is learning mode selection
                const learningModeValue = document.getElementById('learning_mode')?.value;
                console.log('Learning mode value:', learningModeValue);
                
                if (!learningModeValue) {
                    showWarning('Please select a learning mode before proceeding.');
                    return;
                }
                
                console.log('Logged-in user: transitioning from step 2 (learning mode) to step 3 (form)');
                animateStepTransition('step-content-2', 'step-content-3');
                currentStep = 3;
                updateStepper(currentStep);
                setTimeout(() => {
                    // Prefill user data for logged-in users
                    fillLoggedInUserData();
                    
                    // Load batches when entering form step
                    const programSelect = document.getElementById('programSelect');
                    if (programSelect && programSelect.value) {
                        loadBatchesForProgram(programSelect.value);
                    }
                }, 300);
            } else {
                // For non-logged-in users, step 2 is package selection
                const packageInput = document.querySelector('input[name="package_id"]');
                const sessionPackageId = sessionStorage.getItem('selectedPackageId');
                console.log('Checking package selection:', {
                    selectedPackageId,
                    windowSelectedPackageId: window.selectedPackageId,
                    packageInputValue: packageInput?.value,
                    sessionPackageId
                });
                
                if (!selectedPackageId && !window.selectedPackageId && !packageInput?.value && !sessionPackageId) {
                    showWarning('Please select a package before proceeding.');
                    return;
                }
                if (!selectedPackageId && (window.selectedPackageId || packageInput?.value || sessionPackageId)) {
                    selectedPackageId = window.selectedPackageId || packageInput?.value || sessionPackageId;
                }
                
                console.log('Non-logged-in user: transitioning from step 2 (packages) to step 3 (learning mode)');
                animateStepTransition('step-content-2','step-content-3');
                currentStep = 3;
                updateStepper(currentStep);
            }
        } else if (currentStep === 3) {
            if (isUserLoggedIn) {
                // For logged-in users, step 3 is the final form - no next step needed unless submitting
                console.log('Logged-in user at final step (form)');
                return;
            } else {
                // For non-logged-in users, step 3 is learning mode selection
                const learningModeValue = document.getElementById('learning_mode')?.value;
                console.log('Learning mode value:', learningModeValue);
                
                if (!learningModeValue) {
                    showWarning('Please select a learning mode before proceeding.');
                    return;
                }
                
                console.log('Non-logged-in user: transitioning from step 3 (learning mode) to step 4 (account)');
                animateStepTransition('step-content-3', 'step-content-4');
                currentStep = 4;
                updateStepper(currentStep);
            }
        } else if (currentStep === 4 && !isUserLoggedIn) {
            // Only for non-logged-in users: step 4 is Account, step 5 is Form
            if (!validateStep4()) {
                showWarning('Please fill in all required fields correctly.');
                return;
            }
            copyAccountDataToStudentForm();
            console.log('Non-logged in user: transitioning from step 4 to step 5');
            animateStepTransition('step-content-4', 'step-content-5');
            currentStep = 5;
            updateStepper(currentStep);
            setTimeout(() => {
                copyAccountDataToStudentForm();
                // Load batches when entering step 5
                const programSelect = document.getElementById('programSelect');
                if (programSelect && programSelect.value) {
                    loadBatchesForProgram(programSelect.value);
                }
            }, 300);
            console.log('Step transition completed. New currentStep:', currentStep);
        }
        // updateProgressBar(); <-- Only keep this in animateStepTransition
    }


    function prevStep() {
        console.log('prevStep called, current step:', currentStep);

        if (isUserLoggedIn) {
            // For logged-in users: 1 (Packages) -> 2 (Learning Mode) -> 3 (Form)
            if (currentStep === 3) {
                // From form, go back to learning mode
                animateStepTransition('step-content-3', 'step-content-2', true);
                currentStep = 2;
                updateStepper(currentStep);
            } else if (currentStep === 2) {
                // From learning mode, go back to packages
                animateStepTransition('step-content-2', 'step-content-1', true);
                currentStep = 1;
                updateStepper(currentStep);
            }
        } else {
            // For non-logged-in users: 1 (Account Check) -> 2 (Packages) -> 3 (Learning Mode) -> 4 (Account) -> 5 (Form)
            if (currentStep === 5) {
                // From form, go back to account registration
                animateStepTransition('step-content-5', 'step-content-4', true);
                currentStep = 4;
                updateStepper(currentStep);
                // Trigger validation after going back to step 4
                setTimeout(() => {
                    validateStep4();
                    console.log('Step 4 validation triggered after going back');
                }, 500);
            } else if (currentStep === 4) {
                // From account registration, go back to learning mode
                animateStepTransition('step-content-4', 'step-content-3', true);
                currentStep = 3;
                updateStepper(currentStep);
            } else if (currentStep === 3) {
                // From learning mode, go back to package selection
                animateStepTransition('step-content-3', 'step-content-2', true);
                currentStep = 2;
                updateStepper(currentStep);
            } else if (currentStep === 2) {
                // From package selection, go back to account check
                animateStepTransition('step-content-2', 'step-content-1', true);
                currentStep = 1;
                updateStepper(currentStep);
            }
        }
    }

    function updateStepper(currentStep) {
        console.log('=== updateStepper called with currentStep:', currentStep, '===');
        // Determine total steps based on login status
        const totalSteps = isUserLoggedIn ? 3 : 5; // Logged-in: 3 steps, Non-logged-in: 5 steps
        console.log('Total steps:', totalSteps, '(based on isUserLoggedIn:', isUserLoggedIn, ')');
        
        // Update step states
        for (let i = 1; i <= totalSteps; i++) {
            let step = document.getElementById('step-' + i);
            console.log(`Step ${i}:`, step ? 'found' : 'not found');
            if (!step) continue;
            
            if (i < currentStep) {
                step.classList.add('completed');
                step.classList.remove('active');
                console.log(`Step ${i}: marked as completed`);
            } else if (i === currentStep) {
                step.classList.add('active');
                step.classList.remove('completed');
                console.log(`Step ${i}: marked as active`);
            } else {
                step.classList.remove('active', 'completed');
                console.log(`Step ${i}: cleared active/completed`);
            }
        }
        
        // Update progress bar
        const progressBar = document.getElementById('progressBar');
        if (progressBar) {
            const percentage = (currentStep / totalSteps) * 100;
            progressBar.style.width = percentage + '%';
            console.log('Progress bar updated to:', percentage + '%');
        } else {
            console.warn('Progress bar element not found');
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
            if (batchContainer) {
                batchContainer.style.display = 'none';
                console.log('Batch container hidden - asynchronous mode or no program selected');
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
            
            // Handle the new API response format
            const batches = response_data.batches || [];
            const auto_create = response_data.auto_create || false;
            const message = response_data.message || '';
            
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
                                        ${isOngoing ? '<span class="ongoing-indicator"> </span>' : ''}
                                    </div>
                                    <div class="batch-status ${statusClass}">${statusText}</div>
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
                            
                            <div class="alert alert-info">
                                <i class="bi bi-lightbulb me-2"></i>
                                <strong>Recommendation:</strong> Contact the instructor or support team for a catch-up plan before joining.
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
        console.log('=== Copying account data to student form ===');
        
        if (isUserLoggedIn) {
            // If user is logged in, use session data for step 4 fields
            const firstnameField = document.querySelector('input[name="firstname"]');
            const lastnameField = document.querySelector('input[name="lastname"]');
            const emailField = document.querySelector('input[name="email"]');
            
            console.log('Logged in user data:', {
                loggedInUserFirstname,
                loggedInUserLastname,
                loggedInUserEmail
            });
            
            if (firstnameField && loggedInUserFirstname) {
                firstnameField.value = loggedInUserFirstname;
                console.log('✅ Filled firstname from session:', loggedInUserFirstname);
            } else {
                console.log('❌ Could not fill firstname:', { firstnameField: !!firstnameField, loggedInUserFirstname });
            }
            
            if (lastnameField && loggedInUserLastname) {
                lastnameField.value = loggedInUserLastname;
                console.log('✅ Filled lastname from session:', loggedInUserLastname);
            } else {
                console.log('❌ Could not fill lastname:', { lastnameField: !!lastnameField, loggedInUserLastname });
            }
            
            if (emailField && loggedInUserEmail) {
                emailField.value = loggedInUserEmail;
                console.log('✅ Filled email from session:', loggedInUserEmail);
            } else {
                console.log('❌ Could not fill email:', { emailField: !!emailField, loggedInUserEmail });
            }
        } else {
            // Copy from step 4 account registration to step 5 student form (new step numbers)
            console.log('Non-logged in user - copying from account registration step');
            
            const step4Fields = {
                'user_firstname': 'firstname',
                'user_lastname': 'lastname', 
                'user_email': 'email'
            };
            
            Object.keys(step4Fields).forEach(step4Field => {
                const step5Field = step4Fields[step4Field];
                const sourceField = document.querySelector(`input[name="${step4Field}"]`);
                const targetField = document.querySelector(`input[name="${step5Field}"]`);
                
                console.log(`Checking field mapping: ${step4Field} -> ${step5Field}`, {
                    sourceField: !!sourceField,
                    targetField: !!targetField,
                    sourceValue: sourceField?.value
                });
                
                if (sourceField && targetField && sourceField.value.trim()) {
                    targetField.value = sourceField.value.trim();
                    console.log(`✅ Copied ${step4Field} -> ${step5Field}: ${sourceField.value}`);
                    
                    // Trigger any validation or change events
                    const event = new Event('input', { bubbles: true });
                    targetField.dispatchEvent(event);
                } else {
                    console.log(`❌ Could not copy ${step4Field} -> ${step5Field}:`, {
                        sourceExists: !!sourceField,
                        targetExists: !!targetField,
                        hasValue: sourceField?.value?.trim() || 'empty'
                    });
                }
            });
        }
        
        console.log('=== Account data copy completed ===');
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
        
        console.log('=== File Upload Started ===');
        console.log('Field:', fieldName, 'File:', file.name);
        
        // CRITICAL FIX: Store original file reference to prevent losing it
        const originalFileName = file.name;
        const originalFileSize = file.size;
        
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
            // DON'T clear the file input here - let user keep their selection
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
                // DON'T clear file - keep the uploaded file
                return;
            }
            
            console.log('File validation response:', data); // Debug log
            
            if (data.success) {
                console.log('✅ File validation successful');
                showSuccessModal('Document validated successfully!');
                
                // CRITICAL FIX: Store the file path for form submission
                if (data.file_path) {
                    // Always create/update hidden input inside the form
                    let form = inputElement.closest('form');
                    let hiddenFileInput = form.querySelector(`input[name="${fieldName}_path"]`);
                    if (!hiddenFileInput) {
                        hiddenFileInput = document.createElement('input');
                        hiddenFileInput.type = 'hidden';
                        hiddenFileInput.name = fieldName + '_path';
                        form.appendChild(hiddenFileInput);
                    }
                    hiddenFileInput.value = data.file_path;
                    console.log('✅ Stored file path for', fieldName, ':', data.file_path);
                }
                
                // FIXED: Handle program suggestions
                if (data.suggestions && data.suggestions.length > 0) {
                    console.log('✅ Found program suggestions:', data.suggestions);
                    showProgramSuggestions(data.suggestions);
                } else {
                    console.log('No program suggestions found');
                }
                
                // Handle education level detection
                if (data.certificate_level) {
                    console.log('✅ Education level detected:', data.certificate_level);
                    handleEducationLevelDetection(data.certificate_level);
                }
                
                // Add success styling to the input
                inputElement.classList.add('is-valid');
                inputElement.classList.remove('is-invalid');
                
            } else {
                console.error('❌ File validation failed:', data);
                
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
            console.error('❌ File validation error:', error);
            
            // FALLBACK: Even on network error, allow file upload to proceed
            showWarningModal('Network error occurred during file validation. Your file will still be uploaded and processed manually if needed.');
            
            // Add warning styling but still allow form submission
            inputElement.classList.add('is-warning');
            inputElement.classList.remove('is-invalid', 'is-valid');
            
            console.log('File kept in input despite network error - will be processed by backend');
        });
        
        console.log('=== File Upload Process Initiated ===');
    }

    // Show program suggestions in dropdown - ENHANCED VERSION
    function showProgramSuggestions(suggestions) {
        console.log('=== Showing Program Suggestions ===');
        console.log('Suggestions received:', suggestions);
        
        const programSelect = document.getElementById('programSelect');
        if (!programSelect) {
            console.error('Program select element not found');
            return;
        }
        
        // Clear existing suggestions
        const existingSuggestions = programSelect.querySelectorAll('.suggestion-option');
        existingSuggestions.forEach(option => {
            console.log('Removing existing suggestion:', option.textContent);
            option.remove();
        });
        
        // Add suggestion header if suggestions exist
        if (suggestions.length > 0) {
            console.log('Adding suggestions header and options...');
            
            // Create and add header option
            const headerOption = document.createElement('option');
            headerOption.disabled = true;
            headerOption.textContent = '--- Suggested Programs (Based on Your Document) ---';
            headerOption.className = 'suggestion-header';
            headerOption.style.fontWeight = 'bold';
            headerOption.style.color = '#007bff';
            
            // Insert after the default "Select Program" option
            if (programSelect.children.length > 0) {
                programSelect.insertBefore(headerOption, programSelect.children[1]);
            } else {
                programSelect.appendChild(headerOption);
            }
            
            // Add each suggestion
            suggestions.forEach((suggestion, index) => {
                const suggestionOption = document.createElement('option');
                suggestionOption.value = suggestion.program_id || suggestion.id;
                suggestionOption.textContent = `⭐ ${suggestion.program_name || suggestion.name}`;
                suggestionOption.className = 'suggestion-option';
                suggestionOption.style.backgroundColor = '#e3f2fd';
                suggestionOption.style.fontWeight = '500';
                suggestionOption.dataset.suggestion = 'true';
                
                console.log(`Adding suggestion ${index + 1}:`, suggestion.program_name || suggestion.name);
                
                // Insert after header
                const headerIndex = Array.from(programSelect.children).indexOf(headerOption);
                if (headerIndex !== -1 && headerIndex + 1 + index < programSelect.children.length) {
                    programSelect.insertBefore(suggestionOption, programSelect.children[headerIndex + 1 + index]);
                } else {
                    programSelect.appendChild(suggestionOption);
                }
            });
            
            // Highlight the dropdown to draw attention
            programSelect.style.borderColor = '#007bff';
            programSelect.style.boxShadow = '0 0 0 0.2rem rgba(0, 123, 255, 0.25)';
            
            // Show notification modal
            showInfoModal(`🎯 Great! We found ${suggestions.length} program(s) that match your uploaded certificate. Check the suggested programs (marked with ⭐) at the top of the Program dropdown list.`);
            
            // Auto-scroll to the program select field
            setTimeout(() => {
                programSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Flash animation to draw attention
                let flashCount = 0;
                const flashInterval = setInterval(() => {
                    programSelect.style.backgroundColor = flashCount % 2 === 0 ? '#fff3cd' : 'white';
                    flashCount++;
                    if (flashCount >= 6) {
                        clearInterval(flashInterval);
                        programSelect.style.backgroundColor = 'white';
                    }
                }, 300);
            }, 1000);
            
            console.log('✅ Program suggestions added successfully');
        } else {
            console.log('No suggestions to display');
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

    function showWarningModal(message) {
        showModal('Warning', message, 'warning');
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

    // Validate referral code
    function validateReferralCode() {
        const referralInput = document.getElementById('referral_code');
        const referralCode = referralInput.value.trim();
        const validateBtn = document.getElementById('validateReferralBtn');
        const errorDiv = document.getElementById('referralCodeError');
        const successDiv = document.getElementById('referralCodeSuccess');
        
        // Clear previous messages
        errorDiv.style.display = 'none';
        successDiv.style.display = 'none';
        
        if (!referralCode) {
            showReferralError('Please enter a referral code');
            return;
        }
        
        // Show loading state
        validateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validating...';
        validateBtn.disabled = true;
        
        // Make AJAX request to validate referral code
        fetch('/api/validate-referral-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                referral_code: referralCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                showReferralSuccess(`Valid referral code from ${data.referrer_name} (${data.referrer_type})`);
                referralInput.classList.add('is-valid');
                referralInput.classList.remove('is-invalid');
            } else {
                showReferralError(data.message || 'Invalid referral code');
                referralInput.classList.add('is-invalid');
                referralInput.classList.remove('is-valid');
            }
        })
        .catch(error => {
            console.error('Error validating referral code:', error);
            showReferralError('Error validating referral code. Please try again.');
            referralInput.classList.add('is-invalid');
            referralInput.classList.remove('is-valid');
        })
        .finally(() => {
            // Reset button state
            validateBtn.innerHTML = '<i class="fas fa-check"></i> Validate';
            validateBtn.disabled = false;
        });
    }
    
    function showReferralError(message) {
        const errorDiv = document.getElementById('referralCodeError');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
    
    function showReferralSuccess(message) {
        const successDiv = document.getElementById('referralCodeSuccess');
        successDiv.textContent = message;
        successDiv.style.display = 'block';
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
                    
                    // Ensure we always have an array of requirement objects
                    let requirementsArray = [];
                    
                    if (Array.isArray(fileRequirements)) {
                        // Already an array - use it directly
                        requirementsArray = fileRequirements;
                    } else if (typeof fileRequirements === 'object' && fileRequirements !== null) {
                        // Check if it's an object with field names as keys
                        const keys = Object.keys(fileRequirements);
                        if (keys.length > 0 && typeof fileRequirements[keys[0]] === 'object') {
                            // Convert object format: {fieldName: {config}, ...}
                            requirementsArray = Object.entries(fileRequirements).map(([fieldName, config]) => ({
                                field_name: fieldName.replace(/\s+/g, '_'),
                                display_name: fieldName,
                                is_required: config.required !== undefined ? config.required : true,
                                type: config.type || 'file',
                                description: config.description || ''
                            }));
                        } else {
                            // Single requirement object
                            requirementsArray = [fileRequirements];
                        }
                    }
                    
                    console.log('Final requirements array:', requirementsArray);
                    console.log('Array length:', requirementsArray.length);
                    
                    // Use a traditional for loop to ensure proper iteration
                    for (let i = 0; i < requirementsArray.length; i++) {
                        const requirement = requirementsArray[i];
                        console.log('Processing requirement:', requirement);
                        
                        const fieldDiv = document.createElement('div');
                        fieldDiv.className = 'form-group mb-3';
                        
                        // Ensure we're getting the right values
                        const fieldName = (requirement.field_name || requirement.document_type || 'unknown_' + i).toString();
                        const displayName = (requirement.display_name || requirement.description || requirement.field_name || requirement.document_type || 'Unknown Document').toString();
                        const isRequired = requirement.is_required !== undefined ? requirement.is_required : true;
                        const fileType = requirement.type || 'file';
                        
                        console.log('Field details:', { fieldName, displayName, isRequired, fileType });
                        
                        // Set appropriate file accept types
                        let acceptTypes = '.jpg,.jpeg,.png,.pdf';
                        if (fileType === 'image') {
                            acceptTypes = '.jpg,.jpeg,.png';
                        } else if (fileType === 'pdf') {
                            acceptTypes = '.pdf';
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
                                Upload your ${displayName} (${fileType.toUpperCase()} only)
                                ${requirement.description ? ' - ' + requirement.description : ''}
                            </small>
                        `;
                        // Re-attach any existing hidden file path input for this field
                        const form = document.getElementById('enrollmentForm');
                        const existingHidden = form.querySelector(`input[name="${fieldName}_path"]`);
                        if (existingHidden) {
                            fieldDiv.appendChild(existingHidden);
                        }
                        requirementsContainer.appendChild(fieldDiv);
                    }
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

    function removeUploadedFile(fieldName) {
        // Remove hidden input and uploaded info
        const form = document.getElementById('enrollmentForm');
        const hiddenInput = form.querySelector(`input[name='${fieldName}_path']`);
        if (hiddenInput) hiddenInput.remove();
        const uploadedInfo = document.getElementById('uploaded-' + fieldName);
        if (uploadedInfo) uploadedInfo.style.display = 'none';
    }

    </script>
    
    <style>
    /* Referral Code Field Styles */
    .referral-input-group {
        display: flex;
        position: relative;
    }
    
    .referral-input-group input {
        flex: 1;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: none;
    }
    
    .btn-validate-referral {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: 1px solid #28a745;
        color: white;
        padding: 0.375rem 0.75rem;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
        transition: all 0.3s ease;
        white-space: nowrap;
        cursor: pointer;
        font-size: 0.875rem;
    }
    
    .btn-validate-referral:hover {
        background: linear-gradient(135deg, #218838 0%, #1dd1a1 100%);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
    }
    
    .btn-validate-referral:disabled {
        background: #6c757d;
        border-color: #6c757d;
        transform: none;
        box-shadow: none;
        cursor: not-allowed;
    }
    
    .success-message {
        color: #28a745;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
    }
    
    .success-message::before {
        content: '✓';
        margin-right: 0.5rem;
        font-weight: bold;
    }
    
    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
    }
    
    .error-message::before {
        content: '⚠';
        margin-right: 0.5rem;
    }
    
    .form-control.is-valid {
        border-color: #28a745;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%2328a745' d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/%3e%3cpath fill='%2328a745' d='M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%23dc3545' d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/%3e%3cpath fill='%23dc3545' d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .form-control.is-warning {
        border-color: #ffc107;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%23ffc107' d='M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    /* Account Check Step Styles */
    .account-check-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .account-option-card:hover .card {
        border-color: #007bff !important;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
        transform: translateY(-2px);
    }
    
    .account-option-card .card {
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
    }
    
    .account-option-card .icon-container {
        margin-bottom: 1rem;
    }
    
    .question-section {
        margin-bottom: 2rem;
    }
    </style>
    @endpush

    @section('content')
    <div class="form-container">
        <div class="form-wrapper">
            <!-- Stepper Progress -->
            <div class="stepper-progress">
                <div class="stepper">
                    <div class="bar">
                        <div class="progress" id="progressBar" style="width: {{ $userLoggedIn ? '25%' : '20%' }};"></div>
                    </div>
                    <div class="step {{ !$userLoggedIn ? 'active' : '' }}" id="step-1">
                        <div class="circle">1</div>
                        <div class="label">Account Check</div>
                    </div>
                    <div class="step {{ $userLoggedIn ? 'active' : '' }}" id="step-2">
                        <div class="circle">2</div>
                        <div class="label">Packages</div>
                    </div>
                    <div class="step" id="step-3">
                        <div class="circle">3</div>
                        <div class="label">Learning Mode</div>
                    </div>
                    @if(!$userLoggedIn)
                    <div class="step" id="step-4">
                        <div class="circle">4</div>
                        <div class="label">Account</div>
                    </div>
                    <div class="step" id="step-5">
                        <div class="circle">5</div>
                        <div class="label">Form</div>
                    </div>
                    @else
                    <div class="step" id="step-4">
                        <div class="circle">4</div>
                        <div class="label">Form</div>
                    </div>
                    @endif
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
                <!-- Removed duplicate hidden Start_Date field - using the visible date input instead -->

                <!-- Step 1: Account Check -->
                <div class="step-content {{ !$userLoggedIn ? 'active' : '' }}" id="step-content-1">
                    <div class="step-header mb-4">
                        <h2 class="fw-bold text-center" style="font-size:2.5rem;">Welcome to Registration</h2>
                        <p class="text-center text-muted" style="font-size:1.15rem;">Let's get you started with your enrollment</p>
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
                <div class="step-content {{ $userLoggedIn ? 'active' : '' }}" id="step-content-2">
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
                                                 onclick="selectPackage('{{ $package->package_id }}', '{{ addslashes($package->package_name) }}', '{{ $package->amount }}')"
                                                 data-package-id="{{ $package->package_id }}">
                                                <div class="card-body text-center">
                                                    <h4 class="fw-bold mb-2">{{ $package->package_name }}</h4>
                                                    <div class="text-primary fw-bold" style="font-size:2rem;">₱{{ number_format($package->amount, 2) }}</div>
                                                    <p class="text-muted mb-3" style="min-height:2rem;">{{ $package->description ?? 'No description yet.' }}</p>
                                                    <ul class="list-unstyled text-start mx-auto" style="max-width:220px;">
                                                        <li><i class="bi bi-check2 text-success"></i> Full program access</li>
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

                    <!-- Selected Package Display -->
                    <div id="selectedPackageDisplay" class="selected-package-summary mt-3 mb-4" style="text-align:center; display:none;">
                        <span class="ms-3">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            Selected Package: <strong id="selectedPackageName">None</strong>
                        </span>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <button type="button" class="btn btn-lg btn-primary next-btn-pro" onclick="nextStep()" disabled id="packageNextBtn">
                            NEXT: SELECT LEARNING MODE <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

        <!-- Step 3: Learning Mode Selection -->
        <div class="step-content" id="step-content-3">
            <div class="step-header">
                <h2>Choose Learning Mode</h2>
                <p>Select how you'd like to take your classes</p>
            </div>
            
            <div class="row justify-content-center">
                @if(isset($fullPlan) && $fullPlan->enable_synchronous)
                <div class="col-md-5 mb-4">
                    <div class="card selection-card h-100" onclick="selectLearningMode('synchronous')" data-mode="synchronous" style="cursor:pointer;">
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
                @endif
                
                @if(isset($fullPlan) && $fullPlan->enable_asynchronous)
                <div class="col-md-5 mb-4">
                    <div class="card selection-card h-100" onclick="selectLearningMode('asynchronous')" data-mode="asynchronous" style="cursor:pointer;">
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
                @endif
            </div>

            <!-- Selected Learning Mode Display -->
            <div id="selectedLearningModeDisplay" class="selected-display mt-3 mb-4" style="text-align:center; display:none;">
                <span>
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    Selected Mode: <strong id="selectedLearningModeName">None</strong>
                </span>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="prevStep()">
                    <i class="bi bi-arrow-left me-2"></i> Previous
                </button>
                <button type="button" class="btn btn-primary btn-lg" onclick="nextStep()" disabled id="learningModeNextBtn">
                    NEXT: ACCOUNT <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

    <!-- Step 4: Account Registration (only for non-logged users) -->
    <div class="step-content" id="step-content-4">
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
                <button type="button" onclick="nextStep()" id="step4NextBtn" disabled class="btn btn-primary btn-lg">
                    Next <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Step 4/5: Student Registration - Dynamic ID based on login status -->
    <div class="step-content" id="{{ $userLoggedIn ? 'step-content-4' : 'step-content-5' }}">
        <div class="student-step-card">
            <div class="step-header">
                <h2><i class="bi bi-person-lines-fill me-2"></i>Complete Your Registration</h2>
                <p>Fill in your personal and academic information.</p>
            </div>

            <!-- Dynamic Form Fields -->
            @if(isset($formRequirements) && $formRequirements->count() > 0)
                @php 
                    $currentSection = null;
                    $hasFirstname = false;
                    $hasLastname = false;
                    if (isset($formRequirements)) {
                        foreach ($formRequirements as $field) {
                            if ($field->field_name === 'firstname') $hasFirstname = true;
                            if ($field->field_name === 'lastname') $hasLastname = true;
                        }
                    }
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
                                    <div class="form-group" id="file-group-{{ $field->field_name }}">
                                        <label for="{{ $field->field_name }}">{{ $field->field_label }}</label>
                                        <input type="file" name="{{ $field->field_name }}" id="{{ $field->field_name }}" onchange="handleFileUpload(this)">
                                        <div class="uploaded-file-info" id="uploaded-{{ $field->field_name }}" style="display:none;"></div>
                                    </div>
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
                @if(!$hasFirstname)
                <div class="form-group">
                    <label for="firstname" style="font-weight:700;">
                        <i class="bi bi-person me-2"></i>First Name
                        <span class="required">*</span>
                    </label>
                    <input type="text" name="firstname" id="firstname" class="form-control" required>
                </div>
                @endif
                @if(!$hasLastname)
                <div class="form-group">
                    <label for="lastname" style="font-weight:700;">
                        <i class="bi bi-person me-2"></i>Last Name
                        <span class="required">*</span>
                    </label>
                    <input type="text" name="lastname" id="lastname" class="form-control" required>
                </div>
                @endif
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
                                    data-file-requirements="{{ json_encode($level->getFileRequirementsForPlan($enrollmentType ?? 'full')) }}">
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

            <div class="form-group" style="margin-top:2rem;">
                <label for="start_date_input" style="font-size:1.17rem;font-weight:700;"><i class="bi bi-calendar-event me-2"></i>Start Date</label>
                <input type="date" name="Start_Date" id="start_date_input" class="form-control"
                    value="{{ $student->start_date ?? old('Start_Date') }}" 
                    onchange="updateHiddenStartDate()" required>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="termsCheckbox" name="terms_accepted" required>
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
        console.log('=== DOM Content Loaded - Initializing registration form ===');
        console.log('Current step on load:', currentStep);
        console.log('Is user logged in:', isUserLoggedIn);
        
        // Initialize stepper to ensure correct step display
        updateStepper(currentStep);
        
        // Determine which step content to show based on login status
        const allSteps = document.querySelectorAll('.step-content');
        allSteps.forEach((step, index) => {
            step.classList.remove('active');
        });
        
        if (isUserLoggedIn) {
            // For logged-in users: start with step 1 (packages)
            console.log('Logged-in user: Showing package selection (step-content-1)');
            const packageStep = document.getElementById('step-content-1');
            if (packageStep) {
                packageStep.classList.add('active');
                console.log('Package selection step activated');
            }
            
            // Hide account check step for logged-in users
            const accountCheckStep = document.getElementById('step-content-0'); // If it exists
            if (accountCheckStep) {
                accountCheckStep.style.display = 'none';
                console.log('Account check step hidden for logged-in user');
            }
            
            // Prefill user data if form fields are already available
            setTimeout(() => {
                fillLoggedInUserData();
            }, 100);
        } else {
            // For non-logged-in users: start with step 1 (account check)
            console.log('Non-logged-in user: Showing account check (step-content-1)');
            const accountCheckStep = document.getElementById('step-content-1');
            if (accountCheckStep) {
                accountCheckStep.classList.add('active');
                console.log('Account check step activated');
            }
        }
        
        updateHiddenStartDate();
        updateHiddenProgramId();
        updateProgressBar(); // Initialize progress bar
        
        // Check initial visibility after CSS has had time to apply
        setTimeout(() => {
            const activeStep = document.querySelector('.step-content.active');
            if (activeStep) {
                const stepVisible = window.getComputedStyle(activeStep).display !== 'none';
                const stepOpacity = window.getComputedStyle(activeStep).opacity;
                console.log('Active step check - Visible:', stepVisible, 'Opacity:', stepOpacity);
                console.log('Active step ID:', activeStep.id);
            }
        }, 200);
        
        // Add event listeners for changes
    const programSelect = document.getElementById('programSelect');
    if (programSelect) {
    // on dropdown change, pass the new ID into your loader
    programSelect.addEventListener('change', () => {
        updateHiddenProgramId();
        loadBatchesForProgram(programSelect.value);
    });
    // also run it once on page‐load if a program is already selected
    if (programSelect.value) {
        loadBatchesForProgram(programSelect.value);
    }
    }


        
        // Add validation event listeners for Step 3
        const firstnameField = document.getElementById('user_firstname');
        const lastnameField = document.getElementById('user_lastname');
        const emailField = document.getElementById('user_email');
        const passwordField = document.getElementById('password');
        const passwordConfirmField = document.getElementById('password_confirmation');
        
        // Debounce timer for validation
        let validationTimer = null;
        
        function debouncedValidateStep3() {
            clearTimeout(validationTimer);
            validationTimer = setTimeout(validateStep3, 500); // Wait 500ms after user stops typing
        }
        
        function debouncedValidateStep4() {
            clearTimeout(validationTimer);
            validationTimer = setTimeout(validateStep4, 500); // Wait 500ms after user stops typing
        }
        
        // Use appropriate validation function based on login status and current step
        const validationFunction = isUserLoggedIn ? debouncedValidateStep3 : debouncedValidateStep4;
        
        if (firstnameField) {
            firstnameField.addEventListener('input', validationFunction);
        }
        if (lastnameField) {
            lastnameField.addEventListener('input', validationFunction);
        }
        if (emailField) {
            emailField.addEventListener('input', function() {
                setTimeout(validateEmail, 300);
                validationFunction();
            });
        }
        if (passwordField) {
            passwordField.addEventListener('input', function() {
                setTimeout(validatePassword, 50);
                validationFunction();
            });
        }
        if (passwordConfirmField) {
            passwordConfirmField.addEventListener('input', function() {
                setTimeout(validatePasswordConfirmation, 50);
                validationFunction();
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

    // Function to validate all Step 4 (Account Registration) fields - updated function name for new step structure
    function validateStep4() {
        const firstnameField = document.getElementById('user_firstname');
        const lastnameField = document.getElementById('user_lastname');
        const emailField = document.getElementById('user_email');
        const passwordField = document.getElementById('password');
        const passwordConfirmField = document.getElementById('password_confirmation');
        const nextBtn = document.getElementById('step4NextBtn'); // Updated button ID
        
        // Don't validate if we're not on step 4 or if the step is not visible
        const step4Element = document.getElementById('step-4');
        if (!step4Element || step4Element.style.display === 'none' || !step4Element.classList.contains('active')) {
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
        
        console.log('Step 4 Validation:', {
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
    console.log('🚀 FORM SUBMISSION STARTED');
    console.log('Event:', event);
    console.log('Current step:', currentStep);
    console.log('Form element:', event.target);
    
    const form = event.target;
    
    // DUPLICATE PREVENTION: Disable submit button to prevent multiple submissions
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton && submitButton.disabled) {
        console.warn('⚠️ Form submission blocked - already submitted');
        event.preventDefault();
        return false;
    }
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';
    }
    
    // Enhanced debugging
    console.log('Form action:', form.action);
    console.log('Form method:', form.method);
    console.log('Form enctype:', form.enctype);
    
    // Debug current page state
    console.log('📊 CURRENT PAGE STATE:');
    console.log('  URL:', window.location.href);
    console.log('  Document ready state:', document.readyState);
    console.log('  Form submit button:', document.getElementById('submitButton'));
    console.log('  jQuery available:', typeof $ !== 'undefined');
    
    // Check if we're on the correct step
    const finalStep = isUserLoggedIn ? 4 : 5;
    if (currentStep !== finalStep) {
        console.error(`❌ FORM SUBMISSION BLOCKED: Not on step ${finalStep}, current step is:`, currentStep);
        event.preventDefault();
        showFormErrors(['Please complete all previous steps before submitting.']);
        return false;
    }
    
    console.log('✅ Step validation passed');
    
    // run your custom validation
    console.log('🔍 Running form validation...');
    const { isValid, errors } = validateFormBeforeSubmission(form);
    
    console.log('Validation result:', { isValid, errors });
    
    if (!isValid) {
        console.error('❌ FORM VALIDATION FAILED:', errors);
        // block submission and show errors
        event.preventDefault();
        showFormErrors(errors);
        return false;
    }
    
    console.log('✅ Form validation passed');

    // inject batch_id if needed
    const selectedBatchId = sessionStorage.getItem('selectedBatchId');
    const learningMode = form.querySelector('[name="learning_mode"]')?.value;
    
    console.log('Learning mode:', learningMode);
    console.log('Selected batch ID:', selectedBatchId);
    
    if (selectedBatchId) {
        let batchInput = form.querySelector('input[name="batch_id"]');
        if (!batchInput) {
        console.log('Creating new batch_id input');
        batchInput = document.createElement('input');
        batchInput.type = 'hidden';
        batchInput.name = 'batch_id';
        form.appendChild(batchInput);
        }
        batchInput.value = selectedBatchId;
        console.log('✅ Batch ID injected:', selectedBatchId);
    }

    // inject package_id if needed
    const packageId = selectedPackageId || sessionStorage.getItem('selectedPackageId');
    console.log('Package ID:', packageId);
    
    if (packageId) {
        const packageInput = form.querySelector('input[name="package_id"]');
        if (packageInput) {
        packageInput.value = packageId;
        console.log('✅ Package ID updated:', packageId);
        } else {
        console.warn('⚠️ Package input not found in form');
        }
    } else {
        console.warn('⚠️ No package ID available');
    }
    
    // Remove any duplicate program_id inputs
    const allProgramInputs = form.querySelectorAll('input[name="program_id"]');
    if (allProgramInputs.length > 1) {
        console.log('🔧 Removing duplicate program_id inputs');
        for (let i = 1; i < allProgramInputs.length; i++) {
        allProgramInputs[i].remove();
        }
    }
    
    // Ensure program_id is set from the dropdown
    const programSelect = document.getElementById('programSelect');
    const hiddenProgramInput = document.getElementById('hidden_program_id');
    
    if (programSelect && hiddenProgramInput) {
        if (programSelect.value) {
        hiddenProgramInput.value = programSelect.value;
        console.log('✅ Program ID updated before submission:', programSelect.value);
        } else {
        console.error('❌ No program selected!');
        showFormErrors(['Please select a program before submitting.']);
        return false;
        }
    } else {
        console.error('❌ Program select or hidden input not found!');
        showFormErrors(['Program selection error. Please refresh and try again.']);
        return false;
    }
    
    // Show loading state
    showFormLoading(true);
    console.log('✅ Form loading state activated');
    
    // Log all form data before submission
    const formData = new FormData(form);
    console.log('📋 FINAL FORM DATA:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }
    
    // Add detailed form analysis
    console.log('🔍 DETAILED FORM ANALYSIS:');
    console.log('  Form has action:', !!form.action);
    console.log('  Form has method:', !!form.method);
    console.log('  Form has CSRF token:', !!form.querySelector('[name="_token"]'));
    console.log('  Form is properly structured:', form.tagName === 'FORM');
    console.log('  Submit button type:', document.getElementById('submitButton')?.type);
    
    // Check reCAPTCHA status
    console.log('🛡️ RECAPTCHA ANALYSIS:');
    const recaptchaDiv = document.querySelector('.g-recaptcha');
    const recaptchaResponse = document.querySelector('[name="g-recaptcha-response"]');
    console.log('  reCAPTCHA div found:', !!recaptchaDiv);
    console.log('  reCAPTCHA response field found:', !!recaptchaResponse);
    console.log('  reCAPTCHA response value:', recaptchaResponse?.value || 'NONE');
    console.log('  grecaptcha object available:', typeof grecaptcha !== 'undefined');
    
    if (recaptchaDiv && !recaptchaResponse?.value) {
        console.warn('⚠️ reCAPTCHA div present but no response - user may not have completed CAPTCHA');
    }
    
    // Check session storage state
    console.log('� SESSION STORAGE STATE:');
    console.log('  selectedPackageId:', sessionStorage.getItem('selectedPackageId'));
    console.log('  selectedBatchId:', sessionStorage.getItem('selectedBatchId'));
    console.log('  selectedLearningMode:', sessionStorage.getItem('selectedLearningMode'));
    console.log('  selectedProgramId:', sessionStorage.getItem('selectedProgramId'));
    
    // TEMPORARILY PREVENT SUBMISSION FOR DEBUGGING
    console.log('🛑 DEBUGGING COMPLETE - ENABLING FORM SUBMISSION');
    
    // Fix the start date if it's empty
    const startDateInput = form.querySelector('[name="Start_Date"]');
    if (startDateInput && !startDateInput.value) {
        const today = new Date().toISOString().split('T')[0];
        startDateInput.value = today;
        console.log('📅 Fixed empty start date to:', today);
        
        // Hidden field has been removed - only the visible date input is used
        console.log('📅 Start date set in visible field only');
    }
    
    // All validation passed, allow submission
    console.log('� ALLOWING FORM SUBMISSION TO PROCEED');
    
    console.log('🛑 PREVENTING FORM REFRESH - USING AJAX SUBMISSION');
    event.preventDefault(); // Prevent default form submission
    
    // Submit form via AJAX to prevent page refresh
    const finalFormData = new FormData(form);
    
    // Ensure CSRF token is present
    const csrfToken = form.querySelector('[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken && !finalFormData.has('_token')) {
        finalFormData.append('_token', csrfToken);
        console.log('�️ CSRF token added:', csrfToken.substring(0, 10) + '...');
    }
    
    console.log('�🚀 SUBMITTING FORM VIA AJAX');
    console.log('🌐 Request URL:', form.action);
    console.log('📦 Form data entries:');
    for (let [key, value] of finalFormData.entries()) {
        if (key === '_token') {
        console.log(`  ${key}: ${value.substring(0, 10)}...`);
        } else if (value instanceof File) {
        console.log(`  ${key}: [File] ${value.name} (${value.size} bytes)`);
        } else {
        console.log(`  ${key}: ${value}`);
        }
    }
    
    // Double-check that Start_Date is not empty in FormData
    if (!finalFormData.get('Start_Date') || finalFormData.get('Start_Date') === '') {
        const today = new Date().toISOString().split('T')[0];
        finalFormData.set('Start_Date', today);
        console.log('📅 Force-set Start_Date in FormData to:', today);
    }
    
    // Handle reCAPTCHA if present
    const recaptchaResponseValue = document.querySelector('[name="g-recaptcha-response"]')?.value;
    if (recaptchaResponseValue) {
        finalFormData.append('g-recaptcha-response', recaptchaResponseValue);
        console.log('🛡️ reCAPTCHA response added');
    } else {
        console.warn('⚠️ No reCAPTCHA response found - adding empty value to prevent server error');
        // Add empty reCAPTCHA response to prevent server error
        finalFormData.append('g-recaptcha-response', '');
    }
    
    fetch(form.action, {
        method: 'POST',
        body: finalFormData,
        headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
        }
    })
    .then(async response => {
        console.log('📡 Server response received:', response.status, response.statusText);
        console.log('📡 Response headers:', Object.fromEntries(response.headers.entries()));
        
        // Get response text first to handle both JSON and HTML responses
        let responseText;
        try {
        responseText = await response.text();
        console.log('📄 Raw response text (first 500 chars):', responseText.substring(0, 500));
        console.log('📄 Response content type:', response.headers.get('content-type'));
        } catch (textError) {
        console.error('❌ Could not read response text:', textError);
        showFormLoading(false);
        showFormErrors(['Failed to read server response. Please try again.']);
        return;
        }
        
        if (response.ok) {
        // Check if response is JSON or HTML
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            // Handle JSON response
            try {
            const data = JSON.parse(responseText);
            console.log('✅ Registration successful (JSON):', data);
            
            showFormLoading(false);
            alert('🎉 Registration completed successfully! You will be redirected to the success page.');
            
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.href = '/registration/success';
            }
            } catch (jsonError) {
            console.error('❌ Could not parse JSON response:', jsonError);
            showFormLoading(false);
            showFormErrors(['Server returned invalid JSON. Please try again.']);
            }
        } else {
            // Handle HTML response (likely a redirect page or success page)
            console.log('✅ Registration successful (HTML response)');
            
            // Check if response contains success indicators
            const responseTextLower = responseText.toLowerCase();
            if (responseTextLower.includes('success') || responseTextLower.includes('registration') || responseTextLower.includes('complete')) {
            showFormLoading(false);
            alert('🎉 Registration completed successfully!');
            window.location.href = '/registration/success';
            } else if (responseTextLower.includes('login') || responseTextLower.includes('signin')) {
            showFormLoading(false);
            alert('🎉 Registration completed successfully! Please login to continue.');
            window.location.href = '/login';
            } else {
            // Show success and redirect anyway since we got a 200 response
            showFormLoading(false);
            alert('🎉 Registration completed successfully!');
            
            // Try to find redirect URL in the HTML
            const redirectMatch = responseText.match(/window\.location\.href\s*=\s*['"]([^'"]+)['"]/);
            if (redirectMatch) {
                window.location.href = redirectMatch[1];
            } else {
                window.location.href = '/registration/success';
            }
            }
        }
        } else {
        console.error('❌ Server returned error status:', response.status, response.statusText);
        
        // Try to parse as JSON first for structured error handling
        try {
            const errorData = JSON.parse(responseText);
            console.error('❌ Server error data:', errorData);
            
            showFormLoading(false);
            
            if (errorData.errors) {
            const errorMessages = Object.values(errorData.errors).flat();
            showFormErrors(errorMessages);
            } else {
            showFormErrors([errorData.message || 'Registration failed. Please try again.']);
            }
        } catch (parseError) {
            console.error('❌ Could not parse error response as JSON:', parseError);
            console.log('📄 Raw error response:', responseText.substring(0, 1000));
            
            showFormLoading(false);
            
            // Extract meaningful error from HTML if possible
            const errorText = responseText.replace(/<[^>]*>/g, '').trim();
            const truncatedError = errorText.length > 200 ? errorText.substring(0, 200) + '...' : errorText;
            
            showFormErrors([`Server error (${response.status}): ${truncatedError || 'Unknown error'}`]);
        }
        }
    })
    .catch(error => {
        console.error('🔥 Network/Fetch error details:', error);
        console.error('🔥 Error name:', error.name);
        console.error('🔥 Error message:', error.message);
        console.error('🔥 Error stack:', error.stack);
        
        showFormLoading(false);
        
        // More specific error messages
        if (error.name === 'TypeError' && error.message.includes('fetch')) {
        showFormErrors(['Network connection failed. Please check your internet connection and try again.']);
        } else if (error.name === 'AbortError') {
        showFormErrors(['Request was cancelled. Please try again.']);
        } else {
        showFormErrors([`Network error: ${error.message}. Please try again.`]);
        }
    });
    
    return false; // Prevent any default form submission
    }


    // Comprehensive form validation function
    function validateFormBeforeSubmission(form) {
        console.log('🔍 VALIDATING FORM BEFORE SUBMISSION');
        const errors = [];

        // Get all required fields dynamically from the form
        const requiredInputs = form.querySelectorAll('[required]');
        console.log(`Found ${requiredInputs.length} required fields:`, requiredInputs);

        // Check each required field
        requiredInputs.forEach((input, index) => {
            const fieldName = input.name;
            const fieldLabel = input.getAttribute('data-label') ||
                input.previousElementSibling?.textContent?.replace('*', '').trim() ||
                fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace('_', ' ');

            console.log(`  [${index + 1}] ${fieldName} (${input.type}): "${input.value}" - Required: ${input.required}`);

            if (!input.value || input.value.trim() === '') {
                errors.push(`${fieldLabel} is required.`);
                console.error(`    ❌ MISSING: ${fieldLabel}`);
            } else {
                console.log(`    ✅ OK: ${fieldLabel}`);
            }
        });

        // Additional validation for specific field types
        const emailInput = form.querySelector('[name="email"]');
        if (emailInput && emailInput.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                errors.push('Please enter a valid email address.');
                console.error('❌ INVALID EMAIL FORMAT');
            } else {
                console.log('✅ Email format valid');
            }
        }

        // Check contact number format if field exists and has value
        const contactInput = form.querySelector('[name="contact_number"]');
        if (contactInput && contactInput.value) {
            const contactRegex = /^[0-9+\-\s()]+$/;
            if (!contactRegex.test(contactInput.value)) {
                errors.push('Please enter a valid contact number.');
                console.error('❌ INVALID CONTACT NUMBER FORMAT');
            } else {
                console.log('✅ Contact number format valid');
            }
        }

        // Check batch selection for synchronous mode - only if batches are available
        const learningMode = form.querySelector('[name="learning_mode"]')?.value;
        const batchId = sessionStorage.getItem('selectedBatchId') || form.querySelector('[name="batch_id"]')?.value;
        
        console.log('🎯 BATCH VALIDATION:');
        console.log('  Learning mode:', learningMode);
        console.log('  Batch ID from sessionStorage:', sessionStorage.getItem('selectedBatchId'));
        console.log('  Batch ID from form:', form.querySelector('[name="batch_id"]')?.value);
        
        if (learningMode === 'synchronous') {
            // Check if batch container is visible and has batch options
            const batchContainer = document.getElementById('batchSelectionContainer');
            const batchOptions = document.getElementById('batchOptions');
            const hasBatchOptions = batchOptions && batchOptions.querySelector('.batch-option');
            const hasNoBatchesInfo = batchOptions && batchOptions.querySelector('.no-batches-info');
            
            console.log('  Batch container visible:', batchContainer && batchContainer.style.display !== 'none');
            console.log('  Has batch options:', !!hasBatchOptions);
            console.log('  Has no-batches info:', !!hasNoBatchesInfo);
            
            // Only require batch selection if there are actual batch options available
            if (batchContainer && batchContainer.style.display !== 'none' && hasBatchOptions && !hasNoBatchesInfo) {
                if (!batchId || batchId === 'null' || batchId === '') {
                    errors.push('Please select a batch for synchronous learning mode.');
                    console.error('❌ BATCH REQUIRED but not selected');
                } else {
                    console.log('✅ Batch selected for synchronous mode');
                }
            } else {
                console.log('✅ Batch not required (auto-create mode or no batches available)');
            }
            // If no batches available (auto-create mode), allow registration without batch selection
            console.log('Batch validation check:', {
                learningMode,
                batchContainerVisible: batchContainer && batchContainer.style.display !== 'none',
                hasBatchOptions: !!hasBatchOptions,
                hasNoBatchesInfo: !!hasNoBatchesInfo,
                batchId: batchId
            });
        } else {
            console.log('✅ Asynchronous mode - no batch required');
        }

        // Check terms acceptance
        const termsCheckbox = form.querySelector('[name="terms_accepted"]') || document.getElementById('termsCheckbox');
        console.log('🔒 TERMS VALIDATION:');
        console.log('  Terms checkbox found:', !!termsCheckbox);
        console.log('  Terms checkbox checked:', termsCheckbox?.checked);
        
        if (termsCheckbox && !termsCheckbox.checked) {
            errors.push('Please accept the terms and conditions.');
            console.error('❌ TERMS NOT ACCEPTED');
        } else if (termsCheckbox) {
            console.log('✅ Terms accepted');
        } else {
            console.warn('⚠️ Terms checkbox not found, but assuming accepted for compatibility');
        }

        // Check package selection
        const packageId = selectedPackageId || sessionStorage.getItem('selectedPackageId') || form.querySelector('[name="package_id"]')?.value;
        console.log('📦 PACKAGE VALIDATION:');
        console.log('  Package ID:', packageId);
        
        if (!packageId) {
            errors.push('Please select a package.');
            console.error('❌ PACKAGE NOT SELECTED');
        } else {
            console.log('✅ Package selected');
        }

        console.log(`🔍 VALIDATION COMPLETE: ${errors.length} errors found`);
        if (errors.length > 0) {
            console.error('❌ VALIDATION ERRORS:', errors);
        } else {
            console.log('✅ ALL VALIDATIONS PASSED');
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
    window.onProgramSelectionChange = onProgramSelectionChange;


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
        const termsCheckbox = document.getElementById('termsCheckbox');
        if (termsCheckbox) {
            termsCheckbox.checked = true;
        }
        
        // Enable the submit button
        const submitButton = document.getElementById('submitButton');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.classList.remove('disabled');
            submitButton.style.opacity = '1';
        }
        
        // Close the modal
        closeTermsModal();
        
        console.log('Terms and conditions accepted');
    }

    function declineTerms() {
        // Uncheck the terms and conditions checkbox
        const termsCheckbox = document.getElementById('termsCheckbox');
        if (termsCheckbox) {
            termsCheckbox.checked = false;
        }
        
        // Disable the submit button
        const submitButton = document.getElementById('submitButton');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('disabled');
            submitButton.style.opacity = '0.5';
        }
        
        // Close the modal
        closeTermsModal();
        
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
