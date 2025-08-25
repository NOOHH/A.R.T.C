<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Switch to tenant database
Config::set('database.default', 'tenant');

echo "🔍 TESTING PACKAGE_COURSES TABLE\n\n";

try {
    // Check if table exists
    $tableExists = Schema::hasTable('package_courses');
    echo "📋 Package_courses table exists: " . ($tableExists ? 'YES' : 'NO') . "\n";
    
    if ($tableExists) {
        // Get column listing
        $columns = Schema::getColumnListing('package_courses');
        echo "📋 Columns in package_courses table:\n";
        foreach ($columns as $column) {
            echo "  - $column\n";
        }
        
        // Check if we can insert data
        try {
            $result = DB::table('package_courses')->insert([
                'package_id' => 1,
                'subject_id' => 1
            ]);
            echo "✅ Insert test: SUCCESS\n";
            
            // Clean up
            DB::table('package_courses')->where('package_id', 1)->where('subject_id', 1)->delete();
            echo "✅ Cleanup: SUCCESS\n";
            
        } catch (Exception $e) {
            echo "❌ Insert test failed: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
