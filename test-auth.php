<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Check specific user ID 1
    $user1 = DB::table('users')->where('user_id', 1)->first();
    if ($user1) {
        echo "User ID 1: Name: {$user1->user_firstname} {$user1->user_lastname}, Email: {$user1->email}, Role: {$user1->role}\n";
    } else {
        echo "No user with ID 1 in users table\n";
    }
    
    // Check admin ID 1
    $admin1 = DB::table('admins')->where('admin_id', 1)->first();
    if ($admin1) {
        echo "Admin ID 1: Name: {$admin1->admin_name}, Email: {$admin1->email}\n";
    }
    
    // Test database connection
    $users = DB::table('users')->select('user_id', 'user_firstname', 'user_lastname', 'email', 'role')->take(5)->get();
    echo "\nUsers table:\n";
    foreach($users as $user) {
        echo "ID: {$user->user_id}, Name: {$user->user_firstname} {$user->user_lastname}, Email: {$user->email}, Role: {$user->role}\n";
    }
    echo "Users table:\n";
    foreach($users as $user) {
        echo "ID: {$user->user_id}, Name: {$user->user_firstname} {$user->user_lastname}, Email: {$user->email}, Role: {$user->role}\n";
    }
    
    // Check admins table
    $admins = DB::table('admins')->select('admin_id', 'admin_name', 'email')->take(5)->get();
    echo "\nAdmins table:\n";
    foreach($admins as $admin) {
        echo "ID: {$admin->admin_id}, Name: {$admin->admin_name}, Email: {$admin->email}\n";
    }
    
    // Check students table
    $students = DB::table('students')->select('student_id', 'user_id', 'firstname', 'lastname', 'email')->take(5)->get();
    echo "\nStudents table:\n";
    foreach($students as $student) {
        echo "Student ID: {$student->student_id}, User ID: {$student->user_id}, Name: {$student->firstname} {$student->lastname}, Email: {$student->email}\n";
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
