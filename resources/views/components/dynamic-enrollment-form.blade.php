{{-- Dynamic Enrollment Form Component --}}
<div class="dynamic-form-container">
    @php
        $currentSection = null;
    @endphp

    @foreach($requirements as $requirement)

        {{-- Check if this is a section type --}}
        @if($requirement->field_type === 'section')
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="border-bottom pb-2 mb-3 text-primary fw-bold">
                        {{ $requirement->section_name }}
                    </h4>
                </div>
            </div>
            @php
                $currentSection = $requirement->section_name;
            @endphp
            @continue
        @endif

        {{-- Check and print section header for regular fields --}}
        @if ($requirement->section_name && $requirement->section_name !== $currentSection)
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="border-bottom pb-2 mb-3 text-primary fw-bold">
                        {{ $requirement->section_name }}
                    </h4>
                </div>
            </div>
            @php
                $currentSection = $requirement->section_name;
            @endphp
        @endif

        <div class="form-group mb-3" data-field="{{ $requirement->field_name }}">
            @if($requirement->field_type === 'text' || $requirement->field_type === 'email' || $requirement->field_type === 'tel')
                <label for="{{ $requirement->field_name }}" class="form-label {{ $requirement->is_bold ? 'fw-bold' : '' }}">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                <input type="{{ $requirement->field_type }}" 
                       class="form-control" 
                       id="{{ $requirement->field_name }}" 
                       name="{{ $requirement->field_name }}"
                       placeholder="{{ $requirement->field_label }}"
                       @if($requirement->is_required) required @endif>
            
            @elseif($requirement->field_type === 'date')
                <label for="{{ $requirement->field_name }}" class="form-label {{ $requirement->is_bold ? 'fw-bold' : '' }}">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                <input type="date" 
                       class="form-control" 
                       id="{{ $requirement->field_name }}" 
                       name="{{ $requirement->field_name }}"
                       @if($requirement->is_required) required @endif>
            
            @elseif($requirement->field_type === 'file')
                <label for="{{ $requirement->field_name }}" class="form-label {{ $requirement->is_bold ? 'fw-bold' : '' }}">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                <div class="file-upload-container">
                    <button type="button" class="btn btn-outline-primary file-upload-btn" data-field="{{ $requirement->field_name }}">
                        <i class="bi bi-cloud-upload"></i> {{ $requirement->field_label }}
                    </button>
                    <input type="file" 
                           class="form-control d-none" 
                           id="{{ $requirement->field_name }}" 
                           name="{{ $requirement->field_name }}"
                           @if($requirement->is_required) required @endif>
                    <div class="file-status mt-2" id="status-{{ $requirement->field_name }}"></div>
                </div>
            
            @elseif($requirement->field_type === 'textarea')
                <label for="{{ $requirement->field_name }}" class="form-label {{ $requirement->is_bold ? 'fw-bold' : '' }}">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                <textarea class="form-control" 
                          id="{{ $requirement->field_name }}" 
                          name="{{ $requirement->field_name }}"
                          rows="3"
                          placeholder="{{ $requirement->field_label }}"
                          @if($requirement->is_required) required @endif></textarea>
            
            @elseif($requirement->field_type === 'number')
                <label for="{{ $requirement->field_name }}" class="form-label {{ $requirement->is_bold ? 'fw-bold' : '' }}">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                <input type="number" 
                       class="form-control" 
                       id="{{ $requirement->field_name }}" 
                       name="{{ $requirement->field_name }}"
                       placeholder="{{ $requirement->field_label }}"
                       @if($requirement->is_required) required @endif>
                       
            @elseif($requirement->field_type === 'checkbox')
                <div class="form-check">
                    <input type="checkbox" 
                           class="form-check-input" 
                           id="{{ $requirement->field_name }}" 
                           name="{{ $requirement->field_name }}"
                           value="1"
                           @if($requirement->is_required) required @endif>
                    <label class="form-check-label {{ $requirement->is_bold ? 'fw-bold' : '' }}" for="{{ $requirement->field_name }}">
                        {{ $requirement->field_label }}
                        @if($requirement->is_required)<span class="text-danger">*</span>@endif
                    </label>
                </div>
                
            @elseif($requirement->field_type === 'radio')
                <label class="form-label {{ $requirement->is_bold ? 'fw-bold' : '' }}">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                @if($requirement->field_options)
                    @foreach($requirement->field_options as $index => $option)
                        <div class="form-check">
                            <input type="radio" 
                                   class="form-check-input" 
                                   id="{{ $requirement->field_name }}_{{ $index }}" 
                                   name="{{ $requirement->field_name }}"
                                   value="{{ $option }}"
                                   @if($requirement->is_required) required @endif>
                            <label class="form-check-label" for="{{ $requirement->field_name }}_{{ $index }}">
                                {{ $option }}
                            </label>
                        </div>
                    @endforeach
                @endif
            
            @elseif($requirement->field_type === 'select')
                <label for="{{ $requirement->field_name }}" class="form-label {{ $requirement->is_bold ? 'fw-bold' : '' }}">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                
                {{-- Check if this is education_level field for special button group styling --}}
                @if($requirement->field_name === 'education_level' && $requirement->field_options)
                    <div class="education-buttons d-flex justify-content-center gap-3 mb-2">
                        @foreach($requirement->field_options as $index => $option)
                            <button type="button" 
                                    class="btn btn-outline-{{ $index === 0 ? 'primary' : 'success' }} education-btn" 
                                    data-education="{{ strtolower($option) }}" 
                                    onclick="selectEducationLevel('{{ strtolower($option) }}', '{{ $requirement->field_name }}')">
                                <i class="bi bi-{{ $option === 'Undergraduate' ? 'mortarboard' : 'award' }} me-2"></i>{{ $option }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" 
                           id="{{ $requirement->field_name }}" 
                           name="{{ $requirement->field_name }}"
                           value="{{ old($requirement->field_name) }}"
                           @if($requirement->is_required) required @endif>
                @else
                    {{-- Regular select dropdown --}}
                    <select class="form-select" 
                            id="{{ $requirement->field_name }}" 
                            name="{{ $requirement->field_name }}"
                            @if($requirement->is_required) required @endif>
                        <option value="">Select an option</option>
                        @if($requirement->field_options)
                            @foreach($requirement->field_options as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        @endif
                    </select>
                @endif
                
            @elseif($requirement->field_type === 'module_selection')
                <label for="{{ $requirement->field_name }}" class="form-label {{ $requirement->is_bold ? 'fw-bold' : '' }}">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                @php
                    // Get available modules for the current program
                    $availableModules = \App\Models\Module::where('program_id', session('selected_program_id', 1))
                        ->where('is_archived', false)
                        ->orderBy('modules_id')
                        ->get();
                @endphp
                
                @if($availableModules->count() > 0)
                    <div class="module-selection-container">
                        @foreach($availableModules as $module)
                            <div class="form-check mb-2">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="module_{{ $module->modules_id }}" 
                                       name="{{ $requirement->field_name }}[]"
                                       value="{{ $module->modules_id }}"
                                       @if($requirement->is_required && $loop->first) required @endif>
                                <label class="form-check-label" for="module_{{ $module->modules_id }}">
                                    <strong>{{ $module->module_name }}</strong>
                                    @if($module->module_description)
                                        <br><small class="text-muted">{{ $module->module_description }}</small>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> No modules are currently available for this program.
                    </div>
                @endif
            @endif
            
            {{-- Display validation error for this field --}}
            @error($requirement->field_name)
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
    @endforeach
</div>

@push('styles')
<style>
.file-upload-btn {
    width: 100%;
    padding: 12px;
    border: 2px dashed #007bff;
    border-radius: 8px;
    background: transparent;
    transition: all 0.3s ease;
}

.file-upload-btn:hover {
    background: rgba(0, 123, 255, 0.1);
    border-color: #0056b3;
}

.file-status {
    font-size: 0.875rem;
    color: #6c757d;
}

.file-status.success {
    color: #28a745;
}

.file-status.error {
    color: #dc3545;
}

.file-status.warning {
    color: #ffc107;
}

.file-status.info {
    color: #17a2b8;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.dynamic-form-container .form-control:focus,
.dynamic-form-container .form-select:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.module-selection-container {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    background-color: #f8f9fa;
    max-height: 300px;
    overflow-y: auto;
}

.module-selection-container .form-check {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 0.5rem;
    margin-bottom: 0.75rem;
}

.module-selection-container .form-check:last-child {
    border-bottom: none;
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle file upload buttons
    document.querySelectorAll('.file-upload-btn').forEach(button => {
        button.addEventListener('click', function() {
            const fieldName = this.dataset.field;
            const fileInput = document.getElementById(fieldName);
            fileInput.click();
        });
    });
    
    // Handle file input changes with OCR processing
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const fieldName = this.name;
            const statusDiv = document.getElementById(`status-${fieldName}`);
            const button = document.querySelector(`[data-field="${fieldName}"]`);
            
            if (this.files.length > 0) {
                const file = this.files[0];
                
                // Show file selected status
                statusDiv.innerHTML = `<i class="bi bi-check-circle text-success"></i> ${file.name} selected`;
                statusDiv.className = 'file-status success';
                button.innerHTML = `<i class="bi bi-check-circle"></i> ${file.name}`;
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-success');
                
                // Check if this is a document that should be OCR processed
                // Use dynamic pattern matching based on field name keywords
                const shouldProcessOCR = 
                    fieldName.toLowerCase().includes('certificate') ||
                    fieldName.toLowerCase().includes('diploma') ||
                    fieldName.toLowerCase().includes('transcript') ||
                    fieldName.toLowerCase().includes('birth_certificate') ||
                    fieldName.toLowerCase().includes('moral') ||
                    fieldName.toLowerCase().includes('psa') ||
                    fieldName.toLowerCase().includes('tor') ||
                    fieldName.toLowerCase().includes('school_id') ||
                    fieldName.toLowerCase().includes('graduation') ||
                    fieldName.toLowerCase().includes('course_cert') ||
                    fieldName.toLowerCase().includes('good_moral') ||
                    fieldName.toLowerCase().includes('photo') ||
                    fieldName.toLowerCase().includes('identification') ||
                    fieldName.toLowerCase().includes('document') ||
                    fieldName.toLowerCase().includes('record');
                
                if (shouldProcessOCR) {
                    processFileWithOCR(file, fieldName, statusDiv);
                }
            } else {
                statusDiv.innerHTML = '';
                statusDiv.className = 'file-status';
                const originalLabel = button.closest('.form-group').querySelector('label').textContent.replace('*', '').trim();
                button.innerHTML = `<i class="bi bi-cloud-upload"></i> ${originalLabel}`;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
            }
        });
    });

    // File upload modal for viewing uploaded files
    const fileUploadModal = `
    <div class="modal fade" id="fileViewModal" tabindex="-1" aria-labelledby="fileViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileViewModalLabel">View Uploaded File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="fileViewContent">
                        <!-- File content will be loaded here -->
                    </div>
                    <div id="ocrResultsContent" class="mt-3">
                        <!-- OCR results will be displayed here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="removeUploadedFile()">Remove File</button>
                </div>
            </div>
        </div>
    </div>
    `;

    // Add modal to page if it doesn't exist
    if (!document.getElementById('fileViewModal')) {
        document.body.insertAdjacentHTML('beforeend', fileUploadModal);
    }

    // Enhanced file upload handling with view capability
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fieldName = this.name;
            const button = document.querySelector(`[data-field="${fieldName}"]`);
            const statusDiv = document.getElementById(`status-${fieldName}`);
            
            if (file) {
                // Update button to show file selected
                button.innerHTML = `<i class="bi bi-check-circle"></i> ${file.name}`;
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-success');
                
                // Add view button
                const viewButton = document.createElement('button');
                viewButton.type = 'button';
                viewButton.className = 'btn btn-outline-info btn-sm ms-2';
                viewButton.innerHTML = '<i class="bi bi-eye"></i> View';
                viewButton.onclick = () => viewUploadedFile(fieldName, file);
                
                // Add view button after the main button
                if (!button.parentNode.querySelector('.btn-outline-info')) {
                    button.parentNode.appendChild(viewButton);
                }
                
                // Check if this is a document that should be OCR processed
                // Use dynamic pattern matching based on field name keywords
                const shouldProcessOCR = 
                    fieldName.toLowerCase().includes('certificate') ||
                    fieldName.toLowerCase().includes('diploma') ||
                    fieldName.toLowerCase().includes('transcript') ||
                    fieldName.toLowerCase().includes('birth_certificate') ||
                    fieldName.toLowerCase().includes('moral') ||
                    fieldName.toLowerCase().includes('psa') ||
                    fieldName.toLowerCase().includes('tor') ||
                    fieldName.toLowerCase().includes('school_id') ||
                    fieldName.toLowerCase().includes('graduation') ||
                    fieldName.toLowerCase().includes('course_cert') ||
                    fieldName.toLowerCase().includes('good_moral') ||
                    fieldName.toLowerCase().includes('photo') ||
                    fieldName.toLowerCase().includes('identification') ||
                    fieldName.toLowerCase().includes('document') ||
                    fieldName.toLowerCase().includes('record');
                
                if (shouldProcessOCR) {
                    processFileWithOCR(file, fieldName, statusDiv);
                }
            } else {
                // Reset button state
                statusDiv.innerHTML = '';
                statusDiv.className = 'file-status';
                const originalLabel = button.closest('.form-group').querySelector('label').textContent.replace('*', '').trim();
                button.innerHTML = `<i class="bi bi-cloud-upload"></i> ${originalLabel}`;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
                
                // Remove view button
                const viewButton = button.parentNode.querySelector('.btn-outline-info');
                if (viewButton) {
                    viewButton.remove();
                }
            }
        });
    });

    // OCR processing function
    function processFileWithOCR(file, fieldName, statusDiv) {
        // Show OCR processing status
        const processingHtml = `
            <div class="mt-2">
                <div class="d-flex align-items-center text-info">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    <small>Processing document with OCR...</small>
                </div>
            </div>
        `;
        statusDiv.innerHTML += processingHtml;

        // Create form data for upload
        const formData = new FormData();
        formData.append('file', file);
        formData.append('field_name', fieldName);
        formData.append('first_name', document.querySelector('[name="firstname"]')?.value || '');
        formData.append('last_name', document.querySelector('[name="lastname"]')?.value || '');
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }

        // Send to OCR validation endpoint
        fetch('/admin/validate-file', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success with OCR results
                let ocrInfo = '';
                if (data.suggestions && data.suggestions.length > 0) {
                    ocrInfo += '<div class="mt-1"><small class="text-muted">Suggested programs based on document:</small>';
                    data.suggestions.forEach(suggestion => {
                        ocrInfo += `<br><small class="text-primary">• ${suggestion.program.program_name}</small>`;
                    });
                    ocrInfo += '</div>';
                }
                
                if (data.certificate_level) {
                    ocrInfo += `<div class="mt-1"><small class="text-muted">Detected level: <span class="text-info">${data.certificate_level}</span></small></div>`;
                }
                
                statusDiv.innerHTML = `
                    <i class="bi bi-check-circle text-success"></i> ${file.name} uploaded and verified
                    ${ocrInfo}
                `;
                statusDiv.className = 'file-status success';
            } else {
                // Show error
                statusDiv.innerHTML = `
                    <div class="text-warning">
                        <i class="bi bi-exclamation-triangle"></i> ${file.name} uploaded with warnings
                        <br><small>${data.message || 'Document validation failed'}</small>
                    </div>
                `;
                statusDiv.className = 'file-status warning';
            }
        })
        .catch(error => {
            console.error('OCR processing failed:', error);
            statusDiv.innerHTML = `
                <div class="text-info">
                    <i class="bi bi-info-circle"></i> ${file.name} uploaded (OCR processing unavailable)
                    <br><small>File uploaded successfully, manual verification may be required</small>
                </div>
            `;
            statusDiv.className = 'file-status info';
        });
    }

    // Function to view uploaded file
    window.viewUploadedFile = function(fieldName, file) {
        const modal = new bootstrap.Modal(document.getElementById('fileViewModal'));
        const modalTitle = document.getElementById('fileViewModalLabel');
        const fileContent = document.getElementById('fileViewContent');
        const ocrContent = document.getElementById('ocrResultsContent');
        
        modalTitle.textContent = `View: ${file.name}`;
        
        // Display file based on type
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                fileContent.innerHTML = `
                    <div class="text-center">
                        <img src="${e.target.result}" class="img-fluid" style="max-height: 400px;" alt="Uploaded file">
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            fileContent.innerHTML = `
                <div class="text-center">
                    <i class="bi bi-file-earmark-pdf display-1 text-danger"></i>
                    <p class="mt-2">PDF File: ${file.name}</p>
                    <p class="text-muted">Size: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                </div>
            `;
        } else {
            fileContent.innerHTML = `
                <div class="text-center">
                    <i class="bi bi-file-earmark display-1 text-secondary"></i>
                    <p class="mt-2">Document: ${file.name}</p>
                    <p class="text-muted">Size: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                </div>
            `;
        }
        
        // Show OCR results if available
        const statusDiv = document.getElementById(`status-${fieldName}`);
        if (statusDiv && statusDiv.innerHTML.includes('Document processed successfully')) {
            ocrContent.innerHTML = `
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> Document processed successfully with OCR
                </div>
            `;
        } else if (statusDiv && statusDiv.innerHTML.includes('Error processing')) {
            ocrContent.innerHTML = `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> OCR processing encountered issues
                </div>
            `;
        } else {
            ocrContent.innerHTML = '';
        }
        
        // Store current field for removal function
        modal._currentField = fieldName;
        modal.show();
    }

    // Function to remove uploaded file
    window.removeUploadedFile = function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('fileViewModal'));
        const fieldName = modal._currentField;
        
        if (fieldName) {
            const fileInput = document.querySelector(`input[name="${fieldName}"]`);
            const button = document.querySelector(`[data-field="${fieldName}"]`);
            const statusDiv = document.getElementById(`status-${fieldName}`);
            
            // Reset file input
            if (fileInput) {
                fileInput.value = '';
            }
            
            // Reset button state
            if (button) {
                const originalLabel = button.closest('.form-group').querySelector('label').textContent.replace('*', '').trim();
                button.innerHTML = `<i class="bi bi-cloud-upload"></i> ${originalLabel}`;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
            }
            
            // Clear status
            if (statusDiv) {
                statusDiv.innerHTML = '';
                statusDiv.className = 'file-status';
            }
            
            // Remove view button
            const viewButton = button?.parentNode.querySelector('.btn-outline-info');
            if (viewButton) {
                viewButton.remove();
            }
            
            modal.hide();
        }
    }

    // Handle dynamic education level selection
    window.selectEducationLevel = function(level, fieldName) {
        const buttons = document.querySelectorAll('.education-btn');
        const hiddenInput = document.getElementById(fieldName);
        
        // Remove active class from all buttons
        buttons.forEach(btn => {
            btn.classList.remove('btn-primary', 'btn-success');
            if (btn.dataset.education === 'undergraduate') {
                btn.classList.add('btn-outline-primary');
            } else {
                btn.classList.add('btn-outline-success');
            }
        });
        
        // Add active class to selected button
        const selectedBtn = document.querySelector(`[data-education="${level}"]`);
        if (selectedBtn) {
            if (level === 'undergraduate') {
                selectedBtn.classList.remove('btn-outline-primary');
                selectedBtn.classList.add('btn-primary');
            } else {
                selectedBtn.classList.remove('btn-outline-success');
                selectedBtn.classList.add('btn-success');
            }
        }
        
        // Set hidden input value (capitalize first letter)
        hiddenInput.value = level.charAt(0).toUpperCase() + level.slice(1);
        
        console.log('Education level selected:', level);
        
        // Trigger change event for form validation
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    };
});

</script>
@endpush
