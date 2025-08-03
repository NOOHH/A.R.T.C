<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Director;
use App\Models\User;
use App\Http\Controllers\UnifiedLoginController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== Director Creation Fix Test ===\n";

try {
    // Test 1: Check current users table enum structure
    echo "\n1. Checking users table structure...\n";
    $columns = DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'");
    if (!empty($columns)) {
        echo "Current role enum: " . $columns[0]->Type . "\n";
    }
    
    // Test 2: Check directors table structure
    echo "\n2. Checking directors table structure...\n";
    $directorColumns = DB::select("DESCRIBE directors");
    echo "Directors table columns:\n";
    foreach ($directorColumns as $column) {
        echo "- {$column->Field}: {$column->Type}\n";
    }
    
    // Test 3: Try to create a director directly (this should work)
    echo "\n3. Testing direct director creation...\n";
    $testEmail = 'test_director_' . time() . '@test.com';
    $director = new Director();
    $director->directors_name = 'Test Director';
    $director->directors_first_name = 'Test';
    $director->directors_last_name = 'Director';
    $director->directors_email = $testEmail;
    $director->directors_password = Hash::make('password123');
    $director->admin_id = 1;
    $director->save();
    
    echo "✓ Director created successfully with ID: " . $director->directors_id . "\n";
    
    // Test 4: Try the problematic syncToUsersTable method
    echo "\n4. Testing problematic syncToUsersTable method...\n";
    try {
        $user = UnifiedLoginController::syncToUsersTable(
            $testEmail,
            'Test Director',
            'director',
            'password123',
            $director->directors_id
        );
        echo "✗ This should have failed but didn't!\n";
    } catch (Exception $e) {
        echo "✓ Expected error occurred: " . $e->getMessage() . "\n";
        echo "Error details: " . get_class($e) . "\n";
    }
    
    // Test 5: Check if we can create a user with valid roles
    echo "\n5. Testing user creation with valid roles...\n";
    $validRoles = ['unverified', 'student', 'professor'];
    foreach ($validRoles as $role) {
        try {
            $testUserEmail = "test_{$role}_" . time() . '@test.com';
            $user = User::create([
                'email' => $testUserEmail,
                'user_firstname' => "Test {$role}",
                'user_lastname' => 'User',
                'password' => Hash::make('password123'),
                'role' => $role,
                'admin_id' => 1,
                'directors_id' => null
            ]);
            echo "✓ User with role '{$role}' created successfully\n";
            
            // Clean up
            $user->delete();
        } catch (Exception $e) {
            echo "✗ Failed to create user with role '{$role}': " . $e->getMessage() . "\n";
        }
    }
    
    // Clean up test director
    echo "\n6. Cleaning up test data...\n";
    $director->delete();
    echo "✓ Test director deleted\n";
    
    echo "\n=== Test Analysis ===\n";
    echo "The issue is that the syncToUsersTable method tries to create users with role 'director',\n";
    echo "but the users table enum only allows: unverified, student, professor\n";
    echo "Directors should NOT be synced to the users table since they have their own table.\n";
    
    echo "\n=== Recommended Fix ===\n";
    echo "1. Modify syncToUsersTable to skip directors\n";
    echo "2. Or update the enum to include director (but this defeats the purpose of separate tables)\n";
    echo "3. Remove the syncToUsersTable call for directors in AdminDirectorController\n";
    
} catch (Exception $e) {
    echo "Error during test: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
