<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Models\AssignmentSubmission;
use App\Models\ContentItem;

try {
    echo "Testing ContentItem model...\n";
    $contentItemCount = ContentItem::count();
    echo "Content Items count: $contentItemCount\n";
    
    echo "\nTesting AssignmentSubmission model...\n";
    $submissionCount = AssignmentSubmission::count();
    echo "Assignment Submissions count: $submissionCount\n";
    
    echo "\nTesting relationship...\n";
    $submission = AssignmentSubmission::with('contentItem')->first();
    
    if ($submission) {
        echo "Submission ID: " . $submission->id . "\n";
        echo "Content ID: " . $submission->content_id . "\n";
        
        if ($submission->contentItem) {
            echo "Content Item found: " . $submission->contentItem->content_title . "\n";
        } else {
            echo "No content item found for this submission\n";
        }
    } else {
        echo "No submissions found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
