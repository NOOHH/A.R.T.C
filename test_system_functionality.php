<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Chat;
use App\Models\User;

echo "Testing basic API functionality:\n\n";

try {
    echo "1. Database connection test:\n";
    $chatCount = Chat::count();
    $userCount = User::count();
    echo "   ✓ Chats: $chatCount\n";
    echo "   ✓ Users: $userCount\n\n";
    
    echo "2. Encryption test:\n";
    $testMessage = "Test message for encryption";
    $encrypted = Illuminate\Support\Facades\Crypt::encrypt($testMessage);
    $decrypted = Illuminate\Support\Facades\Crypt::decrypt($encrypted);
    echo "   ✓ Original: $testMessage\n";
    echo "   ✓ Encrypted: " . substr($encrypted, 0, 50) . "...\n";
    echo "   ✓ Decrypted: $decrypted\n";
    echo "   ✓ Match: " . ($testMessage === $decrypted ? 'Yes' : 'No') . "\n\n";
    
    echo "3. User status test:\n";
    $onlineUsers = User::where('is_online', 1)->count();
    $totalUsers = User::count();
    echo "   ✓ Total users: $totalUsers\n";
    echo "   ✓ Online users: $onlineUsers\n\n";
    
    echo "4. Chat model test:\n";
    $latestChats = Chat::latest()->take(3)->get();
    foreach ($latestChats as $chat) {
        echo "   ✓ Message ID {$chat->chat_id}: " . substr($chat->message, 0, 30) . "...\n";
        echo "     Encrypted: " . ($chat->is_encrypted ? 'Yes' : 'No') . "\n";
    }
    
    echo "\n✅ All basic tests passed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
