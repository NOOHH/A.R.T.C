<?php
// Test script to verify director dashboard queries work
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing Director Dashboard Queries...\n\n";

try {
    // Test all the queries from DirectorController
    $totalStudents = DB::table('students')->count();
    echo "✓ Total students: $totalStudents\n";
    
    $totalProfessors = DB::table('professors')->count();
    echo "✓ Total professors: $totalProfessors\n";
    
    $totalPrograms = DB::table('programs')->count();
    echo "✓ Total programs: $totalPrograms\n";
    
    $totalBatches = DB::table('student_batches')->count();
    echo "✓ Total batches: $totalBatches\n";
    
    $pendingRegistrations = DB::table('users')->where('role', 'pending')->count();
    echo "✓ Pending registrations: $pendingRegistrations\n";
    
    $activePrograms = DB::table('programs')->where('is_archived', false)->count();
    echo "✓ Active programs: $activePrograms\n";
    
    // Test recent registrations
    $recentRegistrations = DB::table('students')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    echo "✓ Recent registrations: " . count($recentRegistrations) . " found\n";
    
    // Test accessible programs
    $accessiblePrograms = DB::table('programs')
        ->where('is_archived', false)
        ->orderBy('program_name')
        ->get();
    echo "✓ Accessible programs: " . count($accessiblePrograms) . " found\n";
    
    echo "\n✅ All queries executed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
