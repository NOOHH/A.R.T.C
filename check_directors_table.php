<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Directors Table Structure ===\n";
    
    // Get table columns
    $columns = Schema::getColumnListing('directors');
    echo "Columns: " . implode(', ', $columns) . "\n\n";
    
    // Get a sample record
    echo "=== Sample Director Record ===\n";
    $director = DB::table('directors')->first();
    if ($director) {
        foreach ((array)$director as $key => $value) {
            echo "$key: $value\n";
        }
    } else {
        echo "No directors found\n";
    }
    
    echo "\n=== Migration Check ===\n";
    // Check what the actual primary key is
    $primaryKey = DB::select("SHOW KEYS FROM directors WHERE Key_name = 'PRIMARY'");
    if (!empty($primaryKey)) {
        echo "Primary Key: " . $primaryKey[0]->Column_name . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
