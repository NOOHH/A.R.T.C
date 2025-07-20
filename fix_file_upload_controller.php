<?php

// Fix for the file upload issue in AdminModuleController
// Let's modify the courseContentStore method to be more robust

$controllerPath = 'app/Http/Controllers/AdminModuleController.php';
$content = file_get_contents($controllerPath);

// Find and replace the file upload logic
$oldPattern = '/\$attachmentPath = null;\s*\n\s*\/\/ Handle file upload if present\s*\n\s*if \(\$request->hasFile\(\'attachment\'\)\) \{/';

$newFileUploadLogic = '$attachmentPath = null;

        // ENHANCED FILE UPLOAD HANDLING
        \Log::info("=== ENHANCED FILE UPLOAD START ===");
        \Log::info("Request has files: " . ($request->hasFile() ? "YES" : "NO"));
        \Log::info("Request hasFile(attachment): " . ($request->hasFile(\'attachment\') ? "YES" : "NO"));
        
        // Try multiple approaches to get the file
        $attachmentFile = null;
        
        // Method 1: Standard hasFile check
        if ($request->hasFile(\'attachment\')) {
            $attachmentFile = $request->file(\'attachment\');
            \Log::info("File found via hasFile method");
        }
        
        // Method 2: Direct file access
        if (!$attachmentFile && $request->files->has(\'attachment\')) {
            $attachmentFile = $request->files->get(\'attachment\');
            \Log::info("File found via files->get method");
        }
        
        // Method 3: Check all files
        if (!$attachmentFile) {
            foreach ($request->files->all() as $key => $file) {
                if ($key === \'attachment\' && $file instanceof \Illuminate\Http\UploadedFile) {
                    $attachmentFile = $file;
                    \Log::info("File found via all files iteration");
                    break;
                }
            }
        }
        
        if ($attachmentFile && $attachmentFile instanceof \Illuminate\Http\UploadedFile) {';

if (preg_match($oldPattern, $content)) {
    $newContent = preg_replace($oldPattern, $newFileUploadLogic, $content);
    file_put_contents($controllerPath, $newContent);
    echo "✅ Enhanced file upload logic added to controller\n";
} else {
    echo "❌ Could not find the file upload section to replace\n";
    
    // Let's try a simpler approach - just add logging before the hasFile check
    $simplePattern = '/if \(\$request->hasFile\(\'attachment\'\)\) \{/';
    if (preg_match($simplePattern, $content)) {
        $debugCode = '
        // ADDITIONAL FILE UPLOAD DEBUG
        \Log::info("Pre-hasFile debug:", [
            "request_has_files" => $request->hasFile(),
            "request_files_count" => count($request->files->all()),
            "attachment_exists" => $request->files->has("attachment"),
            "all_files" => array_keys($request->files->all())
        ]);
        
        if ($request->hasFile(\'attachment\')) {';
        
        $newContent = preg_replace($simplePattern, $debugCode, $content);
        file_put_contents($controllerPath, $newContent);
        echo "✅ Added additional debugging to controller\n";
    } else {
        echo "❌ Could not find hasFile check either\n";
    }
}

?>
