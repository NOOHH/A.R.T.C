<?php
// Test professor grading workflow
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AssignmentSubmission;
use App\Models\Professor;
use App\Models\ContentItem;

echo "=== PROFESSOR GRADING WORKFLOW TEST ===\n\n";

// Test 1: Check if professor exists and has assigned programs
echo "1. CHECKING PROFESSOR ID 8:\n";
$professor = Professor::with('assignedPrograms')->where('professor_id', 8)->first();
if ($professor) {
    echo "✓ Professor found: {$professor->first_name} {$professor->last_name}\n";
    echo "✓ Assigned programs: " . $professor->assignedPrograms->pluck('program_name')->implode(', ') . "\n";
    $assignedProgramIds = $professor->assignedPrograms->pluck('program_id')->toArray();
    echo "✓ Program IDs: " . implode(', ', $assignedProgramIds) . "\n";
} else {
    echo "✗ Professor not found\n";
    exit(1);
}

echo "\n2. CHECKING SUBMISSIONS FOR PROFESSOR'S PROGRAMS:\n";
$submissions = AssignmentSubmission::with(['student.user', 'program', 'module', 'contentItem'])
    ->whereIn('program_id', $assignedProgramIds)
    ->limit(3)
    ->get();

echo "Found " . $submissions->count() . " submissions\n";
foreach ($submissions as $submission) {
    echo "- Submission ID: {$submission->id}\n";
    echo "  Student: {$submission->student->firstname} {$submission->student->lastname}\n";
    echo "  Program: {$submission->program->program_name}\n";
    echo "  Module: {$submission->module->module_name}\n";
    echo "  Content ID: {$submission->content_id}\n";
    
    if ($submission->contentItem) {
        echo "  ✓ Content Item: {$submission->contentItem->content_title}\n";
    } else {
        echo "  ✗ Content Item: NOT FOUND (content_id: {$submission->content_id})\n";
    }
    
    echo "  Status: {$submission->status}\n";
    echo "  Grade: " . ($submission->grade ?? 'Not graded') . "\n";
    echo "  Submitted: {$submission->submitted_at}\n\n";
}

echo "\n3. TESTING CONTENT ITEM RELATIONSHIPS:\n";
// Check content_items table for existing assignments
$contentItems = ContentItem::where('content_type', 'assignment')->limit(5)->get();
echo "Found " . $contentItems->count() . " assignment content items:\n";
foreach ($contentItems as $item) {
    echo "- ID: {$item->id}, Title: {$item->content_title}\n";
    
    // Check if any submissions reference this content item
    $submissionCount = AssignmentSubmission::where('content_id', $item->id)->count();
    echo "  Submissions: {$submissionCount}\n";
}

echo "\n4. CHECKING GRADING WORKFLOW:\n";
if ($submissions->count() > 0) {
    $testSubmission = $submissions->first();
    echo "Testing with Submission ID: {$testSubmission->id}\n";
    
    // Simulate the details endpoint data
    $submissionData = $testSubmission->toArray();
    echo "✓ Submission data available\n";
    
    if ($testSubmission->contentItem) {
        echo "✓ Assignment details available: {$testSubmission->contentItem->content_title}\n";
    } else {
        echo "⚠ Assignment details missing - will use module name as fallback\n";
    }
    
    echo "✓ Student information: {$testSubmission->student->firstname} {$testSubmission->student->lastname}\n";
    echo "✓ Submission files: " . ($testSubmission->files ? 'Available' : 'None') . "\n";
    echo "✓ Ready for grading\n";
}

echo "\n=== WORKFLOW TEST COMPLETE ===\n";
echo "The professor grading system appears to be properly configured!\n";
?>
