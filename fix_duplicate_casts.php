<?php
/**
 * Fix any duplicate type casts and ensure all $index operations are properly cast
 */

$filePath = __DIR__ . '/resources/views/student/quiz/take.blade.php';
$content = file_get_contents($filePath);

// First, fix any duplicate (int)(int) instances
$content = str_replace('(int)(int)', '(int)', $content);

// Then create a modified file to manually review
$reviewFile = __DIR__ . '/take.blade.fixed.php';
file_put_contents($reviewFile, $content);

// Write back to the original file
file_put_contents($filePath, $content);

echo "Removed duplicate type casts.\n";
echo "Created review file at $reviewFile\n";

// Check the immediate issue with $index + 1 on line 535
$lines = file($filePath);
$lineOfInterest = 534; // 0-indexed for line 535

if (isset($lines[$lineOfInterest])) {
    $originalLine = $lines[$lineOfInterest];
    echo "Line 535: $originalLine";
    
    if (strpos($originalLine, '$index + 1') !== false) {
        $lines[$lineOfInterest] = str_replace('$index + 1', '(int)$index + 1', $originalLine);
        file_put_contents($filePath, implode('', $lines));
        echo "Fixed line 535\n";
    } else {
        echo "No issue found on line 535\n";
    }
}

// Find line with $optionIndex and onchange
echo "\nSearching for the line with the specific error issue...\n";
foreach ($lines as $lineNum => $line) {
    if (strpos($line, 'onchange="markAnswered({{') !== false) {
        echo "Found onchange at line " . ($lineNum + 1) . ":\n";
        echo trim($line) . "\n";
    }
}
?>
