<?php
// Test admin session and access levels
session_start();

echo "<h2>Admin Access Debugging</h2>";

echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Laravel Session (if available):</h3>";
if (function_exists('session')) {
    echo "Laravel session function exists<br>";
} else {
    echo "Laravel session function not available<br>";
}

echo "<h3>Access Level Checks:</h3>";
$userType = $_SESSION['user_type'] ?? session('user_type') ?? 'not_set';
echo "user_type value: '" . $userType . "'<br>";
echo "user_type type: " . gettype($userType) . "<br>";

echo "<h4>Individual Feature Access:</h4>";
echo "Directors Management: " . (($userType === 'admin') ? '✓ ALLOWED' : '❌ BLOCKED') . "<br>";
echo "Packages Management: " . (($userType === 'admin') ? '✓ ALLOWED' : '❌ BLOCKED') . "<br>";
echo "Settings: " . (($userType === 'admin') ? '✓ ALLOWED' : '❌ BLOCKED') . "<br>";
echo "Analytics: " . (($userType === 'admin') ? '✓ ALLOWED' : '❌ BLOCKED') . "<br>";
echo "Financial Reports: " . (($userType === 'admin') ? '✓ ALLOWED' : '❌ BLOCKED') . "<br>";

echo "<h3>Authentication Check Results:</h3>";
if ($userType === 'admin') {
    echo "✅ <strong>User is properly authenticated as admin</strong><br>";
    echo "All restricted features should be visible<br>";
} else {
    echo "❌ <strong>User is NOT authenticated as admin</strong><br>";
    echo "Current user_type: " . $userType . "<br>";
    echo "Only non-restricted features should be visible<br>";
}

echo "<h3>Quick Tests:</h3>";
echo "<a href='/admin/directors' target='_blank'>Test Directors Page</a><br>";
echo "<a href='/admin/packages' target='_blank'>Test Packages Page</a><br>";
echo "<a href='/admin/settings' target='_blank'>Test Settings Page</a><br>";
echo "<a href='/admin/analytics' target='_blank'>Test Analytics Page</a><br>";

echo "<h3>Debug Session:</h3>";
if (isset($_GET['clear_session'])) {
    session_destroy();
    session_start();
    echo "✓ Session cleared!<br>";
}

if (isset($_GET['set_admin'])) {
    $_SESSION['user_type'] = 'admin';
    $_SESSION['user_role'] = 'admin';
    $_SESSION['admin_id'] = 1;
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Administrator';
    $_SESSION['user_email'] = 'admin@artc.com';
    $_SESSION['logged_in'] = true;
    echo "✓ Admin session set!<br>";
}

echo "<br>";
echo "<a href='?set_admin=1'>Set Admin Session</a> | ";
echo "<a href='?clear_session=1'>Clear Session</a> | ";
echo "<a href='?'>Refresh</a>";

echo "<h3>Expected Behavior:</h3>";
echo "<ul>";
echo "<li>Admins should see ALL menu items including Directors, Packages, Settings, Analytics</li>";
echo "<li>Directors should see limited menu items (no Directors management, no Packages, no Settings, no Analytics)</li>";
echo "</ul>";
?>
