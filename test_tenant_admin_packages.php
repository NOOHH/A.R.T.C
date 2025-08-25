<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminPackageController;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TESTING TENANT ADMIN PACKAGES FUNCTIONALITY\n\n";

// Switch to tenant database
Config::set('database.default', 'tenant');

try {
    // Test 1: Check if we can access packages data
    echo "📦 Test 1: Accessing packages data from tenant database\n";
    $packages = DB::table('packages')->get();
    echo "✅ Found " . count($packages) . " packages in tenant database\n";
    
    foreach ($packages as $package) {
        echo "  - {$package->package_name} (ID: {$package->package_id}, Type: {$package->package_type})\n";
    }
    
    // Test 2: Check if AdminPackageController can work with tenant database
    echo "\n📦 Test 2: Testing AdminPackageController with tenant database\n";
    
    // Create controller instance
    $controller = new AdminPackageController();
    
    // Test the index method
    try {
        $response = $controller->index();
        echo "✅ AdminPackageController index method executed successfully\n";
        
        // Check if response is a view
        if (method_exists($response, 'getName')) {
            echo "✅ Response is a view: " . $response->getName() . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ AdminPackageController index method failed: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Check if routes are accessible
    echo "\n📦 Test 3: Testing admin packages routes\n";
    
    $routes = [
        'admin.packages.index' => '/admin/packages',
        'admin.packages.store' => '/admin/packages',
        'admin.packages.show' => '/admin/packages/{id}',
        'admin.packages.edit' => '/admin/packages/{id}/edit',
        'admin.packages.update' => '/admin/packages/{id}',
        'admin.packages.destroy' => '/admin/packages/{id}',
        'admin.get-program-modules' => '/admin/get-program-modules'
    ];
    
    foreach ($routes as $routeName => $routePath) {
        try {
            $route = Route::getRoutes()->getByName($routeName);
            if ($route) {
                echo "✅ Route '$routeName' exists and points to: " . $route->uri() . "\n";
            } else {
                echo "❌ Route '$routeName' not found\n";
            }
        } catch (Exception $e) {
            echo "❌ Error checking route '$routeName': " . $e->getMessage() . "\n";
        }
    }
    
    // Test 4: Check if we can create a new package
    echo "\n📦 Test 4: Testing package creation capability\n";
    
    $testPackageData = [
        'package_name' => 'Test Package - ' . date('Y-m-d H:i:s'),
        'description' => 'This is a test package created during testing',
        'amount' => 999.99,
        'package_type' => 'full',
        'price' => 999.99,
        'created_by_admin_id' => 1
    ];
    
    try {
        $newPackageId = DB::table('packages')->insertGetId($testPackageData);
        echo "✅ Successfully created test package with ID: $newPackageId\n";
        
        // Clean up - delete the test package
        DB::table('packages')->where('package_id', $newPackageId)->delete();
        echo "✅ Test package cleaned up\n";
        
    } catch (Exception $e) {
        echo "❌ Failed to create test package: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Check package relationships
    echo "\n📦 Test 5: Testing package relationships\n";
    
    // Check package-module relationships
    $packageModules = DB::table('package_modules')->get();
    echo "✅ Package-module relationships: " . count($packageModules) . " found\n";
    
    // Check package-course relationships
    $packageCourses = DB::table('package_courses')->get();
    echo "✅ Package-course relationships: " . count($packageCourses) . " found\n";
    
    // Test 6: Test get-program-modules API endpoint
    echo "\n📦 Test 6: Testing get-program-modules API endpoint\n";
    
    try {
        // Get first program
        $firstProgram = DB::table('programs')->first();
        if ($firstProgram) {
            $request = Request::create('/admin/get-program-modules', 'GET', [
                'program_id' => $firstProgram->program_id
            ]);
            
            $response = $controller->getProgramModules($request);
            echo "✅ getProgramModules method executed successfully for program: {$firstProgram->program_name}\n";
            
            // Check if response is JSON
            if (method_exists($response, 'getData')) {
                $data = $response->getData();
                echo "✅ Response contains data structure\n";
            }
        } else {
            echo "⚠️ No programs found to test get-program-modules\n";
        }
        
    } catch (Exception $e) {
        echo "❌ getProgramModules method failed: " . $e->getMessage() . "\n";
    }
    
    // Test 7: Check modules and courses data
    echo "\n📦 Test 7: Checking modules and courses data\n";
    
    $modules = DB::table('modules')->get();
    echo "✅ Found " . count($modules) . " modules in tenant database\n";
    
    $courses = DB::table('courses')->get();
    echo "✅ Found " . count($courses) . " courses in tenant database\n";
    
    // Show sample modules and courses
    if (count($modules) > 0) {
        echo "📚 Sample modules:\n";
        foreach ($modules->take(3) as $module) {
            echo "  - {$module->module_name} (ID: {$module->modules_id})\n";
        }
    }
    
    if (count($courses) > 0) {
        echo "📖 Sample courses:\n";
        foreach ($courses->take(3) as $course) {
            echo "  - {$course->subject_name} (ID: {$course->subject_id})\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error in tenant admin packages test: " . $e->getMessage() . "\n";
}

echo "\n=== TENANT ADMIN PACKAGES TEST COMPLETE ===\n";
