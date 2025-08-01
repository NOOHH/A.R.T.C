<?php
/**
 * Updated debug script with more precise pattern matching
 */

$filePath = __DIR__ . '/resources/views/student/quiz/take.blade.php';
$content = file_get_contents($filePath);

// Check specific lines to see their exact content
$lines = file($filePath);
$linesToCheck = [516, 517, 536, 549, 559, 600, 601, 602];

echo "Checking specific lines:\n";
foreach ($linesToCheck as $lineIndex) {
    if (isset($lines[$lineIndex])) {
        $line = trim($lines[$lineIndex]);
        $lineNumber = $lineIndex + 1;
        
        echo "Line $lineNumber: $line\n";
        
        // Check if properly cast
        if (strpos($line, '(int)$index + 1') !== false) {
            echo "  ✓ Properly cast\n";
        } else if (strpos($line, '$index + 1') !== false) {
            echo "  ✗ Not properly cast\n";
        }
    }
}

echo "\nAll checks complete.\n";
?>
