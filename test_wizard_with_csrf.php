<?php

// Test wizard submission with proper CSRF token
echo "🧪 Testing Wizard Submission with CSRF\n";
echo "======================================\n\n";

// First, get the CSRF token from the modular enrollment page
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/enrollment/modular');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Failed to load enrollment page. HTTP Code: $httpCode\n";
    exit(1);
}

// Extract CSRF token from the page
preg_match('/name="_token"\s+value="([^"]+)"/', $response, $matches);
if (!isset($matches[1])) {
    preg_match('/window\.Laravel\s*=\s*\{[^}]*"csrfToken":"([^"]+)"/', $response, $matches);
}

if (!isset($matches[1])) {
    echo "❌ Could not extract CSRF token\n";
    exit(1);
}

$csrfToken = $matches[1];
echo "✅ CSRF token extracted: " . substr($csrfToken, 0, 20) . "...\n";

// Prepare test data for submission
$data = [
    '_token' => $csrfToken,
    'package_id' => 12, // From our previous test
    'module_ids' => [40, 41], // From our previous test
    'learning_mode' => 'Online',
    'account_data' => [
        'firstName' => 'Test',
        'lastName' => 'Wizard',
        'email' => 'test_wizard_' . time() . '@example.com',
        'password' => 'password123'
    ],
    'profile_data' => [
        'first_name' => 'Test',
        'last_name' => 'Wizard',
        'test' => 'Test value'
    ]
];

echo "✅ Test data prepared\n";
echo "   - Package ID: {$data['package_id']}\n";
echo "   - Modules: " . implode(', ', $data['module_ids']) . "\n";
echo "   - Email: {$data['account_data']['email']}\n\n";

// Submit to the wizard endpoint
$url = 'http://127.0.0.1:8000/enrollment/modular/store';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest',
    'X-CSRF-TOKEN: ' . $csrfToken
]);

echo "🚀 Submitting wizard data...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

if ($httpCode === 200 || $httpCode === 201) {
    $responseData = json_decode($response, true);
    if (isset($responseData['success']) && $responseData['success']) {
        echo "✅ Wizard submission successful!\n";
        echo "Message: " . ($responseData['message'] ?? 'No message') . "\n";
        
        // Verify the data was inserted
        require_once __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $kernel->bootstrap();
        
        $latestUser = \App\Models\User::where('email', $data['account_data']['email'])->first();
        if ($latestUser) {
            echo "✅ User created: {$latestUser->user_firstname} {$latestUser->user_lastname} (ID: {$latestUser->user_id})\n";
            
            $registration = $latestUser->registration;
            if ($registration) {
                echo "✅ Registration created: ID {$registration->registration_id}\n";
            }
            
            $enrollment = \App\Models\Enrollment::where('user_id', $latestUser->user_id)->first();
            if ($enrollment) {
                echo "✅ Enrollment created: ID {$enrollment->enrollment_id}\n";
            }
        }
    } else {
        echo "❌ Wizard submission failed\n";
        echo "Error: " . ($responseData['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "❌ HTTP error during submission\n";
    if (strpos($response, 'CSRF') !== false) {
        echo "CSRF token issue detected\n";
    }
}

echo "\n✅ Wizard test completed!\n";
echo "===========================\n";
