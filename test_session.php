<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing session authentication for different user types:" . PHP_EOL;

// Test with student session
session_start();
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 112;
$_SESSION['user_name'] = 'bryan justimbaste';
$_SESSION['user_role'] = 'student';

echo "Student session test:" . PHP_EOL;
echo "logged_in: " . (session('logged_in', false) ? 'true' : 'false') . PHP_EOL;
echo "user_id: " . session('user_id') . PHP_EOL;
echo "user_name: " . session('user_name') . PHP_EOL;
echo "user_role: " . session('user_role') . PHP_EOL;

// Test with professor session
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 111;
$_SESSION['user_name'] = 'robert san';
$_SESSION['user_role'] = 'professor';

echo PHP_EOL . "Professor session test:" . PHP_EOL;
echo "logged_in: " . (session('logged_in', false) ? 'true' : 'false') . PHP_EOL;
echo "user_id: " . session('user_id') . PHP_EOL;
echo "user_name: " . session('user_name') . PHP_EOL;
echo "user_role: " . session('user_role') . PHP_EOL;
?>
