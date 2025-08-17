<?php
// Test SmartPrep login functionality
// Run this from A.R.T.C directory: php test_smartprep_login.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

echo "=== SmartPrep Login Test ===" . PHP_EOL;

// Get the admin user
$user = DB::table('users')->where('email', 'admin@smartprep.com')->first();

if ($user) {
    echo "✓ Admin user found" . PHP_EOL;
    echo "  Email: " . $user->email . PHP_EOL;
    echo "  Role: " . $user->role . PHP_EOL;
    echo "  ID: " . $user->id . PHP_EOL;
    
    // Test password verification
    $passwordCorrect = Hash::check('admin123', $user->password);
    echo "✓ Password 'admin123' verification: " . ($passwordCorrect ? "SUCCESS" : "FAILED") . PHP_EOL;
    
    if ($passwordCorrect) {
        echo PHP_EOL . "=== LOGIN SUCCESSFUL ===" . PHP_EOL;
        echo "You can now log in to SmartPrep with:" . PHP_EOL;
        echo "URL: http://127.0.0.1:8001/login" . PHP_EOL;
        echo "Email: admin@smartprep.com" . PHP_EOL;
        echo "Password: admin123" . PHP_EOL;
        echo PHP_EOL;
        echo "After login, you should be redirected to:" . PHP_EOL;
        echo "http://127.0.0.1:8001/admin/dashboard" . PHP_EOL;
    }
} else {
    echo "✗ Admin user not found" . PHP_EOL;
}
?>
