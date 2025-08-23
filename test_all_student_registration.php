<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = [
    'Registration Pending' => '/admin-student-registration/pending?preview=true',
    'Registration History' => '/admin-student-registration/history?preview=true',
    'Payment Pending' => '/admin-student-registration/payment/pending?preview=true',
    'Payment History' => '/admin-student-registration/payment/history?preview=true'
];

echo "=== ADMIN STUDENT REGISTRATION TESTS ===\n";

foreach ($urls as $name => $url) {
    echo "Testing {$name}...\n";
    
    try {
        $request = Illuminate\Http\Request::create($url, 'GET');
        $app->instance('request', $request);
        
        $response = $kernel->handle($request);
        $statusCode = $response->getStatusCode();
        
        if ($statusCode === 200) {
            $content = $response->getContent();
            $hasTest1 = strpos($content, 'Test1') !== false || strpos($content, 'test1') !== false;
            $hasNoArtc = strpos(strtolower($content), 'artc') === false;
            
            echo "✅ SUCCESS";
            if ($hasTest1) echo " - Has Test1 branding";
            if ($hasNoArtc) echo " - No ARTC references";
            echo "\n";
        } else {
            echo "❌ FAILED - Status: {$statusCode}\n";
        }
        
    } catch (Exception $e) {
        echo "❌ EXCEPTION - " . $e->getMessage() . "\n";
    }
}

echo "\n=== COMPLETE ===\n";
