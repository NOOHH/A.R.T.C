<?php
// Logs in as admin@smartprep.com, then POSTs to update general settings for website id 11

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

$base = 'http://127.0.0.1:8000';
$jar = new CookieJar();
$client = new Client(['base_uri' => $base, 'cookies' => $jar]);

// 1) GET login page to get CSRF token
$res = $client->get('/smartprep/login');
$body = (string)$res->getBody();
if (!preg_match('/meta name="csrf-token" content="([^"]+)"/', $body, $m)) {
    echo "CSRF token not found on login page\n";
    exit(1);
}
$csrf = $m[1];

// 2) POST login
$res = $client->post('/smartprep/login', [
    'form_params' => [
        '_token' => $csrf,
        'email' => 'admin@smartprep.com',
        'password' => 'admin123'
    ],
    'allow_redirects' => false
]);

$status = $res->getStatusCode();
$loc = $res->getHeaderLine('Location');
echo "Login status: $status, Location: $loc\n";

// 3) GET customize page to get new CSRF token and confirm access
$res = $client->get('/smartprep/dashboard/customize-website', ['allow_redirects' => false]);
echo "Customize GET status: " . $res->getStatusCode() . "\n";
$body = (string)$res->getBody();
if (!preg_match('/meta name="csrf-token" content="([^"]+)"/', $body, $m)) {
    echo "CSRF not found on customize page\n";
} else {
    echo "Customize CSRF found\n";
    $csrf = $m[1];
}

// 4) POST update general settings for website 11
$websiteId = 11;
$endpoint = "/smartprep/dashboard/settings/general/{$websiteId}";

$res = $client->post($endpoint, [
    'headers' => ['X-Requested-With' => 'XMLHttpRequest'],
    'form_params' => [
        '_token' => $csrf,
        'brand_name' => 'Automated Test Brand ' . time(),
        'contact_email' => 'admin@testwebsite.com'
    ]
]);

echo "POST status: " . $res->getStatusCode() . "\n";
echo "Response body:\n" . (string)$res->getBody() . "\n";

