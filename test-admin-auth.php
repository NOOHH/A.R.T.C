<?php
// Test admin authentication and session data
session_start();

echo "<h2>Admin Authentication Test</h2>";

// Check if admin is logged in via PHP session
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    echo "<h3>✓ Admin is logged in via PHP session</h3>";
    echo "<pre>";
    echo "Admin ID: " . ($_SESSION['admin_id'] ?? 'Not set') . "\n";
    echo "Admin Name: " . ($_SESSION['admin_name'] ?? 'Not set') . "\n";
    echo "Admin Role: " . ($_SESSION['admin_role'] ?? 'Not set') . "\n";
    echo "Full Session Data:\n";
    print_r($_SESSION);
    echo "</pre>";
} else {
    echo "<h3>❌ Admin is not logged in via PHP session</h3>";
    echo "<pre>";
    echo "Session data:\n";
    print_r($_SESSION);
    echo "</pre>";
}

// Test the actual API endpoint
echo "<h3>Testing Chat API Authentication:</h3>";
?>
<script>
// Test the API endpoint
fetch('/api/chat/session/search/professors?search=robert')
    .then(response => response.json())
    .then(data => {
        console.log('API Response:', data);
        document.getElementById('api-result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('api-result').innerHTML = '<pre>Error: ' + error.message + '</pre>';
    });
</script>
<div id="api-result">Loading API test...</div>

<h3>Manual Test Links:</h3>
<ul>
    <li><a href="/api/chat/session/search/professors?search=robert" target="_blank">Search for 'robert' in professors</a></li>
    <li><a href="/api/chat/session/search/users?search=test" target="_blank">Search for 'test' in users</a></li>
    <li><a href="/api/chat/session/search/admins?search=admin" target="_blank">Search for 'admin' in admins</a></li>
</ul>
