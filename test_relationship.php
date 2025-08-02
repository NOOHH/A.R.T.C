<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Professor-Program Relationship\n";
echo "=====================================\n";

$professor = \App\Models\Professor::find(8);

echo "1. Direct relationship test:\n";
try {
    $programs = $professor->assignedPrograms()->get();
    echo "✓ assignedPrograms() works: " . $programs->count() . " programs\n";
    foreach($programs as $program) {
        echo "- Program ID: " . $program->program_id . " | Name: " . $program->program_name . "\n";
    }
} catch (\Exception $e) {
    echo "✗ assignedPrograms() failed: " . $e->getMessage() . "\n";
}

echo "\n2. Testing specific where clause:\n";
try {
    $specificProgram = $professor->assignedPrograms()->where('program_id', 40)->first();
    if ($specificProgram) {
        echo "✓ Found program 40: " . $specificProgram->program_name . "\n";
    } else {
        echo "✗ Program 40 not found\n";
    }
} catch (\Exception $e) {
    echo "✗ Where clause failed: " . $e->getMessage() . "\n";
}

echo "\n3. Testing raw query approach:\n";
try {
    $rawResult = \Illuminate\Support\Facades\DB::table('professor_program')
        ->join('programs', 'professor_program.program_id', '=', 'programs.program_id')
        ->where('professor_program.professor_id', 8)
        ->where('programs.program_id', 40)
        ->first();
    
    if ($rawResult) {
        echo "✓ Raw query works: Found program " . $rawResult->program_name . "\n";
    } else {
        echo "✗ Raw query found no results\n";
    }
} catch (\Exception $e) {
    echo "✗ Raw query failed: " . $e->getMessage() . "\n";
}

echo "\n4. Check actual pivot table data:\n";
$pivotData = \Illuminate\Support\Facades\DB::table('professor_program')
    ->where('professor_id', 8)
    ->get();
    
echo "Pivot table entries for professor 8:\n";
foreach($pivotData as $entry) {
    echo "- Professor: " . $entry->professor_id . " | Program: " . $entry->program_id . "\n";
}

echo "\nAnalysis complete!\n";
