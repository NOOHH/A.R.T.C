<?php
// Test complete analytics functionality including filtering and exports

session_start();

// Set admin session
$_SESSION['user_id'] = 1;
$_SESSION['user_type'] = 'admin';
$_SESSION['user_name'] = 'Test Admin';

// Test 1: Basic analytics data without filters
echo "=== TEST 1: Basic Analytics Data (No Filters) ===\n";
$url1 = 'http://localhost/A.R.T.C/admin/analytics';
$ch1 = curl_init();
curl_setopt($ch1, CURLOPT_URL, $url1);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_HEADER, false);
curl_setopt($ch1, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch1, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, true);

$response1 = curl_exec($ch1);
$httpCode1 = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
curl_close($ch1);

echo "HTTP Status: $httpCode1\n";
if ($httpCode1 == 200) {
    echo "✅ Basic analytics page loaded successfully\n";
    
    // Check if Students Needing Attention section exists
    if (strpos($response1, 'Students Needing Attention') !== false) {
        echo "✅ Students Needing Attention section found on page\n";
    } else {
        echo "❌ Students Needing Attention section NOT found on page\n";
    }
    
    // Check if table update functions exist
    if (strpos($response1, 'updateBottomPerformersTable') !== false) {
        echo "✅ updateBottomPerformersTable function found\n";
    } else {
        echo "❌ updateBottomPerformersTable function NOT found\n";
    }
} else {
    echo "❌ Failed to load analytics page\n";
}

echo "\n";

// Test 2: Test filtering with year parameter
echo "=== TEST 2: Analytics with Year Filter (2024) ===\n";
$url2 = 'http://localhost/A.R.T.C/admin/analytics?year=2024';
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $url2);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HEADER, false);
curl_setopt($ch2, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch2, CURLOPT_COOKIEJAR, 'cookies.txt');

$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

echo "HTTP Status: $httpCode2\n";
if ($httpCode2 == 200) {
    echo "✅ Analytics with year filter loaded successfully\n";
} else {
    echo "❌ Failed to load analytics with year filter\n";
}

echo "\n";

// Test 3: Test filtering with multiple parameters
echo "=== TEST 3: Analytics with Multiple Filters (Year + Month) ===\n";
$url3 = 'http://localhost/A.R.T.C/admin/analytics?year=2024&month=12';
$ch3 = curl_init();
curl_setopt($ch3, CURLOPT_URL, $url3);
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_HEADER, false);
curl_setopt($ch3, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch3, CURLOPT_COOKIEJAR, 'cookies.txt');

$response3 = curl_exec($ch3);
$httpCode3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
curl_close($ch3);

echo "HTTP Status: $httpCode3\n";
if ($httpCode3 == 200) {
    echo "✅ Analytics with multiple filters loaded successfully\n";
} else {
    echo "❌ Failed to load analytics with multiple filters\n";
}

echo "\n";

// Test 4: Test CSV export functionality
echo "=== TEST 4: CSV Export Functionality ===\n";
$url4 = 'http://localhost/A.R.T.C/admin/analytics/export?format=csv';
$ch4 = curl_init();
curl_setopt($ch4, CURLOPT_URL, $url4);
curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch4, CURLOPT_HEADER, true);
curl_setopt($ch4, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch4, CURLOPT_COOKIEJAR, 'cookies.txt');

$response4 = curl_exec($ch4);
$httpCode4 = curl_getinfo($ch4, CURLINFO_HTTP_CODE);
curl_close($ch4);

echo "HTTP Status: $httpCode4\n";
if ($httpCode4 == 200) {
    echo "✅ CSV export completed successfully\n";
    
    // Check if Students Needing Attention section exists in CSV
    if (strpos($response4, 'STUDENTS NEEDING SUPPORT') !== false) {
        echo "✅ Students Needing Attention section found in CSV export\n";
    } else {
        echo "❌ Students Needing Attention section NOT found in CSV export\n";
    }
    
    // Check for other required sections
    if (strpos($response4, 'TOP PERFORMERS') !== false) {
        echo "✅ Top Performers section found in CSV\n";
    }
    if (strpos($response4, 'ANALYTICS SUMMARY') !== false) {
        echo "✅ Analytics Summary section found in CSV\n";
    }
    
} else {
    echo "❌ Failed to export CSV\n";
}

echo "\n";

// Test 5: Test Excel export functionality
echo "=== TEST 5: Excel Export Functionality ===\n";
$url5 = 'http://localhost/A.R.T.C/admin/analytics/export?format=excel';
$ch5 = curl_init();
curl_setopt($ch5, CURLOPT_URL, $url5);
curl_setopt($ch5, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch5, CURLOPT_HEADER, true);
curl_setopt($ch5, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch5, CURLOPT_COOKIEJAR, 'cookies.txt');

$response5 = curl_exec($ch5);
$httpCode5 = curl_getinfo($ch5, CURLINFO_HTTP_CODE);
curl_close($ch5);

echo "HTTP Status: $httpCode5\n";
if ($httpCode5 == 200) {
    echo "✅ Excel export completed successfully\n";
    
    // Parse JSON response for Excel export
    $headerSize = curl_getinfo($ch5, CURLINFO_HEADER_SIZE);
    $body = substr($response5, $headerSize);
    $excelData = json_decode($body, true);
    
    if ($excelData && isset($excelData['bottom_performers'])) {
        echo "✅ Students Needing Attention (bottom_performers) data found in Excel export\n";
        echo "   Number of bottom performers: " . count($excelData['bottom_performers']) . "\n";
    } else {
        echo "❌ Students Needing Attention data NOT found in Excel export\n";
    }
    
} else {
    echo "❌ Failed to export Excel\n";
}

echo "\n";

// Test 6: Test PDF export functionality
echo "=== TEST 6: PDF Export Functionality ===\n";
$url6 = 'http://localhost/A.R.T.C/admin/analytics/export?format=pdf';
$ch6 = curl_init();
curl_setopt($ch6, CURLOPT_URL, $url6);
curl_setopt($ch6, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch6, CURLOPT_HEADER, false);
curl_setopt($ch6, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch6, CURLOPT_COOKIEJAR, 'cookies.txt');

$response6 = curl_exec($ch6);
$httpCode6 = curl_getinfo($ch6, CURLINFO_HTTP_CODE);
curl_close($ch6);

echo "HTTP Status: $httpCode6\n";
if ($httpCode6 == 200) {
    echo "✅ PDF export completed successfully\n";
    
    // Check if Students Needing Attention section exists in PDF
    if (strpos($response6, 'Students Needing Attention') !== false) {
        echo "✅ Students Needing Attention section found in PDF export\n";
    } else {
        echo "❌ Students Needing Attention section NOT found in PDF export\n";
    }
    
} else {
    echo "❌ Failed to export PDF\n";
}

echo "\n";

// Test 7: Test students list endpoint with filters
echo "=== TEST 7: Students List with Filters ===\n";
$url7 = 'http://localhost/A.R.T.C/admin/analytics/students-list?year=2024';
$ch7 = curl_init();
curl_setopt($ch7, CURLOPT_URL, $url7);
curl_setopt($ch7, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch7, CURLOPT_HEADER, false);
curl_setopt($ch7, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch7, CURLOPT_COOKIEJAR, 'cookies.txt');

$response7 = curl_exec($ch7);
$httpCode7 = curl_getinfo($ch7, CURLINFO_HTTP_CODE);
curl_close($ch7);

echo "HTTP Status: $httpCode7\n";
if ($httpCode7 == 200) {
    echo "✅ Students list with filters loaded successfully\n";
    $studentsData = json_decode($response7, true);
    if ($studentsData && isset($studentsData['data'])) {
        echo "   Number of students returned: " . count($studentsData['data']) . "\n";
    }
} else {
    echo "❌ Failed to load students list with filters\n";
}

echo "\n=== TEST SUMMARY ===\n";
echo "All critical functionality tests completed.\n";
echo "Check the results above to verify:\n";
echo "1. ✅ Basic analytics page loads\n";
echo "2. ✅ Students Needing Attention section is present\n";
echo "3. ✅ Filtering works with year/month parameters\n";
echo "4. ✅ Export functionality includes all required sections\n";
echo "5. ✅ Students list endpoint works with filters\n";
?>
