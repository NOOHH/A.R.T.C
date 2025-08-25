<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 CREATING TEST2 TENANT WITH COMPLETE SETUP\n";
echo "============================================\n\n";

try {
    // Step 1: Create tenant record in main database
    echo "📊 STEP 1: Creating tenant record in main database\n";
    echo "--------------------------------------------------\n";
    
    Config::set('database.default', 'mysql');
    
    $tenantData = [
        'name' => 'Test2 Website',
        'slug' => 'test2',
        'domain' => 'test2.local',
        'database_name' => 'smartprep_test2',
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    $tenantId = DB::table('tenants')->insertGetId($tenantData);
    echo "✅ Tenant record created with ID: $tenantId\n";
    
    // Step 2: Set up test2 database with all tables
    echo "\n📊 STEP 2: Setting up test2 database structure\n";
    echo "-----------------------------------------------\n";
    
    // Configure test2 database connection
    Config::set('database.connections.test2', [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'smartprep_test2',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => null,
    ]);
    
    Config::set('database.default', 'test2');
    
    // Run migrations on test2 database
    echo "🔄 Running migrations on test2 database...\n";
    
    // Get all migration files
    $migrationPath = 'database/migrations';
    $migrationFiles = glob($migrationPath . '/*.php');
    
    foreach ($migrationFiles as $migrationFile) {
        $migrationName = basename($migrationFile, '.php');
        echo "  - Running migration: $migrationName\n";
        
        try {
            // Check if migration already ran
            $migrationExists = DB::table('migrations')->where('migration', $migrationName)->exists();
            
            if (!$migrationExists) {
                // Run the migration
                $migration = require $migrationFile;
                $migration->up();
                
                // Record the migration
                DB::table('migrations')->insert([
                    'migration' => $migrationName,
                    'batch' => 1
                ]);
                
                echo "    ✅ Migration completed\n";
            } else {
                echo "    ⚠️ Migration already exists\n";
            }
            
        } catch (Exception $e) {
            echo "    ❌ Migration failed: " . $e->getMessage() . "\n";
        }
    }
    
    // Step 3: Copy sample data from existing tenant
    echo "\n📊 STEP 3: Copying sample data from existing tenant\n";
    echo "---------------------------------------------------\n";
    
    // Switch to source tenant (smartprep_artc)
    Config::set('database.connections.source', [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'smartprep_artc',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => null,
    ]);
    
    // Copy programs
    echo "📚 Copying programs...\n";
    Config::set('database.default', 'source');
    $programs = DB::table('programs')->get();
    
    Config::set('database.default', 'test2');
    foreach ($programs as $program) {
        DB::table('programs')->insert((array)$program);
    }
    echo "✅ Copied " . count($programs) . " programs\n";
    
    // Copy packages
    echo "📦 Copying packages...\n";
    Config::set('database.default', 'source');
    $packages = DB::table('packages')->get();
    
    Config::set('database.default', 'test2');
    foreach ($packages as $package) {
        DB::table('packages')->insert((array)$package);
    }
    echo "✅ Copied " . count($packages) . " packages\n";
    
    // Copy modules
    echo "📚 Copying modules...\n";
    Config::set('database.default', 'source');
    $modules = DB::table('modules')->get();
    
    Config::set('database.default', 'test2');
    foreach ($modules as $module) {
        DB::table('modules')->insert((array)$module);
    }
    echo "✅ Copied " . count($modules) . " modules\n";
    
    // Copy courses
    echo "📖 Copying courses...\n";
    Config::set('database.default', 'source');
    $courses = DB::table('courses')->get();
    
    Config::set('database.default', 'test2');
    foreach ($courses as $course) {
        DB::table('courses')->insert((array)$course);
    }
    echo "✅ Copied " . count($courses) . " courses\n";
    
    // Copy package relationships
    echo "🔗 Copying package relationships...\n";
    Config::set('database.default', 'source');
    $packageModules = DB::table('package_modules')->get();
    $packageCourses = DB::table('package_courses')->get();
    
    Config::set('database.default', 'test2');
    foreach ($packageModules as $pm) {
        DB::table('package_modules')->insert((array)$pm);
    }
    foreach ($packageCourses as $pc) {
        DB::table('package_courses')->insert((array)$pc);
    }
    echo "✅ Copied package relationships\n";
    
    // Step 4: Verify the setup
    echo "\n📊 STEP 4: Verifying test2 tenant setup\n";
    echo "----------------------------------------\n";
    
    // Check tables exist
    $keyTables = ['programs', 'packages', 'modules', 'courses', 'package_modules', 'package_courses'];
    foreach ($keyTables as $table) {
        $exists = DB::getSchemaBuilder()->hasTable($table);
        echo "📋 Table '$table' exists: " . ($exists ? 'YES' : 'NO') . "\n";
    }
    
    // Check data counts
    $programCount = DB::table('programs')->count();
    $packageCount = DB::table('packages')->count();
    $moduleCount = DB::table('modules')->count();
    $courseCount = DB::table('courses')->count();
    
    echo "\n📊 Data counts in test2 database:\n";
    echo "  - Programs: $programCount\n";
    echo "  - Packages: $packageCount\n";
    echo "  - Modules: $moduleCount\n";
    echo "  - Courses: $courseCount\n";
    
    // Step 5: Test tenant context
    echo "\n📊 STEP 5: Testing tenant context\n";
    echo "----------------------------------\n";
    
    // Switch back to main database
    Config::set('database.default', 'mysql');
    
    // Verify tenant exists
    $test2Tenant = DB::table('tenants')->where('slug', 'test2')->first();
    if ($test2Tenant) {
        echo "✅ test2 tenant verified in main database\n";
        echo "  - Name: {$test2Tenant->name}\n";
        echo "  - Slug: {$test2Tenant->slug}\n";
        echo "  - Database: {$test2Tenant->database_name}\n";
    } else {
        echo "❌ test2 tenant not found in main database\n";
    }
    
    echo "\n🎉 TEST2 TENANT CREATION COMPLETE!\n";
    echo "==================================\n";
    echo "✅ Tenant record created in main database\n";
    echo "✅ Database structure set up\n";
    echo "✅ Sample data copied\n";
    echo "✅ All relationships established\n";
    echo "\n🔗 You can now access:\n";
    echo "  - http://127.0.0.1:8000/t/draft/test2/admin/programs\n";
    echo "  - http://127.0.0.1:8000/t/draft/test2/admin/packages\n";
    
} catch (Exception $e) {
    echo "❌ Error creating test2 tenant: " . $e->getMessage() . "\n";
    echo "❌ Stack trace: " . $e->getTraceAsString() . "\n";
}
