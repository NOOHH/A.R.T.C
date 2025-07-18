<?php
$file = 'resources/views/registration/Modular_enrollment.blade.php';
$content = file_get_contents($file);

$lines = explode("\n", $content);

// Check for all directive pairs
$directives = [
    ['@if', '@endif'],
    ['@foreach', '@endforeach'],
    ['@for', '@endfor'],
    ['@while', '@endwhile'],
    ['@section', '@endsection'],
    ['@push', '@endpush'],
    ['@php', '@endphp']
];

foreach ($directives as $pair) {
    $opening = $pair[0];
    $closing = $pair[1];
    
    $openCount = 0;
    $closeCount = 0;
    
    foreach ($lines as $lineNum => $line) {
        // Count opening directives
        $openCount += preg_match_all('/\\' . $opening . '(?!\w)/', $line);
        
        // Count closing directives
        $closeCount += preg_match_all('/\\' . $closing . '(?!\w)/', $line);
    }
    
    echo "$opening: $openCount, $closing: $closeCount";
    if ($openCount !== $closeCount) {
        echo " [MISMATCH!]";
    }
    echo "\n";
}

// Also check for any PHP syntax issues with specific search
echo "\nChecking for specific PHP syntax patterns...\n";

// Look for lines that might have PHP syntax issues
foreach ($lines as $lineNum => $line) {
    $lineNumber = $lineNum + 1;
    
    // Check for potential PHP syntax issues
    if (strpos($line, '<?') !== false && strpos($line, '?>') === false) {
        echo "Line $lineNumber: Potential unclosed PHP tag: $line\n";
    }
    
    // Check for unmatched brackets in @if statements
    if (preg_match('/@if\s*\(/', $line)) {
        $openParens = substr_count($line, '(');
        $closeParens = substr_count($line, ')');
        if ($openParens !== $closeParens) {
            echo "Line $lineNumber: Unmatched parentheses in @if: $line\n";
        }
    }
}
?>
