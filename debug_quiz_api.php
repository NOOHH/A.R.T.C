<?php

// Debug script to see raw QuizAPI response
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== Raw QuizAPI Response Debug ===\n";

try {
    $apiKey = 'hT3HqPH5nfI86Ixt8sOisvZqgF7SMNaGyVcAeyTP';
    $baseUrl = 'https://quizapi.io/api/v1';
    
    $response = Http::timeout(30)->get($baseUrl . '/questions', [
        'apiKey' => $apiKey,
        'limit' => 1
    ]);

    echo "Status: " . $response->status() . "\n";
    echo "Successful: " . ($response->successful() ? 'Yes' : 'No') . "\n\n";

    if ($response->successful()) {
        $data = $response->json();
        echo "Raw response:\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
        
        if (!empty($data) && is_array($data)) {
            echo "\nFirst question structure:\n";
            $firstQuestion = $data[0];
            echo "Keys: " . implode(', ', array_keys($firstQuestion)) . "\n";
            
            foreach ($firstQuestion as $key => $value) {
                if (is_array($value)) {
                    echo "$key: " . json_encode($value) . "\n";
                } else {
                    echo "$key: " . substr(trim($value), 0, 100) . "...\n";
                }
            }
        }
    } else {
        echo "Response body: " . $response->body() . "\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
