<?php

$file = 'app/Http/Controllers/AdminModuleController.php';
$content = file_get_contents($file);

// Fix the hasFile() calls without parameters
$content = str_replace(
    'request->hasFile()',
    'count($request->files->all()) > 0',
    $content
);

// Fix the debug array key
$content = str_replace(
    '"request_has_files" => $request->hasFile()',
    '"request_has_files" => count($request->files->all()) > 0',
    $content
);

file_put_contents($file, $content);

echo "✅ Fixed all hasFile() calls without parameters\n";

?>
