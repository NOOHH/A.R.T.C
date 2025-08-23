<?php

echo "=== DETAILED PROFESSOR ERROR ANALYSIS ===\n\n";

$url = 'http://127.0.0.1:8000/professor/dashboard?website=15&preview=true';

echo "Fetching professor dashboard for detailed error analysis...\n";
echo "URL: {$url}\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_error($ch)) {
    echo "❌ cURL Error: " . curl_error($ch) . "\n";
    exit;
}

echo "📊 HTTP Status: {$httpCode}\n";
echo "📄 Content Length: " . strlen($response) . " bytes\n\n";

// Look for specific error patterns
$errorPatterns = [
    'smartprep.professors' => 'Professor table access error',
    'SQLSTATE[42S02]' => 'Table not found SQL error',
    'Base table or view not found' => 'Database table missing',
    'Table \'smartprep.professors\' doesn\'t exist' => 'Direct professor table error',
    'undefined variable' => 'PHP undefined variable',
    'undefined index' => 'PHP undefined array index',
    'undefined offset' => 'PHP undefined array offset',
    'call to undefined method' => 'PHP method error',
    'class not found' => 'PHP class error',
    'fatal error' => 'PHP fatal error',
    'parse error' => 'PHP syntax error'
];

echo "🔍 SCANNING FOR ERRORS...\n\n";

$foundErrors = [];
foreach ($errorPatterns as $pattern => $description) {
    if (stripos($response, $pattern) !== false) {
        $foundErrors[] = $description . " (pattern: {$pattern})";
        echo "❌ FOUND: {$description}\n";
        
        // Extract context around the error
        $lines = explode("\n", $response);
        foreach ($lines as $lineNum => $line) {
            if (stripos($line, $pattern) !== false) {
                echo "   Context Line {$lineNum}: " . trim(strip_tags($line)) . "\n";
                // Show surrounding lines for context
                for ($i = max(0, $lineNum - 2); $i <= min(count($lines) - 1, $lineNum + 2); $i++) {
                    if ($i !== $lineNum) {
                        echo "   Line {$i}: " . trim(strip_tags($lines[$i])) . "\n";
                    }
                }
                break;
            }
        }
        echo "\n";
    }
}

if (empty($foundErrors)) {
    echo "✅ No critical errors detected in obvious error patterns.\n\n";
    echo "Let's check for JavaScript errors or warnings...\n\n";
    
    // Check for JavaScript console errors
    if (preg_match_all('/console\.(error|warn|log)\([^)]+\)/i', $response, $matches)) {
        echo "🔍 JavaScript Console Messages Found:\n";
        foreach ($matches[0] as $consoleMsg) {
            echo "   📝 " . $consoleMsg . "\n";
        }
        echo "\n";
    }
    
    // Check for Laravel error blade directives
    if (preg_match_all('/@error\([^)]+\)/i', $response, $matches)) {
        echo "🔍 Laravel Error Directives Found:\n";
        foreach ($matches[0] as $errorDirective) {
            echo "   📝 " . $errorDirective . "\n";
        }
        echo "\n";
    }
    
    // Check if response contains expected professor dashboard elements
    $expectedElements = [
        'Professor Dashboard' => 'Dashboard title',
        'assigned programs' => 'Programs section',
        'total students' => 'Statistics section',
        'announcements' => 'Announcements section'
    ];
    
    echo "🔍 CHECKING FOR EXPECTED DASHBOARD ELEMENTS...\n\n";
    foreach ($expectedElements as $element => $description) {
        if (stripos($response, $element) !== false) {
            echo "✅ Found: {$description}\n";
        } else {
            echo "❌ Missing: {$description}\n";
        }
    }
}

curl_close($ch);

echo "\n=== ANALYSIS COMPLETE ===\n";
