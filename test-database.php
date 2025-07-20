<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing database connection...\n";

try {
    $result = DB::select('SELECT 1 as test');
    echo "✅ Database connected successfully\n";
    
    // Test if modules table exists
    $modules = DB::select('SELECT COUNT(*) as count FROM modules LIMIT 1');
    echo "✅ Modules table accessible\n";
    
    // Test if content_items table exists  
    $content = DB::select('SELECT COUNT(*) as count FROM content_items LIMIT 1');
    echo "✅ Content items table accessible\n";
    
    echo "All database tests passed!\n";
    
} catch(Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}

?>
