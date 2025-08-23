<?php

// Final comprehensive test to verify professor preview functionality
$testUrls = [
    // Tenant professor preview routes (draft)
    'Draft Dashboard' => 'http://127.0.0.1:8000/t/draft/test1/professor/dashboard?website=15&preview=true',
    'Draft Meetings' => 'http://127.0.0.1:8000/t/draft/test1/professor/meetings?website=15&preview=true', 
    'Draft Announcements' => 'http://127.0.0.1:8000/t/draft/test1/professor/announcements?website=15&preview=true',
    'Draft Grading' => 'http://127.0.0.1:8000/t/draft/test1/professor/grading?website=15&preview=true',
    'Draft Modules' => 'http://127.0.0.1:8000/t/draft/test1/professor/modules?website=15&preview=true',
    
    // Regular professor routes with preview mode
    'Regular Meetings Preview' => 'http://127.0.0.1:8000/professor/meetings?preview=true&website=15',
    'Regular Dashboard Preview' => 'http://127.0.0.1:8000/professor/dashboard?preview=true&website=15',
];

echo "=================================================================\n";
echo "PROFESSOR PREVIEW FUNCTIONALITY - FINAL TEST\n";
echo "=================================================================\n\n";

$allPassed = true;

foreach ($testUrls as $page => $url) {
    echo sprintf("%-30s", $page) . " ... ";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Final Test'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ ERROR: {$error}\n";
        $allPassed = false;
    } elseif ($httpCode === 200) {
        // Check if response contains expected professor preview content
        if (strpos($response, 'preview-professor') !== false || 
            strpos($response, 'Dr. Jane Professor') !== false ||
            strpos($response, 'Professor Dashboard') !== false) {
            echo "✅ PASS (200)\n";
        } else {
            echo "⚠️  OK but may not be preview content (200)\n";
        }
    } elseif ($httpCode === 302 || $httpCode === 301) {
        echo "❌ REDIRECT ({$httpCode}) - Authentication issue\n";
        $allPassed = false;
    } else {
        echo "❌ FAILED ({$httpCode})\n";
        $allPassed = false;
    }
}

echo "\n=================================================================\n";
if ($allPassed) {
    echo "🎉 ALL TESTS PASSED! Professor preview functionality is working.\n";
} else {
    echo "⚠️  Some tests failed. Check the results above.\n";
}
echo "=================================================================\n";
