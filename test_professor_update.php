<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Professor Profile Update\n";
echo "=================================\n";

// Get the first professor
$professor = \App\Models\Professor::first();
if (!$professor) {
    echo "No professors found in database\n";
    exit;
}

echo "Professor found: " . $professor->professor_name . "\n";
echo "Current dynamic_data: " . json_encode($professor->dynamic_data) . "\n";

// Test updating dynamic_data
$professor->dynamic_data = ['test' => 'value'];
$professor->save();

echo "Updated dynamic_data successfully\n";

// Reload and verify
$professor->refresh();
echo "Verified dynamic_data: " . json_encode($professor->dynamic_data) . "\n";

echo "Test completed successfully!\n";
