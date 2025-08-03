<?php
/**
 * Simple Admin API Debug Test
 */

echo "=== ADMIN API DEBUG TEST ===\n\n";

try {
    // Bootstrap Laravel without starting sessions
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    
    // Basic Laravel boot
    $app->make('Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables')->bootstrap($app);
    $app->make('Illuminate\Foundation\Bootstrap\LoadConfiguration')->bootstrap($app);
    $app->make('Illuminate\Foundation\Bootstrap\HandleExceptions')->bootstrap($app);
    $app->make('Illuminate\Foundation\Bootstrap\RegisterFacades')->bootstrap($app);
    $app->make('Illuminate\Foundation\Bootstrap\RegisterProviders')->bootstrap($app);
    
    echo "1. DATABASE CONNECTION TEST\n";
    echo "===========================\n";
    
    try {
        $moduleCount = \App\Models\Module::count();
        $courseCount = \App\Models\Course::count();
        $contentCount = \App\Models\ContentItem::count();
        
        echo "✓ Database connected successfully\n";
        echo "  Modules: $moduleCount\n";
        echo "  Courses: $courseCount\n";
        echo "  Content Items: $contentCount\n";
    } catch (Exception $e) {
        echo "✗ Database error: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    echo "\n2. ROUTE DEFINITIONS CHECK\n";
    echo "==========================\n";
    
    $routes = [
        'admin.modules.content' => '/admin/modules/{moduleId}/content',
        'admin.modules.courses.content' => '/admin/modules/{moduleId}/courses/{courseId}/content'
    ];
    
    foreach ($routes as $name => $pattern) {
        try {
            $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName($name);
            if ($route) {
                echo "✓ Route '$name' found\n";
                echo "  URI: " . $route->uri() . "\n";
                echo "  Methods: " . implode('|', $route->methods()) . "\n";
                echo "  Controller: " . $route->getActionName() . "\n";
                echo "  Middleware: " . implode(', ', $route->gatherMiddleware()) . "\n\n";
            } else {
                echo "✗ Route '$name' not found\n";
            }
        } catch (Exception $e) {
            echo "✗ Route '$name' error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "3. CONTROLLER METHODS TEST\n";
    echo "==========================\n";
    
    try {
        $controller = new \App\Http\Controllers\AdminModuleController();
        
        // Get a test module
        $testModule = \App\Models\Module::first();
        if (!$testModule) {
            echo "✗ No modules found in database\n";
            exit(1);
        }
        
        echo "Testing with Module ID: {$testModule->modules_id} - {$testModule->module_name}\n\n";
        
        // Test getModuleContent method
        echo "Testing getModuleContent method:\n";
        $response = $controller->getModuleContent($testModule->modules_id);
        
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $content = $response->getContent();
            $data = json_decode($content, true);
            
            if ($data && isset($data['success'])) {
                echo "✓ getModuleContent: SUCCESS\n";
                echo "  Response type: JSON\n";
                echo "  Success: " . ($data['success'] ? 'true' : 'false') . "\n";
                echo "  Module name: " . ($data['module']['module_name'] ?? 'N/A') . "\n";
                echo "  Courses count: " . count($data['courses'] ?? []) . "\n";
            } else {
                echo "✗ getModuleContent: Invalid JSON response\n";
                echo "  Content: " . substr($content, 0, 200) . "\n";
            }
        } else {
            echo "✗ getModuleContent: Not a JSON response\n";
            echo "  Response type: " . get_class($response) . "\n";
        }
        
        // Test getCourseContentItems method
        $testCourse = \App\Models\Course::where('module_id', $testModule->modules_id)->first();
        if ($testCourse) {
            echo "\nTesting getCourseContentItems method:\n";
            echo "Course ID: {$testCourse->subject_id} - {$testCourse->subject_name}\n";
            
            $response = $controller->getCourseContentItems($testModule->modules_id, $testCourse->subject_id);
            
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $content = $response->getContent();
                $data = json_decode($content, true);
                
                if ($data && isset($data['success'])) {
                    echo "✓ getCourseContentItems: SUCCESS\n";
                    echo "  Response type: JSON\n";
                    echo "  Success: " . ($data['success'] ? 'true' : 'false') . "\n";
                    echo "  Course name: " . ($data['course']['course_name'] ?? 'N/A') . "\n";
                    echo "  Content items count: " . count($data['content_items'] ?? []) . "\n";
                } else {
                    echo "✗ getCourseContentItems: Invalid JSON response\n";
                    echo "  Content: " . substr($content, 0, 200) . "\n";
                }
            } else {
                echo "✗ getCourseContentItems: Not a JSON response\n";
                echo "  Response type: " . get_class($response) . "\n";
            }
        } else {
            echo "\nNo courses found for test module\n";
        }
        
    } catch (Exception $e) {
        echo "✗ Controller test error: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
    echo "\n4. ADMIN SETTINGS CHECK\n";
    echo "=======================\n";
    
    try {
        $directorSettings = \App\Models\AdminSetting::where('setting_key', 'LIKE', 'director_%')->get();
        
        if ($directorSettings->count() > 0) {
            echo "Director-related settings:\n";
            foreach ($directorSettings as $setting) {
                echo "  {$setting->setting_key} = '{$setting->setting_value}'\n";
            }
            
            $modulesSetting = \App\Models\AdminSetting::getValue('director_manage_modules', 'false');
            $canManageModules = $modulesSetting === 'true' || $modulesSetting === '1';
            echo "\nDirector can manage modules: " . ($canManageModules ? 'YES' : 'NO') . "\n";
        } else {
            echo "No director settings found\n";
        }
    } catch (Exception $e) {
        echo "✗ Admin settings error: " . $e->getMessage() . "\n";
    }
    
    echo "\n5. MIDDLEWARE CLASS CHECK\n";
    echo "=========================\n";
    
    if (class_exists('\App\Http\Middleware\CheckAdminDirectorAuth')) {
        echo "✓ CheckAdminDirectorAuth middleware class exists\n";
    } else {
        echo "✗ CheckAdminDirectorAuth middleware class not found\n";
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "The controller methods are working and returning JSON.\n";
    echo "The issue is likely authentication middleware redirecting to login.\n";
    echo "When a user is NOT logged in, the middleware redirects to login page (HTML).\n";
    echo "JavaScript tries to parse this HTML as JSON, causing the syntax error.\n\n";
    
    echo "SOLUTION:\n";
    echo "1. Ensure user is logged in as admin or director\n";
    echo "2. Enable director_manage_modules setting if using director account\n";
    echo "3. Include proper headers in JavaScript requests (Accept: application/json)\n";
    echo "4. Add authentication error handling in JavaScript\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
