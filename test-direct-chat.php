<?php

// Direct test of database tables and chat functionality
require_once './vendor/autoload.php';

// Bootstrap Laravel
$app = require_once './bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Direct Database and Chat Test ===\n\n";

// Test database connection
echo "1. Testing database connection...\n";
try {
    $pdo = DB::connection()->getPdo();
    echo "Database connected successfully\n\n";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n\n";
    exit;
}

// Test Chat model
echo "2. Testing Chat model...\n";
try {
    $chatCount = App\Models\Chat::count();
    echo "Total chats in database: $chatCount\n";
    
    $recentChats = App\Models\Chat::orderBy('created_at', 'desc')->limit(5)->get();
    echo "Recent chats:\n";
    foreach ($recentChats as $chat) {
        echo "  - ID: {$chat->chat_id}, From: {$chat->sender_id}, To: {$chat->receiver_id}, Message: " . substr($chat->message, 0, 50) . "...\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Chat model test failed: " . $e->getMessage() . "\n\n";
}

// Test Admin model
echo "3. Testing Admin model...\n";
try {
    $adminCount = App\Models\Admin::count();
    echo "Total admins in database: $adminCount\n";
    
    $admins = App\Models\Admin::limit(3)->get();
    echo "Admins:\n";
    foreach ($admins as $admin) {
        echo "  - ID: {$admin->admin_id}, Name: {$admin->admin_name}, Email: {$admin->email}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Admin model test failed: " . $e->getMessage() . "\n\n";
}

// Test Professor model
echo "4. Testing Professor model...\n";
try {
    $professorCount = App\Models\Professor::count();
    echo "Total professors in database: $professorCount\n";
    
    $professors = App\Models\Professor::limit(3)->get();
    echo "Professors:\n";
    foreach ($professors as $professor) {
        echo "  - ID: {$professor->professor_id}, Name: {$professor->professor_name}, Email: {$professor->email}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Professor model test failed: " . $e->getMessage() . "\n\n";
}

// Test creating a chat message
echo "5. Testing chat message creation...\n";
try {
    $chat = App\Models\Chat::create([
        'sender_id' => 1,
        'receiver_id' => 8,
        'message' => 'Test message from direct test - ' . date('Y-m-d H:i:s'),
        'sent_at' => now()
    ]);
    
    echo "Chat message created successfully with ID: {$chat->chat_id}\n";
} catch (Exception $e) {
    echo "Chat message creation failed: " . $e->getMessage() . "\n";
}

// Test retrieving messages between users
echo "\n6. Testing message retrieval between users 1 and 8...\n";
try {
    $messages = App\Models\Chat::where(function ($query) {
        $query->where('sender_id', 1)->where('receiver_id', 8);
    })->orWhere(function ($query) {
        $query->where('sender_id', 8)->where('receiver_id', 1);
    })->orderBy('created_at', 'asc')->get();
    
    echo "Found " . $messages->count() . " messages between users 1 and 8:\n";
    foreach ($messages->take(5) as $message) {
        echo "  - From {$message->sender_id} to {$message->receiver_id}: " . substr($message->message, 0, 50) . "...\n";
    }
} catch (Exception $e) {
    echo "Message retrieval failed: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
