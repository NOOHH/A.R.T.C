<?php

// Final verification test for enrollment system
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Student;
use App\Models\Registration;
use App\Models\Enrollment;

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "🔍 Final Enrollment Verification Test\n";
echo "=====================================\n\n";

// Get the latest user
$latestUser = User::latest('user_id')->first();
if ($latestUser) {
    echo "Testing user: {$latestUser->user_firstname} {$latestUser->user_lastname} (ID: {$latestUser->user_id})\n";
    echo "User enrollment_id: " . ($latestUser->enrollment_id ?? 'NULL') . "\n\n";
    
    // Test registration relationship
    $registration = $latestUser->registration;
    if ($registration) {
        echo "✅ Registration found: ID {$registration->registration_id}\n";
        echo "   - Package: {$registration->package_name}\n";
        echo "   - Learning Mode: {$registration->learning_mode}\n";
        echo "   - Enrollment Type: {$registration->enrollment_type}\n";
    } else {
        echo "❌ No registration found via relationship\n";
    }
    
    // Test enrollment relationship
    $enrollment = $latestUser->enrollment;
    if ($enrollment) {
        echo "✅ Enrollment found: ID {$enrollment->enrollment_id}\n";
        echo "   - Status: {$enrollment->enrollment_status}\n";
        echo "   - Payment: {$enrollment->payment_status}\n";
        echo "   - Type: {$enrollment->enrollment_type}\n";
    } else {
        echo "❌ No enrollment found via relationship\n";
        
        // Try to find enrollment directly
        $directEnrollment = Enrollment::where('user_id', $latestUser->user_id)->first();
        if ($directEnrollment) {
            echo "⚠️  Enrollment exists in database but relationship not working\n";
            echo "   - Direct query found enrollment ID: {$directEnrollment->enrollment_id}\n";
            echo "   - User enrollment_id field: " . ($latestUser->enrollment_id ?? 'NULL') . "\n";
        }
    }
    
    // Test student relationship (if exists)
    $student = Student::where('user_id', $latestUser->user_id)->first();
    if ($student) {
        echo "✅ Student record found: {$student->student_id}\n";
        echo "   - Name: {$student->firstname} {$student->lastname}\n";
        echo "   - Email: {$student->email}\n";
    } else {
        echo "❌ No student record found\n";
    }
}

// Summary of database state
echo "\n📊 Final Database Summary:\n";
echo "========================\n";
echo "Total Users: " . User::count() . "\n";
echo "Total Students: " . Student::count() . "\n";
echo "Total Registrations: " . Registration::count() . "\n";
echo "Total Enrollments: " . Enrollment::count() . "\n\n";

// Check for any orphaned records
$usersWithoutRegistration = User::whereDoesntHave('registration')->count();
$usersWithoutEnrollment = User::whereNull('enrollment_id')->count();

echo "Users without registration: {$usersWithoutRegistration}\n";
echo "Users without enrollment_id: {$usersWithoutEnrollment}\n\n";

echo "✅ Verification complete!\n";
echo "=====================================\n";
