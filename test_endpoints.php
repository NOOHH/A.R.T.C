<?php
// Test script to check admin modules endpoints
echo "🧪 TESTING ADMIN ENDPOINTS\n";
echo "==========================\n\n";

$baseUrl = 'http://127.0.0.1:8000';

// Function to make HTTP requests with session cookies
function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => __DIR__ . '/cookie.txt',
        CURLOPT_COOKIEFILE => __DIR__ . '/cookie.txt',
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept: application/json, text/html, */*',
            'X-Requested-With: XMLHttpRequest'
        ]
    ]);
    
    if ($data && $method === 'POST') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['response' => $response, 'code' => $httpCode];
}

// 1. First login to establish session
echo "1. TESTING LOGIN:\n";
$loginData = [
    'username' => 'admin',
    'password' => 'admin123',
    'user_type' => 'admin'
];

$result = makeRequest($baseUrl . '/login', 'POST', http_build_query($loginData));
echo "Login attempt: HTTP " . $result['code'] . "\n";

if ($result['code'] == 200 || $result['code'] == 302) {
    echo "✅ Login successful or redirected\n";
} else {
    echo "❌ Login failed\n";
}

// 2. Test admin modules page
echo "\n2. TESTING ADMIN MODULES PAGE:\n";
$result = makeRequest($baseUrl . '/admin/modules?program_id=40');
echo "Admin modules page: HTTP " . $result['code'] . "\n";

if ($result['code'] == 200) {
    echo "✅ Admin modules page accessible\n";
    
    // Check for JavaScript errors in the response
    if (strpos($result['response'], 'Unexpected token') !== false) {
        echo "❌ JavaScript syntax error detected in response\n";
    } else {
        echo "✅ No obvious JavaScript syntax errors\n";
    }
} else {
    echo "❌ Admin modules page not accessible\n";
}

// 3. Test course API endpoint
echo "\n3. TESTING COURSE API:\n";

// First, let's see what courses exist
$result = makeRequest($baseUrl . '/admin/courses');
echo "Course list API: HTTP " . $result['code'] . "\n";

if ($result['code'] == 200) {
    echo "✅ Course list API accessible\n";
    
    $data = json_decode($result['response'], true);
    if ($data && isset($data['courses'])) {
        echo "✅ Course data found: " . count($data['courses']) . " courses\n";
        
        // Test with first course if available
        if (!empty($data['courses'])) {
            $firstCourse = $data['courses'][0];
            $courseId = $firstCourse['subject_id'];
            
            echo "\n4. TESTING SPECIFIC COURSE API:\n";
            $result = makeRequest($baseUrl . '/admin/courses/' . $courseId);
            echo "Course detail API (ID: $courseId): HTTP " . $result['code'] . "\n";
            
            if ($result['code'] == 200) {
                echo "✅ Course detail API working\n";
            } else {
                echo "❌ Course detail API failed\n";
                echo "Response: " . substr($result['response'], 0, 200) . "...\n";
            }
        }
    } else {
        echo "❌ Invalid course data format\n";
    }
} else {
    echo "❌ Course list API not accessible\n";
    echo "Response: " . substr($result['response'], 0, 200) . "...\n";
}

echo "\n🏁 ENDPOINT TESTING COMPLETE\n";
