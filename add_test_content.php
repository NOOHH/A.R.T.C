<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Module;

echo "=== CREATING TEST MODULE WITH CONTENT ===\n";

// Find the Culinary Module 1 - Creation of Food
$module = Module::where('module_name', 'Module 1 - Creation of Food')->first();

if ($module) {
    echo "Found module: " . $module->module_name . " (ID: " . $module->modules_id . ")\n";
    
    // Add some sample content
    $module->module_description = "This module covers the fundamental principles of food creation, including ingredient selection, preparation techniques, and basic cooking methods. Students will learn essential culinary skills and food safety practices.";
    
    // Add sample content data
    $contentData = [
        'learning_objectives' => [
            'Understand basic cooking techniques',
            'Learn food safety principles',
            'Master ingredient preparation',
            'Develop knife skills'
        ],
        'estimated_duration' => '2 hours',
        'difficulty_level' => 'Beginner',
        'materials_needed' => [
            'Chef knife',
            'Cutting board',
            'Basic cooking utensils',
            'Fresh ingredients'
        ]
    ];
    
    $module->content_data = $contentData;
    $module->save();
    
    echo "✅ Module updated with sample content!\n";
    echo "Description: " . $module->module_description . "\n";
    echo "Content Data: " . json_encode($contentData, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "❌ Module not found\n";
}

echo "\n=== COMPLETE ===\n";
?>
