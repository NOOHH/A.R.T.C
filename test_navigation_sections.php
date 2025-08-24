<?php
/**
 * Test Navigation Sections - Comprehensive validation of customize website navigation
 * This script tests that all navigation tabs properly show their corresponding sections
 */

require_once __DIR__ . '/vendor/autoload.php';

// Test configuration
$testUrl = 'http://127.0.0.1:8000/smartprep/dashboard/customize-website';
$cookieFile = __DIR__ . '/test_navigation_cookies.txt';

// Expected navigation sections
$expectedSections = [
    'general' => 'General Settings',
    'branding' => 'Branding & Design', 
    'navbar' => 'Navigation Bar',
    'homepage' => 'Homepage Content',
    'student' => 'Student Portal',
    'professor' => 'Professor Panel Settings',
    'admin' => 'Admin Panel Settings',
    'auth' => 'Authentication & Registration',
    'permissions' => 'Permissions',
    'advanced' => 'Advanced Settings'
];

// Special subsections for permissions
$permissionSubsections = [
    'director-features' => 'Director Features',
    'professor-features' => 'Professor Features'
];

echo "=== NAVIGATION SECTIONS VALIDATION ===\n";
echo "Testing customize website navigation tabs and sections\n\n";

// Initialize cURL for session management
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
    // Get the customize website page
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode !== 200) {
        throw new Exception("Failed to load customize website page. HTTP Code: $httpCode");
    }
    
    echo "✓ Successfully loaded customize website page\n";
    
    // Parse HTML to check for navigation elements
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($response);
    libxml_clear_errors();
    
    $xpath = new DOMXPath($dom);
    
    // Check navigation tabs
    echo "\n--- NAVIGATION TABS CHECK ---\n";
    foreach ($expectedSections as $sectionKey => $sectionTitle) {
        $tabXPath = "//button[@class='settings-nav-tab' or contains(@class, 'settings-nav-tab')][@data-section='$sectionKey']";
        $tabNodes = $xpath->query($tabXPath);
        
        if ($tabNodes->length > 0) {
            echo "✓ Found navigation tab for '$sectionKey' section\n";
        } else {
            echo "✗ Missing navigation tab for '$sectionKey' section\n";
        }
    }
    
    // Check section content divs
    echo "\n--- SECTION CONTENT CHECK ---\n";
    foreach ($expectedSections as $sectionKey => $sectionTitle) {
        $sectionId = $sectionKey . '-settings';
        $sectionXPath = "//div[@id='$sectionId']";
        $sectionNodes = $xpath->query($sectionXPath);
        
        if ($sectionNodes->length > 0) {
            echo "✓ Found section content for '$sectionId'\n";
            
            // Check if section has expected title/header
            $headerXPath = "//div[@id='$sectionId']//h5[contains(text(), '" . explode(' ', $sectionTitle)[0] . "')]";
            $headerNodes = $xpath->query($headerXPath);
            
            if ($headerNodes->length > 0) {
                echo "  ✓ Section has proper header\n";
            } else {
                echo "  ⚠ Section missing expected header\n";
            }
        } else {
            echo "✗ Missing section content div for '$sectionId'\n";
        }
    }
    
    // Check permission subsections
    echo "\n--- PERMISSION SUBSECTIONS CHECK ---\n";
    foreach ($permissionSubsections as $subsectionKey => $subsectionTitle) {
        $subsectionXPath = "//div[@id='$subsectionKey']";
        $subsectionNodes = $xpath->query($subsectionXPath);
        
        if ($subsectionNodes->length > 0) {
            echo "✓ Found permission subsection '$subsectionKey'\n";
        } else {
            echo "✗ Missing permission subsection '$subsectionKey'\n";
        }
    }
    
    // Check JavaScript functionality
    echo "\n--- JAVASCRIPT FUNCTIONALITY CHECK ---\n";
    
    // Look for tab click handlers
    if (strpos($response, 'settings-nav-tab') !== false) {
        echo "✓ Found navigation tab CSS classes\n";
    } else {
        echo "✗ Missing navigation tab CSS classes\n";
    }
    
    if (strpos($response, 'data-section') !== false) {
        echo "✓ Found data-section attributes for tab targeting\n";
    } else {
        echo "✗ Missing data-section attributes\n";
    }
    
    if (strpos($response, 'addEventListener') !== false && strpos($response, 'settings-nav-tab') !== false) {
        echo "✓ Found JavaScript event listeners for navigation\n";
    } else {
        echo "✗ Missing JavaScript navigation functionality\n";
    }
    
    if (strpos($response, 'showSection') !== false) {
        echo "✓ Found showSection function for permission navigation\n";
    } else {
        echo "✗ Missing showSection function\n";
    }
    
    // Check for auth preview functionality
    if (strpos($response, 'updatePreviewForSection') !== false) {
        echo "✓ Found preview update functionality\n";
    } else {
        echo "✗ Missing preview update functionality\n";
    }
    
    if (strpos($response, "case 'auth':") !== false) {
        echo "✓ Found auth case in preview logic\n";
    } else {
        echo "⚠ Auth case may be missing in preview logic\n";
    }
    
    echo "\n--- FORM STRUCTURE CHECK ---\n";
    
    // Check for form elements in each section
    $formsFound = [];
    $formIds = ['generalForm', 'brandingForm', 'navbarForm', 'homepageForm', 'studentForm', 
                'professorForm', 'adminForm', 'loginForm', 'registrationForm', 'advancedForm'];
    
    foreach ($formIds as $formId) {
        $formXPath = "//form[@id='$formId']";
        $formNodes = $xpath->query($formXPath);
        
        if ($formNodes->length > 0) {
            echo "✓ Found form '$formId'\n";
            $formsFound[] = $formId;
        } else {
            echo "⚠ Form '$formId' not found\n";
        }
    }
    
    echo "\n=== VALIDATION SUMMARY ===\n";
    echo "Navigation tabs: " . count($expectedSections) . " expected\n";
    echo "Section content divs: Should match navigation tabs\n";
    echo "Permission subsections: " . count($permissionSubsections) . " expected\n";
    echo "Forms found: " . count($formsFound) . " out of " . count($formIds) . "\n";
    
    echo "\n=== RECOMMENDATIONS ===\n";
    echo "1. Refresh the customize website page and verify:\n";
    echo "   - All navigation tabs are visible\n";
    echo "   - Clicking each tab shows the corresponding section\n";
    echo "   - Auth tab shows login/register settings\n";
    echo "   - Permissions tab shows director/professor buttons\n";
    echo "2. Check browser console for any JavaScript errors\n";
    echo "3. Verify preview updates when switching between sections\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} finally {
    curl_close($ch);
    if (file_exists($cookieFile)) {
        unlink($cookieFile);
    }
}

echo "\nNavigation validation completed!\n";
?>
