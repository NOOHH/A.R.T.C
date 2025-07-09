<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Session;

echo "=== Testing Dashboard with Mock Session ===\n\n";

try {
    // Find a user that has pending enrollments
    $enrollment = Enrollment::where('enrollment_status', 'pending')->with('program')->first();
    
    if (!$enrollment) {
        echo "No pending enrollments found. Creating a test enrollment...\n";
        
        // Create a test user and enrollment
        $user = User::create([
            'user_name' => 'Test User',
            'user_email' => 'test@example.com',
            'user_password' => bcrypt('password'),
            'user_role' => 'Student'
        ]);
        
        $enrollment = Enrollment::create([
            'user_id' => $user->user_id,
            'program_id' => 1, // Assume program 1 exists
            'package_id' => 1, // Assume package 1 exists
            'enrollment_status' => 'pending',
            'payment_status' => 'pending',
            'enrollment_type' => 'full',
            'learning_mode' => 'synchronous'
        ]);
        
        echo "Created test user {$user->user_id} and enrollment {$enrollment->enrollment_id}\n";
    } else {
        echo "Found existing pending enrollment {$enrollment->enrollment_id} for user {$enrollment->user_id}\n";
        echo "Program: " . ($enrollment->program ? $enrollment->program->program_name : 'No program') . "\n";
    }
    
    // Now simulate accessing the dashboard
    echo "\nSimulating dashboard access for user {$enrollment->user_id}...\n";
    
    // Test the dashboard logic manually (without PHP sessions)
    $testUserId = $enrollment->user_id;
    $enrollments = collect();
    
    if ($testUserId) {
        $userEnrollments = Enrollment::where('user_id', $testUserId)
            ->with(['program', 'package', 'batch'])
            ->get();
        $enrollments = $enrollments->merge($userEnrollments);
        echo "Found " . $userEnrollments->count() . " enrollments by user_id\n";
        
        foreach ($userEnrollments as $e) {
            echo "  - Enrollment {$e->enrollment_id}: " . 
                 ($e->program ? $e->program->program_name : 'No program') . 
                 " (Status: {$e->enrollment_status})\n";
        }
    }
    
    echo "\nTotal enrollments found: " . $enrollments->count() . "\n";
    
    if ($enrollments->count() > 0) {
        echo "✓ Dashboard should show these enrollments\n";
    } else {
        echo "✗ Dashboard will show 'not enrolled' message\n";
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
