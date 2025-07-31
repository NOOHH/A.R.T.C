<?php
// Set up professor session for testing
session_start();

// Set required session variables for professor authentication
$_SESSION['logged_in'] = true;
$_SESSION['professor_id'] = 8;
$_SESSION['user_role'] = 'professor';
$_SESSION['user_type'] = 'professor';
$_SESSION['user_id'] = 8;
$_SESSION['professor_name'] = 'robert san';

echo "Professor session set successfully!" . PHP_EOL;
echo "Session ID: " . session_id() . PHP_EOL;
echo "Professor ID: " . $_SESSION['professor_id'] . PHP_EOL;
echo "User Role: " . $_SESSION['user_role'] . PHP_EOL;
echo "Logged In: " . ($_SESSION['logged_in'] ? 'Yes' : 'No') . PHP_EOL;

// Save session
session_write_close();

echo "\nNow you can test the quiz generator!" . PHP_EOL;
