<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Chat;
use App\Models\User;

try {
    echo "Chat count: " . Chat::count() . "\n";
    
    // Get some users for testing
    $users = User::take(2)->get();
    if ($users->count() >= 2) {
        $sender = $users[0];
        $receiver = $users[1];
        
        echo "Testing encryption with users:\n";
        echo "- Sender: {$sender->user_firstname} {$sender->user_lastname} (ID: {$sender->user_id})\n";
        echo "- Receiver: {$receiver->user_firstname} {$receiver->user_lastname} (ID: {$receiver->user_id})\n";
        
        // Create a test message
        $testMessage = "This is a test encrypted message!";
        
        $chat = Chat::create([
            'sender_id' => $sender->user_id,
            'receiver_id' => $receiver->user_id,
            'message' => $testMessage,
        ]);
        
        echo "\nCreated message with ID: {$chat->chat_id}\n";
        echo "Original message: {$testMessage}\n";
        echo "Is encrypted in DB: " . ($chat->is_encrypted ? 'Yes' : 'No') . "\n";
        echo "Retrieved message: {$chat->message}\n";
        
        // Check raw database value
        $rawData = \DB::table('chats')->where('chat_id', $chat->chat_id)->first();
        echo "Raw DB message: {$rawData->message}\n";
        echo "Raw is_encrypted flag: {$rawData->is_encrypted}\n";
    } else {
        echo "Not enough users found for testing.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
