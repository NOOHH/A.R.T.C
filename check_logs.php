<?php
echo "=== CHECKING RECENT LARAVEL LOGS ===\n";

$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    $lines = explode(PHP_EOL, $content);
    
    echo "Total log lines: " . count($lines) . "\n";
    echo "Checking last 100 lines for file upload related entries...\n\n";
    
    $relevantLines = [];
    for ($i = max(0, count($lines) - 100); $i < count($lines); $i++) {
        $line = $lines[$i] ?? '';
        if (strpos($line, 'Laravel storeAs') !== false || 
            strpos($line, 'Manual file copy') !== false ||
            strpos($line, 'move_uploaded_file') !== false ||
            strpos($line, 'AFTER_ALL_METHODS') !== false ||
            strpos($line, 'attachment_path') !== false ||
            strpos($line, 'DETAILED FILE STORAGE') !== false ||
            strpos($line, 'FINAL DATA BEFORE DB') !== false) {
            $relevantLines[] = $line;
        }
    }
    
    if (empty($relevantLines)) {
        echo "No relevant file upload logs found in recent entries.\n";
        echo "Last 10 log lines:\n";
        for ($i = max(0, count($lines) - 10); $i < count($lines); $i++) {
            echo ($i + 1) . ": " . ($lines[$i] ?? '') . "\n";
        }
    } else {
        echo "Found " . count($relevantLines) . " relevant log entries:\n";
        foreach ($relevantLines as $index => $line) {
            echo ($index + 1) . ": " . $line . "\n";
        }
    }
    
} else {
    echo "❌ Laravel log file not found at: $logFile\n";
}

echo "\n=== LOG CHECK COMPLETE ===\n";
?>
