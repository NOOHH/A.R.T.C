<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== Testing Director Announcement Creation Fix ===\n\n";
    
    // Test 1: Check directors table structure
    echo "1. Directors table structure:\n";
    $directorColumns = DB::select('DESCRIBE directors');
    foreach ($directorColumns as $column) {
        echo "   {$column->Field} - {$column->Type}\n";
    }
    
    // Test 2: Check announcements table structure  
    echo "\n2. Announcements table structure:\n";
    $announcementColumns = DB::select('DESCRIBE announcements');
    foreach ($announcementColumns as $column) {
        echo "   {$column->Field} - {$column->Type}\n";
    }
    
    // Test 3: Check directors data
    echo "\n3. Directors in database:\n";
    $directors = DB::select('SELECT directors_id, directors_name, directors_email, admin_id FROM directors LIMIT 5');
    foreach ($directors as $director) {
        echo "   ID: {$director->directors_id}, Name: {$director->directors_name}, Email: {$director->directors_email}, Admin ID: {$director->admin_id}\n";
    }
    
    // Test 4: Check recent announcements and their creators
    echo "\n4. Recent announcements and their creators:\n";
    $announcements = DB::select('SELECT announcement_id, title, admin_id, professor_id, created_at FROM announcements ORDER BY created_at DESC LIMIT 5');
    foreach ($announcements as $announcement) {
        echo "   Announcement ID: {$announcement->announcement_id}\n";
        echo "   Title: {$announcement->title}\n";
        echo "   Admin ID: {$announcement->admin_id}\n";
        echo "   Professor ID: {$announcement->professor_id}\n";
        echo "   Created: {$announcement->created_at}\n";
        
        // Check if this admin_id exists in admins table
        $admin = DB::select('SELECT admin_name FROM admins WHERE admin_id = ?', [$announcement->admin_id]);
        if (!empty($admin)) {
            echo "   Creator (Admin): {$admin[0]->admin_name}\n";
        } else {
            // Check if this admin_id might be a director
            $director = DB::select('SELECT directors_name FROM directors WHERE directors_id = ?', [$announcement->admin_id]);
            if (!empty($director)) {
                echo "   Creator (Director): {$director[0]->directors_name}\n";
            } else {
                echo "   Creator: Unknown (admin_id {$announcement->admin_id} not found in admins or directors)\n";
            }
        }
        echo "\n";
    }
    
    // Test 5: Test the Announcement model's getCreatorName method
    echo "5. Testing Announcement model getCreatorName method:\n";
    $recentAnnouncement = \App\Models\Announcement::orderBy('created_at', 'desc')->first();
    if ($recentAnnouncement) {
        echo "   Recent announcement: {$recentAnnouncement->title}\n";
        echo "   Creator name via model: {$recentAnnouncement->getCreatorName()}\n";
    } else {
        echo "   No announcements found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
