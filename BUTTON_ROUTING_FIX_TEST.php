<?php
echo "🔧 BUTTON ROUTING FIX TEST\n";
echo "=" . str_repeat("=", 35) . "\n\n";

// Test the admin modules page to check if buttons have correct URLs
$modulesUrl = 'http://127.0.0.1:8000/t/draft/smartprep/admin/modules?website=1';

echo "🎯 Testing admin modules page:\n";
echo "   URL: $modulesUrl\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 15,
        'ignore_errors' => true
    ]
]);

$response = @file_get_contents($modulesUrl, false, $context);

if ($response !== false) {
    echo "✅ Modules page accessible\n";
    
    // Check if the buttons now have tenant-aware URLs
    $buttonChecks = [
        'quiz-generator' => [
            'pattern' => '/t/draft/smartprep/admin/quiz-generator',
            'class' => 'quiz-generator-btn'
        ],
        'modules-archived' => [
            'pattern' => '/t/draft/smartprep/admin/modules/archived',
            'class' => 'view-archived-btn'
        ],
        'batch-upload' => [
            'pattern' => 'showBatchModal',
            'class' => 'batch-upload-btn'
        ]
    ];
    
    foreach ($buttonChecks as $name => $check) {
        if (strpos($response, $check['pattern']) !== false && strpos($response, $check['class']) !== false) {
            echo "✅ $name button: FIXED - using tenant-aware URL\n";
        } else {
            echo "❌ $name button: NOT FIXED - still using wrong URL\n";
        }
    }
    
    // Check for database errors
    if (strpos($response, 'SQLSTATE') !== false) {
        echo "❌ Database errors detected\n";
    } else {
        echo "✅ No database errors\n";
    }
    
    // Check for custom branding
    if (strpos($response, 'SmartPrep Learning Center') !== false) {
        echo "✅ Custom branding present\n";
    } else {
        echo "⚠️  Custom branding not detected\n";
    }
    
} else {
    echo "❌ Modules page not accessible\n";
}

echo "\n🧪 INDIVIDUAL BUTTON URL TESTS:\n";

// Test each button URL directly
$buttonUrls = [
    'quiz-generator' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/quiz-generator?website=1',
    'modules-archived' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/modules/archived?website=1'
];

foreach ($buttonUrls as $name => $url) {
    echo "🎯 Testing $name URL: $url\n";
    
    $response = @file_get_contents($url, false, $context);
    if ($response !== false) {
        echo "   ✅ URL accessible\n";
        
        if (strpos($response, 'SQLSTATE') === false) {
            echo "   ✅ No database errors\n";
        } else {
            echo "   ❌ Database errors present\n";
        }
    } else {
        echo "   ❌ URL not accessible\n";
    }
    echo "\n";
}

// Clear view cache to ensure changes take effect
echo "🧹 CLEARING CACHES:\n";
$viewClear = shell_exec('cd C:\xampp\htdocs\A.R.T.C && php artisan view:clear 2>&1');
echo "View cache cleared: " . ($viewClear ? "✅ SUCCESS" : "❌ FAILED") . "\n";

$routeClear = shell_exec('cd C:\xampp\htdocs\A.R.T.C && php artisan route:clear 2>&1');
echo "Route cache cleared: " . ($routeClear ? "✅ SUCCESS" : "❌ FAILED") . "\n";

echo "\n🎉 Button routing fix test complete!\n";
echo "If buttons still redirect incorrectly, check:\n";
echo "1. Browser cache (hard refresh with Ctrl+F5)\n";
echo "2. JavaScript preventing default behavior\n";
echo "3. CSS z-index issues covering buttons\n";
?>
