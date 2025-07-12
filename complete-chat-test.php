<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <title>Complete Chat System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-section { 
            margin: 20px 0; 
            padding: 20px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
        }
        .result { 
            background: #f8f9fa; 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 5px; 
            border-left: 4px solid #007bff;
        }
        .error { 
            background: #ffebee; 
            color: #c62828; 
            border-left-color: #c62828;
        }
        .success { 
            background: #e8f5e8; 
            color: #2e7d32; 
            border-left-color: #4caf50;
        }
        .test-button {
            margin: 5px;
        }
        pre {
            max-height: 300px;
            overflow-y: auto;
            font-size: 12px;
        }
    </style>
</head>
<body>
<?php
session_start();
// Set admin session for testing
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'Test Admin';
$_SESSION['admin_role'] = 'admin';
?>

<div class="container mt-4">
    <h1>Complete Chat System Test</h1>
    
    <div class="test-section">
        <h2>1. Authentication Status</h2>
        <div class="result">
            <strong>Admin Session:</strong><br>
            Admin logged in: <?php echo isset($_SESSION['admin_logged_in']) ? 'Yes' : 'No'; ?><br>
            Admin ID: <?php echo $_SESSION['admin_id'] ?? 'Not set'; ?><br>
            Admin Name: <?php echo $_SESSION['admin_name'] ?? 'Not set'; ?><br>
        </div>
    </div>
    
    <div class="test-section">
        <h2>2. Professor Search Test</h2>
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-primary test-button" onclick="testProfessorSearch()">Test Professor Search ("robert")</button>
                <button class="btn btn-secondary test-button" onclick="testProfessorSearchAPI()">Test Professor API</button>
            </div>
            <div class="col-md-6">
                <button class="btn btn-info test-button" onclick="testAllUserSearch()">Test All User Search</button>
                <button class="btn btn-warning test-button" onclick="testAdminSearch()">Test Admin Search</button>
            </div>
        </div>
        <div id="professorSearchResult"></div>
    </div>
    
    <div class="test-section">
        <h2>3. Message Sending Test</h2>
        <div class="row">
            <div class="col-md-6">
                <label>Receiver ID:</label>
                <input type="number" id="receiverId" class="form-control" value="1" placeholder="Enter receiver ID">
            </div>
            <div class="col-md-6">
                <label>Message:</label>
                <input type="text" id="messageText" class="form-control" value="Test message from admin" placeholder="Enter message">
            </div>
        </div>
        <button class="btn btn-success mt-2" onclick="sendTestMessage()">Send Test Message</button>
        <div id="messageResult"></div>
    </div>
    
    <div class="test-section">
        <h2>4. Message Retrieval Test</h2>
        <div class="row">
            <div class="col-md-6">
                <label>Chat with User ID:</label>
                <input type="number" id="chatWithId" class="form-control" value="1" placeholder="Enter user ID">
            </div>
        </div>
        <button class="btn btn-info mt-2" onclick="getTestMessages()">Get Messages</button>
        <div id="messagesResult"></div>
    </div>
    
    <div class="test-section">
        <h2>5. Database Debug</h2>
        <button class="btn btn-warning" onclick="debugDatabase()">Debug Database</button>
        <div id="databaseResult"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function testProfessorSearch() {
    fetch('/api/chat/session/search/professors?search=robert')
        .then(response => response.json())
        .then(data => {
            showResult('professorSearchResult', data, 'Professor Search Result');
        })
        .catch(error => {
            showError('professorSearchResult', 'Professor Search Error: ' + error.message);
        });
}

function testProfessorSearchAPI() {
    fetch('/test/chat/search/professors?search=robert')
        .then(response => response.json())
        .then(data => {
            showResult('professorSearchResult', data, 'Professor API Result');
        })
        .catch(error => {
            showError('professorSearchResult', 'Professor API Error: ' + error.message);
        });
}

function testAllUserSearch() {
    fetch('/api/chat/session/users?q=robert')
        .then(response => response.json())
        .then(data => {
            showResult('professorSearchResult', data, 'All User Search Result');
        })
        .catch(error => {
            showError('professorSearchResult', 'All User Search Error: ' + error.message);
        });
}

function testAdminSearch() {
    fetch('/api/chat/session/search/admins?search=admin')
        .then(response => response.json())
        .then(data => {
            showResult('professorSearchResult', data, 'Admin Search Result');
        })
        .catch(error => {
            showError('professorSearchResult', 'Admin Search Error: ' + error.message);
        });
}

function sendTestMessage() {
    const receiverId = document.getElementById('receiverId').value;
    const messageText = document.getElementById('messageText').value;
    
    fetch('/api/chat/session/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({
            receiver_id: receiverId,
            message: messageText
        })
    })
    .then(response => response.json())
    .then(data => {
        showResult('messageResult', data, 'Message Send Result');
    })
    .catch(error => {
        showError('messageResult', 'Message Send Error: ' + error.message);
    });
}

function getTestMessages() {
    const chatWithId = document.getElementById('chatWithId').value;
    
    fetch(`/api/chat/session/messages?with=${chatWithId}`)
        .then(response => response.json())
        .then(data => {
            showResult('messagesResult', data, 'Messages Retrieval Result');
        })
        .catch(error => {
            showError('messagesResult', 'Messages Retrieval Error: ' + error.message);
        });
}

function debugDatabase() {
    fetch('/debug/professors')
        .then(response => response.json())
        .then(data => {
            showResult('databaseResult', data, 'Database Debug Result');
        })
        .catch(error => {
            showError('databaseResult', 'Database Debug Error: ' + error.message);
        });
}

function showResult(elementId, data, title) {
    const element = document.getElementById(elementId);
    element.innerHTML = `
        <div class="result success">
            <h5>${title}</h5>
            <pre>${JSON.stringify(data, null, 2)}</pre>
        </div>
    `;
}

function showError(elementId, message) {
    const element = document.getElementById(elementId);
    element.innerHTML = `
        <div class="result error">
            <h5>Error</h5>
            <p>${message}</p>
        </div>
    `;
}

// Auto-run some tests on page load
document.addEventListener('DOMContentLoaded', function() {
    testProfessorSearch();
    debugDatabase();
});
</script>
</body>
</html>
