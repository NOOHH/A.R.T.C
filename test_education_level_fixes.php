<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EducationLevel;
use Illuminate\Support\Facades\DB;

echo "=== Education Level Fixes Test ===\n\n";

try {
    // Test 1: Check existing education levels
    echo "1. Checking existing education levels:\n";
    $levels = EducationLevel::all();
    
    if ($levels->count() > 0) {
        foreach ($levels as $level) {
            echo "   - ID: {$level->id}, Name: {$level->level_name}, Active: " . ($level->is_active ? 'Yes' : 'No') . "\n";
            
            // Check file requirements
            $fileReqs = $level->file_requirements;
            if ($fileReqs) {
                if (is_string($fileReqs)) {
                    $parsed = json_decode($fileReqs, true);
                    echo "     File requirements (string): " . (is_array($parsed) ? count($parsed) . " items" : "Invalid JSON") . "\n";
                } else {
                    echo "     File requirements (array): " . count($fileReqs) . " items\n";
                }
            } else {
                echo "     File requirements: None\n";
            }
        }
    } else {
        echo "   No education levels found\n";
    }
    
    // Test 2: Create a test education level
    echo "\n2. Creating test education level:\n";
    
    $testData = [
        'level_name' => 'Test Level ' . date('Y-m-d H:i:s'),
        'level_description' => 'Test description',
        'is_active' => true,
        'file_requirements' => json_encode([
            [
                'field_name' => 'test_document',
                'document_type' => 'custom',
                'file_type' => 'image',
                'is_required' => true,
                'available_full_plan' => true,
                'available_modular_plan' => true
            ]
        ])
    ];
    
    $newLevel = EducationLevel::create($testData);
    echo "   Created education level with ID: {$newLevel->id}\n";
    echo "   is_active value: " . ($newLevel->is_active ? 'true' : 'false') . "\n";
    
    // Test 3: Update the test level
    echo "\n3. Updating test education level:\n";
    $updateData = [
        'is_active' => false,
        'file_requirements' => json_encode([
            [
                'field_name' => 'updated_document',
                'document_type' => 'diploma',
                'file_type' => 'pdf',
                'is_required' => false,
                'available_full_plan' => true,
                'available_modular_plan' => false
            ]
        ])
    ];
    
    $newLevel->update($updateData);
    echo "   Updated education level\n";
    echo "   is_active value: " . ($newLevel->is_active ? 'true' : 'false') . "\n";
    
    // Test 4: Verify the update
    $updatedLevel = EducationLevel::find($newLevel->id);
    echo "   Verified is_active: " . ($updatedLevel->is_active ? 'true' : 'false') . "\n";
    
    $fileReqs = $updatedLevel->file_requirements;
    if (is_string($fileReqs)) {
        $parsed = json_decode($fileReqs, true);
        echo "   File requirements count: " . (is_array($parsed) ? count($parsed) : "Invalid") . "\n";
    }
    
    // Test 5: Clean up
    echo "\n4. Cleaning up test data:\n";
    $newLevel->delete();
    echo "   Test education level deleted\n";
    
    echo "\n✅ All tests passed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n"; 