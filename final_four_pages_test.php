<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "FINAL COMPREHENSIVE TEST - All Four Admin Pages\n";
echo "===============================================\n\n";

$pages = [
    'Payment Pending' => 'http://127.0.0.1:8000/t/draft/test1/admin-student-registration/payment/pending?preview=true&t=' . time() . '&website=15',
    'Payment History' => 'http://127.0.0.1:8000/t/draft/test1/admin-student-registration/payment/history?preview=true&t=' . time() . '&website=15',
    'FAQ Management' => 'http://127.0.0.1:8000/t/draft/test1/admin/faq?preview=true&t=' . time() . '&website=15',
    'Assignment Submissions' => 'http://127.0.0.1:8000/t/draft/test1/admin/submissions?preview=true&t=' . time() . '&website=15'
];

$results = [];

foreach ($pages as $pageName => $url) {
    echo "Testing: $pageName\n";
    echo str_repeat('-', 50) . "\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Connection: close'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    $result = [
        'page' => $pageName,
        'status' => $httpCode,
        'success' => false,
        'has_branding' => false,
        'response_size' => 0,
        'branding_count' => 0,
        'error' => $error
    ];

    if ($error) {
        echo "❌ cURL Error: $error\n";
    } else {
        $result['response_size'] = strlen($response);
        $result['branding_count'] = substr_count($response, 'TEST11');
        $result['has_branding'] = $result['branding_count'] > 0;
        
        if ($httpCode === 200) {
            $result['success'] = true;
            echo "✅ HTTP Status: $httpCode (Success)\n";
            echo "📄 Response Size: " . $result['response_size'] . " bytes\n";
            echo "🎨 TEST11 Branding Instances: " . $result['branding_count'] . "\n";
            
            if ($result['has_branding']) {
                echo "✅ Tenant customization applied\n";
            } else {
                echo "❌ No tenant customization found\n";
            }
            
            // Check for specific page indicators
            if (strpos(strtolower($response), strtolower(str_replace(' ', '', $pageName))) !== false ||
                strpos($response, $pageName) !== false) {
                echo "✅ Page content verified\n";
            } else {
                echo "⚠️ Page content not clearly identified\n";
            }
            
        } else {
            echo "❌ HTTP Status: $httpCode (Failed)\n";
            if (strlen($response) < 1000) {
                echo "Response snippet: " . substr($response, 0, 200) . "...\n";
            }
        }
    }
    
    $results[] = $result;
    echo "\n";
}

// Summary
echo "SUMMARY REPORT\n";
echo "==============\n\n";

$allWorking = true;
$allWithBranding = true;

foreach ($results as $result) {
    $status = $result['success'] ? '✅ WORKING' : '❌ FAILED';
    $branding = $result['has_branding'] ? '✅ BRANDED' : '❌ NO BRAND';
    
    echo sprintf("%-25s %s %s (Size: %d bytes, Instances: %d)\n", 
        $result['page'] . ':', 
        $status, 
        $branding,
        $result['response_size'],
        $result['branding_count']
    );
    
    if (!$result['success']) $allWorking = false;
    if (!$result['has_branding']) $allWithBranding = false;
}

echo "\n";

if ($allWorking && $allWithBranding) {
    echo "🎉 SUCCESS: All four admin pages are working with proper tenant customization!\n";
    echo "✅ Payment Pending - Working with TEST11 branding\n";
    echo "✅ Payment History - Working with TEST11 branding\n";
    echo "✅ FAQ Management - Working with TEST11 branding\n";
    echo "✅ Assignment Submissions - Working with TEST11 branding\n";
    echo "\n🔧 SOLUTION IMPLEMENTED:\n";
    echo "• Added tenant preview routes for all missing pages\n";
    echo "• Implemented preview controller methods with mock data\n";
    echo "• Updated admin sidebar to use tenant-aware URLs\n";
    echo "• Applied AdminPreviewCustomization trait for consistent branding\n";
} else {
    echo "⚠️ PARTIAL SUCCESS: Some issues remain\n";
    if (!$allWorking) {
        echo "❌ Not all pages are responding correctly\n";
    }
    if (!$allWithBranding) {
        echo "❌ Not all pages have proper tenant customization\n";
    }
}

echo "\nTest completed at " . date('Y-m-d H:i:s') . "\n";
?>
