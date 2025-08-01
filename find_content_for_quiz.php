<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$content = \App\Models\ContentItem::where('content_type', 'quiz')
    ->whereRaw("JSON_EXTRACT(content_data, '$.quiz_id') = ?", [47])
    ->first();

if($content) {
    echo "Found content ID: " . $content->id . " - " . $content->content_title . "\n";
    echo "Content URL: http://127.0.0.1:8000/student/content/" . $content->id . "/view\n";
} else {
    echo "No content found for quiz 47\n";
    
    // Let's see what content items exist
    echo "\nAvailable quiz content items:\n";
    $contents = \App\Models\ContentItem::where('content_type', 'quiz')->get(['id', 'content_title', 'content_data']);
    foreach($contents as $item) {
        echo "ID: {$item->id}, Title: {$item->content_title}, Data: " . json_encode($item->content_data) . "\n";
    }
}
