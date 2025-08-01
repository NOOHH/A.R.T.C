<?php
/**
 * Final direct line fix for take.blade.php
 */

$filePath = __DIR__ . '/resources/views/student/quiz/take.blade.php';
$lines = file($filePath);

// Specific lines where we need to replace $index + 1 with (int)$index + 1
$lineFixMap = [
    516 => true, // Line 517
    517 => true, // Line 518
    536 => true, // Line 537
    549 => true, // Line 550
    559 => true, // Line 560
    600 => true, // Line 601
    601 => true, // Line 602
    602 => true  // Line 603
];

$fixed = 0;
foreach ($lineFixMap as $lineIndex => $shouldFix) {
    if (isset($lines[$lineIndex]) && $shouldFix) {
        $originalLine = $lines[$lineIndex];
        
        // Check for literal string "$index + 1"
        if (strpos($originalLine, '$index + 1') !== false) {
            $lines[$lineIndex] = str_replace('$index + 1', '(int)$index + 1', $originalLine);
            $fixed++;
            echo "Fixed line " . ($lineIndex + 1) . "\n";
        }
    }
}

file_put_contents($filePath, implode('', $lines));
echo "Fixed a total of $fixed lines\n";

// Confirm the changes took effect
echo "\nConfirming line 537 (zero-indexed 536):\n";
$updatedLines = file($filePath);
if (isset($updatedLines[536])) {
    echo trim($updatedLines[536]) . "\n";
}
?>
