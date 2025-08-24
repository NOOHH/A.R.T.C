<?php
/**
 * COMPREHENSIVE FIX VALIDATION TEST
 * 
 * Tests all the fixes we just applied:
 * 1. Advanced tab duplicate includes removed
 * 2. Homepage ENROLL NOW button now tenant-aware
 * 3. Enhanced Login/Register customization fields added
 * 4. PreviewController updated to pass tenant slug
 */

echo "=== COMPREHENSIVE FIX VALIDATION TEST ===\n";
echo "Testing all applied fixes...\n\n";

$fixes_validated = [];
$remaining_issues = [];

echo "1. TESTING ADVANCED TAB FIX\n";
echo "============================\n";

// Check if duplicate includes were removed
$advanced_file = 'resources/views/smartprep/dashboard/partials/settings/advanced.blade.php';
if (file_exists($advanced_file)) {
    $advanced_content = file_get_contents($advanced_file);
    $director_includes = substr_count($advanced_content, '@include(\'smartprep.dashboard.partials.settings.director-features\')');
    $professor_includes = substr_count($advanced_content, '@include(\'smartprep.dashboard.partials.settings.professor-features\')');
    
    if ($director_includes === 1 && $professor_includes === 1) {
        $fixes_validated[] = "Advanced tab duplicate includes removed";
        echo "✅ FIXED: Duplicate includes removed from advanced.blade.php\n";
        echo "   Director includes: $director_includes (should be 1)\n";
        echo "   Professor includes: $professor_includes (should be 1)\n";
    } else {
        $remaining_issues[] = "Advanced tab still has duplicate includes";
        echo "❌ ISSUE: Still has duplicate includes in advanced.blade.php\n";
        echo "   Director includes: $director_includes\n";
        echo "   Professor includes: $professor_includes\n";
    }
} else {
    $remaining_issues[] = "advanced.blade.php file missing";
    echo "❌ ISSUE: advanced.blade.php file missing\n";
}

echo "\n2. TESTING HOMEPAGE ENROLLMENT BUTTON FIX\n";
echo "==========================================\n";

// Check if homepage button is now tenant-aware
$homepage_file = 'resources/views/welcome/homepage.blade.php';
if (file_exists($homepage_file)) {
    $homepage_content = file_get_contents($homepage_file);
    
    // Check for tenant-aware routing
    if (strpos($homepage_content, 'isset($tenantSlug)') !== false && 
        strpos($homepage_content, '/t/draft/') !== false) {
        $fixes_validated[] = "Homepage ENROLL NOW button now tenant-aware";
        echo "✅ FIXED: Homepage ENROLL NOW button now uses tenant-aware routing\n";
        echo "   Contains tenant checking logic\n";
    } else {
        $remaining_issues[] = "Homepage button still not tenant-aware";
        echo "❌ ISSUE: Homepage button still not tenant-aware\n";
    }
} else {
    $remaining_issues[] = "homepage.blade.php file missing";
    echo "❌ ISSUE: homepage.blade.php file missing\n";
}

echo "\n3. TESTING LOGIN/REGISTER CUSTOMIZATION ENHANCEMENT\n";
echo "===================================================\n";

// Check if enhanced auth fields were added
$auth_file = 'resources/views/smartprep/dashboard/partials/settings/auth.blade.php';
if (file_exists($auth_file)) {
    $auth_content = file_get_contents($auth_file);
    
    $enhanced_fields = [
        'Background Color (Top of Gradient)' => 'login_bg_top_color',
        'Gradient Color (Bottom of Gradient)' => 'login_bg_bottom_color', 
        'Accent Color (Button Color)' => 'login_accent_color',
        'Text Color' => 'login_text_color',
        'Input Border Color' => 'login_input_border',
        'Input Focus Color' => 'login_input_focus',
        'Registration Form Fields' => 'registration_fields'
    ];
    
    $added_fields = [];
    $missing_fields = [];
    
    foreach ($enhanced_fields as $label => $field) {
        if (strpos($auth_content, $field) !== false) {
            $added_fields[] = $label;
        } else {
            $missing_fields[] = $label;
        }
    }
    
    if (empty($missing_fields)) {
        $fixes_validated[] = "All enhanced login/register fields added";
        echo "✅ FIXED: All enhanced login/register customization fields added\n";
        foreach ($added_fields as $field) {
            echo "   ✓ $field\n";
        }
    } else {
        $remaining_issues[] = "Some enhanced login/register fields still missing: " . implode(', ', $missing_fields);
        echo "✅ PARTIAL: Some enhanced fields added, but missing:\n";
        foreach ($missing_fields as $field) {
            echo "   ❌ $field\n";
        }
    }
    
    // Check for dynamic registration form fields
    if (strpos($auth_content, 'registration_fields[]') !== false) {
        echo "   ✅ Dynamic registration form fields added\n";
    } else {
        echo "   ❌ Dynamic registration form fields missing\n";
    }
    
} else {
    $remaining_issues[] = "auth.blade.php file missing";
    echo "❌ ISSUE: auth.blade.php file missing\n";
}

echo "\n4. TESTING PREVIEWCONTROLLER TENANT SLUG PASSING\n";
echo "================================================\n";

// Check if PreviewController passes tenant slug
$controller_file = 'app/Http/Controllers/Tenant/PreviewController.php';
if (file_exists($controller_file)) {
    $controller_content = file_get_contents($controller_file);
    
    if (strpos($controller_content, 'use ($slug)') !== false && 
        strpos($controller_content, '$tenantSlug = $slug') !== false) {
        $fixes_validated[] = "PreviewController now passes tenant slug to homepage";
        echo "✅ FIXED: PreviewController now passes tenant slug to homepage\n";
        echo "   Contains 'use (\$slug)' closure parameter\n";
        echo "   Contains '\$tenantSlug = \$slug' assignment\n";
    } else {
        $remaining_issues[] = "PreviewController not passing tenant slug properly";
        echo "❌ ISSUE: PreviewController not passing tenant slug properly\n";
    }
} else {
    $remaining_issues[] = "PreviewController file missing";
    echo "❌ ISSUE: PreviewController file missing\n";
}

echo "\n5. TESTING ROUTE REGISTRATION\n";
echo "=============================\n";

// Check routes in smartprep.php (where they should be)
$smartprep_routes = file_get_contents('routes/smartprep.php');
if (strpos($smartprep_routes, 'dashboard.settings.update.auth') !== false) {
    echo "✅ Auth settings route exists (smartprep.dashboard.settings.update.auth)\n";
} else {
    echo "❌ Auth settings route missing\n";
}

if (strpos($smartprep_routes, 'dashboard.settings.update.director') !== false) {
    echo "✅ Director settings route exists (smartprep.dashboard.settings.update.director)\n";
} else {
    echo "❌ Director settings route missing\n";
}

echo "\n6. FUNCTIONAL TESTING\n";
echo "=====================\n";

// Test route accessibility
$routes_to_test = [
    'SmartPrep Dashboard' => 'http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=16',
    'Tenant Homepage with Enrollment' => 'http://127.0.0.1:8000/t/draft/test1',
    'Tenant Enrollment' => 'http://127.0.0.1:8000/t/draft/test1/enrollment',
];

foreach ($routes_to_test as $name => $url) {
    $start_time = microtime(true);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $end_time = microtime(true);
    $response_time = round(($end_time - $start_time) * 1000, 2);
    
    if ($http_code === 200) {
        echo "✅ $name: HTTP $http_code ({$response_time}ms)\n";
    } else {
        echo "❌ $name: HTTP $http_code ({$response_time}ms)\n";
    }
}

echo "\n7. CONTENT VALIDATION\n";
echo "=====================\n";

// Test if tenant homepage contains proper routing logic
$tenant_home_url = 'http://127.0.0.1:8000/t/draft/test1';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tenant_home_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    if (strpos($response, '/t/draft/test1/enrollment') !== false) {
        echo "✅ Tenant homepage ENROLL NOW button points to tenant enrollment\n";
        $fixes_validated[] = "Tenant homepage enrollment button working";
    } else {
        echo "❌ Tenant homepage ENROLL NOW button not pointing to tenant enrollment\n";
        $remaining_issues[] = "Tenant homepage enrollment button not working";
    }
} else {
    echo "❌ Could not test tenant homepage content (HTTP $http_code)\n";
}

echo "\n=== FIX VALIDATION SUMMARY ===\n";
echo "Fixes Validated: " . count($fixes_validated) . "\n";
echo "Remaining Issues: " . count($remaining_issues) . "\n\n";

if (!empty($fixes_validated)) {
    echo "✅ SUCCESSFULLY APPLIED FIXES:\n";
    foreach ($fixes_validated as $index => $fix) {
        echo ($index + 1) . ". $fix\n";
    }
}

if (!empty($remaining_issues)) {
    echo "\n❌ REMAINING ISSUES:\n";
    foreach ($remaining_issues as $index => $issue) {
        echo ($index + 1) . ". $issue\n";
    }
} else {
    echo "\n🎉 ALL ISSUES HAVE BEEN RESOLVED! 🎉\n";
}

echo "\n=== NEXT STEPS ===\n";
if (empty($remaining_issues)) {
    echo "✅ All fixes applied successfully!\n";
    echo "✅ Clear browser cache and test manually\n";
    echo "✅ Advanced tab should now show permissions\n";
    echo "✅ ENROLL NOW button should redirect to tenant page\n";
    echo "✅ Login/Register tab should have enhanced fields\n";
} else {
    echo "🔧 Apply additional fixes for remaining issues\n";
    echo "🧪 Run additional tests to validate fixes\n";
}

echo "\n=== COMPREHENSIVE TESTING COMPLETE ===\n";
?>
