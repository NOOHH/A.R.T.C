<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Switch to tenant database
Config::set('database.default', 'tenant');

echo "🔍 CHECKING PACKAGE_COURSES TABLE STRUCTURE\n\n";

try {
    $columns = DB::select('DESCRIBE package_courses');
    echo "📋 Package_courses table columns:\n";
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
    
    echo "\n📋 Courses table columns:\n";
    $courseColumns = DB::select('DESCRIBE courses');
    foreach ($courseColumns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
