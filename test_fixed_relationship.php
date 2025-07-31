<?php

use App\Models\AssignmentSubmission;
use App\Models\ContentItem;

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== Testing the fixed relationship ===\n";
    
    // Test the relationship
    $submission = AssignmentSubmission::with('contentItem')->first();
    
    if ($submission) {
        echo "Submission ID: " . $submission->id . "\n";
        echo "Content ID: " . $submission->content_id . "\n";
        
        if ($submission->contentItem) {
            echo "SUCCESS: Content Item found!\n";
            echo "Content Title: " . $submission->contentItem->content_title . "\n";
            echo "Content Type: " . $submission->contentItem->content_type . "\n";
        } else {
            echo "WARNING: No content item found for this submission\n";
            
            // Check if there's a matching content item
            $contentItem = ContentItem::find($submission->content_id);
            if ($contentItem) {
                echo "But content item exists with ID {$submission->content_id}: {$contentItem->content_title}\n";
            } else {
                echo "No content item exists with ID {$submission->content_id}\n";
            }
        }
    } else {
        echo "No submissions found\n";
    }
    
    echo "\n=== Testing all submissions ===\n";
    $submissions = AssignmentSubmission::with('contentItem')->get();
    foreach ($submissions as $sub) {
        echo "Submission {$sub->id}: content_id={$sub->content_id}, ";
        if ($sub->contentItem) {
            echo "content_title='{$sub->contentItem->content_title}'\n";
        } else {
            echo "NO CONTENT ITEM FOUND\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
