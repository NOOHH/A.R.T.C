<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat API Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Chat API Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Session Info</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>User ID:</strong> {{ session('user_id', 'Not set') }}</p>
                        <p><strong>User Name:</strong> {{ session('user_name', 'Not set') }}</p>
                        <p><strong>User Role:</strong> {{ session('user_role', 'Not set') }}</p>
                        <p><strong>Logged In:</strong> {{ session('logged_in', 'false') ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Test API Endpoints</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary mb-2" onclick="testGetProfessors()">Get Professors</button>
                        <button class="btn btn-success mb-2" onclick="testGetStudents()">Get Students</button>
                        <button class="btn btn-info mb-2" onclick="testGetMessages()">Get Messages</button>
                        <button class="btn btn-warning mb-2" onclick="testSendMessage()">Send Test Message</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Test Results</h5>
                    </div>
                    <div class="card-body">
                        <pre id="testResults" style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 4px;">
Ready to test...
                        </pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const resultsDiv = document.getElementById('testResults');
        
        function logResult(message) {
            resultsDiv.textContent += new Date().toLocaleTimeString() + ': ' + message + '\n';
            resultsDiv.scrollTop = resultsDiv.scrollHeight;
        }
        
        function testGetProfessors() {
            logResult('Testing get professors...');
            fetch('/api/chat/session/users?type=professor', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'include'
            })
            .then(response => {
                logResult('Status: ' + response.status);
                return response.json();
            })
            .then(data => {
                logResult('Professors response: ' + JSON.stringify(data, null, 2));
            })
            .catch(error => {
                logResult('Error: ' + error.message);
            });
        }
        
        function testGetStudents() {
            logResult('Testing get students...');
            fetch('/api/chat/session/users?type=student', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'include'
            })
            .then(response => {
                logResult('Status: ' + response.status);
                return response.json();
            })
            .then(data => {
                logResult('Students response: ' + JSON.stringify(data, null, 2));
            })
            .catch(error => {
                logResult('Error: ' + error.message);
            });
        }
        
        function testGetMessages() {
            logResult('Testing get messages with user ID 111...');
            fetch('/api/chat/session/messages?with=111', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'include'
            })
            .then(response => {
                logResult('Status: ' + response.status);
                return response.json();
            })
            .then(data => {
                logResult('Messages response: ' + JSON.stringify(data, null, 2));
            })
            .catch(error => {
                logResult('Error: ' + error.message);
            });
        }
        
        function testSendMessage() {
            logResult('Testing send message to user ID 111...');
            fetch('/api/chat/session/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'include',
                body: JSON.stringify({
                    receiver_id: 111,
                    message: 'Test message from debug page'
                })
            })
            .then(response => {
                logResult('Status: ' + response.status);
                return response.json();
            })
            .then(data => {
                logResult('Send message response: ' + JSON.stringify(data, null, 2));
            })
            .catch(error => {
                logResult('Error: ' + error.message);
            });
        }
    </script>
</body>
</html>
