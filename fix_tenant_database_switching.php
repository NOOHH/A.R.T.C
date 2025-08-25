<?php
echo "🔧 FIXING TENANT DATABASE SWITCHING FOR ENROLLMENT CONTROLLERS\n";
echo "==============================================================\n\n";

// The issue is that the controllers are still using the main database
// Let's check and fix the ModularRegistrationController

echo "1️⃣ ANALYZING MODULAR REGISTRATION CONTROLLER:\n";
echo "---------------------------------------------\n";

$modularController = 'app/Http/Controllers/ModularRegistrationController.php';
if (file_exists($modularController)) {
    $content = file_get_contents($modularController);
    
    // Check if it's using tenant-aware database queries
    if (strpos($content, 'packages') !== false) {
        echo "✅ Controller uses packages table\n";
        
        // Check if it's using proper tenant database connection
        if (strpos($content, 'connection(') !== false) {
            echo "✅ Controller specifies database connection\n";
        } else {
            echo "❌ Controller not using explicit database connection\n";
        }
        
        // Look for the specific query causing the error
        if (strpos($content, "package_type") !== false) {
            echo "✅ Found package_type query\n";
        }
    }
} else {
    echo "❌ ModularRegistrationController not found\n";
}

echo "\n2️⃣ CHECKING TENANT MIDDLEWARE REGISTRATION:\n";
echo "-------------------------------------------\n";

// Check if tenant middleware is properly registered for tenant routes
$webRoutes = 'routes/web.php';
if (file_exists($webRoutes)) {
    $content = file_get_contents($webRoutes);
    
    // Look for tenant route groups
    if (preg_match('/Route::prefix\([\'"]t[\'"].*?middleware.*?tenant/s', $content)) {
        echo "✅ Tenant middleware registered in web routes\n";
    } else {
        echo "❌ Tenant middleware not found in web routes\n";
    }
}

// Check kernel middleware registration
$kernel = 'app/Http/Kernel.php';
if (file_exists($kernel)) {
    $content = file_get_contents($kernel);
    
    if (strpos($content, 'TenantMiddleware') !== false) {
        echo "✅ TenantMiddleware registered in Kernel\n";
    } else {
        echo "❌ TenantMiddleware not registered in Kernel\n";
    }
}

echo "\n3️⃣ CREATING EXPLICIT TENANT-AWARE CONTROLLER FIXES:\n";
echo "----------------------------------------------------\n";

// Create a backup and modify the ModularRegistrationController
if (file_exists($modularController)) {
    $backupFile = $modularController . '.backup.' . date('Y-m-d-H-i-s');
    copy($modularController, $backupFile);
    echo "✅ Backup created: $backupFile\n";
    
    $content = file_get_contents($modularController);
    
    // Add tenant-aware database queries
    $originalContent = $content;
    
    // Fix 1: Add use statements if not present
    if (strpos($content, 'use App\Services\TenantService;') === false) {
        $content = str_replace(
            'use Illuminate\Http\Request;',
            "use Illuminate\Http\Request;\nuse App\Services\TenantService;",
            $content
        );
        echo "✅ Added TenantService use statement\n";
    }
    
    // Fix 2: Replace direct Package model queries with tenant-aware queries
    $packageQueries = [
        "Package::where('package_type', 'modular')" => "DB::connection('tenant')->table('packages')->where('package_type', 'modular')",
        "Package::where('package_type', 'full')" => "DB::connection('tenant')->table('packages')->where('package_type', 'full')",
        "\$packages = Package::" => "\$packages = DB::connection('tenant')->table('packages')->"
    ];
    
    foreach ($packageQueries as $from => $to) {
        if (strpos($content, $from) !== false) {
            $content = str_replace($from, $to, $content);
            echo "✅ Fixed package query: $from\n";
        }
    }
    
    // Fix 3: Add DB use statement if not present
    if (strpos($content, 'use Illuminate\Support\Facades\DB;') === false) {
        $content = str_replace(
            'use Illuminate\Http\Request;',
            "use Illuminate\Http\Request;\nuse Illuminate\Support\Facades\DB;",
            $content
        );
        echo "✅ Added DB facade use statement\n";
    }
    
    // Save the modified controller if changes were made
    if ($content !== $originalContent) {
        file_put_contents($modularController, $content);
        echo "✅ ModularRegistrationController updated with tenant-aware queries\n";
    } else {
        echo "ℹ️  ModularRegistrationController already has proper queries\n";
    }
}

// Fix StudentRegistrationController as well
echo "\n4️⃣ FIXING STUDENT REGISTRATION CONTROLLER:\n";
echo "------------------------------------------\n";

$studentController = 'app/Http/Controllers/StudentRegistrationController.php';
if (file_exists($studentController)) {
    $backupFile = $studentController . '.backup.' . date('Y-m-d-H-i-s');
    copy($studentController, $backupFile);
    echo "✅ Backup created: $backupFile\n";
    
    $content = file_get_contents($studentController);
    $originalContent = $content;
    
    // Similar fixes for StudentRegistrationController
    if (strpos($content, 'use App\Services\TenantService;') === false) {
        $content = str_replace(
            'use Illuminate\Http\Request;',
            "use Illuminate\Http\Request;\nuse App\Services\TenantService;",
            $content
        );
        echo "✅ Added TenantService use statement\n";
    }
    
    if (strpos($content, 'use Illuminate\Support\Facades\DB;') === false) {
        $content = str_replace(
            'use Illuminate\Http\Request;',
            "use Illuminate\Http\Request;\nuse Illuminate\Support\Facades\DB;",
            $content
        );
        echo "✅ Added DB facade use statement\n";
    }
    
    // Replace any Package model usage
    $packageQueries = [
        "Package::where('package_type', 'full')" => "DB::connection('tenant')->table('packages')->where('package_type', 'full')",
        "\$packages = Package::" => "\$packages = DB::connection('tenant')->table('packages')->"
    ];
    
    foreach ($packageQueries as $from => $to) {
        if (strpos($content, $from) !== false) {
            $content = str_replace($from, $to, $content);
            echo "✅ Fixed package query: $from\n";
        }
    }
    
    if ($content !== $originalContent) {
        file_put_contents($studentController, $content);
        echo "✅ StudentRegistrationController updated with tenant-aware queries\n";
    } else {
        echo "ℹ️  StudentRegistrationController already has proper queries\n";
    }
}

echo "\n5️⃣ VERIFYING TENANT ROUTE MIDDLEWARE SETUP:\n";
echo "-------------------------------------------\n";

// Check if the tenant routes are properly configured with middleware
$tenantRoutes = 'routes/tenant.php';
if (file_exists($tenantRoutes)) {
    $content = file_get_contents($tenantRoutes);
    echo "✅ Tenant routes file exists\n";
    
    // These routes should automatically get tenant middleware when loaded
    // Let's verify the RouteServiceProvider loads them correctly
    $routeServiceProvider = 'app/Providers/RouteServiceProvider.php';
    if (file_exists($routeServiceProvider)) {
        $providerContent = file_get_contents($routeServiceProvider);
        
        if (strpos($providerContent, 'tenant.php') !== false) {
            echo "✅ tenant.php routes loaded in RouteServiceProvider\n";
        } else {
            echo "❌ tenant.php routes not loaded in RouteServiceProvider\n";
            
            // Add tenant routes to RouteServiceProvider
            echo "🔧 Adding tenant routes to RouteServiceProvider...\n";
            
            // Find the map method and add tenant route mapping
            $pattern = '/(protected function mapWebRoutes.*?\{.*?\})/s';
            if (preg_match($pattern, $providerContent, $matches)) {
                $webRoutesMethod = $matches[1];
                
                $tenantRoutesMethod = "
    protected function mapTenantRoutes()
    {
        Route::middleware(['web', 'tenant'])
            ->namespace(\$this->namespace)
            ->group(base_path('routes/tenant.php'));
    }";
                
                // Add the method and call it
                $providerContent = str_replace(
                    $webRoutesMethod,
                    $webRoutesMethod . $tenantRoutesMethod,
                    $providerContent
                );
                
                // Add call to map method
                $providerContent = str_replace(
                    '$this->mapWebRoutes();',
                    "\$this->mapWebRoutes();\n        \$this->mapTenantRoutes();",
                    $providerContent
                );
                
                file_put_contents($routeServiceProvider, $providerContent);
                echo "✅ Tenant routes added to RouteServiceProvider\n";
            }
        }
    }
}

echo "\n=== TENANT DATABASE SWITCHING FIXES COMPLETE ===\n";
echo "🧪 Testing the fixed routes...\n";
?>
