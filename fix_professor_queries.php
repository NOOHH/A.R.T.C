<?php
// Script to fix all remaining Professor queries in AnnouncementController

$file = 'app/Http/Controllers/Professor/AnnouncementController.php';
$content = file_get_contents($file);

// Pattern to find: $professor = Professor::where('professor_id', session('professor_id'))->first();
$pattern = '/\$professor = Professor::where\(\'professor_id\', session\(\'professor_id\'\)\)->first\(\);/';

$replacement = '// Get professor safely with preview mode handling
        try {
            $professor = $this->getProfessorSafely();
        } catch (\Exception $e) {
            return redirect()->route(\'professor.dashboard\')->with(\'error\', $e->getMessage());
        }';

// Count how many matches we'll replace
$count = preg_match_all($pattern, $content);
echo "Found $count instances of direct Professor queries to replace\n";

// Perform the replacement
$newContent = preg_replace($pattern, $replacement, $content);

if ($newContent !== $content) {
    file_put_contents($file, $newContent);
    echo "✅ Successfully updated $file\n";
    echo "Replaced all remaining direct Professor queries with safe getProfessorSafely() calls\n";
} else {
    echo "No changes needed - all queries already updated\n";
}

// Verify the changes
$finalContent = file_get_contents($file);
$remainingCount = preg_match_all($pattern, $finalContent);
echo "Remaining direct Professor queries: $remainingCount\n";

if ($remainingCount === 0) {
    echo "✅ All Professor queries successfully updated!\n";
} else {
    echo "❌ Still have $remainingCount direct queries that need manual fixing\n";
}
?>
