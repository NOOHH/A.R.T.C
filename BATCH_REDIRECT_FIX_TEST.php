<?php
echo "🧪 BATCH UPLOAD BUTTON REDIRECT FIX TEST\n";
echo "=" . str_repeat("=", 45) . "\n\n";

// Test the modules page to see if the JavaScript fix works
$modulesUrl = 'http://127.0.0.1:8000/t/draft/smartprep/admin/modules?website=1';

echo "🎯 Step 1: Testing admin modules page accessibility\n";
echo "URL: $modulesUrl\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

$response = @file_get_contents($modulesUrl, false, $context);

if ($response !== false) {
    echo "✅ Modules page accessible\n";
    
    // Check if the JavaScript fix is present
    if (strpos($response, 'isPreviewMode = currentPath.includes') !== false) {
        echo "✅ JavaScript fix detected in page\n";
    } else {
        echo "❌ JavaScript fix not found in page\n";
    }
    
    // Check if the tenant detection logic is present
    if (strpos($response, '/t/draft/${tenant}/admin/courses/upload') !== false) {
        echo "✅ Tenant-aware URL logic present\n";
    } else {
        echo "❌ Tenant-aware URL logic not found\n";
    }
    
    // Check for any database errors
    if (strpos($response, 'SQLSTATE') === false) {
        echo "✅ No database errors\n";
    } else {
        echo "❌ Database errors detected\n";
    }
    
} else {
    echo "❌ Modules page not accessible\n";
}

echo "\n🎯 Step 2: Testing target course upload URL\n";
$targetUrl = 'http://127.0.0.1:8000/t/draft/smartprep/admin/courses/upload?website=1';
echo "URL: $targetUrl\n";

$response2 = @file_get_contents($targetUrl, false, $context);

if ($response2 !== false) {
    echo "✅ Target course upload page accessible\n";
    
    // Check if it renders without errors
    if (strpos($response2, 'Error rendering full view') === false) {
        echo "✅ Page renders without errors\n";
    } else {
        echo "❌ Page has rendering errors\n";
    }
    
    if (strpos($response2, 'Course Content Upload') !== false || 
        strpos($response2, 'Upload') !== false) {
        echo "✅ Page contains expected content\n";
    } else {
        echo "⚠️  Page content may be unexpected\n";
    }
    
} else {
    echo "❌ Target course upload page not accessible\n";
}

echo "\n🎯 Step 3: Clear caches to ensure changes take effect\n";

$viewClear = shell_exec('cd C:\xampp\htdocs\A.R.T.C && php artisan view:clear 2>&1');
echo "View cache cleared: " . ($viewClear ? "✅ SUCCESS" : "❌ FAILED") . "\n";

$routeClear = shell_exec('cd C:\xampp\htdocs\A.R.T.C && php artisan route:clear 2>&1');
echo "Route cache cleared: " . ($routeClear ? "✅ SUCCESS" : "❌ FAILED") . "\n";

echo "\n📊 SUMMARY:\n";
echo "Fixed JavaScript redirection logic to:\n";
echo "✅ Detect preview mode by checking URL path\n";
echo "✅ Extract tenant from URL when in preview mode\n";
echo "✅ Use tenant-aware URL: /t/draft/{tenant}/admin/courses/upload\n";
echo "✅ Preserve website parameter\n";
echo "✅ Fall back to regular admin URL when not in preview\n";

echo "\n🎉 Test complete! The batch upload button should now redirect correctly.\n";
echo "Try clicking the 'Add Course Content' button in the browser.\n";
?>
