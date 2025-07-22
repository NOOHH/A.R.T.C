@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Payment Method Settings')

@push('styles')
<style>
    .payment-method-card {
        border: 1px solid #e9ecef;
        border-radius: 15px;
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .payment-method-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .method-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
    }
    
    .method-body {
        padding: 20px;
    }
    
    .field-item {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .field-info {
        flex-grow: 1;
    }
    
    .field-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }
    
    .field-details {
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .field-actions {
        display: flex;
        gap: 5px;
    }
    
    .add-field-section {
        background: #fff;
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        margin-top: 15px;
        transition: all 0.3s ease;
    }
    
    .add-field-section:hover {
        border-color: #667eea;
        background: #f8f9ff;
    }
    
    .method-status {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .status-toggle {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }
    
    .status-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background-color: #28a745;
    }
    
    input:checked + .slider:before {
        transform: translateX(26px);
    }
    
    .preview-section {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
    }
    
    .preview-form {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-credit-card-2-front me-2"></i>Payment Method Settings</h2>
                <div>
                    <button class="btn btn-outline-primary" onclick="previewStudentView()">
                        <i class="bi bi-eye me-2"></i>Preview Student View
                    </button>
                    <button class="btn btn-primary" onclick="addNewPaymentMethod()">
                        <i class="bi bi-plus-circle me-2"></i>Add Payment Method
                    </button>
                </div>
            </div>
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Dynamic Payment System:</strong> Configure custom fields for each payment method. 
                Students will see these fields when making payments.
            </div>
            
            <!-- Payment Methods List -->
            <div id="paymentMethodsContainer">
                <!-- GCash Method -->
                <div class="payment-method-card" data-method-id="11">
                    <div class="method-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">GCash</h4>
                                <p class="mb-0 opacity-75">Mobile payment via GCash</p>
                            </div>
                            <div class="method-status">
                                <span class="me-2">Status:</span>
                                <label class="status-toggle">
                                    <input type="checkbox" checked onchange="togglePaymentMethod(11, this)">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="method-body">
                        <h6><i class="bi bi-list-ul me-2"></i>Custom Fields</h6>
                        <div class="fields-container" id="fields-11">
                            <!-- Fields will be loaded dynamically -->
                            <div class="text-center text-muted py-3">
                                <div class="spinner-border spinner-border-sm me-2"></div>
                                Loading fields...
                            </div>
                        </div>
                        
                        <div class="add-field-section">
                            <i class="bi bi-plus-circle text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">Add Custom Field</h6>
                            <p class="text-muted mb-3">Students will fill these fields when paying</p>
                            <button class="btn btn-primary" onclick="showAddFieldModal(11)">
                                <i class="bi bi-plus me-2"></i>Add Field
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Bank Transfer Method (Disabled) -->
                <div class="payment-method-card" data-method-id="12">
                    <div class="method-header" style="background: #6c757d;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">Bank Transfer</h4>
                                <p class="mb-0 opacity-75">Direct bank transfer payment</p>
                            </div>
                            <div class="method-status">
                                <span class="me-2">Status:</span>
                                <label class="status-toggle">
                                    <input type="checkbox" onchange="togglePaymentMethod(12, this)">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="method-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            This payment method is disabled. Enable it to configure custom fields.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Field Modal -->
<div class="modal fade" id="addFieldModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Custom Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addFieldForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Field Name *</label>
                                <input type="text" class="form-control" id="fieldName" name="field_name" 
                                       placeholder="e.g., gcash_reference" required>
                                <div class="form-text">Internal name (no spaces, lowercase)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Field Label *</label>
                                <input type="text" class="form-control" id="fieldLabel" name="field_label" 
                                       placeholder="e.g., GCash Reference Number" required>
                                <div class="form-text">Label shown to students</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Field Type *</label>
                                <select class="form-select" id="fieldType" name="field_type" required onchange="toggleFieldOptions()">
                                    <option value="">Select field type</option>
                                    <option value="text">Text Input</option>
                                    <option value="number">Number Input</option>
                                    <option value="date">Date Input</option>
                                    <option value="file">File Upload</option>
                                    <option value="textarea">Text Area</option>
                                    <option value="select">Dropdown Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="sortOrder" name="sort_order" 
                                       value="1" min="1">
                                <div class="form-text">Display order (1 = first)</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="fieldOptionsContainer" style="display: none;">
                        <label class="form-label">Options (for dropdown)</label>
                        <textarea class="form-control" id="fieldOptions" name="field_options" rows="4" 
                                  placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                        <div class="form-text">One option per line</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isRequired" name="is_required" checked>
                            <label class="form-check-label" for="isRequired">
                                Required Field
                            </label>
                        </div>
                    </div>
                    
                    <!-- Live Preview -->
                    <div class="preview-section">
                        <h6><i class="bi bi-eye me-2"></i>Live Preview</h6>
                        <div class="preview-form">
                            <div id="fieldPreview">
                                <em class="text-muted">Configure field settings to see preview</em>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus me-2"></i>Add Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Student View Preview Modal -->
<div class="modal fade" id="studentPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Payment View Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    This is how students will see the payment form based on your configuration.
                </div>
                <div id="studentPreviewContent">
                    <!-- Student view will be rendered here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentMethodId = null;

// Load payment method fields on page load
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentMethodFields(11); // Load GCash fields
});

function loadPaymentMethodFields(methodId) {
    const container = document.getElementById(`fields-${methodId}`);
    
    fetch(`/admin/payment-methods/${methodId}/fields`)
        .then(response => response.json())
        .then(fields => {
            if (fields.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">No custom fields configured yet</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = fields.map(field => `
                <div class="field-item">
                    <div class="field-info">
                        <div class="field-label">${field.field_label}</div>
                        <div class="field-details">
                            <span class="badge bg-secondary me-2">${field.field_type}</span>
                            ${field.is_required ? '<span class="badge bg-danger me-2">Required</span>' : '<span class="badge bg-success me-2">Optional</span>'}
                            <small class="text-muted">Order: ${field.sort_order}</small>
                        </div>
                    </div>
                    <div class="field-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="editField(${field.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteField(${field.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading fields:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading fields. Please refresh the page.
                </div>
            `;
        });
}

function showAddFieldModal(methodId) {
    currentMethodId = methodId;
    document.getElementById('addFieldForm').reset();
    document.getElementById('fieldPreview').innerHTML = '<em class="text-muted">Configure field settings to see preview</em>';
    new bootstrap.Modal(document.getElementById('addFieldModal')).show();
}

function toggleFieldOptions() {
    const fieldType = document.getElementById('fieldType').value;
    const optionsContainer = document.getElementById('fieldOptionsContainer');
    
    if (fieldType === 'select') {
        optionsContainer.style.display = 'block';
    } else {
        optionsContainer.style.display = 'none';
    }
    
    updatePreview();
}

function updatePreview() {
    const label = document.getElementById('fieldLabel').value;
    const type = document.getElementById('fieldType').value;
    const required = document.getElementById('isRequired').checked;
    const options = document.getElementById('fieldOptions').value;
    
    if (!label || !type) {
        document.getElementById('fieldPreview').innerHTML = '<em class="text-muted">Configure field settings to see preview</em>';
        return;
    }
    
    let preview = `<label class="form-label">${label} ${required ? '<span class="text-danger">*</span>' : ''}</label>`;
    
    switch (type) {
        case 'text':
            preview += `<input type="text" class="form-control" placeholder="${label}" ${required ? 'required' : ''}>`;
            break;
        case 'number':
            preview += `<input type="number" class="form-control" placeholder="${label}" ${required ? 'required' : ''}>`;
            break;
        case 'date':
            preview += `<input type="date" class="form-control" ${required ? 'required' : ''}>`;
            break;
        case 'file':
            preview += `<input type="file" class="form-control" accept="image/*" ${required ? 'required' : ''}>`;
            break;
        case 'textarea':
            preview += `<textarea class="form-control" rows="3" placeholder="${label}" ${required ? 'required' : ''}></textarea>`;
            break;
        case 'select':
            const optionList = options.split('\n').filter(opt => opt.trim());
            preview += `<select class="form-select" ${required ? 'required' : ''}>
                <option value="">Choose ${label}</option>
                ${optionList.map(opt => `<option value="${opt.trim()}">${opt.trim()}</option>`).join('')}
            </select>`;
            break;
    }
    
    document.getElementById('fieldPreview').innerHTML = `<div class="mb-3">${preview}</div>`;
}

// Add event listeners for live preview
document.getElementById('fieldLabel').addEventListener('input', updatePreview);
document.getElementById('fieldType').addEventListener('change', updatePreview);
document.getElementById('isRequired').addEventListener('change', updatePreview);
document.getElementById('fieldOptions').addEventListener('input', updatePreview);

// Handle form submission
document.getElementById('addFieldForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const options = formData.get('field_options');
    
    // Convert options to array if it's a select field
    if (formData.get('field_type') === 'select' && options) {
        const optionsArray = options.split('\n').filter(opt => opt.trim()).map(opt => opt.trim());
        formData.set('field_options', JSON.stringify(optionsArray));
    }
    
    fetch(`/admin/payment-methods/${currentMethodId}/fields`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addFieldModal')).hide();
            loadPaymentMethodFields(currentMethodId);
            showAlert('success', 'Field added successfully!');
        } else {
            showAlert('error', data.message || 'Error adding field');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Error adding field');
    });
});

function togglePaymentMethod(methodId, checkbox) {
    const isEnabled = checkbox.checked;
    
    fetch(`/admin/payment-methods/${methodId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ is_enabled: isEnabled })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', `Payment method ${isEnabled ? 'enabled' : 'disabled'} successfully!`);
            // Update UI based on status
            updateMethodCard(methodId, isEnabled);
        } else {
            checkbox.checked = !isEnabled; // Revert checkbox
            showAlert('error', data.message || 'Error updating payment method');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        checkbox.checked = !isEnabled; // Revert checkbox
        showAlert('error', 'Error updating payment method');
    });
}

function updateMethodCard(methodId, isEnabled) {
    const card = document.querySelector(`[data-method-id="${methodId}"]`);
    const header = card.querySelector('.method-header');
    
    if (isEnabled) {
        header.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
    } else {
        header.style.background = '#6c757d';
    }
}

function deleteField(fieldId) {
    if (!confirm('Are you sure you want to delete this field? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/payment-method-fields/${fieldId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadPaymentMethodFields(currentMethodId);
            showAlert('success', 'Field deleted successfully!');
        } else {
            showAlert('error', data.message || 'Error deleting field');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Error deleting field');
    });
}

function previewStudentView() {
    // This would show how the student payment modal would look
    new bootstrap.Modal(document.getElementById('studentPreviewModal')).show();
    
    // Load actual student view preview
    document.getElementById('studentPreviewContent').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="payment-method-card border rounded p-3 mb-3" style="cursor: pointer;">
                    <h5 class="mb-2">GCash</h5>
                    <p class="text-muted mb-0">Mobile payment via GCash</p>
                </div>
            </div>
        </div>
        <div class="border rounded p-4" style="background: #f8f9fa;">
            <h6 class="mb-3">Dynamic Form Fields (as students will see):</h6>
            <div id="dynamicPreviewFields">
                <div class="text-center text-muted">
                    <div class="spinner-border spinner-border-sm me-2"></div>
                    Loading preview...
                </div>
            </div>
        </div>
    `;
    
    // Load actual fields for preview
    fetch('/admin/payment-methods/11/fields')
        .then(response => response.json())
        .then(fields => {
            const container = document.getElementById('dynamicPreviewFields');
            if (fields.length === 0) {
                container.innerHTML = '<p class="text-muted">No fields configured</p>';
                return;
            }
            
            container.innerHTML = fields.map(field => {
                let input = '';
                switch (field.field_type) {
                    case 'text':
                        input = `<input type="text" class="form-control" placeholder="${field.field_label}">`;
                        break;
                    case 'number':
                        input = `<input type="number" class="form-control" placeholder="${field.field_label}">`;
                        break;
                    case 'file':
                        input = `<input type="file" class="form-control" accept="image/*">`;
                        break;
                    case 'textarea':
                        input = `<textarea class="form-control" rows="3" placeholder="${field.field_label}"></textarea>`;
                        break;
                    default:
                        input = `<input type="text" class="form-control" placeholder="${field.field_label}">`;
                }
                
                return `
                    <div class="mb-3">
                        <label class="form-label">
                            ${field.field_label} 
                            ${field.is_required ? '<span class="text-danger">*</span>' : ''}
                        </label>
                        ${input}
                    </div>
                `;
            }).join('');
        });
}

function showAlert(type, message) {
    // Simple alert implementation
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert at top of page
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endpush
