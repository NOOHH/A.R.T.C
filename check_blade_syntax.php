<?php
$file = 'resources/views/registration/Modular_enrollment.blade.php';
$content = file_get_contents($file);

$lines = explode("\n", $content);
$ifStack = [];
$errors = [];

foreach($lines as $lineNum => $line) {
    $lineNumber = $lineNum + 1;
    
    // Check for @if statements
    if (preg_match('/@if\s*\(/', $line)) {
        $ifStack[] = $lineNumber;
    }
    
    // Check for @endif statements
    if (preg_match('/@endif/', $line)) {
        if (empty($ifStack)) {
            $errors[] = "Line $lineNumber: @endif without matching @if";
        } else {
            array_pop($ifStack);
        }
    }
}

// Check for unmatched @if statements
if (!empty($ifStack)) {
    foreach ($ifStack as $ifLine) {
        $errors[] = "Line $ifLine: @if without matching @endif";
    }
}

if (empty($errors)) {
    echo "No @if/@endif mismatches found.\n";
} else {
    echo "Found issues:\n";
    foreach ($errors as $error) {
        echo "$error\n";
    }
}

echo "\nTotal @if statements: " . substr_count($content, '@if') . "\n";
echo "Total @endif statements: " . substr_count($content, '@endif') . "\n";
?>
