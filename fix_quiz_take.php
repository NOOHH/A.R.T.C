<?php
/**
 * Script to fix all instances of $index + 1 in take.blade.php
 */

$filePath = __DIR__ . '/resources/views/student/quiz/take.blade.php';
$content = file_get_contents($filePath);

// Replace all instances of $index + 1 with (int)$index + 1
$pattern = '/\$index \+ 1/';
$replacement = '(int)$index + 1';
$newContent = preg_replace($pattern, $replacement, $content);

// Also fix instances of $index === 0 or $index > 0
$newContent = str_replace('$index === 0', '(int)$index === 0', $newContent);
$newContent = str_replace('$index > 0', '(int)$index > 0', $newContent);

// Write the updated content back to the file
file_put_contents($filePath, $newContent);

echo "Fixed all instances of \$index + 1 and \$index comparisons in take.blade.php\n";
?>
