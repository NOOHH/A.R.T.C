<?php
require_once __DIR__ . '/vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Check database tables
    $registrationsCount = \Illuminate\Support\Facades\DB::table('registrations')->count();
    $enrollmentsCount = \Illuminate\Support\Facades\DB::table('enrollments')->count();
    $studentsCount = \Illuminate\Support\Facades\DB::table('students')->count();
    
    echo "Database Table Counts:\n";
    echo "Registrations: $registrationsCount\n";
    echo "Enrollments: $enrollmentsCount\n";
    echo "Students: $studentsCount\n";
    
    // Try to check recent registrations
    echo "\nRecent Registration IDs from logs:\n";
    $recentEnrollments = \Illuminate\Support\Facades\DB::table('enrollments')
        ->select(['id', 'registration_id', 'user_id', 'created_at'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($recentEnrollments as $enrollment) {
        echo "Enrollment ID: {$enrollment->id}, Registration ID: {$enrollment->registration_id}, User ID: {$enrollment->user_id}, Created: {$enrollment->created_at}\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
