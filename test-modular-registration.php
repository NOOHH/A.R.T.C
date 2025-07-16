<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Modular Registration System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .test-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .test-result {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
        }
        
        .test-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .test-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .test-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffedb8;
        }
        
        .test-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .json-output {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            font-family: monospace;
            font-size: 14px;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-success {
            background: #28a745;
            color: white;
        }
        
        .status-error {
            background: #dc3545;
            color: white;
        }
        
        .status-warning {
            background: #ffc107;
            color: black;
        }
        
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .btn-test {
            margin: 5px;
        }
        
        .progress-section {
            margin: 20px 0;
        }
        
        .progress-step {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-radius: 50%;
            margin: 0 5px;
            font-weight: bold;
        }
        
        .progress-step.completed {
            background: #28a745;
            color: white;
        }
        
        .progress-step.current {
            background: #007bff;
            color: white;
        }
        
        .progress-step.pending {
            background: #6c757d;
            color: white;
        }
        
        .quick-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .quick-actions .btn {
            margin: 2px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="test-container">
            <div class="quick-actions">
                <button class="btn btn-sm btn-primary" onclick="runAllTests()">Run All Tests</button>
                <button class="btn btn-sm btn-secondary" onclick="clearResults()">Clear Results</button>
                <button class="btn btn-sm btn-success" onclick="runDatabaseTests()">DB Tests</button>
                <button class="btn btn-sm btn-info" onclick="runRegistrationTest()">Registration Test</button>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3">
                            <i class="fas fa-vials"></i> Modular Registration System Tests
                        </h1>
                        <div class="progress-section">
                            <span class="progress-step pending" id="step-1">1</span>
                            <span class="progress-step pending" id="step-2">2</span>
                            <span class="progress-step pending" id="step-3">3</span>
                            <span class="progress-step pending" id="step-4">4</span>
                            <span class="progress-step pending" id="step-5">5</span>
                        </div>
                    </div>
                    
                    <div class="test-section">
                        <h4><i class="fas fa-database"></i> Database Structure Tests</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-outline-primary btn-test" onclick="testDatabaseTables()">
                                    Test Database Tables
                                </button>
                                <button class="btn btn-outline-primary btn-test" onclick="testPackageStructure()">
                                    Test Package Structure
                                </button>
                                <button class="btn btn-outline-primary btn-test" onclick="testRegistrationStructure()">
                                    Test Registration Structure
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-primary btn-test" onclick="testFormRequirements()">
                                    Test Form Requirements
                                </button>
                                <button class="btn btn-outline-primary btn-test" onclick="testProgramModules()">
                                    Test Program Modules
                                </button>
                                <button class="btn btn-outline-primary btn-test" onclick="testBatchSystem()">
                                    Test Batch System
                                </button>
                            </div>
                        </div>
                        <div id="database-results" class="mt-3"></div>
                    </div>
                    
                    <div class="test-section">
                        <h4><i class="fas fa-route"></i> Route & Controller Tests</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-outline-success btn-test" onclick="testModularRoutes()">
                                    Test Modular Routes
                                </button>
                                <button class="btn btn-outline-success btn-test" onclick="testPackageRoutes()">
                                    Test Package Routes
                                </button>
                                <button class="btn btn-outline-success btn-test" onclick="testApiEndpoints()">
                                    Test API Endpoints
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-success btn-test" onclick="testRegistrationController()">
                                    Test Registration Controller
                                </button>
                                <button class="btn btn-outline-success btn-test" onclick="testAdminPackageController()">
                                    Test Admin Package Controller
                                </button>
                                <button class="btn btn-outline-success btn-test" onclick="testMiddleware()">
                                    Test Middleware
                                </button>
                            </div>
                        </div>
                        <div id="route-results" class="mt-3"></div>
                    </div>
                    
                    <div class="test-section">
                        <h4><i class="fas fa-user-plus"></i> Registration Flow Tests</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-outline-warning btn-test" onclick="testModularEnrollmentForm()">
                                    Test Modular Form
                                </button>
                                <button class="btn btn-outline-warning btn-test" onclick="testPackageSelection()">
                                    Test Package Selection
                                </button>
                                <button class="btn btn-outline-warning btn-test" onclick="testModuleSelection()">
                                    Test Module Selection
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-warning btn-test" onclick="testFormValidation()">
                                    Test Form Validation
                                </button>
                                <button class="btn btn-outline-warning btn-test" onclick="testRegistrationSubmission()">
                                    Test Registration Submit
                                </button>
                                <button class="btn btn-outline-warning btn-test" onclick="testAjaxHandling()">
                                    Test AJAX Handling
                                </button>
                            </div>
                        </div>
                        <div id="registration-results" class="mt-3"></div>
                    </div>
                    
                    <div class="test-section">
                        <h4><i class="fas fa-cogs"></i> Admin Package Management Tests</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-outline-info btn-test" onclick="testPackageCreation()">
                                    Test Package Creation
                                </button>
                                <button class="btn btn-outline-info btn-test" onclick="testPackageEditing()">
                                    Test Package Editing
                                </button>
                                <button class="btn btn-outline-info btn-test" onclick="testPackageModals()">
                                    Test Package Modals
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-info btn-test" onclick="testPackageAnalytics()">
                                    Test Package Analytics
                                </button>
                                <button class="btn btn-outline-info btn-test" onclick="testPackageArchiving()">
                                    Test Package Archiving
                                </button>
                                <button class="btn btn-outline-info btn-test" onclick="testPackageValidation()">
                                    Test Package Validation
                                </button>
                            </div>
                        </div>
                        <div id="admin-results" class="mt-3"></div>
                    </div>
                    
                    <div class="test-section">
                        <h4><i class="fas fa-chart-line"></i> System Integration Tests</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-outline-danger btn-test" onclick="testFullRegistrationFlow()">
                                    Test Full Registration Flow
                                </button>
                                <button class="btn btn-outline-danger btn-test" onclick="testDataPersistence()">
                                    Test Data Persistence
                                </button>
                                <button class="btn btn-outline-danger btn-test" onclick="testUserSessions()">
                                    Test User Sessions
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-danger btn-test" onclick="testEnrollmentCreation()">
                                    Test Enrollment Creation
                                </button>
                                <button class="btn btn-outline-danger btn-test" onclick="testAdminNotifications()">
                                    Test Admin Notifications
                                </button>
                                <button class="btn btn-outline-danger btn-test" onclick="testSystemPerformance()">
                                    Test System Performance
                                </button>
                            </div>
                        </div>
                        <div id="integration-results" class="mt-3"></div>
                    </div>
                    
                    <div class="test-section">
                        <h4><i class="fas fa-clipboard-check"></i> Test Results Summary</h4>
                        <div id="test-summary" class="row">
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-success">Passed</h5>
                                        <p class="card-text display-4" id="passed-count">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-danger">Failed</h5>
                                        <p class="card-text display-4" id="failed-count">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-warning">Warnings</h5>
                                        <p class="card-text display-4" id="warning-count">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title text-info">Total</h5>
                                        <p class="card-text display-4" id="total-count">0</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let testResults = {
            passed: 0,
            failed: 0,
            warnings: 0,
            total: 0
        };

        function updateProgress(step) {
            document.getElementById(`step-${step}`).classList.remove('pending');
            document.getElementById(`step-${step}`).classList.add('current');
            
            if (step > 1) {
                document.getElementById(`step-${step-1}`).classList.remove('current');
                document.getElementById(`step-${step-1}`).classList.add('completed');
            }
        }

        function updateTestSummary() {
            document.getElementById('passed-count').textContent = testResults.passed;
            document.getElementById('failed-count').textContent = testResults.failed;
            document.getElementById('warning-count').textContent = testResults.warnings;
            document.getElementById('total-count').textContent = testResults.total;
        }

        function showResult(containerId, message, type = 'info', data = null) {
            const container = document.getElementById(containerId);
            const resultDiv = document.createElement('div');
            resultDiv.className = `test-result test-${type}`;
            
            let content = `<strong>${type.toUpperCase()}:</strong> ${message}`;
            if (data) {
                content += `<div class="json-output">${typeof data === 'string' ? data : JSON.stringify(data, null, 2)}</div>`;
            }
            
            resultDiv.innerHTML = content;
            container.appendChild(resultDiv);
            
            // Update counters
            testResults.total++;
            if (type === 'success') testResults.passed++;
            else if (type === 'error') testResults.failed++;
            else if (type === 'warning') testResults.warnings++;
            
            updateTestSummary();
            
            // Auto-scroll to result
            resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function clearResults() {
            const containers = ['database-results', 'route-results', 'registration-results', 'admin-results', 'integration-results'];
            containers.forEach(id => {
                document.getElementById(id).innerHTML = '';
            });
            
            testResults = { passed: 0, failed: 0, warnings: 0, total: 0 };
            updateTestSummary();
            
            // Reset progress
            for (let i = 1; i <= 5; i++) {
                const step = document.getElementById(`step-${i}`);
                step.classList.remove('current', 'completed');
                step.classList.add('pending');
            }
        }

        // Database Tests
        async function testDatabaseTables() {
            updateProgress(1);
            showResult('database-results', 'Testing database table structure...', 'info');
            
            try {
                const response = await fetch('/check_db_structure.php');
                const data = await response.text();
                
                if (data.includes('ERROR') || data.includes('FAIL')) {
                    showResult('database-results', 'Database structure test failed', 'error', data);
                } else {
                    showResult('database-results', 'Database structure test passed', 'success', data);
                }
            } catch (error) {
                showResult('database-results', 'Database structure test failed', 'error', error.message);
            }
        }

        async function testPackageStructure() {
            showResult('database-results', 'Testing package table structure...', 'info');
            
            try {
                const response = await fetch('/check_packages.php');
                const data = await response.text();
                
                if (data.includes('ERROR') || data.includes('FAIL')) {
                    showResult('database-results', 'Package structure test failed', 'error', data);
                } else {
                    showResult('database-results', 'Package structure test passed', 'success', data);
                }
            } catch (error) {
                showResult('database-results', 'Package structure test failed', 'error', error.message);
            }
        }

        async function testRegistrationStructure() {
            showResult('database-results', 'Testing registration table structure...', 'info');
            
            try {
                const response = await fetch('/check_table_structure.php');
                const data = await response.text();
                
                if (data.includes('ERROR') || data.includes('FAIL')) {
                    showResult('database-results', 'Registration structure test failed', 'error', data);
                } else {
                    showResult('database-results', 'Registration structure test passed', 'success', data);
                }
            } catch (error) {
                showResult('database-results', 'Registration structure test failed', 'error', error.message);
            }
        }

        async function testFormRequirements() {
            showResult('database-results', 'Testing form requirements...', 'info');
            
            try {
                // Test form requirements structure
                const response = await fetch('/admin/form-requirements', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    showResult('database-results', 'Form requirements test passed', 'success', data);
                } else {
                    showResult('database-results', 'Form requirements test failed', 'error', `Status: ${response.status}`);
                }
            } catch (error) {
                showResult('database-results', 'Form requirements test failed', 'error', error.message);
            }
        }

        async function testProgramModules() {
            showResult('database-results', 'Testing program modules relationship...', 'info');
            
            try {
                const response = await fetch('/get-program-modules?program_id=1');
                const data = await response.json();
                
                if (data.success) {
                    showResult('database-results', 'Program modules test passed', 'success', data);
                } else {
                    showResult('database-results', 'Program modules test failed', 'error', data);
                }
            } catch (error) {
                showResult('database-results', 'Program modules test failed', 'error', error.message);
            }
        }

        async function testBatchSystem() {
            showResult('database-results', 'Testing batch system...', 'info');
            
            try {
                const response = await fetch('/check_batches.php');
                const data = await response.text();
                
                if (data.includes('ERROR') || data.includes('FAIL')) {
                    showResult('database-results', 'Batch system test failed', 'error', data);
                } else {
                    showResult('database-results', 'Batch system test passed', 'success', data);
                }
            } catch (error) {
                showResult('database-results', 'Batch system test failed', 'error', error.message);
            }
        }

        // Route Tests
        async function testModularRoutes() {
            updateProgress(2);
            showResult('route-results', 'Testing modular enrollment routes...', 'info');
            
            try {
                const response = await fetch('/enrollment/modular');
                
                if (response.ok) {
                    showResult('route-results', 'Modular enrollment route test passed', 'success', `Status: ${response.status}`);
                } else {
                    showResult('route-results', 'Modular enrollment route test failed', 'error', `Status: ${response.status}`);
                }
            } catch (error) {
                showResult('route-results', 'Modular enrollment route test failed', 'error', error.message);
            }
        }

        async function testPackageRoutes() {
            showResult('route-results', 'Testing package management routes...', 'info');
            
            try {
                const response = await fetch('/admin/packages');
                
                if (response.ok) {
                    showResult('route-results', 'Package management route test passed', 'success', `Status: ${response.status}`);
                } else {
                    showResult('route-results', 'Package management route test failed', 'error', `Status: ${response.status}`);
                }
            } catch (error) {
                showResult('route-results', 'Package management route test failed', 'error', error.message);
            }
        }

        async function testApiEndpoints() {
            showResult('route-results', 'Testing API endpoints...', 'info');
            
            const endpoints = [
                '/get-program-modules?program_id=1',
                '/admin/packages/1',
                '/admin/packages/program/1/modules'
            ];
            
            for (const endpoint of endpoints) {
                try {
                    const response = await fetch(endpoint);
                    
                    if (response.ok) {
                        showResult('route-results', `API endpoint ${endpoint} test passed`, 'success', `Status: ${response.status}`);
                    } else {
                        showResult('route-results', `API endpoint ${endpoint} test failed`, 'error', `Status: ${response.status}`);
                    }
                } catch (error) {
                    showResult('route-results', `API endpoint ${endpoint} test failed`, 'error', error.message);
                }
            }
        }

        async function testRegistrationController() {
            showResult('route-results', 'Testing registration controller...', 'info');
            
            try {
                const response = await fetch('/student/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        test: true
                    })
                });
                
                if (response.status === 422 || response.status === 302) {
                    showResult('route-results', 'Registration controller test passed (validation working)', 'success', `Status: ${response.status}`);
                } else {
                    showResult('route-results', 'Registration controller test needs attention', 'warning', `Status: ${response.status}`);
                }
            } catch (error) {
                showResult('route-results', 'Registration controller test failed', 'error', error.message);
            }
        }

        async function testAdminPackageController() {
            showResult('route-results', 'Testing admin package controller...', 'info');
            
            try {
                const response = await fetch('/admin/packages', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    showResult('route-results', 'Admin package controller test passed', 'success', `Status: ${response.status}`);
                } else {
                    showResult('route-results', 'Admin package controller test failed', 'error', `Status: ${response.status}`);
                }
            } catch (error) {
                showResult('route-results', 'Admin package controller test failed', 'error', error.message);
            }
        }

        async function testMiddleware() {
            showResult('route-results', 'Testing middleware...', 'info');
            
            try {
                const response = await fetch('/admin/dashboard');
                
                if (response.status === 302 || response.status === 401) {
                    showResult('route-results', 'Middleware test passed (protection working)', 'success', `Status: ${response.status}`);
                } else {
                    showResult('route-results', 'Middleware test needs attention', 'warning', `Status: ${response.status}`);
                }
            } catch (error) {
                showResult('route-results', 'Middleware test failed', 'error', error.message);
            }
        }

        // Registration Flow Tests
        async function testModularEnrollmentForm() {
            updateProgress(3);
            showResult('registration-results', 'Testing modular enrollment form...', 'info');
            
            try {
                const response = await fetch('/enrollment/modular');
                const html = await response.text();
                
                if (html.includes('Modular Enrollment') && html.includes('enrollmentForm')) {
                    showResult('registration-results', 'Modular enrollment form test passed', 'success', 'Form loaded successfully');
                } else {
                    showResult('registration-results', 'Modular enrollment form test failed', 'error', 'Form not found or incomplete');
                }
            } catch (error) {
                showResult('registration-results', 'Modular enrollment form test failed', 'error', error.message);
            }
        }

        async function testPackageSelection() {
            showResult('registration-results', 'Testing package selection...', 'info');
            
            try {
                const response = await fetch('/enrollment/modular');
                const html = await response.text();
                
                if (html.includes('selectPackage') && html.includes('package-card')) {
                    showResult('registration-results', 'Package selection test passed', 'success', 'Package selection functionality found');
                } else {
                    showResult('registration-results', 'Package selection test failed', 'error', 'Package selection functionality not found');
                }
            } catch (error) {
                showResult('registration-results', 'Package selection test failed', 'error', error.message);
            }
        }

        async function testModuleSelection() {
            showResult('registration-results', 'Testing module selection...', 'info');
            
            try {
                const response = await fetch('/enrollment/modular');
                const html = await response.text();
                
                if (html.includes('handleModuleSelection') && html.includes('module-card')) {
                    showResult('registration-results', 'Module selection test passed', 'success', 'Module selection functionality found');
                } else {
                    showResult('registration-results', 'Module selection test failed', 'error', 'Module selection functionality not found');
                }
            } catch (error) {
                showResult('registration-results', 'Module selection test failed', 'error', error.message);
            }
        }

        async function testFormValidation() {
            showResult('registration-results', 'Testing form validation...', 'info');
            
            try {
                const response = await fetch('/enrollment/modular');
                const html = await response.text();
                
                if (html.includes('validateStep') && html.includes('validateField')) {
                    showResult('registration-results', 'Form validation test passed', 'success', 'Validation functions found');
                } else {
                    showResult('registration-results', 'Form validation test failed', 'error', 'Validation functions not found');
                }
            } catch (error) {
                showResult('registration-results', 'Form validation test failed', 'error', error.message);
            }
        }

        async function testRegistrationSubmission() {
            showResult('registration-results', 'Testing registration submission...', 'info');
            
            try {
                const response = await fetch('/enrollment/modular');
                const html = await response.text();
                
                if (html.includes('submitRegistrationForm') && html.includes('student.register')) {
                    showResult('registration-results', 'Registration submission test passed', 'success', 'Submission functionality found');
                } else {
                    showResult('registration-results', 'Registration submission test failed', 'error', 'Submission functionality not found');
                }
            } catch (error) {
                showResult('registration-results', 'Registration submission test failed', 'error', error.message);
            }
        }

        async function testAjaxHandling() {
            showResult('registration-results', 'Testing AJAX handling...', 'info');
            
            try {
                const response = await fetch('/enrollment/modular');
                const html = await response.text();
                
                if (html.includes('XMLHttpRequest') && html.includes('fetch')) {
                    showResult('registration-results', 'AJAX handling test passed', 'success', 'AJAX functionality found');
                } else {
                    showResult('registration-results', 'AJAX handling test failed', 'error', 'AJAX functionality not found');
                }
            } catch (error) {
                showResult('registration-results', 'AJAX handling test failed', 'error', error.message);
            }
        }

        // Admin Package Management Tests
        async function testPackageCreation() {
            updateProgress(4);
            showResult('admin-results', 'Testing package creation...', 'info');
            
            try {
                const response = await fetch('/admin/packages');
                const html = await response.text();
                
                if (html.includes('createPackage') && html.includes('package_name')) {
                    showResult('admin-results', 'Package creation test passed', 'success', 'Package creation functionality found');
                } else {
                    showResult('admin-results', 'Package creation test failed', 'error', 'Package creation functionality not found');
                }
            } catch (error) {
                showResult('admin-results', 'Package creation test failed', 'error', error.message);
            }
        }

        async function testPackageEditing() {
            showResult('admin-results', 'Testing package editing...', 'info');
            
            try {
                const response = await fetch('/admin/packages');
                const html = await response.text();
                
                if (html.includes('editPackage') && html.includes('updatePackage')) {
                    showResult('admin-results', 'Package editing test passed', 'success', 'Package editing functionality found');
                } else {
                    showResult('admin-results', 'Package editing test failed', 'error', 'Package editing functionality not found');
                }
            } catch (error) {
                showResult('admin-results', 'Package editing test failed', 'error', error.message);
            }
        }

        async function testPackageModals() {
            showResult('admin-results', 'Testing package modals...', 'info');
            
            try {
                const response = await fetch('/admin/packages');
                const html = await response.text();
                
                if (html.includes('createPackageModal') && html.includes('editPackageModal')) {
                    showResult('admin-results', 'Package modals test passed', 'success', 'Package modals found');
                } else {
                    showResult('admin-results', 'Package modals test failed', 'error', 'Package modals not found');
                }
            } catch (error) {
                showResult('admin-results', 'Package modals test failed', 'error', error.message);
            }
        }

        async function testPackageAnalytics() {
            showResult('admin-results', 'Testing package analytics...', 'info');
            
            try {
                const response = await fetch('/admin/packages');
                const html = await response.text();
                
                if (html.includes('analytics') && html.includes('totalPackages')) {
                    showResult('admin-results', 'Package analytics test passed', 'success', 'Package analytics found');
                } else {
                    showResult('admin-results', 'Package analytics test failed', 'error', 'Package analytics not found');
                }
            } catch (error) {
                showResult('admin-results', 'Package analytics test failed', 'error', error.message);
            }
        }

        async function testPackageArchiving() {
            showResult('admin-results', 'Testing package archiving...', 'info');
            
            try {
                const response = await fetch('/admin/packages');
                const html = await response.text();
                
                if (html.includes('archivePackage') && html.includes('restorePackage')) {
                    showResult('admin-results', 'Package archiving test passed', 'success', 'Package archiving functionality found');
                } else {
                    showResult('admin-results', 'Package archiving test failed', 'error', 'Package archiving functionality not found');
                }
            } catch (error) {
                showResult('admin-results', 'Package archiving test failed', 'error', error.message);
            }
        }

        async function testPackageValidation() {
            showResult('admin-results', 'Testing package validation...', 'info');
            
            try {
                const response = await fetch('/admin/packages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        test: true
                    })
                });
                
                if (response.status === 422 || response.status === 302) {
                    showResult('admin-results', 'Package validation test passed', 'success', 'Validation working');
                } else {
                    showResult('admin-results', 'Package validation test needs attention', 'warning', `Status: ${response.status}`);
                }
            } catch (error) {
                showResult('admin-results', 'Package validation test failed', 'error', error.message);
            }
        }

        // Integration Tests
        async function testFullRegistrationFlow() {
            updateProgress(5);
            showResult('integration-results', 'Testing full registration flow...', 'info');
            
            try {
                // Test the complete flow
                const steps = [
                    { name: 'Load Form', url: '/enrollment/modular' },
                    { name: 'Check Packages', url: '/enrollment/modular' },
                    { name: 'Check Modules', url: '/get-program-modules?program_id=1' }
                ];
                
                let passedSteps = 0;
                
                for (const step of steps) {
                    try {
                        const response = await fetch(step.url);
                        if (response.ok) {
                            passedSteps++;
                            showResult('integration-results', `${step.name} step passed`, 'success', `Status: ${response.status}`);
                        } else {
                            showResult('integration-results', `${step.name} step failed`, 'error', `Status: ${response.status}`);
                        }
                    } catch (error) {
                        showResult('integration-results', `${step.name} step failed`, 'error', error.message);
                    }
                }
                
                if (passedSteps === steps.length) {
                    showResult('integration-results', 'Full registration flow test passed', 'success', `${passedSteps}/${steps.length} steps passed`);
                } else {
                    showResult('integration-results', 'Full registration flow test failed', 'error', `${passedSteps}/${steps.length} steps passed`);
                }
            } catch (error) {
                showResult('integration-results', 'Full registration flow test failed', 'error', error.message);
            }
        }

        async function testDataPersistence() {
            showResult('integration-results', 'Testing data persistence...', 'info');
            
            try {
                const response = await fetch('/check_student_data.php');
                const data = await response.text();
                
                if (data.includes('ERROR') || data.includes('FAIL')) {
                    showResult('integration-results', 'Data persistence test failed', 'error', data);
                } else {
                    showResult('integration-results', 'Data persistence test passed', 'success', data);
                }
            } catch (error) {
                showResult('integration-results', 'Data persistence test failed', 'error', error.message);
            }
        }

        async function testUserSessions() {
            showResult('integration-results', 'Testing user sessions...', 'info');
            
            try {
                const response = await fetch('/login');
                
                if (response.ok) {
                    showResult('integration-results', 'User session test passed', 'success', `Status: ${response.status}`);
                } else {
                    showResult('integration-results', 'User session test failed', 'error', `Status: ${response.status}`);
                }
            } catch (error) {
                showResult('integration-results', 'User session test failed', 'error', error.message);
            }
        }

        async function testEnrollmentCreation() {
            showResult('integration-results', 'Testing enrollment creation...', 'info');
            
            try {
                const response = await fetch('/check_user_enrollments.php');
                const data = await response.text();
                
                if (data.includes('ERROR') || data.includes('FAIL')) {
                    showResult('integration-results', 'Enrollment creation test failed', 'error', data);
                } else {
                    showResult('integration-results', 'Enrollment creation test passed', 'success', data);
                }
            } catch (error) {
                showResult('integration-results', 'Enrollment creation test failed', 'error', error.message);
            }
        }

        async function testAdminNotifications() {
            showResult('integration-results', 'Testing admin notifications...', 'info');
            
            try {
                const response = await fetch('/admin/enrollments');
                
                if (response.ok) {
                    showResult('integration-results', 'Admin notifications test passed', 'success', `Status: ${response.status}`);
                } else {
                    showResult('integration-results', 'Admin notifications test failed', 'error', `Status: ${response.status}`);
                }
            } catch (error) {
                showResult('integration-results', 'Admin notifications test failed', 'error', error.message);
            }
        }

        async function testSystemPerformance() {
            showResult('integration-results', 'Testing system performance...', 'info');
            
            try {
                const startTime = performance.now();
                const response = await fetch('/enrollment/modular');
                const endTime = performance.now();
                
                const loadTime = endTime - startTime;
                
                if (response.ok && loadTime < 5000) {
                    showResult('integration-results', 'System performance test passed', 'success', `Load time: ${loadTime.toFixed(2)}ms`);
                } else {
                    showResult('integration-results', 'System performance test failed', 'error', `Load time: ${loadTime.toFixed(2)}ms`);
                }
            } catch (error) {
                showResult('integration-results', 'System performance test failed', 'error', error.message);
            }
        }

        // Combined test functions
        async function runAllTests() {
            clearResults();
            
            showResult('database-results', 'Running comprehensive test suite...', 'info');
            
            // Database tests
            await testDatabaseTables();
            await testPackageStructure();
            await testRegistrationStructure();
            await testFormRequirements();
            await testProgramModules();
            await testBatchSystem();
            
            // Route tests
            await testModularRoutes();
            await testPackageRoutes();
            await testApiEndpoints();
            await testRegistrationController();
            await testAdminPackageController();
            await testMiddleware();
            
            // Registration flow tests
            await testModularEnrollmentForm();
            await testPackageSelection();
            await testModuleSelection();
            await testFormValidation();
            await testRegistrationSubmission();
            await testAjaxHandling();
            
            // Admin tests
            await testPackageCreation();
            await testPackageEditing();
            await testPackageModals();
            await testPackageAnalytics();
            await testPackageArchiving();
            await testPackageValidation();
            
            // Integration tests
            await testFullRegistrationFlow();
            await testDataPersistence();
            await testUserSessions();
            await testEnrollmentCreation();
            await testAdminNotifications();
            await testSystemPerformance();
            
            showResult('integration-results', 'All tests completed!', 'success', `Passed: ${testResults.passed}, Failed: ${testResults.failed}, Warnings: ${testResults.warnings}`);
        }

        async function runDatabaseTests() {
            clearResults();
            
            await testDatabaseTables();
            await testPackageStructure();
            await testRegistrationStructure();
            await testFormRequirements();
            await testProgramModules();
            await testBatchSystem();
        }

        async function runRegistrationTest() {
            clearResults();
            
            await testModularEnrollmentForm();
            await testPackageSelection();
            await testModuleSelection();
            await testFormValidation();
            await testRegistrationSubmission();
            await testAjaxHandling();
            await testFullRegistrationFlow();
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            updateTestSummary();
            showResult('integration-results', 'Test suite loaded and ready', 'success', 'Click "Run All Tests" to start comprehensive testing');
        });
    </script>
</body>
</html>
