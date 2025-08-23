<?php
echo "🔍 NAVBAR CUSTOMIZATION ISSUE - DIAGNOSTIC TEST\n";
echo "================================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tenant = 'test1';
$params = '?website=15&preview=true&t=' . time();

// Pages that should show navbar customization but may be failing
$problematicPages = [
    'Payment Pending' => '/t/draft/' . $tenant . '/admin-student-registration/payment/pending',
    'Payment History' => '/t/draft/' . $tenant . '/admin-student-registration/payment/history', 
    'FAQ Management' => '/t/draft/' . $tenant . '/admin/faq',
    'Assignment Submissions' => '/t/draft/' . $tenant . '/admin/submissions'
];

echo "Testing pages that should show 'Test1' navbar but may not be working...\n\n";

$workingCount = 0;
$totalCount = count($problematicPages);

foreach ($problematicPages as $pageName => $path) {
    $url = $baseUrl . $path . $params;
    echo "🧪 Testing: $pageName\n";
    echo "   URL: $url\n";
    
    try {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET',
                'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
        
        $content = @file_get_contents($url, false, $context);
        
        if ($content === false) {
            echo "   ❌ FAILED - Could not load page\n";
            continue;
        }
        
        // Check for custom branding "Test1"
        $hasTest1Branding = strpos($content, 'Test1') !== false;
        $hasDefaultBranding = strpos($content, 'Ascendo Review and Training Center') !== false || 
                             strpos($content, 'Ascendo Review &amp; Training Center') !== false;
        
        if ($hasTest1Branding && !$hasDefaultBranding) {
            echo "   ✅ WORKING - Shows 'Test1' branding\n";
            $workingCount++;
        } elseif ($hasTest1Branding && $hasDefaultBranding) {
            echo "   ⚠️  MIXED - Shows both 'Test1' and default branding\n";
        } elseif ($hasDefaultBranding) {
            echo "   ❌ PROBLEM - Shows default branding instead of 'Test1'\n";
        } else {
            echo "   ❓ UNKNOWN - Cannot detect branding\n";
        }
        
        // Check if it's a 404 or error page
        if (strpos($content, '404') !== false || strpos($content, 'Not Found') !== false) {
            echo "   ⚠️  Page returns 404 - Route may not exist\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ ERROR - " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "📊 SUMMARY:\n";
echo "===========\n";
echo "Working pages: $workingCount / $totalCount\n";
echo "Failed pages: " . ($totalCount - $workingCount) . " / $totalCount\n\n";

echo "🔧 REQUIRED FIXES:\n";
echo "==================\n";
echo "1. Add tenant preview routes for Payment Pending/History\n";
echo "2. Add tenant preview routes for FAQ Management\n";
echo "3. Add tenant preview routes for Assignment Submissions\n";
echo "4. Update sidebar to use tenant URLs for these pages\n";
echo "5. Add AdminPreviewCustomization trait to controllers\n";
echo "6. Create preview methods in controllers\n";
