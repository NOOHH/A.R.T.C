<?php

// Test script to verify modular enrollment fields
require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Simulate the modular enrollment data
$testData = [
    'enrollment_type' => 'Modular',
    'package_id' => 12,
    'program_id' => 32,
    'selected_modules' => json_encode([
        ['id' => 22, 'name' => 'aaaaaaaaaaaaa'],
        ['id' => 26, 'name' => 'Module 1'],
        ['id' => 31, 'name' => 'yes']
    ]),
    'sync_async_mode' => 'async',
    'learning_mode' => 'asynchronous',
    'user_firstname' => 'Test',
    'user_lastname' => 'Modular',
    'email' => 'test.modular@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'education_level' => 'Undergraduate',
    'Start_Date' => '2025-08-01',
    'firstname' => 'Test',
    'lastname' => 'Modular',
    'middlename' => 'User',
    'contact_number' => '09123456789',
    'street_address' => '123 Test St',
    'city' => 'Test City',
    'state_province' => 'Test Province',
    'zipcode' => '12345'
];

echo "=== MODULAR ENROLLMENT SYSTEM TEST ===\n\n";

// Test 1: Check if required database columns exist
echo "1. Testing database structure...\n";
try {
    $registrationColumns = DB::select("SHOW COLUMNS FROM registrations LIKE 'Start_Date'");
    if (empty($registrationColumns)) {
        echo "❌ ERROR: Start_Date column not found in registrations table\n";
    } else {
        echo "✅ Start_Date column exists\n";
    }
    
    $educationColumns = DB::select("SHOW COLUMNS FROM registrations LIKE 'education_level'");
    if (empty($educationColumns)) {
        echo "❌ ERROR: education_level column not found in registrations table\n";
    } else {
        echo "✅ education_level column exists\n";
    }
    
    $syncAsyncColumns = DB::select("SHOW COLUMNS FROM registrations LIKE 'sync_async_mode'");
    if (empty($syncAsyncColumns)) {
        echo "❌ ERROR: sync_async_mode column not found in registrations table\n";
    } else {
        echo "✅ sync_async_mode column exists\n";
    }
    
    $packagesColumns = DB::select("SHOW COLUMNS FROM packages LIKE 'package_type'");
    if (empty($packagesColumns)) {
        echo "❌ ERROR: package_type column not found in packages table\n";
    } else {
        echo "✅ package_type column exists\n";
    }
    
    $moduleCountColumns = DB::select("SHOW COLUMNS FROM packages LIKE 'module_count'");
    if (empty($moduleCountColumns)) {
        echo "❌ ERROR: module_count column not found in packages table\n";
    } else {
        echo "✅ module_count column exists\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: Database test failed: " . $e->getMessage() . "\n";
}

// Test 2: Check packages data
echo "\n2. Testing packages data...\n";
try {
    $packages = DB::select("SELECT package_id, package_name, package_type, module_count, program_id FROM packages WHERE package_type = 'modular'");
    if (empty($packages)) {
        echo "❌ ERROR: No modular packages found\n";
    } else {
        echo "✅ Found " . count($packages) . " modular packages:\n";
        foreach ($packages as $package) {
            echo "   - Package {$package->package_id}: {$package->package_name} ({$package->module_count} modules, Program ID: {$package->program_id})\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERROR: Package test failed: " . $e->getMessage() . "\n";
}

// Test 3: Check modules data
echo "\n3. Testing modules data...\n";
try {
    $modules = DB::select("SELECT modules_id, module_name, program_id FROM modules WHERE program_id = 32 AND is_archived = 0");
    if (empty($modules)) {
        echo "❌ ERROR: No modules found for program ID 32\n";
    } else {
        echo "✅ Found " . count($modules) . " modules for program ID 32:\n";
        foreach ($modules as $module) {
            echo "   - Module {$module->modules_id}: {$module->module_name}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERROR: Module test failed: " . $e->getMessage() . "\n";
}

// Test 4: Test form requirements
echo "\n4. Testing form requirements...\n";
try {
    $formRequirements = DB::select("SELECT field_name, field_type, is_active FROM form_requirements WHERE is_active = 1");
    if (empty($formRequirements)) {
        echo "❌ ERROR: No active form requirements found\n";
    } else {
        echo "✅ Found " . count($formRequirements) . " active form requirements:\n";
        foreach ($formRequirements as $requirement) {
            echo "   - {$requirement->field_name} ({$requirement->field_type})\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERROR: Form requirements test failed: " . $e->getMessage() . "\n";
}

// Test 5: Simulate registration data validation
echo "\n5. Testing registration data validation...\n";
$validationPassed = true;

// Check required fields
$requiredFields = ['enrollment_type', 'package_id', 'program_id', 'selected_modules', 'sync_async_mode', 'learning_mode', 'user_firstname', 'user_lastname', 'email', 'password', 'education_level', 'Start_Date'];

foreach ($requiredFields as $field) {
    if (!isset($testData[$field]) || empty($testData[$field])) {
        echo "❌ ERROR: Required field '{$field}' is missing\n";
        $validationPassed = false;
    }
}

if ($validationPassed) {
    echo "✅ All required fields are present\n";
}

// Test 6: Test JSON validation for selected_modules
echo "\n6. Testing selected modules JSON...\n";
try {
    $selectedModules = json_decode($testData['selected_modules'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ ERROR: Invalid JSON for selected_modules\n";
    } else {
        echo "✅ Selected modules JSON is valid: " . count($selectedModules) . " modules selected\n";
        foreach ($selectedModules as $module) {
            echo "   - Module {$module['id']}: {$module['name']}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERROR: JSON test failed: " . $e->getMessage() . "\n";
}

// Test 7: Test data insertion (dry run)
echo "\n7. Testing data insertion (dry run)...\n";
try {
    // Simulate the registration creation without actually inserting
    $registrationData = [
        'user_id' => 999999, // Fake user ID
        'package_id' => $testData['package_id'],
        'program_id' => $testData['program_id'],
        'enrollment_type' => $testData['enrollment_type'],
        'learning_mode' => $testData['learning_mode'],
        'selected_modules' => $testData['selected_modules'],
        'Start_Date' => $testData['Start_Date'],
        'education_level' => $testData['education_level'],
        'sync_async_mode' => $testData['sync_async_mode'],
        'firstname' => $testData['firstname'],
        'lastname' => $testData['lastname'],
        'middlename' => $testData['middlename'],
        'status' => 'pending'
    ];
    
    echo "✅ Registration data prepared:\n";
    foreach ($registrationData as $key => $value) {
        echo "   - {$key}: {$value}\n";
    }
    
    echo "Note: This is a dry run - no actual data was inserted\n";
} catch (Exception $e) {
    echo "❌ ERROR: Data insertion test failed: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "Summary:\n";
echo "- Database structure: Check above for individual column tests\n";
echo "- Package data: Check above for modular packages\n";
echo "- Module data: Check above for program modules\n";
echo "- Form requirements: Check above for active requirements\n";
echo "- Data validation: Check above for required fields\n";
echo "- JSON validation: Check above for selected modules\n";
echo "- Data insertion: Dry run completed successfully\n";
echo "\nNext steps:\n";
echo "1. Test the actual form submission via browser\n";
echo "2. Check the stepper functionality\n";
echo "3. Verify module selection limits\n";
echo "4. Test sync/async mode selection\n";
echo "5. Test the complete registration flow\n";
