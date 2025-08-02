<?php
// Test admin authentication setup

// Start the session
session_start();

// Set admin session variables
$_SESSION['user_id'] = 1;  // Admin user ID
$_SESSION['logged_in'] = true;
$_SESSION['user_type'] = 'admin';
$_SESSION['user_role'] = 'admin';

echo "Admin session set:\n";
echo "User ID: " . $_SESSION['user_id'] . "\n";
echo "Logged in: " . ($_SESSION['logged_in'] ? 'true' : 'false') . "\n";
echo "User type: " . $_SESSION['user_type'] . "\n";
echo "User role: " . $_SESSION['user_role'] . "\n";

// Now test the URL with session
echo "\nTesting URL with admin session...\n";

$url = 'http://127.0.0.1:8000/admin/quiz-generator';
$cookies = session_name() . '=' . session_id();

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookies);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Final URL: $finalUrl\n";
echo "Is redirected to login: " . (strpos($finalUrl, 'login') !== false ? 'YES' : 'NO') . "\n";
?>
