<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test as student sending to professor
session(['user_id' => 179, 'user_name' => 'Vince Michael Dela Vega', 'role' => 'student', 'logged_in' => true]);

try {
    echo "=== TESTING CHAT SYSTEM ===\n\n";
    
    // 1. Test message sending
    echo "1. Testing message sending from student (179) to professor (8)...\n";
    
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'receiver_id' => 8,
        'message' => 'Test message from student to professor - ' . date('H:i:s')
    ]);
    $request->headers->set('Content-Type', 'application/json');
    
    $controller = new \App\Http\Controllers\ChatController();
    $response = $controller->sendSessionMessage($request);
    
    echo "Send Response Status: " . $response->getStatusCode() . "\n";
    $responseData = json_decode($response->getContent(), true);
    echo "Send Response: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($responseData['success']) {
        $messageId = $responseData['id'];
        echo "✅ Message sent successfully with ID: $messageId\n\n";
        
        // 2. Test message retrieval as student
        echo "2. Testing message retrieval as student...\n";
        $getRequest = new \Illuminate\Http\Request();
        $getRequest->merge(['with' => 8]);
        
        $getResponse = $controller->getSessionMessages($getRequest);
        echo "Get Response Status: " . $getResponse->getStatusCode() . "\n";
        $getResponseData = json_decode($getResponse->getContent(), true);
        
        if ($getResponseData['success']) {
            echo "✅ Found " . count($getResponseData['messages']) . " messages\n";
            foreach ($getResponseData['messages'] as $msg) {
                echo "- Message ID {$msg['id']}: \"{$msg['message']}\" from {$msg['sender']['name']} ({$msg['sender']['role']})\n";
            }
        } else {
            echo "❌ Failed to get messages: " . json_encode($getResponseData) . "\n";
        }
        
        // 3. Test message retrieval as professor (simulate professor login)
        echo "\n3. Testing message retrieval as professor...\n";
        session(['professor_id' => 8, 'user_name' => 'Professor User', 'role' => 'professor', 'logged_in' => true]);
        session()->forget('user_id'); // Clear student session
        
        $professorGetRequest = new \Illuminate\Http\Request();
        $professorGetRequest->merge(['with' => 179]);
        
        $professorController = new \App\Http\Controllers\ChatController();
        $professorGetResponse = $professorController->getSessionMessages($professorGetRequest);
        echo "Professor Get Response Status: " . $professorGetResponse->getStatusCode() . "\n";
        $professorGetResponseData = json_decode($professorGetResponse->getContent(), true);
        
        if ($professorGetResponseData['success']) {
            echo "✅ Professor found " . count($professorGetResponseData['messages']) . " messages\n";
            foreach ($professorGetResponseData['messages'] as $msg) {
                echo "- Message ID {$msg['id']}: \"{$msg['message']}\" from {$msg['sender']['name']} ({$msg['sender']['role']})\n";
            }
        } else {
            echo "❌ Professor failed to get messages: " . json_encode($professorGetResponseData) . "\n";
        }
        
        // 4. Test search functionality
        echo "\n4. Testing search functionality...\n";
        
        // Test student search from professor perspective
        $searchRequest = new \Illuminate\Http\Request();
        $searchRequest->merge(['search' => 'vince', 'type' => 'student']);
        
        $searchResponse = $professorController->searchUsers($searchRequest);
        echo "Search Response Status: " . $searchResponse->getStatusCode() . "\n";
        $searchResponseData = json_decode($searchResponse->getContent(), true);
        
        if ($searchResponseData['success']) {
            echo "✅ Search found " . count($searchResponseData['users']) . " students\n";
            foreach ($searchResponseData['users'] as $user) {
                echo "- {$user['name']} ({$user['email']}) - {$user['role']}\n";
            }
        } else {
            echo "❌ Search failed: " . json_encode($searchResponseData) . "\n";
        }
        
    } else {
        echo "❌ Failed to send message: " . $responseData['error'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
