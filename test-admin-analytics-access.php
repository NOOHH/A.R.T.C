<?php
// Test admin analytics access control
session_start();

echo "<h2>Admin Analytics Access Test</h2>";

echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Session Variables Check:</h3>";
echo "user_type: " . ($_SESSION['user_type'] ?? 'Not set') . "<br>";
echo "user_role: " . ($_SESSION['user_role'] ?? 'Not set') . "<br>";
echo "admin_id: " . ($_SESSION['admin_id'] ?? 'Not set') . "<br>";
echo "user_id: " . ($_SESSION['user_id'] ?? 'Not set') . "<br>";

echo "<h3>Authentication Check:</h3>";
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
    echo "✓ User is authenticated as admin - Analytics should be accessible<br>";
} else {
    echo "❌ User is NOT authenticated as admin - Analytics should be blocked<br>";
}

echo "<h3>Test Links:</h3>";
echo "<a href='/admin/analytics' target='_blank'>Try to access Analytics Dashboard</a><br>";
echo "<a href='/admin/dashboard' target='_blank'>Go to Admin Dashboard</a><br>";

echo "<h3>Simulate Admin Login:</h3>";
if (isset($_GET['simulate_admin'])) {
    $_SESSION['user_type'] = 'admin';
    $_SESSION['user_role'] = 'admin';
    $_SESSION['admin_id'] = 1;
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Administrator';
    $_SESSION['user_email'] = 'admin@artc.com';
    echo "✓ Admin session simulated! <a href='?'>Refresh to see changes</a><br>";
}

if (isset($_GET['simulate_director'])) {
    $_SESSION['user_type'] = 'director';
    $_SESSION['user_role'] = 'director';
    $_SESSION['directors_id'] = 7;
    $_SESSION['user_id'] = 7;
    $_SESSION['user_name'] = 'alek piriz';
    $_SESSION['user_email'] = 'alek@gmail.com';
    echo "✓ Director session simulated! <a href='?'>Refresh to see changes</a><br>";
}

if (isset($_GET['logout'])) {
    session_destroy();
    session_start();
    echo "✓ Session cleared! <a href='?'>Refresh to see changes</a><br>";
}

echo "<br>";
echo "<a href='?simulate_admin=1'>Simulate Admin Login</a> | ";
echo "<a href='?simulate_director=1'>Simulate Director Login</a> | ";
echo "<a href='?logout=1'>Logout</a>";
?>
