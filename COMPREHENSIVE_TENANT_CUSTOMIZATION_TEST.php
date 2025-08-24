<?php
/**
 * Comprehensive Multi-Tenant Customization Test Suite
 * 
 * This test validates:
 * 1. Advanced settings sidebar functionality
 * 2. Login page customization with tenant awareness
 * 3. Brand settings application
 * 4. Database consistency
 * 5. Frontend JavaScript functionality
 */

echo "\n🧪 COMPREHENSIVE TENANT CUSTOMIZATION TEST\n";
echo "==========================================\n\n";

// Configuration
$baseUrl = 'http://127.0.0.1:8000';
$testWebsiteId = 15;
$tenantSlug = 'test';

// Test URLs
$urls = [
    'customize_dashboard' => "$baseUrl/smartprep/dashboard/customize-website?website=$testWebsiteId",
    'tenant_login' => "$baseUrl/t/$tenantSlug/login?website=$testWebsiteId&preview=true&t=" . time(),
    'tenant_preview' => "$baseUrl/t/draft/$tenantSlug/login?website=$testWebsiteId&preview=true&t=" . time(),
];

$results = [];

/**
 * Test 1: Verify Advanced Settings Tab Functionality
 */
function testAdvancedSettingsNavigation($baseUrl, $testWebsiteId) {
    echo "🔧 Test 1: Advanced Settings Navigation\n";
    echo "--------------------------------------\n";
    
    $results = [];
    $customizeFile = __DIR__ . '/resources/views/smartprep/dashboard/customize-website.blade.php';
    
    if (!file_exists($customizeFile)) {
        return ['❌', 'Customize website blade file not found'];
    }
    
    $content = file_get_contents($customizeFile);
    
    // Check for navigation tabs
    $navTabsChecks = [
        'permissions_tab' => strpos($content, 'data-section="permissions"') !== false,
        'advanced_tab' => strpos($content, 'data-section="advanced"') !== false,
        'permissions_section' => strpos($content, 'id="permissions-settings"') !== false,
        'advanced_section' => strpos($content, 'id="advanced-settings"') !== false,
    ];
    
    // Check JavaScript functionality
    $jsChecks = [
        'showSection_function' => strpos($content, 'function showSection(sectionId)') !== false,
        'tab_click_handler' => strpos($content, 'addEventListener(\'click\', function()') !== false,
        'section_switching' => strpos($content, 'section + \'-settings\'') !== false,
    ];
    
    $issues = [];
    foreach ($navTabsChecks as $check => $passed) {
        echo ($passed ? "✅" : "❌") . " Navigation: $check\n";
        if (!$passed) $issues[] = $check;
    }
    
    foreach ($jsChecks as $check => $passed) {
        echo ($passed ? "✅" : "❌") . " JavaScript: $check\n";
        if (!$passed) $issues[] = $check;
    }
    
    echo "\n";
    return empty($issues) ? ['✅', 'All navigation tests passed'] : ['❌', 'Issues: ' . implode(', ', $issues)];
}

/**
 * Test 2: Login Page Tenant Customization
 */
function testLoginCustomization($baseUrl, $testWebsiteId, $tenantSlug) {
    echo "🎨 Test 2: Login Page Customization\n";
    echo "----------------------------------\n";
    
    $loginFile = __DIR__ . '/resources/views/Login/login.blade.php';
    
    if (!file_exists($loginFile)) {
        echo "❌ Login template not found\n\n";
        return ['❌', 'Login template not found'];
    }
    
    $content = file_get_contents($loginFile);
    
    // Check for customizable elements
    $customizationChecks = [
        'review_text_div' => strpos($content, 'class="review-text"') !== false,
        'brand_text_dynamic' => strpos($content, 'class="brand-text"') !== false,
        'logo_dynamic' => strpos($content, 'getLogoUrl()') !== false,
        'settings_helper' => strpos($content, 'SettingsHelper::getSettings()') !== false,
        'tenant_awareness' => strpos($content, '$tenantSlug') !== false,
    ];
    
    $issues = [];
    foreach ($customizationChecks as $check => $passed) {
        echo ($passed ? "✅" : "❌") . " Login customization: $check\n";
        if (!$passed) $issues[] = $check;
    }
    
    // Test live login page
    $testUrl = "$baseUrl/t/draft/$tenantSlug/login?website=$testWebsiteId&preview=true&t=" . time();
    echo "\n🌐 Testing live login page: $testUrl\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Customization-Test/1.0');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        echo "✅ Login page loads successfully\n";
        
        // Check for customizable elements in the response
        $liveChecks = [
            'left_panel' => strpos($response, 'class="left"') !== false,
            'review_text' => strpos($response, 'Review Smarter') !== false,
            'brand_logo' => strpos($response, 'alt="Logo"') !== false,
            'brand_text' => strpos($response, 'class="brand-text"') !== false,
        ];
        
        foreach ($liveChecks as $check => $found) {
            echo ($found ? "✅" : "❌") . " Live check: $check\n";
            if (!$found) $issues[] = "live_$check";
        }
    } else {
        echo "❌ Login page failed to load (HTTP: $httpCode)\n";
        $issues[] = 'page_load_failed';
    }
    
    echo "\n";
    return empty($issues) ? ['✅', 'Login customization tests passed'] : ['❌', 'Issues: ' . implode(', ', $issues)];
}

/**
 * Test 3: Database Settings Consistency
 */
function testDatabaseConsistency() {
    echo "🗄️ Test 3: Database Settings Consistency\n";
    echo "---------------------------------------\n";
    
    try {
        // Check if required tables exist
        $pdo = new PDO('mysql:host=localhost;dbname=artc_main', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $tables = ['websites', 'tenants'];
        $issues = [];
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Table exists: $table\n";
            } else {
                echo "❌ Table missing: $table\n";
                $issues[] = "missing_table_$table";
            }
        }
        
        // Check website settings structure
        $stmt = $pdo->query("SELECT id, name, slug, settings FROM websites WHERE id = 15 LIMIT 1");
        $website = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($website) {
            echo "✅ Test website found (ID: 15)\n";
            
            $settings = json_decode($website['settings'] ?? '{}', true);
            $expectedSections = ['general', 'branding', 'navbar', 'homepage', 'auth'];
            
            foreach ($expectedSections as $section) {
                if (isset($settings[$section])) {
                    echo "✅ Settings section exists: $section\n";
                } else {
                    echo "⚠️ Settings section missing: $section\n";
                }
            }
        } else {
            echo "❌ Test website not found\n";
            $issues[] = 'test_website_missing';
        }
        
        echo "\n";
        return empty($issues) ? ['✅', 'Database consistency passed'] : ['⚠️', 'Minor issues: ' . implode(', ', $issues)];
        
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "\n\n";
        return ['❌', 'Database connection failed'];
    }
}

/**
 * Test 4: Controller Methods and Routes
 */
function testControllerAndRoutes($baseUrl, $testWebsiteId) {
    echo "🛣️ Test 4: Controller Methods and Routes\n";
    echo "--------------------------------------\n";
    
    $controllerFile = __DIR__ . '/app/Http/Controllers/Smartprep/Dashboard/CustomizeWebsiteController.php';
    
    if (!file_exists($controllerFile)) {
        echo "❌ CustomizeWebsiteController not found\n\n";
        return ['❌', 'Controller not found'];
    }
    
    $content = file_get_contents($controllerFile);
    
    // Check for required methods
    $methods = [
        'updateAdvanced' => strpos($content, 'function updateAdvanced') !== false,
        'updateAuth' => strpos($content, 'function updateAuth') !== false,
        'updateTenantSettings' => strpos($content, 'function updateTenantSettings') !== false,
        'updateNavbar' => strpos($content, 'function updateNavbar') !== false,
    ];
    
    $issues = [];
    foreach ($methods as $method => $exists) {
        echo ($exists ? "✅" : "❌") . " Controller method: $method\n";
        if (!$exists) $issues[] = $method;
    }
    
    // Test route accessibility
    $routeTests = [
        'customize_page' => "$baseUrl/smartprep/dashboard/customize-website?website=$testWebsiteId",
    ];
    
    foreach ($routeTests as $routeName => $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 || $httpCode === 302) {
            echo "✅ Route accessible: $routeName\n";
        } else {
            echo "❌ Route issue: $routeName (HTTP: $httpCode)\n";
            $issues[] = "route_$routeName";
        }
    }
    
    echo "\n";
    return empty($issues) ? ['✅', 'Controller and routes passed'] : ['❌', 'Issues: ' . implode(', ', $issues)];
}

/**
 * Test 5: CSS and JavaScript Integration
 */
function testFrontendIntegration($baseUrl, $testWebsiteId) {
    echo "🎭 Test 5: Frontend Integration\n";
    echo "------------------------------\n";
    
    $testUrl = "$baseUrl/smartprep/dashboard/customize-website?website=$testWebsiteId";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $issues = [];
    
    if ($httpCode === 200 && $response) {
        echo "✅ Customize page loads\n";
        
        // Check for essential elements
        $frontendChecks = [
            'settings_nav_tabs' => strpos($response, 'settings-nav-tab') !== false,
            'permissions_tab' => strpos($response, 'data-section="permissions"') !== false,
            'advanced_tab' => strpos($response, 'data-section="advanced"') !== false,
            'javascript_functions' => strpos($response, 'showSection') !== false,
            'color_pickers' => strpos($response, 'type="color"') !== false,
            'csrf_token' => strpos($response, 'csrf_token') !== false,
        ];
        
        foreach ($frontendChecks as $check => $found) {
            echo ($found ? "✅" : "❌") . " Frontend: $check\n";
            if (!$found) $issues[] = $check;
        }
    } else {
        echo "❌ Customize page failed to load (HTTP: $httpCode)\n";
        $issues[] = 'page_load_failed';
    }
    
    echo "\n";
    return empty($issues) ? ['✅', 'Frontend integration passed'] : ['❌', 'Issues: ' . implode(', ', $issues)];
}

// Run all tests
echo "🚀 Starting Comprehensive Test Suite...\n\n";

$testResults = [
    'Advanced Settings Navigation' => testAdvancedSettingsNavigation($baseUrl, $testWebsiteId),
    'Login Page Customization' => testLoginCustomization($baseUrl, $testWebsiteId, $tenantSlug),
    'Database Consistency' => testDatabaseConsistency(),
    'Controller and Routes' => testControllerAndRoutes($baseUrl, $testWebsiteId),
    'Frontend Integration' => testFrontendIntegration($baseUrl, $testWebsiteId),
];

// Summary
echo "📊 TEST SUMMARY\n";
echo "===============\n";

$passed = 0;
$failed = 0;
$warnings = 0;

foreach ($testResults as $testName => $result) {
    echo $result[0] . " $testName: " . $result[1] . "\n";
    if ($result[0] === '✅') $passed++;
    elseif ($result[0] === '⚠️') $warnings++;
    else $failed++;
}

echo "\n";
echo "Passed: $passed | Warnings: $warnings | Failed: $failed\n";

if ($failed > 0) {
    echo "\n❌ CRITICAL ISSUES FOUND - FIXES REQUIRED\n";
    exit(1);
} elseif ($warnings > 0) {
    echo "\n⚠️ MINOR ISSUES FOUND - IMPROVEMENTS RECOMMENDED\n";
} else {
    echo "\n✅ ALL TESTS PASSED - SYSTEM WORKING CORRECTLY\n";
}

echo "\n🔧 Next Steps:\n";
echo "1. Fix any critical issues identified\n";
echo "2. Test the fixed functionality\n";
echo "3. Verify tenant-specific customizations\n";
echo "4. Test color and text customization\n\n";
?>
