<?php
/**
 * Comprehensive Archived Content Investigation
 * Testing all aspects: routes, controllers, views, data flow, JavaScript
 */

echo "🔍 COMPREHENSIVE ARCHIVED CONTENT INVESTIGATION\n";
echo "==============================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test11';
$url = "/t/draft/$tenant/admin/archived";

echo "📍 Testing URL: $baseUrl$url\n\n";

// Test 1: Basic Response
echo "1️⃣  BASIC RESPONSE TEST\n";
echo "----------------------\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'test_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response Length: " . strlen($response) . " characters\n";

if ($httpCode !== 200) {
    echo "❌ FAILED: HTTP $httpCode\n";
    exit(1);
}

// Test 2: Content Analysis
echo "\n2️⃣  CONTENT ANALYSIS\n";
echo "-------------------\n";

$hasArchivedData = strpos($response, 'archivedPrograms') !== false;
$hasArchivedCourses = strpos($response, 'archivedCourses') !== false;
$hasTestData = strpos($response, 'TEST11') !== false;
$hasPreviewMode = strpos($response, 'preview') !== false || strpos($response, 'Preview') !== false;
$hasNavigation = strpos($response, 'navbar') !== false || strpos($response, 'nav-') !== false;
$hasJavaScript = strpos($response, '<script') !== false;
$hasBootstrap = strpos($response, 'bootstrap') !== false || strpos($response, 'card') !== false;
$hasEmptyState = strpos($response, 'Select a program above') !== false;
$hasActualData = strpos($response, 'Nursing Program') !== false || strpos($response, 'MedTech') !== false;

echo "✅ Has archived programs data: " . ($hasArchivedData ? "YES" : "NO") . "\n";
echo "✅ Has archived courses data: " . ($hasArchivedCourses ? "YES" : "NO") . "\n";
echo "✅ Has TEST11 branding: " . ($hasTestData ? "YES" : "NO") . "\n";
echo "✅ Has preview mode: " . ($hasPreviewMode ? "YES" : "NO") . "\n";
echo "✅ Has navigation: " . ($hasNavigation ? "YES" : "NO") . "\n";
echo "✅ Has JavaScript: " . ($hasJavaScript ? "YES" : "NO") . "\n";
echo "✅ Has Bootstrap styling: " . ($hasBootstrap ? "YES" : "NO") . "\n";
echo "⚠️  Shows empty state: " . ($hasEmptyState ? "YES" : "NO") . "\n";
echo "✅ Has actual mock data: " . ($hasActualData ? "YES" : "NO") . "\n";

// Test 3: Extract JavaScript Data
echo "\n3️⃣  JAVASCRIPT DATA EXTRACTION\n";
echo "-----------------------------\n";

// Look for data passed to JavaScript
if (preg_match('/archivedPrograms.*?(\[.*?\])/s', $response, $matches)) {
    echo "✅ Found archivedPrograms JavaScript data\n";
    echo "Data: " . substr($matches[1], 0, 200) . "...\n";
} else {
    echo "❌ No archivedPrograms JavaScript data found\n";
}

if (preg_match('/archivedCourses.*?(\[.*?\])/s', $response, $matches)) {
    echo "✅ Found archivedCourses JavaScript data\n";
    echo "Data: " . substr($matches[1], 0, 200) . "...\n";
} else {
    echo "❌ No archivedCourses JavaScript data found\n";
}

// Test 4: Template Variable Check
echo "\n4️⃣  TEMPLATE VARIABLE CHECK\n";
echo "-------------------------\n";

// Check if PHP variables are being passed correctly
$hasPhpArchivedPrograms = strpos($response, '@if(isset($archivedPrograms))') !== false;
$hasPhpArchivedCourses = strpos($response, '@if(isset($archivedCourses))') !== false;
$hasPhpForeach = strpos($response, '@foreach') !== false;

echo "PHP Template Checks:\n";
echo "- archivedPrograms variable: " . ($hasPhpArchivedPrograms ? "FOUND" : "NOT FOUND") . "\n";
echo "- archivedCourses variable: " . ($hasPhpArchivedCourses ? "FOUND" : "NOT FOUND") . "\n";
echo "- @foreach loops: " . ($hasPhpForeach ? "FOUND" : "NOT FOUND") . "\n";

// Test 5: Data Table Analysis
echo "\n5️⃣  DATA TABLE ANALYSIS\n";
echo "---------------------\n";

$hasTableHeaders = strpos($response, '<th>') !== false;
$hasTableRows = strpos($response, '<tr>') !== false;
$hasDataTables = strpos($response, 'DataTable') !== false || strpos($response, 'dataTable') !== false;

echo "Table Elements:\n";
echo "- Table headers: " . ($hasTableHeaders ? "FOUND" : "NOT FOUND") . "\n";
echo "- Table rows: " . ($hasTableRows ? "FOUND" : "NOT FOUND") . "\n";
echo "- DataTables JS: " . ($hasDataTables ? "FOUND" : "NOT FOUND") . "\n";

// Test 6: Error Detection
echo "\n6️⃣  ERROR DETECTION\n";
echo "------------------\n";

$hasPhpErrors = strpos($response, 'Fatal error') !== false || strpos($response, 'Parse error') !== false;
$hasBladeErrors = strpos($response, 'Blade') !== false && strpos($response, 'error') !== false;
$hasUndefinedVar = strpos($response, 'Undefined variable') !== false;
$hasMissingView = strpos($response, 'View not found') !== false;

echo "Error Checks:\n";
echo "- PHP errors: " . ($hasPhpErrors ? "FOUND ❌" : "NONE ✅") . "\n";
echo "- Blade errors: " . ($hasBladeErrors ? "FOUND ❌" : "NONE ✅") . "\n";
echo "- Undefined variables: " . ($hasUndefinedVar ? "FOUND ❌" : "NONE ✅") . "\n";
echo "- Missing view: " . ($hasMissingView ? "FOUND ❌" : "NONE ✅") . "\n";

// Test 7: Mock Data Structure
echo "\n7️⃣  MOCK DATA STRUCTURE TEST\n";
echo "--------------------------\n";

// Extract any visible data to see what's actually being rendered
if (preg_match_all('/TEST11.*?Program/i', $response, $matches)) {
    echo "✅ Found " . count($matches[0]) . " TEST11 program references:\n";
    foreach (array_unique($matches[0]) as $match) {
        echo "  - $match\n";
    }
} else {
    echo "❌ No TEST11 program data found in rendered output\n";
}

// Test 8: Controller Method Test
echo "\n8️⃣  CONTROLLER METHOD DIRECT TEST\n";
echo "-------------------------------\n";

try {
    // Test the controller method directly
    echo "Testing AdminController::previewArchivedContent method...\n";
    
    // Simulate the method call
    $testOutput = shell_exec('php -r "
        require_once \"vendor/autoload.php\";
        \$app = require_once \"bootstrap/app.php\";
        \$controller = new App\\Http\\Controllers\\AdminController();
        try {
            \$result = \$controller->previewArchivedContent(\"test11\");
            echo \"Controller method executed successfully\\n\";
            echo \"Result type: \" . get_class(\$result) . \"\\n\";
        } catch (Exception \$e) {
            echo \"Controller error: \" . \$e->getMessage() . \"\\n\";
        }
    "');
    
    echo $testOutput;
    
} catch (Exception $e) {
    echo "❌ Controller test failed: " . $e->getMessage() . "\n";
}

// Test 9: View File Check
echo "\n9️⃣  VIEW FILE VERIFICATION\n";
echo "------------------------\n";

$viewPath = 'resources/views/admin/archived/index.blade.php';
if (file_exists($viewPath)) {
    echo "✅ View file exists: $viewPath\n";
    $viewContent = file_get_contents($viewPath);
    $viewSize = strlen($viewContent);
    echo "✅ View file size: $viewSize characters\n";
    
    // Check for specific template elements
    $hasExtendsDirective = strpos($viewContent, '@extends') !== false;
    $hasSectionContent = strpos($viewContent, '@section') !== false;
    $hasArchivedProgramsLoop = strpos($viewContent, 'archivedPrograms') !== false;
    
    echo "Template Structure:\n";
    echo "- @extends directive: " . ($hasExtendsDirective ? "FOUND ✅" : "MISSING ❌") . "\n";
    echo "- @section directives: " . ($hasSectionContent ? "FOUND ✅" : "MISSING ❌") . "\n";
    echo "- archivedPrograms usage: " . ($hasArchivedProgramsLoop ? "FOUND ✅" : "MISSING ❌") . "\n";
    
} else {
    echo "❌ View file not found: $viewPath\n";
}

// Final Assessment
echo "\n🎯 FINAL ASSESSMENT\n";
echo "==================\n";

$issues = [];
if (!$hasActualData) $issues[] = "No actual mock data visible in output";
if ($hasEmptyState) $issues[] = "Showing empty state instead of data";
if (!$hasArchivedData) $issues[] = "archivedPrograms variable not found";
if (!$hasArchivedCourses) $issues[] = "archivedCourses variable not found";
if ($hasPhpErrors || $hasBladeErrors) $issues[] = "Template rendering errors detected";

if (empty($issues)) {
    echo "🟢 ALL CHECKS PASSED - Archive page should be working correctly\n";
} else {
    echo "🔴 ISSUES FOUND:\n";
    foreach ($issues as $issue) {
        echo "  ❌ $issue\n";
    }
}

echo "\n📋 NEXT STEPS:\n";
echo "1. Fix any identified issues in the controller/view\n";
echo "2. Ensure mock data is properly passed to the template\n";
echo "3. Verify template is correctly displaying the data\n";
echo "4. Test JavaScript functionality for filtering/searching\n";

// Clean up
if (file_exists('test_cookies.txt')) {
    unlink('test_cookies.txt');
}
?>
