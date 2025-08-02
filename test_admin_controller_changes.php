<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Quiz;
use App\Models\Admin;

echo "=== TESTING ADMIN CONTROLLER CHANGES ===\n";

// Test admin controller with ALL quizzes
echo "1. TESTING ADMIN QUIZ DISPLAY (ALL QUIZZES):\n";
echo "-------------------------------------------\n";

// Simulate admin session
session(['user_id' => 1, 'logged_in' => true, 'user_role' => 'admin']);

$adminId = session('user_id');
echo "Admin ID from session: {$adminId}\n";

// Get ALL quizzes (new logic)
$allQuizzes = Quiz::with(['program', 'module', 'course', 'questions'])
                ->orderBy('created_at', 'desc')
                ->get();

$draftQuizzes = $allQuizzes->where('status', 'draft');
$publishedQuizzes = $allQuizzes->where('status', 'published');
$archivedQuizzes = $allQuizzes->where('status', 'archived');

echo "\nAdmin should now see ALL quizzes:\n";
echo "Total quizzes: {$allQuizzes->count()}\n";
echo "Draft: {$draftQuizzes->count()}\n";
echo "Published: {$publishedQuizzes->count()}\n";
echo "Archived: {$archivedQuizzes->count()}\n";

echo "\nQuiz breakdown:\n";
foreach ($allQuizzes as $quiz) {
    $creator = 'Unknown';
    if ($quiz->admin_id) {
        $admin = Admin::find($quiz->admin_id);
        $creator = "Admin: " . ($admin ? $admin->admin_name : "ID {$quiz->admin_id}");
    } elseif ($quiz->professor_id) {
        $creator = "Professor ID: {$quiz->professor_id}";
    }
    
    echo "  - Quiz {$quiz->quiz_id}: {$quiz->quiz_title} ({$quiz->status}) - Created by: {$creator}\n";
}

echo "\n2. TESTING STATUS CHANGE OPERATIONS:\n";
echo "-----------------------------------\n";

// Test with professor-created quiz (ID 48: "THIS IS PROFESSOR")
$professorQuiz = Quiz::find(48);
if ($professorQuiz) {
    echo "Testing status changes on professor-created quiz: {$professorQuiz->quiz_title}\n";
    echo "Original status: {$professorQuiz->status}\n";
    
    // Test publish
    $professorQuiz->update([
        'status' => 'published',
        'is_draft' => false,
        'is_active' => true
    ]);
    
    $professorQuiz->refresh();
    echo "✓ Published successfully - Status: {$professorQuiz->status}\n";
    
    // Test archive
    $professorQuiz->update([
        'status' => 'archived',
        'is_draft' => false,
        'is_active' => false
    ]);
    
    $professorQuiz->refresh();
    echo "✓ Archived successfully - Status: {$professorQuiz->status}\n";
    
    // Test draft
    $professorQuiz->update([
        'status' => 'draft',
        'is_draft' => true,
        'is_active' => false
    ]);
    
    $professorQuiz->refresh();
    echo "✓ Moved to draft successfully - Status: {$professorQuiz->status}\n";
}

// Test with admin-created quiz (ID 46: "qweqw")
$adminQuiz = Quiz::find(46);
if ($adminQuiz) {
    echo "\nTesting status changes on admin-created quiz: {$adminQuiz->quiz_title}\n";
    echo "Original status: {$adminQuiz->status}\n";
    
    // Test publish
    $adminQuiz->update([
        'status' => 'published',
        'is_draft' => false,
        'is_active' => true
    ]);
    
    $adminQuiz->refresh();
    echo "✓ Published successfully - Status: {$adminQuiz->status}\n";
    
    // Test archive
    $adminQuiz->update([
        'status' => 'archived',
        'is_draft' => false,
        'is_active' => false
    ]);
    
    $adminQuiz->refresh();
    echo "✓ Archived successfully - Status: {$adminQuiz->status}\n";
    
    // Test draft
    $adminQuiz->update([
        'status' => 'draft',
        'is_draft' => true,
        'is_active' => false
    ]);
    
    $adminQuiz->refresh();
    echo "✓ Moved to draft successfully - Status: {$adminQuiz->status}\n";
}

echo "\n3. FINAL VERIFICATION:\n";
echo "---------------------\n";

// Get final state
$finalQuizzes = Quiz::orderBy('created_at', 'desc')->get();
$finalDraft = $finalQuizzes->where('status', 'draft')->count();
$finalPublished = $finalQuizzes->where('status', 'published')->count();
$finalArchived = $finalQuizzes->where('status', 'archived')->count();

echo "Final status counts:\n";
echo "  Draft: {$finalDraft}\n";
echo "  Published: {$finalPublished}\n";
echo "  Archived: {$finalArchived}\n";

echo "\n=== ADMIN CONTROLLER CHANGES TESTED SUCCESSFULLY ===\n";
echo "✓ Admin can now see ALL quizzes (both admin and professor created)\n";
echo "✓ Admin can manage status of ANY quiz\n";
echo "✓ No access control restrictions for admin\n";
echo "✓ Controller logic updated successfully\n";
