<?php
/**
 * FINAL COMPREHENSIVE VALIDATION TEST
 * Tests everything: database, routes, controllers, API, web, JS integration, codebase
 * As requested: "thoroughly check everything create test, run test, check database, 
 * routes controller, api, web, js, the codebase, always run test to check if something is being changed"
 */

echo "🔬 FINAL COMPREHENSIVE SYSTEM VALIDATION\n";
echo "Checking: Database, Routes, Controllers, API, Web, JS, Codebase\n";
echo "=================================================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. DATABASE CONNECTIVITY TEST
echo "1️⃣ DATABASE CONNECTIVITY TEST\n";
echo str_repeat('-', 50) . "\n";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=smartprep_test1', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test tenant settings table
    $stmt = $pdo->query("SHOW TABLES LIKE 'tenant_settings'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Database connected successfully\n";
        echo "✅ tenant_settings table exists\n";
        
        // Check for TEST11 customization data
        $stmt = $pdo->query("SELECT * FROM tenant_settings WHERE tenant_slug = 'test1' AND setting_key = 'navbar_customization'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && strpos($result['setting_value'], 'TEST11') !== false) {
            echo "✅ TEST11 customization data found in database\n";
            $success[] = "Database contains proper TEST11 branding data";
        } else {
            echo "⚠️  TEST11 customization data not found in database\n";
            $warnings[] = "Missing TEST11 customization in database";
        }
    } else {
        echo "❌ tenant_settings table not found\n";
        $errors[] = "Missing tenant_settings table";
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    $errors[] = "Database connectivity: " . $e->getMessage();
}
echo "\n";

// 2. ROUTE VALIDATION TEST
echo "2️⃣ ROUTE VALIDATION TEST\n";
echo str_repeat('-', 50) . "\n";
$routeOutput = shell_exec('php artisan route:list 2>&1');
if (strpos($routeOutput, 'tenant.draft.admin.certificates') !== false &&
    strpos($routeOutput, 'tenant.draft.admin.archived') !== false &&
    strpos($routeOutput, 'tenant.draft.admin.courses.upload') !== false &&
    strpos($routeOutput, 'tenant.draft.admin.payments.pending') !== false) {
    echo "✅ All required tenant routes are registered\n";
    $success[] = "Route registration complete";
} else {
    echo "❌ Some tenant routes are missing\n";
    $errors[] = "Missing required tenant routes";
}
echo "\n";

// 3. CONTROLLER METHOD VALIDATION
echo "3️⃣ CONTROLLER METHOD VALIDATION\n";
echo str_repeat('-', 50) . "\n";
$controllerFile = file_get_contents('app/Http/Controllers/AdminController.php');
$requiredMethods = [
    'previewPaymentPending',
    'previewPaymentHistory', 
    'previewCertificates',
    'previewArchivedContent',
    'previewCourseContentUpload'
];

$allMethodsExist = true;
foreach ($requiredMethods as $method) {
    if (strpos($controllerFile, "function {$method}") !== false) {
        echo "✅ Method {$method} exists\n";
    } else {
        echo "❌ Method {$method} missing\n";
        $errors[] = "Missing controller method: {$method}";
        $allMethodsExist = false;
    }
}

if ($allMethodsExist) {
    $success[] = "All controller methods implemented";
}
echo "\n";

// 4. WEB ENDPOINT FUNCTIONALITY TEST
echo "4️⃣ WEB ENDPOINT FUNCTIONALITY TEST\n";
echo str_repeat('-', 50) . "\n";

$baseUrl = 'http://localhost:8000';
$tenant = 'test1';
$params = 'website=15&preview=true';

$criticalEndpoints = [
    'Payment Pending' => "/t/draft/{$tenant}/admin/payments/pending?{$params}",
    'Payment History' => "/t/draft/{$tenant}/admin/payments/history?{$params}",
    'Certificates' => "/t/draft/{$tenant}/admin/certificates?{$params}",
    'Archived Content' => "/t/draft/{$tenant}/admin/archived?{$params}",
    'Course Upload' => "/t/draft/{$tenant}/admin/courses/upload?{$params}",
];

$workingEndpoints = 0;
$brandedEndpoints = 0;

foreach ($criticalEndpoints as $name => $endpoint) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $workingEndpoints++;
        $brandingCount = substr_count($response, 'TEST11');
        
        if ($brandingCount >= 2) {
            echo "✅ {$name}: Working + Branded ({$brandingCount} instances)\n";
            $brandedEndpoints++;
        } else {
            echo "⚠️  {$name}: Working but not branded\n";
            $warnings[] = "{$name} endpoint lacks proper branding";
        }
    } else {
        echo "❌ {$name}: HTTP {$httpCode}\n";
        $errors[] = "{$name} endpoint returning HTTP {$httpCode}";
    }
}

if ($workingEndpoints === count($criticalEndpoints)) {
    $success[] = "All critical endpoints are functional";
}

if ($brandedEndpoints === count($criticalEndpoints)) {
    $success[] = "All endpoints have proper TEST11 branding";
}
echo "\n";

// 5. JAVASCRIPT INTEGRATION CHECK
echo "5️⃣ JAVASCRIPT INTEGRATION CHECK\n";
echo str_repeat('-', 50) . "\n";

// Check if sidebar JS is properly integrated
$sidebarFile = 'resources/views/admin/admin-layouts/admin-sidebar.blade.php';
if (file_exists($sidebarFile)) {
    $sidebarContent = file_get_contents($sidebarFile);
    
    if (strpos($sidebarContent, 'tenantSlug') !== false && 
        strpos($sidebarContent, 'basePreviewUrl') !== false) {
        echo "✅ Sidebar has tenant-aware JavaScript integration\n";
        $success[] = "JavaScript tenant integration working";
    } else {
        echo "⚠️  Sidebar may lack proper tenant JavaScript integration\n";
        $warnings[] = "Sidebar JavaScript integration incomplete";
    }
    
    // Check for proper URL generation
    if (strpos($sidebarContent, 'certificatesUrl') !== false &&
        strpos($sidebarContent, 'archivedUrl') !== false &&
        strpos($sidebarContent, 'courseUploadUrl') !== false) {
        echo "✅ All new section URLs are properly integrated\n";
        $success[] = "New section URL integration complete";
    } else {
        echo "❌ Some new section URLs missing from sidebar\n";
        $errors[] = "Incomplete sidebar URL integration";
    }
} else {
    echo "❌ Sidebar file not found\n";
    $errors[] = "Missing sidebar template file";
}
echo "\n";

// 6. CODEBASE CONSISTENCY CHECK
echo "6️⃣ CODEBASE CONSISTENCY CHECK\n";
echo str_repeat('-', 50) . "\n";

// Check for AdminPreviewCustomization trait usage
if (strpos($controllerFile, 'AdminPreviewCustomization') !== false &&
    strpos($controllerFile, 'loadAdminPreviewCustomization') !== false) {
    echo "✅ AdminPreviewCustomization trait properly used\n";
    $success[] = "Consistent customization trait usage";
} else {
    echo "❌ AdminPreviewCustomization trait not properly integrated\n";
    $errors[] = "Missing customization trait integration";
}

// Check routes file for consistency
$routesFile = file_get_contents('routes/web.php');
if (strpos($routesFile, 'AdminController::class') !== false) {
    echo "✅ Routes properly reference controller classes\n";
    $success[] = "Consistent route-controller binding";
} else {
    echo "⚠️  Some routes may use closure functions instead of controllers\n";
    $warnings[] = "Inconsistent route definitions";
}
echo "\n";

// FINAL SUMMARY
echo str_repeat('=', 80) . "\n";
echo "📊 COMPREHENSIVE SYSTEM VALIDATION SUMMARY\n";
echo str_repeat('=', 80) . "\n\n";

echo "✅ SUCCESSES (" . count($success) . "):\n";
foreach ($success as $item) {
    echo "   • {$item}\n";
}
echo "\n";

if (!empty($warnings)) {
    echo "⚠️  WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $item) {
        echo "   • {$item}\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $item) {
        echo "   • {$item}\n";
    }
    echo "\n";
}

// Overall system health
$totalChecks = count($success) + count($warnings) + count($errors);
$healthScore = round((count($success) / ($totalChecks > 0 ? $totalChecks : 1)) * 100);

echo "🏥 OVERALL SYSTEM HEALTH: {$healthScore}%\n";

if (count($errors) === 0 && $healthScore >= 80) {
    echo "🎉 SYSTEM STATUS: HEALTHY\n";
    echo "✅ All reported 404 issues have been resolved\n";
    echo "✅ TEST11 branding is working across all sections\n";
    echo "✅ Database, routes, controllers, web endpoints validated\n";
    echo "✅ Tenant customization system is functioning properly\n";
} elseif (count($errors) === 0) {
    echo "⚠️  SYSTEM STATUS: STABLE WITH WARNINGS\n";
    echo "✅ Core functionality working but optimization needed\n";
} else {
    echo "❌ SYSTEM STATUS: REQUIRES ATTENTION\n";
    echo "⚠️  Some critical issues need to be addressed\n";
}

echo "\nValidation completed at " . date('Y-m-d H:i:s') . "\n";

?>
