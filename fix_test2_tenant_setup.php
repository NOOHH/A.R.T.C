<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 FIXING TEST2 TENANT SETUP\n";
echo "============================\n\n";

try {
    // Step 1: Create migrations table in test2 database
    echo "📊 STEP 1: Creating migrations table in test2 database\n";
    echo "------------------------------------------------------\n";
    
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
    
    // Create migrations table manually
    DB::statement('
        CREATE TABLE IF NOT EXISTS `migrations` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `migration` varchar(255) NOT NULL,
            `batch` int(11) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ');
    
    echo "✅ Migrations table created in test2 database\n";
    
    // Step 2: Run migrations properly
    echo "\n📊 STEP 2: Running migrations on test2 database\n";
    echo "-----------------------------------------------\n";
    
    // Use Laravel's migration command
    $output = shell_exec('php artisan migrate --database=test2 --force 2>&1');
    echo "Migration output:\n$output\n";
    
    // Step 3: Copy data from existing tenant
    echo "\n📊 STEP 3: Copying data from existing tenant\n";
    echo "--------------------------------------------\n";
    
    // Configure source database connection
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
        $programData = (array)$program;
        unset($programData['program_id']); // Remove ID to auto-generate
        DB::table('programs')->insert($programData);
    }
    echo "✅ Copied " . count($programs) . " programs\n";
    
    // Copy packages
    echo "📦 Copying packages...\n";
    Config::set('database.default', 'source');
    $packages = DB::table('packages')->get();
    
    Config::set('database.default', 'test2');
    foreach ($packages as $package) {
        $packageData = (array)$package;
        unset($packageData['package_id']); // Remove ID to auto-generate
        DB::table('packages')->insert($packageData);
    }
    echo "✅ Copied " . count($packages) . " packages\n";
    
    // Copy modules
    echo "📚 Copying modules...\n";
    Config::set('database.default', 'source');
    $modules = DB::table('modules')->get();
    
    Config::set('database.default', 'test2');
    foreach ($modules as $module) {
        $moduleData = (array)$module;
        unset($moduleData['modules_id']); // Remove ID to auto-generate
        DB::table('modules')->insert($moduleData);
    }
    echo "✅ Copied " . count($modules) . " modules\n";
    
    // Copy courses
    echo "📖 Copying courses...\n";
    Config::set('database.default', 'source');
    $courses = DB::table('courses')->get();
    
    Config::set('database.default', 'test2');
    foreach ($courses as $course) {
        $courseData = (array)$course;
        unset($courseData['subject_id']); // Remove ID to auto-generate
        DB::table('courses')->insert($courseData);
    }
    echo "✅ Copied " . count($courses) . " courses\n";
    
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
    
    // Step 5: Test tenant access
    echo "\n📊 STEP 5: Testing tenant access\n";
    echo "--------------------------------\n";
    
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
        echo "⚠️ Creating test2 tenant record in main database...\n";
        
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
    }
    
    echo "\n🎉 TEST2 TENANT SETUP COMPLETE!\n";
    echo "===============================\n";
    echo "✅ Database structure created\n";
    echo "✅ Sample data copied\n";
    echo "✅ Tenant record verified\n";
    echo "\n🔗 You can now access:\n";
    echo "  - http://127.0.0.1:8000/t/draft/test2/admin/programs\n";
    echo "  - http://127.0.0.1:8000/t/draft/test2/admin/packages\n";
    
} catch (Exception $e) {
    echo "❌ Error fixing test2 tenant: " . $e->getMessage() . "\n";
    echo "❌ Stack trace: " . $e->getTraceAsString() . "\n";
}
