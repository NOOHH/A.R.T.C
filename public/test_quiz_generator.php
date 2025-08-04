<?php
// Test script to debug quiz generator redirection issue
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\AdminSetting;

echo "=== Quiz Generator Debug Test ===\n\n";

// 1. Check if AI Quiz is enabled
$aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value');
echo "1. AI Quiz Enabled Setting: " . ($aiQuizEnabled ?? 'NOT FOUND') . "\n";

// 2. Check admin session data
echo "\n2. Session Data:\n";
echo "   user_id: " . (session('user_id') ?? 'NULL') . "\n";
echo "   logged_in: " . (session('logged_in') ?? 'NULL') . "\n";
echo "   role: " . (session('role') ?? 'NULL') . "\n";
echo "   type: " . (session('type') ?? 'NULL') . "\n";

// 3. Check if admin exists
if (session('user_id')) {
    $admin = DB::table('admins')->where('admin_id', session('user_id'))->first();
    echo "\n3. Admin Record:\n";
    if ($admin) {
        echo "   Admin ID: " . $admin->admin_id . "\n";
        echo "   Admin Name: " . $admin->admin_name . "\n";
        echo "   Email: " . $admin->email . "\n";
    } else {
        echo "   Admin not found for user_id: " . session('user_id') . "\n";
    }
}

// 4. Test the route directly
echo "\n4. Testing Route Access:\n";
try {
    // Simulate admin session
    Session::put('user_id', 1);
    Session::put('logged_in', true);
    Session::put('role', 'admin');
    Session::put('type', 'admin');
    
    echo "   Session set for admin user_id: 1\n";
    
    // Check if the route exists
    $routes = app('router')->getRoutes();
    $quizRoute = null;
    foreach ($routes as $route) {
        if ($route->getName() === 'admin.quiz-generator') {
            $quizRoute = $route;
            break;
        }
    }
    
    if ($quizRoute) {
        echo "   Route 'admin.quiz-generator' found\n";
        echo "   Route URI: " . $quizRoute->uri() . "\n";
        echo "   Route Methods: " . implode(', ', $quizRoute->methods()) . "\n";
        echo "   Route Middleware: " . implode(', ', $quizRoute->middleware()) . "\n";
    } else {
        echo "   Route 'admin.quiz-generator' NOT FOUND\n";
    }
    
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

// 5. Check middleware configuration
echo "\n5. Middleware Configuration:\n";
$middleware = app('router')->getMiddleware();
if (isset($middleware['admin.director.auth'])) {
    echo "   admin.director.auth middleware: " . $middleware['admin.director.auth'] . "\n";
} else {
    echo "   admin.director.auth middleware: NOT FOUND\n";
}

echo "\n=== Test Complete ===\n";
?> 