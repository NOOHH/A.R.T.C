<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Program;
use App\Models\Course;
use App\Models\ContentItem;
use App\Models\StudentBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

// CLEAN VERSION OF courseContentStore METHOD
public function courseContentStore(Request $request)
{
    try {
        // Log the incoming request for debugging
        Log::info('Course content store request received', [
            'method' => $request->method(),
            'has_attachment' => $request->hasFile('attachment'),
            'content_type' => $request->get('content_type'),
            'course_id' => $request->get('course_id'),
            'module_id' => $request->get('module_id'),
            'program_id' => $request->get('program_id'),
            'content_title' => $request->get('content_title'),
        ]);

        // Validate required fields
        $requiredFields = ['program_id', 'module_id', 'course_id', 'content_type', 'content_title'];
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!$request->has($field) || $request->get($field) === null || $request->get($field) === '') {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            Log::warning('Missing required fields', ['missing_fields' => $missingFields]);
            return response()->json([
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $missingFields),
                'errors' => ['missing_fields' => $missingFields]
            ], 422);
        }

        // Set up validation rules
        $validationRules = [
            'program_id' => 'required|exists:programs,program_id',
            'module_id' => 'required|exists:modules,modules_id',
            'course_id' => 'required|exists:courses,subject_id',
            'content_type' => 'required|in:lesson,quiz,test,assignment,pdf,link,video,document',
            'content_title' => 'required|string|max:255',
            'content_description' => 'nullable|string',
            'enable_submission' => 'nullable|boolean',
            'allowed_file_types' => 'nullable|string',
            'max_file_size' => 'nullable|integer|min:1|max:100',
            'submission_instructions' => 'nullable|string',
            'content_url' => 'nullable|url'
        ];
        
        if ($request->hasFile('attachment')) {
            $validationRules['attachment'] = 'file|max:51200'; // 50MB max
        }
        
        $request->validate($validationRules);

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            
            Log::info('Processing file upload', [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_error' => $file->getError(),
                'is_valid' => $file->isValid()
            ]);
            
            if ($file->isValid() && $file->getError() === UPLOAD_ERR_OK) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('content', $filename, 'public');
                
                if (!$attachmentPath) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File upload failed - could not store file',
                        'errors' => ['attachment' => ['Failed to store file on server']]
                    ], 422);
                }
                
                Log::info('File uploaded successfully', ['path' => $attachmentPath]);
            } else {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File is too large (exceeds server limit)',
                    UPLOAD_ERR_FORM_SIZE => 'File is too large (exceeds form limit)',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
                ];
                $errorMessage = $errorMessages[$file->getError()] ?? 'Unknown upload error';
                
                Log::error('File upload error', [
                    'error_code' => $file->getError(),
                    'error_message' => $errorMessage,
                    'file_name' => $file->getClientOriginalName()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'File upload failed',
                    'errors' => ['attachment' => [$errorMessage]]
                ], 422);
            }
        }

        // Get referenced objects to ensure they exist
        $program = Program::find($request->program_id);
        $module = Module::find($request->module_id);
        $course = Course::find($request->course_id);
        
        if (!$program || !$module || !$course) {
            Log::error('Referenced objects not found', [
                'program_exists' => $program ? true : false,
                'module_exists' => $module ? true : false,
                'course_exists' => $course ? true : false,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Referenced program, module, or course not found'
            ], 404);
        }

        // Prepare content-specific data based on type
        $contentType = $request->input('content_type');
        $contentData = [];
        
        switch ($contentType) {
            case 'lesson':
                $contentData = [
                    'lesson_content' => $request->input('lesson_content'),
                    'video_url' => $request->input('lesson_video_url'),
                ];
                break;
            case 'video':
                $contentData = [
                    'video_url' => $request->input('lesson_video_url') ?: $request->input('content_url'),
                ];
                break;
            case 'assignment':
                $contentData = [
                    'assignment_instructions' => $request->input('assignment_instructions'),
                    'due_date' => $request->input('due_date'),
                    'max_points' => $request->input('max_points', 0),
                ];
                break;
            case 'quiz':
                $contentData = [
                    'quiz_instructions' => $request->input('quiz_instructions'),
                    'time_limit' => $request->input('time_limit', 30),
                    'max_points' => $request->input('max_points', 0),
                ];
                break;
            case 'test':
                $contentData = [
                    'test_instructions' => $request->input('test_instructions'),
                    'test_date' => $request->input('test_date'),
                    'test_duration' => $request->input('test_duration', 60),
                    'total_marks' => $request->input('total_marks', 100),
                ];
                break;
            case 'pdf':
            case 'document':
                $contentData = [
                    'document_url' => $request->input('content_url'),
                ];
                break;
            case 'link':
                $contentData = [
                    'link_url' => $request->input('content_url'),
                ];
                break;
        }

        Log::info('Creating ContentItem with data', [
            'course_id' => $course->subject_id,
            'content_type' => $contentType,
            'has_attachment' => !empty($attachmentPath),
            'attachment_path' => $attachmentPath
        ]);

        // Create content item directly linked to course (no lesson_id)
        $contentItem = ContentItem::create([
            'content_title' => $request->input('content_title'),
            'content_description' => $request->input('content_description'),
            'course_id' => $course->subject_id,
            'content_type' => $contentType,
            'content_data' => $contentData,
            'attachment_path' => $attachmentPath,
            'max_points' => $request->input('max_points', 0),
            'due_date' => $request->input('due_date'),
            'time_limit' => $request->input('time_limit'),
            'is_required' => true,
            'is_active' => true,
            'enable_submission' => $request->boolean('enable_submission'),
            'allowed_file_types' => $request->input('allowed_file_types'),
            'max_file_size' => $request->input('max_file_size', 10),
            'submission_instructions' => $request->input('submission_instructions'),
            'content_url' => $request->input('content_url'),
        ]);

        Log::info('ContentItem created successfully', ['content_item_id' => $contentItem->id]);

        return response()->json([
            'success' => true,
            'message' => 'Course content created successfully!',
            'content_item' => $contentItem
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed', [
            'errors' => $e->errors(),
            'request_data' => $request->except(['attachment'])
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Error creating course content: ' . $e->getMessage(), [
            'request' => $request->except(['attachment']),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error creating course content: ' . $e->getMessage(),
            'errors' => [$e->getMessage()]
        ], 500);
    }
}
