<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing Quiz Generator Data...\n\n";

// Check current quiz count
$quizCount = DB::table('quizzes')->count();
echo "Current quiz count: $quizCount\n";

// Check recent quiz attempts
$recentQuizzes = DB::table('quizzes')->orderBy('created_at', 'desc')->limit(5)->get();
foreach ($recentQuizzes as $quiz) {
    echo "Quiz: {$quiz->quiz_title} - Created: {$quiz->created_at}\n";
}

echo "\nChecking professors...\n";
$professors = DB::table('professors')->get(['professor_id', 'professor_name']);
foreach ($professors as $prof) {
    echo "Professor ID: {$prof->professor_id} - Name: {$prof->professor_name}\n";
}

echo "\nChecking programs...\n";
$programs = DB::table('programs')->get(['program_id', 'program_name']);
foreach ($programs as $program) {
    echo "Program ID: {$program->program_id} - Name: {$program->program_name}\n";
}

echo "\nChecking modules...\n";
$modules = DB::table('modules')->get(['modules_id', 'module_name', 'program_id']);
foreach ($modules as $module) {
    echo "Module ID: {$module->modules_id} - Name: {$module->module_name} - Program: {$module->program_id}\n";
}

echo "\nDone!\n";
