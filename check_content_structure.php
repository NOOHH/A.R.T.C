<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// First, let's see what columns exist in the content_items table
try {
    $content = \App\Models\ContentItem::first();
    if($content) {
        echo "Content item attributes:\n";
        print_r($content->getAttributes());
    } else {
        echo "No content items found\n";
    }
} catch (Exception $e) {
    echo "Error accessing content items: " . $e->getMessage() . "\n";
}
