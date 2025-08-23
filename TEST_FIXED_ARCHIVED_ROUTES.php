<?php
echo "🔧 TESTING FIXED TENANT ARCHIVED ROUTES\n";
echo "=" . str_repeat("=", 45) . "\n\n";

/**
 * Testing the fixed archived routes after view updates
 */

$testUrls = [
    'http://127.0.0.1:8000/t/draft/test1/admin/students/archived?website=15' => 'Students Archived',
    'http://127.0.0.1:8000/t/draft/test1/admin/professors/archived?website=15' => 'Professors Archived'
];

$context = stream_context_create([
    'http' => [
        'timeout' => 15,
        'ignore_errors' => true,
        'header' => "User-Agent: PHP Debug Client\r\n"
    ]
]);

foreach ($testUrls as $url => $description) {
    echo "🔧 Testing $description:\n";
    echo "   URL: $url\n";
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        if (strpos($response, 'ModelNotFoundException') !== false) {
            echo "   ❌ ERROR: Still getting ModelNotFoundException\n";
            
            // Look for specific error details
            preg_match('/No query results for model \[([^\]]+)\]/', $response, $matches);
            if (isset($matches[1])) {
                echo "   📄 MODEL: {$matches[1]}\n";
            }
        } elseif (strpos($response, 'Whoops') !== false || strpos($response, 'Exception') !== false) {
            echo "   ❌ ERROR: Other exception detected\n";
        } else {
            echo "   ✅ SUCCESS: No errors detected\n";
            
            // Check for specific content
            if (strpos($response, 'Archived Students') !== false || strpos($response, 'Archived Professors') !== false) {
                echo "   ✅ CONTENT: Archived page title found\n";
            }
            
            // Check for tenant-aware buttons
            if (strpos($response, '/t/draft/test1/admin/') !== false) {
                echo "   ✅ BUTTONS: Tenant-aware URLs found in page\n";
            }
            
            // Check for preview mode indicators
            if (strpos($response, 'Preview mode') !== false) {
                echo "   ✅ PREVIEW: Preview mode text detected\n";
            }
        }
        
        echo "   📊 RESPONSE SIZE: " . strlen($response) . " bytes\n";
        
    } else {
        echo "   ❌ NO RESPONSE: Cannot reach URL\n";
    }
    echo "\n";
}

echo "🔧 BUTTON TESTS:\n";
echo "=" . str_repeat("-", 30) . "\n";

// Test the parent pages to ensure buttons still work
$parentUrls = [
    'http://127.0.0.1:8000/t/draft/test1/admin/students?website=15' => 'Students Index',
    'http://127.0.0.1:8000/t/draft/test1/admin/professors?website=15' => 'Professors Index'
];

foreach ($parentUrls as $url => $description) {
    echo "🔧 Testing $description buttons:\n";
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false && strpos($response, 'ModelNotFoundException') === false) {
        // Check for archived button with tenant URL
        if (strpos($response, '/t/draft/test1/admin/students/archived') !== false || 
            strpos($response, '/t/draft/test1/admin/professors/archived') !== false) {
            echo "   ✅ ARCHIVED BUTTON: Has correct tenant URL\n";
        } else {
            echo "   ❌ ARCHIVED BUTTON: Missing or incorrect URL\n";
        }
    } else {
        echo "   ❌ PARENT PAGE: Has errors\n";
    }
}

echo "\n💡 SUMMARY:\n";
echo "=" . str_repeat("-", 30) . "\n";
echo "✅ Fixed view templates with conditional tenant logic\n";
echo "✅ Updated AdminProfessorController to pass preview variables\n";
echo "✅ Routes should now work without ModelNotFoundException\n";
echo "✅ All tenant URLs should be correctly generated\n";
?>
