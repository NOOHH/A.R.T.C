<?php
echo "🔍 DEBUGGING TENANT ARCHIVED ROUTE ERRORS\n";
echo "=" . str_repeat("=", 45) . "\n\n";

/**
 * Debugging the ModelNotFoundException in tenant archived routes
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
    echo "🔍 Debugging $description:\n";
    echo "   URL: $url\n";
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        // Extract error details
        if (strpos($response, 'ModelNotFoundException') !== false) {
            echo "   ❌ ERROR TYPE: ModelNotFoundException\n";
            
            // Look for specific error details
            preg_match('/No query results for model \[([^\]]+)\]/', $response, $matches);
            if (isset($matches[1])) {
                echo "   📄 MODEL: {$matches[1]}\n";
            }
            
            // Look for line numbers and file paths
            preg_match('/vendor\\\\laravel\\\\framework\\\\src\\\\([^"]+)"[^:]*line["\s]*(\d+)/', $response, $fileMatches);
            if (isset($fileMatches[1], $fileMatches[2])) {
                echo "   📍 FILE: {$fileMatches[1]} (line {$fileMatches[2]})\n";
            }
            
            // Check if it's using the preview method
            if (strpos($response, 'previewArchived') !== false) {
                echo "   ✅ ROUTE: Using previewArchived method\n";
            } else {
                echo "   ❌ ROUTE: NOT using previewArchived method\n";
            }
            
            // Look for any preview indicators
            if (strpos($response, 'preview') !== false) {
                echo "   ⚠️  PREVIEW: Some preview content detected\n";
            } else {
                echo "   ❌ PREVIEW: No preview content detected\n";
            }
        } else {
            echo "   ✅ SUCCESS: No ModelNotFoundException\n";
        }
        
        // Check response size to understand what we're getting
        echo "   📊 RESPONSE SIZE: " . strlen($response) . " bytes\n";
        
        // Look for Laravel error page indicators
        if (strpos($response, 'Whoops') !== false) {
            echo "   🔍 ERROR PAGE: Laravel Whoops error page detected\n";
        }
        
    } else {
        echo "   ❌ NO RESPONSE: Cannot reach URL\n";
    }
    echo "\n";
}

echo "🔍 ROUTE ANALYSIS:\n";
echo "=" . str_repeat("-", 30) . "\n";

// Let's also check what the actual route definitions look like
echo "📋 Checking route definitions in web.php...\n";

$webRoutes = file_get_contents('routes/web.php');

// Find our added routes
if (strpos($webRoutes, "Route::get('/draft/{tenant}/admin/students/archived'") !== false) {
    echo "✅ ROUTE DEF: Students archived route found in web.php\n";
} else {
    echo "❌ ROUTE DEF: Students archived route missing from web.php\n";
}

if (strpos($webRoutes, "Route::get('/draft/{tenant}/admin/professors/archived'") !== false) {
    echo "✅ ROUTE DEF: Professors archived route found in web.php\n";
} else {
    echo "❌ ROUTE DEF: Professors archived route missing from web.php\n";
}

// Check controller method calls
if (strpos($webRoutes, 'AdminStudentListController::class)->previewArchived') !== false) {
    echo "✅ CONTROLLER: AdminStudentListController@previewArchived called\n";
} else {
    echo "❌ CONTROLLER: AdminStudentListController@previewArchived not called\n";
}

if (strpos($webRoutes, 'AdminProfessorController::class)->previewArchived') !== false) {
    echo "✅ CONTROLLER: AdminProfessorController@previewArchived called\n";
} else {
    echo "❌ CONTROLLER: AdminProfessorController@previewArchived not called\n";
}

echo "\n🔍 POTENTIAL ISSUES:\n";
echo "=" . str_repeat("-", 30) . "\n";
echo "1. Controllers may not be using preview mode correctly\n";
echo "2. Route parameters may not be passed properly\n";
echo "3. Session data may not be set correctly\n";
echo "4. Mock data generation may have errors\n";

echo "\n💡 NEXT STEPS:\n";
echo "=" . str_repeat("-", 30) . "\n";
echo "1. Check controller implementation details\n";
echo "2. Verify route parameter passing\n";
echo "3. Test with different tenant/website parameters\n";
echo "4. Add error handling to controller methods\n";
?>
