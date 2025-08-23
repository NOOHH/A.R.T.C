<?php
echo "🔍 INVESTIGATING ADMIN PREVIEW PAGES\n";
echo "====================================\n\n";

$tenant = 'test11';
$website_param = '15';
$preview_param = 'true';

$pages_to_check = [
    'Archived Programs' => "http://127.0.0.1:8000/t/draft/$tenant/admin/programs/archived?website=$website_param&preview=$preview_param",
    'Archived Modules' => "http://127.0.0.1:8000/t/draft/$tenant/admin/archived?website=$website_param&preview=$preview_param",
    'Quiz Generator' => "http://127.0.0.1:8000/t/draft/$tenant/admin/quiz-generator?website=$website_param&preview=$preview_param",
    'Course Content Upload' => "http://127.0.0.1:8000/t/draft/$tenant/admin/courses/upload?website=$website_param&preview=$preview_param",
    'Certificates' => "http://127.0.0.1:8000/t/draft/$tenant/admin/certificates?website=$website_param&preview=$preview_param",
    'Directors' => "http://127.0.0.1:8000/t/draft/$tenant/admin/directors?website=$website_param&preview=$preview_param"
];

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0\r\n",
        'timeout' => 10
    ]
]);

foreach ($pages_to_check as $name => $url) {
    echo "Testing $name...\n";
    echo "URL: $url\n";
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        echo "❌ FAILED - Page failed to load\n";
        $error = error_get_last();
        if ($error) {
            echo "Error: " . $error['message'] . "\n";
        }
    } else {
        echo "✅ SUCCESS - Page loads\n";
        
        // Check if using hardcoded HTML (signs of old implementation)
        $hasHardcodedHTML = (
            strpos($response, '<div class="header">') !== false ||
            strpos($response, 'Preview Mode Active') !== false ||
            strpos($response, '← Back to Admin Dashboard') !== false
        );
        
        // Check if using proper Blade template (signs of new implementation)
        $hasProperTemplate = (
            strpos($response, 'admin-layouts') !== false ||
            strpos($response, 'navbar') !== false ||
            strpos($response, 'sidebar') !== false
        );
        
        if ($hasHardcodedHTML && !$hasProperTemplate) {
            echo "❌ ISSUE - Using hardcoded HTML instead of Blade template\n";
        } else if ($hasProperTemplate) {
            echo "✅ SUCCESS - Using proper Blade template\n";
        } else {
            echo "❓ UNCLEAR - Could not determine template type\n";
        }
        
        // Check for tenant branding
        if (strpos($response, 'TEST11') !== false) {
            echo "✅ SUCCESS - Tenant branding present\n";
        } else {
            echo "❓ INFO - No tenant branding found\n";
        }
        
        // Check for errors
        $hasErrors = (
            strpos($response, 'Undefined property') !== false ||
            strpos($response, 'does not exist') !== false ||
            strpos($response, 'Fatal error') !== false
        );
        
        if ($hasErrors) {
            echo "❌ ERROR - Contains PHP errors\n";
        } else {
            echo "✅ SUCCESS - No PHP errors detected\n";
        }
    }
    echo "\n";
}

echo "Investigation complete. Issues found will be fixed systematically.\n";
?>
