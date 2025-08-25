<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AdminProgramController;
use App\Http\Controllers\AdminPackageController;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🌐 TESTING TENANT WEB INTERFACE FOR ADMIN PROGRAMS & PACKAGES\n\n";

// Switch to tenant database
Config::set('database.default', 'tenant');

// Mock session data for admin user
Session::put('user_type', 'admin');
Session::put('admin_id', 1);

try {
    // Test 1: Test admin programs page rendering
    echo "📊 Test 1: Testing admin programs page rendering\n";
    
    $programController = new AdminProgramController();
    
    try {
        $response = $programController->index();
        echo "✅ Admin programs page rendered successfully\n";
        
        // Check if view data is properly loaded
        if (method_exists($response, 'getData')) {
            $viewData = $response->getData();
            echo "✅ View data loaded successfully\n";
            
            // Check if programs data is available
            if (isset($viewData['programs'])) {
                echo "✅ Programs data available in view: " . count($viewData['programs']) . " programs\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Admin programs page rendering failed: " . $e->getMessage() . "\n";
    }
    
    // Test 2: Test admin packages page rendering
    echo "\n📦 Test 2: Testing admin packages page rendering\n";
    
    $packageController = new AdminPackageController();
    
    try {
        $response = $packageController->index();
        echo "✅ Admin packages page rendered successfully\n";
        
        // Check if view data is properly loaded
        if (method_exists($response, 'getData')) {
            $viewData = $response->getData();
            echo "✅ View data loaded successfully\n";
            
            // Check if packages data is available
            if (isset($viewData['packages'])) {
                echo "✅ Packages data available in view: " . count($viewData['packages']) . " packages\n";
            }
            
            // Check if programs data is available for dropdown
            if (isset($viewData['programs'])) {
                echo "✅ Programs dropdown data available: " . count($viewData['programs']) . " programs\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Admin packages page rendering failed: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Test program creation via POST request
    echo "\n📊 Test 3: Testing program creation via POST request\n";
    
    $programData = [
        'program_name' => 'Test Web Program - ' . date('Y-m-d H:i:s'),
        'program_description' => 'This is a test program created via web interface simulation',
        'program_image' => null
    ];
    
    $request = Request::create('/admin/programs', 'POST', $programData);
    $request->setLaravelSession(Session::getFacadeRoot());
    
    try {
        $response = $programController->store($request);
        echo "✅ Program creation via POST request successful\n";
        
        // Check if program was actually created
        $createdProgram = DB::table('programs')
            ->where('program_name', $programData['program_name'])
            ->first();
            
        if ($createdProgram) {
            echo "✅ Program actually created in database with ID: {$createdProgram->program_id}\n";
            
            // Clean up
            DB::table('programs')->where('program_id', $createdProgram->program_id)->delete();
            echo "✅ Test program cleaned up\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Program creation via POST request failed: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Test package creation via POST request
    echo "\n📦 Test 4: Testing package creation via POST request\n";
    
    $packageData = [
        'package_name' => 'Test Web Package - ' . date('Y-m-d H:i:s'),
        'description' => 'This is a test package created via web interface simulation',
        'amount' => 1499.99,
        'package_type' => 'full',
        'program_id' => 40, // Use existing program ID
        'access_period_days' => 365,
        'access_period_months' => 0,
        'access_period_years' => 0
    ];
    
    $request = Request::create('/admin/packages', 'POST', $packageData);
    $request->setLaravelSession(Session::getFacadeRoot());
    
    try {
        $response = $packageController->store($request);
        echo "✅ Package creation via POST request successful\n";
        
        // Check if package was actually created
        $createdPackage = DB::table('packages')
            ->where('package_name', $packageData['package_name'])
            ->first();
            
        if ($createdPackage) {
            echo "✅ Package actually created in database with ID: {$createdPackage->package_id}\n";
            
            // Clean up
            DB::table('packages')->where('package_id', $createdPackage->package_id)->delete();
            echo "✅ Test package cleaned up\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Package creation via POST request failed: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Test JavaScript functionality simulation
    echo "\n🔧 Test 5: Testing JavaScript functionality simulation\n";
    
    // Test get-program-modules API endpoint
    $firstProgram = DB::table('programs')->first();
    if ($firstProgram) {
        $request = Request::create('/admin/get-program-modules', 'GET', [
            'program_id' => $firstProgram->program_id
        ]);
        
        try {
            $response = $packageController->getProgramModules($request);
            echo "✅ get-program-modules API endpoint working for program: {$firstProgram->program_name}\n";
            
            // Check response structure
            if (method_exists($response, 'getData')) {
                $data = $response->getData();
                if (isset($data->success) && $data->success) {
                    echo "✅ API response indicates success\n";
                    if (isset($data->modules)) {
                        echo "✅ API returned " . count($data->modules) . " modules\n";
                    }
                }
            }
            
        } catch (Exception $e) {
            echo "❌ get-program-modules API endpoint failed: " . $e->getMessage() . "\n";
        }
    }
    
    // Test 6: Test view file existence and structure
    echo "\n📄 Test 6: Testing view file existence and structure\n";
    
    $viewFiles = [
        'admin.admin-programs.admin-programs' => 'resources/views/admin/admin-programs/admin-programs.blade.php',
        'admin.admin-packages.admin-packages' => 'resources/views/admin/admin-packages/admin-packages.blade.php'
    ];
    
    foreach ($viewFiles as $viewName => $viewPath) {
        if (file_exists($viewPath)) {
            echo "✅ View file exists: $viewPath\n";
            
            // Check for key elements in the view
            $viewContent = file_get_contents($viewPath);
            
            if (strpos($viewContent, 'Add Program') !== false) {
                echo "✅ 'Add Program' button found in view\n";
            }
            
            if (strpos($viewContent, 'Add New Package') !== false) {
                echo "✅ 'Add New Package' button found in view\n";
            }
            
            if (strpos($viewContent, 'showAddModal') !== false) {
                echo "✅ showAddModal function found in view\n";
            }
            
            if (strpos($viewContent, 'admin-programs.js') !== false) {
                echo "✅ admin-programs.js script included in view\n";
            }
            
            if (strpos($viewContent, 'admin-packages.js') !== false) {
                echo "✅ admin-packages.js script included in view\n";
            }
            
        } else {
            echo "❌ View file missing: $viewPath\n";
        }
    }
    
    // Test 7: Test CSS and JS file existence
    echo "\n🎨 Test 7: Testing CSS and JS file existence\n";
    
    $assetFiles = [
        'public/css/admin/admin-programs/admin-programs.css',
        'public/js/admin/admin-programs.js',
        'public/js/admin/admin-packages.js'
    ];
    
    foreach ($assetFiles as $assetPath) {
        if (file_exists($assetPath)) {
            echo "✅ Asset file exists: $assetPath\n";
        } else {
            echo "❌ Asset file missing: $assetPath\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error in tenant web interface test: " . $e->getMessage() . "\n";
}

echo "\n=== TENANT WEB INTERFACE TEST COMPLETE ===\n";
