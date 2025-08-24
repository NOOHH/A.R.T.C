<?php
/**
 * NAVIGATION FUNCTIONALITY TEST - Test all navigation features
 * This script tests the actual navigation functionality and section visibility
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "=== NAVIGATION FUNCTIONALITY TEST ===\n";
echo "Testing all navigation sections and functionality\n\n";

$testUrl = 'http://127.0.0.1:8000/test-navigation';
$cookieFile = __DIR__ . '/nav_test_cookies.txt';

// Initialize cURL
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_USERAGENT => 'Navigation Test Bot/1.0'
]);

try {
    // Get the test navigation page
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode !== 200) {
        throw new Exception("Failed to load test navigation page. HTTP Code: $httpCode");
    }
    
    echo "✓ Test navigation page loaded successfully\n\n";
    
    // Parse HTML
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($response);
    libxml_clear_errors();
    
    $xpath = new DOMXPath($dom);
    
    echo "1. === NAVIGATION TABS VERIFICATION ===\n";
    
    $expectedTabs = [
        'general' => 'General',
        'branding' => 'Branding',
        'navbar' => 'Navigation', 
        'homepage' => 'Homepage',
        'student' => 'Student Portal',
        'professor' => 'Professor Panel',
        'admin' => 'Admin Panel',
        'auth' => 'Auth',
        'permissions' => 'Permissions',
        'advanced' => 'Advanced'
    ];
    
    $foundTabs = [];
    foreach ($expectedTabs as $section => $label) {
        $tabXPath = "//button[contains(@class, 'settings-nav-tab')][@data-section='$section']";
        $tabNodes = $xpath->query($tabXPath);
        
        if ($tabNodes->length > 0) {
            echo "✓ Found '$label' tab (data-section='$section')\n";
            $foundTabs[$section] = true;
        } else {
            echo "✗ Missing '$label' tab (data-section='$section')\n";
            $foundTabs[$section] = false;
        }
    }
    
    echo "\n2. === SECTION CONTENT VERIFICATION ===\n";
    
    $foundSections = [];
    foreach ($expectedTabs as $section => $label) {
        $sectionId = $section . '-settings';
        $sectionXPath = "//div[@id='$sectionId']";
        $sectionNodes = $xpath->query($sectionXPath);
        
        if ($sectionNodes->length > 0) {
            echo "✓ Found section '$sectionId'\n";
            $foundSections[$section] = true;
            
            // Check if section has visibility style
            $sectionNode = $sectionNodes->item(0);
            $style = $sectionNode->getAttribute('style');
            if (strpos($style, 'display: none') !== false) {
                echo "  ⚠ Section hidden by CSS (display: none)\n";
            }
            
            // Check for content
            if (trim($sectionNode->textContent)) {
                echo "  ✓ Section has content\n";
            } else {
                echo "  ⚠ Section appears empty\n";
            }
        } else {
            echo "✗ Missing section '$sectionId'\n";
            $foundSections[$section] = false;
        }
    }
    
    echo "\n3. === JAVASCRIPT FUNCTIONALITY CHECK ===\n";
    
    // Check for JavaScript functionality
    $jsChecks = [
        'has_event_listeners' => strpos($response, 'addEventListener') !== false,
        'has_tab_click_handler' => strpos($response, 'settings-nav-tab') !== false && strpos($response, 'click') !== false,
        'has_section_show_logic' => strpos($response, 'style.display') !== false || strpos($response, 'display: block') !== false,
        'has_preview_update' => strpos($response, 'updatePreviewForSection') !== false,
        'has_auth_case' => strpos($response, "case 'auth'") !== false,
        'has_showSection_func' => strpos($response, 'showSection') !== false
    ];
    
    foreach ($jsChecks as $check => $found) {
        echo ($found ? "✓" : "✗") . " $check\n";
    }
    
    echo "\n4. === AUTH SECTION SPECIFIC CHECK ===\n";
    
    // Check for auth-specific content
    $authChecks = [
        'login_customization' => strpos($response, 'LOGIN CUSTOMIZATION') !== false,
        'registration_forms' => strpos($response, 'Registration Form Fields') !== false,
        'login_form' => strpos($response, 'loginForm') !== false,
        'registration_form' => strpos($response, 'registrationForm') !== false,
        'auth_settings_id' => strpos($response, 'id="auth-settings"') !== false
    ];
    
    foreach ($authChecks as $check => $found) {
        echo ($found ? "✓" : "✗") . " $check\n";
    }
    
    echo "\n5. === PERMISSIONS SECTION CHECK ===\n";
    
    // Check for permissions content
    $permissionChecks = [
        'permissions_settings' => strpos($response, 'id="permissions-settings"') !== false,
        'director_features' => strpos($response, 'id="director-features"') !== false,
        'professor_features' => strpos($response, 'id="professor-features"') !== false,
        'configure_buttons' => strpos($response, 'showSection(') !== false
    ];
    
    foreach ($permissionChecks as $check => $found) {
        echo ($found ? "✓" : "✗") . " $check\n";
    }
    
    echo "\n6. === FORM STRUCTURE CHECK ===\n";
    
    // Check for form IDs
    $expectedForms = [
        'generalForm', 'brandingForm', 'navbarForm', 'homepageForm',
        'studentForm', 'professorForm', 'adminForm', 'loginForm',
        'registrationForm', 'advancedForm'
    ];
    
    $foundForms = 0;
    foreach ($expectedForms as $formId) {
        if (strpos($response, "id=\"$formId\"") !== false) {
            echo "✓ Found form '$formId'\n";
            $foundForms++;
        } else {
            echo "✗ Missing form '$formId'\n";
        }
    }
    
    echo "\nForms found: $foundForms/" . count($expectedForms) . "\n";
    
    echo "\n7. === CSS CLASSES CHECK ===\n";
    
    // Check for required CSS classes
    $cssChecks = [
        'settings-nav-tab' => substr_count($response, 'settings-nav-tab'),
        'sidebar-section' => substr_count($response, 'sidebar-section'),
        'section-header' => substr_count($response, 'section-header'),
        'form-group' => substr_count($response, 'form-group')
    ];
    
    foreach ($cssChecks as $class => $count) {
        echo "Class '$class': found $count times " . ($count > 0 ? "✓" : "✗") . "\n";
    }
    
    echo "\n=== TEST RESULTS SUMMARY ===\n";
    
    $tabsFound = array_sum($foundTabs);
    $sectionsFound = array_sum($foundSections);
    $jsChecksPass = array_sum($jsChecks);
    $authChecksPass = array_sum($authChecks);
    $permissionChecksPass = array_sum($permissionChecks);
    
    echo "Navigation tabs: $tabsFound/" . count($expectedTabs) . " found\n";
    echo "Section content: $sectionsFound/" . count($expectedTabs) . " found\n";
    echo "JavaScript functionality: $jsChecksPass/" . count($jsChecks) . " checks passed\n";
    echo "Auth section features: $authChecksPass/" . count($authChecks) . " checks passed\n";
    echo "Permissions features: $permissionChecksPass/" . count($permissionChecks) . " checks passed\n";
    echo "Forms: $foundForms/" . count($expectedForms) . " found\n";
    
    $overallScore = ($tabsFound + $sectionsFound + $jsChecksPass + $authChecksPass + $permissionChecksPass + $foundForms) / 
                   (count($expectedTabs) + count($expectedTabs) + count($jsChecks) + count($authChecks) + count($permissionChecks) + count($expectedForms)) * 100;
    
    echo "\nOVERALL SCORE: " . round($overallScore, 1) . "%\n";
    
    if ($overallScore >= 80) {
        echo "✓ NAVIGATION SYSTEM IS WORKING WELL\n";
    } elseif ($overallScore >= 60) {
        echo "⚠ NAVIGATION SYSTEM NEEDS MINOR FIXES\n";
    } else {
        echo "✗ NAVIGATION SYSTEM NEEDS MAJOR FIXES\n";
    }
    
    echo "\n=== SPECIFIC ISSUES TO FIX ===\n";
    
    if ($foundTabs['auth'] && $foundSections['auth'] && $authChecksPass >= 3) {
        echo "✓ AUTH SECTION IS FUNCTIONAL\n";
    } else {
        echo "✗ AUTH SECTION NEEDS ATTENTION:\n";
        if (!$foundTabs['auth']) echo "  - Add auth tab to navigation\n";
        if (!$foundSections['auth']) echo "  - Add auth-settings section\n";
        if ($authChecksPass < 3) echo "  - Add auth content (login/registration forms)\n";
    }
    
    if ($foundTabs['permissions'] && $foundSections['permissions'] && $permissionChecksPass >= 2) {
        echo "✓ PERMISSIONS SECTION IS FUNCTIONAL\n";
    } else {
        echo "✗ PERMISSIONS SECTION NEEDS ATTENTION:\n";
        if (!$foundTabs['permissions']) echo "  - Add permissions tab to navigation\n";
        if (!$foundSections['permissions']) echo "  - Add permissions-settings section\n";
        if ($permissionChecksPass < 2) echo "  - Add director/professor feature subsections\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} finally {
    curl_close($ch);
    if (file_exists($cookieFile)) {
        unlink($cookieFile);
    }
}

echo "\nTest completed: " . date('Y-m-d H:i:s') . "\n";
?>
