<?php
$file = 'resources/views/registration/Modular_enrollment.blade.php';
$content = file_get_contents($file);

$lines = explode("\n", $content);

echo "Looking for unclosed multi-line @section directives...\n";

$multiLineSections = [];
$endsections = [];

foreach ($lines as $lineNum => $line) {
    $lineNumber = $lineNum + 1;
    
    // Check for multi-line @section (those that don't have a value on the same line)
    if (preg_match('/@section\s*\(\s*[\'"][^\'"]*[\'"]\s*\)\s*$/', $line)) {
        $multiLineSections[] = $lineNumber;
        echo "Found multi-line @section at line $lineNumber: " . trim($line) . "\n";
    }
    
    // Check for single-line @section (those that have a value)
    if (preg_match('/@section\s*\(\s*[\'"][^\'"]*[\'"]\s*,/', $line)) {
        echo "Found single-line @section at line $lineNumber: " . trim($line) . "\n";
    }
    
    // Check for @endsection
    if (preg_match('/@endsection/', $line)) {
        $endsections[] = $lineNumber;
        echo "Found @endsection at line $lineNumber\n";
    }
}

echo "\nSummary:\n";
echo "Multi-line @sections: " . count($multiLineSections) . "\n";
echo "@endsections: " . count($endsections) . "\n";

if (count($multiLineSections) !== count($endsections)) {
    echo "MISMATCH! Some multi-line @section directives are not closed.\n";
    
    if (count($multiLineSections) > count($endsections)) {
        echo "Missing @endsection for @section at lines: ";
        // This is a simple check - in a complex template you'd need more sophisticated tracking
        $unclosed = array_slice($multiLineSections, count($endsections));
        echo implode(', ', $unclosed) . "\n";
    }
}
?>
