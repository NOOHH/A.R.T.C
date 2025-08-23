<?php
echo "🧪 ADMIN ROUTING TEST AFTER FIX\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// Test the three problematic routes
$testRoutes = [
    'quiz-generator' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/quiz-generator?website=1',
    'courses-upload' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/courses/upload?website=1',
    'modules-archived' => 'http://127.0.0.1:8000/t/draft/smartprep/admin/modules/archived?website=1'
];

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

$allPassed = true;

foreach ($testRoutes as $name => $url) {
    echo "🎯 Testing: $name\n";
    echo "   URL: $url\n";
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        echo "✅ Route accessible\n";
        
        // Check for database errors
        if (strpos($response, 'SQLSTATE') !== false || strpos($response, 'No database selected') !== false) {
            echo "❌ Database error still present!\n";
            // Extract the error details
            preg_match('/SQLSTATE\[.*?\]: (.+?)(?=\s*\(|$)/s', $response, $matches);
            if (isset($matches[1])) {
                echo "   Error: " . trim($matches[1]) . "\n";
            }
            $allPassed = false;
        } else {
            echo "✅ No database errors detected\n";
        }
        
        // Check for custom branding
        if (strpos($response, 'SmartPrep Learning Center') !== false) {
            echo "✅ Custom branding present\n";
        } else {
            echo "⚠️  Custom branding not detected\n";
        }
        
        // Check for successful page content
        if (strpos($response, 'Preview') !== false || strpos($response, 'working correctly') !== false) {
            echo "✅ Page content loaded successfully\n";
        } else {
            echo "⚠️  Unexpected page content\n";
        }
        
    } else {
        echo "❌ Route not accessible\n";
        $allPassed = false;
    }
    
    echo "\n";
}

echo "📊 FINAL RESULT:\n";
if ($allPassed) {
    echo "🎉 ALL ROUTES WORKING! Database issues resolved!\n";
} else {
    echo "❌ Some issues remain - check the details above\n";
}

// Also test the Laravel route list to make sure all routes are registered
echo "\n🛣️ ROUTE REGISTRATION CHECK:\n";
$routeOutput = shell_exec('cd C:\xampp\htdocs\A.R.T.C && php artisan route:list --compact | findstr "admin.*quiz-generator\\|admin.*courses.*upload\\|admin.*modules.*archived" 2>&1');

if ($routeOutput) {
    echo "✅ Route list output:\n";
    echo $routeOutput;
} else {
    echo "❌ Could not get route list\n";
}

echo "\n🎯 Test complete!\n";
?>
