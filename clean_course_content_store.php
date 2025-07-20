<?php

// Clean courseContentStore method for AdminModuleController
// This will replace the entire method to fix all the debug mess

public function courseContentStore(Request $request)
{
    // Clean debugging
    \Log::info('=== COURSE CONTENT STORE START ===');
    \Log::info('Request data:', [
        'method' => $request->method(),
        'content_type' => $request->header('Content-Type'),
        'has_files' => $request->hasFile(),
        'files_count' => count($request->files->all()),
        'has_attachment' => $request->hasFile('attachment'),
        'form_data' => $request->except(['attachment', '_token'])
    ]);
    
    // List all files in request
    foreach ($request->files->all() as $key => $file) {
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            \Log::info("File detected - Key: {$key}, Name: " . $file->getClientOriginalName());
        }
    }

    try {
        // Validate request
        $request->validate([
            'program_id' => 'required|integer',
            'module_id' => 'required|integer', 
            'course_id' => 'required|integer',
            'content_type' => 'required|string',
            'content_title' => 'required|string|max:255',
            'content_description' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240'
        ]);

        $attachmentPath = null;
        
        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            \Log::info('Processing file upload:', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'is_valid' => $file->isValid(),
                'error_code' => $file->getError()
            ]);
            
            if ($file->isValid()) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '.' . $extension;
                
                $filePath = $file->storeAs('content', $filename, 'public');
                $attachmentPath = 'storage/' . $filePath;
                
                \Log::info('File stored successfully:', [
                    'original_name' => $originalName,
                    'stored_as' => $filename,
                    'path' => $attachmentPath
                ]);
            } else {
                \Log::error('File validation failed:', [
                    'error_code' => $file->getError(),
                    'max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size')
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'File upload failed. Error code: ' . $file->getError()
                ], 422);
            }
        } else {
            \Log::warning('No file attachment found in request');
        }

        // Create content item
        $contentItem = new ContentItem();
        $contentItem->program_id = $request->program_id;
        $contentItem->module_id = $request->module_id;
        $contentItem->course_id = $request->course_id;
        $contentItem->content_type = $request->content_type;
        $contentItem->content_title = $request->content_title;
        $contentItem->content_description = $request->content_description;
        $contentItem->attachment_path = $attachmentPath;
        $contentItem->save();

        \Log::info('Content item created successfully:', [
            'id' => $contentItem->id,
            'attachment_path' => $contentItem->attachment_path
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Content created successfully',
            'data' => [
                'id' => $contentItem->id,
                'content_title' => $contentItem->content_title,
                'attachment_path' => $contentItem->attachment_path
            ]
        ]);

    } catch (ValidationException $e) {
        \Log::error('Validation failed:', [
            'errors' => $e->errors(),
            'request_data' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        \Log::error('Content creation failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while creating content: ' . $e->getMessage()
        ], 500);
    }
}

?>
