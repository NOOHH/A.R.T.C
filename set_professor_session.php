<?php
/**
 * Set professor session for testing
 */

session_start();

// Set professor session data
$_SESSION['professor_id'] = 8;
$_SESSION['logged_in'] = true;
$_SESSION['user_role'] = 'professor';
$_SESSION['user_type'] = 'professor';
$_SESSION['user_id'] = 8;

echo "Professor session set successfully:\n";
echo "- professor_id: " . $_SESSION['professor_id'] . "\n";
echo "- logged_in: " . ($_SESSION['logged_in'] ? 'true' : 'false') . "\n";
echo "- user_role: " . $_SESSION['user_role'] . "\n";
echo "- Session ID: " . session_id() . "\n";
?>
