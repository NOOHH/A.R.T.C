<?php
// Admin Session Fix and Test
session_start();

echo "<h1>Admin Session Fix</h1>";

// Clear existing session and set proper admin session
if (isset($_GET['fix_admin_session'])) {
    // Clear all session data
    session_destroy();
    session_start();
    
    // Set proper admin session variables
    $_SESSION['user_id'] = 1;
    $_SESSION['user_type'] = 'admin';
    $_SESSION['user_name'] = 'Administrator';
    $_SESSION['user_email'] = 'admin@artc.com';
    $_SESSION['user_role'] = 'admin';
    $_SESSION['admin_id'] = 1;
    $_SESSION['logged_in'] = true;
    
    echo "<div style='background: green; color: white; padding: 15px; margin: 10px 0;'>";
    echo "<h3>✅ Admin Session Fixed!</h3>";
    echo "<p>Session has been cleared and proper admin credentials set.</p>";
    echo "<p><a href='/admin/dashboard' style='color: white; text-decoration: underline;'>Go to Admin Dashboard</a></p>";
    echo "</div>";
}

echo "<h2>Current Session Status</h2>";
echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th style='padding: 8px;'>Variable</th><th style='padding: 8px;'>Value</th><th style='padding: 8px;'>Status</th></tr>";

$sessionVars = [
    'user_type' => $_SESSION['user_type'] ?? 'NOT_SET',
    'user_role' => $_SESSION['user_role'] ?? 'NOT_SET', 
    'user_id' => $_SESSION['user_id'] ?? 'NOT_SET',
    'admin_id' => $_SESSION['admin_id'] ?? 'NOT_SET',
    'user_name' => $_SESSION['user_name'] ?? 'NOT_SET',
    'user_email' => $_SESSION['user_email'] ?? 'NOT_SET',
    'logged_in' => $_SESSION['logged_in'] ?? 'NOT_SET'
];

foreach ($sessionVars as $key => $value) {
    $status = '';
    if ($key === 'user_type') {
        $status = ($value === 'admin') ? '✅ CORRECT' : '❌ WRONG';
    } elseif ($key === 'logged_in') {
        $status = ($value === true || $value === '1') ? '✅ LOGGED IN' : '❌ NOT LOGGED IN';
    } else {
        $status = ($value !== 'NOT_SET') ? '✅ SET' : '❌ NOT SET';
    }
    
    echo "<tr>";
    echo "<td style='padding: 8px;'><strong>{$key}</strong></td>";
    echo "<td style='padding: 8px;'>{$value}</td>";
    echo "<td style='padding: 8px;'>{$status}</td>";
    echo "</tr>";
}
echo "</table>";

$userType = $_SESSION['user_type'] ?? 'none';
$isAdmin = ($userType === 'admin');

echo "<h2>Admin Feature Access</h2>";
if ($isAdmin) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 10px 0;'>";
    echo "<h3>✅ Admin Access Verified</h3>";
    echo "<p>You should now see ALL admin features including:</p>";
    echo "<ul>";
    echo "<li>Directors (under Accounts menu)</li>";
    echo "<li>Packages (under Programs menu)</li>";
    echo "<li>Settings menu</li>";
    echo "<li>Analytics menu</li>";
    echo "<li>Financial Reports</li>";
    echo "<li>Referral Reports</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0;'>";
    echo "<h3>❌ Not Recognized as Admin</h3>";
    echo "<p>Current user_type: <strong>{$userType}</strong></p>";
    echo "<p>You need to fix the admin session.</p>";
    echo "</div>";
}

echo "<h2>Actions</h2>";
if (!$isAdmin) {
    echo "<a href='?fix_admin_session=1' style='background: #dc3545; color: white; padding: 15px 25px; text-decoration: none; font-weight: bold; margin: 10px;'>🔧 Fix Admin Session</a><br><br>";
}

echo "<a href='/admin/dashboard' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; margin: 5px;'>Go to Dashboard</a> ";
echo "<a href='/logout' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; margin: 5px;'>Logout</a> ";
echo "<a href='?' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; margin: 5px;'>Refresh</a>";

echo "<h2>Instructions</h2>";
echo "<ol>";
echo "<li>Click 'Fix Admin Session' if you're not showing as admin</li>";
echo "<li>Go to Admin Dashboard</li>"; 
echo "<li>Verify you can see Directors, Packages, Settings, and Analytics menus</li>";
echo "<li>If still not working, clear browser cache and cookies</li>";
echo "</ol>";

echo "<h2>Debug Info</h2>";
echo "<details>";
echo "<summary>Full Session Data</summary>";
echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6;'>";
print_r($_SESSION);
echo "</pre>";
echo "</details>";
?>
