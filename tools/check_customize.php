<?php
$url = 'http://127.0.0.1:8000/smartprep/dashboard/customize-website';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

$response = curl_exec($ch);
if ($response === false) {
    echo "cURL error: " . curl_error($ch) . PHP_EOL;
    exit(1);
}
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);

echo "HTTP STATUS: $http_code\n";
echo "---HEADERS---\n";
echo $header . "\n";
echo "---BODY SNIPPET (first 800 chars)---\n";
echo substr($body, 0, 800) . "\n";

curl_close($ch);
