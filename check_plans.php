<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Program;
use Illuminate\Support\Facades\Schema;

echo "=== PROGRAMS TABLE COLUMNS ===\n";
$columns = Schema::getColumnListing('programs');
echo implode(', ', $columns) . "\n\n";

echo "=== PROGRAMS ===\n";
$programs = Program::select('program_id', 'program_name')->take(5)->get();
foreach ($programs as $program) {
    echo "ID: {$program->program_id}, Name: {$program->program_name}\n";
}

echo "\n=== PLANS TABLE COLUMNS ===\n";
$planColumns = Schema::getColumnListing('plans');
echo implode(', ', $planColumns) . "\n\n";

echo "=== PLANS ===\n";
try {
    $plans = \App\Models\Plan::all();
    foreach ($plans as $plan) {
        echo "ID: {$plan->plan_id}, Name: {$plan->plan_name}\n";
    }
} catch (\Exception $e) {
    echo "Error getting plans: " . $e->getMessage() . "\n";
}

echo "\n=== EDUCATION LEVELS ===\n";
$levels = \App\Models\EducationLevel::all();
foreach ($levels as $level) {
    echo "ID: {$level->id}, Name: {$level->level_name}\n";
    echo "  - General: " . ($level->available_for_general ? 'Yes' : 'No') . "\n";
    echo "  - Professional: " . ($level->available_for_professional ? 'Yes' : 'No') . "\n";
    echo "  - Review: " . ($level->available_for_review ? 'Yes' : 'No') . "\n";
}
