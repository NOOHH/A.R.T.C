<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminProgramController;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TESTING TENANT ADMIN PROGRAMS FUNCTIONALITY\n\n";

// Switch to tenant database
Config::set('database.default', 'tenant');

try {
    // Test 1: Check if we can access programs data
    echo "📊 Test 1: Accessing programs data from tenant database\n";
    $programs = DB::table('programs')->get();
    echo "✅ Found " . count($programs) . " programs in tenant database\n";
    
    foreach ($programs as $program) {
        echo "  - {$program->program_name} (ID: {$program->program_id})\n";
    }
    
    // Test 2: Check if AdminProgramController can work with tenant database
    echo "\n📊 Test 2: Testing AdminProgramController with tenant database\n";
    
    // Mock a request
    $request = Request::create('/admin/programs', 'GET');
    
    // Create controller instance
    $controller = new AdminProgramController();
    
    // Test the index method
    try {
        $response = $controller->index();
        echo "✅ AdminProgramController index method executed successfully\n";
        
        // Check if response is a view
        if (method_exists($response, 'getName')) {
            echo "✅ Response is a view: " . $response->getName() . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ AdminProgramController index method failed: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Check if routes are accessible
    echo "\n📊 Test 3: Testing admin programs routes\n";
    
    $routes = [
        'admin.programs.index' => '/admin/programs',
        'admin.programs.store' => '/admin/programs',
        'admin.programs.archived' => '/admin/programs/archived'
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
    
    // Test 4: Check if we can create a new program
    echo "\n📊 Test 4: Testing program creation capability\n";
    
    $testProgramData = [
        'program_name' => 'Test Program - ' . date('Y-m-d H:i:s'),
        'program_description' => 'This is a test program created during testing',
        'is_archived' => false
    ];
    
    try {
        $newProgramId = DB::table('programs')->insertGetId($testProgramData);
        echo "✅ Successfully created test program with ID: $newProgramId\n";
        
        // Clean up - delete the test program
        DB::table('programs')->where('program_id', $newProgramId)->delete();
        echo "✅ Test program cleaned up\n";
        
    } catch (Exception $e) {
        echo "❌ Failed to create test program: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Check enrollments relationship
    echo "\n📊 Test 5: Testing enrollments relationship\n";
    
    $programWithEnrollments = DB::table('programs')
        ->leftJoin('enrollments', 'programs.program_id', '=', 'enrollments.program_id')
        ->select('programs.program_id', 'programs.program_name', DB::raw('COUNT(enrollments.enrollment_id) as enrollment_count'))
        ->groupBy('programs.program_id', 'programs.program_name')
        ->get();
    
    echo "✅ Programs with enrollment counts:\n";
    foreach ($programWithEnrollments as $program) {
        echo "  - {$program->program_name}: {$program->enrollment_count} enrollments\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error in tenant admin programs test: " . $e->getMessage() . "\n";
}

echo "\n=== TENANT ADMIN PROGRAMS TEST COMPLETE ===\n";
