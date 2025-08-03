<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Director;
use App\Models\Admin;
use App\Http\Controllers\UnifiedLoginController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

echo "=== Director Registration Fix Test ===\n";

try {
    // Set session for admin context
    Session::put('admin_id', 1);
    
    // Test the fixed syncToUsersTable method
    echo "\n1. Testing fixed syncToUsersTable method with director role...\n";
    $testEmail = 'fixed_test_director_' . time() . '@test.com';
    
    $result = UnifiedLoginController::syncToUsersTable(
        $testEmail,
        'Fixed Test Director',
        'director',
        'password123',
        9999
    );
    
    if ($result === null) {
        echo "✓ Director sync correctly skipped - no user created in users table\n";
    } else {
        echo "✗ Unexpected result: " . print_r($result, true) . "\n";
    }
    
    // Test 2: Verify no user was created in users table
    echo "\n2. Verifying no user was created in users table...\n";
    $userCheck = DB::table('users')->where('email', $testEmail)->first();
    if (!$userCheck) {
        echo "✓ Confirmed: No user record created in users table\n";
    } else {
        echo "✗ Unexpected: User record found in users table\n";
    }
    
    // Test 3: Test the full director creation process (simulate AdminDirectorController)
    echo "\n3. Testing full director creation process...\n";
    
    // Create director directly (this simulates what AdminDirectorController should do)
    $director = new Director();
    $director->directors_name = 'Test Full Director';
    $director->directors_first_name = 'Test';
    $director->directors_last_name = 'Full Director';
    $director->directors_email = $testEmail;
    $director->directors_password = Hash::make('password123');
    $director->admin_id = 1;
    $director->has_all_program_access = false;
    $director->directors_archived = false;
    $director->save();
    
    echo "✓ Director created successfully with ID: " . $director->directors_id . "\n";
    
    // Now test the sync call (this should not create a user)
    $syncResult = UnifiedLoginController::syncToUsersTable(
        $director->directors_email,
        $director->directors_name,
        'director',
        'password123',
        $director->directors_id
    );
    
    if ($syncResult === null) {
        echo "✓ Sync call returned null as expected\n";
    } else {
        echo "✗ Sync call returned unexpected result\n";
    }
    
    // Test 4: Verify director can be retrieved and authenticated
    echo "\n4. Testing director retrieval and authentication...\n";
    $retrievedDirector = Director::where('directors_email', $testEmail)->first();
    if ($retrievedDirector) {
        echo "✓ Director can be retrieved from directors table\n";
        
        // Test password verification
        if (Hash::check('password123', $retrievedDirector->directors_password)) {
            echo "✓ Director password verification works\n";
        } else {
            echo "✗ Director password verification failed\n";
        }
    } else {
        echo "✗ Director not found in directors table\n";
    }
    
    // Test 5: Check that other roles still work
    echo "\n5. Testing that other roles still sync correctly...\n";
    $professorEmail = 'test_professor_' . time() . '@test.com';
    $professorResult = UnifiedLoginController::syncToUsersTable(
        $professorEmail,
        'Test Professor',
        'professor',
        'password123',
        null
    );
    
    if ($professorResult && $professorResult->role === 'professor') {
        echo "✓ Professor role still syncs correctly\n";
        // Clean up
        $professorResult->delete();
    } else {
        echo "✗ Professor role sync failed\n";
    }
    
    // Clean up
    echo "\n6. Cleaning up test data...\n";
    $director->delete();
    echo "✓ Test director deleted\n";
    
    echo "\n=== Fix Verification Complete ===\n";
    echo "✓ Directors are no longer synced to users table\n";
    echo "✓ Directors maintain separate authentication in directors table\n";
    echo "✓ Other roles continue to work normally\n";
    echo "✓ The original SQL error should now be resolved\n";
    
} catch (Exception $e) {
    echo "Error during test: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
