<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\GeminiQuizService;
use Illuminate\Support\Facades\Log;

echo "🔧 Debug Gemini Service Issues\n";
echo "================================\n";

try {
    $geminiService = new GeminiQuizService();
    
    // Test content
    $testContent = "Cloud computing is a technology that provides on-demand access to computing resources over the internet. The Shared Responsibility Model divides security responsibilities between cloud providers and customers. In Infrastructure as a Service (IaaS), customers have more responsibility compared to Platform as a Service (PaaS) or Software as a Service (SaaS). Data encryption is essential in cloud environments, involving both encryption at rest and encryption in transit.";
    
    echo "✓ Test content length: " . strlen($testContent) . " characters\n";
    echo "✓ API Key configured: " . (empty(env('GEMINI_API_KEY')) ? "No" : "Yes") . "\n";
    
    // Enable more detailed logging
    Log::info('Starting Gemini debug test');
    
    // Call the service with detailed logging
    $result = $geminiService->generateQuiz($testContent, 2, 1);
    
    echo "\n🔍 Debug Results:\n";
    echo "================\n";
    
    if (!empty($result['error'])) {
        echo "❌ Error: " . $result['error'] . "\n";
    }
    
    if (!empty($result['mcqs'])) {
        echo "✅ MCQs generated: " . count($result['mcqs']) . "\n";
        foreach ($result['mcqs'] as $i => $mcq) {
            echo "  MCQ " . ($i+1) . ": " . substr($mcq['text'], 0, 80) . "...\n";
        }
    }
    
    if (!empty($result['true_false'])) {
        echo "✅ True/False generated: " . count($result['true_false']) . "\n";
        foreach ($result['true_false'] as $i => $tf) {
            echo "  T/F " . ($i+1) . ": " . substr($tf['statement'], 0, 80) . "...\n";
        }
    }
    
    if (!empty($result['raw_content'])) {
        echo "📝 Raw API Response (first 500 chars):\n";
        echo substr($result['raw_content'], 0, 500) . "\n";
    }
    
    echo "\n🔧 Attempting simple direct API test...\n";
    
    // Direct API test
    $apiKey = env('GEMINI_API_KEY');
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key={$apiKey}";
    
    $simpleData = [
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => 'Based on this content: "' . $testContent . '" Generate exactly 1 multiple choice question with 4 options (A, B, C, D) and exactly 1 true/false statement. Format as: MCQ: Question? A. Option B. Option C. Option D. Option Answer: X T/F: Statement (True/False)']
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 1000
        ]
    ];
    
    $context = stream_context_create([
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($simpleData),
            'timeout' => 30
        ]
    ]);
    
    $directResult = file_get_contents($url, false, $context);
    
    if ($directResult !== FALSE) {
        $response = json_decode($directResult, true);
        if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            echo "✅ Direct API call successful!\n";
            echo "Response: " . $response['candidates'][0]['content']['parts'][0]['text'] . "\n";
        } else {
            echo "❌ Unexpected direct API response format\n";
        }
    } else {
        echo "❌ Direct API call failed\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>
