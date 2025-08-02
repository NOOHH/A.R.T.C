<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$content = \App\Models\ContentItem::find(83);
echo "Content 83 status:\n";
echo "- Active: " . ($content->is_active ? 'YES' : 'NO') . "\n";
echo "- Archived: " . ($content->is_archived ? 'YES' : 'NO') . "\n";

// Fix the content to be active
if (!$content->is_active) {
    $content->update(['is_active' => true]);
    echo "✓ Set content to active\n";
}
