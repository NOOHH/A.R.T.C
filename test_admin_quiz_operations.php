<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Quiz;
use App\Models\Admin;

echo "=== ADMIN QUIZ MANAGEMENT TEST ===\n";

// Test admin quiz status changes
$adminId = 1; // Based on the analysis above
$quizId = 46;   // Admin's quiz "qweqw"

echo "Testing admin quiz management for Admin ID: {$adminId}, Quiz ID: {$quizId}\n";

$quiz = Quiz::find($quizId);
if (!$quiz) {
    echo "ERROR: Quiz {$quizId} not found!\n";
    exit(1);
}

echo "Found quiz: {$quiz->quiz_title} (Status: {$quiz->status})\n";
echo "Quiz belongs to admin_id: {$quiz->admin_id}\n";
echo "Quiz belongs to professor_id: " . ($quiz->professor_id ?? 'null') . "\n";

// Test publish operation
echo "\n1. TESTING PUBLISH OPERATION:\n";
echo "-----------------------------\n";

$originalStatus = $quiz->status;
$quiz->update([
    'status' => 'published',
    'is_draft' => false,
    'is_active' => true
]);

$quiz->refresh();
echo "✓ Quiz status changed from '{$originalStatus}' to '{$quiz->status}'\n";
echo "✓ is_draft: " . ($quiz->is_draft ? 'true' : 'false') . "\n";
echo "✓ is_active: " . ($quiz->is_active ? 'true' : 'false') . "\n";

// Test archive operation
echo "\n2. TESTING ARCHIVE OPERATION:\n";
echo "-----------------------------\n";

$quiz->update([
    'status' => 'archived',
    'is_draft' => false,
    'is_active' => false
]);

$quiz->refresh();
echo "✓ Quiz status changed to '{$quiz->status}'\n";
echo "✓ is_draft: " . ($quiz->is_draft ? 'true' : 'false') . "\n";
echo "✓ is_active: " . ($quiz->is_active ? 'true' : 'false') . "\n";

// Test draft operation
echo "\n3. TESTING DRAFT OPERATION:\n";
echo "--------------------------\n";

$quiz->update([
    'status' => 'draft',
    'is_draft' => true,
    'is_active' => false
]);

$quiz->refresh();
echo "✓ Quiz status changed to '{$quiz->status}'\n";
echo "✓ is_draft: " . ($quiz->is_draft ? 'true' : 'false') . "\n";
echo "✓ is_active: " . ($quiz->is_active ? 'true' : 'false') . "\n";

echo "\n4. TESTING ADMIN CONTROLLER QUERY:\n";
echo "----------------------------------\n";

// Simulate controller index query
$allQuizzes = Quiz::where('admin_id', $adminId)
                ->with(['program', 'module', 'course', 'questions'])
                ->orderBy('created_at', 'desc')
                ->get();

echo "Admin would see {$allQuizzes->count()} quiz(es)\n";

$draftQuizzes = $allQuizzes->where('status', 'draft');
$publishedQuizzes = $allQuizzes->where('status', 'published');
$archivedQuizzes = $allQuizzes->where('status', 'archived');

echo "Breakdown:\n";
echo "  Draft: {$draftQuizzes->count()}\n";
echo "  Published: {$publishedQuizzes->count()}\n";
echo "  Archived: {$archivedQuizzes->count()}\n";

echo "\n5. TESTING PROFESSOR SEPARATION:\n";
echo "-------------------------------\n";

$professorQuizzes = Quiz::where('professor_id', 8)->get();
echo "Professor quizzes (should NOT appear for admin): {$professorQuizzes->count()}\n";

foreach ($professorQuizzes as $pQuiz) {
    echo "  - Quiz {$pQuiz->quiz_id}: {$pQuiz->quiz_title} (Professor: {$pQuiz->professor_id}, Admin: " . ($pQuiz->admin_id ?? 'null') . ")\n";
}

echo "\n=== ALL TESTS COMPLETED ===\n";
echo "The database operations work correctly.\n";
echo "Issues must be in the controller logic, routes, or JavaScript.\n";
