<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Check users table
    echo "=== USERS TABLE ===\n";
    $users = DB::select("SELECT user_id, user_firstname, user_lastname, email, role FROM users LIMIT 10");
    foreach ($users as $user) {
        echo "ID: {$user->user_id}, Name: {$user->user_firstname} {$user->user_lastname}, Email: {$user->email}, Role: {$user->role}\n";
    }
    
    // Check students 
    echo "\n=== STUDENTS ===\n";
    $students = DB::select("SELECT student_id, firstname, lastname, email FROM students LIMIT 5");
    foreach ($students as $student) {
        echo "ID: {$student->student_id}, Name: {$student->firstname} {$student->lastname}, Email: {$student->email}\n";
    }
    
    echo "\n=== PROFESSORS ===\n";
    $professors = DB::select("SELECT professor_id, professor_first_name, professor_last_name, professor_email FROM professors LIMIT 5");
    foreach ($professors as $professor) {
        echo "ID: {$professor->professor_id}, Name: {$professor->professor_first_name} {$professor->professor_last_name}, Email: {$professor->professor_email}\n";
    }
    
    echo "\n=== ADMINS ===\n";
    $admins = DB::select("SELECT admin_id, admin_name, email FROM admins LIMIT 5");
    foreach ($admins as $admin) {
        echo "ID: {$admin->admin_id}, Name: {$admin->admin_name}, Email: {$admin->email}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
