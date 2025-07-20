<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

// Test route for file upload debugging
Route::post('/test-file-upload', function (Request $request) {
    Log::info('=== TEST FILE UPLOAD ===');
    Log::info('Method: ' . $request->method());
    Log::info('Content Type: ' . $request->header('Content-Type'));
    Log::info('Has files: ' . ($request->hasFile() ? 'YES' : 'NO'));
    Log::info('Files count: ' . count($request->files->all()));
    Log::info('Has attachment: ' . ($request->hasFile('attachment') ? 'YES' : 'NO'));
    
    // Log all form data except file
    Log::info('Form data: ', $request->except(['attachment', '_token']));
    
    // Check all files
    foreach ($request->files->all() as $key => $file) {
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            Log::info("File {$key}: " . $file->getClientOriginalName() . " (" . $file->getSize() . " bytes)");
        }
    }
    
    // Try to get the attachment file
    $file = $request->file('attachment');
    if ($file) {
        Log::info('Attachment details: ', [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'error' => $file->getError(),
            'valid' => $file->isValid()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'File received successfully',
            'file_info' => [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ]
        ]);
    } else {
        Log::info('No attachment file found');
        return response()->json([
            'success' => false,
            'message' => 'No file received'
        ]);
    }
});

?>
