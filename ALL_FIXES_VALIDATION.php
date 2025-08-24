<?php
// Final comprehensive test of all fixes

echo "==== COMPREHENSIVE FINAL VALIDATION TEST ====\n\n";

$tests = [
    [
        'name' => 'Advanced Tab (Fixed duplicate includes)',
        'url' => 'http://localhost:8000/smartprep/dashboard/customize-website',
        'checks' => [
            'advanced tab' => ['advanced', 1], // Should appear
            'permissions content' => ['permission', 1], // Should have permissions content
        ]
    ],
    [
        'name' => 'Tenant Homepage with ENROLL NOW button',
        'url' => 'http://localhost:8000/t/draft/artc',
        'checks' => [
            'ENROLL NOW button' => ['ENROLL NOW', 1],
            'tenant enrollment URL' => ['draft\/artc\/enrollment', 1],
            'homepage content' => ['hero', 1],
        ]
    ],
    [
        'name' => 'Login/Register Customization Fields',
        'url' => 'http://localhost:8000/smartprep/dashboard/customize-website',
        'checks' => [
            'LOGIN CUSTOMIZATION' => ['LOGIN CUSTOMIZATION', 1],
            'gradient colors' => ['gradient', 1],
            'Registration Form Fields' => ['Registration Form Fields', 1],
        ]
    ]
];

$passed = 0;
$failed = 0;
$errors = [];

foreach ($tests as $test) {
    echo "=== Testing: {$test['name']} ===\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Test Script');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✅ HTTP 200 - Page loaded successfully\n";
        
        foreach ($test['checks'] as $checkName => $checkData) {
            list($pattern, $expectedCount) = $checkData;
            
            $matches = preg_match_all("/$pattern/i", $response);
            
            if ($matches >= $expectedCount) {
                echo "✅ $checkName: Found $matches instances (expected >= $expectedCount)\n";
                $passed++;
            } else {
                echo "❌ $checkName: Found $matches instances (expected >= $expectedCount)\n";
                $failed++;
                $errors[] = "{$test['name']}: $checkName check failed";
            }
        }
    } else {
        echo "❌ HTTP $httpCode - Failed to load page\n";
        $failed++;
        $errors[] = "{$test['name']}: HTTP $httpCode error";
    }
    
    echo "\n";
}

echo "==== FINAL RESULTS ====\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: $failed\n";
echo "Success Rate: " . round(($passed / ($passed + $failed)) * 100, 1) . "%\n";

if (!empty($errors)) {
    echo "\n⚠️  Issues found:\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
}

if ($failed == 0) {
    echo "\n🎉 ALL TESTS PASSED! All fixes are working correctly!\n";
} else {
    echo "\n⚠️  Some issues remain to be resolved.\n";
}

echo "\n==== TEST COMPLETE ====\n";
?>
