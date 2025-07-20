<?php

// Let's modify the AdminModuleController to add more debugging
// First, let's check the current courseContentStore method

$controllerPath = 'app/Http/Controllers/AdminModuleController.php';
$content = file_get_contents($controllerPath);

// Find the line where we check hasFile('attachment')
$pattern = '/if \(\$request->hasFile\(\'attachment\'\)\) \{/';

if (preg_match($pattern, $content)) {
    echo "Found hasFile check in controller\n";
    
    // Let's add more debugging before this check
    $debugCode = '
        // ENHANCED FILE UPLOAD DEBUGGING
        \Log::info("=== FILE UPLOAD DEBUG START ===");
        \Log::info("Request method: " . $request->method());
        \Log::info("Request content type: " . $request->header("Content-Type"));
        \Log::info("Request has files: " . ($request->hasFile() ? "YES" : "NO"));
        \Log::info("Request files count: " . count($request->files->all()));
        \Log::info("Request attachment check: " . ($request->hasFile("attachment") ? "YES" : "NO"));
        
        // Check all files in request
        foreach ($request->files->all() as $key => $file) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                \Log::info("File found - Key: {$key}, Name: " . $file->getClientOriginalName() . ", Size: " . $file->getSize());
            } else {
                \Log::info("Non-file found - Key: {$key}, Type: " . gettype($file));
            }
        }
        
        // Check specific attachment
        $attachmentFile = $request->file("attachment");
        if ($attachmentFile) {
            \Log::info("Attachment file details: " . json_encode([
                "name" => $attachmentFile->getClientOriginalName(),
                "size" => $attachmentFile->getSize(),
                "mime" => $attachmentFile->getMimeType(),
                "error" => $attachmentFile->getError(),
                "is_valid" => $attachmentFile->isValid(),
                "tmp_name" => $attachmentFile->getRealPath()
            ]));
        } else {
            \Log::info("No attachment file found");
        }
        \Log::info("=== FILE UPLOAD DEBUG END ===");
        ';
    
    // Insert the debug code before the hasFile check
    $replacement = $debugCode . '
        if ($request->hasFile(\'attachment\')) {';
    
    $newContent = preg_replace($pattern, $replacement, $content);
    
    // Write back to file
    file_put_contents($controllerPath, $newContent);
    
    echo "✅ Added enhanced debugging to AdminModuleController\n";
    echo "Now try uploading a file and check the logs\n";
} else {
    echo "❌ Could not find hasFile check in controller\n";
}

?>
