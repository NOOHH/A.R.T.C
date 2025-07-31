<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== DIRECTORS TABLE STRUCTURE ===\n";
    $directorColumns = DB::select('DESCRIBE directors');
    foreach ($directorColumns as $column) {
        echo "{$column->Field} - {$column->Type}\n";
    }
    
    echo "\n=== ANNOUNCEMENTS TABLE STRUCTURE ===\n";
    $announcementColumns = DB::select('DESCRIBE announcements');
    foreach ($announcementColumns as $column) {
        echo "{$column->Field} - {$column->Type}\n";
    }
    
    echo "\n=== DIRECTORS DATA (SAMPLE) ===\n";
    $directors = DB::select('SELECT directors_id, directors_name, directors_email FROM directors LIMIT 5');
    foreach ($directors as $director) {
        echo "ID: {$director->directors_id}, Name: {$director->directors_name}, Email: {$director->directors_email}\n";
    }
    
    echo "\n=== ANNOUNCEMENT FIELDS THAT STORE CREATOR INFO ===\n";
    $sampleAnnouncement = DB::select('SELECT * FROM announcements LIMIT 1');
    if (!empty($sampleAnnouncement)) {
        $fields = array_keys((array)$sampleAnnouncement[0]);
        foreach ($fields as $field) {
            if (strpos($field, 'admin') !== false || strpos($field, 'professor') !== false || strpos($field, 'director') !== false || strpos($field, 'created') !== false) {
                echo "Creator-related field: {$field}\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
