<?php

// Test StudentDashboardController getContent functionality
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StudentDashboardController;

echo "Testing StudentDashboardController getContent...\n\n";

try {
    // First, let's see what content items exist
    $content = DB::table('content_items')->select('id', 'content_title', 'attachment_path')->limit(3)->get();
    
    if ($content->count() > 0) {
        echo "Available content items:\n";
        foreach ($content as $item) {
            echo "   - ID: {$item->id}, Title: {$item->content_title}, Path: {$item->attachment_path}\n";
        }
        
        $controller = new StudentDashboardController();
        $testContentId = $content->first()->id;
        
        echo "\n2. Testing getContent for content ID {$testContentId}:\n";
        $response = $controller->getContent($testContentId);
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            echo "   ✓ Content retrieved successfully\n";
            echo "   - Title: " . $data['content']['content_title'] . "\n";
            echo "   - Type: " . $data['content']['content_type'] . "\n";
            echo "   - Attachment: " . ($data['content']['attachment_path'] ?? 'None') . "\n";
        } else {
            echo "   ❌ Error: " . $data['message'] . "\n";
        }
    } else {
        echo "No content items found in database.\n";
    }
    
    echo "\n✅ Content test completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
