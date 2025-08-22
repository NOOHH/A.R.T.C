<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);$kernel->bootstrap();

use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://127.0.0.1:8000', 'cookies' => true, 'allow_redirects' => true]);

function extractCsrf($html){ if(preg_match('/name="_token" value="([^"]+)"/',$html,$m)) return $m[1]; if(preg_match('/meta name="csrf-token" content="([^"]+)"/',$html,$m)) return $m[1]; return null; }

// Step 1: GET login page
$loginPage = $client->get('/smartprep/login');
$csrf = extractCsrf((string)$loginPage->getBody());
if(!$csrf){ echo "Failed to extract CSRF\n"; exit(1);} echo "CSRF: $csrf\n";

// Step 2: POST login
$loginResp = $client->post('/smartprep/login', [
    'form_params' => [
        'email' => 'admin@smartprep.com',
        'password' => 'admin123',
        '_token' => $csrf
    ],
    'headers' => ['Referer' => 'http://127.0.0.1:8000/smartprep/login']
]);
echo "Login status: ".$loginResp->getStatusCode()."\n";

// Step 3: POST navbar update
$csrf2 = extractCsrf((string)$client->get('/smartprep/dashboard/customize-website?website=11')->getBody());
$brand = 'Test Brand '.date('H:i:s');
$resp = $client->post('/smartprep/dashboard/settings/navbar/11', [
    'multipart' => [
        ['name' => 'brand_name', 'contents' => $brand],
        ['name' => 'show_login_button', 'contents' => '1']
    ],
    'headers' => ['X-CSRF-TOKEN' => $csrf2, 'Accept' => 'application/json','Referer'=>'http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=11']
]);

echo "Navbar POST status: ".$resp->getStatusCode()."\n";
echo $resp->getBody()."\n";
