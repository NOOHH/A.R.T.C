<?php
/**
 * Script to enable director dashboard access
 * This enables the director to access the main director dashboard page
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 ENABLING DIRECTOR DASHBOARD ACCESS...\n\n";

try {
    // First, check current director permissions
    echo "📊 CURRENT DIRECTOR PERMISSIONS:\n";
    $currentSettings = DB::table('admin_settings')
        ->where('admin_id', 15)
        ->where('website_id', 15)
        ->first();
    
    if ($currentSettings) {
        echo "   - director_manage_modules: " . ($currentSettings->director_manage_modules ?? 'NULL') . "\n";
        echo "   - director_manage_professors: " . ($currentSettings->director_manage_professors ?? 'NULL') . "\n";
        echo "   - director_view_students: " . ($currentSettings->director_view_students ?? 'NULL') . "\n";
        echo "   - director_dashboard_access: " . ($currentSettings->director_dashboard_access ?? 'NULL') . "\n";
    } else {
        echo "   ❌ No settings found for admin_id=15, website_id=15\n";
    }
    
    echo "\n🔄 UPDATING DIRECTOR PERMISSIONS...\n";
    
    // Enable all director permissions including dashboard access
    $updated = DB::table('admin_settings')
        ->where('admin_id', 15)
        ->where('website_id', 15)
        ->update([
            'director_manage_modules' => 'true',
            'director_manage_professors' => 'true', 
            'director_view_students' => 'true',
            'director_dashboard_access' => 'true',
            'can_access_director_features' => 'true',
            'updated_at' => now()
        ]);
    
    if ($updated > 0) {
        echo "✅ UPDATED: $updated row(s) affected\n";
        
        // Verify the changes
        $verifySettings = DB::table('admin_settings')
            ->where('admin_id', 15)
            ->where('website_id', 15)
            ->first();
        
        echo "\n📋 VERIFIED PERMISSIONS:\n";
        echo "   ✅ director_manage_modules: " . ($verifySettings->director_manage_modules ?? 'NULL') . "\n";
        echo "   ✅ director_manage_professors: " . ($verifySettings->director_manage_professors ?? 'NULL') . "\n";
        echo "   ✅ director_view_students: " . ($verifySettings->director_view_students ?? 'NULL') . "\n";
        echo "   ✅ director_dashboard_access: " . ($verifySettings->director_dashboard_access ?? 'NULL') . "\n";
        echo "   ✅ can_access_director_features: " . ($verifySettings->can_access_director_features ?? 'NULL') . "\n";
        
    } else {
        echo "⚠️  No rows were updated. Let's check if record exists...\n";
        
        // Check if admin exists
        $adminExists = DB::table('admins')->where('id', 15)->exists();
        echo "   Admin ID 15 exists: " . ($adminExists ? 'YES' : 'NO') . "\n";
        
        // Check if website exists  
        $websiteExists = DB::table('websites')->where('id', 15)->exists();
        echo "   Website ID 15 exists: " . ($websiteExists ? 'YES' : 'NO') . "\n";
        
        if ($adminExists && $websiteExists) {
            echo "\n🔧 CREATING NEW ADMIN_SETTINGS RECORD...\n";
            
            $inserted = DB::table('admin_settings')->insert([
                'admin_id' => 15,
                'website_id' => 15,
                'director_manage_modules' => 'true',
                'director_manage_professors' => 'true',
                'director_view_students' => 'true', 
                'director_dashboard_access' => 'true',
                'can_access_director_features' => 'true',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "✅ CREATED: New admin_settings record inserted\n";
        }
    }
    
    echo "\n🎯 DIRECTOR DASHBOARD ACCESS ENABLED!\n";
    echo "   The director should now be able to access:\n";
    echo "   - Main director dashboard\n";
    echo "   - Module management\n";
    echo "   - Professor management\n";
    echo "   - Student viewing\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "📍 FILE: " . $e->getFile() . "\n";
    echo "📍 LINE: " . $e->getLine() . "\n";
}

echo "\n✅ DIRECTOR DASHBOARD ENABLEMENT COMPLETE!\n";
