// Admin Functions - Shared across all admin pages
// This file contains common functions used in admin pages

console.log('🔧 Admin functions loaded');

// ========================================
// REGISTRATION FUNCTIONS
// ========================================

// Approve registration function
window.approveRegistration = function(registrationId) {
    console.log('✅ approveRegistration called with ID:', registrationId);
    
    if (confirm('Are you sure you want to approve this registration?')) {
        fetch(`/admin/registration/${registrationId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Registration approved successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to approve registration'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving registration');
        });
    }
};

// Reject registration function
window.rejectRegistration = function(registrationId) {
    console.log('❌ rejectRegistration called with ID:', registrationId);
    
    const reason = prompt('Please provide a reason for rejection:');
    if (reason !== null) {
        fetch(`/admin/registration/${registrationId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Registration rejected successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to reject registration'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting registration');
        });
    }
};

// View registration details function
window.viewRegistrationDetails = function(registrationId) {
    console.log('👁️ viewRegistrationDetails called with ID:', registrationId);
    
    if (!registrationId) {
        alert('Invalid registration ID');
        return;
    }

    // Check if we're on the admin registration page (with modal)
    const modalElement = document.getElementById('registrationModal');
    if (modalElement) {
        // We're on a page with the modal, use it
        console.log('Using modal for registration details');
        
                 // Initialize modal if needed
         let registrationModal = new bootstrap.Modal(modalElement);
         
         const modalDetails = document.getElementById('registration-details-content');
         
         if (modalDetails) {
             modalDetails.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
         }
         registrationModal.show();

        // Fetch registration details and populate modal
        fetch(`/test/registration/${registrationId}/details`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch details');
                }
                return response.json();
            })
                         .then(data => {
                 console.log('Registration details received:', data);
                 
                 if (modalDetails) {
                     // Use the existing modal population logic if available
                     if (typeof populateRegistrationModal === 'function') {
                         populateRegistrationModal(data, modalDetails);
                     } else {
                                              // Enhanced registration details display (similar to payment details modal)
                     function na(value) {
                         return (value === undefined || value === null || value === '' || value === 'null') ? 'N/A' : value;
                     }
                     
                     // Helper function to get user name from various sources
                     function getUserName(data) {
                         // Try user_info first (from API)
                         if (data.user_info && data.user_info.full_name) {
                             return data.user_info.full_name;
                         }
                         
                         // Try individual name fields
                         const firstName = data.firstname || data.user_info?.firstname || '';
                         const lastName = data.lastname || data.user_info?.lastname || '';
                         
                         if (firstName || lastName) {
                             return `${firstName} ${lastName}`.trim();
                         }
                         
                         // Try personal_info fields
                         if (data.personal_info && data.personal_info.firstname) {
                             const first = data.personal_info.firstname.value || '';
                             const last = data.personal_info.lastname?.value || '';
                             return `${first} ${last}`.trim();
                         }
                         
                         return 'N/A';
                     }
                     
                     // Helper function to get contact info
                     function getContactInfo(data) {
                         // Try contact_info fields first
                         if (data.contact_info) {
                             for (const [key, field] of Object.entries(data.contact_info)) {
                                 if (field.value && field.value !== 'N/A') {
                                     return field.value;
                                 }
                             }
                         }
                         
                         // Try direct fields
                         return data.contact_number || data.phone_number || data.mobile_number || 'N/A';
                     }
                     
                     // Helper function to get address info
                     function getAddressInfo(data) {
                         const parts = [];
                         
                         // Try address_info fields first
                         if (data.address_info) {
                             for (const [key, field] of Object.entries(data.address_info)) {
                                 if (field.value && field.value !== 'N/A') {
                                     parts.push(field.value);
                                 }
                             }
                         }
                         
                         // Try direct fields
                         if (data.street_address) parts.push(data.street_address);
                         if (data.city) parts.push(data.city);
                         if (data.state_province) parts.push(data.state_province);
                         
                         return parts.length > 0 ? parts.join(', ') : 'N/A';
                     }
                     
                     // Helper function to get education level
                     function getEducationLevel(data) {
                         // Try education_level_info first (enhanced)
                         if (data.education_level_info && data.education_level_info.level_name) {
                             return data.education_level_info.level_name;
                         }
                         
                         // Try education_info fields
                         if (data.education_info && data.education_info.education_level) {
                             return data.education_info.education_level.value || 'N/A';
                         }
                         
                         // Try direct field
                         return data.education_level || 'N/A';
                     }
                     
                     // Helper function to get document information
                     function getDocumentInfo(data) {
                         let documentHtml = '';
                         
                         // Check if we have education level info with file requirements
                         if (data.education_level_info && data.education_level_info.uploaded_documents && data.education_level_info.uploaded_documents.length > 0) {
                             documentHtml += `<div class="mb-3"><strong>Education Level: ${data.education_level_info.level_name}</strong></div>`;
                             
                             data.education_level_info.uploaded_documents.forEach(doc => {
                                 const statusClass = doc.uploaded ? 'text-success' : 'text-muted';
                                 const statusText = doc.uploaded ? '✓ Uploaded' : 'Not uploaded';
                                 const requiredBadge = doc.is_required ? '<span class="badge bg-danger ms-1">Required</span>' : '';
                                 
                                 if (doc.uploaded) {
                                     documentHtml += `
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>${doc.display_name}:</strong>${requiredBadge}</div>
                                             <div class="col-sm-8">
                                                 <span class="${statusClass}">${statusText}</span>
                                                 <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="viewDocument('${doc.file_path}', '${doc.display_name}')">
                                                     <i class="bi bi-eye"></i> View
                                                 </button>
                                             </div>
                                         </div>
                                     `;
                                 } else {
                                     documentHtml += `
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>${doc.display_name}:</strong>${requiredBadge}</div>
                                             <div class="col-sm-8">
                                                 <span class="${statusClass}">${statusText}</span>
                                             </div>
                                         </div>
                                     `;
                                 }
                             });
                         } else {
                             // Fallback to old method if education level info is not available
                             
                             // Check for documents in categorized data
                             if (data.documents && Object.keys(data.documents).length > 0) {
                                 Object.entries(data.documents).forEach(([key, field]) => {
                                     if (field.value && field.value !== 'N/A') {
                                         documentHtml += `
                                             <div class="row mb-2">
                                                 <div class="col-sm-4"><strong>${field.label}:</strong></div>
                                                 <div class="col-sm-8">
                                                     <span class="text-success">✓ Uploaded</span>
                                                     <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="viewDocument('${field.value}', '${field.label}')">
                                                         <i class="bi bi-eye"></i> View
                                                     </button>
                                                 </div>
                                             </div>
                                         `;
                                     } else {
                                         documentHtml += `
                                             <div class="row mb-2">
                                                 <div class="col-sm-4"><strong>${field.label}:</strong></div>
                                                 <div class="col-sm-8">
                                                     <span class="text-muted">Not uploaded</span>
                                                 </div>
                                             </div>
                                         `;
                                     }
                                 });
                             }
                             
                             // Check for document_info_enhanced
                             if (data.document_info_enhanced && data.document_info_enhanced.uploaded_documents && data.document_info_enhanced.uploaded_documents.length > 0) {
                                 data.document_info_enhanced.uploaded_documents.forEach(doc => {
                                     documentHtml += `
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>${doc.display_name}:</strong></div>
                                             <div class="col-sm-8">
                                                 <span class="text-success">✓ Uploaded</span>
                                                 <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="viewDocument('${doc.file_path}', '${doc.display_name}')">
                                                     <i class="bi bi-eye"></i> View
                                                 </button>
                                             </div>
                                         </div>
                                     `;
                                 });
                             }
                             
                             // Check for direct document fields
                             const documentFields = ['PSA', 'TOR', 'diploma', 'diploma_certificate', 'Course_Cert', 'good_moral', 'photo_2x2', 'valid_id', 'birth_certificate', 'Cert_of_Grad'];
                             documentFields.forEach(fieldName => {
                                 if (data[fieldName] && data[fieldName] !== 'N/A') {
                                     documentHtml += `
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>${fieldName}:</strong></div>
                                             <div class="col-sm-8">
                                                 <span class="text-success">✓ Uploaded</span>
                                                 <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="viewDocument('${data[fieldName]}', '${fieldName}')">
                                                     <i class="bi bi-eye"></i> View
                                                 </button>
                                             </div>
                                         </div>
                                     `;
                                 }
                             });
                         }
                         
                         if (!documentHtml) {
                             documentHtml = '<div class="text-muted">No documents uploaded</div>';
                         }
                         
                         return documentHtml;
                     }
                     
                     // Function to view documents
                     window.viewDocument = function(filePath, documentName) {
                         // Check if it's a URL or file path
                         if (filePath.startsWith('http') || filePath.startsWith('//')) {
                             // External URL
                             window.open(filePath, '_blank');
                         } else {
                             // Local file - construct the URL
                             const baseUrl = window.location.origin;
                             const fullUrl = `${baseUrl}/storage/${filePath}`;
                             window.open(fullUrl, '_blank');
                         }
                     };
                     
                     modalDetails.innerHTML = `
                         <div class="row">
                             <div class="col-md-6">
                                 <div class="card mb-3">
                                     <div class="card-header bg-primary text-white">
                                         <h6 class="mb-0"><i class="bi bi-person"></i> Student Information</h6>
                                     </div>
                                     <div class="card-body">
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Name:</strong></div>
                                             <div class="col-sm-8">${getUserName(data)}</div>
                                         </div>
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Email:</strong></div>
                                             <div class="col-sm-8">${na(data.email || data.user_info?.email)}</div>
                                         </div>
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Contact:</strong></div>
                                             <div class="col-sm-8">${getContactInfo(data)}</div>
                                         </div>
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Address:</strong></div>
                                             <div class="col-sm-8">${getAddressInfo(data)}</div>
                                         </div>
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Account Created:</strong></div>
                                             <div class="col-sm-8">${na(data.user_info?.account_registered_date || data.created_at_formatted || data.created_at)}</div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="card mb-3">
                                     <div class="card-header bg-success text-white">
                                         <h6 class="mb-0"><i class="bi bi-mortarboard"></i> Program Details</h6>
                                     </div>
                                     <div class="card-body">
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Program:</strong></div>
                                             <div class="col-sm-8">${na(data.program_name || data.program?.program_name)}</div>
                                         </div>
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Package:</strong></div>
                                             <div class="col-sm-8">${na(data.package_name || data.package?.package_name)}</div>
                                         </div>
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Plan Type:</strong></div>
                                             <div class="col-sm-8">${na(data.plan_type || data.enrollment_type || data.plan_name || 'Full')}</div>
                                         </div>
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Learning Mode:</strong></div>
                                             <div class="col-sm-8">${na(data.learning_mode)}</div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="col-md-6">
                                 <div class="card mb-3">
                                     <div class="card-header bg-warning text-dark">
                                         <h6 class="mb-0"><i class="bi bi-file-earmark-text"></i> Registration Information</h6>
                                     </div>
                                     <div class="card-body">
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Status:</strong></div>
                                             <div class="col-sm-8">
                                                 <span class="badge ${data.status === 'approved' ? 'bg-success' : data.status === 'rejected' ? 'bg-danger' : 'bg-warning'}">
                                                     ${na(data.status)}
                                                 </span>
                                             </div>
                                         </div>
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Registration ID:</strong></div>
                                             <div class="col-sm-8">${na(data.registration_id)}</div>
                                         </div>
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Education Level:</strong></div>
                                             <div class="col-sm-8">${getEducationLevel(data)}</div>
                                         </div>
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Course Info:</strong></div>
                                             <div class="col-sm-8">${na(data.course_info)}</div>
                                         </div>
                                         <div class="row mb-2">
                                             <div class="col-sm-4"><strong>Registration Date:</strong></div>
                                             <div class="col-sm-8">${na(data.created_at_formatted || data.created_at)}</div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="card mb-3">
                                     <div class="card-header bg-info text-white">
                                         <h6 class="mb-0"><i class="bi bi-file-earmark"></i> Document Information</h6>
                                     </div>
                                     <div class="card-body">
                                         ${getDocumentInfo(data)}
                                     </div>
                                 </div>
                             </div>
                         </div>
                         ${(data.personal_info && Object.keys(data.personal_info).length > 0) || (data.contact_info && Object.keys(data.contact_info).length > 0) || (data.address_info && Object.keys(data.address_info).length > 0) || (data.education_info && Object.keys(data.education_info).length > 0) ? `
                         <div class="row mt-3">
                             <div class="col-12">
                                 <div class="card">
                                     <div class="card-header bg-secondary text-white">
                                         <h6 class="mb-0"><i class="bi bi-person-badge"></i> Additional Information</h6>
                                     </div>
                                     <div class="card-body">
                                         <div class="row">
                                             ${Object.entries(data.personal_info || {}).map(([key, field]) => `
                                                 <div class="col-md-6 mb-2">
                                                     <div class="row">
                                                         <div class="col-sm-4"><strong>${field.label}:</strong></div>
                                                         <div class="col-sm-8">${na(field.value)}</div>
                                                     </div>
                                                 </div>
                                             `).join('')}
                                             ${Object.entries(data.contact_info || {}).map(([key, field]) => `
                                                 <div class="col-md-6 mb-2">
                                                     <div class="row">
                                                         <div class="col-sm-4"><strong>${field.label}:</strong></div>
                                                         <div class="col-sm-8">${na(field.value)}</div>
                                                     </div>
                                                 </div>
                                             `).join('')}
                                             ${Object.entries(data.address_info || {}).map(([key, field]) => `
                                                 <div class="col-md-6 mb-2">
                                                     <div class="row">
                                                         <div class="col-sm-4"><strong>${field.label}:</strong></div>
                                                         <div class="col-sm-8">${na(field.value)}</div>
                                                     </div>
                                                 </div>
                                             `).join('')}
                                             ${Object.entries(data.education_info || {}).map(([key, field]) => `
                                                 <div class="col-md-6 mb-2">
                                                     <div class="row">
                                                         <div class="col-sm-4"><strong>${field.label}:</strong></div>
                                                         <div class="col-sm-8">${na(field.value)}</div>
                                                     </div>
                                                 </div>
                                             `).join('')}
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         ` : ''}
                     `;
                     }
                 } else {
                     console.error('Modal details element not found');
                     alert('Error: Modal details element not found. Please refresh the page and try again.');
                 }
             })
             .catch(error => {
                 console.error('Error fetching registration details:', error);
                 if (modalDetails) {
                     modalDetails.innerHTML = '<div class="alert alert-danger">Error loading registration details</div>';
                 } else {
                     alert('Error loading registration details. Please refresh the page and try again.');
                 }
             });
    } else {
        // No modal available, redirect to details page
        console.log('No modal found, redirecting to details page');
        window.location.href = `/admin/registration/${registrationId}`;
    }
};

               // Undo approval function
        window.undoApproval = function(registrationId) {
            console.log('↩️ undoApproval called with ID:', registrationId);

            if (confirm('Are you sure you want to undo the approval for this registration?')) {
                fetch(`/admin/registrations/${registrationId}/undo-approval`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Approval undone successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to undo approval'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error undoing approval');
                });
            }
        };

       // View rejected registration details function
       window.viewRejectedRegistrationDetails = function(registrationId) {
           console.log('👁️ viewRejectedRegistrationDetails called with ID:', registrationId);

           // Redirect to registration details page
           window.location.href = `/admin/registration/${registrationId}`;
       };

       // Edit rejected fields function
       window.editRejectedFields = function(registrationId) {
           console.log('✏️ editRejectedFields called with ID:', registrationId);

           // Show the edit rejected fields modal
           const modal = document.getElementById('editRejectedFieldsModal');
           if (modal) {
               const bootstrapModal = new bootstrap.Modal(modal);
               bootstrapModal.show();
               
               // Set the form action
               const form = document.getElementById('editRejectedFieldsForm');
               if (form) {
                   form.action = `/admin/registration/${registrationId}/update-rejection`;
               }
           } else {
               console.error('Edit rejected fields modal not found');
               alert('Modal not found. Please refresh the page and try again.');
           }
       };

       // Undo rejection function
       window.undoRejection = function(registrationId) {
           console.log('↩️ undoRejection called with ID:', registrationId);

           if (confirm('Are you sure you want to undo the rejection for this registration?')) {
               fetch(`/admin/registration/${registrationId}/undo-rejection`, {
                   method: 'POST',
                   headers: {
                       'Content-Type': 'application/json',
                       'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                   }
               })
               .then(response => response.json())
               .then(data => {
                   if (data.success) {
                       alert('Rejection undone successfully!');
                       location.reload();
                   } else {
                       alert('Error: ' + (data.message || 'Failed to undo rejection'));
                   }
               })
               .catch(error => {
                   console.error('Error:', error);
                   alert('Error undoing rejection');
               });
           }
       };

// ========================================
// ENROLLMENT FUNCTIONS
// ========================================

// View enrollment details function
window.viewDetails = function(enrollmentId) {
    console.log('👁️ viewDetails called with ID:', enrollmentId);
    
    // Redirect to enrollment details page
    window.location.href = `/admin/enrollment/${enrollmentId}`;
};

// Mark payment as paid function
window.markAsPaid = function(enrollmentId) {
    console.log('💰 markAsPaid called with ID:', enrollmentId);
    
    if (confirm('Are you sure you want to mark this payment as completed?')) {
        fetch(`/admin/enrollment/${enrollmentId}/mark-paid`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment marked as completed!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to mark payment as completed'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error marking payment as completed');
        });
    }
};

// Retry payment function
window.retryPayment = function(enrollmentId) {
    console.log('🔄 retryPayment called with ID:', enrollmentId);
    
    if (confirm('Are you sure you want to retry this payment?')) {
        fetch(`/admin/enrollment/${enrollmentId}/retry-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment retry initiated');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to retry payment'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error retrying payment');
        });
    }
};

// ========================================
// STUDENT FUNCTIONS
// ========================================

// View student details function
window.viewStudentDetails = function(studentId) {
    console.log('👁️ viewStudentDetails called with ID:', studentId);
    
    // Redirect to student details page
    window.location.href = `/admin/student/${studentId}`;
};

// Approve student function
window.approveStudent = function(studentId) {
    console.log('✅ approveStudent called with ID:', studentId);
    
    if (confirm('Are you sure you want to approve this student?')) {
        fetch(`/admin/student/${studentId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Student approved successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to approve student'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving student');
        });
    }
};

// Reject student function
window.rejectStudent = function(studentId) {
    console.log('❌ rejectStudent called with ID:', studentId);
    
    const reason = prompt('Please provide a reason for rejection:');
    if (reason !== null) {
        fetch(`/admin/student/${studentId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Student rejected successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to reject student'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting student');
        });
    }
};

// ========================================
// UTILITY FUNCTIONS
// ========================================

// Generic confirmation function
window.confirmAction = function(message, callback) {
    if (confirm(message)) {
        callback();
    }
};

// Generic success message function
window.showSuccessMessage = function(message) {
    alert(message);
};

// Generic error message function
window.showErrorMessage = function(message) {
    alert('Error: ' + message);
};

// Generic AJAX function
window.makeAjaxRequest = function(url, method, data, successCallback, errorCallback) {
    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    const options = {
        method: method,
        headers: headers
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    fetch(url, options)
        .then(response => response.json())
        .then(data => {
            if (successCallback) {
                successCallback(data);
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            if (errorCallback) {
                errorCallback(error);
            }
        });
};

// ========================================
// INITIALIZATION
// ========================================

// Initialize admin functions when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Admin functions initialized');
    
               // Log available functions for debugging
           console.log('🔧 Available admin functions:', {
               'approveRegistration': typeof window.approveRegistration,
               'rejectRegistration': typeof window.rejectRegistration,
               'viewRegistrationDetails': typeof window.viewRegistrationDetails,
               'undoApproval': typeof window.undoApproval,
               'viewRejectedRegistrationDetails': typeof window.viewRejectedRegistrationDetails,
               'editRejectedFields': typeof window.editRejectedFields,
               'undoRejection': typeof window.undoRejection,
               'viewDetails': typeof window.viewDetails,
               'markAsPaid': typeof window.markAsPaid,
               'retryPayment': typeof window.retryPayment,
               'viewStudentDetails': typeof window.viewStudentDetails,
               'approveStudent': typeof window.approveStudent,
               'rejectStudent': typeof window.rejectStudent
           });
});

console.log('✅ Admin functions file loaded successfully'); 