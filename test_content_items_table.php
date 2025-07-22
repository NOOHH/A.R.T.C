<?php

// Check content_items table structure
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== CONTENT_ITEMS TABLE STRUCTURE TEST ===\n\n";

try {
    echo "1. CHECKING TABLE EXISTS:\n";
    $tableExists = Schema::hasTable('content_items');
    echo "   content_items table exists: " . ($tableExists ? 'YES' : 'NO') . "\n\n";
    
    if ($tableExists) {
        echo "2. CHECKING TABLE COLUMNS:\n";
        $columns = Schema::getColumnListing('content_items');
        echo "   Total columns: " . count($columns) . "\n";
        
        $keyColumns = ['id', 'content_title', 'attachment_path', 'course_id', 'content_type'];
        foreach ($keyColumns as $col) {
            $exists = in_array($col, $columns);
            echo "   {$col}: " . ($exists ? 'EXISTS' : 'MISSING') . "\n";
        }
        
        echo "\n   All columns:\n";
        foreach ($columns as $col) {
            echo "     - {$col}\n";
        }
        
        echo "\n3. CHECKING COLUMN TYPES:\n";
        $columnTypes = DB::select("DESCRIBE content_items");
        foreach ($columnTypes as $col) {
            if (in_array($col->Field, ['id', 'content_title', 'attachment_path', 'course_id'])) {
                echo "   {$col->Field}: {$col->Type} (Null: {$col->Null}, Default: {$col->Default})\n";
            }
        }
        
        echo "\n4. TESTING INSERT:\n";
        // Test insert with attachment_path
        try {
            $testData = [
                'content_title' => 'TEST ATTACHMENT INSERT',
                'content_description' => 'Testing attachment path insert',
                'course_id' => 32,
                'content_type' => 'lesson',
                'attachment_path' => 'content/test_insert_' . time() . '.pdf',
                'is_required' => true,
                'is_active' => true,
            ];
            
            echo "   Attempting insert with data:\n";
            foreach ($testData as $key => $value) {
                echo "     {$key}: {$value}\n";
            }
            
            $insertId = DB::table('content_items')->insertGetId($testData);
            echo "   ✅ Insert successful! ID: {$insertId}\n";
            
            // Verify the insert
            $inserted = DB::table('content_items')->where('id', $insertId)->first();
            echo "   Verification:\n";
            echo "     ID: {$inserted->id}\n";
            echo "     Title: {$inserted->content_title}\n";
            echo "     Attachment Path: " . ($inserted->attachment_path ?: 'NULL') . "\n";
            echo "     Course ID: {$inserted->course_id}\n";
            
            // Clean up test record
            DB::table('content_items')->where('id', $insertId)->delete();
            echo "   ✅ Test record cleaned up\n";
            
        } catch (Exception $e) {
            echo "   ❌ Insert failed: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>
