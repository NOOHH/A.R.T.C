<?php
// Test login for modal functionality
session_start();

// Simulate admin login
$_SESSION['user_id'] = 1;
$_SESSION['logged_in'] = true;
$_SESSION['user_type'] = 'admin';
$_SESSION['username'] = 'test_admin';

echo "Session set for testing:\n";
echo "User ID: " . $_SESSION['user_id'] . "\n";
echo "Logged in: " . ($_SESSION['logged_in'] ? 'Yes' : 'No') . "\n";
echo "User type: " . $_SESSION['user_type'] . "\n";

echo "\nNow you can test the modal functionality by visiting the admin page.\n";
echo "The modal should work correctly with authentication in place.\n";
?>
