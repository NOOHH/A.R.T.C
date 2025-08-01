<?php
/**
 * More detailed script to fix all instances of $index + 1 in take.blade.php
 */

$filePath = __DIR__ . '/resources/views/student/quiz/take.blade.php';
$content = file_get_contents($filePath);

echo "Original content length: " . strlen($content) . "\n";

// Find the line number of "onchange="markAnswered({{ $index + 1 }})">"
$lines = file($filePath);
foreach ($lines as $lineNum => $line) {
    if (strpos($line, 'onchange="markAnswered({{ $index + 1 }})">') !== false) {
        echo "Found target pattern at line " . ($lineNum + 1) . "\n";
    }
}

// Directly fix the issue by loading the file as lines, modifying specific lines
$lines = file($filePath);
$fixed = 0;

foreach ($lines as $lineNum => &$line) {
    $originalLine = $line;
    
    // Replace all instances of {{ $index + 1 }} with {{ (int)$index + 1 }}
    $pattern = '/\{\{ \$index \+ 1 \}\}/';
    $replacement = '{{ (int)$index + 1 }}';
    $newLine = preg_replace($pattern, $replacement, $line);
    
    // Also replace comparison operators
    $newLine = str_replace('{{ $index === 0', '{{ (int)$index === 0', $newLine);
    $newLine = str_replace('{{ $index > 0', '{{ (int)$index > 0', $newLine);
    
    if ($newLine !== $originalLine) {
        $line = $newLine;
        $fixed++;
        echo "Fixed line " . ($lineNum + 1) . "\n";
    }
}

// Write the modified lines back to the file
file_put_contents($filePath, implode('', $lines));

echo "Fixed $fixed lines in take.blade.php\n";
?>
