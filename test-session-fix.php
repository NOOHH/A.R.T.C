<?php

// Test session fix for admin authentication
session_start();

// Simulate admin login by setting session variables as UnifiedLoginController would
$_SESSION['user_id'] = 1;
$_SESSION['user_type'] = 'admin';
$_SESSION['user_name'] = 'Test Admin';
$_SESSION['user_email'] = 'admin@test.com';

// Also set Laravel session variables
$_SESSION['user_role'] = 'admin';
$_SESSION['logged_in'] = true;

echo "Session variables set:\n";
echo "PHP Session user_id: " . ($_SESSION['user_id'] ?? 'not set') . "\n";
echo "PHP Session user_type: " . ($_SESSION['user_type'] ?? 'not set') . "\n";
echo "PHP Session user_role: " . ($_SESSION['user_role'] ?? 'not set') . "\n";
echo "PHP Session logged_in: " . ($_SESSION['logged_in'] ? 'true' : 'false') . "\n";

// Load Laravel and test SessionManager
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Test our SessionManager
use App\Helpers\SessionManager;

echo "\nSessionManager tests:\n";
echo "isLoggedIn(): " . (SessionManager::isLoggedIn() ? 'true' : 'false') . "\n";
echo "getUserType(): " . (SessionManager::getUserType() ?? 'null') . "\n";

echo "\nTest completed successfully!\n";
