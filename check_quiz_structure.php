<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== QUIZ DATABASE STRUCTURE ANALYSIS ===\n\n";

// Check quizzes table structure
try {
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('quizzes');
    echo "QUIZZES TABLE COLUMNS:\n";
    foreach($columns as $col) {
        echo "  - $col\n";
    }
    
    // Get sample quiz data
    $sampleQuiz = \App\Models\Quiz::first();
    if ($sampleQuiz) {
        // Refresh the model to get fresh data
        $sampleQuiz = $sampleQuiz->fresh();
        
        echo "\nSAMPLE QUIZ DATA:\n";
        echo "  Quiz ID: {$sampleQuiz->quiz_id}\n";
        echo "  Title: {$sampleQuiz->quiz_title}\n";
        echo "  Time Limit: {$sampleQuiz->time_limit}\n";
        echo "  Max Attempts: {$sampleQuiz->max_attempts}\n";
        echo "  Status: {$sampleQuiz->status}\n";
        echo "  Created: {$sampleQuiz->created_at}\n";
        
        // Check new columns directly
        echo "  Due Date: " . ($sampleQuiz->due_date ?? 'NULL') . "\n";
        echo "  Infinite Retakes: " . ($sampleQuiz->infinite_retakes ? 'TRUE' : 'FALSE') . "\n";
        echo "  Has Deadline: " . ($sampleQuiz->has_deadline ? 'TRUE' : 'FALSE') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check content_items table for quiz content
echo "\n=== CONTENT_ITEMS QUIZ ANALYSIS ===\n";
try {
    $quizContent = \App\Models\ContentItem::where('content_type', 'quiz')->first();
    if ($quizContent) {
        echo "SAMPLE QUIZ CONTENT ITEM:\n";
        echo "  Content ID: {$quizContent->id}\n";
        echo "  Title: {$quizContent->content_title}\n";
        echo "  Due Date: " . ($quizContent->due_date ?? 'NULL') . "\n";
        echo "  Content Data: " . json_encode($quizContent->content_data) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
