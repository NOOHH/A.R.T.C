<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TESTING TENANT DATABASE CONNECTION\n\n";

// Test tenant database connection
try {
    Config::set('database.default', 'tenant');
    DB::connection()->getPdo();
    echo "✅ Tenant database connection: SUCCESS\n";
    
    // Check what tables exist in tenant database
    $tables = DB::select('SHOW TABLES');
    echo "📋 Tables in tenant database:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "  - $tableName\n";
    }
    
    // Check if programs table exists
    $programsExists = DB::getSchemaBuilder()->hasTable('programs');
    echo "\n📊 Programs table exists: " . ($programsExists ? 'YES' : 'NO') . "\n";
    
    // Check if packages table exists
    $packagesExists = DB::getSchemaBuilder()->hasTable('packages');
    echo "📦 Packages table exists: " . ($packagesExists ? 'YES' : 'NO') . "\n";
    
    // Check if modules table exists
    $modulesExists = DB::getSchemaBuilder()->hasTable('modules');
    echo "📚 Modules table exists: " . ($modulesExists ? 'YES' : 'NO') . "\n";
    
    // Check if courses table exists
    $coursesExists = DB::getSchemaBuilder()->hasTable('courses');
    echo "📖 Courses table exists: " . ($coursesExists ? 'YES' : 'NO') . "\n";
    
    // Check sample data
    if ($programsExists) {
        $programCount = DB::table('programs')->count();
        echo "🎓 Programs count: $programCount\n";
    }
    
    if ($packagesExists) {
        $packageCount = DB::table('packages')->count();
        echo "📦 Packages count: $packageCount\n";
    }
    
} catch (Exception $e) {
    echo "❌ Tenant database connection: FAILED - " . $e->getMessage() . "\n";
}

echo "\n=== TENANT DATABASE TEST COMPLETE ===\n";
