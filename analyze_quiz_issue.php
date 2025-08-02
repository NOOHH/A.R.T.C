<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Quiz;
use App\Models\Professor;
use App\Models\Admin;

echo "=== QUIZ DATABASE ANALYSIS ===\n";

// Count all quizzes by creator type
echo "1. QUIZ BREAKDOWN BY CREATOR:\n";
echo "----------------------------\n";

$allQuizzes = Quiz::all();
$adminQuizzes = $allQuizzes->whereNotNull('admin_id');
$professorQuizzes = $allQuizzes->whereNotNull('professor_id')->whereNull('admin_id');
$legacyQuizzes = $allQuizzes->whereNull('admin_id')->whereNull('professor_id');

echo "Total quizzes: " . $allQuizzes->count() . "\n";
echo "Admin created: " . $adminQuizzes->count() . "\n";
echo "Professor created: " . $professorQuizzes->count() . "\n";
echo "Legacy (no creator): " . $legacyQuizzes->count() . "\n";

echo "\n2. RECENT QUIZZES WITH CREATOR INFO:\n";
echo "----------------------------------\n";

$recentQuizzes = Quiz::orderBy('created_at', 'desc')->limit(10)->get();
foreach ($recentQuizzes as $quiz) {
    $creator = 'Unknown';
    if ($quiz->admin_id) {
        $admin = Admin::find($quiz->admin_id);
        $creator = "Admin: " . ($admin ? $admin->admin_name : "ID {$quiz->admin_id}");
    } elseif ($quiz->professor_id) {
        $prof = Professor::find($quiz->professor_id);
        $creator = "Professor: " . ($prof ? $prof->professor_first_name . ' ' . $prof->professor_last_name : "ID {$quiz->professor_id}");
    }
    
    echo "Quiz {$quiz->quiz_id}: {$quiz->quiz_title} ({$quiz->status}) - {$creator}\n";
}

echo "\n3. STATUS BREAKDOWN:\n";
echo "------------------\n";

$statusBreakdown = Quiz::selectRaw('status, COUNT(*) as count')->groupBy('status')->get();
foreach ($statusBreakdown as $status) {
    echo "{$status->status}: {$status->count} quizzes\n";
}

echo "\n4. CHECKING SESSION SIMULATION FOR ADMIN:\n";
echo "----------------------------------------\n";

// Simulate admin session
session(['user_id' => 1, 'logged_in' => true, 'user_role' => 'admin']);

$adminId = session('user_id');
echo "Admin ID from session: " . $adminId . "\n";

// Test admin quiz query (simulating controller logic)
$adminQuizzes = Quiz::where('admin_id', $adminId)
                  ->with(['program', 'module', 'course', 'questions'])
                  ->orderBy('created_at', 'desc')
                  ->get();

echo "Quizzes for admin {$adminId}: " . $adminQuizzes->count() . "\n";

foreach ($adminQuizzes as $quiz) {
    echo "  - Quiz {$quiz->quiz_id}: {$quiz->quiz_title} ({$quiz->status})\n";
}

echo "\n5. CHECKING SESSION SIMULATION FOR PROFESSOR:\n";
echo "--------------------------------------------\n";

// Simulate professor session  
session(['professor_id' => 8, 'logged_in' => true, 'user_role' => 'professor']);

$professorId = session('professor_id');
echo "Professor ID from session: " . $professorId . "\n";

// Test professor quiz query
$professorQuizzes = Quiz::where('professor_id', $professorId)
                      ->with(['program', 'questions'])
                      ->orderBy('created_at', 'desc')
                      ->get();

echo "Quizzes for professor {$professorId}: " . $professorQuizzes->count() . "\n";

foreach ($professorQuizzes as $quiz) {
    echo "  - Quiz {$quiz->quiz_id}: {$quiz->quiz_title} ({$quiz->status})\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
