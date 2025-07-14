<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== Testing Controller Method Directly ===\n";

$controller = new \App\Http\Controllers\StudentRegistrationController();
$request = new \Illuminate\Http\Request(['program_id' => 33]);

$response = $controller->getBatchesByProgram($request);
echo "Response status: " . $response->getStatusCode() . "\n";
echo "Raw response content:\n";
echo $response->getContent() . "\n";

$data = json_decode($response->getContent(), true);
echo "\nParsed data:\n";
print_r($data);

if (is_array($data) && count($data) > 0) {
    echo "\nFirst batch details:\n";
    foreach($data as $index => $batch) {
        echo "Batch $index:\n";
        foreach($batch as $key => $value) {
            echo "  $key: $value\n";
        }
    }
}
?>
