<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Course table structure\n";
echo "==============================\n";

try {
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('courses');
    echo "Course table columns:\n";
    foreach($columns as $column) {
        echo "- " . $column . "\n";
    }
    
    echo "\nChecking if archiving columns exist:\n";
    echo "- is_archived: " . (in_array('is_archived', $columns) ? 'YES' : 'NO') . "\n";
    echo "- archived_at: " . (in_array('archived_at', $columns) ? 'YES' : 'NO') . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
