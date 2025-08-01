<?php
/**
 * Final attempt to fix the issues using direct string replacements
 */

$filePath = __DIR__ . '/resources/views/student/quiz/take.blade.php';
$content = file_get_contents($filePath);

// Print the original content for line 535 and surrounding lines
$lines = file($filePath);
echo "Original content around line 535:\n";
for ($i = 530; $i < 540; $i++) {
    if (isset($lines[$i])) {
        echo "Line " . ($i + 1) . ": " . $lines[$i];
    }
}

// Explicit replacements for each use case
$searchReplace = [
    'onchange="markAnswered({{ $index + 1 }})">' => 'onchange="markAnswered({{ (int)$index + 1 }})">',
    'id="question-{{ $index + 1 }}"' => 'id="question-{{ (int)$index + 1 }}"',
    'style="{{ $index > 0' => 'style="{{ (int)$index > 0',
    '<div class="question-number">{{ $index + 1 }}</div>' => '<div class="question-number">{{ (int)$index + 1 }}</div>',
    'id="qBtn-{{ $index + 1 }}"' => 'id="qBtn-{{ (int)$index + 1 }}"',
    'onclick="goToQuestion({{ $index + 1 }})">' => 'onclick="goToQuestion({{ (int)$index + 1 }})">',
    '{{ $index + 1 }}</button>' => '{{ (int)$index + 1 }}</button>',
    'class="question-btn {{ $index === 0' => 'class="question-btn {{ (int)$index === 0'
];

// Apply all replacements
$newContent = $content;
$replacementCount = 0;

foreach ($searchReplace as $search => $replace) {
    $count = 0;
    $tempContent = str_replace($search, $replace, $newContent, $count);
    
    if ($count > 0) {
        $newContent = $tempContent;
        $replacementCount += $count;
        echo "Replaced '$search' with '$replace' $count times.\n";
    }
}

// Write the modified content back to the file
if ($replacementCount > 0) {
    file_put_contents($filePath, $newContent);
    echo "Total replacements: $replacementCount\n";
} else {
    echo "No replacements made.\n";
}

// Verify the changes
echo "\nVerifying changes for line 535:\n";
clearstatcache();
$updatedLines = file($filePath);
for ($i = 530; $i < 540; $i++) {
    if (isset($updatedLines[$i])) {
        echo "Line " . ($i + 1) . ": " . $updatedLines[$i];
    }
}
?>
