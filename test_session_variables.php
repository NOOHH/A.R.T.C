<?php
// Test session variables
session_start();

echo "<h2>Session Variable Test</h2>";

// Check $_SESSION
echo "<h3>PHP \$_SESSION variables:</h3>";
echo "user_id: " . ($_SESSION['user_id'] ?? 'Not set') . "<br>";
echo "user_name: " . ($_SESSION['user_name'] ?? 'Not set') . "<br>";
echo "logged_in: " . ($_SESSION['logged_in'] ?? 'Not set') . "<br>";
echo "user_type: " . ($_SESSION['user_type'] ?? 'Not set') . "<br>";

// Include Laravel functions
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

// Check Laravel session
echo "<h3>Laravel session variables:</h3>";
echo "user_id: " . (session('user_id') ?? 'Not set') . "<br>";
echo "user_name: " . (session('user_name') ?? 'Not set') . "<br>";
echo "logged_in: " . (session('logged_in') ?? 'Not set') . "<br>";
echo "user_role: " . (session('user_role') ?? 'Not set') . "<br>";

// Test the fallback logic
echo "<h3>Fallback logic (what will be used in chat):</h3>";
$currentUserId = session('user_id') ?: ($_SESSION['user_id'] ?? null);
$currentUserName = session('user_name') ?: ($_SESSION['user_name'] ?? 'Guest');
$currentUserAuth = session('logged_in') ?: ($_SESSION['logged_in'] ?? false);
$currentUserRole = session('user_role') ?: ($_SESSION['user_type'] ?? 'guest');

echo "Final user_id: " . ($currentUserId ?? 'null') . "<br>";
echo "Final user_name: " . $currentUserName . "<br>";
echo "Final logged_in: " . ($currentUserAuth ? 'true' : 'false') . "<br>";
echo "Final user_role: " . $currentUserRole . "<br>";
?>
