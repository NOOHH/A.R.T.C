<?php

// Comprehensive enrollment database testing script
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Student;
use App\Models\Registration;
use App\Models\Enrollment;
use App\Models\Package;
use App\Models\Program;
use App\Models\Module;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "🧪 Starting Enrollment Database Tests\n";
echo "=====================================\n\n";

// Get current record counts
echo "📊 Current Database State:\n";
echo "Users: " . User::count() . "\n";
echo "Students: " . Student::count() . "\n";  
echo "Registrations: " . Registration::count() . "\n";
echo "Enrollments: " . Enrollment::count() . "\n";
echo "\n";

// Test 1: Check available packages and modules
echo "🔍 Test 1: Checking Available Data\n";
echo "==================================\n";

$packages = Package::where('package_type', 'modular')->get();
echo "Available modular packages: " . $packages->count() . "\n";
foreach ($packages as $package) {
    echo "  - Package ID: {$package->package_id}, Name: {$package->package_name}, Program ID: {$package->program_id}\n";
}

$programs = Program::where('is_archived', false)->get();
echo "Available programs: " . $programs->count() . "\n";
foreach ($programs as $program) {
    echo "  - Program ID: {$program->program_id}, Name: {$program->program_name}\n";
}

$modules = Module::where('is_archived', false)->take(5)->get();
echo "Available modules (first 5): " . $modules->count() . "\n";
foreach ($modules as $module) {
    echo "  - Module ID: {$module->modules_id}, Name: {$module->module_name}, Program ID: {$module->program_id}\n";
}

echo "\n";

// Test 2: Manual Modular Enrollment Creation
echo "🧪 Test 2: Manual Modular Enrollment Creation\n";
echo "============================================\n";

try {
    DB::beginTransaction();
    
    // Get first available package
    $package = $packages->first();
    if (!$package) {
        throw new Exception("No modular packages available for testing");
    }
    
    echo "Using Package: {$package->package_name} (ID: {$package->package_id})\n";
    
    // Create test user
    $testEmail = 'test_modular_' . time() . '@example.com';
    echo "Creating test user with email: {$testEmail}\n";
    
    $user = User::create([
        'user_firstname' => 'Test',
        'user_lastname' => 'Modular',
        'email' => $testEmail,
        'password' => Hash::make('password123'),
        'role' => 'student',
        'admin_id' => 1,
        'directors_id' => 1
    ]);
    
    echo "✅ User created with ID: {$user->user_id}\n";
    
    // Create student record
    $student = Student::create([
        'student_firstname' => 'Test',  // This field doesn't exist
        'student_lastname' => 'Modular', // This field doesn't exist  
        'student_email' => $testEmail,   // This field doesn't exist
        'firstname' => 'Test',           // Correct field name
        'lastname' => 'Modular',         // Correct field name
        'email' => $testEmail,           // Correct field name
        'user_id' => $user->user_id,
        'student_id' => 'TEST' . str_pad($user->user_id, 6, '0', STR_PAD_LEFT),
        'education_level' => 'Undergraduate' // Required field
    ]);
    
    echo "✅ Student created with ID: {$student->student_id}\n";
    
    // Get some modules for the test
    $selectedModules = Module::where('program_id', $package->program_id)
                           ->where('is_archived', false)
                           ->take(2)
                           ->pluck('modules_id')
                           ->toArray();
                           
    if (empty($selectedModules)) {
        // Fallback to any modules if none in the package's program
        $selectedModules = Module::where('is_archived', false)->take(2)->pluck('modules_id')->toArray();
    }
    
    echo "Selected modules: " . implode(', ', $selectedModules) . "\n";
    
    // Create registration
    $registration = Registration::create([
        'user_id' => $user->user_id,
        'firstname' => 'Test',
        'lastname' => 'Modular',
        'program_id' => $package->program_id,
        'package_id' => $package->package_id,
        'program_name' => $package->program->program_name ?? 'Test Program',
        'package_name' => $package->package_name,
        'learning_mode' => 'Asynchronous',
        'enrollment_type' => 'Modular',
        'selected_modules' => json_encode($selectedModules),
        'status' => 'pending'
    ]);
    
    echo "✅ Registration created with ID: {$registration->registration_id}\n";
    
        // Create enrollment
        $enrollment = Enrollment::create([
            'user_id' => $user->user_id,
            'student_id' => $student->student_id,
            'program_id' => $package->program_id,
            'package_id' => $package->package_id,
            'learning_mode' => 'Asynchronous', // Must be 'Synchronous' or 'Asynchronous'
            'enrollment_type' => 'Modular',
            'enrollment_status' => 'pending',
            'payment_status' => 'pending',
            'Modular_enrollment' => json_encode($selectedModules)
        ]);    echo "✅ Enrollment created with ID: {$enrollment->enrollment_id}\n";
    
    // Update user with enrollment_id
    $user->enrollment_id = $enrollment->enrollment_id;
    $user->save();
    echo "✅ User updated with enrollment_id: {$enrollment->enrollment_id}\n";
    
    DB::commit();
    echo "✅ Modular enrollment test completed successfully!\n\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Modular enrollment test failed: " . $e->getMessage() . "\n\n";
}

// Test 3: Manual Full Enrollment Creation  
echo "🧪 Test 3: Manual Full Enrollment Creation\n";
echo "=========================================\n";

try {
    DB::beginTransaction();
    
    // Get a full package
    $fullPackage = Package::where('package_type', 'full')->first();
    if (!$fullPackage) {
        echo "⚠️  No full packages available, skipping full enrollment test\n\n";
    } else {
        echo "Using Package: {$fullPackage->package_name} (ID: {$fullPackage->package_id})\n";
        
        // Create test user
        $testEmail = 'test_full_' . time() . '@example.com';
        echo "Creating test user with email: {$testEmail}\n";
        
        $user = User::create([
            'user_firstname' => 'Test',
            'user_lastname' => 'Full',
            'email' => $testEmail,
            'password' => Hash::make('password123'),
            'role' => 'student',
            'admin_id' => 1,
            'directors_id' => 1
        ]);
        
        echo "✅ User created with ID: {$user->user_id}\n";
        
        // Create student record
        $student = Student::create([
            'student_firstname' => 'Test',  // This field doesn't exist
            'student_lastname' => 'Full',   // This field doesn't exist
            'student_email' => $testEmail,  // This field doesn't exist
            'firstname' => 'Test',          // Correct field name
            'lastname' => 'Full',           // Correct field name
            'email' => $testEmail,          // Correct field name
            'user_id' => $user->user_id,
            'student_id' => 'FULL' . str_pad($user->user_id, 6, '0', STR_PAD_LEFT),
            'education_level' => 'Graduate' // Required field
        ]);
        
        echo "✅ Student created with ID: {$student->student_id}\n";
        
        // Create registration
        $registration = Registration::create([
            'user_id' => $user->user_id,
            'firstname' => 'Test',
            'lastname' => 'Full',
            'program_id' => $fullPackage->program_id,
            'package_id' => $fullPackage->package_id,
            'program_name' => $fullPackage->program->program_name ?? 'Test Program',
            'package_name' => $fullPackage->package_name,
                            'learning_mode' => 'Synchronous',
            'enrollment_type' => 'Full',
            'status' => 'pending'
        ]);
        
        echo "✅ Registration created with ID: {$registration->registration_id}\n";
        
        // Create enrollment
        $enrollment = Enrollment::create([
            'user_id' => $user->user_id,
            'student_id' => $student->student_id,
            'program_id' => $fullPackage->program_id,
            'package_id' => $fullPackage->package_id,
            'learning_mode' => 'Synchronous', // Must be 'Synchronous' or 'Asynchronous'
            'enrollment_type' => 'Full',
            'enrollment_status' => 'pending',
            'payment_status' => 'pending'
        ]);
        
        echo "✅ Enrollment created with ID: {$enrollment->enrollment_id}\n";
        
        // Update user with enrollment_id
        $user->enrollment_id = $enrollment->enrollment_id;
        $user->save();
        echo "✅ User updated with enrollment_id: {$enrollment->enrollment_id}\n";
        
        DB::commit();
        echo "✅ Full enrollment test completed successfully!\n\n";
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Full enrollment test failed: " . $e->getMessage() . "\n\n";
}

// Test 4: Check final database state
echo "📊 Final Database State:\n";
echo "========================\n";
echo "Users: " . User::count() . "\n";
echo "Students: " . Student::count() . "\n";
echo "Registrations: " . Registration::count() . "\n";
echo "Enrollments: " . Enrollment::count() . "\n";
echo "\n";

// Test 5: Verify relationships work
echo "🔗 Test 5: Verifying Relationships\n";
echo "==================================\n";

$latestUser = User::latest('user_id')->first();
if ($latestUser) {
    echo "Latest user: {$latestUser->user_firstname} {$latestUser->user_lastname} (ID: {$latestUser->user_id})\n";
    
    $userRegistration = $latestUser->registration;
    if ($userRegistration) {
        echo "✅ User has registration: {$userRegistration->registration_id}\n";
    } else {
        echo "❌ User has no registration relationship\n";
    }
    
    $userEnrollment = $latestUser->enrollment;
    if ($userEnrollment) {
        echo "✅ User has enrollment: {$userEnrollment->enrollment_id}\n";
    } else {
        echo "❌ User has no enrollment relationship\n";
    }
}

echo "\n✅ All database tests completed!\n";
echo "=====================================\n";
