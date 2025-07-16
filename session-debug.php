<?php
// Debug current session for admin
session_start();

echo "<h1>Session Debug for Admin Access Issue</h1>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

echo "<h2>PHP Session (\$_SESSION)</h2>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th style='padding: 8px;'>Key</th><th style='padding: 8px;'>Value</th></tr>";
if (!empty($_SESSION)) {
    foreach ($_SESSION as $key => $value) {
        echo "<tr><td style='padding: 8px;'>" . htmlspecialchars($key) . "</td><td style='padding: 8px;'>" . htmlspecialchars($value) . "</td></tr>";
    }
} else {
    echo "<tr><td colspan='2' style='padding: 8px; text-align: center;'>No PHP session data</td></tr>";
}
echo "</table>";

// Test Laravel session access
echo "<h2>Laravel Session Access Test</h2>";
echo "<p>Testing how Blade templates access session...</p>";

// Simulate Laravel session helper
function session($key = null) {
    if ($key === null) {
        return $_SESSION;
    }
    return $_SESSION[$key] ?? null;
}

$testKeys = ['user_type', 'user_role', 'user_id', 'user_name', 'user_email'];
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th style='padding: 8px;'>Key</th><th style='padding: 8px;'>session(\$key)</th><th style='padding: 8px;'>\$_SESSION[\$key]</th></tr>";

foreach ($testKeys as $key) {
    $laravelValue = session($key);
    $phpValue = $_SESSION[$key] ?? 'NOT_SET';
    echo "<tr>";
    echo "<td style='padding: 8px;'>" . $key . "</td>";
    echo "<td style='padding: 8px;'>" . ($laravelValue ?? 'NULL') . "</td>";
    echo "<td style='padding: 8px;'>" . $phpValue . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Admin Feature Access Test</h2>";
$userType = session('user_type');
$isAdmin = ($userType === 'admin');

echo "<p><strong>Current user_type:</strong> '" . $userType . "'</p>";
echo "<p><strong>Is Admin:</strong> " . ($isAdmin ? 'YES' : 'NO') . "</p>";

echo "<h3>Menu Items That Should Show for Admin:</h3>";
echo "<ul>";
echo "<li>Directors (under Accounts): " . ($isAdmin ? '✅ Should Show' : '❌ Hidden') . "</li>";
echo "<li>Packages (under Programs): " . ($isAdmin ? '✅ Should Show' : '❌ Hidden') . "</li>";
echo "<li>Settings: " . ($isAdmin ? '✅ Should Show' : '❌ Hidden') . "</li>";
echo "<li>Analytics: " . ($isAdmin ? '✅ Should Show' : '❌ Hidden') . "</li>";
echo "<li>Financial Reports: " . ($isAdmin ? '✅ Should Show' : '❌ Hidden') . "</li>";
echo "<li>Referral Reports: " . ($isAdmin ? '✅ Should Show' : '❌ Hidden') . "</li>";
echo "</ul>";

if (!$isAdmin) {
    echo "<div style='background: #ffe6e6; border: 1px solid #ff0000; padding: 10px; margin: 20px 0;'>";
    echo "<h3>🚨 PROBLEM DETECTED</h3>";
    echo "<p>You are not recognized as an admin user. This is why you can't see admin features.</p>";
    echo "<p><strong>Current user_type:</strong> '" . $userType . "'</p>";
    echo "<p><strong>Expected:</strong> 'admin'</p>";
    echo "</div>";
    
    echo "<h3>Quick Fix:</h3>";
    if (isset($_GET['force_admin'])) {
        $_SESSION['user_type'] = 'admin';
        $_SESSION['user_role'] = 'admin';
        echo "<p style='color: green;'>✅ Admin session forced! <a href='?'>Refresh to test</a></p>";
    } else {
        echo "<p><a href='?force_admin=1' style='background: red; color: white; padding: 10px; text-decoration: none;'>Force Admin Session</a></p>";
    }
}

echo "<h2>Test Links</h2>";
echo "<p><a href='/admin/dashboard'>Go to Admin Dashboard</a></p>";
echo "<p><a href='/admin/directors'>Test Directors Page</a></p>";
echo "<p><a href='/admin/packages'>Test Packages Page</a></p>";
echo "<p><a href='/admin/settings'>Test Settings Page</a></p>";
echo "<p><a href='/admin/analytics'>Test Analytics Page</a></p>";

echo "<h2>Actions</h2>";
echo "<p><a href='?' style='background: blue; color: white; padding: 8px; text-decoration: none;'>Refresh Page</a></p>";
if (isset($_GET['clear'])) {
    session_destroy();
    echo "<p style='color: red;'>Session cleared! Please log in again.</p>";
} else {
    echo "<p><a href='?clear=1' style='background: red; color: white; padding: 8px; text-decoration: none;'>Clear Session</a></p>";
}
?>
