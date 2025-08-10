
let hasUnsavedChanges = false;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize main tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('#settingsTabs button[data-bs-toggle="tab"]'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });

    // Initialize student sub-tabs
    var studentSubTabList = [].slice.call(document.querySelectorAll('#studentSubTabs button[data-bs-toggle="tab"]'));
    studentSubTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });

    // Load existing form requirements and settings
    loadFormRequirements();
    loadNavbarSettings();
    loadFooterSettings();
    loadStudentPortalSettings();
    loadHomepageSettings();
    loadHomepageSettings();
    loadProfessorSettings();
    
    // Add new requirement functionality
    const addRequirementButton = document.getElementById('addRequirement');
    if (addRequirementButton) {
        addRequirementButton.addEventListener('click', function() {
            addRequirementField();
            showAlert('New field added. Remember to save your changes!', 'info');
        });
    }
    
    // Track changes in form fields
    document.addEventListener('change', function(e) {
        if (e.target.closest('#studentRequirementsForm')) {
            hasUnsavedChanges = true;
            updateSaveButtonState();
        }
    });
    
    // Track changes in input fields
    document.addEventListener('input', function(e) {
        if (e.target.closest('#studentRequirementsForm')) {
            hasUnsavedChanges = true;
            updateSaveButtonState();
        }
    });
    
    // Warn user about unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });
    
    function updateSaveButtonState() {
        const submitButton = document.querySelector('#studentRequirementsForm button[type="submit"]');
        if (submitButton && hasUnsavedChanges) {
            submitButton.classList.add('btn-warning');
            submitButton.classList.remove('btn-success');
            submitButton.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Save Changes';
        }
    }
    
    function resetSaveButtonState() {
        const submitButton = document.querySelector('#studentRequirementsForm button[type="submit"]');
        if (submitButton) {
            submitButton.classList.remove('btn-warning');
            submitButton.classList.add('btn-success');
            submitButton.innerHTML = '<i class="fas fa-save"></i> Save Form Fields';
        }
        hasUnsavedChanges = false;
    }

    // Navbar color preview functionality
    document.getElementById('previewColors').addEventListener('click', function() {
        previewNavbarColors();
    });

    // Reset colors functionality
    document.getElementById('resetColors').addEventListener('click', function() {
        resetNavbarColors();
    });

    // Save navbar settings
    document.getElementById('navbarSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveNavbarSettings();
    });

    // Save student requirements
    const studentRequirementsForm = document.getElementById('studentRequirementsForm');
    if (studentRequirementsForm) {
        studentRequirementsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateSortOrder(); // Update sort order before saving
            saveFormRequirements();
        });
    }
    
    // Save student portal settings
    const studentPortalForm = document.getElementById('studentPortalForm');
    if (studentPortalForm) {
        studentPortalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveStudentPortalSettings();
        });
    }

    // Save professor features settings
    const professorFeaturesForm = document.getElementById('professorFeaturesForm');
    if (professorFeaturesForm) {
        professorFeaturesForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveProfessorSettings();
        });
    }

    // ✅ Initialize Sortable for requirements container (drag & drop)
    const requirementsContainer = document.getElementById('requirementsContainer');
    if (requirementsContainer) {
        new Sortable(requirementsContainer, {
            animation: 150,
            ghostClass: 'dragging-placeholder',
            handle: '.requirement-handle',
            onEnd: function (evt) {
                console.log(`Item moved: ${evt.oldIndex} -> ${evt.newIndex}`);
                updateSortOrder();
                hasUnsavedChanges = true;
                updateSaveButtonState();
                showAlert('Field order changed. Remember to save your changes!', 'info');
            }
        });
    }
});

function updateSaveButtonState() {
    const submitButton = document.querySelector('#studentRequirementsForm button[type="submit"]');
    if (submitButton && hasUnsavedChanges) {
        submitButton.classList.add('btn-warning');
        submitButton.classList.remove('btn-success');
        submitButton.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Save Changes';
    }
}

function resetSaveButtonState() {
    const submitButton = document.querySelector('#studentRequirementsForm button[type="submit"]');
    if (submitButton) {
        submitButton.classList.remove('btn-warning');
        submitButton.classList.add('btn-success');
        submitButton.innerHTML = '<i class="fas fa-save"></i> Save Form Fields';
    }
    hasUnsavedChanges = false;
}

function loadFormRequirements() {
    console.log('Loading form requirements...');
    
    fetch('/admin/settings/form-requirements')
        .then(response => {
            console.log('Load response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Loaded requirements:', data);
            const container = document.getElementById('requirementsContainer');
            if (!container) {
                console.error('Requirements container not found');
                return;
            }
            
            container.innerHTML = '';
            
            // Sort data by sort_order
            const sortedData = data.sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0));
            
            sortedData.forEach(requirement => {
                addRequirementField(requirement);
            });
            
            if (data.length === 0) {
                // Add default requirements if none exist
                addRequirementField({
                    field_name: 'phone_number',
                    field_label: 'Phone Number', 
                    field_type: 'tel',
                    program_type: 'both',
                    is_required: true,
                    is_active: true
                });
                addRequirementField({
                    field_name: 'tor_document',
                    field_label: 'Transcript of Records (TOR)',
                    field_type: 'file',
                    program_type: 'both', 
                    is_required: true,
                    is_active: true
                });
            }
        })
        .catch(error => {
            console.error('Error loading requirements:', error);
            showAlert('Error loading form requirements: ' + error.message, 'danger');
        });
}
function addRequirementField(data = {}) {
    const container = document.getElementById('requirementsContainer');
    const index = container.children.length;

    const requirementDiv = document.createElement('div');
    // Add visual styling for inactive fields
    const isActive = data.is_active !== false;
    const inactiveClass = !isActive ? ' requirement-inactive' : '';
    const inactiveStyle = !isActive ? ' style="opacity: 0.6; background-color: #f8f9fa;"' : '';
    
    requirementDiv.className = `requirement-item border rounded p-3 mb-3 d-flex align-items-start${inactiveClass}`;
    if (!isActive) {
        requirementDiv.style.opacity = '0.6';
        requirementDiv.style.backgroundColor = '#f8f9fa';
    }
    
    requirementDiv.innerHTML = `
        <div class="requirement-handle me-2 d-flex align-items-center justify-content-center" 
             style="cursor: grab; width: 30px;">
            <i class="fas fa-grip-vertical"></i>
        </div>
        <div class="flex-grow-1">
            ${!isActive ? '<div class="alert alert-warning py-1 px-2 mb-2 small"><i class="fas fa-eye-slash"></i> This field is currently <strong>inactive</strong> and hidden from registration forms</div>' : ''}
            <div class="row gx-2">
                <!-- Section Name (3 cols) -->
                <div class="col-md-3">
                    <label class="form-label">Section Name</label>
                    <input type="text" class="form-control" 
                           name="requirements[${index}][section_name]"
                           value="${data.section_name || ''}" 
                           placeholder="e.g., Personal Information">
                </div>
                <!-- Field Name (2 cols) -->
                <div class="col-md-2">
                    <label class="form-label">Field Name</label>
                    <input type="text" class="form-control" 
                           name="requirements[${index}][field_name]"
                           value="${data.field_name || ''}" 
                           placeholder="e.g., phone_number">
                </div>
                <!-- Display Label (2 cols) -->
                <div class="col-md-2">
                    <label class="form-label">Display Label</label>
                    <input type="text" class="form-control" 
                           name="requirements[${index}][field_label]"
                           value="${data.field_label || ''}" 
                           placeholder="e.g., Phone Number"
                           style="${data.is_bold ? 'font-weight:bold;' : ''}">
                </div>
                <!-- Field Type (2 cols) -->
                <div class="col-md-2">
                    <label class="form-label">Field Type</label>
                    <select class="form-select" 
                            name="requirements[${index}][field_type]" 
                            onchange="handleFieldTypeChange(this)">
                        <option value="text"     ${data.field_type==='text'     ? 'selected':''}>Text</option>
                        <option value="email"    ${data.field_type==='email'    ? 'selected':''}>Email</option>
                        <option value="tel"      ${data.field_type==='tel'      ? 'selected':''}>Phone</option>
                        <option value="date"     ${data.field_type==='date'     ? 'selected':''}>Date</option>
                        <option value="file"     ${data.field_type==='file'     ? 'selected':''}>File</option>
                        <option value="select"   ${data.field_type==='select'   ? 'selected':''}>Dropdown</option>
                        <option value="textarea" ${data.field_type==='textarea' ? 'selected':''}>Textarea</option>
                        <option value="section"  ${data.field_type==='section'  ? 'selected':''}>Section Header</option>
                        <option value="module_selection" ${data.field_type==='module_selection' ? 'selected':''}>Module Selection</option>
                    </select>
                </div>
                <!-- Program (1 col) -->
                <div class="col-md-1">
                    <label class="form-label">Program</label>
                    <select class="form-select" 
                            name="requirements[${index}][program_type]">
                        <option value="both"     ${data.program_type==='both'     ? 'selected':''}>Both</option>
                        <option value="complete" ${data.program_type==='complete' ? 'selected':''}>Complete</option>
                        <option value="modular"  ${data.program_type==='modular'  ? 'selected':''}>Modular</option>
                    </select>
                </div>
                <!-- Required & Active switches (1 col) -->
                <div class="col-md-1 d-flex flex-column align-items-start">
                    <label class="form-label">Required</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="requirements[${index}][is_required]"
                               value="1"
                               ${data.is_required ? 'checked' : ''}>
                    </div>
                    <label class="form-label mt-2">Active</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="requirements[${index}][is_active]"
                               value="1"
                               onchange="toggleFieldActiveStatus(this)"
                               ${isActive ? 'checked' : ''}>
                    </div>
                </div>
                <!-- Bold & Trash buttons (1 col) -->
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm me-1"
                            onclick="toggleBoldText(this)"
                            title="Toggle Bold Text">
                        <i class="fas fa-bold"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="removeRequirement(this)"
                            title="Remove Field">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <!-- Field Options (for select fields) -->
            <div class="field-options-container mt-2" style="display: ${data.field_type === 'select' ? 'block' : 'none'}">
                <label class="form-label">Field Options (one per line)</label>
                <textarea class="form-control" rows="3" 
                          name="requirements[${index}][field_options]"
                          placeholder="Undergraduate&#10;Graduate">${data.field_options ? data.field_options.join('\n') : ''}</textarea>
                <small class="text-muted">Enter each option on a new line</small>
            </div>
            <!-- HIDDEN flags -->
            <input type="hidden" name="requirements[${index}][id]"         value="${data.id || ''}">
            <input type="hidden" name="requirements[${index}][is_bold]"    value="${data.is_bold ? '1' : '0'}">
            <input type="hidden" name="requirements[${index}][sort_order]" value="${data.sort_order || index}">
        </div>
    `;
    container.appendChild(requirementDiv);
}


function removeRequirement(button) {
    const requirementItem = button.closest('.requirement-item');
    const fieldLabel = requirementItem.querySelector('input[name*="[field_label]"]').value || 'this field';
    
    if (confirm(`Are you sure you want to remove "${fieldLabel}"?`)) {
        requirementItem.remove();
        updateSortOrder();
        hasUnsavedChanges = true;
        updateSaveButtonState();
        showAlert('Field removed. Remember to save your changes!', 'warning');
    }
}

function handleFieldTypeChange(selectElement) {
    const row = selectElement.closest('.row');
    const fieldNameInput = row.querySelector('input[name*="[field_name]"]');
    const fieldLabelInput = row.querySelector('input[name*="[field_label]"]');
    const sectionInput = row.querySelector('input[name*="[section_name]"]');
    const optionsContainer = selectElement.closest('.requirement-item').querySelector('.field-options-container');
    
    if (selectElement.value === 'section') {
        fieldNameInput.style.display = 'none';
        fieldLabelInput.style.display = 'none';
        sectionInput.placeholder = 'Section Title (e.g., Personal Information)';
        sectionInput.style.backgroundColor = '#f8f9fa';
        sectionInput.style.fontWeight = 'bold';
        optionsContainer.style.display = 'none';
    } else {
        fieldNameInput.style.display = 'block';
        fieldLabelInput.style.display = 'block';
        sectionInput.placeholder = 'e.g., Personal Information';
        sectionInput.style.backgroundColor = '';
        sectionInput.style.fontWeight = '';
        
        // Show options container only for select fields
        if (selectElement.value === 'select') {
            optionsContainer.style.display = 'block';
        } else {
            optionsContainer.style.display = 'none';
        }
    }
}

function updateSortOrder() {
    const requirementsContainer = document.getElementById('requirementsContainer');
    const items = requirementsContainer.querySelectorAll('.requirement-item');
    
    items.forEach((item, index) => {
        // Find the sort_order hidden input and update it
        const sortOrderInput = item.querySelector('input[name*="[sort_order]"]');
        if (sortOrderInput) {
            sortOrderInput.value = index;
        } else {
            // If no sort_order input exists, create one
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `requirements[${index}][sort_order]`;
            hiddenInput.value = index;
            item.appendChild(hiddenInput);
        }
        
        // Update the index in all input names for this item
        const inputs = item.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name && input.name.includes('requirements[')) {
                const newName = input.name.replace(/requirements\[\d+\]/, `requirements[${index}]`);
                input.name = newName;
            }
        });
    });
}

function saveFormRequirements() {
    const form = document.getElementById('studentRequirementsForm');
    
    // Validate form before saving
    if (!validateRequirementsForm()) {
        return;
    }
    
    // Process form data to handle checkboxes properly
    const formData = new FormData();
    const requirementItems = document.querySelectorAll('.requirement-item');
    
    requirementItems.forEach((item, index) => {
        const data = {
            id: item.querySelector('input[name*="[id]"]')?.value || '',
            section_name: item.querySelector('input[name*="[section_name]"]')?.value || '',
            field_name: item.querySelector('input[name*="[field_name]"]')?.value || '',
            field_label: item.querySelector('input[name*="[field_label]"]')?.value || '',
            field_type: item.querySelector('select[name*="[field_type]"]')?.value || '',
            program_type: item.querySelector('select[name*="[program_type]"]')?.value || '',
            is_required: item.querySelector('input[name*="[is_required]"]')?.checked ? '1' : '0',
            is_active: item.querySelector('input[name*="[is_active]"]')?.checked ? '1' : '0',
            is_bold: item.querySelector('input[name*="[is_bold]"]')?.value || '0',
            sort_order: item.querySelector('input[name*="[sort_order]"]')?.value || index,
            field_options: item.querySelector('textarea[name*="[field_options]"]')?.value || ''
        };
        
        // Add to FormData
        Object.keys(data).forEach(key => {
            formData.append(`requirements[${index}][${key}]`, data[key]);
        });
    });
    
    // Add CSRF token
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    console.log('Saving form requirements...');
    
    // Log form data for debugging
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    submitButton.disabled = true;
    
    fetch('/admin/settings/form-requirements', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showAlert('Form fields saved successfully!', 'success');
            resetSaveButtonState();
            // Reload the requirements to show the updated state
            setTimeout(() => {
                loadFormRequirements();
            }, 1000);
        } else {
            showAlert(data.error || 'Error saving form fields', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving form fields: ' + error.message, 'danger');
    })
    .finally(() => {
        // Reset button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

function validateRequirementsForm() {
    const requirementItems = document.querySelectorAll('.requirement-item');
    let isValid = true;
    let errors = [];
    
    requirementItems.forEach((item, index) => {
        const fieldType = item.querySelector('select[name*="[field_type]"]').value;
        const fieldName = item.querySelector('input[name*="[field_name]"]').value.trim();
        const fieldLabel = item.querySelector('input[name*="[field_label]"]').value.trim();
        const sectionName = item.querySelector('input[name*="[section_name]"]').value.trim();
        
        // Validate based on field type
        if (fieldType === 'section') {
            if (!sectionName) {
                errors.push(`Row ${index + 1}: Section header must have a section name`);
                isValid = false;
            }
        } else {
            if (!fieldName) {
                errors.push(`Row ${index + 1}: Field name is required`);
                isValid = false;
            }
            if (!fieldLabel) {
                errors.push(`Row ${index + 1}: Display label is required`);
                isValid = false;
            }
            
            // Validate field name format
            if (fieldName && !/^[a-zA-Z_][a-zA-Z0-9_]*$/.test(fieldName)) {
                errors.push(`Row ${index + 1}: Field name must start with a letter and contain only letters, numbers, and underscores`);
                isValid = false;
            }
        }
    });
    
    if (!isValid) {
        showAlert('Please fix the following errors:<br>' + errors.join('<br>'), 'danger');
    }
    
    return isValid;
}

function loadNavbarSettings() {
    fetch('/admin/settings/navbar')
        .then(response => response.json())
        .then(data => {
            Object.keys(data).forEach(key => {
                const input = document.querySelector(`input[name="${key}"]`);
                if (input && data[key]) {
                    input.value = data[key];
                }
            });
        })
        .catch(error => {
            console.error('Error loading navbar settings:', error);
        });
}

function loadFooterSettings() {
    fetch('/admin/settings/footer')
        .then(response => response.json())
        .then(data => {
            Object.keys(data).forEach(key => {
                const input = document.querySelector(`input[name="${key}"], textarea[name="${key}"]`);
                if (input && data[key]) {
                    input.value = data[key];
                }
            });
        })
        .catch(error => {
            console.error('Error loading footer settings:', error);
        });
}

function loadStudentPortalSettings() {
    fetch('/admin/settings/student-portal')
        .then(response => response.json())
        .then(data => {
            Object.keys(data).forEach(key => {
                const input = document.querySelector(`#studentPortalForm input[name="${key}"]`);
                if (input && data[key]) {
                    input.value = data[key];
                }
            });
        })
        .catch(error => {
            console.error('Error loading student portal settings:', error);
        });
}

function loadHomepageSettings() {
    console.log('Loading homepage settings...');
    
    fetch('/admin/settings/homepage')
        .then(response => response.json())
        .then(data => {
            console.log('Homepage settings loaded:', data);
            
            // Update hero section form
            const heroForm = document.getElementById('heroSectionForm');
            if (heroForm) {
                Object.keys(data).forEach(key => {
                    const input = heroForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
            
            // Update programs section form
            const programsForm = document.getElementById('programsSectionForm');
            if (programsForm) {
                Object.keys(data).forEach(key => {
                    const input = programsForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
            
            // Update modalities section form
            const modalitiesForm = document.getElementById('modalitiesSectionForm');
            if (modalitiesForm) {
                Object.keys(data).forEach(key => {
                    const input = modalitiesForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
            
            // Update about section form
            const aboutForm = document.getElementById('aboutSectionForm');
            if (aboutForm) {
                Object.keys(data).forEach(key => {
                    const input = aboutForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading homepage settings:', error);
        });
}

function loadNavbarCustomizationSettings() {
    console.log('Loading navbar customization settings...');
    
    fetch('/admin/settings/navbar')
        .then(response => response.json())
        .then(data => {
            console.log('Navbar settings loaded:', data);
            
            // Update navbar customization form
            const navbarForm = document.getElementById('navbarCustomizationForm');
            if (navbarForm) {
                Object.keys(data).forEach(key => {
                    const input = navbarForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
            
            // Update advanced navbar form
            const advancedForm = document.getElementById('advancedNavbarForm');
            if (advancedForm) {
                Object.keys(data).forEach(key => {
                    const input = advancedForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading navbar settings:', error);
        });
}

function loadFooterCustomizationSettings() {
    console.log('Loading footer customization settings...');
    
    fetch('/admin/settings/footer')
        .then(response => response.json())
        .then(data => {
            console.log('Footer settings loaded:', data);
            
            // Update footer form
            const footerForm = document.getElementById('footerCustomizationForm');
            if (footerForm) {
                Object.keys(data).forEach(key => {
                    const input = footerForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading footer settings:', error);
        });
}

function saveAllNavbarSettings() {
    console.log('Saving all navbar settings...');
    
    // Collect all navbar form data
    const allData = {};
    
    // Get data from navbar customization form
    const navbarForm = document.getElementById('navbarCustomizationForm');
    if (navbarForm) {
        const formData = new FormData(navbarForm);
        for (let [key, value] of formData.entries()) {
            allData[key] = value;
        }
    }
    
    // Get data from advanced navbar form
    const advancedForm = document.getElementById('advancedNavbarForm');
    if (advancedForm) {
        const formData = new FormData(advancedForm);
        for (let [key, value] of formData.entries()) {
            allData[key] = value;
        }
    }
    
    // Add CSRF token
    allData._token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/admin/settings/navbar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(allData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Navbar settings saved:', data);
        if (data.success) {
            showAlert('Navbar settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving navbar settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving navbar settings:', error);
        showAlert('Error saving navbar settings: ' + error.message, 'danger');
    });
}

function saveFooterSettings() {
    console.log('Saving footer settings...');
    
    const footerForm = document.getElementById('footerCustomizationForm');
    const formData = new FormData(footerForm);
    
    const allData = {};
    for (let [key, value] of formData.entries()) {
        allData[key] = value;
    }
    
    allData._token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/admin/settings/footer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(allData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Footer settings saved:', data);
        if (data.success) {
            showAlert('Footer settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving footer settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving footer settings:', error);
        showAlert('Error saving footer settings: ' + error.message, 'danger');
    });
}

function previewNavbarColors() {
    showAlert('Preview functionality coming soon!', 'info');
}

function resetNavbarColors() {
    const defaultColors = {
        navbar_bg_color: '#ffffff',
        navbar_text_color: '#333333', 
        navbar_brand_name: 'Ascendo Review and Training Center',
        navbar_hover_color: '#007bff',
        navbar_active_color: '#0056b3',
        header_bg: '#ffffff',
        header_text: '#333333',
        header_border: '#e0e0e0',
        sidebar_bg: '#343a40',
        sidebar_text: '#ffffff',
        active_link_bg: '#007bff'
    };
    
    // Reset navbar customization form
    const navbarForm = document.getElementById('navbarCustomizationForm');
    if (navbarForm) {
        Object.keys(defaultColors).forEach(key => {
            const input = navbarForm.querySelector(`[name="${key}"]`);
            if (input && defaultColors[key]) {
                input.value = defaultColors[key];
            }
        });
    }
    
    // Reset advanced navbar form
    const advancedForm = document.getElementById('advancedNavbarForm');
    if (advancedForm) {
        Object.keys(defaultColors).forEach(key => {
            const input = advancedForm.querySelector(`[name="${key}"]`);
            if (input && defaultColors[key]) {
                input.value = defaultColors[key];
            }
        });
    }
    
    showAlert('Colors reset to defaults', 'info');
}

// Missing utility functions
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
    alertDiv.innerHTML = `
        <div>${message}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-dismiss after 5 seconds (except for error messages)
    if (type !== 'danger') {
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }
        }, 5000);
    }
    
    // Manually dismiss on click
    alertDiv.querySelector('.btn-close').addEventListener('click', () => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 150);
    });
}

function toggleFieldActiveStatus(checkbox) {
    const requirementItem = checkbox.closest('.requirement-item');
    
    if (checkbox.checked) {
        // Field is active
        requirementItem.classList.remove('requirement-inactive');
        requirementItem.style.opacity = '1';
        requirementItem.style.backgroundColor = '';
        
        // Remove inactive warning
        const warningAlert = requirementItem.querySelector('.alert-warning');
        if (warningAlert) {
            warningAlert.remove();
        }
    } else {
        // Field is inactive
        requirementItem.classList.add('requirement-inactive');
        requirementItem.style.opacity = '0.6';
        requirementItem.style.backgroundColor = '#f8f9fa';
        
        // Add inactive warning if not already present
        const flexGrow = requirementItem.querySelector('.flex-grow-1');
        const existingWarning = flexGrow.querySelector('.alert-warning');
        if (!existingWarning) {
            const warningDiv = document.createElement('div');
            warningDiv.className = 'alert alert-warning py-1 px-2 mb-2 small';
            warningDiv.innerHTML = '<i class="fas fa-eye-slash"></i> This field is currently <strong>inactive</strong> and hidden from registration forms';
            flexGrow.insertBefore(warningDiv, flexGrow.firstChild);
        }
    }
    
    // Track changes
    hasUnsavedChanges = true;
    updateSaveButtonState();
    
    // Show immediate feedback
    showAlert('Field status updated. Remember to save your changes!', 'info');
}

function toggleBoldText(button) {
    const requirementItem = button.closest('.requirement-item');
    const labelInput = requirementItem.querySelector('input[name*="[field_label]"]');
    const hiddenBoldInput = requirementItem.querySelector('input[name*="[is_bold]"]');
    
    if (labelInput.style.fontWeight === 'bold') {
        labelInput.style.fontWeight = 'normal';
        button.classList.remove('btn-secondary');
        button.classList.add('btn-outline-secondary');
        if (hiddenBoldInput) {
            hiddenBoldInput.value = '0';
        }
    } else {
        labelInput.style.fontWeight = 'bold';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-secondary');
        if (hiddenBoldInput) {
            hiddenBoldInput.value = '1';
        }
    }
    
    // Track changes
    hasUnsavedChanges = true;
    updateSaveButtonState();
    
    showAlert('Text style updated. Remember to save your changes!', 'info');
}

function saveHomepageSettings() {
    console.log('Saving homepage settings...');
    
    const allData = {};
    
    // Collect data from all homepage forms
    const forms = ['heroSectionForm', 'programsSectionForm', 'modalitiesSectionForm', 'aboutSectionForm'];
    
    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            const formData = new FormData(form);
            for (let [key, value] of formData.entries()) {
                allData[key] = value;
            }
        }
    });
    
    // Add CSRF token
    allData._token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/admin/settings/homepage', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(allData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Homepage settings saved:', data);
        if (data.success) {
            showAlert('Homepage settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving homepage settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving homepage settings:', error);
        showAlert('Error saving homepage settings: ' + error.message, 'danger');
    });
}

function saveStudentPortalSettings() {
    console.log('Saving student portal settings...');
    
    const form = document.getElementById('studentPortalForm');
    const formData = new FormData(form);
    
    fetch('/admin/settings/student-portal', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Student portal settings saved:', data);
        if (data.success) {
            showAlert('Student portal settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving student portal settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving student portal settings:', error);
        showAlert('Error saving student portal settings: ' + error.message, 'danger');
    });
}

function previewForm(type) {
    showAlert(`Preview ${type} form functionality coming soon!`, 'info');
}

// Professor Settings Functions
function loadProfessorSettings() {
    fetch('/admin/settings/professor-features', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Professor settings loaded:', data);
        if (data.success && data.settings) {
            // Set checkbox values based on loaded settings
            Object.keys(data.settings).forEach(key => {
                const checkbox = document.querySelector(`input[name="${key}"]`);
                if (checkbox) {
                    checkbox.checked = data.settings[key] === 'true' || data.settings[key] === true;
                }
            });
        }
    })
    .catch(error => {
        console.error('Error loading professor settings:', error);
        // Set default values if loading fails
        const checkboxes = document.querySelectorAll('#professorFeaturesForm input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true; // Default to enabled
        });
    });
}

function saveProfessorSettings() {
    const form = document.getElementById('professorFeaturesForm');
    const formData = new FormData(form);
    
    // Add unchecked checkboxes as false
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        if (!checkbox.checked) {
            formData.set(checkbox.name, 'false');
        } else {
            formData.set(checkbox.name, 'true');
        }
    });

    fetch('/admin/settings/professor-features', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Professor settings saved:', data);
        if (data.success) {
            showAlert('Professor feature settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving professor settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving professor settings:', error);
        showAlert('Error saving professor settings: ' + error.message, 'danger');
    });
}