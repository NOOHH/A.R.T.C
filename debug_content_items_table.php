<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Content Items Table Structure:\n";
    echo "==============================\n";
    
    $columns = DB::select('DESCRIBE content_items');
    foreach ($columns as $column) {
        echo sprintf("%-25s %-20s %-8s %-8s %-15s %s\n",
            $column->Field,
            $column->Type,
            $column->Null,
            $column->Key,
            $column->Default ?? 'NULL',
            $column->Extra ?? ''
        );
    }
    
    echo "\n\nTesting ContentItem model creation...\n";
    echo "=====================================\n";
    
    // Test if we can create a minimal ContentItem
    $testData = [
        'content_title' => 'Test Content',
        'course_id' => 1, // assuming course ID 1 exists
        'content_type' => 'lesson',
        'is_active' => true,
    ];
    
    echo "Test data: " . json_encode($testData) . "\n";
    
    try {
        $contentItem = \App\Models\ContentItem::create($testData);
        echo "SUCCESS: ContentItem created with ID: " . $contentItem->id . "\n";
        
        // Delete the test record
        $contentItem->delete();
        echo "Test record cleaned up.\n";
        
    } catch (Exception $e) {
        echo "ERROR creating ContentItem: " . $e->getMessage() . "\n";
        echo "Exception type: " . get_class($e) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Exception type: " . get_class($e) . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
