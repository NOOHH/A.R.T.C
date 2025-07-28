<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test 1: Send a message from user 179 to a professor
session(['user_id' => 179, 'user_name' => 'Vince Michael Dela Vega', 'role' => 'student']);

try {
    echo "=== Testing Message Send & Receive ===\n";
    
    // First, let's find a professor to send to
    echo "Finding professors...\n";
    $professors = DB::table('professors')->where('professor_archived', false)->limit(3)->get();
    foreach ($professors as $prof) {
        echo "Professor ID: {$prof->professor_id}, Name: {$prof->professor_name}, Email: {$prof->professor_email}\n";
    }
    
    if ($professors->count() > 0) {
        $professorId = $professors->first()->professor_id;
        
        // Create a test message
        echo "\nSending message from user 179 to professor {$professorId}...\n";
        $chat = App\Models\Chat::create([
            'sender_id' => 179,
            'receiver_id' => $professorId,
            'message' => 'Test message from student to professor - ' . date('H:i:s'),
            'sent_at' => now(),
            'is_read' => false
        ]);
        
        echo "Message created with ID: {$chat->chat_id}\n";
        echo "Encrypted content: " . substr($chat->body_cipher, 0, 50) . "...\n";
        echo "Decrypted message: '{$chat->message}'\n";
        
        // Now test retrieving messages
        echo "\nTesting message retrieval...\n";
        $messages = App\Models\Chat::where(function ($q) use ($professorId) {
            $q->where('sender_id', 179)->where('receiver_id', $professorId);
        })->orWhere(function ($q) use ($professorId) {
            $q->where('sender_id', $professorId)->where('receiver_id', 179);
        })->orderBy('sent_at', 'asc')->get();
        
        echo "Found {$messages->count()} messages in conversation:\n";
        foreach ($messages as $msg) {
            echo "- [{$msg->sent_at}] From: {$msg->sender_id} To: {$msg->receiver_id} - '{$msg->message}'\n";
        }
    }
    
    // Test 2: Check search functionality
    echo "\n=== Testing Search Functionality ===\n";
    
    // Test student search (should work for professors)
    echo "Testing student search...\n";
    $students = DB::table('users')->where('role', 'student')->limit(3)->get();
    foreach ($students as $student) {
        echo "Student: {$student->user_firstname} {$student->user_lastname} ({$student->email})\n";
    }
    
    // Test professor search
    echo "\nTesting professor search...\n";
    $professorSearch = DB::table('professors')
        ->where('professor_archived', false)
        ->where('professor_name', 'like', '%a%')
        ->limit(3)
        ->get();
    
    foreach ($professorSearch as $prof) {
        echo "Professor: {$prof->professor_name} ({$prof->professor_email})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
