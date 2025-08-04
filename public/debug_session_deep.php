<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Session Debugging</h1>";

// Start our session like the application does
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up test session for user ID 15
$_SESSION['user_id'] = 15;
$_SESSION['user_name'] = 'Vince Michael Dela Vega';
$_SESSION['user_firstname'] = 'Vince Michael';
$_SESSION['user_lastname'] = 'Dela Vega'; 
$_SESSION['user_email'] = '123@gmail.com';
$_SESSION['user_type'] = 'student';
$_SESSION['user_role'] = 'student';
$_SESSION['role'] = 'student';
$_SESSION['logged_in'] = true;

echo "<h2>📋 Current Session State</h2>";
echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 8px;'>";
print_r($_SESSION);
echo "</pre>";

// Test the SessionManager
require_once '../app/Helpers/SessionManager.php';

echo "<h2>🛠️ SessionManager Tests</h2>";

echo "<p><strong>SessionManager::isLoggedIn():</strong> " . (App\Helpers\SessionManager::isLoggedIn() ? 'TRUE ✅' : 'FALSE ❌') . "</p>";
echo "<p><strong>SessionManager::getUserType():</strong> " . (App\Helpers\SessionManager::getUserType() ?? 'NULL') . "</p>";
echo "<p><strong>SessionManager::get('user_id'):</strong> " . (App\Helpers\SessionManager::get('user_id') ?? 'NULL') . "</p>";
echo "<p><strong>SessionManager::get('user_name'):</strong> " . (App\Helpers\SessionManager::get('user_name') ?? 'NULL') . "</p>";

echo "<h2>🔗 Session Info</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . session_status() . "</p>";
echo "<p><strong>Session Save Path:</strong> " . session_save_path() . "</p>";
echo "<p><strong>Session Name:</strong> " . session_name() . "</p>";

// Test direct route access with a simple curl request
echo "<h2>🌐 Test Laravel Route Access</h2>";

// Save session and get ID
session_write_close();
$sessionId = session_id();

echo "<p><strong>Session saved, ID:</strong> $sessionId</p>";

// Create a simple test request to Laravel
$url = 'http://localhost/A.R.T.C/public/student/dashboard';

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'Cookie: PHPSESSID=' . $sessionId,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ],
        'timeout' => 10
    ]
]);

echo "<p>Trying to access: <code>$url</code></p>";

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "<p>❌ Failed to access Laravel route</p>";
    $error = error_get_last();
    if ($error) {
        echo "<p>Error: " . $error['message'] . "</p>";
    }
} else {
    echo "<p>✅ Got response from Laravel</p>";
    
    // Check what we got back
    if (strpos($response, 'dashboard') !== false) {
        echo "<p>✅ Response contains 'dashboard' - likely successful</p>";
    }
    
    if (strpos($response, 'login') !== false || strpos($response, 'Login') !== false) {
        echo "<p>❌ Response contains 'login' - authentication failed</p>";
    }
    
    echo "<h3>📄 Response Headers</h3>";
    if (isset($http_response_header)) {
        echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 8px;'>";
        print_r($http_response_header);
        echo "</pre>";
    }
    
    echo "<h3>📄 Response Preview (first 300 chars)</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace; white-space: pre-wrap;'>";
    echo htmlspecialchars(substr($response, 0, 300));
    echo "</div>";
}

echo "<br><hr>";
echo "<h2>🔗 Quick Links</h2>";
echo "<a href='/A.R.T.C/public/student/dashboard' target='_blank' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Dashboard</a>";
echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Quiz Route</a>";
echo "<a href='set_test_session.php' style='background: #6f42c1; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Setup Session</a>";
?>
