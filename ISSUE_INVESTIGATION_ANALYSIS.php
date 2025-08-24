<?php
/**
 * COMPREHENSIVE ISSUE INVESTIGATION AND FIX
 * 
 * This test identifies and fixes the reported issues:
 * 1. Advanced tab showing empty
 * 2. ENROLL NOW button not redirecting to tenant page
 * 3. Missing enhanced Login/Register customization fields
 */

echo "=== COMPREHENSIVE ISSUE INVESTIGATION ===\n";
echo "Starting thorough analysis of reported issues...\n\n";

$issues_found = [];
$fixes_applied = [];

echo "1. INVESTIGATING ADVANCED TAB EMPTY ISSUE\n";
echo "========================================\n";

// Check if director-features.blade.php file exists and has content
$director_features_file = 'resources/views/smartprep/dashboard/partials/settings/director-features.blade.php';
if (!file_exists($director_features_file)) {
    $issues_found[] = "director-features.blade.php file missing";
    echo "❌ ISSUE: director-features.blade.php file missing\n";
} else {
    $content = file_get_contents($director_features_file);
    if (strlen($content) < 100) {
        $issues_found[] = "director-features.blade.php file too short/empty";
        echo "❌ ISSUE: director-features.blade.php file appears empty or too short\n";
    } else {
        echo "✅ director-features.blade.php file exists and has content\n";
        echo "   File size: " . strlen($content) . " bytes\n";
    }
}

// Check professor-features.blade.php
$professor_features_file = 'resources/views/smartprep/dashboard/partials/settings/professor-features.blade.php';
if (!file_exists($professor_features_file)) {
    $issues_found[] = "professor-features.blade.php file missing";
    echo "❌ ISSUE: professor-features.blade.php file missing\n";
} else {
    $content = file_get_contents($professor_features_file);
    if (strlen($content) < 100) {
        $issues_found[] = "professor-features.blade.php file too short/empty";
        echo "❌ ISSUE: professor-features.blade.php file appears empty or too short\n";
    } else {
        echo "✅ professor-features.blade.php file exists and has content\n";
        echo "   File size: " . strlen($content) . " bytes\n";
    }
}

// Check advanced.blade.php for duplicate includes
$advanced_file = 'resources/views/smartprep/dashboard/partials/settings/advanced.blade.php';
if (file_exists($advanced_file)) {
    $advanced_content = file_get_contents($advanced_file);
    $director_includes = substr_count($advanced_content, '@include(\'smartprep.dashboard.partials.settings.director-features\')');
    $professor_includes = substr_count($advanced_content, '@include(\'smartprep.dashboard.partials.settings.professor-features\')');
    
    if ($director_includes > 1 || $professor_includes > 1) {
        $issues_found[] = "Duplicate @include statements in advanced.blade.php";
        echo "❌ ISSUE: Found duplicate @include statements in advanced.blade.php\n";
        echo "   Director includes: $director_includes\n";
        echo "   Professor includes: $professor_includes\n";
    } else {
        echo "✅ No duplicate includes found in advanced.blade.php\n";
    }
} else {
    $issues_found[] = "advanced.blade.php file missing";
    echo "❌ ISSUE: advanced.blade.php file missing\n";
}

echo "\n2. INVESTIGATING ENROLLMENT REDIRECT ISSUE\n";
echo "==========================================\n";

// Check homepage.blade.php for enrollment URL
$homepage_file = 'resources/views/welcome/homepage.blade.php';
if (file_exists($homepage_file)) {
    $homepage_content = file_get_contents($homepage_file);
    
    // Check for hardcoded enrollment URL
    if (strpos($homepage_content, 'url(\'/enrollment\')') !== false) {
        $issues_found[] = "Homepage uses hardcoded /enrollment URL instead of tenant-aware route";
        echo "❌ ISSUE: Homepage ENROLL NOW button uses hardcoded /enrollment URL\n";
        echo "   Should use tenant-aware routing logic\n";
    } else {
        echo "✅ Homepage does not use hardcoded enrollment URL\n";
    }
} else {
    $issues_found[] = "homepage.blade.php file missing";
    echo "❌ ISSUE: homepage.blade.php file missing\n";
}

// Check if tenant enrollment routes exist
$routes_output = shell_exec('php artisan route:list --name=tenant.enrollment 2>&1');
if (strpos($routes_output, 'tenant.enrollment') !== false) {
    echo "✅ Tenant enrollment routes exist\n";
} else {
    $issues_found[] = "Tenant enrollment routes missing";
    echo "❌ ISSUE: Tenant enrollment routes missing\n";
}

echo "\n3. INVESTIGATING LOGIN/REGISTER CUSTOMIZATION\n";
echo "=============================================\n";

// Check auth.blade.php for enhanced fields
$auth_file = 'resources/views/smartprep/dashboard/partials/settings/auth.blade.php';
if (file_exists($auth_file)) {
    $auth_content = file_get_contents($auth_file);
    
    $required_fields = [
        'Background Color (Top of Gradient)' => 'login_bg_top_color',
        'Gradient Color (Bottom of Gradient)' => 'login_bg_bottom_color', 
        'Accent Color (Button Color)' => 'login_accent_color',
        'Text Color' => 'login_text_color',
        'Card Background' => 'login_card_bg',
        'Input Border Color' => 'login_input_border',
        'Input Focus Color' => 'login_input_focus',
        'Registration Form Fields' => 'registration_fields'
    ];
    
    $missing_fields = [];
    foreach ($required_fields as $label => $field) {
        if (strpos($auth_content, $field) === false) {
            $missing_fields[] = $label;
        }
    }
    
    if (!empty($missing_fields)) {
        $issues_found[] = "Missing enhanced login/register fields: " . implode(', ', $missing_fields);
        echo "❌ ISSUE: Missing enhanced login/register customization fields:\n";
        foreach ($missing_fields as $field) {
            echo "   - $field\n";
        }
    } else {
        echo "✅ All enhanced login/register fields present\n";
    }
    
    echo "   Auth file size: " . strlen($auth_content) . " bytes\n";
} else {
    $issues_found[] = "auth.blade.php file missing";
    echo "❌ ISSUE: auth.blade.php file missing\n";
}

echo "\n4. CHECKING CONTROLLER INTEGRATION\n";
echo "==================================\n";

// Check CustomizeWebsiteController for auth methods
$controller_file = 'app/Http/Controllers/Smartprep/Dashboard/CustomizeWebsiteController.php';
if (file_exists($controller_file)) {
    $controller_content = file_get_contents($controller_file);
    
    if (strpos($controller_content, 'updateAuth') === false) {
        $issues_found[] = "updateAuth method missing in CustomizeWebsiteController";
        echo "❌ ISSUE: updateAuth method missing in CustomizeWebsiteController\n";
    } else {
        echo "✅ updateAuth method exists in CustomizeWebsiteController\n";
    }
    
    if (strpos($controller_content, 'updateDirector') === false) {
        $issues_found[] = "updateDirector method missing in CustomizeWebsiteController";
        echo "❌ ISSUE: updateDirector method missing in CustomizeWebsiteController\n";
    } else {
        echo "✅ updateDirector method exists in CustomizeWebsiteController\n";
    }
    
    if (strpos($controller_content, 'updateProfessorFeatures') === false) {
        $issues_found[] = "updateProfessorFeatures method missing in CustomizeWebsiteController";
        echo "❌ ISSUE: updateProfessorFeatures method missing in CustomizeWebsiteController\n";
    } else {
        echo "✅ updateProfessorFeatures method exists in CustomizeWebsiteController\n";
    }
} else {
    $issues_found[] = "CustomizeWebsiteController file missing";
    echo "❌ ISSUE: CustomizeWebsiteController file missing\n";
}

echo "\n5. CHECKING ROUTE REGISTRATION\n";
echo "==============================\n";

// Check if auth routes are registered
$web_routes = file_get_contents('routes/web.php');
if (strpos($web_routes, 'dashboard.settings.update.auth') === false) {
    $issues_found[] = "Auth settings route not registered";
    echo "❌ ISSUE: Auth settings route not registered\n";
} else {
    echo "✅ Auth settings route registered\n";
}

if (strpos($web_routes, 'dashboard.settings.update.director') === false) {
    $issues_found[] = "Director settings route not registered";
    echo "❌ ISSUE: Director settings route not registered\n";
} else {
    echo "✅ Director settings route registered\n";
}

echo "\n6. CHECKING JAVASCRIPT INTEGRATION\n";
echo "==================================\n";

// Check customize-scripts.blade.php for required functions
$scripts_file = 'resources/views/smartprep/dashboard/partials/customize-scripts.blade.php';
if (file_exists($scripts_file)) {
    $scripts_content = file_get_contents($scripts_file);
    
    $required_functions = ['updateAuth', 'updateDirectorFeatures', 'updateProfessorFeatures', 'showSection'];
    $missing_functions = [];
    
    foreach ($required_functions as $func) {
        if (strpos($scripts_content, "function $func") === false) {
            $missing_functions[] = $func;
        }
    }
    
    if (!empty($missing_functions)) {
        $issues_found[] = "Missing JavaScript functions: " . implode(', ', $missing_functions);
        echo "❌ ISSUE: Missing JavaScript functions:\n";
        foreach ($missing_functions as $func) {
            echo "   - $func\n";
        }
    } else {
        echo "✅ All required JavaScript functions present\n";
    }
} else {
    $issues_found[] = "customize-scripts.blade.php file missing";
    echo "❌ ISSUE: customize-scripts.blade.php file missing\n";
}

echo "\n=== ISSUE SUMMARY ===\n";
echo "Total issues found: " . count($issues_found) . "\n\n";

if (empty($issues_found)) {
    echo "🎉 NO CRITICAL ISSUES FOUND!\n";
    echo "The problems might be minor configuration or display issues.\n\n";
} else {
    echo "📋 ISSUES IDENTIFIED:\n";
    foreach ($issues_found as $index => $issue) {
        echo ($index + 1) . ". $issue\n";
    }
}

echo "\n=== PERFORMANCE TEST ===\n";

// Test route performance
$routes_to_test = [
    'SmartPrep Dashboard' => 'http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=16',
    'Tenant Enrollment' => 'http://127.0.0.1:8000/t/draft/test1/enrollment',
    'Regular Enrollment' => 'http://127.0.0.1:8000/enrollment'
];

foreach ($routes_to_test as $name => $url) {
    $start_time = microtime(true);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $end_time = microtime(true);
    $response_time = round(($end_time - $start_time) * 1000, 2);
    
    echo "$name: HTTP $http_code ({$response_time}ms)\n";
}

echo "\n=== READY FOR TARGETED FIXES ===\n";
echo "Investigation complete. Proceeding with specific fixes based on findings.\n";
?>
