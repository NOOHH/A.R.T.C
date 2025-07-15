@extends('layouts.navbar')

@section('title', 'Modular Enrollment - Select Individual Modules')
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

    <!-- reCAPTCHA -->
    @if(env('RECAPTCHA_SITE_KEY'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    <!-- Critical JavaScript functions for immediate availability -->
    <script>
        
        // Global variables (declare first for immediate availability)
        let modularCurrentStep = 1;
        let selectedPackageId = null;
        let selectedModules = [];
        let selectedProgramId = null;
        let selectedPaymentMethod = null;
        let currentPackageIndex = 0;
        let packagesPerView = 2;
        let totalPackages = <?php echo isset($packages) && is_countable($packages) ? (int)count($packages) : 0; ?>;
        let otpSent = false;
        let otpVerified = false;
        let fileUploadStatus = {};

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
        @php
            $userLoggedIn = session('user_id') || (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']));
            $userId = session('user_id') ?: (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '');
            $userName = session('user_name') ?: (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '');
            $userFirstname = session('user_firstname') ?: (isset($_SESSION['user_firstname']) ? $_SESSION['user_firstname'] : '');
            $userLastname = session('user_lastname') ?: (isset($_SESSION['user_lastname']) ? $_SESSION['user_lastname'] : '');
            $userEmail = session('user_email') ?: (isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '');
        @endphp

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

        // Dynamic form requirements data from admin-settings
        const formRequirements = @json($formRequirements ?? []);
        console.log('Form requirements loaded:', formRequirements);

        // Package selection function
        function selectPackage(packageId, packageName, packagePrice, programId) {
            console.log('Package selection called:', { packageId, packageName, packagePrice, programId });
            
            // Remove selection from all package cards
            document.querySelectorAll('.package-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Highlight selected package
            if (event && event.target) {
                const card = event.target.closest('.package-card');
                if (card) card.classList.add('selected');
            }

            // Store selection in global variables
            selectedPackageId = packageId;
            selectedProgramId = programId;
            
            console.log('Package selected:', { packageId, packageName, packagePrice, programId });
            
            // Show modules section
            const modulesSection = document.getElementById('moduleSelectionSection');
            if (modulesSection) {
                modulesSection.style.display = 'block';
                modulesSection.classList.add('show');
            }
            
            // Load modules for the selected package
            loadModulesForPackage(packageId);
            
            // Update hidden input
            const packageInput = document.getElementById('selectedPackage');
            if (packageInput) {
                packageInput.value = packageId;
            }
        }

        // Load modules for selected package
        function loadModulesForPackage(packageId) {
            console.log('Loading modules for package:', packageId);
            
            if (!packageId) {
                console.error('Package ID is required');
                return;
            }
            
            // Show loading state
            const modulesContainer = document.getElementById('modulesContainer');
            if (modulesContainer) {
                modulesContainer.innerHTML = '<div class="loading-modules"><i class="fas fa-spinner fa-spin"></i> Loading modules...</div>';
            }
            
            // Fetch modules via AJAX
            fetch(`${MODULES_URL}?package_id=${packageId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Modules loaded:', data);
                    
                    if (data.success) {
                        displayModules(data.modules);
                    } else {
                        console.error('Failed to load modules:', data.message);
                        if (modulesContainer) {
                            modulesContainer.innerHTML = '<div class="error-message alert alert-danger">Failed to load modules. Please try again.</div>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading modules:', error);
                    if (modulesContainer) {
                        modulesContainer.innerHTML = '<div class="error-message alert alert-danger">Error loading modules. Please try again.</div>';
                    }
                });
        }

        // Display modules in the UI
        function displayModules(modules) {
            const modulesContainer = document.getElementById('modulesContainer');
            if (!modulesContainer) return;
            
            if (!modules || modules.length === 0) {
                modulesContainer.innerHTML = '<div class="no-modules alert alert-info">No modules available for this package.</div>';
                return;
            }
            
            const moduleHtml = modules.map(module => `
                <div class="module-card" data-module-id="${module.id}">
                    <div class="module-header">
                        <input type="checkbox" class="module-checkbox" id="module_${module.id}" 
                               value="${module.id}" onchange="handleModuleSelection(this)">
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
            `).join('');
            
            modulesContainer.innerHTML = moduleHtml;
            
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

        // Handle module selection
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
            updateSelectedModulesInput();
            
            // Enable/disable next button
            const nextBtn = document.getElementById('packageNextBtn');
            if (nextBtn) {
                nextBtn.disabled = selectedModules.length === 0;
                nextBtn.style.opacity = selectedModules.length > 0 ? '1' : '0.5';
            }
            
            console.log('Selected modules:', selectedModules);
        }

        // Update selected modules display
        function updateSelectedModulesDisplay() {
            const countElement = document.getElementById('selectedModulesCount');
            const listElement = document.getElementById('selectedModulesList');
            const summaryElement = document.getElementById('selectedModulesSummary');
            
            if (selectedModules.length === 0) {
                if (summaryElement) summaryElement.style.display = 'none';
                return;
            }
            
            if (countElement) countElement.textContent = selectedModules.length;
            if (listElement) {
                listElement.innerHTML = selectedModules.map(module => `
                    <span class="selected-module-tag badge bg-primary me-2 mb-2">${module.name}</span>
                `).join('');
            }
            if (summaryElement) summaryElement.style.display = 'block';
        }

        // Update selected modules hidden input
        function updateSelectedModulesInput() {
            const hiddenInput = document.getElementById('selected_modules');
            if (hiddenInput) {
                hiddenInput.value = JSON.stringify(selectedModules);
            }
        }

        // Main step navigation function
        function nextStep() {
            console.log('Next step called, current step:', modularCurrentStep);
            
            if (modularCurrentStep === 1) {
                // Step 1: Package and Module Selection
                if (!selectedPackageId) {
                    showAlert('Please select a package first.', 'warning');
                    return;
                }
                
                if (!selectedModules || selectedModules.length === 0) {
                    showAlert('Please select at least one module.', 'warning');
                    return;
                }
                
                // Move to step 2 (Account Registration)
                modularCurrentStep = 2;
                animateStepTransition('step1', 'step2');
                
                // Load dynamic form fields for step 2
                loadFormRequirements();
                
                // Hide/show account creation fields based on login status
                toggleAccountFields();
                
            } else if (modularCurrentStep === 2) {
                // Step 2: Account Registration - validate and move to step 3
                if (!validateStep2()) {
                    return;
                }
                
                // Move to step 3 (Student Registration)
                modularCurrentStep = 3;
                animateStepTransition('step2', 'step3');
                
                // Load dynamic form fields for step 3
                loadFormRequirements();
                
                // Show selected package and modules summary
                showSelectionSummary();
                
            } else if (modularCurrentStep === 3) {
                // Step 3: Student Registration - validate and submit
                if (!validateStep3()) {
                    return;
                }
                
                // Submit the form
                submitRegistrationForm();
            }
        }

        function prevStep() {
            console.log('Previous step called, current step:', modularCurrentStep);
            
            if (modularCurrentStep === 2) {
                modularCurrentStep = 1;
                animateStepTransition('step2', 'step1', true);
            } else if (modularCurrentStep === 3) {
                modularCurrentStep = 2;
                animateStepTransition('step3', 'step2', true);
            }
        }

        // Load dynamic form requirements from admin-settings
        function loadFormRequirements() {
            console.log('Loading form requirements for step:', modularCurrentStep);
            
            if (!formRequirements || formRequirements.length === 0) {
                console.log('No form requirements found');
                return;
            }
            
            const containerId = modularCurrentStep === 2 ? 'accountDynamicFields' : 'studentDynamicFields';
            const container = document.getElementById(containerId);
            
            if (!container) {
                console.error('Dynamic fields container not found:', containerId);
                return;
            }
            
            // Filter requirements by program type (modular) and step
            const relevantRequirements = formRequirements.filter(req => {
                if (req.field_type === 'section') return true;
                
                // Step 2: Account-related fields
                if (modularCurrentStep === 2) {
                    return ['user_firstname', 'user_lastname', 'email', 'password'].includes(req.field_name);
                }
                
                // Step 3: Student-related fields
                if (modularCurrentStep === 3) {
                    return !['user_firstname', 'user_lastname', 'email', 'password'].includes(req.field_name);
                }
                
                return true;
            });
            
            // Generate form fields HTML
            const fieldsHtml = relevantRequirements.map(req => generateFormField(req)).join('');
            container.innerHTML = fieldsHtml;
            
            // Initialize field interactions
            initializeFormFieldInteractions();
        }

        // Generate individual form field HTML
        function generateFormField(requirement) {
            if (requirement.field_type === 'section') {
                return `
                    <div class="form-section-header mb-3">
                        <h5 class="text-primary">${requirement.section_name}</h5>
                        <hr>
                    </div>
                `;
            }
            
            const isRequired = requirement.is_required;
            const fieldId = requirement.field_name;
            const fieldLabel = requirement.field_label;
            const fieldType = requirement.field_type;
            const isBold = requirement.is_bold;
            
            let fieldHtml = '';
            
            switch (fieldType) {
                case 'text':
                case 'email':
                case 'password':
                    fieldHtml = `
                        <div class="form-group mb-3">
                            <label for="${fieldId}" class="form-label ${isBold ? 'fw-bold' : ''}">
                                ${fieldLabel}${isRequired ? ' <span class="text-danger">*</span>' : ''}
                            </label>
                            <input type="${fieldType}" class="form-control" id="${fieldId}" name="${fieldId}" 
                                   ${isRequired ? 'required' : ''}>
                        </div>
                    `;
                    break;
                
                case 'textarea':
                    fieldHtml = `
                        <div class="form-group mb-3">
                            <label for="${fieldId}" class="form-label ${isBold ? 'fw-bold' : ''}">
                                ${fieldLabel}${isRequired ? ' <span class="text-danger">*</span>' : ''}
                            </label>
                            <textarea class="form-control" id="${fieldId}" name="${fieldId}" rows="3" 
                                      ${isRequired ? 'required' : ''}></textarea>
                        </div>
                    `;
                    break;
                
                case 'select':
                    const options = requirement.field_options || [];
                    const optionsHtml = options.map(option => `<option value="${option}">${option}</option>`).join('');
                    fieldHtml = `
                        <div class="form-group mb-3">
                            <label for="${fieldId}" class="form-label ${isBold ? 'fw-bold' : ''}">
                                ${fieldLabel}${isRequired ? ' <span class="text-danger">*</span>' : ''}
                            </label>
                            <select class="form-control" id="${fieldId}" name="${fieldId}" 
                                    ${isRequired ? 'required' : ''}>
                                <option value="">Select ${fieldLabel}</option>
                                ${optionsHtml}
                            </select>
                        </div>
                    `;
                    break;
                
                case 'radio':
                    const radioOptions = requirement.field_options || [];
                    const radioHtml = radioOptions.map((option, index) => `
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="${fieldId}" 
                                   id="${fieldId}_${index}" value="${option}" ${isRequired ? 'required' : ''}>
                            <label class="form-check-label" for="${fieldId}_${index}">
                                ${option}
                            </label>
                        </div>
                    `).join('');
                    fieldHtml = `
                        <div class="form-group mb-3">
                            <label class="form-label ${isBold ? 'fw-bold' : ''}">
                                ${fieldLabel}${isRequired ? ' <span class="text-danger">*</span>' : ''}
                            </label>
                            ${radioHtml}
                        </div>
                    `;
                    break;
                
                case 'checkbox':
                    const checkboxOptions = requirement.field_options || [];
                    const checkboxHtml = checkboxOptions.map((option, index) => `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="${fieldId}[]" 
                                   id="${fieldId}_${index}" value="${option}">
                            <label class="form-check-label" for="${fieldId}_${index}">
                                ${option}
                            </label>
                        </div>
                    `).join('');
                    fieldHtml = `
                        <div class="form-group mb-3">
                            <label class="form-label ${isBold ? 'fw-bold' : ''}">
                                ${fieldLabel}${isRequired ? ' <span class="text-danger">*</span>' : ''}
                            </label>
                            ${checkboxHtml}
                        </div>
                    `;
                    break;
                
                case 'file':
                    fieldHtml = `
                        <div class="form-group mb-3">
                            <label for="${fieldId}" class="form-label ${isBold ? 'fw-bold' : ''}">
                                ${fieldLabel}${isRequired ? ' <span class="text-danger">*</span>' : ''}
                            </label>
                            <input type="file" class="form-control" id="${fieldId}" name="${fieldId}" 
                                   ${isRequired ? 'required' : ''} accept=".jpg,.jpeg,.png,.pdf">
                            <small class="form-text text-muted">Supported formats: JPG, JPEG, PNG, PDF (Max 5MB)</small>
                        </div>
                    `;
                    break;
                
                default:
                    fieldHtml = `
                        <div class="form-group mb-3">
                            <label for="${fieldId}" class="form-label ${isBold ? 'fw-bold' : ''}">
                                ${fieldLabel}${isRequired ? ' <span class="text-danger">*</span>' : ''}
                            </label>
                            <input type="text" class="form-control" id="${fieldId}" name="${fieldId}" 
                                   ${isRequired ? 'required' : ''}>
                        </div>
                    `;
            }
            
            return fieldHtml;
        }

        // Initialize form field interactions
        function initializeFormFieldInteractions() {
            // Add file upload handlers
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function(e) {
                    handleFileUpload(e.target);
                });
            });
            
            // Add validation handlers
            const formInputs = document.querySelectorAll('input, select, textarea');
            formInputs.forEach(input => {
                input.addEventListener('blur', function(e) {
                    validateField(e.target);
                });
            });
        }

        // Handle file upload and validation
        function handleFileUpload(fileInput) {
            const file = fileInput.files[0];
            if (!file) return;
            
            // Validate file type and size
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!allowedTypes.includes(file.type)) {
                showAlert('Please upload a valid file type (JPEG, JPG, PNG, PDF).', 'error');
                fileInput.value = '';
                return;
            }
            
            if (file.size > maxSize) {
                showAlert('File size must be less than 5MB.', 'error');
                fileInput.value = '';
                return;
            }
            
            // Mark upload as successful
            fileUploadStatus[fileInput.name] = 'success';
            
            // Show success message
            showUploadSuccess(fileInput);
            
            // If it's an image, show preview
            if (file.type.startsWith('image/')) {
                showImagePreview(fileInput, file);
            }
        }

        // Show upload success
        function showUploadSuccess(fileInput) {
            const successElement = document.createElement('div');
            successElement.className = 'upload-success text-success mt-1';
            successElement.innerHTML = '<i class="fas fa-check-circle"></i> File uploaded successfully';
            
            // Remove any existing success message
            const existingSuccess = fileInput.parentNode.querySelector('.upload-success');
            if (existingSuccess) {
                existingSuccess.remove();
            }
            
            fileInput.parentNode.appendChild(successElement);
        }

        // Show image preview
        function showImagePreview(fileInput, file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'image-preview mt-2';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeImagePreview(this)">Remove</button>
                `;
                
                // Remove existing preview
                const existingPreview = fileInput.parentNode.querySelector('.image-preview');
                if (existingPreview) {
                    existingPreview.remove();
                }
                
                fileInput.parentNode.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }

        // Remove image preview
        function removeImagePreview(button) {
            const preview = button.closest('.image-preview');
            const fileInput = preview.parentNode.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.value = '';
                delete fileUploadStatus[fileInput.name];
            }
            preview.remove();
        }

        // Validate individual field
        function validateField(field) {
            const fieldContainer = field.closest('.form-group');
            const existingError = fieldContainer.querySelector('.error-message');
            
            // Remove existing error message
            if (existingError) {
                existingError.remove();
            }
            
            // Remove invalid class
            field.classList.remove('is-invalid');
            
            // Check if field is required and empty
            if (field.hasAttribute('required') && !field.value.trim()) {
                showFieldError(field, 'This field is required.');
                return false;
            }
            
            // Email validation
            if (field.type === 'email' && field.value.trim()) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(field.value.trim())) {
                    showFieldError(field, 'Please enter a valid email address.');
                    return false;
                }
            }
            
            // Password validation
            if (field.type === 'password' && field.value.trim()) {
                if (field.value.length < 8) {
                    showFieldError(field, 'Password must be at least 8 characters long.');
                    return false;
                }
            }
            
            return true;
        }

        // Show field error
        function showFieldError(field, message) {
            const fieldContainer = field.closest('.form-group');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-danger mt-1';
            errorDiv.textContent = message;
            fieldContainer.appendChild(errorDiv);
            
            field.classList.add('is-invalid');
        }

        // Toggle account fields visibility based on login status
        function toggleAccountFields() {
            const accountFields = document.getElementById('accountFields');
            const loginNotice = document.getElementById('loginNotice');
            
            if (isUserLoggedIn) {
                if (accountFields) accountFields.style.display = 'none';
                if (loginNotice) loginNotice.style.display = 'block';
                
                // Pre-fill user data
                prefillUserData();
            } else {
                if (accountFields) accountFields.style.display = 'block';
                if (loginNotice) loginNotice.style.display = 'none';
            }
        }

        // Pre-fill user data for logged-in users
        function prefillUserData() {
            const fields = {
                'user_firstname': loggedInUserFirstname,
                'user_lastname': loggedInUserLastname,
                'email': loggedInUserEmail
            };
            
            Object.entries(fields).forEach(([fieldName, value]) => {
                const field = document.getElementById(fieldName);
                if (field && value) {
                    field.value = value;
                }
            });
        }

        // Step 2 validation
        function validateStep2() {
            let isValid = true;
            
            if (!isUserLoggedIn) {
                // Validate account creation fields
                const requiredFields = ['user_firstname', 'user_lastname', 'email', 'password'];
                
                requiredFields.forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    if (field && !validateField(field)) {
                        isValid = false;
                    }
                });
                
                // Check password confirmation
                const password = document.getElementById('password');
                const passwordConfirm = document.getElementById('password_confirmation');
                
                if (password && passwordConfirm) {
                    if (password.value !== passwordConfirm.value) {
                        showFieldError(passwordConfirm, 'Passwords do not match.');
                        isValid = false;
                    }
                }
            }
            
            // Validate dynamic fields
            const dynamicFields = document.querySelectorAll('#accountDynamicFields input, #accountDynamicFields select, #accountDynamicFields textarea');
            dynamicFields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });
            
            return isValid;
        }

        // Step 3 validation
        function validateStep3() {
            let isValid = true;
            
            // Validate dynamic fields
            const dynamicFields = document.querySelectorAll('#studentDynamicFields input, #studentDynamicFields select, #studentDynamicFields textarea');
            dynamicFields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });
            
            // Validate reCAPTCHA if enabled
            if (hasRecaptcha) {
                const recaptchaResponse = grecaptcha.getResponse();
                if (!recaptchaResponse) {
                    showAlert('Please complete the reCAPTCHA verification.', 'error');
                    isValid = false;
                }
            }
            
            return isValid;
        }

        // Show selection summary
        function showSelectionSummary() {
            const summaryContainer = document.getElementById('selectionSummary');
            if (!summaryContainer) return;
            
            const packageName = document.querySelector('.package-card.selected .package-name')?.textContent || 'Unknown Package';
            const modulesList = selectedModules.map(module => module.name).join(', ');
            
            summaryContainer.innerHTML = `
                <div class="selection-summary card">
                    <div class="card-body">
                        <h5 class="card-title">Your Selection</h5>
                        <p><strong>Package:</strong> ${packageName}</p>
                        <p><strong>Modules:</strong> ${modulesList}</p>
                        <p><strong>Total Modules:</strong> ${selectedModules.length}</p>
                    </div>
                </div>
            `;
        }

        // Submit registration form
        function submitRegistrationForm() {
            const form = document.getElementById('enrollmentForm');
            if (!form) return;
            
            // Show loading state
            showLoadingState(true);
            
            // Update hidden inputs
            updateHiddenInputs();
            
            // Prepare form data for AJAX submission
            const formData = new FormData(form);
            
            // Submit form via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                showLoadingState(false);
                
                if (data.success) {
                    showAlert(data.message, 'success');
                    
                    // Redirect to success page if provided
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                } else {
                    showAlert(data.message || 'Registration failed. Please try again.', 'error');
                    
                    // Show field-specific errors if available
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const fieldElement = document.getElementById(field);
                            if (fieldElement) {
                                fieldElement.classList.add('is-invalid');
                                
                                // Add error message
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = data.errors[field][0];
                                fieldElement.parentNode.appendChild(errorDiv);
                            }
                        });
                    }
                }
            })
            .catch(error => {
                showLoadingState(false);
                console.error('Registration error:', error);
                showAlert('An error occurred during registration. Please try again.', 'error');
            });
        }

        // Update hidden inputs before submission
        function updateHiddenInputs() {
            // Update enrollment type
            const enrollmentTypeInput = document.getElementById('enrollment_type');
            if (enrollmentTypeInput) {
                enrollmentTypeInput.value = 'modular';
            }
            
            // Update selected package
            const selectedPackageInput = document.getElementById('selectedPackage');
            if (selectedPackageInput) {
                selectedPackageInput.value = selectedPackageId;
            }
            
            // Update selected modules
            const selectedModulesInput = document.getElementById('selected_modules');
            if (selectedModulesInput) {
                selectedModulesInput.value = JSON.stringify(selectedModules);
            }
            
            // Update program ID
            const programIdInput = document.getElementById('program_id');
            if (programIdInput) {
                programIdInput.value = selectedProgramId;
            }
        }

        // Show loading state
        function showLoadingState(show) {
            const loadingOverlay = document.getElementById('loadingOverlay');
            const submitButton = document.getElementById('submitButton');
            
            if (show) {
                if (loadingOverlay) loadingOverlay.style.display = 'flex';
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                }
            } else {
                if (loadingOverlay) loadingOverlay.style.display = 'none';
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Complete Registration';
                }
            }
        }

        // Alert system
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer');
            if (!alertContainer) {
                // Create alert container if it doesn't exist
                const container = document.createElement('div');
                container.id = 'alertContainer';
                container.className = 'alert-container';
                document.body.appendChild(container);
            }
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            
            document.getElementById('alertContainer').appendChild(alertDiv);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Function to update progress bar based on current step
        function updateProgressBar() {
            const progressBar = document.querySelector('.progress-bar');
            if (!progressBar) return;
            
            let percentage = 0;
            switch(modularCurrentStep) {
                case 1: percentage = 33; break;  // Package/Module Selection
                case 2: percentage = 67; break;  // Account Registration
                case 3: percentage = 100; break; // Student Registration
                default: percentage = 33;
            }
            
            progressBar.style.width = percentage + '%';
            progressBar.setAttribute('aria-valuenow', percentage);
            
            // Update stepper
            updateStepper();
        }

        // Update stepper visual state
        function updateStepper() {
            // Update step indicators
            for (let i = 1; i <= 3; i++) {
                const step = document.getElementById(`stepper-${i}`);
                if (step) {
                    step.classList.remove('active', 'completed');
                    if (i < modularCurrentStep) {
                        step.classList.add('completed');
                    } else if (i === modularCurrentStep) {
                        step.classList.add('active');
                    }
                }
            }
            
            // Update progress bar
            const progressBar = document.querySelector('.stepper .bar');
            if (progressBar) {
                progressBar.classList.remove('progress-33', 'progress-67', 'progress-100');
                if (modularCurrentStep === 2) {
                    progressBar.classList.add('progress-33');
                } else if (modularCurrentStep === 3) {
                    progressBar.classList.add('progress-67');
                } else if (modularCurrentStep > 3) {
                    progressBar.classList.add('progress-100');
                }
            }
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

        // Initialize page on load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Modular enrollment page loaded');
            
            // Initialize first step
            updateProgressBar();
            
            // Load form requirements for initial step
            if (formRequirements && formRequirements.length > 0) {
                console.log('Initial form requirements loaded:', formRequirements.length);
            }
            
            // Set up package selection handlers
            const packageCards = document.querySelectorAll('.package-card');
            packageCards.forEach(card => {
                card.addEventListener('click', function() {
                    const packageId = this.dataset.packageId;
                    const packageName = this.dataset.packageName;
                    const packagePrice = this.dataset.packagePrice;
                    const programId = this.dataset.programId;
                    
                    selectPackage(packageId, packageName, packagePrice, programId);
                });
            });
            
            // Handle form submission
            const form = document.getElementById('enrollmentForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (modularCurrentStep === 3) {
                        submitRegistrationForm();
                    } else {
                        nextStep();
                    }
                });
            }
        });

        // Handle form submission
        function handleFormSubmission(event) {
            event.preventDefault();
            
            console.log('Form submission triggered, current step:', modularCurrentStep);
            
            if (modularCurrentStep === 3) {
                // Final submission
                if (validateStep3()) {
                    showLoadingState(true);
                    return true; // Allow form submission
                }
                return false;
            } else {
                // Move to next step
                nextStep();
                return false;
            }
        }
    </script>
@endpush

@section('content')
<div class="form-container">
    <div class="form-wrapper">
        <!-- Alert Container -->
        <div id="alertContainer" class="alert-container"></div>
        
        <!-- Progress Bar -->
        <div class="stepper-progress">
            <div class="stepper">
                <div class="bar"></div>
                <div class="step active" id="stepper-1">
                    <div class="circle">1</div>
                    <div class="label">Package</div>
                </div>
                <div class="step" id="stepper-2">
                    <div class="circle">2</div>
                    <div class="label">Account</div>
                </div>
                <div class="step" id="stepper-3">
                    <div class="circle">3</div>
                    <div class="label">Finish</div>
                </div>
            </div>
        </div>

        <form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" class="registration-form" id="enrollmentForm" novalidate onsubmit="return handleFormSubmission(event)">
            @csrf
            
            <!-- Hidden inputs for form data -->
            <input type="hidden" id="enrollment_type" name="enrollment_type" value="modular">
            <input type="hidden" id="selectedPackage" name="package_id" value="">
            <input type="hidden" id="selected_modules" name="selected_modules" value="">
            <input type="hidden" id="program_id" name="program_id" value="">
            
            <!-- Step 1: Package and Module Selection -->
            <div id="step1" class="step-content active">
                <div class="step-header">
                    <h2>Select Package & Modules</h2>
                    <p>Choose a package and select individual modules for your modular enrollment.</p>
                </div>
                
                <!-- Package Selection -->
                <div class="packages-section">
                    <h3>Available Packages</h3>
                    <div class="packages-grid">
                        @if($packages && count($packages) > 0)
                            @foreach($packages as $package)
                                <div class="package-card" data-package-id="{{ $package->package_id }}" 
                                     data-package-name="{{ $package->package_name }}"
                                     data-package-price="{{ $package->amount }}"
                                     data-program-id="{{ $package->program_id }}">
                                    <div class="package-header">
                                        <h4 class="package-name">{{ $package->package_name }}</h4>
                                        <div class="package-price">{{ number_format($package->amount, 2) }} PHP</div>
                                    </div>
                                    <div class="package-description">
                                        {{ $package->description ?? 'No description available' }}
                                    </div>
                                    <div class="package-features">
                                        <ul>
                                            <li>Flexible module selection</li>
                                            <li>Self-paced learning</li>
                                            <li>Certificate upon completion</li>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="no-packages">
                                <p>No packages available at this time.</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Module Selection -->
                <div class="modules-section" id="moduleSelectionSection" style="display: none;">
                    <h3>Select Modules</h3>
                    <p class="text-center text-muted mb-4">Choose the modules you want to enroll in from the selected package.</p>
                    
                    <div id="modulesContainer">
                        <!-- Modules will be loaded here via AJAX -->
                    </div>
                    
                    <!-- Selected Modules Summary -->
                    <div id="selectedModulesSummary" class="selected-modules-summary" style="display: none;">
                        <h4>Selected Modules (<span id="selectedModulesCount">0</span>)</h4>
                        <div id="selectedModulesList"></div>
                    </div>
                </div>
                
                <!-- Navigation -->
                <div class="step-navigation">
                    <button type="button" class="btn btn-primary" id="packageNextBtn" onclick="nextStep()" disabled>
                        Next Step <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 2: Account Registration -->
            <div id="step2" class="step-content">
                <div class="step-header">
                    <h2>Account Information</h2>
                    <p>Create your account or use existing login information.</p>
                </div>
                
                <!-- Logged in user notice -->
                <div id="loginNotice" class="login-notice" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        You are logged in as <strong id="loggedInUserName"></strong>. 
                        Your account information will be used automatically.
                    </div>
                </div>
                
                <!-- Account creation fields -->
                <div id="accountFields">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="user_firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="user_firstname" name="user_firstname" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="user_lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="user_lastname" name="user_lastname" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Dynamic form fields from admin-settings -->
                <div id="accountDynamicFields">
                    <!-- Dynamic fields will be loaded here -->
                </div>
                
                <!-- Navigation -->
                <div class="step-navigation">
                    <button type="button" class="btn btn-secondary" onclick="prevStep()">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary" onclick="nextStep()">
                        Next Step <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 3: Student Registration -->
            <div id="step3" class="step-content">
                <div class="step-header">
                    <h2>Student Information</h2>
                    <p>Complete your student registration with additional details.</p>
                </div>
                
                <!-- Selection Summary -->
                <div id="selectionSummary" class="mb-4">
                    <!-- Summary will be loaded here -->
                </div>
                
                <!-- Dynamic form fields from admin-settings -->
                <div id="studentDynamicFields">
                    <!-- Dynamic fields will be loaded here -->
                </div>
                
                <!-- reCAPTCHA -->
                @if(env('RECAPTCHA_SITE_KEY'))
                <div class="form-group mb-3">
                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                </div>
                @endif
                
                <!-- Navigation -->
                <div class="step-navigation">
                    <button type="button" class="btn btn-secondary" onclick="prevStep()">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button type="submit" class="btn btn-success" id="submitButton">
                        <i class="fas fa-check"></i> Complete Registration
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-content">
        <i class="fas fa-spinner fa-spin fa-3x"></i>
        <p>Processing your registration...</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Additional scripts can be added here
    console.log('Modular enrollment system initialized');
</script>
@endpush
