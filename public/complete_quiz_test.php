<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔄 Complete Quiz Flow Test</h1>";

// Step 1: Set up the session through Laravel
echo "<h2>Step 1: Setting up Laravel Session</h2>";

// Try to initialize session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set both PHP and Laravel session data
$_SESSION['user_id'] = 15;
$_SESSION['user_name'] = 'Vince Michael Dela Vega';
$_SESSION['user_firstname'] = 'Vince Michael';
$_SESSION['user_lastname'] = 'Dela Vega';
$_SESSION['user_email'] = '123@gmail.com';
$_SESSION['user_type'] = 'student';
$_SESSION['user_role'] = 'student';
$_SESSION['role'] = 'student';
$_SESSION['logged_in'] = true;

echo "<p>✅ PHP Session set for user ID 15</p>";

// Step 2: Test access to a working Laravel route first
echo "<h2>Step 2: Test Dashboard Access</h2>";
echo "<p>Let's first verify the user can access the dashboard...</p>";
echo "<iframe src='/A.R.T.C/public/student/dashboard' width='100%' height='200px' style='border: 2px solid #ddd; border-radius: 8px;'></iframe>";

echo "<h2>Step 3: Direct Quiz Route Test</h2>";
echo "<p>Now let's try the quiz route...</p>";
echo "<iframe src='/A.R.T.C/public/student/quiz/take/3' width='100%' height='400px' style='border: 2px solid #ddd; border-radius: 8px;'></iframe>";

echo "<h2>Step 4: Manual Links</h2>";
echo "<p>If the iframes don't work properly, try these direct links:</p>";
echo "<a href='/A.R.T.C/public/student/dashboard' target='_blank' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Dashboard (should work)</a>";
echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Quiz Route (test this)</a>";
echo "<a href='direct_quiz_test.php' style='background: #6f42c1; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Direct Quiz Test</a>";

echo "<h2>Step 5: Debug Information</h2>";
echo "<p><strong>Current Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Data:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 8px;'>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Step 6: Expected Results</h2>";
echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>🎯 What Should Happen:</h3>";
echo "<ul>";
echo "<li>✅ Dashboard iframe should load successfully (proving authentication works)</li>";
echo "<li>✅ Quiz iframe should show the quiz taking interface with question 'wwwww'</li>";
echo "<li>✅ No login page redirects</li>";
echo "<li>✅ No 'access denied' errors</li>";
echo "</ul>";
echo "</div>";

echo "<h2>Step 7: Troubleshooting</h2>";
echo "<p>If the quiz is still not working:</p>";
echo "<ul>";
echo "<li>Check if the quiz attempt #3 still belongs to student 2025-08-00003</li>";
echo "<li>Verify the SessionManager is reading Laravel session correctly</li>";
echo "<li>Check if there are any middleware issues</li>";
echo "<li>Look at Laravel logs for specific error messages</li>";
echo "</ul>";
?>
