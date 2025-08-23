<?php
// Comprehensive Admin Preview System Test

$tenant = 'test1';
$base_url = 'http://127.0.0.1:8000';

// Major admin sections that need tenant preview support
$admin_pages = [
    'dashboard' => "/t/draft/$tenant/admin-dashboard",
    'students' => "/admin/students",
    'professors' => "/admin/professors", 
    'programs' => "/admin/programs",
    'modules' => "/admin/modules",
    'announcements' => "/admin/announcements",
    'batches' => "/admin/batches",
    'enrollments' => "/admin/enrollments",
    'payments' => "/admin/payments/pending",
    'analytics' => "/admin/analytics",
    'settings' => "/admin/settings",
    'packages' => "/admin/packages",
    'faq' => "/admin/faq",
    'directors' => "/admin/directors",
    'quiz_generator' => "/admin/quiz-generator"
];

echo "=== Admin Preview System Analysis ===\n\n";

echo "1. TESTING CURRENT ADMIN PREVIEW ROUTES:\n";
echo "Only admin dashboard has tenant preview route: /t/draft/\$tenant/admin-dashboard\n";
echo "All other admin pages use regular routes without tenant support.\n\n";

echo "2. TESTING ADMIN PAGES ACCESS:\n";

foreach ($admin_pages as $page_name => $url) {
    echo "Testing: " . ucfirst(str_replace('_', ' ', $page_name)) . "\n";
    echo "URL: $base_url$url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Cookie: preview_tenant=' . $tenant,
        'Accept: text/html'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $final_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    
    if ($http_code === 200) {
        echo "✅ Status: SUCCESS ($http_code)\n";
        
        // Check if redirected to login
        if (strpos($final_url, 'login') !== false) {
            echo "❌ ISSUE: Redirected to login page\n";
        } elseif (strpos($response, "login") !== false && strpos($response, "password") !== false) {
            echo "❌ ISSUE: Shows login form - authentication failed\n";
        } else {
            echo "✅ Page loaded successfully\n";
        }
    } elseif ($http_code === 302 || $http_code === 301) {
        echo "⚠️  Status: REDIRECT ($http_code)\n";
        echo "   Redirected to: $final_url\n";
        if (strpos($final_url, 'login') !== false) {
            echo "❌ ISSUE: Redirected to login page\n";
        }
    } else {
        echo "❌ Status: FAILED ($http_code)\n";
    }
    
    echo "---\n";
}

echo "\n3. AUTHENTICATION ANALYSIS:\n";
echo "- Admin dashboard has preview route that sets session: ['user_role'=>'admin','logged_in'=>true]\n";
echo "- Other admin pages likely fail because they check for real admin authentication\n";
echo "- Need to create tenant preview routes for all major admin sections\n";
echo "- Need to update admin middleware to handle preview mode\n\n";

echo "4. REQUIRED FIXES:\n";
echo "✅ Create tenant preview routes for all admin sections\n";
echo "✅ Update admin authentication middleware for preview mode\n";
echo "✅ Update admin sidebar for tenant-aware routing\n";
echo "✅ Update admin controllers to handle preview mode\n";
echo "✅ Test all admin functionality in preview mode\n\n";

echo "5. DATABASE ISSUES TO EXPECT:\n";
echo "- Similar to professor preview: database table access issues\n";
echo "- Admin controllers will try to access tables that don't exist in preview\n";
echo "- Need preview detection in all admin controllers\n";
?>
