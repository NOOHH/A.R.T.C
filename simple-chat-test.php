<?php
// Simple test to verify professor search works
session_start();

// Set fake admin session for testing
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'Test Admin';
$_SESSION['admin_role'] = 'admin';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Chat Search Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .result { background: #f5f5f5; padding: 10px; margin: 10px 0; }
        .error { background: #ffebee; color: #c62828; }
        .success { background: #e8f5e8; color: #2e7d32; }
    </style>
</head>
<body>
    <h1>Simple Chat Search Test</h1>
    
    <div class="test-section">
        <h2>Session Status</h2>
        <div class="result">
            <?php
            echo "Admin logged in: " . (isset($_SESSION['admin_logged_in']) ? 'Yes' : 'No') . "<br>";
            echo "Admin ID: " . ($_SESSION['admin_id'] ?? 'Not set') . "<br>";
            echo "Admin Name: " . ($_SESSION['admin_name'] ?? 'Not set') . "<br>";
            ?>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Test API Endpoints</h2>
        <button onclick="testProfessors()">Test Professor Search</button>
        <button onclick="testUsers()">Test User Search</button>
        <button onclick="testAdmins()">Test Admin Search</button>
        <div id="apiResults"></div>
    </div>
    
    <script>
        function testProfessors() {
            fetch('/api/chat/session/search/professors?search=robert')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('apiResults').innerHTML = 
                        '<div class="result"><h3>Professor Search Results:</h3><pre>' + 
                        JSON.stringify(data, null, 2) + '</pre></div>';
                })
                .catch(error => {
                    document.getElementById('apiResults').innerHTML = 
                        '<div class="result error"><h3>Professor Search Error:</h3>' + 
                        error.message + '</div>';
                });
        }
        
        function testUsers() {
            fetch('/api/chat/session/search/users?search=test')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('apiResults').innerHTML = 
                        '<div class="result"><h3>User Search Results:</h3><pre>' + 
                        JSON.stringify(data, null, 2) + '</pre></div>';
                })
                .catch(error => {
                    document.getElementById('apiResults').innerHTML = 
                        '<div class="result error"><h3>User Search Error:</h3>' + 
                        error.message + '</div>';
                });
        }
        
        function testAdmins() {
            fetch('/api/chat/session/search/admins?search=admin')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('apiResults').innerHTML = 
                        '<div class="result"><h3>Admin Search Results:</h3><pre>' + 
                        JSON.stringify(data, null, 2) + '</pre></div>';
                })
                .catch(error => {
                    document.getElementById('apiResults').innerHTML = 
                        '<div class="result error"><h3>Admin Search Error:</h3>' + 
                        error.message + '</div>';
                });
        }
        
        // Auto-test on page load
        document.addEventListener('DOMContentLoaded', function() {
            testProfessors();
        });
    </script>
</body>
</html>
