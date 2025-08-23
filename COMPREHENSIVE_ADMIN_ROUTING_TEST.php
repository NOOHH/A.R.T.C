<?php
echo "🔍 COMPREHENSIVE ADMIN ROUTING & DATABASE TEST\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Test 1: Check Laravel development server status
echo "📡 TEST 1: Laravel Development Server Status\n";
$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'ignore_errors' => true
    ]
]);

$serverTest = @file_get_contents('http://127.0.0.1:8000', false, $context);
if ($serverTest !== false) {
    echo "✅ Laravel development server is running\n";
} else {
    echo "❌ Laravel development server is NOT running\n";
}
echo "\n";

// Test 2: Check specific admin routes
echo "🎯 TEST 2: Admin Route Accessibility Test\n";
$adminRoutes = [
    'quiz-generator' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/quiz-generator',
    'courses-upload' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/courses/upload',
    'modules-archived' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/modules/archived'
];

foreach ($adminRoutes as $name => $url) {
    echo "Testing: $name -> $url\n";
    $response = @file_get_contents($url, false, $context);
    $httpCode = 200;
    
    if (isset($http_response_header)) {
        foreach ($http_response_header as $header) {
            if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                $httpCode = $matches[1];
                break;
            }
        }
    }
    
    if ($response !== false && $httpCode == 200) {
        echo "✅ $name: Accessible (HTTP $httpCode)\n";
        
        // Check for database errors in response
        if (strpos($response, 'SQLSTATE') !== false) {
            echo "⚠️  Database error detected in response\n";
            preg_match('/SQLSTATE\[.*?\]: (.*)/', $response, $matches);
            if (isset($matches[1])) {
                echo "   Error: " . trim($matches[1]) . "\n";
            }
        }
        
        // Check for specific content
        if (strpos($response, 'SmartPrep Learning Center') !== false) {
            echo "✅ Custom branding detected\n";
        }
        
    } elseif ($httpCode == 302) {
        echo "🔄 $name: Redirecting (HTTP $httpCode)\n";
    } elseif ($httpCode == 404) {
        echo "❌ $name: Route not found (HTTP $httpCode)\n";
    } else {
        echo "❌ $name: Error accessing route (HTTP $httpCode)\n";
    }
    echo "\n";
}

// Test 3: Database connection test
echo "🗄️ TEST 3: Database Connection Test\n";
try {
    // Test main database connection
    $mainDb = new PDO('mysql:host=localhost;dbname=artc_management_system', 'root', '');
    $mainDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Main database (artc_management_system) connection successful\n";
    
    // Test smartprep tenant database
    $tenantDb = new PDO('mysql:host=localhost;dbname=smartprep_artc', 'root', '');
    $tenantDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Tenant database (smartprep_artc) connection successful\n";
    
    // Check admin_settings table
    $stmt = $tenantDb->query("SHOW TABLES LIKE 'admin_settings'");
    if ($stmt->rowCount() > 0) {
        echo "✅ admin_settings table exists in tenant database\n";
        
        // Check for director_view_students setting
        $stmt = $tenantDb->prepare("SELECT * FROM admin_settings WHERE setting_key = 'director_view_students'");
        $stmt->execute();
        $setting = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($setting) {
            echo "✅ director_view_students setting found: " . json_encode($setting) . "\n";
        } else {
            echo "⚠️  director_view_students setting NOT found\n";
        }
    } else {
        echo "❌ admin_settings table does NOT exist in tenant database\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Check Laravel routes
echo "🛣️ TEST 4: Laravel Route Check\n";
echo "Checking if routes are properly registered...\n";

$routeOutput = shell_exec('cd C:\xampp\htdocs\A.R.T.C && php artisan route:list --compact 2>&1');
if ($routeOutput) {
    echo "✅ Route list command executed\n";
    
    // Check for specific admin routes
    $requiredRoutes = [
        't/{tenant}/admin/quiz-generator',
        't/{tenant}/admin/courses/upload', 
        't/{tenant}/admin/modules/archived'
    ];
    
    foreach ($requiredRoutes as $route) {
        if (strpos($routeOutput, $route) !== false) {
            echo "✅ Route found: $route\n";
        } else {
            echo "❌ Route NOT found: $route\n";
        }
    }
} else {
    echo "❌ Could not execute route list command\n";
}
echo "\n";

// Test 5: Check TenantService functionality
echo "🏢 TEST 5: TenantService Database Switching Test\n";
$testScript = '
<?php
require_once "vendor/autoload.php";

$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\TenantService;

try {
    echo "Testing TenantService database switching...\n";
    
    $tenantService = new TenantService();
    $result = $tenantService->switchToTenantDatabase("smartprep");
    
    if ($result) {
        echo "✅ Successfully switched to smartprep database\n";
        
        // Test database query
        $settings = DB::table("admin_settings")->where("setting_key", "director_view_students")->first();
        if ($settings) {
            echo "✅ Successfully queried admin_settings table\n";
        } else {
            echo "⚠️  admin_settings query returned no results\n";
        }
    } else {
        echo "❌ Failed to switch to smartprep database\n";
    }
    
} catch (Exception $e) {
    echo "❌ TenantService test error: " . $e->getMessage() . "\n";
}
?>';

file_put_contents('temp_tenant_test.php', $testScript);
$tenantTestOutput = shell_exec('cd C:\xampp\htdocs\A.R.T.C && php temp_tenant_test.php 2>&1');
echo $tenantTestOutput;
unlink('temp_tenant_test.php');
echo "\n";

// Summary
echo "📊 COMPREHENSIVE TEST SUMMARY\n";
echo "=" . str_repeat("=", 30) . "\n";
echo "✅ Server Status: Checked\n";
echo "✅ Route Accessibility: Tested\n";
echo "✅ Database Connections: Verified\n";
echo "✅ Laravel Routes: Inspected\n";
echo "✅ TenantService: Validated\n";
echo "\n🎯 Check the results above for specific issues to address!\n";
?>
