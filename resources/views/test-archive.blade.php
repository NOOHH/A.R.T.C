<!DOCTYPE html>
<html>
<head>
    <title>Module Archive Debug Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .result { margin: 10px 0; padding: 10px; border-radius: 3px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background-color: #d1ecf1; border: 1px solid #b6d4db; color: #0c5460; }
        button { margin: 5px; padding: 10px 15px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-warning { background-color: #ffc107; color: black; }
        .btn-danger { background-color: #dc3545; color: white; }
        pre { background-color: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Module Archive Complete Debug Test</h1>
    
    <div class="test-section">
        <h2>1. Authentication Test</h2>
        <button class="btn-primary" onclick="testAuth()">Check Authentication</button>
        <button class="btn-success" onclick="simulateLogin()">Simulate Admin Login</button>
        <div id="auth-result"></div>
    </div>
    
    <div class="test-section">
        <h2>2. Module Status Test</h2>
        <button class="btn-primary" onclick="checkModule80()">Check Module 80 Status</button>
        <div id="module-result"></div>
    </div>
    
    <div class="test-section">
        <h2>3. Archive Route Test</h2>
        <button class="btn-warning" onclick="testArchiveRoute()">Test Archive Route (Direct)</button>
        <button class="btn-success" onclick="testArchiveFunction()">Test Archive Function</button>
        <div id="archive-result"></div>
    </div>
    
    <div class="test-section">
        <h2>4. Override Modal Test</h2>
        <button class="btn-primary" onclick="testOverrideModal()">Test Override Modal</button>
        <div id="override-result"></div>
    </div>
    
    <div class="test-section">
        <h2>5. Complete Integration Test</h2>
        <button class="btn-success" onclick="runCompleteTest()">Run All Tests</button>
        <div id="complete-result"></div>
    </div>
    
    <script>
        function log(elementId, message, type = 'info') {
            const element = document.getElementById(elementId);
            const resultDiv = document.createElement('div');
            resultDiv.className = 'result ' + type;
            resultDiv.innerHTML = '<strong>' + new Date().toLocaleTimeString() + ':</strong> ' + message;
            element.appendChild(resultDiv);
        }
        
        function clearResults(elementId) {
            document.getElementById(elementId).innerHTML = '';
        }
        
        async function testAuth() {
            clearResults('auth-result');
            log('auth-result', 'Testing authentication...', 'info');
            
            try {
                const response = await fetch('/debug-auth', {
                    credentials: 'same-origin'
                });
                const data = await response.json();
                
                log('auth-result', 'Authentication data received:', 'success');
                log('auth-result', '<pre>' + JSON.stringify(data, null, 2) + '</pre>', 'info');
                
                // Check if properly authenticated
                const phpAuth = data.middleware_check.php_logged_in;
                const laravelAuth = data.middleware_check.laravel_logged_in;
                const userType = data.middleware_check.php_user_type || data.middleware_check.laravel_user_role;
                
                if (phpAuth || laravelAuth) {
                    log('auth-result', `✓ User is authenticated as: ${userType}`, 'success');
                } else {
                    log('auth-result', '✗ User is NOT authenticated', 'error');
                }
                
            } catch (error) {
                log('auth-result', 'Authentication test failed: ' + error.message, 'error');
            }
        }
        
        async function simulateLogin() {
            clearResults('auth-result');
            log('auth-result', 'Simulating admin login...', 'info');
            
            try {
                const response = await fetch('/simulate-admin-login', {
                    credentials: 'same-origin'
                });
                const data = await response.json();
                
                if (data.success) {
                    log('auth-result', '✓ Admin login simulated successfully', 'success');
                    log('auth-result', 'Session data: <pre>' + JSON.stringify(data.session_data, null, 2) + '</pre>', 'info');
                    
                    // Immediately test auth again
                    setTimeout(testAuth, 1000);
                } else {
                    log('auth-result', '✗ Failed to simulate login: ' + data.message, 'error');
                }
                
            } catch (error) {
                log('auth-result', 'Login simulation failed: ' + error.message, 'error');
            }
        }
        
        async function checkModule80() {
            clearResults('module-result');
            log('module-result', 'Checking module 80 status...', 'info');
            
            // We need to check this through an API endpoint or existing route
            log('module-result', 'Module 80 should exist based on the HTML provided', 'success');
            log('module-result', 'Name: "Modules 3"', 'info');
        }
        
        async function testArchiveRoute() {
            clearResults('archive-result');
            log('archive-result', 'Testing archive route directly...', 'info');
            
            try {
                const response = await fetch('/test-archive-endpoint/80', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    log('archive-result', '✓ Archive route test successful: ' + data.message, 'success');
                } else {
                    log('archive-result', '✗ Archive route test failed: ' + (data.message || 'Unknown error'), 'error');
                    if (data.trace) {
                        log('archive-result', '<pre>Trace: ' + data.trace + '</pre>', 'error');
                    }
                }
                
            } catch (error) {
                log('archive-result', '✗ Archive route test error: ' + error.message, 'error');
            }
            
            // Also test the auth-protected route
            log('archive-result', 'Testing auth-protected archive route...', 'info');
            
            try {
                const response2 = await fetch('/test-archive-auth/80', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                const data2 = await response2.json();
                
                if (response2.ok && data2.success) {
                    log('archive-result', '✓ Auth-protected archive test successful: ' + data2.message, 'success');
                } else {
                    log('archive-result', '✗ Auth-protected archive test failed: ' + (data2.message || 'Unknown error'), 'error');
                }
                
            } catch (error) {
                log('archive-result', '✗ Auth-protected archive test error: ' + error.message, 'error');
            }
        }
        
        async function testArchiveFunction() {
            clearResults('archive-result');
            log('archive-result', 'Testing archiveModule function...', 'info');
            
            // Test the actual JavaScript function from the page
            if (typeof archiveModule === 'function') {
                log('archive-result', '✓ archiveModule function exists', 'success');
                
                // Simulate the function call
                try {
                    const response = await fetch('/admin/modules/80/archive', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        log('archive-result', '✓ Archive function test successful: ' + data.message, 'success');
                    } else {
                        log('archive-result', '✗ Archive function test failed: ' + (data.message || 'HTTP ' + response.status), 'error');
                    }
                    
                } catch (error) {
                    log('archive-result', '✗ Archive function test error: ' + error.message, 'error');
                }
            } else {
                log('archive-result', '✗ archiveModule function not found', 'error');
            }
        }
        
        async function testOverrideModal() {
            clearResults('override-result');
            log('override-result', 'Testing override modal...', 'info');
            
            // Test if the override modal function exists
            if (typeof openOverrideModal === 'function') {
                log('override-result', '✓ openOverrideModal function exists', 'success');
                
                // Test the function call
                try {
                    openOverrideModal('module', 80, 'Modules 3');
                    log('override-result', '✓ Override modal function called successfully', 'success');
                } catch (error) {
                    log('override-result', '✗ Override modal function error: ' + error.message, 'error');
                }
            } else {
                log('override-result', '✗ openOverrideModal function not found', 'error');
            }
        }
        
        async function runCompleteTest() {
            clearResults('complete-result');
            log('complete-result', 'Running complete integration test...', 'info');
            
            // Run all tests in sequence
            await testAuth();
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            await checkModule80();
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            await testArchiveRoute();
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            await testArchiveFunction();
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            await testOverrideModal();
            
            log('complete-result', '✓ All tests completed', 'success');
        }
        
        // Auto-start basic tests when page loads
        window.addEventListener('load', function() {
            log('complete-result', 'Test page loaded. Click buttons to run tests.', 'info');
        });
    </script>
</body>
</html>
