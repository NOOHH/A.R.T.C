<?php

echo "=== COMPREHENSIVE PREVIEW AND ENROLLMENT ISSUE INVESTIGATION ===\n\n";

// Issue 1: JavaScript Error Investigation
echo "1. JAVASCRIPT ERROR INVESTIGATION\n";
echo "Looking for line 2612 error in customize-website...\n";

$customizeFile = 'resources/views/smartprep/dashboard/customize-website.blade.php';
$content = file_get_contents($customizeFile);
$lines = explode("\n", $content);
$totalLines = count($lines);

echo "Total lines in customize-website.blade.php: $totalLines\n";

// Look for potential null style errors around line numbers
$checkLines = [2600, 2610, 2612, 2620, 1270, 1271, 1312, 1314];
foreach ($checkLines as $lineNum) {
    if (isset($lines[$lineNum - 1])) {
        $line = trim($lines[$lineNum - 1]);
        if (strpos($line, '.style') !== false) {
            echo "Line $lineNum: $line\n";
        }
    }
}

// Issue 2: Preview Navigation for Auth Section
echo "\n2. PREVIEW AUTH NAVIGATION INVESTIGATION\n";

// Check if preview functionality exists for auth section
$previewPattern = '/preview.*auth|auth.*preview/i';
if (preg_match($previewPattern, $content)) {
    echo "✅ Preview functionality found for auth section\n";
} else {
    echo "❌ No preview functionality found for auth section\n";
}

// Check preview URL generation
$previewUrlPattern = '/preview.*=.*true/';
if (preg_match($previewUrlPattern, $content)) {
    echo "✅ Preview URL generation found\n";
} else {
    echo "❌ Preview URL generation not found\n";
}

// Issue 3: Enrollment Routes Tenant Awareness
echo "\n3. ENROLLMENT ROUTES TENANT AWARENESS\n";

$routeFiles = ['routes/web.php', 'routes/tenant.php'];
foreach ($routeFiles as $routeFile) {
    if (file_exists($routeFile)) {
        $routeContent = file_get_contents($routeFile);
        
        echo "\nChecking $routeFile:\n";
        
        // Check for enrollment routes
        $enrollmentRoutes = [
            'enrollment/full' => strpos($routeContent, 'enrollment/full') !== false,
            'enrollment/modular' => strpos($routeContent, 'enrollment/modular') !== false,
        ];
        
        foreach ($enrollmentRoutes as $route => $found) {
            $status = $found ? '✅' : '❌';
            echo "$status $route route found in $routeFile\n";
        }
        
        // Check if routes are properly tenant-aware
        $tenantMiddleware = strpos($routeContent, "middleware(['tenant'") !== false;
        $tenantGroup = strpos($routeContent, "Route::group(['domain'") !== false;
        
        if ($tenantMiddleware || $tenantGroup) {
            echo "✅ Tenant middleware/grouping found in $routeFile\n";
        } else {
            echo "❌ No tenant awareness found in $routeFile\n";
        }
    }
}

// Issue 4: Logout Functionality for Preview Mode
echo "\n4. LOGOUT FUNCTIONALITY INVESTIGATION\n";

$logoutChecks = [
    'Logout route exists' => function() {
        $webRoutes = file_get_contents('routes/web.php');
        return strpos($webRoutes, '/logout') !== false;
    },
    'Preview mode logout' => function() {
        $customizeContent = file_get_contents('resources/views/smartprep/dashboard/customize-website.blade.php');
        return strpos($customizeContent, 'logout') !== false;
    },
    'Auth controller logout' => function() {
        $authControllers = glob('app/Http/Controllers/**/Auth*Controller.php');
        foreach ($authControllers as $controller) {
            $content = file_get_contents($controller);
            if (strpos($content, 'logout') !== false) {
                return true;
            }
        }
        return false;
    }
];

foreach ($logoutChecks as $checkName => $checkFunc) {
    $result = $checkFunc();
    $status = $result ? '✅' : '❌';
    echo "$status $checkName\n";
}

// Issue 5: Enrollment Controllers Investigation
echo "\n5. ENROLLMENT CONTROLLERS INVESTIGATION\n";

$enrollmentControllers = [
    'StudentRegistrationController' => 'app/Http/Controllers/StudentRegistrationController.php',
    'ModularRegistrationController' => 'app/Http/Controllers/ModularRegistrationController.php',
];

foreach ($enrollmentControllers as $controllerName => $path) {
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        echo "\n$controllerName Analysis:\n";
        
        // Check for tenant awareness
        $tenantChecks = [
            'uses tenant middleware' => strpos($content, "middleware('tenant'") !== false,
            'switches database' => strpos($content, 'switchTenantConnection') !== false || strpos($content, 'DB::connection') !== false,
            'has logout method' => strpos($content, 'function logout') !== false,
            'redirects properly' => strpos($content, 'redirect') !== false,
        ];
        
        foreach ($tenantChecks as $check => $result) {
            $status = $result ? '✅' : '❌';
            echo "  $status $check\n";
        }
    } else {
        echo "❌ $controllerName not found at $path\n";
    }
}

echo "\n=== SUMMARY OF ISSUES ===\n";
echo "1. JavaScript error at line 2612 - needs investigation\n";
echo "2. Auth section preview navigation not working\n";
echo "3. Enrollment routes need tenant awareness\n";
echo "4. Enrollment pages need proper logout functionality\n";
echo "5. Need to implement preview mode logout\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Fix JavaScript null reference errors\n";
echo "2. Implement auth preview navigation\n";
echo "3. Move enrollment routes to tenant-aware structure\n";
echo "4. Add logout functionality to enrollment controllers\n";
echo "5. Create preview mode logout mechanism\n";

?>
