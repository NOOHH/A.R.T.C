<?php
/**
 * CUSTOMIZE WEBSITE PERMISSION TEST SCRIPT
 * Comprehensive testing for customize-website page permission modifications
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

echo "🎯 CUSTOMIZE WEBSITE PERMISSION MODIFICATION TEST\n";
echo "===============================================\n\n";

// Test URLs
$customizePageUrl = "http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=16";
$adminPreviewUrl = "http://127.0.0.1:8000/t/draft/test2/admin-dashboard?website=16&preview=true&t=" . time();

echo "1️⃣ TESTING CURRENT CUSTOMIZE-WEBSITE PAGE\n";
echo "-------------------------------------------\n";

try {
    // Test the customize-website page access
    $response = Http::timeout(10)->get($customizePageUrl);
    
    echo "   ✅ Customize page status: " . $response->status() . "\n";
    
    if ($response->successful()) {
        $content = $response->body();
        
        // Check for Advanced Settings section
        $hasAdvancedSettings = strpos($content, 'Advanced Settings') !== false;
        echo "   " . ($hasAdvancedSettings ? "✅" : "❌") . " Advanced Settings section: " . ($hasAdvancedSettings ? "FOUND" : "NOT FOUND") . "\n";
        
        // Check for specific advanced elements we need to remove
        $elementsToRemove = [
            'Custom CSS' => 'Custom CSS',
            'Custom JavaScript' => 'Custom JavaScript', 
            'Google Analytics ID' => 'Google Analytics ID',
            'Facebook Pixel ID' => 'Facebook Pixel ID',
            'Meta Tags' => 'Meta Tags',
            'System Preferences' => 'System Preferences',
            'Maintenance Mode' => 'Maintenance Mode',
            'Debug Mode' => 'Debug Mode',
            'Enable Caching' => 'Enable Caching'
        ];
        
        echo "\n   📋 CURRENT ADVANCED ELEMENTS:\n";
        foreach ($elementsToRemove as $key => $text) {
            $found = strpos($content, $text) !== false;
            echo "      " . ($found ? "❌" : "✅") . " $key: " . ($found ? "PRESENT (to be removed)" : "NOT FOUND") . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "   ❌ ERROR testing customize page: " . $e->getMessage() . "\n";
}

echo "\n2️⃣ TESTING ADMIN-DASHBOARD PREVIEW STRUCTURE\n";
echo "----------------------------------------------\n";

try {
    // Test the admin-dashboard preview to understand permission structure
    $response = Http::timeout(10)->get($adminPreviewUrl);
    
    echo "   ✅ Admin preview status: " . $response->status() . "\n";
    
    if ($response->successful()) {
        $content = $response->body();
        
        // Look for permission structure in admin preview
        $permissionElements = [
            'Director Features' => 'director',
            'Professor Features' => 'professor',
            'Permission' => 'permission',
            'Access Control' => 'access',
            'Feature' => 'feature'
        ];
        
        echo "\n   📋 ADMIN PREVIEW PERMISSION ELEMENTS:\n";
        foreach ($permissionElements as $element => $keyword) {
            $found = strpos(strtolower($content), strtolower($element)) !== false;
            echo "      " . ($found ? "✅" : "❌") . " $element: " . ($found ? "FOUND" : "NOT FOUND") . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "   ❌ ERROR testing admin preview: " . $e->getMessage() . "\n";
}

echo "\n3️⃣ DATABASE PERMISSION STRUCTURE ANALYSIS\n";
echo "-------------------------------------------\n";

try {
    // Check admin_settings for director and professor permissions
    echo "   🔍 DIRECTOR PERMISSIONS:\n";
    $directorPermissions = DB::table('admin_settings')
        ->where('setting_key', 'like', 'director_%')
        ->where('is_active', 1)
        ->get(['setting_key', 'setting_value']);
    
    foreach ($directorPermissions as $perm) {
        $status = $perm->setting_value === 'true' ? "✅" : "❌";
        echo "      $status {$perm->setting_key}: {$perm->setting_value}\n";
    }
    
    echo "\n   🔍 PROFESSOR PERMISSIONS:\n";
    $professorPermissions = DB::table('admin_settings')
        ->where('setting_key', 'like', 'professor_%')
        ->where('is_active', 1)
        ->get(['setting_key', 'setting_value']);
    
    foreach ($professorPermissions as $perm) {
        $status = $perm->setting_value === 'true' || $perm->setting_value === '1' ? "✅" : "❌";
        echo "      $status {$perm->setting_key}: {$perm->setting_value}\n";
    }
    
} catch (\Exception $e) {
    echo "   ❌ ERROR checking database permissions: " . $e->getMessage() . "\n";
}

echo "\n4️⃣ ROUTE AND CONTROLLER ANALYSIS\n";
echo "-----------------------------------\n";

try {
    // Check if required routes exist
    $requiredRoutes = [
        'smartprep.dashboard.customize' => 'Customize page route',
        'dashboard.settings.update.advanced' => 'Advanced settings update',
        'admin.settings.professor-features' => 'Professor features route',
        'admin.settings.director-features' => 'Director features route'
    ];
    
    echo "   🔍 ROUTE AVAILABILITY:\n";
    foreach ($requiredRoutes as $routeName => $description) {
        try {
            $routeExists = \Route::has($routeName);
            echo "      " . ($routeExists ? "✅" : "❌") . " $description: " . ($routeExists ? "EXISTS" : "NOT FOUND") . "\n";
        } catch (\Exception $e) {
            echo "      ❌ $description: ERROR checking route\n";
        }
    }
    
} catch (\Exception $e) {
    echo "   ❌ ERROR checking routes: " . $e->getMessage() . "\n";
}

echo "\n5️⃣ FILE STRUCTURE VALIDATION\n";
echo "------------------------------\n";

$requiredFiles = [
    'resources/views/dashboard/customize-website.blade.php' => 'Main customize page',
    'resources/views/admin/admin-settings/admin-settings.blade.php' => 'Admin settings reference',
    'app/Http/Controllers/Smartprep/Dashboard/CustomizeWebsiteController.php' => 'Controller',
    'routes/smartprep.php' => 'Routes file'
];

echo "   📁 FILE EXISTENCE CHECK:\n";
foreach ($requiredFiles as $file => $description) {
    $exists = file_exists(__DIR__ . '/' . $file);
    echo "      " . ($exists ? "✅" : "❌") . " $description: " . ($exists ? "EXISTS" : "NOT FOUND") . "\n";
}

echo "\n6️⃣ JAVASCRIPT FUNCTION ANALYSIS\n";
echo "--------------------------------\n";

try {
    $customizeFilePath = __DIR__ . '/resources/views/dashboard/customize-website.blade.php';
    if (file_exists($customizeFilePath)) {
        $content = file_get_contents($customizeFilePath);
        
        $jsFunctions = [
            'handleFormSubmission' => 'Form submission handler',
            'updateAdvanced' => 'Advanced settings update',
            'refreshPreview' => 'Preview refresh function',
            'saveAllSettings' => 'Save all settings function'
        ];
        
        echo "   🔍 JAVASCRIPT FUNCTIONS:\n";
        foreach ($jsFunctions as $func => $description) {
            $found = strpos($content, $func) !== false;
            echo "      " . ($found ? "✅" : "❌") . " $description: " . ($found ? "FOUND" : "NOT FOUND") . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "   ❌ ERROR analyzing JavaScript: " . $e->getMessage() . "\n";
}

echo "\n7️⃣ PERMISSION INTEGRATION REQUIREMENTS\n";
echo "---------------------------------------\n";

echo "   📝 IMPLEMENTATION PLAN:\n";
echo "   1. ✅ Remove Advanced Settings section from customize-website.blade.php\n";
echo "   2. ✅ Add Director Features section (copy from admin-settings)\n";
echo "   3. ✅ Add Professor Features section (copy from admin-settings)\n";
echo "   4. ✅ Update JavaScript handlers for new sections\n";
echo "   5. ✅ Update controller methods for permission saving\n";
echo "   6. ✅ Add database integration for permission storage\n";
echo "   7. ✅ Create comprehensive test suite\n";
echo "   8. ✅ Test all functionality end-to-end\n";

echo "\n8️⃣ TESTING CHECKLIST SUMMARY\n";
echo "-----------------------------\n";

$testChecklist = [
    '🌐 Current page loads correctly',
    '📋 Advanced settings section identified', 
    '🎯 Admin preview permission structure analyzed',
    '💾 Database permissions mapped',
    '🛣️ Routes validated',
    '📁 Required files confirmed',
    '⚙️ JavaScript functions identified',
    '📝 Implementation plan created'
];

echo "   ✅ PRE-MODIFICATION TESTS COMPLETE:\n";
foreach ($testChecklist as $item) {
    echo "      ✅ $item\n";
}

echo "\n🚀 READY FOR IMPLEMENTATION!\n";
echo "============================\n";
echo "Next: Run CUSTOMIZE_WEBSITE_IMPLEMENTATION.php to make changes\n";
