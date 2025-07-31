<?php
// Quick debug script for the three issues
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Debugging A.R.T.C Issues\n";
echo "===========================\n\n";

// Issue 1: Check assignment submissions
echo "1. Testing Assignment Submissions:\n";
try {
    $submissions = \App\Models\AssignmentSubmission::where('files', '!=', null)
        ->where('files', '!=', '')
        ->limit(3)
        ->get(['id', 'files', 'status']);
    
    echo "Found {$submissions->count()} submissions with files\n";
    
    foreach($submissions as $sub) {
        echo "  - Submission ID: {$sub->id} | Status: {$sub->status}\n";
        echo "  - Files type: " . gettype($sub->files) . "\n";
        
        if(is_string($sub->files)) {
            $decoded = json_decode($sub->files, true);
            echo "  - JSON decode: " . (is_array($decoded) ? "SUCCESS" : "FAILED") . "\n";
            if(is_array($decoded)) {
                echo "  - Files count: " . count($decoded) . "\n";
            }
        }
        echo "\n";
    }
} catch(Exception $e) {
    echo "  ERROR: {$e->getMessage()}\n";
}

// Issue 2: Check assignments for calendar
echo "2. Testing Calendar Assignments:\n";
try {
    $assignments = \App\Models\ContentItem::where('content_type', 'assignment')
        ->whereNotNull('due_date')
        ->limit(3)
        ->get(['id', 'content_title', 'due_date', 'course_id']);
    
    echo "Found {$assignments->count()} assignments with due dates\n";
    
    foreach($assignments as $assignment) {
        echo "  - Assignment ID: {$assignment->id}\n";
        echo "  - Title: {$assignment->content_title}\n";
        echo "  - Due Date: {$assignment->due_date}\n";
        echo "  - Course ID: {$assignment->course_id}\n";
        echo "  - Calendar URL should be: /student/content/{$assignment->id}/view\n";
        echo "\n";
    }
} catch(Exception $e) {
    echo "  ERROR: {$e->getMessage()}\n";
}

// Issue 3: Test completion data
echo "3. Testing Completion Data:\n";
try {
    $completions = \App\Models\ContentCompletion::limit(5)->get(['id', 'student_id', 'content_id', 'completed_at']);
    echo "Found {$completions->count()} content completions\n";
    
    foreach($completions as $completion) {
        echo "  - Completion ID: {$completion->id} | Student: {$completion->student_id} | Content: {$completion->content_id}\n";
    }
    
    $courseCompletions = \App\Models\CourseCompletion::limit(5)->get(['id', 'student_id', 'course_id', 'completed_at']);
    echo "\nFound {$courseCompletions->count()} course completions\n";
    
} catch(Exception $e) {
    echo "  ERROR: {$e->getMessage()}\n";
}

echo "\n✅ Debug complete!\n";
?>
