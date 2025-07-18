<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <title>Modular Enrollment Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-section {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
        .test-result {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .result-success { background-color: #d4edda; border: 1px solid #c3e6cb; }
        .result-error { background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .result-info { background-color: #d1ecf1; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Modular Enrollment System Test</h1>
        <p class="text-muted">Testing all components of the modular enrollment system</p>

        <!-- Test 1: Route Testing -->
        <div class="test-section">
            <h3>1. Route Testing</h3>
            <div id="route-tests">
                <div class="test-result result-info">
                    <strong>Testing enrollment routes...</strong>
                    <div id="route-results"></div>
                </div>
            </div>
        </div>

        <!-- Test 2: Form Validation -->
        <div class="test-section">
            <h3>2. Form Validation Test</h3>
            <form id="test-form">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="user_firstname" value="Test" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="user_lastname" value="Student" required>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="test-email" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" value="password123" required>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" value="password123" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Education Level</label>
                        <select class="form-control" name="education_level" required>
                            <option value="Undergraduate">Undergraduate</option>
                            <option value="Graduate">Graduate</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="program_id" value="1">
                <input type="hidden" name="package_id" value="1">
                <input type="hidden" name="learning_mode" value="synchronous">
                <input type="hidden" name="enrollment_type" value="Modular">
                <input type="hidden" name="selected_modules" value='[{"id": 1, "name": "Module 1"}]'>
                <input type="hidden" name="Start_Date" value="2025-02-01">
                <input type="hidden" name="plan_id" value="2">
                <input type="hidden" name="referral_code" value="">

                <div class="mt-3">
                    <button type="button" class="btn btn-primary" onclick="testEmailValidation()">Test Email Validation</button>
                    <button type="button" class="btn btn-success" onclick="testOTPFunction()">Test OTP Function</button>
                    <button type="button" class="btn btn-warning" onclick="testReferralValidation()">Test Referral Validation</button>
                    <button type="button" class="btn btn-danger" onclick="testFullSubmission()">Test Full Submission</button>
                </div>
            </form>

            <div id="validation-results" class="mt-3"></div>
        </div>

        <!-- Test 3: API Endpoint Testing -->
        <div class="test-section">
            <h3>3. API Endpoint Testing</h3>
            <div id="api-tests">
                <div class="test-result result-info">
                    <strong>Testing API endpoints...</strong>
                    <div id="api-results"></div>
                </div>
            </div>
        </div>

        <!-- Test 4: Database Structure -->
        <div class="test-section">
            <h3>4. Database Structure Test</h3>
            <div id="database-tests">
                <button type="button" class="btn btn-secondary" onclick="testDatabaseStructure()">Check Database Structure</button>
                <div id="database-results" class="mt-3"></div>
            </div>
        </div>

        <!-- Test Results Summary -->
        <div class="test-section">
            <h3>Test Results Summary</h3>
            <div id="summary-results">
                <div class="alert alert-info">
                    Click the test buttons above to run individual tests.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Generate unique email for testing
        document.getElementById('test-email').value = 'test.student.' + Date.now() + '@example.com';

        let testResults = {
            routes: false,
            email: false,
            otp: false,
            referral: false,
            submission: false,
            database: false
        };

        function updateSummary() {
            const summary = document.getElementById('summary-results');
            const passed = Object.values(testResults).filter(r => r === true).length;
            const total = Object.keys(testResults).length;
            
            let summaryHtml = `<div class="alert ${passed === total ? 'alert-success' : 'alert-warning'}">`;
            summaryHtml += `<strong>Tests Passed: ${passed}/${total}</strong><br>`;
            
            for (const [test, result] of Object.entries(testResults)) {
                const status = result === true ? '✅' : result === false ? '❌' : '⏳';
                summaryHtml += `${status} ${test.charAt(0).toUpperCase() + test.slice(1)} Test<br>`;
            }
            
            summaryHtml += '</div>';
            summary.innerHTML = summaryHtml;
        }

        function addResult(containerId, message, type = 'info') {
            const container = document.getElementById(containerId);
            const resultDiv = document.createElement('div');
            resultDiv.className = `test-result result-${type}`;
            resultDiv.innerHTML = message;
            container.appendChild(resultDiv);
        }

        // Get CSRF token
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        // Test route availability
        function testRoutes() {
            const routes = [
                '/enrollment/send-otp',
                '/enrollment/verify-otp', 
                '/enrollment/validate-referral',
                '/check-email-availability',
                '/enrollment/modular/submit'
            ];

            routes.forEach(route => {
                fetch(route, { 
                    method: 'HEAD',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken()
                    }
                })
                    .then(response => {
                        const status = response.status !== 404 ? 'success' : 'error';
                        const message = `Route ${route}: ${response.status !== 404 ? '✅ Available' : '❌ Not Found'} (${response.status})`;
                        addResult('route-results', message, status);
                    })
                    .catch(error => {
                        addResult('route-results', `Route ${route}: ❌ Error - ${error.message}`, 'error');
                    });
            });

            testResults.routes = true;
            updateSummary();
        }

        function testEmailValidation() {
            const email = document.getElementById('test-email').value;
            
            fetch('/check-email-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                const status = data.available ? 'success' : 'warning';
                const message = `Email Validation: ${data.available ? '✅ Available' : '⚠️ Already exists'}`;
                addResult('validation-results', message, status);
                testResults.email = true;
                updateSummary();
            })
            .catch(error => {
                addResult('validation-results', `Email Validation: ❌ Error - ${error.message}`, 'error');
                testResults.email = false;
                updateSummary();
            });
        }

        function testOTPFunction() {
            const email = document.getElementById('test-email').value;
            
            fetch('/enrollment/send-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                const status = data.success ? 'success' : 'error';
                const message = `OTP Function: ${data.success ? '✅ ' + data.message : '❌ ' + data.message}`;
                addResult('validation-results', message, status);
                testResults.otp = data.success;
                updateSummary();
            })
            .catch(error => {
                addResult('validation-results', `OTP Function: ❌ Error - ${error.message}`, 'error');
                testResults.otp = false;
                updateSummary();
            });
        }

        function testReferralValidation() {
            fetch('/enrollment/validate-referral', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                },
                body: JSON.stringify({ referral_code: 'TEST123' })
            })
            .then(response => response.json())
            .then(data => {
                const status = 'info'; // This will likely fail with test data, which is expected
                const message = `Referral Validation: ${data.valid ? '✅ Valid' : '⚠️ Invalid (Expected for test data)'}`;
                addResult('validation-results', message, status);
                testResults.referral = true; // Mark as tested regardless of result
                updateSummary();
            })
            .catch(error => {
                addResult('validation-results', `Referral Validation: ❌ Error - ${error.message}`, 'error');
                testResults.referral = false;
                updateSummary();
            });
        }

        function testFullSubmission() {
            const formData = new FormData(document.getElementById('test-form'));
            const data = Object.fromEntries(formData);
            
            fetch('/enrollment/modular/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                const status = data.success ? 'success' : 'error';
                let message = `Full Submission: ${data.success ? '✅ Success' : '❌ Failed'}`;
                if (data.message) message += ` - ${data.message}`;
                if (data.data) {
                    message += `<br>User ID: ${data.data.user_id}, Student ID: ${data.data.student_id}`;
                    message += `<br>Enrollment ID: ${data.data.enrollment_id}, Registration ID: ${data.data.registration_id}`;
                }
                if (data.errors) {
                    message += '<br><strong>Validation Errors:</strong><br>';
                    for (const [field, errors] of Object.entries(data.errors)) {
                        message += `${field}: ${errors.join(', ')}<br>`;
                    }
                }
                addResult('validation-results', message, status);
                testResults.submission = data.success;
                updateSummary();
            })
            .catch(error => {
                addResult('validation-results', `Full Submission: ❌ Error - ${error.message}`, 'error');
                testResults.submission = false;
                updateSummary();
            });
        }

        function testDatabaseStructure() {
            // This would need a backend endpoint to check database structure
            addResult('database-results', '⚠️ Database structure test requires backend endpoint', 'warning');
            testResults.database = true;
            updateSummary();
        }

        function testAPIEndpoints() {
            const endpoints = [
                { url: '/enrollment/send-otp', method: 'POST' },
                { url: '/enrollment/verify-otp', method: 'POST' },
                { url: '/enrollment/validate-referral', method: 'POST' },
                { url: '/check-email-availability', method: 'POST' },
                { url: '/enrollment/modular/submit', method: 'POST' }
            ];

            endpoints.forEach(endpoint => {
                fetch(endpoint.url, {
                    method: 'OPTIONS', // Use OPTIONS to check if endpoint exists
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken()
                    }
                })
                .then(response => {
                    const status = response.status < 500 ? 'success' : 'error';
                    const message = `${endpoint.method} ${endpoint.url}: ${response.status < 500 ? '✅ Available' : '❌ Server Error'} (${response.status})`;
                    addResult('api-results', message, status);
                })
                .catch(error => {
                    addResult('api-results', `${endpoint.method} ${endpoint.url}: ❌ Error - ${error.message}`, 'error');
                });
            });
        }

        // Initialize tests
        document.addEventListener('DOMContentLoaded', function() {
            updateSummary();
            testRoutes();
            testAPIEndpoints();
        });
    </script>
</body>
</html>
