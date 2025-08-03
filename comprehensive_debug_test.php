<?php
/**
 * Comprehensive Admin Modules API Debug Test
 * Tests authentication, routes, and API responses
 */

// Bootstrap Laravel
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();
    
    echo "=== COMPREHENSIVE ADMIN MODULES DEBUG ===\n\n";
    
    // Start session to simulate web request
    session_start();
    
    echo "1. SESSION AND AUTHENTICATION CHECK\n";
    echo "===================================\n";
    
    // Check PHP session
    echo "PHP Session Data:\n";
    if (!empty($_SESSION)) {
        foreach ($_SESSION as $key => $value) {
            echo "  \$_SESSION['$key'] = " . (is_array($value) ? json_encode($value) : $value) . "\n";
        }
    } else {
        echo "  No PHP session data found\n";
    }
    
    echo "\nLaravel Session Data:\n";
    $laravelSession = session()->all();
    if (!empty($laravelSession)) {
        foreach ($laravelSession as $key => $value) {
            echo "  session('$key') = " . (is_array($value) ? json_encode($value) : $value) . "\n";
        }
    } else {
        echo "  No Laravel session data found\n";
    }
    
    // Test authentication
    echo "\n2. AUTHENTICATION GUARDS CHECK\n";
    echo "===============================\n";
    
    $authUser = \Illuminate\Support\Facades\Auth::user();
    $authAdmin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
    $authDirector = \Illuminate\Support\Facades\Auth::guard('director')->user();
    
    echo "Auth::user(): " . ($authUser ? "ID: {$authUser->id}" : "NULL") . "\n";
    echo "Auth::guard('admin')->user(): " . ($authAdmin ? "ID: {$authAdmin->admin_id}" : "NULL") . "\n";
    echo "Auth::guard('director')->user(): " . ($authDirector ? "ID: {$authDirector->director_id}" : "NULL") . "\n";
    
    // Test middleware
    echo "\n3. MIDDLEWARE SIMULATION\n";
    echo "========================\n";
    
    $middleware = new \App\Http\Middleware\CheckAdminDirectorAuth();
    $request = \Illuminate\Http\Request::create('/admin/modules/1/content', 'GET');
    
    try {
        $response = $middleware->handle($request, function($req) {
            return response()->json(['success' => true, 'message' => 'Middleware passed']);
        });
        
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            echo "✓ Middleware test: PASSED - " . $response->getContent() . "\n";
        } else {
            echo "✗ Middleware test: FAILED - Redirected to: " . $response->headers->get('Location') . "\n";
        }
    } catch (Exception $e) {
        echo "✗ Middleware test: ERROR - " . $e->getMessage() . "\n";
    }
    
    // Test database connection and data
    echo "\n4. DATABASE CHECK\n";
    echo "=================\n";
    
    try {
        $moduleCount = \App\Models\Module::count();
        $courseCount = \App\Models\Course::count();
        $contentCount = \App\Models\ContentItem::count();
        
        echo "✓ Database connected\n";
        echo "  Modules: $moduleCount\n";
        echo "  Courses: $courseCount\n";
        echo "  Content Items: $contentCount\n";
        
        // Test specific module
        $testModule = \App\Models\Module::first();
        if ($testModule) {
            echo "  Test Module: {$testModule->modules_id} - {$testModule->module_name}\n";
            
            $courses = \App\Models\Course::where('module_id', $testModule->modules_id)->get();
            echo "  Courses in test module: " . $courses->count() . "\n";
            
            foreach ($courses as $course) {
                $contentItems = \App\Models\ContentItem::where('course_id', $course->subject_id)->count();
                echo "    Course {$course->subject_id}: {$course->subject_name} ({$contentItems} items)\n";
            }
        }
    } catch (Exception $e) {
        echo "✗ Database error: " . $e->getMessage() . "\n";
    }
    
    // Test API endpoints directly
    echo "\n5. DIRECT API ENDPOINT TESTS\n";
    echo "============================\n";
    
    if ($testModule) {
        echo "Testing AdminModuleController methods directly:\n";
        
        try {
            $controller = new \App\Http\Controllers\AdminModuleController();
            
            // Test getModuleContent
            echo "\nTesting getModuleContent({$testModule->modules_id}):\n";
            $response = $controller->getModuleContent($testModule->modules_id);
            $content = $response->getContent();
            $data = json_decode($content, true);
            
            if ($data && isset($data['success'])) {
                echo "✓ getModuleContent: SUCCESS\n";
                echo "  Module: " . ($data['module']['module_name'] ?? 'N/A') . "\n";
                echo "  Courses: " . count($data['courses'] ?? []) . "\n";
            } else {
                echo "✗ getModuleContent: FAILED\n";
                echo "  Response: " . substr($content, 0, 200) . "...\n";
            }
            
            // Test getCourseContentItems
            if (!empty($courses)) {
                $testCourse = $courses->first();
                echo "\nTesting getCourseContentItems({$testModule->modules_id}, {$testCourse->subject_id}):\n";
                
                $response = $controller->getCourseContentItems($testModule->modules_id, $testCourse->subject_id);
                $content = $response->getContent();
                $data = json_decode($content, true);
                
                if ($data && isset($data['success'])) {
                    echo "✓ getCourseContentItems: SUCCESS\n";
                    echo "  Course: " . ($data['course']['course_name'] ?? 'N/A') . "\n";
                    echo "  Content Items: " . count($data['content_items'] ?? []) . "\n";
                } else {
                    echo "✗ getCourseContentItems: FAILED\n";
                    echo "  Response: " . substr($content, 0, 200) . "...\n";
                }
            }
        } catch (Exception $e) {
            echo "✗ Controller test error: " . $e->getMessage() . "\n";
        }
    }
    
    // Test route resolution
    echo "\n6. ROUTE RESOLUTION TEST\n";
    echo "========================\n";
    
    try {
        $routes = [
            '/admin/modules/1/content',
            '/admin/modules/1/courses/1/content'
        ];
        
        foreach ($routes as $path) {
            try {
                $route = \Illuminate\Support\Facades\Route::getRoutes()->match(
                    \Illuminate\Http\Request::create($path, 'GET')
                );
                
                echo "✓ Route '$path' found:\n";
                echo "  Name: " . ($route->getName() ?? 'unnamed') . "\n";
                echo "  Controller: " . $route->getActionName() . "\n";
                echo "  Middleware: " . implode(', ', $route->gatherMiddleware()) . "\n";
            } catch (Exception $e) {
                echo "✗ Route '$path' error: " . $e->getMessage() . "\n";
            }
        }
    } catch (Exception $e) {
        echo "✗ Route resolution error: " . $e->getMessage() . "\n";
    }
    
    // Check admin settings for director permissions
    echo "\n7. DIRECTOR PERMISSIONS CHECK\n";
    echo "=============================\n";
    
    try {
        $directorModulesSetting = \App\Models\AdminSetting::getValue('director_manage_modules', 'false');
        echo "director_manage_modules setting: '$directorModulesSetting'\n";
        
        $canDirectorManageModules = $directorModulesSetting === 'true' || $directorModulesSetting === '1';
        echo "Can director manage modules: " . ($canDirectorManageModules ? 'YES' : 'NO') . "\n";
        
        // List all director-related settings
        $directorSettings = \App\Models\AdminSetting::where('setting_key', 'LIKE', 'director_%')->get();
        echo "\nAll director settings:\n";
        foreach ($directorSettings as $setting) {
            echo "  {$setting->setting_key} = '{$setting->setting_value}'\n";
        }
    } catch (Exception $e) {
        echo "✗ Director permissions error: " . $e->getMessage() . "\n";
    }
    
    echo "\n8. RECOMMENDATIONS\n";
    echo "==================\n";
    
    if (!$authUser && !$authAdmin && !$authDirector) {
        echo "❌ NO AUTHENTICATED USER FOUND\n";
        echo "   - Log in as admin or director in the browser\n";
        echo "   - Ensure session is properly maintained\n";
    } elseif ($authDirector && !$canDirectorManageModules) {
        echo "❌ DIRECTOR LACKS MODULE PERMISSIONS\n";
        echo "   - Enable director_manage_modules in admin settings\n";
    } else {
        echo "✅ AUTHENTICATION LOOKS GOOD\n";
        echo "   - API endpoints should work if called from authenticated session\n";
    }
    
    echo "\n=== DEBUG COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
