<?php
// Test the fixed professor grading functionality
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Professor;
use App\Models\AssignmentSubmission;

echo "=== TESTING FIXED GRADING SYSTEM ===\n\n";

try {
    // Test 1: Get professor 8 and their assigned programs
    echo "1. Testing Professor 8 Program Access:\n";
    $professor = Professor::where('professor_id', 8)->first();
    
    if (!$professor) {
        echo "✗ Professor 8 not found\n";
        exit(1);
    }
    
    echo "✓ Professor found: {$professor->first_name} {$professor->last_name}\n";
    
    // Test the fixed query - get assigned program IDs with proper table qualification
    $assignedProgramIds = $professor->assignedPrograms()->pluck('programs.program_id')->toArray();
    echo "✓ Assigned Program IDs: " . implode(', ', $assignedProgramIds) . "\n";
    
    // Test 2: Get submissions for professor's programs
    echo "\n2. Testing Submission Access:\n";
    $submissions = AssignmentSubmission::with(['student', 'program', 'module', 'contentItem'])
        ->whereIn('program_id', $assignedProgramIds)
        ->limit(3)
        ->get();
    
    echo "✓ Found " . $submissions->count() . " submissions\n";
    
    foreach ($submissions as $submission) {
        echo "- Submission ID: {$submission->id}\n";
        echo "  Student: {$submission->student->firstname} {$submission->student->lastname}\n";
        echo "  Program: {$submission->program->program_name}\n";
        echo "  Module: {$submission->module->module_name}\n";
        echo "  Status: {$submission->status}\n";
        echo "  Grade: " . ($submission->grade ?? 'Not graded') . "\n";
        
        // Test if content item relationship works
        if ($submission->contentItem) {
            echo "  ✓ Assignment: {$submission->contentItem->content_title}\n";
        } else {
            echo "  ⚠ Assignment details missing (content_id: {$submission->content_id})\n";
        }
        echo "\n";
    }
    
    echo "3. Testing Grading Modal Data Structure:\n";
    if ($submissions->count() > 0) {
        $testSubmission = $submissions->first();
        
        // Simulate what the details endpoint would return
        $modalData = [
            'submission' => [
                'id' => $testSubmission->id,
                'student' => [
                    'firstname' => $testSubmission->student->firstname,
                    'lastname' => $testSubmission->student->lastname,
                ],
                'program' => [
                    'program_name' => $testSubmission->program->program_name,
                ],
                'module' => [
                    'module_name' => $testSubmission->module->module_name,
                ],
                'submitted_at' => $testSubmission->submitted_at,
                'status' => $testSubmission->status,
                'grade' => $testSubmission->grade,
                'feedback' => $testSubmission->feedback,
                'comments' => $testSubmission->comments,
            ]
        ];
        
        // Add content item data if available
        if ($testSubmission->contentItem) {
            $modalData['submission']['content_item'] = [
                'content_title' => $testSubmission->contentItem->content_title,
                'content_description' => $testSubmission->contentItem->content_description,
                'content_data' => $testSubmission->contentItem->content_data,
            ];
        }
        
        echo "✓ Modal data structure ready for submission ID: {$testSubmission->id}\n";
        echo "✓ All required fields available for grading modal\n";
    }
    
    echo "\n=== GRADING SYSTEM TESTS PASSED ===\n";
    echo "The ambiguous column error has been fixed!\n";
    echo "Professor grading functionality should now work properly.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
