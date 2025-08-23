<?php
/**
 * Test the method error fix and identify module management issue
 */

echo "🔧 TESTING METHOD ERROR FIX\n";
echo "===========================\n";

// Test the pending page that was showing the method error
$pending_url = 'http://127.0.0.1:8000/t/draft/test1/admin-student-registration/pending?website=15&preview=true';
echo "Testing pending page: {$pending_url}\n";

$response = @file_get_contents($pending_url, false, stream_context_create([
    'http' => ['timeout' => 10, 'ignore_errors' => true]
]));

if ($response === false) {
    echo "❌ FAILED - Could not fetch page\n";
} else {
    if (strpos($response, 'loadTenantCustomization does not exist') !== false) {
        echo "❌ STILL HAS METHOD ERROR\n";
    } else if (strpos($response, 'TEST11') !== false && strpos($response, 'Pending Applications') !== false) {
        echo "✅ METHOD ERROR FIXED - Page loads correctly with TEST11 branding\n";
    } else {
        echo "⚠️  LOADS but may have other issues\n";
        echo "Response snippet: " . substr(strip_tags($response), 0, 200) . "...\n";
    }
}

echo "\n🔍 INVESTIGATING MODULE MANAGEMENT ISSUE\n";
echo "=========================================\n";
echo "User reported: 'whenever i select a Select Program to View/Manage Modules: on this on the module management it send me back to this ARTC Admin Portal instead of the tenant page'\n\n";

// Let's find the modules management page
$possible_module_urls = [
    'Admin Modules Page' => 'http://127.0.0.1:8000/t/draft/test1/admin/modules?website=15&preview=true',
    'Course Content Upload' => 'http://127.0.0.1:8000/t/draft/test1/admin/courses/upload?website=15&preview=true',
    'Admin Modules (alt)' => 'http://127.0.0.1:8000/t/draft/test1/admin-modules?website=15&preview=true'
];

foreach ($possible_module_urls as $name => $url) {
    echo "Testing {$name}: {$url}\n";
    
    $response = @file_get_contents($url, false, stream_context_create([
        'http' => ['timeout' => 10, 'ignore_errors' => true]
    ]));
    
    if ($response === false) {
        echo "❌ 404 - Page not found\n";
    } else {
        if (strpos($response, 'Select Program to View/Manage Modules') !== false) {
            echo "✅ FOUND MODULE MANAGEMENT PAGE!\n";
            echo "   Contains 'Select Program to View/Manage Modules'\n";
            
            // Check for JavaScript that might cause redirect issues
            if (strpos($response, 'getTenantFromPath') !== false) {
                echo "   ✅ Has tenant-aware JavaScript functions\n";
            } else {
                echo "   ❌ Missing tenant-aware JavaScript - THIS IS THE ISSUE!\n";
            }
        } else if (strpos($response, 'TEST11') !== false) {
            echo "✅ Loads with TEST11 branding\n";
        } else {
            echo "⚠️  Loads but content unknown\n";
        }
    }
    echo "\n";
}

echo "🔍 NEXT STEPS:\n";
echo "==============\n";
echo "1. ✅ Fixed method error (loadTenantCustomization → loadAdminPreviewCustomization)\n";
echo "2. 🔍 Need to find the exact module management page\n";
echo "3. 🔧 Need to remove 'Course Content Upload' from sidebar\n";
echo "4. 🔧 Need to fix module selection redirect issue\n";

echo "\n🏁 Investigation completed!\n";
?>
