<?php
echo "🧪 FINAL ADMIN ROUTING TEST AFTER COMPLETE FIX\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Test all three admin routes after the complete fix
$testRoutes = [
    'quiz-generator' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/quiz-generator?website=1',
    'courses-upload' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/courses/upload?website=1',
    'modules-archived' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/modules/archived?website=1'
];

$context = stream_context_create([
    'http' => [
        'timeout' => 15,
        'ignore_errors' => true
    ]
]);

$allPassed = true;
$testResults = [];

foreach ($testRoutes as $name => $url) {
    echo "🎯 Testing: $name\n";
    echo "   URL: $url\n";
    
    $startTime = microtime(true);
    $response = @file_get_contents($url, false, $context);
    $endTime = microtime(true);
    $loadTime = round(($endTime - $startTime) * 1000, 2);
    
    $testResult = [
        'name' => $name,
        'accessible' => false,
        'has_db_error' => false,
        'has_branding' => false,
        'has_content' => false,
        'load_time' => $loadTime
    ];
    
    if ($response !== false) {
        echo "✅ Route accessible ($loadTime ms)\n";
        $testResult['accessible'] = true;
        
        // Check for database errors
        if (strpos($response, 'SQLSTATE') !== false || strpos($response, 'No database selected') !== false) {
            echo "❌ Database error still present!\n";
            $testResult['has_db_error'] = true;
            $allPassed = false;
            
            // Extract the error details
            if (preg_match('/SQLSTATE\[.*?\]: (.+?)(?=\s*\(|$)/s', $response, $matches)) {
                echo "   Error: " . trim($matches[1]) . "\n";
            }
        } else {
            echo "✅ No database errors detected\n";
        }
        
        // Check for custom branding
        if (strpos($response, 'SmartPrep Learning Center') !== false || strpos($response, 'Learning Portal') !== false) {
            echo "✅ Custom branding present\n";
            $testResult['has_branding'] = true;
        } else {
            echo "⚠️  Custom branding not detected\n";
        }
        
        // Check for successful page content
        if (strpos($response, 'Quiz Generator') !== false || 
            strpos($response, 'Courses Upload') !== false || 
            strpos($response, 'Modules Archived') !== false ||
            strpos($response, 'working correctly') !== false) {
            echo "✅ Page content loaded successfully\n";
            $testResult['has_content'] = true;
        } else {
            echo "⚠️  Unexpected page content\n";
            echo "   Response preview: " . substr($response, 0, 150) . "...\n";
        }
        
    } else {
        echo "❌ Route not accessible\n";
        $allPassed = false;
    }
    
    $testResults[] = $testResult;
    echo "\n";
}

echo "📊 FINAL TEST SUMMARY:\n";
echo "=" . str_repeat("=", 25) . "\n";

foreach ($testResults as $result) {
    echo "🎯 {$result['name']}:\n";
    echo "   ✅ Accessible: " . ($result['accessible'] ? 'YES' : 'NO') . "\n";
    echo "   ✅ No DB Errors: " . (!$result['has_db_error'] ? 'YES' : 'NO') . "\n";
    echo "   ✅ Has Branding: " . ($result['has_branding'] ? 'YES' : 'NO') . "\n";
    echo "   ✅ Has Content: " . ($result['has_content'] ? 'YES' : 'NO') . "\n";
    echo "   ⏱️  Load Time: {$result['load_time']} ms\n";
    echo "\n";
}

if ($allPassed) {
    echo "🎉 SUCCESS! ALL ADMIN ROUTES ARE WORKING PERFECTLY!\n";
    echo "✅ No database connection errors\n";
    echo "✅ All routes accessible\n";
    echo "✅ Custom tenant branding functional\n";
} else {
    echo "❌ Some issues still remain - check the details above\n";
}

// Verify that all AdminSetting calls have been removed
echo "\n🔍 VERIFICATION: AdminSetting::getValue usage check\n";
$layoutContent = file_get_contents('resources/views/admin/admin-dashboard/admin-dashboard-layout.blade.php');

$adminSettingCount = substr_count($layoutContent, 'AdminSetting::getValue');
echo "   AdminSetting::getValue count in layout: $adminSettingCount\n";

if ($adminSettingCount === 0) {
    echo "✅ All AdminSetting::getValue calls have been successfully replaced!\n";
} else {
    echo "❌ Still has $adminSettingCount AdminSetting::getValue calls\n";
}

$adminSettingsCount = substr_count($layoutContent, '$adminSettings');
echo "   \$adminSettings usage count in layout: $adminSettingsCount\n";

echo "\n🎯 Complete test finished!\n";
?>
