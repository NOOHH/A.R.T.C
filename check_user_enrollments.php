<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== Checking User Enrollments ===\n";

// Find the user bryan justimbaste
$users = \App\Models\User::where('user_firstname', 'bryan')
    ->where('user_lastname', 'justimbaste')
    ->get();

if ($users->count() === 0) {
    echo "No users found with name 'bryan justimbaste'\n";
    // Let's check all users with similar names
    $similarUsers = \App\Models\User::where('user_firstname', 'LIKE', '%bryan%')
        ->orWhere('user_lastname', 'LIKE', '%justimbaste%')
        ->get();
    
    echo "\nSimilar users found:\n";
    foreach($similarUsers as $user) {
        echo "  - {$user->user_firstname} {$user->user_lastname} (ID: {$user->user_id})\n";
    }
}

foreach($users as $user) {
    echo "Found user: {$user->user_firstname} {$user->user_lastname} (ID: {$user->user_id})\n";
    
    // Check enrollments
    $enrollments = \App\Models\Enrollment::where('user_id', $user->user_id)->get();
    echo "Total enrollments: " . $enrollments->count() . "\n";
    
    foreach($enrollments as $enrollment) {
        $program = \App\Models\Program::find($enrollment->program_id);
        echo "  - Program: {$program->program_name} | Status: {$enrollment->enrollment_status} | Payment: {$enrollment->payment_status}\n";
    }
    
    // Check students table
    $student = \App\Models\Student::where('user_id', $user->user_id)->first();
    if($student) {
        echo "Student record found: {$student->firstname} {$student->lastname} (Program ID: {$student->program_id})\n";
    } else {
        echo "No student record found\n";
    }
}

// Also check what programs are currently being shown in the enrollment form
echo "\n=== Programs Available for Enrollment ===\n";
$programs = \App\Models\Program::where('is_archived', false)->get();
foreach($programs as $program) {
    echo "- {$program->program_name} (ID: {$program->program_id})\n";
}
?>
