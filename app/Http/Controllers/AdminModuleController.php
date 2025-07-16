<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Program;
use App\Models\Course;
use App\Models\StudentBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminModuleController extends Controller
{
    /**
     * Display a listing of modules.
     */
    public function index(Request $request)
    {
        $programs = Program::all();
        $modules = collect();
        $allModules = Module::where('is_archived', false)->with(['program', 'batch'])->get();
        
        if ($request->has('program_id') && $request->program_id != '') {
            $modules = Module::where('program_id', $request->program_id)
                           ->where('is_archived', false)
                           ->with(['program', 'batch'])
                           ->orderBy('module_order', 'asc')
                           ->orderBy('created_at', 'desc')
                           ->get();
        }

        return view('admin.admin-modules.admin-modules', compact('programs', 'modules', 'allModules'));
    }

    /**
     * Show the form for creating a new module.
     */
    public function create()
    {
        $programs = Program::all();
        return view('admin.admin-modules.create', compact('programs'));
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'module_name' => 'required|string|max:255',
            'module_description' => 'nullable|string',
            'program_id' => 'required|exists:programs,program_id',
            'batch_id' => 'required|exists:student_batches,batch_id',
            'learning_mode' => 'required|in:Synchronous,Asynchronous',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg,mp4,webm,ogg|max:102400',
            'content_type' => 'nullable|string|in:module,assignment,quiz,ai_quiz,test,link',
            'video_url' => 'nullable|url',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $attachmentPath = $file->storeAs('modules', $filename, 'public');
        }

        // Handle AI document upload
        $aiDocumentPath = null;
        if ($request->hasFile('ai_document')) {
            $file = $request->file('ai_document');
            $filename = time() . '_ai_doc_' . $file->getClientOriginalName();
            $aiDocumentPath = $file->storeAs('modules/ai_documents', $filename, 'public');
        }

        // Prepare content-specific data
        $contentData = [];
        $contentType = $request->input('content_type', 'module');

        // Extract content-specific fields based on type
        switch ($contentType) {
            case 'assignment':
                $contentData = [
                    'assignment_title' => $request->input('assignment_title'),
                    'assignment_instructions' => $request->input('assignment_instructions'),
                    'due_date' => $request->input('due_date'),
                    'max_points' => $request->input('max_points'),
                    'allow_late_submission' => $request->boolean('allow_late_submission'),
                ];
                break;
            case 'quiz':
                $contentData = [
                    'quiz_title' => $request->input('quiz_title'),
                    'quiz_description' => $request->input('quiz_description'),
                    'time_limit' => $request->input('time_limit'),
                    'question_count' => $request->input('question_count'),
                    'randomize_questions' => $request->boolean('randomize_questions'),
                ];
                break;
            case 'ai_quiz':
                $contentData = [
                    'ai_quiz_title' => $request->input('ai_quiz_title'),
                    'ai_quiz_description' => $request->input('ai_quiz_description'),
                    'ai_document_path' => $aiDocumentPath,
                    'ai_num_questions' => $request->input('ai_num_questions', 10),
                    'ai_difficulty' => $request->input('ai_difficulty', 'medium'),
                    'ai_quiz_type' => $request->input('ai_quiz_type', 'multiple_choice'),
                    'ai_time_limit' => $request->input('ai_time_limit', 30),
                    'ai_instructions' => $request->input('ai_instructions'),
                    'ai_generated' => true,
                ];
                break;
            case 'test':
                $contentData = [
                    'test_title' => $request->input('test_title'),
                    'test_description' => $request->input('test_description'),
                    'test_date' => $request->input('test_date'),
                    'duration' => $request->input('duration'),
                    'total_marks' => $request->input('total_marks'),
                ];
                break;
            case 'link':
                $contentData = [
                    'external_url' => $request->input('external_url'),
                    'link_title' => $request->input('link_title'),
                    'link_description' => $request->input('link_description'),
                    'link_type' => $request->input('link_type', 'other'),
                    'open_in_new_tab' => $request->boolean('open_in_new_tab'),
                ];
                break;
        }

        // Handle video URL (YouTube, Vimeo, etc.)
        $videoUrl = null;
        $videoPath = null;
        if ($request->video_url) {
            $videoUrl = $request->video_url;
            // Convert YouTube URLs to embed format
            if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                $videoUrl = $this->convertYouTubeToEmbed($videoUrl);
            }
        }

        $module = Module::create([
            'module_name' => $request->module_name,
            'module_description' => $request->module_description,
            'program_id' => $request->program_id,
            'batch_id' => $request->batch_id,
            'learning_mode' => $request->learning_mode,
            'attachment' => $attachmentPath,
            'content_type' => $contentType,
            'content_data' => $contentData,
            'video_url' => $videoUrl,
            'video_path' => $videoPath,
            'is_archived' => false,
        ]);

        // If this is an AI quiz, trigger AI generation
        if ($contentType === 'ai_quiz' && $aiDocumentPath) {
            $this->generateAIQuiz($module);
        }

        return redirect()->route('admin.modules.index', ['program_id' => $request->program_id])
                        ->with('success', 'Module created successfully!');
    }

    /**
     * Display the specified module.
     */
    public function show(Module $module)
    {
        return view('admin.admin-modules.show', compact('module'));
    }


    /**
     * Get modules for a specific program (JSON).
     */
    public function getModulesForProgram($programId)
    {
        try {
            $modules = Module::where('program_id', $programId)
                             ->where('is_archived', false)
                             ->orderBy('module_order','asc')
                             ->get(['modules_id as id','module_name']);

            return response()->json([
                'success' => true,
                'modules' => $modules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading modules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing a module.
     */
    public function edit($id)
    {
        $module = Module::findOrFail($id);
        $programs = Program::where('is_archived', false)->get();
        
        return view('admin.admin-modules.edit', compact('module', 'programs'));
    }

    /**
     * Update the specified module in storage.
     */
    public function update(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        
        $request->validate([
            'module_name' => 'required|string|max:255',
            'module_description' => 'nullable|string',
            'program_id' => 'required|exists:programs,program_id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg,mp4,avi,mov|max:102400',
            'content_type' => 'nullable|string|in:module,assignment,quiz,test,link',
        ]);

        // Handle file upload if provided
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($module->attachment && Storage::disk('public')->exists($module->attachment)) {
                Storage::disk('public')->delete($module->attachment);
            }
            
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $module->attachment = $file->storeAs('modules', $filename, 'public');
        }

        // Update module details
        $module->module_name = $request->module_name;
        $module->module_description = $request->module_description;
        $module->program_id = $request->program_id;
        $module->save();

        return redirect()->route('admin.modules.index', ['program_id' => $module->program_id])
                        ->with('success', 'Module updated successfully!');
    }

    /**
     * Upload video for a module.
     */
    public function uploadVideo(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        
        $request->validate([
            'video' => 'required|file|mimes:mp4,avi,mov,wmv|max:512000', // 500MB max
        ]);

        try {
            if ($request->hasFile('video')) {
                // Delete old video if exists
                if ($module->video_path && Storage::disk('public')->exists($module->video_path)) {
                    Storage::disk('public')->delete($module->video_path);
                }
                
                $file = $request->file('video');
                $filename = time() . '_video_' . $file->getClientOriginalName();
                $videoPath = $file->storeAs('modules/videos', $filename, 'public');
                
                // Update module with video path
                $module->video_path = $videoPath;
                $module->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Video uploaded successfully!',
                    'video_url' => asset('storage/' . $videoPath)
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Video upload failed: ' . $e->getMessage()
            ], 500);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No video file provided.'
        ]);
    }

    /**
     * Add additional content to a module.
     */
    public function addContent(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        
        $request->validate([
            'content_type' => 'required|string|in:text,file,link,video',
            'content_title' => 'required|string|max:255',
            'content_description' => 'nullable|string',
            'content_file' => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg,mp4,avi,mov|max:102400',
            'content_link' => 'nullable|url',
            'content_text' => 'nullable|string',
        ]);

        try {
            $contentData = [
                'type' => $request->content_type,
                'title' => $request->content_title,
                'description' => $request->content_description,
                'created_at' => now()->toISOString(),
            ];

            // Handle different content types
            switch ($request->content_type) {
                case 'file':
                case 'video':
                    if ($request->hasFile('content_file')) {
                        $file = $request->file('content_file');
                        $filename = time() . '_content_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('modules/content', $filename, 'public');
                        $contentData['file_path'] = $filePath;
                        $contentData['file_name'] = $file->getClientOriginalName();
                        $contentData['file_size'] = $file->getSize();
                    }
                    break;
                    
                case 'link':
                    $contentData['url'] = $request->content_link;
                    break;
                    
                case 'text':
                    $contentData['content'] = $request->content_text;
                    break;
            }

            // Get existing additional content or create new array
            $additionalContent = $module->additional_content ? json_decode($module->additional_content, true) : [];
            $additionalContent[] = $contentData;
            
            // Update module with new content
            $module->additional_content = json_encode($additionalContent);
            $module->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Content added successfully!',
                'content' => $contentData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified module from storage.
     */
    public function destroy(Module $module)
    {
        $programId = $module->program_id;
        
        // Delete attachment file if it exists
        if ($module->attachment && Storage::disk('public')->exists($module->attachment)) {
            Storage::disk('public')->delete($module->attachment);
        }
        
        $module->delete();

        return redirect()->route('admin.modules.index', ['program_id' => $programId])
                        ->with('success', 'Module deleted successfully!');
    }

    /**
     * Store multiple modules in storage (batch upload).
     */
    public function batchStore(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'modules' => 'required|array|min:1',
            'modules.*.module_name' => 'required|string|max:255',
            'modules.*.module_description' => 'nullable|string',
            'modules.*.attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg|max:10240',
            'modules.*.content_type' => 'required|in:module,assignment,quiz,test,link',
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($request->modules as $index => $moduleData) {
            try {
                $attachmentPath = null;
                
                // Handle file upload if present
                if ($request->hasFile("modules.{$index}.attachment")) {
                    $file = $request->file("modules.{$index}.attachment");
                    $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                    $attachmentPath = $file->storeAs('modules', $filename, 'public');
                }

                // Prepare content-specific data
                $contentData = [];
                $contentType = $moduleData['content_type'];
                $allData = $request->input("modules.{$index}");
                
                switch ($contentType) {
                    case 'assignment':
                        $contentData = [
                            'assignment_title' => $allData['assignment_title'] ?? null,
                            'assignment_instructions' => $allData['assignment_instructions'] ?? null,
                            'due_date' => $allData['due_date'] ?? null,
                            'max_points' => $allData['max_points'] ?? null,
                        ];
                        break;
                    case 'quiz':
                        $contentData = [
                            'quiz_title' => $allData['quiz_title'] ?? null,
                            'quiz_description' => $allData['quiz_description'] ?? null,
                            'time_limit' => $allData['time_limit'] ?? null,
                            'question_count' => $allData['question_count'] ?? null,
                        ];
                        break;
                    case 'test':
                        $contentData = [
                            'test_title' => $allData['test_title'] ?? null,
                            'test_description' => $allData['test_description'] ?? null,
                            'test_date' => $allData['test_date'] ?? null,
                            'duration' => $allData['duration'] ?? null,
                            'total_marks' => $allData['total_marks'] ?? null,
                        ];
                        break;
                    case 'link':
                        $contentData = [
                            'external_url' => $allData['external_url'] ?? null,
                            'link_title' => $allData['link_title'] ?? null,
                            'link_description' => $allData['link_description'] ?? null,
                            'link_type' => $allData['link_type'] ?? 'other',
                        ];
                        break;
                }

                Module::create([
                    'module_name' => $moduleData['module_name'],
                    'module_description' => $moduleData['module_description'] ?? null,
                    'program_id' => $request->program_id,
                    'attachment' => $attachmentPath,
                    'content_type' => $contentType,
                    'content_data' => $contentData,
                    'is_archived' => false,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to create module '{$moduleData['module_name']}': " . $e->getMessage();
            }
        }

        $message = "Successfully created {$successCount} module(s).";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return redirect()->route('admin.modules.index', ['program_id' => $request->program_id])
                        ->with('success', $message);
    }

    /**
     * Toggle archive status of a module.
     */
    public function toggleArchive(Request $request, Module $module)
    {
        $request->validate([
            'is_archived' => 'required|boolean'
        ]);

        $module->update(['is_archived' => $request->is_archived]);

        $status = $request->is_archived ? 'archived' : 'unarchived';
        
        return response()->json([
            'success' => true,
            'message' => "Module {$status} successfully!"
        ]);
    }

    /**
     * Batch delete modules.
     */
    public function batchDelete(Request $request)
    {
        $request->validate([
            'module_ids' => 'required|array|min:1',
            'module_ids.*' => 'exists:modules,modules_id'
        ]);

        $modules = Module::whereIn('modules_id', $request->module_ids)->get();
        
        try {
            foreach ($modules as $module) {
                // Delete file if exists
                if ($module->attachment && Storage::disk('public')->exists($module->attachment)) {
                    Storage::disk('public')->delete($module->attachment);
                }
                $module->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => count($modules) . ' modules deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Batch delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show archived modules.
     */
    public function archived(Request $request)
    {
        $programs = Program::all();
        $archivedModules = collect();
        
        if ($request->has('program_id') && $request->program_id != '') {
            $archivedModules = Module::where('program_id', $request->program_id)
                           ->where('is_archived', true)
                           ->with('program')
                           ->orderBy('updated_at', 'desc')
                           ->get();
        }
        
        return view('admin.admin-modules.admin-modules-archived', compact('programs', 'archivedModules'));
    }

    /**
     * Update module order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'module_ids' => 'required|array',
            'module_ids.*' => 'exists:modules,modules_id'
        ]);

        try {
            foreach ($request->module_ids as $index => $moduleId) {
                Module::where('modules_id', $moduleId)
                    ->update(['module_order' => $index + 1]);
            }

            return response()->json(['success' => true, 'message' => 'Module order updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update module order: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle admin override for a module.
     */
    public function toggleAdminOverride(Request $request, $moduleId)
    {
        try {
            $module = Module::findOrFail($moduleId);
            $module->admin_override = !$module->admin_override;
            $module->save();

            return response()->json([
                'success' => true, 
                'message' => 'Admin override updated successfully',
                'admin_override' => $module->admin_override
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update admin override']);
        }
    }

    /**
     * Generate AI quiz from uploaded document
     */
    private function generateAIQuiz($module)
    {
        try {
            $contentData = $module->content_data;
            $documentPath = storage_path('app/public/' . $contentData['ai_document_path']);
            
            // Extract text from document
            $text = $this->extractTextFromDocument($documentPath);
            
            // Generate quiz questions using AI (placeholder for now)
            $questions = $this->generateQuizQuestions($text, $contentData);
            
            // Update module with generated questions
            $contentData['generated_questions'] = $questions;
            $contentData['generation_status'] = 'completed';
            $contentData['generated_at'] = now()->toISOString();
            
            $module->content_data = $contentData;
            $module->save();
            
        } catch (\Exception $e) {
            Log::error('AI Quiz generation failed: ' . $e->getMessage());
            
            // Update module with error status
            $contentData = $module->content_data;
            $contentData['generation_status'] = 'failed';
            $contentData['generation_error'] = $e->getMessage();
            $module->content_data = $contentData;
            $module->save();
        }
    }

    /**
     * Extract text from uploaded document
     */
    private function extractTextFromDocument($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $text = '';
        
        switch (strtolower($extension)) {
            case 'txt':
                $text = file_get_contents($filePath);
                break;
            case 'pdf':
                // PDF text extraction (requires additional libraries)
                // For now, return placeholder
                $text = 'PDF content extraction requires additional setup';
                break;
            case 'doc':
            case 'docx':
                // DOC/DOCX text extraction (requires additional libraries)
                // For now, return placeholder
                $text = 'DOC/DOCX content extraction requires additional setup';
                break;
            default:
                throw new \Exception('Unsupported file format');
        }
        
        return $text;
    }

    /**
     * Generate quiz questions using AI
     */
    private function generateQuizQuestions($text, $contentData)
    {
        // This is a placeholder for AI quiz generation
        // In a real implementation, you would integrate with an AI service
        
        $numQuestions = $contentData['ai_num_questions'];
        $difficulty = $contentData['ai_difficulty'];
        $quizType = $contentData['ai_quiz_type'];
        
        $questions = [];
        
        // Generate sample questions based on parameters
        for ($i = 1; $i <= $numQuestions; $i++) {
            if ($quizType === 'multiple_choice' || $quizType === 'mixed') {
                $questions[] = [
                    'id' => $i,
                    'question' => "Sample question $i based on the document content",
                    'type' => 'multiple_choice',
                    'options' => [
                        'A' => 'Option A',
                        'B' => 'Option B',
                        'C' => 'Option C',
                        'D' => 'Option D'
                    ],
                    'correct_answer' => 'A',
                    'difficulty' => $difficulty,
                    'points' => 1
                ];
            } else {
                $questions[] = [
                    'id' => $i,
                    'question' => "True/False question $i based on the document content",
                    'type' => 'true_false',
                    'options' => [
                        'A' => 'True',
                        'B' => 'False'
                    ],
                    'correct_answer' => 'A',
                    'difficulty' => $difficulty,
                    'points' => 1
                ];
            }
        }
        
        return $questions;
    }

    /**
     * Get batches for a specific program
     */
    public function getBatchesForProgram($programId)
    {
        try {
            $batches = \App\Models\StudentBatch::where('program_id', $programId)
                ->select('batch_id as id', 'batch_name')
                ->get();
            
            return response()->json([
                'success' => true,
                'batches' => $batches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading batches: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get courses for a specific program
     */
    public function getCoursesForProgram($programId)
    {
        try {
            $courses = \App\Models\Course::where('program_id', $programId)
                ->select('subject_id', 'subject_name')
                ->get();
            
            return response()->json([
                'success' => true,
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading courses: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Archive a module
     */
    public function archive($id)
    {
        try {
            $module = Module::findOrFail($id);
            $module->is_archived = true;
            $module->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Module archived successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error archiving module: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Convert YouTube URL to embed format
     */
    private function convertYouTubeToEmbed($url)
    {
        // Extract video ID from different YouTube URL formats
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        
        return $url; // Return original if not a YouTube URL
    }

    /**
     * Update admin override settings for a module
     */
    public function updateOverride(Request $request, $moduleId)
    {
        try {
            $module = Module::findOrFail($moduleId);
            $overrides = $request->input('admin_override', []);
            
            $module->admin_override = $overrides;
            $module->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Override settings updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating override settings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get override settings for a module
     */
    public function getOverrideSettings($moduleId)
    {
        try {
            $module = Module::findOrFail($moduleId);
            $overrides = $module->admin_override ? $module->admin_override : [];
            
            return response()->json([
                'success' => true,
                'overrides' => $overrides
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading override settings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the admin quiz generator form
     */
    public function adminQuizGenerator()
    {
        $programs = Program::all();
        return view('admin.admin-modules.admin-quiz-generator', compact('programs'));
    }

    /**
     * Generate AI Quiz for Admin (Public endpoint)
     */
    public function generateAdminAiQuiz(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'batch_id' => 'required|exists:student_batches,batch_id',
            'document' => 'required|file|mimes:pdf,doc,docx,csv,txt|max:10240',
            'quiz_title' => 'required|string|max:255',
            'num_questions' => 'required|integer|min:1|max:50',
            'difficulty' => 'required|in:easy,medium,hard',
            'quiz_type' => 'required|in:multiple_choice,true_false,mixed',
            'time_limit' => 'required|integer|min:10|max:180',
        ]);

        try {
            // Store the uploaded document
            $file = $request->file('document');
            $filename = time() . '_ai_quiz_' . $file->getClientOriginalName();
            $documentPath = $file->storeAs('modules/ai_documents', $filename, 'public');

            // Create the quiz in the database
            $quiz = new \App\Models\Quiz();
            $quiz->professor_id = null; // Admin generated
            $quiz->program_id = $request->program_id;
            $quiz->quiz_title = $request->quiz_title;
            $quiz->instructions = $request->quiz_description ?? 'AI Generated Quiz from ' . $file->getClientOriginalName();
            $quiz->difficulty = $request->difficulty;
            $quiz->total_questions = $request->num_questions;
            $quiz->time_limit = $request->time_limit;
            $quiz->document_path = $documentPath;
            $quiz->is_active = true;
            $quiz->save();

            // Generate mock questions for now (replace with actual AI integration)
            $this->generateMockQuestions($quiz, $request->num_questions, $request->quiz_type);

            // Create assignment for the batch
            $this->createQuizAssignment($quiz, $request->batch_id);

            $programs = \App\Models\Program::all();
            return view('admin.admin-modules.admin-quiz-generator', compact('programs', 'quiz'))
                ->with('success', 'Quiz generated successfully and assigned to the batch!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI Quiz generation error: ' . $e->getMessage());
            $programs = \App\Models\Program::all();
            return view('admin.admin-modules.admin-quiz-generator', compact('programs'))
                ->with('error', 'Error generating AI quiz: ' . $e->getMessage());
        }
    }

    private function generateMockQuestions($quiz, $numQuestions, $quizType)
    {
        $sampleQuestions = [
            'multiple_choice' => [
                ['question' => 'What is the capital of France?', 'options' => ['Paris', 'London', 'Berlin', 'Madrid'], 'correct' => 'Paris'],
                ['question' => 'Which planet is closest to the Sun?', 'options' => ['Venus', 'Mercury', 'Earth', 'Mars'], 'correct' => 'Mercury'],
                ['question' => 'What is 2 + 2?', 'options' => ['3', '4', '5', '6'], 'correct' => '4'],
                ['question' => 'Which programming language is used for web development?', 'options' => ['Python', 'JavaScript', 'C++', 'Java'], 'correct' => 'JavaScript'],
                ['question' => 'What is the largest ocean on Earth?', 'options' => ['Atlantic', 'Indian', 'Arctic', 'Pacific'], 'correct' => 'Pacific'],
            ],
            'true_false' => [
                ['question' => 'The Earth is flat.', 'correct' => 'false'],
                ['question' => 'Water boils at 100°C.', 'correct' => 'true'],
                ['question' => 'There are 12 months in a year.', 'correct' => 'true'],
                ['question' => 'The sun rises in the west.', 'correct' => 'false'],
                ['question' => 'HTML stands for HyperText Markup Language.', 'correct' => 'true'],
            ]
        ];

        for ($i = 0; $i < $numQuestions; $i++) {
            $question = new \App\Models\QuizQuestion();
            $question->quiz_id = $quiz->quiz_id;
            $question->quiz_title = $quiz->quiz_title;
            $question->program_id = $quiz->program_id;
            $question->difficulty = $quiz->difficulty;
            $question->points = 1;
            $question->is_active = true;
            $question->created_by_admin = 1; // Admin created
            
            if ($quizType === 'multiple_choice' || ($quizType === 'mixed' && $i % 2 === 0)) {
                $mcQuestions = $sampleQuestions['multiple_choice'];
                $sample = $mcQuestions[$i % count($mcQuestions)];
                
                $question->question_type = 'multiple_choice';
                $question->question_text = $sample['question'];
                $question->options = $sample['options'];
                $question->correct_answer = $sample['correct'];
            } else {
                $tfQuestions = $sampleQuestions['true_false'];
                $sample = $tfQuestions[$i % count($tfQuestions)];
                
                $question->question_type = 'true_false';
                $question->question_text = $sample['question'];
                $question->options = ['true', 'false'];
                $question->correct_answer = $sample['correct'];
            }
            
            $question->save();
        }
    }

    private function createQuizAssignment($quiz, $batchId)
    {
        // Get all students in the batch
        $students = \App\Models\StudentBatch::where('batch_id', $batchId)
            ->where('enrollment_status', 'enrolled')
            ->get();

        foreach ($students as $student) {
            // Create assignment in deadlines table (using existing table structure)
            $deadline = new \App\Models\Deadline();
            $deadline->student_id = $student->student_id;
            $deadline->program_id = $quiz->program_id;
            $deadline->title = $quiz->quiz_title;
            $deadline->description = $quiz->instructions;
            $deadline->due_date = now()->addDays(7); // 7 days from now
            $deadline->type = 'quiz';
            $deadline->reference_id = $quiz->quiz_id;
            $deadline->status = 'pending';
            $deadline->save();
        }
    }
}
