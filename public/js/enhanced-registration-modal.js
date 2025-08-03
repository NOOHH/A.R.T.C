// Enhanced Registration Details Modal JavaScript
// This file handles dynamic display of registration details based on form requirements and enrollment flow

function createEnhancedRegistrationModal(data) {
    // Create modal container
    const modalContainer = document.createElement('div');
    modalContainer.className = 'modal fade';
    modalContainer.id = 'registrationDetailsModal';
    modalContainer.setAttribute('tabindex', '-1');
    modalContainer.setAttribute('aria-labelledby', 'registrationDetailsModalLabel');
    modalContainer.setAttribute('aria-hidden', 'true');

    // Create sections based on form requirements and available data
    const sections = [];

    // User Details (Personal Information) - Only if form requirements exist and have data
    if (data.personal_info && Object.keys(data.personal_info).length > 0) {
        const personalFields = [];
        Object.entries(data.personal_info).forEach(([key, fieldData]) => {
            personalFields.push({
                label: fieldData.label,
                value: fieldData.value
            });
        });
        
        if (personalFields.length > 0) {
            sections.push({
                title: 'User Details',
                icon: 'fas fa-user',
                color: 'primary',
                fields: personalFields
            });
        }
    }

    // Enrollment Details (Combined enrollment flow and details)
    const enrollmentFields = [
        { label: 'Registration ID', value: data.registration_id || 'N/A' },
        { label: 'Program', value: data.program_name || 'N/A' },
        { label: 'Package', value: data.package_name || 'N/A' },
        { label: 'Plan', value: data.plan_name || 'N/A' },
        { label: 'Enrollment Type', value: data.enrollment_type || 'N/A' },
        { label: 'Learning Mode', value: data.learning_mode || 'N/A' },
        { label: 'Course Info', value: data.course_info || 'N/A' },
        { label: 'Status', value: data.status || 'N/A' },
        { label: 'Registered', value: data.created_at || 'N/A' }
    ];

    // Add enrollment info if available
    if (data.enrollment_info) {
        enrollmentFields.push(
            { label: 'Enrollment ID', value: data.enrollment_info.enrollment_id || 'N/A' },
            { label: 'Enrollment Status', value: data.enrollment_info.enrollment_status || 'N/A' },
            { label: 'Payment Status', value: data.enrollment_info.payment_status || 'N/A' },
            { label: 'Progress', value: data.enrollment_info.progress_percentage ? `${data.enrollment_info.progress_percentage}%` : 'N/A' },
            { label: 'Batch ID', value: data.enrollment_info.batch_id || 'N/A' },
            { label: 'Start Date', value: data.enrollment_info.start_date || 'N/A' },
            { label: 'Enrolled', value: data.enrollment_info.created_at || 'N/A' }
        );
    }

    sections.push({
        title: 'Enrollment Details',
        icon: 'fas fa-school',
        color: 'success',
        fields: enrollmentFields
    });

    // Contact Information (Only if form requirements exist and have data)
    if (data.contact_info && Object.keys(data.contact_info).length > 0) {
        const contactFields = [];
        // Always include email if available
        if (data.email && data.email !== 'N/A') {
            contactFields.push({ label: 'Email', value: data.email });
        }
        
        Object.entries(data.contact_info).forEach(([key, fieldData]) => {
            contactFields.push({
                label: fieldData.label,
                value: fieldData.value
            });
        });
        
        if (contactFields.length > 0) {
            sections.push({
                title: 'Contact Information',
                icon: 'fas fa-phone',
                color: 'info',
                fields: contactFields
            });
        }
    }

    // Address Information (Only if form requirements exist and have data)
    if (data.address_info && Object.keys(data.address_info).length > 0) {
        const addressFields = [];
        Object.entries(data.address_info).forEach(([key, fieldData]) => {
            addressFields.push({
                label: fieldData.label,
                value: fieldData.value
            });
        });
        
        if (addressFields.length > 0) {
            sections.push({
                title: 'Address Information',
                icon: 'fas fa-map-marker-alt',
                color: 'warning',
                fields: addressFields
            });
        }
    }

    // Education Information (Only if form requirements exist and have data)
    if (data.education_info && Object.keys(data.education_info).length > 0) {
        const educationFields = [];
        Object.entries(data.education_info).forEach(([key, fieldData]) => {
            educationFields.push({
                label: fieldData.label,
                value: fieldData.value
            });
        });
        
        if (educationFields.length > 0) {
            sections.push({
                title: 'Education Information',
                icon: 'fas fa-graduation-cap',
                color: 'secondary',
                fields: educationFields
            });
        }
    }

    // Documents (Only if form requirements exist and have data)
    if (data.documents && Object.keys(data.documents).length > 0) {
        const documentFields = [];
        Object.entries(data.documents).forEach(([key, fieldData]) => {
            if (typeof fieldData === 'object' && fieldData.label) {
                // New format with metadata
                documentFields.push({
                    label: fieldData.label,
                    value: fieldData.value && fieldData.value !== 'N/A' ? 
                           createFileViewButton(fieldData.value, fieldData.label) : 'Not uploaded'
                });
            } else if (fieldData && fieldData !== 'N/A') {
                // Legacy format
                documentFields.push({
                    label: key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
                    value: createFileViewButton(fieldData, key)
                });
            }
        });
        
        if (documentFields.length > 0) {
            sections.push({
                title: 'Documents',
                icon: 'fas fa-file-alt',
                color: 'danger',
                fields: documentFields
            });
        }
    }

    // Handle Graduate/Undergraduate file uploads with view buttons
    if (data.Graduate && data.Graduate !== 'N/A') {
        const graduateField = {
            label: 'Graduate Documents',
            value: createFileViewButton(data.Graduate, 'Graduate Documents')
        };
        
        // Add to documents section if it exists, otherwise create new section
        let documentsSection = sections.find(s => s.title === 'Documents');
        if (documentsSection) {
            documentsSection.fields.push(graduateField);
        } else {
            sections.push({
                title: 'Documents',
                icon: 'fas fa-file-alt',
                color: 'danger',
                fields: [graduateField]
            });
        }
    }

    if (data.Undergraduate && data.Undergraduate !== 'N/A') {
        const undergraduateField = {
            label: 'Undergraduate Documents',
            value: createFileViewButton(data.Undergraduate, 'Undergraduate Documents')
        };
        
        // Add to documents section if it exists, otherwise create new section
        let documentsSection = sections.find(s => s.title === 'Documents');
        if (documentsSection) {
            documentsSection.fields.push(undergraduateField);
        } else {
            sections.push({
                title: 'Documents',
                icon: 'fas fa-file-alt',
                color: 'danger',
                fields: [undergraduateField]
            });
        }
    }

    // Other Fields (Only if form requirements exist and have data - no redundancy)
    if (data.other_fields && Object.keys(data.other_fields).length > 0) {
        const otherFields = [];
        Object.entries(data.other_fields).forEach(([key, fieldData]) => {
            otherFields.push({
                label: fieldData.label,
                value: fieldData.value
            });
        });
        
        if (otherFields.length > 0) {
            sections.push({
                title: 'Other Information',
                icon: 'fas fa-info-circle',
                color: 'dark',
                fields: otherFields
            });
        }
    }

    // Helper function to create file view buttons
    function createFileViewButton(filename, label) {
        if (!filename || filename === 'N/A') {
            return 'Not uploaded';
        }
        
        const ext = filename.split('.').pop().toLowerCase();
        const isViewable = ['jpg','jpeg','png','gif','webp','pdf'].includes(ext);
        
        if (isViewable) {
            return `File uploaded <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="showDocumentPreview('${filename}','${label}')">
                        <i class="fas fa-eye me-1"></i>View
                    </button>`;
        } else {
            return `<a href="/storage/documents/${filename}" target="_blank" class="text-decoration-none">
                        <i class="fas fa-download me-1"></i>Download ${label}
                    </a>`;
        }
    }

    // Ensure showDocumentPreview function is available globally
    if (typeof window.showDocumentPreview === 'undefined') {
        window.showDocumentPreview = function(filename, label) {
            const baseUrl = window.location.origin;
            const ext = filename.split('.').pop().toLowerCase();
            
            // Create modal if it doesn't exist
            let modal = document.getElementById('documentImageModal');
            if (!modal) {
                modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.id = 'documentImageModal';
                modal.innerHTML = `
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="documentImageModalLabel">${label} Preview</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" id="documentImageModalBody">
                                <!-- Content will be inserted here -->
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }
            
            const modalBody = document.getElementById('documentImageModalBody');
            const modalLabel = document.getElementById('documentImageModalLabel');
            
            let content = '';
            if (["jpg","jpeg","png","gif","webp"].includes(ext)) {
                content = `<img src="${baseUrl}/storage/documents/${filename}" alt="${label}" class="img-fluid" style="max-height:70vh;">`;
            } else if (ext === 'pdf') {
                content = `<iframe src="${baseUrl}/storage/documents/${filename}" style="width:100%;height:70vh;" frameborder="0"></iframe>`;
            } else {
                content = `<a href="${baseUrl}/storage/documents/${filename}" target="_blank">Download ${label}</a>`;
            }
            
            modalBody.innerHTML = content;
            modalLabel.textContent = label + ' Preview';
            
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        };
    }

    // Build modal HTML
    let sectionsHtml = '';
    sections.forEach(section => {
        let fieldsHtml = '';
        section.fields.forEach(field => {
            fieldsHtml += `
                <div class="col-md-6 mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <strong class="text-muted small">${field.label}:</strong>
                        <span class="ms-2 text-end small">${field.value}</span>
                    </div>
                </div>
            `;
        });

        sectionsHtml += `
            <div class="card mb-3">
                <div class="card-header bg-${section.color} text-white py-2">
                    <h6 class="mb-0">
                        <i class="${section.icon} me-2"></i>${section.title}
                        <span class="badge bg-light text-dark ms-2">${section.fields.length} field${section.fields.length !== 1 ? 's' : ''}</span>
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="row">
                        ${fieldsHtml}
                    </div>
                </div>
            </div>
        `;
    });

    modalContainer.innerHTML = `
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="registrationDetailsModalLabel">
                        <i class="fas fa-user-graduate me-2"></i>Registration Details - ${data.registration_id}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    ${sectionsHtml}
                    
                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Note:</strong> This modal shows only the fields that were presented to the student during their specific enrollment flow 
                            based on the admin's dynamic form configuration and the program type they selected.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    `;

    return modalContainer;
}

// Function to show registration details modal
function showRegistrationDetails(registrationId) {
    // Remove existing modal if any
    const existingModal = document.getElementById('registrationDetailsModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Show loading state
    const loadingModal = document.createElement('div');
    loadingModal.className = 'modal fade show d-block';
    loadingModal.style.backgroundColor = 'rgba(0,0,0,0.5)';
    loadingModal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0">Loading registration details...</p>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(loadingModal);

    // Fetch registration details
    fetch(`/admin/registration/${registrationId}/details`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            // Remove loading modal
            loadingModal.remove();
            
            // Create and show the registration details modal
            const modal = createEnhancedRegistrationModal(data);
            document.body.appendChild(modal);
            
            // Initialize Bootstrap modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            // Clean up when modal is hidden
            modal.addEventListener('hidden.bs.modal', function () {
                modal.remove();
            });
        })
        .catch(error => {
            // Remove loading modal
            loadingModal.remove();
            
            // Show error modal
            const errorModal = document.createElement('div');
            errorModal.className = 'modal fade show d-block';
            errorModal.style.backgroundColor = 'rgba(0,0,0,0.5)';
            errorModal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle me-2"></i>Error Loading Registration Details
                            </h5>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">Failed to load registration details: ${error.message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Close</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(errorModal);
            
            // Auto-remove error modal after 5 seconds
            setTimeout(() => {
                if (errorModal.parentNode) {
                    errorModal.remove();
                }
            }, 5000);
            
            console.error('Error loading registration details:', error);
        });
}

// Enhanced view registration details function for compatibility
function viewEnhancedRegistrationDetails(registrationId) {
    showRegistrationDetails(registrationId);
}

// Initialize event handlers when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to all registration detail buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.view-registration-details, .view-registration-details *')) {
            e.preventDefault();
            const button = e.target.closest('.view-registration-details');
            const registrationId = button.getAttribute('data-registration-id');
            if (registrationId) {
                showRegistrationDetails(registrationId);
            }
        }
    });
});

// Replace the global functions for backward compatibility
if (typeof window !== 'undefined') {
    window.viewRegistrationDetails = showRegistrationDetails;
    window.viewRejectedRegistrationDetails = showRegistrationDetails;
    window.viewEnhancedRegistrationDetails = viewEnhancedRegistrationDetails;
    window.showRegistrationDetails = showRegistrationDetails;
}
