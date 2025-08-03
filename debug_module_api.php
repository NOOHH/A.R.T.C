<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminModuleController;

echo "=== COMPREHENSIVE MODULE API DEBUGGING ===\n\n";

// Test 1: Check if routes exist
echo "1. CHECKING ROUTES:\n";
$routes = Route::getRoutes();
$moduleContentRoutes = [];

foreach ($routes as $route) {
    if (strpos($route->uri(), 'admin/modules') !== false && strpos($route->uri(), 'content') !== false) {
        $moduleContentRoutes[] = [
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'middleware' => $route->gatherMiddleware()
        ];
    }
}

foreach ($moduleContentRoutes as $route) {
    echo "✓ {$route['method']} {$route['uri']} -> {$route['name']}\n";
    echo "  Middleware: " . implode(', ', $route['middleware']) . "\n\n";
}

// Test 2: Check if AdminModuleController methods exist
echo "2. CHECKING CONTROLLER METHODS:\n";
$controller = new AdminModuleController();
$methods = ['getModuleContent', 'getCourseContentItems'];

foreach ($methods as $method) {
    if (method_exists($controller, $method)) {
        echo "✓ AdminModuleController::{$method} exists\n";
    } else {
        echo "✗ AdminModuleController::{$method} missing\n";
    }
}

// Test 3: Check database tables and sample data
echo "\n3. CHECKING DATABASE:\n";
try {
    $modules = \App\Models\Module::where('is_archived', false)->limit(3)->get();
    echo "✓ Found " . $modules->count() . " active modules\n";
    
    foreach ($modules as $module) {
        echo "  Module {$module->modules_id}: {$module->module_name}\n";
        
        $courses = \App\Models\Course::where('module_id', $module->modules_id)->limit(2)->get();
        echo "    - {$courses->count()} courses\n";
        
        foreach ($courses as $course) {
            $contentCount = \App\Models\ContentItem::where('course_id', $course->subject_id)->count();
            echo "      Course {$course->subject_id}: {$course->subject_name} ({$contentCount} content items)\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

// Test 4: Simulate API calls
echo "\n4. TESTING API ENDPOINTS:\n";

// Test getModuleContent
if (!empty($modules)) {
    $testModule = $modules->first();
    echo "Testing getModuleContent for module {$testModule->modules_id}:\n";
    
    try {
        $response = $controller->getModuleContent($testModule->modules_id);
        $responseData = json_decode($response->getContent(), true);
        
        if (isset($responseData['success']) && $responseData['success']) {
            echo "✓ getModuleContent API working\n";
            echo "  Module: {$responseData['module']['module_name']}\n";
            echo "  Courses: " . count($responseData['courses']) . "\n";
        } else {
            echo "✗ getModuleContent API failed\n";
            echo "  Error: " . ($responseData['message'] ?? 'Unknown error') . "\n";
        }
    } catch (Exception $e) {
        echo "✗ getModuleContent exception: " . $e->getMessage() . "\n";
    }
    
    // Test getCourseContentItems
    $testCourse = \App\Models\Course::where('module_id', $testModule->modules_id)->first();
    if ($testCourse) {
        echo "\nTesting getCourseContentItems for course {$testCourse->subject_id}:\n";
        
        try {
            $response = $controller->getCourseContentItems($testModule->modules_id, $testCourse->subject_id);
            $responseData = json_decode($response->getContent(), true);
            
            if (isset($responseData['success']) && $responseData['success']) {
                echo "✓ getCourseContentItems API working\n";
                echo "  Content items: " . count($responseData['content_items']) . "\n";
            } else {
                echo "✗ getCourseContentItems API failed\n";
                echo "  Error: " . ($responseData['message'] ?? 'Unknown error') . "\n";
            }
        } catch (Exception $e) {
            echo "✗ getCourseContentItems exception: " . $e->getMessage() . "\n";
        }
    }
}

// Test 5: Check session and auth
echo "\n5. CHECKING AUTHENTICATION:\n";
echo "Session ID: " . session_id() . "\n";
echo "User ID: " . (session('user_id') ?? 'Not set') . "\n";
echo "User Type: " . (session('user_type') ?? 'Not set') . "\n";
echo "User Role: " . (session('user_role') ?? 'Not set') . "\n";

// Test 6: Check middleware
echo "\n6. CHECKING MIDDLEWARE:\n";
$middlewares = app('router')->getMiddleware();
foreach (['admin.auth', 'admin.director.auth'] as $middleware) {
    if (isset($middlewares[$middleware])) {
        echo "✓ Middleware '{$middleware}' registered: {$middlewares[$middleware]}\n";
    } else {
        echo "✗ Middleware '{$middleware}' not found\n";
    }
}

echo "\n=== DEBUGGING COMPLETE ===\n";

?>
