<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Comprehensive Database Test ===\n\n";
    
    // Test all the queries from the admin dashboard
    echo "1. Testing Analytics Queries:\n";
    
    $totalStudents = DB::table('students')->count();
    echo "✓ Total Students: $totalStudents\n";
    
    $totalPrograms = DB::table('programs')->where('is_archived', false)->count();
    echo "✓ Total Programs: $totalPrograms\n";
    
    $totalModules = DB::table('modules')->where('is_archived', false)->count();
    echo "✓ Total Modules: $totalModules\n";
    
    $totalEnrollments = DB::table('enrollments')->count();
    echo "✓ Total Enrollments: $totalEnrollments\n";
    
    echo "\n2. Testing Director-related Queries:\n";
    
    // Test director lookup
    $director = DB::table('directors')->where('directors_id', 7)->first();
    if ($director) {
        echo "✓ Director found: " . $director->directors_name . "\n";
        echo "  - Email: " . $director->directors_email . "\n";
        echo "  - Archived: " . ($director->directors_archived ? 'Yes' : 'No') . "\n";
        echo "  - Has all program access: " . ($director->has_all_program_access ? 'Yes' : 'No') . "\n";
    }
    
    echo "\n3. Testing Registration Queries:\n";
    
    $pendingRegistrations = DB::table('registrations')->where('status', 'pending')->count();
    echo "✓ Pending Registrations: $pendingRegistrations\n";
    
    echo "\n4. Testing Admin Settings Queries:\n";
    
    $directorFeatures = [
        'director_view_students',
        'director_manage_programs', 
        'director_manage_modules',
        'director_manage_professors',
        'director_manage_batches',
        'director_view_analytics',
        'director_manage_enrollments'
    ];
    
    foreach ($directorFeatures as $feature) {
        $setting = DB::table('admin_settings')->where('setting_key', $feature)->first();
        $status = $setting ? $setting->setting_value : 'true';
        echo "✓ $feature: $status\n";
    }
    
    echo "\n5. Testing Chat Controller Queries:\n";
    
    // Test directors query for chat
    $chatDirectors = DB::table('directors')
        ->where('directors_archived', false)
        ->select('directors_id as id', 'directors_name as name', 'directors_email as email')
        ->limit(5)
        ->get();
        
    echo "✓ Chat Directors Query: Found " . $chatDirectors->count() . " directors\n";
    
    foreach ($chatDirectors as $dir) {
        echo "  - ID: {$dir->id}, Name: {$dir->name}, Email: {$dir->email}\n";
    }
    
    echo "\n=== All Database Tests Passed! ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
