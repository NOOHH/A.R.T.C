<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== DATABASE STRUCTURE ANALYSIS ===\n";

// Check content_items table
if (Schema::hasTable('content_items')) {
    echo "✅ content_items table exists\n";
    $columns = Schema::getColumnListing('content_items');
    echo "Columns: " . implode(', ', $columns) . "\n";
    
    // Get sample data
    $items = DB::table('content_items')->limit(3)->get();
    echo "Sample records: " . $items->count() . "\n";
    foreach ($items as $item) {
        echo "  - ID: " . $item->id . ", Type: " . ($item->content_type ?? 'N/A') . ", File: " . ($item->file_path ?? 'N/A') . "\n";
    }
    echo "\n";
} else {
    echo "❌ content_items table does not exist\n\n";
}

// Check courses table
if (Schema::hasTable('courses')) {
    echo "✅ courses table exists\n";
    $columns = Schema::getColumnListing('courses');
    echo "Columns: " . implode(', ', $columns) . "\n";
    
    // Get sample data
    $courses = DB::table('courses')->limit(3)->get();
    echo "Sample records: " . $courses->count() . "\n";
    foreach ($courses as $course) {
        echo "  - Subject ID: " . $course->subject_id . ", Name: " . ($course->subject_name ?? 'N/A') . "\n";
    }
    echo "\n";
} else {
    echo "❌ courses table does not exist\n\n";
}

// Check modules table structure
echo "=== MODULES TABLE ===\n";
$columns = Schema::getColumnListing('modules');
echo "Columns: " . implode(', ', $columns) . "\n";

// Check for relationships
$modules = DB::table('modules')->limit(3)->get();
foreach ($modules as $module) {
    echo "Module " . $module->modules_id . ": " . $module->module_name . "\n";
    echo "  Program ID: " . $module->program_id . "\n";
    echo "  Content Type: " . ($module->content_type ?? 'N/A') . "\n";
    echo "  Attachment: " . ($module->attachment ?? 'N/A') . "\n";
    echo "  Video Path: " . ($module->video_path ?? 'N/A') . "\n";
    echo "\n";
}

// List all tables to see the structure
echo "=== ALL TABLES ===\n";
$tables = DB::select('SHOW TABLES');
$databaseName = DB::getDatabaseName();
$tableKey = "Tables_in_" . $databaseName;

foreach ($tables as $table) {
    $tableName = $table->$tableKey;
    if (strpos($tableName, 'content') !== false || strpos($tableName, 'course') !== false || strpos($tableName, 'module') !== false) {
        echo "📋 " . $tableName . "\n";
    }
}

echo "\n=== COMPLETE ===\n";
?>
