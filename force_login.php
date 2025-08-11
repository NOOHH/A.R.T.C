<?php
/**
 * ULTRA SIMPLE LOGIN BYPASS - NO LARAVEL DEPENDENCIES
 * This will work even if Laravel is having issues
 */

// Start session first
session_start();

echo "<h1>🚨 ULTRA SIMPLE LOGIN BYPASS</h1>";

// If this is a POST request, force login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['force_login'])) {
    
    echo "<h2>🔧 FORCING LOGIN SESSION...</h2>";
    
    // Force create session variables for student login
    $_SESSION['user_id'] = 'emergency_user_123';
    $_SESSION['user_name'] = 'Emergency User';
    $_SESSION['user_email'] = $_POST['email'] ?? 'emergency@example.com';
    $_SESSION['user_role'] = 'student';
    $_SESSION['role'] = 'student';
    $_SESSION['logged_in'] = true;
    $_SESSION['user_firstname'] = 'Emergency';
    $_SESSION['user_lastname'] = 'User';
    
    echo "<p>✅ Session variables created successfully!</p>";
    echo "<p>Session ID: " . session_id() . "</p>";
    echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<p>Role: " . $_SESSION['role'] . "</p>";
    
    echo "<h3>🔄 Attempting Redirect to Dashboard...</h3>";
    echo "<script>
        console.log('Redirecting to dashboard...');
        setTimeout(function() {
            window.location.href = '/student/dashboard';
        }, 2000);
    </script>";
    
    echo "<p><strong>You should be redirected automatically.</strong></p>";
    echo "<p>If not, <a href='/student/dashboard'>click here to go to dashboard</a></p>";
    
    echo "<h3>⚠️ IMPORTANT AFTER LOGIN:</h3>";
    echo "<ul>";
    echo "<li>This is a temporary bypass</li>";
    echo "<li>You'll need to fix the real login system</li>";
    echo "<li>Check your database for real user credentials</li>";
    echo "</ul>";
    
    exit;
}

// Show simple form
echo "<h2>🚨 Emergency Login Bypass</h2>";
echo "<p><strong>This will force create a session and log you in as a student</strong></p>";

echo "<form method='POST' style='border: 2px solid #dc3545; padding: 20px; max-width: 400px; background: #fff3cd;'>";
echo "    <h3>⚠️ Force Login (Bypass Everything)</h3>";
echo "    <p>Enter any email (for session purposes):</p>";
echo "    <input type='email' name='email' placeholder='your@email.com' style='width: 100%; padding: 10px; margin: 10px 0;'>";
echo "    <br>";
echo "    <button type='submit' name='force_login' value='1' style='background: #dc3545; color: white; padding: 15px 30px; border: none; font-weight: bold; font-size: 16px;'>";
echo "        🚨 FORCE LOGIN NOW";
echo "    </button>";
echo "</form>";

echo "<h3>📋 What this does:</h3>";
echo "<ul>";
echo "<li>✅ Creates session variables manually (no Laravel needed)</li>";
echo "<li>✅ Sets you up as a logged-in student</li>";
echo "<li>✅ Redirects to student dashboard</li>";
echo "<li>✅ Bypasses ALL validation and authentication</li>";
echo "</ul>";

echo "<h3>🔗 Alternative Options:</h3>";
echo "<ul>";
echo "<li><a href='/check_users.php'>👥 Check what users exist in database</a></li>";
echo "<li><a href='/emergency_login_fix.php'>🔧 Advanced login diagnostic</a></li>";
echo "<li><a href='/test-simple'>🧪 Test if Laravel is working</a></li>";
echo "</ul>";

echo "<h3>🆘 Current Session Info:</h3>";
echo "<pre>";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE') . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Current Session Data: " . print_r($_SESSION, true) . "\n";
echo "</pre>";

echo "<p><strong style='color: red;'>⚠️ This is for emergency access only!</strong></p>";
echo "<p>After you're logged in, you should fix the real login system.</p>";
?>
