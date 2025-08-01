<?php
/**
 * Debug script to validate the take.blade.php file
 */

$filePath = __DIR__ . '/resources/views/student/quiz/take.blade.php';
$content = file_get_contents($filePath);

// Check for any remaining instances of $index + 1 without type casting
$pattern = '/\$index \+ 1/';
preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

if (!empty($matches[0])) {
    echo "Found instances of \$index + 1 without type casting:\n";
    foreach ($matches[0] as $match) {
        $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
        echo "Line $line: {$match[0]}\n";
    }
} else {
    echo "No instances of \$index + 1 without type casting found.\n";
}

echo "\nChecking for proper type casting on all instances...\n";
$pattern = '/\(int\)\$index \+ 1/';
preg_match_all($pattern, $content, $matches);
echo "Found " . count($matches[0]) . " properly cast instances.\n";

echo "\nAnalysis complete.\n";
?>
