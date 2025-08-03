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
        
        const modalDetails = document.getElementById('modal-details');
        const modalActions = document.getElementById('modal-actions');
        
        if (modalDetails) {
            modalDetails.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
        }
        registrationModal.show();

        // Fetch registration details and populate modal
        fetch(`/admin/registration/${registrationId}/details`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch details');
                }
                return response.json();
            })
            .then(data => {
                console.log('Registration details received:', data);
                
                // Use the existing modal population logic if available
                if (typeof populateRegistrationModal === 'function') {
                    populateRegistrationModal(data, modalDetails, modalActions);
                } else {
                    // Fallback: simple data display
                    modalDetails.innerHTML = `
                        <div class="row">
                            <div class="col-12">
                                <h6>Registration Details</h6>
                                <p><strong>Name:</strong> ${data.firstname || 'N/A'} ${data.lastname || 'N/A'}</p>
                                <p><strong>Email:</strong> ${data.email || 'N/A'}</p>
                                <p><strong>Status:</strong> ${data.status || 'N/A'}</p>
                                <p><strong>Program:</strong> ${data.program_name || 'N/A'}</p>
                                <p><strong>Package:</strong> ${data.package_name || 'N/A'}</p>
                                <pre>${JSON.stringify(data, null, 2)}</pre>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching registration details:', error);
                if (modalDetails) {
                    modalDetails.innerHTML = '<div class="alert alert-danger">Error loading registration details</div>';
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
               fetch(`/admin/registration/${registrationId}/undo-approval`, {
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