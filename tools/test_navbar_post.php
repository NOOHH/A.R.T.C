<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);$kernel->bootstrap();

use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://127.0.0.1:8000', 'cookies' => true, 'allow_redirects' => true]);
try {
    // Login as admin
    $login = $client->post('/smartprep/login', [
        'form_params' => [
            'email' => 'admin@smartprep.com',
            'password' => 'admin123',
            '_token' => csrf_token()
        ]
    ]);
} catch (\Throwable $e) { echo "Login failed: {$e->getMessage()}\n"; }

try {
    $resp = $client->post('/smartprep/dashboard/settings/navbar/11', [
        'multipart' => [
            ['name' => 'brand_name', 'contents' => 'Test Brand '.date('H:i:s')],
            ['name' => 'show_login_button', 'contents' => '1']
        ],
        'headers' => ['X-CSRF-TOKEN' => csrf_token(), 'Accept' => 'application/json']
    ]);
    echo "Status: ".$resp->getStatusCode()."\n";
    echo $resp->getBody();
} catch (\Throwable $e) {
    echo "Navbar update failed: {$e->getMessage()}\n";
}
