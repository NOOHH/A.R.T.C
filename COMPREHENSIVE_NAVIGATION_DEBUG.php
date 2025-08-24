<?php
/**
 * COMPREHENSIVE NAVIGATION DEBUG - Deep investigation of customize website navigation issues
 * This script performs extensive testing of all components that could affect navigation visibility
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "=== COMPREHENSIVE NAVIGATION DEBUG SYSTEM ===\n";
echo "Investigating why navigation sections are not showing\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

// Test configuration
$testUrl = 'http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=15';
$cookieFile = __DIR__ . '/debug_cookies.txt';

// Initialize results tracking
$results = [
    'route_tests' => [],
    'controller_tests' => [],
    'view_tests' => [],
    'javascript_tests' => [],
    'database_tests' => [],
    'api_tests' => [],
    'integration_tests' => []
];

echo "1. === ROUTE SYSTEM TESTING ===\n";

// Test 1: Route existence and accessibility
echo "Testing route accessibility...\n";
$routeTests = [
    'customize-main' => 'http://127.0.0.1:8000/smartprep/dashboard/customize-website',
    'customize-with-website' => 'http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=15',
    'dashboard-base' => 'http://127.0.0.1:8000/smartprep/dashboard'
];

foreach ($routeTests as $testName => $url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_NOBODY => true,
        CURLOPT_HEADER => true
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    
    $results['route_tests'][$testName] = [
        'status' => $httpCode,
        'accessible' => $httpCode === 200,
        'redirected' => $finalUrl !== $url,
        'final_url' => $finalUrl
    ];
    
    echo "  $testName: HTTP $httpCode " . ($httpCode === 200 ? "✓" : "✗") . "\n";
    if ($finalUrl !== $url) {
        echo "    Redirected to: $finalUrl\n";
    }
    
    curl_close($ch);
}

echo "\n2. === CONTROLLER & VIEW TESTING ===\n";

// Test 2: Controller method exists and responds
echo "Testing controller method...\n";
$controllerFile = __DIR__ . '/app/Http/Controllers/Smartprep/Dashboard/CustomizeWebsiteController.php';
if (file_exists($controllerFile)) {
    $controllerContent = file_get_contents($controllerFile);
    
    echo "✓ Controller file exists\n";
    
    $methods = ['current', 'index', 'show'];
    foreach ($methods as $method) {
        if (strpos($controllerContent, "function $method") !== false) {
            echo "  ✓ Method '$method' found\n";
            $results['controller_tests'][$method] = true;
        } else {
            echo "  ✗ Method '$method' missing\n";
            $results['controller_tests'][$method] = false;
        }
    }
    
    // Check for view return statements
    if (strpos($controllerContent, 'return view(') !== false) {
        echo "  ✓ View return statements found\n";
    } else {
        echo "  ✗ No view return statements\n";
    }
} else {
    echo "✗ Controller file not found: $controllerFile\n";
}

// Test 3: View file structure
echo "\nTesting view files...\n";
$viewPaths = [
    'main-view' => __DIR__ . '/resources/views/smartprep/dashboard/customize-website.blade.php',
    'scripts-partial' => __DIR__ . '/resources/views/smartprep/dashboard/partials/customize-scripts.blade.php'
];

foreach ($viewPaths as $name => $path) {
    if (file_exists($path)) {
        echo "✓ $name exists\n";
        $content = file_get_contents($path);
        
        // Check for navigation elements
        $navChecks = [
            'settings-nav-tab' => strpos($content, 'settings-nav-tab') !== false,
            'data-section' => strpos($content, 'data-section') !== false,
            'auth-settings' => strpos($content, 'auth-settings') !== false,
            'permissions-settings' => strpos($content, 'permissions-settings') !== false,
            'addEventListener' => strpos($content, 'addEventListener') !== false
        ];
        
        foreach ($navChecks as $check => $found) {
            echo "  $check: " . ($found ? "✓" : "✗") . "\n";
        }
        
        $results['view_tests'][$name] = $navChecks;
    } else {
        echo "✗ $name not found: $path\n";
        $results['view_tests'][$name] = false;
    }
}

echo "\n3. === JAVASCRIPT & ASSETS TESTING ===\n";

// Test 4: JavaScript functionality
echo "Testing JavaScript assets...\n";
$jsFiles = [
    'app.js' => __DIR__ . '/public/js/app.js',
    'mix-manifest' => __DIR__ . '/public/mix-manifest.json'
];

foreach ($jsFiles as $name => $path) {
    if (file_exists($path)) {
        echo "✓ $name exists\n";
        $results['javascript_tests'][$name] = true;
    } else {
        echo "✗ $name missing: $path\n";
        $results['javascript_tests'][$name] = false;
    }
}

// Test 5: Live page content analysis
echo "\n4. === LIVE PAGE CONTENT ANALYSIS ===\n";
echo "Fetching live page content...\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $testUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_USERAGENT => 'Navigation Debug Bot/1.0'
]);

$pageContent = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200 && $pageContent) {
    echo "✓ Page loaded successfully\n";
    
    // Analyze page content
    $contentChecks = [
        'has_html_structure' => strpos($pageContent, '<html') !== false,
        'has_nav_tabs' => strpos($pageContent, 'settings-nav-tab') !== false,
        'has_sections' => strpos($pageContent, 'sidebar-section') !== false,
        'has_auth_section' => strpos($pageContent, 'auth-settings') !== false,
        'has_permissions_section' => strpos($pageContent, 'permissions-settings') !== false,
        'has_javascript' => strpos($pageContent, 'addEventListener') !== false,
        'has_csrf_token' => strpos($pageContent, 'csrf-token') !== false,
        'has_errors' => strpos($pageContent, 'error') !== false || strpos($pageContent, 'Error') !== false,
        'is_login_redirect' => strpos($pageContent, 'login') !== false && strpos($pageContent, 'password') !== false && !strpos($pageContent, 'settings-nav-tab')
    ];
    
    foreach ($contentChecks as $check => $result) {
        echo "  $check: " . ($result ? "✓" : "✗") . "\n";
    }
    
    $results['integration_tests']['page_content'] = $contentChecks;
    
    // Extract specific elements for debugging
    if (preg_match_all('/<button[^>]*settings-nav-tab[^>]*>(.*?)<\/button>/s', $pageContent, $matches)) {
        echo "\nFound navigation tabs:\n";
        foreach ($matches[1] as $tabContent) {
            echo "  - " . strip_tags($tabContent) . "\n";
        }
    } else {
        echo "\n✗ No navigation tabs found in HTML\n";
    }
    
    // Check for section divs
    if (preg_match_all('/id="([^"]*-settings)"/', $pageContent, $matches)) {
        echo "\nFound section IDs:\n";
        foreach ($matches[1] as $sectionId) {
            echo "  - $sectionId\n";
        }
    } else {
        echo "\n✗ No section IDs found\n";
    }
    
} else {
    echo "✗ Failed to load page. HTTP: $httpCode\n";
    if ($httpCode === 302 || $httpCode === 301) {
        echo "  Page is redirecting - authentication issue?\n";
    }
}

curl_close($ch);

echo "\n5. === DATABASE TESTING ===\n";

// Test 6: Database connectivity and data
echo "Testing database connectivity...\n";
try {
    // Test database connection
    $dbTestScript = "
    <?php
    require_once '" . __DIR__ . "/vendor/autoload.php';
    \$app = require_once '" . __DIR__ . "/bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\\Contracts\\Console\\Kernel::class);
    \$kernel->bootstrap();
    
    try {
        \$websites = DB::table('websites')->where('id', 15)->first();
        if (\$websites) {
            echo 'Database connection: ✓' . PHP_EOL;
            echo 'Website ID 15 exists: ✓' . PHP_EOL;
            echo 'Website name: ' . \$websites->name . PHP_EOL;
        } else {
            echo 'Database connection: ✓' . PHP_EOL;
            echo 'Website ID 15 exists: ✗' . PHP_EOL;
        }
    } catch (Exception \$e) {
        echo 'Database error: ' . \$e->getMessage() . PHP_EOL;
    }
    ?>";
    
    file_put_contents(__DIR__ . '/temp_db_test.php', $dbTestScript);
    $dbOutput = shell_exec('cd ' . __DIR__ . ' && php temp_db_test.php');
    echo $dbOutput;
    unlink(__DIR__ . '/temp_db_test.php');
    
} catch (Exception $e) {
    echo "✗ Database test failed: " . $e->getMessage() . "\n";
}

echo "\n6. === API ENDPOINT TESTING ===\n";

// Test 7: API endpoints related to settings
$apiEndpoints = [
    'general' => 'http://127.0.0.1:8000/smartprep/dashboard/settings/general/15',
    'branding' => 'http://127.0.0.1:8000/smartprep/dashboard/settings/branding/15',
    'auth' => 'http://127.0.0.1:8000/smartprep/dashboard/settings/auth/15'
];

foreach ($apiEndpoints as $endpoint => $url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_NOBODY => true,
        CURLOPT_TIMEOUT => 5
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "API $endpoint: HTTP $httpCode " . ($httpCode < 500 ? "✓" : "✗") . "\n";
    $results['api_tests'][$endpoint] = $httpCode;
    
    curl_close($ch);
}

echo "\n7. === CACHE & CONFIGURATION TESTING ===\n";

// Test 8: Laravel cache and config status
echo "Testing Laravel cache status...\n";
$cacheCommands = [
    'route:clear' => 'php artisan route:clear',
    'config:clear' => 'php artisan config:clear',
    'view:clear' => 'php artisan view:clear',
    'cache:clear' => 'php artisan cache:clear'
];

foreach ($cacheCommands as $name => $command) {
    $output = shell_exec("cd " . __DIR__ . " && $command 2>&1");
    $success = strpos($output, 'successfully') !== false || strpos($output, 'cleared') !== false;
    echo "$name: " . ($success ? "✓" : "✗") . "\n";
    if (!$success) {
        echo "  Output: " . trim($output) . "\n";
    }
}

echo "\n=== DIAGNOSTIC SUMMARY ===\n";

// Generate diagnostic report
$issues = [];
if (!$results['route_tests']['customize-with-website']['accessible']) {
    $issues[] = "Route not accessible - check authentication/middleware";
}

if (!isset($results['view_tests']['main-view']['settings-nav-tab']) || !$results['view_tests']['main-view']['settings-nav-tab']) {
    $issues[] = "Navigation tabs missing from view template";
}

if (!isset($results['view_tests']['main-view']['addEventListener']) || !$results['view_tests']['main-view']['addEventListener']) {
    $issues[] = "JavaScript event listeners missing";
}

if (isset($results['integration_tests']['page_content']['is_login_redirect']) && $results['integration_tests']['page_content']['is_login_redirect']) {
    $issues[] = "Page redirecting to login - authentication required";
}

if (empty($issues)) {
    echo "✓ No critical issues detected\n";
} else {
    echo "✗ Issues found:\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
}

echo "\n=== RECOMMENDED ACTIONS ===\n";
echo "1. Check browser console for JavaScript errors\n";
echo "2. Verify user authentication status\n";
echo "3. Ensure all caches are cleared\n";
echo "4. Check if middleware is blocking access\n";
echo "5. Verify view template compilation\n";

// Cleanup
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

echo "\nDiagnostic completed: " . date('Y-m-d H:i:s') . "\n";
?>
