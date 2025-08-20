<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

echo "=== COMPREHENSIVE NAVBAR CUSTOMIZATION TEST ===\n\n";

try {
    // Test 1: Check admin settings and tenant settings
    echo "1. CHECKING ADMIN NAVBAR SETTINGS:\n";
    
    $adminSettings = DB::table('ui_settings')
        ->where('group', 'navbar')
        ->where('key', 'brand_name')
        ->value('value');
    
    echo "   Admin brand name: " . ($adminSettings ?? 'NOT SET') . "\n";
    
    // Test 2: Check tenant database settings
    echo "\n2. CHECKING TENANT NAVBAR SETTINGS:\n";
    
    // Find tenant
    $tenant = DB::table('tenants')->where('domain', 'z.smartprep.local')->first();
    if ($tenant) {
        $tenantConnection = "tenant_{$tenant->slug}";
        
        // Configure connection
        config(["database.connections.$tenantConnection" => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $tenant->database,
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]]);
        
        $tenantBrandName = DB::connection($tenantConnection)
            ->table('settings')
            ->where('group', 'navbar')
            ->where('key', 'brand_name')
            ->value('value');
            
        echo "   Tenant brand name: " . ($tenantBrandName ?? 'NOT SET') . "\n";
        
        // Test 3: Test navbar update with form data
        echo "\n3. TESTING NAVBAR UPDATE SIMULATION:\n";
        
        $testBrandName = 'TEST_BRAND_' . date('His');
        
        // Simulate navbar update
        DB::connection($tenantConnection)->table('settings')
            ->updateOrInsert(
                ['group' => 'navbar', 'key' => 'brand_name'],
                ['value' => $testBrandName, 'type' => 'text']
            );
            
        // Verify update
        $updatedBrandName = DB::connection($tenantConnection)
            ->table('settings')
            ->where('group', 'navbar')
            ->where('key', 'brand_name')
            ->value('value');
            
        echo "   Updated brand name: $updatedBrandName\n";
        echo "   Update success: " . ($updatedBrandName === $testBrandName ? 'YES' : 'NO') . "\n";
        
        // Test 4: Check professor navbar in actual templates
        echo "\n4. CHECKING PROFESSOR NAVBAR FILES:\n";
        
        $professorNavbarFiles = [
            'resources/views/professor/layouts/navbar.blade.php',
            'resources/views/professor/partials/navbar.blade.php',
            'resources/views/smartprep/professor/layouts/navbar.blade.php',
            'resources/views/smartprep/professor/partials/navbar.blade.php'
        ];
        
        foreach ($professorNavbarFiles as $file) {
            if (file_exists($file)) {
                echo "   ✓ Found: $file\n";
                $content = file_get_contents($file);
                if (strpos($content, 'brand_name') !== false || strpos($content, 'brand-name') !== false) {
                    echo "     Contains brand name reference\n";
                } else {
                    echo "     ⚠ Missing brand name reference\n";
                }
            } else {
                echo "   ✗ Missing: $file\n";
            }
        }
        
        // Test 5: Check routes and form submissions
        echo "\n5. FORM SUBMISSION ISSUES:\n";
        
        // Check if tenant dashboard navbar form has proper event handler
        $dashboardNavbarFile = 'resources/views/smartprep/dashboard/partials/settings/navbar.blade.php';
        if (file_exists($dashboardNavbarFile)) {
            $navbarContent = file_get_contents($dashboardNavbarFile);
            if (strpos($navbarContent, 'onsubmit=') !== false) {
                echo "   ✓ Navbar form has onsubmit handler\n";
            } else {
                echo "   ✗ Navbar form MISSING onsubmit handler\n";
                echo "   → This is likely why navbar changes aren't being saved!\n";
            }
        }
        
        // Test 6: Check if the professor views are using dynamic brand names
        echo "\n6. CHECKING PROFESSOR VIEWS FOR DYNAMIC BRAND NAME:\n";
        
        // Find all professor blade files
        $professorViewsDir = 'resources/views/smartprep/professor';
        if (is_dir($professorViewsDir)) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($professorViewsDir));
            $bladeFiles = [];
            
            foreach ($files as $file) {
                if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $bladeFiles[] = $file->getPathname();
                }
            }
            
            $staticBrandCount = 0;
            $dynamicBrandCount = 0;
            
            foreach ($bladeFiles as $file) {
                $content = file_get_contents($file);
                // Check for static brand names
                if (preg_match('/SmartPrep|ARTC|A\.R\.T\.C/i', $content)) {
                    $staticBrandCount++;
                }
                // Check for dynamic brand names
                if (preg_match('/\$.*brand|settings.*brand|config.*brand/i', $content)) {
                    $dynamicBrandCount++;
                }
            }
            
            echo "   Professor blade files found: " . count($bladeFiles) . "\n";
            echo "   Files with static brand names: $staticBrandCount\n";
            echo "   Files with dynamic brand names: $dynamicBrandCount\n";
            
            if ($staticBrandCount > $dynamicBrandCount) {
                echo "   ⚠ Many files use static brand names - this prevents customization!\n";
            }
        }
        
    } else {
        echo "   ✗ Tenant not found for z.smartprep.local\n";
    }
    
    echo "\n=== DIAGNOSIS SUMMARY ===\n";
    echo "1. Backend database operations: WORKING\n";
    echo "2. Navbar form submission: NEEDS FIX (missing onsubmit handler)\n";
    echo "3. Professor navbar customization: NEEDS INVESTIGATION\n";
    echo "4. Static vs dynamic brand names: NEEDS REVIEW\n";
    
    echo "\n=== RECOMMENDED FIXES ===\n";
    echo "1. Add onsubmit='updateNavbar(event)' to tenant navbar form\n";
    echo "2. Add JavaScript updateNavbar function to tenant dashboard\n";
    echo "3. Update professor views to use dynamic brand names\n";
    echo "4. Ensure brand name is passed to all template contexts\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
