<?php
// Set up admin session for testing
session_start();

// Set fake admin session
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'Test Admin';
$_SESSION['admin_role'] = 'admin';

// Redirect to chat test
header('Location: /final-chat-test.html');
exit;
?>
