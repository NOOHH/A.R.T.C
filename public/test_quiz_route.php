<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Test Quiz Route Access</h1>";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up test session like the current user
$_SESSION['user_id'] = 15;
$_SESSION['user_name'] = 'Vince Michael Dela Vega';
$_SESSION['user_firstname'] = 'Vince Michael';
$_SESSION['user_lastname'] = 'Dela Vega'; 
$_SESSION['user_email'] = '123@gmail.com';
$_SESSION['user_type'] = 'student';
$_SESSION['user_role'] = 'student';
$_SESSION['role'] = 'student';
$_SESSION['logged_in'] = true;

echo "<p>✅ Session set for user ID 15</p>";

// Try to make a request to the Laravel quiz route
$url = 'http://localhost/A.R.T.C/public/student/quiz/take/3';

// Get current session cookie
session_write_close();
$sessionId = session_id();

// Use curl to make the request with session cookie
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_COOKIE => "PHPSESSID=" . $sessionId,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    CURLOPT_HTTPHEADER => [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: gzip, deflate',
        'Connection: keep-alive',
        'Upgrade-Insecure-Requests: 1',
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($curl, CURLINFO_REDIRECT_URL);

if ($response === false) {
    echo "<p>❌ Curl error: " . curl_error($curl) . "</p>";
} else {
    echo "<p>📊 HTTP Status: $httpCode</p>";
    
    if ($redirectUrl) {
        echo "<p>🔄 Redirected to: $redirectUrl</p>";
    }
    
    // Check if response contains quiz content or dashboard redirect
    if (strpos($response, 'dashboard') !== false) {
        echo "<p>❌ Response contains 'dashboard' - likely redirected</p>";
    }
    
    if (strpos($response, 'quiz') !== false) {
        echo "<p>✅ Response contains 'quiz' - likely working</p>";
    }
    
    if (strpos($response, 'Login') !== false || strpos($response, 'login') !== false) {
        echo "<p>❌ Response contains 'login' - authentication failed</p>";
    }
    
    // Show first 500 characters of response
    echo "<h3>📄 Response Preview (first 500 chars):</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace; white-space: pre-wrap;'>";
    echo htmlspecialchars(substr($response, 0, 500));
    echo "</div>";
}

curl_close($curl);

echo "<br><hr>";
echo "<h2>🔗 Direct Links</h2>";
echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Try Quiz Route</a>";
echo "<a href='/A.R.T.C/public/student/dashboard' target='_blank' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Dashboard</a>";
echo "<a href='debug_quiz_session.php' style='background: #6f42c1; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Debug Session</a>";
?>
