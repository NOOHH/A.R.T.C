<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Checking message encryption...\n";
    
    // Get the latest chat record
    $latestChat = DB::table('chats')->orderBy('chat_id', 'desc')->first();
    
    if ($latestChat) {
        echo "Latest Chat ID: {$latestChat->chat_id}\n";
        echo "Sender: {$latestChat->sender_id}, Receiver: {$latestChat->receiver_id}\n";
        echo "Raw body_cipher (encrypted): " . substr($latestChat->body_cipher, 0, 50) . "...\n";
        echo "Length of encrypted data: " . strlen($latestChat->body_cipher) . " characters\n";
        
        // Now use the Chat model to decrypt it
        $chatModel = App\Models\Chat::find($latestChat->chat_id);
        if ($chatModel) {
            echo "Decrypted message via model: '{$chatModel->message}'\n";
            echo "Decrypted body via model: '{$chatModel->body}'\n";
        }
        
        // Check if it looks encrypted (starts with Laravel's encryption format)
        if (strpos($latestChat->body_cipher, 'eyJpdiI6') === 0) {
            echo "✅ Message appears to be properly encrypted with Laravel Crypt\n";
        } else {
            echo "❌ Message does not appear to be encrypted\n";
        }
    } else {
        echo "No chat records found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
