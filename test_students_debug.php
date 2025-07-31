<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Students Table ===\n";

try {
    // Get some students from the students table
    $students = DB::table('students')
        ->join('users', 'students.user_id', '=', 'users.user_id')
        ->select('students.student_id', 'users.user_firstname', 'users.user_lastname', 'users.user_id')
        ->limit(5)
        ->get();
    
    echo "Found " . $students->count() . " students:\n";
    foreach ($students as $student) {
        echo "- Student ID: " . $student->student_id . " | Name: " . $student->user_firstname . " " . $student->user_lastname . " | User ID: " . $student->user_id . "\n";
    }
    
    echo "\n=== Checking Payment Student IDs ===\n";
    $paymentStudents = DB::table('payments')
        ->select('student_id')
        ->distinct()
        ->limit(10)
        ->get();
    
    echo "Payment student IDs:\n";
    foreach ($paymentStudents as $payment) {
        echo "- " . $payment->student_id . "\n";
    }
    
    echo "\n=== Checking Users Table ===\n";
    $users = DB::table('users')
        ->select('user_id', 'user_firstname', 'user_lastname', 'role')
        ->where('role', 'student')
        ->limit(5)
        ->get();
    
    echo "Found " . $users->count() . " student users:\n";
    foreach ($users as $user) {
        echo "- User ID: " . $user->user_id . " | Name: " . $user->user_firstname . " " . $user->user_lastname . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
