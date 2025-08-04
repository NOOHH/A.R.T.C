<?php
// Test our updated SessionManager in Laravel context
require_once '../bootstrap/app.php';

// Start Laravel application
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request to initialize Laravel
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Set up test session data in Laravel
session([
    'user_id' => 15,
    'user_name' => 'Vince Michael Dela Vega',
    'user_role' => 'student',
    'logged_in' => true
]);

echo "<h1>🧪 Laravel SessionManager Test</h1>";
echo "<p>✅ Laravel application initialized</p>";
echo "<p>✅ Test session data set</p>";

// Test our SessionManager
use App\Helpers\SessionManager;

echo "<h2>🛠️ SessionManager Tests (Laravel Context)</h2>";

echo "<p><strong>isLaravelContext():</strong> " . (function_exists('app') && app()->bound('session') ? 'TRUE ✅' : 'FALSE ❌') . "</p>";
echo "<p><strong>SessionManager::isLoggedIn():</strong> " . (SessionManager::isLoggedIn() ? 'TRUE ✅' : 'FALSE ❌') . "</p>";
echo "<p><strong>SessionManager::getUserType():</strong> " . (SessionManager::getUserType() ?? 'NULL') . "</p>";
echo "<p><strong>SessionManager::get('user_id'):</strong> " . (SessionManager::get('user_id') ?? 'NULL') . "</p>";
echo "<p><strong>SessionManager::get('user_name'):</strong> " . (SessionManager::get('user_name') ?? 'NULL') . "</p>";

echo "<h2>📋 Direct Laravel Session</h2>";
echo "<p><strong>session('user_id'):</strong> " . (session('user_id') ?? 'NULL') . "</p>";
echo "<p><strong>session('user_role'):</strong> " . (session('user_role') ?? 'NULL') . "</p>";
echo "<p><strong>session('logged_in'):</strong> " . (session('logged_in') ? 'TRUE' : 'FALSE') . "</p>";

// Test the middleware authentication check
echo "<h2>🔒 Middleware Check Simulation</h2>";

try {
    if (!SessionManager::isLoggedIn()) {
        echo "<p>❌ Authentication failed</p>";
    } else {
        echo "<p>✅ Authentication successful</p>";
        
        $userType = SessionManager::getUserType();
        if ($userType === 'student') {
            echo "<p>✅ User type is student</p>";
        } else {
            echo "<p>❌ User type is not student: $userType</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<br><hr>";
echo "<h2>🔗 Test Quiz Access</h2>";
echo "<a href='/A.R.T.C/public/student/quiz/take/3' target='_blank' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Try Quiz Route Now</a>";
echo "<a href='/A.R.T.C/public/student/dashboard' target='_blank' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Dashboard</a>";
?>
