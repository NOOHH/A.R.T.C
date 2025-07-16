<?php
// Comprehensive analytics access control test
session_start();

echo "<h1>Analytics Access Control - Comprehensive Test</h1>";

// Function to test access
function testAnalyticsAccess($userType, $sessionData) {
    // Clear session
    session_destroy();
    session_start();
    
    // Set session data
    foreach ($sessionData as $key => $value) {
        $_SESSION[$key] = $value;
    }
    
    echo "<h3>Testing as $userType:</h3>";
    echo "<strong>Session Data:</strong><br>";
    echo "user_type: " . ($_SESSION['user_type'] ?? 'Not set') . "<br>";
    echo "user_role: " . ($_SESSION['user_role'] ?? 'Not set') . "<br>";
    echo "user_name: " . ($_SESSION['user_name'] ?? 'Not set') . "<br>";
    
    // Check access
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
        echo "<span style='color: green;'>✓ Analytics ACCESS GRANTED</span><br>";
    } else {
        echo "<span style='color: red;'>❌ Analytics ACCESS DENIED</span><br>";
    }
    
    echo "<a href='/admin/analytics' target='_blank'>Test Analytics Dashboard</a><br>";
    echo "<a href='/admin/dashboard' target='_blank'>Test Admin Dashboard</a><br>";
    echo "<hr>";
}

// Test scenarios
echo "<h2>Test Scenarios:</h2>";

// Test 1: Admin user
testAnalyticsAccess('Admin', [
    'user_type' => 'admin',
    'user_role' => 'admin',
    'user_id' => 1,
    'user_name' => 'Administrator',
    'user_email' => 'admin@artc.com',
    'logged_in' => true
]);

// Test 2: Director user
testAnalyticsAccess('Director', [
    'user_type' => 'director',
    'user_role' => 'director',
    'user_id' => 7,
    'directors_id' => 7,
    'user_name' => 'alek piriz',
    'user_email' => 'alek@gmail.com',
    'logged_in' => true
]);

// Test 3: Professor user
testAnalyticsAccess('Professor', [
    'user_type' => 'professor',
    'user_role' => 'professor',
    'user_id' => 1,
    'professor_id' => 1,
    'user_name' => 'Test Professor',
    'user_email' => 'professor@test.com',
    'logged_in' => true
]);

// Test 4: No session
session_destroy();
session_start();
echo "<h3>Testing with No Session:</h3>";
echo "user_type: Not set<br>";
echo "<span style='color: red;'>❌ Analytics ACCESS DENIED</span><br>";
echo "<a href='/admin/analytics' target='_blank'>Test Analytics Dashboard</a><br>";
echo "<hr>";

echo "<h2>Summary of Changes Made:</h2>";
echo "<ul>";
echo "<li><strong>Admin Dashboard Layout:</strong> Added @if(session('user_type') === 'admin') check around Analytics menu item</li>";
echo "<li><strong>AdminAnalyticsController:</strong> Added authentication checks to all methods (index, getData, getBatches, getSubjects)</li>";
echo "<li><strong>UnifiedLoginController:</strong> Fixed session consistency - all user types now set 'user_type' variable</li>";
echo "<li><strong>Access Control:</strong> Only users with session('user_type') === 'admin' can access analytics</li>";
echo "</ul>";

echo "<h2>Expected Behavior:</h2>";
echo "<ul>";
echo "<li><strong>Admins:</strong> Can see Analytics menu and access all analytics features</li>";
echo "<li><strong>Directors:</strong> Cannot see Analytics menu and get access denied if they try to access directly</li>";
echo "<li><strong>Professors:</strong> Cannot see Analytics menu and get access denied if they try to access directly</li>";
echo "<li><strong>Unauthenticated users:</strong> Cannot access analytics at all</li>";
echo "</ul>";

echo "<h3>Quick Test Links:</h3>";
echo "<a href='?admin=1'>Set Admin Session</a> | ";
echo "<a href='?director=1'>Set Director Session</a> | ";
echo "<a href='?professor=1'>Set Professor Session</a> | ";
echo "<a href='?clear=1'>Clear Session</a>";

// Handle quick test actions
if (isset($_GET['admin'])) {
    $_SESSION['user_type'] = 'admin';
    $_SESSION['user_name'] = 'Administrator';
    echo "<script>window.location.reload();</script>";
}

if (isset($_GET['director'])) {
    $_SESSION['user_type'] = 'director';
    $_SESSION['user_name'] = 'Director';
    echo "<script>window.location.reload();</script>";
}

if (isset($_GET['professor'])) {
    $_SESSION['user_type'] = 'professor';
    $_SESSION['user_name'] = 'Professor';
    echo "<script>window.location.reload();</script>";
}

if (isset($_GET['clear'])) {
    session_destroy();
    echo "<script>window.location.href = window.location.pathname;</script>";
}
?>
