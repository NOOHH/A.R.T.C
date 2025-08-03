<?php

// Simple test script to test password reset email functionality
echo "Testing Password Reset Email Functionality\n";
echo "==========================================\n\n";

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

try {
    echo "1. Testing if email exists in database...\n";
    
    // Check if email exists in any user table
    $user = null;
    $userType = null;
    
    $email = 'vince03handsome11@gmail.com';
    
    // Check students table
    $student = Student::where('email', $email)->first();
    if ($student) {
        $user = $student;
        $userType = 'student';
        echo "   ✓ Found user in students table\n";
    } else {
        echo "   ✗ User not found in students table\n";
        echo "   Note: You may need to register this email in the system first\n";
        
        // For testing purposes, let's create a test student record
        echo "\n2. Creating test student record...\n";
        $user = Student::create([
            'email' => $email,
            'firstname' => 'Test',
            'lastname' => 'User',
            'password' => bcrypt('temporary123'),
            'student_id' => 'TEST' . time()
        ]);
        $userType = 'student';
        echo "   ✓ Test student created\n";
    }
    
    echo "\n3. Generating password reset token...\n";
    $token = Str::random(60);
    
    // Store token in session (simplified for testing)
    echo "   ✓ Token generated: " . substr($token, 0, 10) . "...\n";
    
    echo "\n4. Sending password reset email...\n";
    
    // Send the email
    Mail::to($email)->send(new PasswordResetMail($user, $token));
    
    echo "   ✓ Password reset email sent successfully!\n";
    echo "   ✓ Check your email inbox for the password reset link\n";
    
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n==========================================\n";
echo "Test completed. Check your email!\n";
?>
