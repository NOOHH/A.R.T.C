<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';

// Boot the application
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Check if program_image column exists
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('programs');
    echo "Program table columns:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    
    echo "\nProgram image column exists: " . (in_array('program_image', $columns) ? 'YES' : 'NO') . "\n";
    
    // Check model fillable
    $program = new \App\Models\Program();
    echo "\nProgram model fillable fields:\n";
    foreach ($program->getFillable() as $field) {
        echo "- $field\n";
    }
    
    echo "\nProgram image field in fillable: " . (in_array('program_image', $program->getFillable()) ? 'YES' : 'NO') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
