#!/usr/bin/env php
<?php

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Now we can use Laravel features
use App\Models\Program;
use App\Models\Module;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

echo "=== COMPREHENSIVE DATABASE SEARCH DEBUG ===\n\n";

echo "1. CHECKING PROGRAMS TABLE:\n";
echo "----------------------------\n";
$programs = Program::all();
echo "Total programs found: " . $programs->count() . "\n\n";

foreach ($programs as $program) {
    echo "Program ID: {$program->program_id}\n";
    echo "Name: {$program->program_name}\n";
    echo "Description: " . ($program->program_description ?? 'NULL') . "\n";
    echo "Is Active: " . ($program->is_active ?? 'NULL') . "\n";
    echo "Is Archived: " . ($program->is_archived ?? 'NULL') . "\n";
    echo "Created: " . ($program->created_at ?? 'NULL') . "\n";
    echo "---\n";
}

echo "\n2. SEARCHING FOR SPECIFIC PROGRAMS:\n";
echo "-----------------------------------\n";

// Check for 'nursing'
$nursingPrograms = Program::where('program_name', 'like', '%nursing%')
    ->orWhere('program_description', 'like', '%nursing%')
    ->get();

echo "Programs matching 'nursing': " . $nursingPrograms->count() . "\n";
foreach ($nursingPrograms as $program) {
    echo "Found: ID={$program->program_id}, Name='{$program->program_name}', Active=" . 
         ($program->is_active ?? 'NULL') . ", Archived=" . ($program->is_archived ?? 'NULL') . "\n";
}

// Check for 'civil'
echo "\nPrograms matching 'civil':\n";
$civilPrograms = Program::where('program_name', 'like', '%civil%')
    ->orWhere('program_description', 'like', '%civil%')
    ->get();

echo "Programs matching 'civil': " . $civilPrograms->count() . "\n";
foreach ($civilPrograms as $program) {
    echo "Found: ID={$program->program_id}, Name='{$program->program_name}', Active=" . 
         ($program->is_active ?? 'NULL') . ", Archived=" . ($program->is_archived ?? 'NULL') . "\n";
}

// Check for 'engineer'
echo "\nPrograms matching 'engineer':\n";
$engineerPrograms = Program::where('program_name', 'like', '%engineer%')
    ->orWhere('program_description', 'like', '%engineer%')
    ->get();

echo "Programs matching 'engineer': " . $engineerPrograms->count() . "\n";
foreach ($engineerPrograms as $program) {
    echo "Found: ID={$program->program_id}, Name='{$program->program_name}', Active=" . 
         ($program->is_active ?? 'NULL') . ", Archived=" . ($program->is_archived ?? 'NULL') . "\n";
}

echo "\n3. TESTING SEARCH CONDITIONS:\n";
echo "-----------------------------\n";

echo "a) Programs where is_archived = false:\n";
$notArchived = Program::where('is_archived', false)->get();
echo "   Count: " . $notArchived->count() . "\n";
foreach ($notArchived as $prog) {
    echo "   - {$prog->program_name} (ID: {$prog->program_id})\n";
}

echo "\nb) Programs where is_active = true:\n";
$active = Program::where('is_active', true)->get();
echo "   Count: " . $active->count() . "\n";
foreach ($active as $prog) {
    echo "   - {$prog->program_name} (ID: {$prog->program_id})\n";
}

echo "\nc) Programs with BOTH conditions:\n";
$both = Program::where('is_archived', false)
    ->where('is_active', true)
    ->get();
echo "   Count: " . $both->count() . "\n";
foreach ($both as $prog) {
    echo "   - {$prog->program_name} (ID: {$prog->program_id})\n";
}

echo "\nd) Nursing programs with conditions:\n";
$nursingWithConditions = Program::where(function($query) {
        $query->where('program_name', 'like', '%nursing%')
              ->orWhere('program_description', 'like', '%nursing%');
    })
    ->where('is_archived', false)
    ->where('is_active', true)
    ->get();

echo "   Count: " . $nursingWithConditions->count() . "\n";
foreach ($nursingWithConditions as $program) {
    echo "   - {$program->program_name} (ID: {$program->program_id})\n";
}

echo "\n4. CHECKING USER 180 (Your Student):\n";
echo "------------------------------------\n";
$user = User::find(180);
if ($user) {
    echo "User found: {$user->user_firstname} {$user->user_lastname}\n";
    echo "Role: {$user->role}\n";
    
    $student = Student::where('user_id', 180)->first();
    if ($student) {
        echo "Student record found: student_id = {$student->student_id}\n";
        
        $enrollments = $student->enrollments;
        echo "Enrollments count: " . $enrollments->count() . "\n";
        foreach ($enrollments as $enrollment) {
            $program = $enrollment->program;
            echo "  - Enrolled in: " . ($program ? $program->program_name : 'Unknown') . " (ID: {$enrollment->program_id})\n";
        }
    } else {
        echo "No student record found for user_id 180\n";
    }
} else {
    echo "User not found with ID 180\n";
}

echo "\n5. TESTING EXACT SEARCH QUERY:\n";
echo "------------------------------\n";
$searchQuery = 'nursing';

// Test the exact logic from searchAllProgramsForStudent
$searchResults = Program::where(function($programQuery) use ($searchQuery) {
        $programQuery->where('program_name', 'like', "%{$searchQuery}%")
            ->orWhere('program_description', 'like', "%{$searchQuery}%");
    })
    ->where('is_archived', false)
    ->where('is_active', true)
    ->with(['modules.courses', 'professors'])
    ->get();

echo "Search results for '{$searchQuery}': " . $searchResults->count() . "\n";
foreach ($searchResults as $result) {
    echo "  - {$result->program_name} (ID: {$result->program_id})\n";
    echo "    Modules: " . $result->modules->count() . "\n";
    echo "    Courses: " . $result->modules->sum(function($module) { return $module->courses->count(); }) . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
