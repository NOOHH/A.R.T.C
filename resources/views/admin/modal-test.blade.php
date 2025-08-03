<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration Modal Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Registration Modal Test</h1>
        
        <div class="alert alert-info">
            <strong>Testing:</strong> This page tests the registration modal functionality.
        </div>
        
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-primary" onclick="testModal(3)">
                    Test View Registration Details (ID: 3)
                </button>
                
                <button type="button" class="btn btn-secondary" onclick="testAPI(3)">
                    Test API Directly (ID: 3)
                </button>
            </div>
        </div>
        
        <div class="mt-4">
            <h3>Debug Output:</h3>
            <div id="debug-output" class="border p-3 bg-light">
                <em>Debug information will appear here...</em>
            </div>
        </div>
    </div>

    <!-- Include the modal from the admin registration page -->
    <div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrationModalLabel">Registration Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="modal-details">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer" id="modal-actions">
                    <!-- Actions will be populated based on status -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function debugLog(message) {
            const output = document.getElementById('debug-output');
            output.innerHTML += '<div>' + new Date().toLocaleTimeString() + ': ' + message + '</div>';
        }

        function na(value) {
            return (value === undefined || value === null || value === '' || value === 'null') ? 'N/A' : value;
        }

        function testAPI(registrationId) {
            debugLog('Testing API call to /admin/registration/' + registrationId + '/details');
            
            fetch('/admin/registration/' + registrationId + '/details')
                .then(response => {
                    debugLog('Response status: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    debugLog('API Response: ' + JSON.stringify(data, null, 2));
                })
                .catch(error => {
                    debugLog('API Error: ' + error.message);
                });
        }

        function testModal(registrationId) {
            debugLog('Testing modal functionality for registration ID: ' + registrationId);
            
            // Check if modal exists
            const modalElement = document.getElementById('registrationModal');
            if (!modalElement) {
                debugLog('ERROR: Modal element not found!');
                return;
            }
            
            debugLog('Modal element found, initializing...');
            const registrationModal = new bootstrap.Modal(modalElement);
            
            const modalDetails = document.getElementById('modal-details');
            const modalActions = document.getElementById('modal-actions');
            
            if (!modalDetails) {
                debugLog('ERROR: modal-details element not found!');
                return;
            }
            
            debugLog('Setting loading spinner...');
            modalDetails.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><div class="mt-2">Loading...</div></div>';
            
            debugLog('Showing modal...');
            registrationModal.show();
            
            debugLog('Fetching registration details...');
            fetch('/admin/registration/' + registrationId + '/details')
                .then(response => {
                    debugLog('Fetch response status: ' + response.status);
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    debugLog('Data received successfully');
                    
                    // Simple display of data
                    modalDetails.innerHTML = `
                        <div class="col-12">
                            <h6>Registration Details</h6>
                            <table class="table table-striped">
                                <tr><td><strong>Registration ID:</strong></td><td>${na(data.registration_id)}</td></tr>
                                <tr><td><strong>Name:</strong></td><td>${na(data.firstname)} ${na(data.lastname)}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${na(data.email)}</td></tr>
                                <tr><td><strong>Status:</strong></td><td><span class="badge bg-warning">${na(data.status)}</span></td></tr>
                                <tr><td><strong>Program:</strong></td><td>${na(data.program_name)}</td></tr>
                                <tr><td><strong>Package:</strong></td><td>${na(data.package_name)}</td></tr>
                                <tr><td><strong>Created:</strong></td><td>${na(data.created_at)}</td></tr>
                            </table>
                            
                            <h6>Debug Data:</h6>
                            <pre style="max-height: 300px; overflow-y: auto;">${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                    
                    // Add action buttons
                    if (data.status === 'pending') {
                        modalActions.innerHTML = `
                            <button type="button" class="btn btn-success" onclick="approveFromModal(${data.registration_id})">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger" onclick="rejectFromModal(${data.registration_id})">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        `;
                    } else {
                        modalActions.innerHTML = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
                    }
                    
                    debugLog('Modal populated successfully');
                })
                .catch(error => {
                    debugLog('Error: ' + error.message);
                    modalDetails.innerHTML = '<div class="alert alert-danger">Error loading registration details: ' + error.message + '</div>';
                });
        }

        function approveFromModal(registrationId) {
            if (confirm('Are you sure you want to approve this registration?')) {
                debugLog('Approving registration ' + registrationId);
                
                fetch('/admin/registration/' + registrationId + '/approve', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        debugLog('Approval successful!');
                        alert('Registration approved successfully!');
                        bootstrap.Modal.getInstance(document.getElementById('registrationModal')).hide();
                    } else {
                        debugLog('Approval failed: ' + (data.message || 'Unknown error'));
                        alert('Error: ' + (data.message || 'Failed to approve registration'));
                    }
                })
                .catch(error => {
                    debugLog('Approval error: ' + error.message);
                    alert('Error approving registration: ' + error.message);
                });
            }
        }

        function rejectFromModal(registrationId) {
            const reason = prompt('Please enter rejection reason:');
            if (reason) {
                debugLog('Rejecting registration ' + registrationId + ' with reason: ' + reason);
                
                fetch('/admin/registration/' + registrationId + '/reject-with-reason', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ rejection_reason: reason })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        debugLog('Rejection successful!');
                        alert('Registration rejected successfully!');
                        bootstrap.Modal.getInstance(document.getElementById('registrationModal')).hide();
                    } else {
                        debugLog('Rejection failed: ' + (data.message || 'Unknown error'));
                        alert('Error: ' + (data.message || 'Failed to reject registration'));
                    }
                })
                .catch(error => {
                    debugLog('Rejection error: ' + error.message);
                    alert('Error rejecting registration: ' + error.message);
                });
            }
        }

        // Initialize debug
        debugLog('Modal test page loaded');
    </script>
</body>
</html>
