<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat System Test</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .chat-test-container {
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
        
        .test-button {
            margin: 5px;
        }
        
        .debug-output {
            background: #1a1a1a;
            color: #00ff00;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="chat-test-container">
        <h1 class="mb-4">Chat System Test & Debug</h1>
        
        <!-- Session Info -->
        <div class="test-section">
            <h3>Session Information</h3>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>User ID:</strong> {{ session('user_id', 'Not set') }}</p>
                    <p><strong>User Name:</strong> {{ session('user_name', 'Not set') }}</p>
                    <p><strong>User Role:</strong> {{ session('user_role', 'Not set') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Logged In:</strong> {{ session('logged_in', false) ? 'Yes' : 'No' }}</p>
                    <p><strong>Admin ID:</strong> {{ session('admin_id', 'Not set') }}</p>
                    <p><strong>Admin Logged In:</strong> {{ session('admin_logged_in', false) ? 'Yes' : 'No' }}</p>
                </div>
            </div>
        </div>
        
        <!-- API Tests -->
        <div class="test-section">
            <h3>API Tests</h3>
            <div class="mb-3">
                <button class="btn btn-primary test-button" onclick="testSearchUsers('all')">Test Search All Users</button>
                <button class="btn btn-success test-button" onclick="testSearchUsers('professor')">Test Search Professors</button>
                <button class="btn btn-info test-button" onclick="testSearchUsers('admin')">Test Search Admins</button>
                <button class="btn btn-warning test-button" onclick="testSearchUsers('director')">Test Search Directors</button>
                <button class="btn btn-secondary test-button" onclick="testSearchUsers('student')">Test Search Students</button>
            </div>
            
            <div class="mb-3">
                <button class="btn btn-danger test-button" onclick="testCheckNewMessages()">Test Check New Messages</button>
                <button class="btn btn-dark test-button" onclick="clearDebugOutput()">Clear Debug Output</button>
            </div>
        </div>
        
        <!-- Debug Output -->
        <div class="test-section">
            <h3>Debug Output</h3>
            <div id="debugOutput" class="debug-output">
                Waiting for test results...
            </div>
        </div>
        
        <!-- Chat Component -->
        <div class="test-section">
            <h3>Chat Component</h3>
            <p>Click the chat button below to test the chat interface:</p>
            @include('components.global-chat')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function debugLog(message) {
            const output = document.getElementById('debugOutput');
            const timestamp = new Date().toLocaleTimeString();
            output.textContent += `[${timestamp}] ${message}\n`;
            output.scrollTop = output.scrollHeight;
        }
        
        function clearDebugOutput() {
            document.getElementById('debugOutput').textContent = 'Debug output cleared...\n';
        }
        
        async function testSearchUsers(type) {
            debugLog(`Testing search for user type: ${type}`);
            
            try {
                const response = await fetch(`/api/chat/session/users?${new URLSearchParams({
                    type: type,
                    q: ''
                })}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                debugLog(`Response Status: ${response.status}`);
                debugLog(`Response Data: ${JSON.stringify(data, null, 2)}`);
                
                if (data.success) {
                    debugLog(`Found ${data.total} users of type ${type}`);
                    if (data.debug) {
                        debugLog(`Debug Info: ${JSON.stringify(data.debug, null, 2)}`);
                    }
                } else {
                    debugLog(`Error: ${data.error}`);
                }
                
            } catch (error) {
                debugLog(`Network Error: ${error.message}`);
            }
        }
        
        async function testCheckNewMessages() {
            debugLog('Testing check new messages...');
            
            try {
                const response = await fetch('/api/chat/session/check-new-messages', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                debugLog(`Response Status: ${response.status}`);
                debugLog(`Response Data: ${JSON.stringify(data, null, 2)}`);
                
            } catch (error) {
                debugLog(`Network Error: ${error.message}`);
            }
        }
        
        // Test search with actual search term
        async function testSearchWithTerm(type, searchTerm) {
            debugLog(`Testing search for '${searchTerm}' in ${type} users`);
            
            try {
                const response = await fetch(`/api/chat/session/users?${new URLSearchParams({
                    type: type,
                    q: searchTerm
                })}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                debugLog(`Search Results: ${JSON.stringify(data, null, 2)}`);
                
            } catch (error) {
                debugLog(`Search Error: ${error.message}`);
            }
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            debugLog('Chat Test Page Loaded');
            debugLog('Session info from PHP:');
            debugLog('- User ID: {{ session("user_id", "Not set") }}');
            debugLog('- User Role: {{ session("user_role", "Not set") }}');
            debugLog('- Logged In: {{ session("logged_in", false) ? "Yes" : "No" }}');
        });
    </script>
</body>
</html>
