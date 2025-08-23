<?php
/**
 * Debug Failing Admin Preview Pages
 * Targeted testing for the 4 remaining failing pages
 */

$timestamp = time();

echo "🔍 DEBUGGING FAILING ADMIN PREVIEW PAGES\n";
echo "==========================================\n\n";

$failingPages = [
    'Students' => "http://localhost:8000/t/draft/test1/admin/students?website=15&preview=true&t={$timestamp}",
    'Directors' => "http://localhost:8000/t/draft/test1/admin/directors?website=15&preview=true&t={$timestamp}",
    'Quiz Generator' => "http://localhost:8000/t/draft/test1/admin/quiz-generator?website=15&preview=true&t={$timestamp}",
    'Payment Pending' => "http://localhost:8000/t/draft/test1/admin-student-registration/payment/pending?website=15&preview=true&t={$timestamp}"
];

foreach ($failingPages as $name => $url) {
    echo "🔍 Testing: {$name}\n";
    echo "   URL: {$url}\n";
    
    try {
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'ignore_errors' => true
            ]
        ]);
        
        $response = file_get_contents($url, false, $context);
        
        if ($response === false) {
            echo "   ❌ Failed to fetch response\n";
        } else {
            // Check for Test1 branding
            if (strpos($response, 'Test1') !== false) {
                echo "   ✅ SUCCESS - Test1 branding found!\n";
            } else {
                // Extract error details
                if (preg_match('/Object of class stdClass could not be converted to string/', $response)) {
                    echo "   ❌ Object Conversion Error - stdClass to string issue\n";
                } elseif (preg_match('/Undefined property: stdClass::\$(\w+)/', $response, $matches)) {
                    echo "   ❌ Missing Property Error - Missing: \${$matches[1]}\n";
                } elseif (preg_match('/HTTP\/1\.\d\s+(\d+)/', $response, $matches)) {
                    echo "   ❌ HTTP Error {$matches[1]}\n";
                } elseif (strpos($response, 'Error') !== false) {
                    // Extract error message
                    if (preg_match('/Error.*?:(.*?)(?:\(View:|$)/s', $response, $matches)) {
                        $errorMsg = trim($matches[1]);
                        echo "   ❌ Error: " . substr($errorMsg, 0, 100) . "...\n";
                    } else {
                        echo "   ❌ Unknown Error\n";
                    }
                } else {
                    echo "   ⚠️  No Test1 branding found\n";
                }
            }
        }
    } catch (Exception $e) {
        echo "   ❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== DEBUG COMPLETE ===\n";
echo "Run this script after each fix to validate changes.\n";
