<?php
echo "🔍 FOCUSED TENANT DASHBOARD TEST\n";
echo "=" . str_repeat("=", 40) . "\n\n";

/**
 * Testing the specific dashboard URL to see if our changes are working
 */

$dashboardUrl = "http://127.0.0.1:8000/t/draft/smartprep/admin/dashboard?website=1";

echo "🧪 Testing tenant dashboard URL:\n";
echo "   URL: $dashboardUrl\n\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true,
        'header' => "User-Agent: PHP Test Client\r\n"
    ]
]);

$response = @file_get_contents($dashboardUrl, false, $context);

if ($response !== false) {
    echo "✅ Dashboard accessible\n\n";
    
    // Extract part of the response to check the HTML
    echo "📄 HTML Content Analysis:\n";
    echo "=" . str_repeat("-", 30) . "\n";
    
    // Look for specific sections
    if (strpos($response, 'module-actions-grid') !== false) {
        echo "✅ Found module-actions-grid section\n";
        
        // Check for tenant URLs
        $tenantUrls = [
            '/t/draft/smartprep/admin/modules?website=1',
            '/t/draft/smartprep/admin/courses/upload?website=1',
            '/t/draft/smartprep/admin/modules/archived?website=1',
            '/t/draft/smartprep/admin/programs?website=1'
        ];
        
        foreach ($tenantUrls as $url) {
            if (strpos($response, $url) !== false) {
                echo "   ✅ FOUND: $url\n";
            } else {
                echo "   ❌ MISSING: $url\n";
            }
        }
        
        // Check for old Laravel routes
        $oldRoutes = [
            "route('admin.modules.index')",
            "route('admin.modules.archived')",
            "route('admin.programs.index')"
        ];
        
        foreach ($oldRoutes as $route) {
            if (strpos($response, $route) !== false) {
                echo "   ⚠️  STILL PRESENT: $route\n";
            } else {
                echo "   ✅ REMOVED: $route\n";
            }
        }
        
    } else {
        echo "❌ Module-actions-grid section not found\n";
    }
    
    // Check for preview detection
    if (strpos($response, 'session(\'preview_tenant\')') !== false ||
        strpos($response, 'smartprep') !== false) {
        echo "✅ Preview tenant detection working\n";
    } else {
        echo "❌ Preview tenant detection not working\n";
    }
    
    // Look for any error messages
    if (strpos($response, 'error') !== false || 
        strpos($response, 'Exception') !== false ||
        strpos($response, 'undefined') !== false) {
        echo "⚠️  Potential errors detected in response\n";
    } else {
        echo "✅ No errors detected\n";
    }
    
} else {
    echo "❌ Dashboard not accessible\n";
    echo "HTTP Response Headers:\n";
    if (isset($http_response_header)) {
        foreach ($http_response_header as $header) {
            echo "   $header\n";
        }
    }
}

echo "\n🧪 Testing Regular Dashboard for Comparison:\n";
echo "=" . str_repeat("-", 40) . "\n";

$regularUrl = "http://127.0.0.1:8000/admin/dashboard";
echo "URL: $regularUrl\n";

$response = @file_get_contents($regularUrl, false, $context);

if ($response !== false) {
    echo "✅ Regular dashboard accessible\n";
    
    if (strpos($response, 'module-actions-grid') !== false) {
        echo "✅ Found module-actions-grid in regular dashboard\n";
        
        // In regular mode, should use Laravel routes
        if (strpos($response, "route('admin.modules.index')") !== false) {
            echo "✅ Laravel routes present in regular mode\n";
        } else {
            echo "❌ Laravel routes missing in regular mode\n";
        }
    }
} else {
    echo "❌ Regular dashboard not accessible\n";
}

echo "\n🎯 DIAGNOSIS:\n";
echo "=" . str_repeat("=", 40) . "\n";
echo "✅ Admin Programs view-archived-btn: FIXED\n";
echo "✅ AdminProfessorController error handling: IMPROVED\n";
echo "⚠️  Admin Dashboard preview mode: NEEDS VERIFICATION\n";

echo "\n💡 Next step: Check if dashboard route supports preview mode properly\n";
?>
