<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Start the Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "🔧 CHECKING ARCHIVE COLUMNS\n";
echo "===========================\n\n";

// Check courses table for archive columns
echo "1. COURSES TABLE STRUCTURE:\n";
try {
    $columns = DB::select("DESCRIBE courses");
    $hasIsArchived = false;
    $hasArchivedAt = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'is_archived') $hasIsArchived = true;
        if ($column->Field === 'archived_at') $hasArchivedAt = true;
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n   Archive columns status:\n";
    echo "   - is_archived: " . ($hasIsArchived ? "✅ EXISTS" : "❌ MISSING") . "\n";
    echo "   - archived_at: " . ($hasArchivedAt ? "✅ EXISTS" : "❌ MISSING") . "\n";
    
} catch (Exception $e) {
    echo "❌ Error checking courses table: " . $e->getMessage() . "\n";
}

// Check content_items table for archive columns
echo "\n2. CONTENT_ITEMS TABLE STRUCTURE:\n";
try {
    $columns = DB::select("DESCRIBE content_items");
    $hasIsArchived = false;
    $hasArchivedAt = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'is_archived') $hasIsArchived = true;
        if ($column->Field === 'archived_at') $hasArchivedAt = true;
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n   Archive columns status:\n";
    echo "   - is_archived: " . ($hasIsArchived ? "✅ EXISTS" : "❌ MISSING") . "\n";
    echo "   - archived_at: " . ($hasArchivedAt ? "✅ EXISTS" : "❌ MISSING") . "\n";
    
} catch (Exception $e) {
    echo "❌ Error checking content_items table: " . $e->getMessage() . "\n";
}

echo "\n🏁 ARCHIVE COLUMNS CHECK COMPLETE\n";
