<?php
// Admin Access Verification Test
session_start();

echo "<h1>Admin Access Verification</h1>";

// Check current authentication
echo "<h2>Current Authentication Status</h2>";
$userType = $_SESSION['user_type'] ?? null;
$userRole = $_SESSION['user_role'] ?? null;
$userId = $_SESSION['user_id'] ?? null;
$userName = $_SESSION['user_name'] ?? null;

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th style='padding: 8px;'>Session Variable</th><th style='padding: 8px;'>Value</th><th style='padding: 8px;'>Status</th></tr>";
echo "<tr><td style='padding: 8px;'>user_type</td><td style='padding: 8px;'>'" . $userType . "'</td><td style='padding: 8px;'>" . ($userType === 'admin' ? '✅ ADMIN' : '❌ NOT ADMIN') . "</td></tr>";
echo "<tr><td style='padding: 8px;'>user_role</td><td style='padding: 8px;'>'" . $userRole . "'</td><td style='padding: 8px;'>Info only</td></tr>";
echo "<tr><td style='padding: 8px;'>user_id</td><td style='padding: 8px;'>'" . $userId . "'</td><td style='padding: 8px;'>Info only</td></tr>";
echo "<tr><td style='padding: 8px;'>user_name</td><td style='padding: 8px;'>'" . $userName . "'</td><td style='padding: 8px;'>Info only</td></tr>";
echo "</table>";

// Feature access test
echo "<h2>Feature Access Test</h2>";
$isAdmin = ($userType === 'admin');

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th style='padding: 8px;'>Feature</th><th style='padding: 8px;'>Should Show for Admin</th><th style='padding: 8px;'>Current Status</th><th style='padding: 8px;'>Test Link</th></tr>";

$features = [
    ['Directors Management', 'YES', ($isAdmin ? '✅ VISIBLE' : '❌ HIDDEN'), '<a href="/admin/directors" target="_blank">Test</a>'],
    ['Packages Management', 'YES', ($isAdmin ? '✅ VISIBLE' : '❌ HIDDEN'), '<a href="/admin/packages" target="_blank">Test</a>'],
    ['Settings', 'YES', ($isAdmin ? '✅ VISIBLE' : '❌ HIDDEN'), '<a href="/admin/settings" target="_blank">Test</a>'],
    ['Analytics', 'YES', ($isAdmin ? '✅ VISIBLE' : '❌ HIDDEN'), '<a href="/admin/analytics" target="_blank">Test</a>'],
    ['Financial Reports', 'YES', ($isAdmin ? '✅ VISIBLE' : '❌ HIDDEN'), 'N/A'],
    ['Referral Reports', 'YES', ($isAdmin ? '✅ VISIBLE' : '❌ HIDDEN'), 'N/A']
];

foreach ($features as $feature) {
    echo "<tr>";
    foreach ($feature as $cell) {
        echo "<td style='padding: 8px;'>" . $cell . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Quick actions
echo "<h2>Quick Actions</h2>";
if (isset($_GET['set_admin'])) {
    // Set proper admin session
    $_SESSION['user_type'] = 'admin';
    $_SESSION['user_role'] = 'admin';
    $_SESSION['admin_id'] = 1;
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Administrator';
    $_SESSION['user_email'] = 'admin@artc.com';
    $_SESSION['logged_in'] = true;
    echo "<div style='color: green; font-weight: bold; margin: 10px 0;'>✅ Admin session set! Refresh page to see changes.</div>";
}

if (isset($_GET['set_director'])) {
    // Set director session
    $_SESSION['user_type'] = 'director';
    $_SESSION['user_role'] = 'director';
    $_SESSION['directors_id'] = 7;
    $_SESSION['user_id'] = 7;
    $_SESSION['user_name'] = 'alek piriz';
    $_SESSION['user_email'] = 'alek@gmail.com';
    $_SESSION['logged_in'] = true;
    echo "<div style='color: blue; font-weight: bold; margin: 10px 0;'>✅ Director session set! Refresh page to see changes.</div>";
}

if (isset($_GET['clear'])) {
    session_destroy();
    session_start();
    echo "<div style='color: red; font-weight: bold; margin: 10px 0;'>✅ Session cleared! Refresh page to see changes.</div>";
}

echo "<div style='margin: 20px 0;'>";
echo "<a href='?set_admin=1' style='background: green; color: white; padding: 10px; text-decoration: none; margin: 5px;'>Set Admin Session</a> ";
echo "<a href='?set_director=1' style='background: blue; color: white; padding: 10px; text-decoration: none; margin: 5px;'>Set Director Session</a> ";
echo "<a href='?clear=1' style='background: red; color: white; padding: 10px; text-decoration: none; margin: 5px;'>Clear Session</a> ";
echo "<a href='?' style='background: gray; color: white; padding: 10px; text-decoration: none; margin: 5px;'>Refresh</a>";
echo "</div>";

// Debug information
echo "<h2>Debug Information</h2>";
echo "<h3>Expected Behavior:</h3>";
echo "<ul>";
echo "<li><strong>Admins</strong>: Should see ALL features (Directors, Packages, Settings, Analytics, Financial Reports, Referral Reports)</li>";
echo "<li><strong>Directors</strong>: Should see LIMITED features (no Directors management, no Packages, no Settings, no Analytics, no Financial/Referral Reports)</li>";
echo "</ul>";

echo "<h3>How to Fix If Not Working:</h3>";
echo "<ol>";
echo "<li>Make sure you're logging in through the proper admin login</li>";
echo "<li>Check that the login controller sets session('user_type') = 'admin'</li>";
echo "<li>Verify there are no typos in the blade template conditions</li>";
echo "<li>Clear browser cache and cookies</li>";
echo "</ol>";

echo "<h3>Full Session Data:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
print_r($_SESSION);
echo "</pre>";
?>
