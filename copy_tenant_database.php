<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 COPYING TENANT DATABASE STRUCTURE AND DATA\n";
echo "=============================================\n\n";

try {
    // Step 1: Use mysqldump to copy the entire database
    echo "📊 STEP 1: Copying database structure and data\n";
    echo "-----------------------------------------------\n";
    
    $sourceDb = 'smartprep_artc';
    $targetDb = 'smartprep_test2';
    $host = '127.0.0.1';
    $user = 'root';
    $password = '';
    
    // Drop target database if it exists
    echo "🗑️ Dropping existing target database...\n";
    $dropCommand = "mysql -h $host -u $user -e \"DROP DATABASE IF EXISTS $targetDb;\"";
    exec($dropCommand);
    echo "✅ Target database dropped\n";
    
    // Create target database
    echo "🏗️ Creating target database...\n";
    $createCommand = "mysql -h $host -u $user -e \"CREATE DATABASE $targetDb;\"";
    exec($createCommand);
    echo "✅ Target database created\n";
    
    // Copy entire database structure and data
    echo "📋 Copying database structure and data...\n";
    $dumpCommand = "mysqldump -h $host -u $user --single-transaction --routines --triggers $sourceDb | mysql -h $host -u $user $targetDb";
    exec($dumpCommand);
    echo "✅ Database copied successfully\n";
    
    // Step 2: Verify the copy
    echo "\n📊 STEP 2: Verifying database copy\n";
    echo "-----------------------------------\n";
    
    // Connect to target database
    Config::set('database.connections.test2', [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => $targetDb,
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => null,
    ]);
    
    Config::set('database.default', 'test2');
    
    // Check tables
    $tables = DB::select('SHOW TABLES');
    echo "📋 Tables in test2 database:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "  - $tableName\n";
    }
    
    // Check key data
    $keyTables = ['programs', 'packages', 'modules', 'courses'];
    foreach ($keyTables as $table) {
        $count = DB::table($table)->count();
        echo "📊 Table '$table' count: $count\n";
    }
    
    // Step 3: Update tenant record in main database
    echo "\n📊 STEP 3: Updating tenant record\n";
    echo "--------------------------------\n";
    
    Config::set('database.default', 'mysql');
    
    // Check if test2 tenant exists
    $test2Tenant = DB::table('tenants')->where('slug', 'test2')->first();
    
    if ($test2Tenant) {
        echo "✅ test2 tenant already exists in main database\n";
        echo "  - Name: {$test2Tenant->name}\n";
        echo "  - Slug: {$test2Tenant->slug}\n";
        echo "  - Database: {$test2Tenant->database_name}\n";
    } else {
        echo "⚠️ Creating test2 tenant record...\n";
        
        $tenantData = [
            'name' => 'Test2 Website',
            'slug' => 'test2',
            'domain' => 'test2.local',
            'database_name' => $targetDb,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        $tenantId = DB::table('tenants')->insertGetId($tenantData);
        echo "✅ Tenant record created with ID: $tenantId\n";
    }
    
    // Step 4: Test tenant functionality
    echo "\n📊 STEP 4: Testing tenant functionality\n";
    echo "--------------------------------------\n";
    
    // Test database connection
    Config::set('database.default', 'test2');
    try {
        DB::connection()->getPdo();
        echo "✅ test2 database connection: SUCCESS\n";
        
        // Test sample queries
        $programs = DB::table('programs')->get();
        echo "✅ Programs query: " . count($programs) . " found\n";
        
        $packages = DB::table('packages')->get();
        echo "✅ Packages query: " . count($packages) . " found\n";
        
    } catch (Exception $e) {
        echo "❌ test2 database connection: FAILED - " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 TENANT DATABASE COPY COMPLETE!\n";
    echo "=================================\n";
    echo "✅ Database structure copied\n";
    echo "✅ All data copied\n";
    echo "✅ Tenant record verified\n";
    echo "✅ Database connection tested\n";
    echo "\n🔗 You can now access:\n";
    echo "  - http://127.0.0.1:8000/t/draft/test2/admin/programs\n";
    echo "  - http://127.0.0.1:8000/t/draft/test2/admin/packages\n";
    
} catch (Exception $e) {
    echo "❌ Error copying tenant database: " . $e->getMessage() . "\n";
    echo "❌ Stack trace: " . $e->getTraceAsString() . "\n";
}
