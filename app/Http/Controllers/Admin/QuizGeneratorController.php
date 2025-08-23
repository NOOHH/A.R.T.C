<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AdminPreviewCustomization;
use App\Models\Professor;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\AdminSetting;
use App\Models\Module;
use App\Models\Program;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use App\Models\Course;
use App\Models\ContentItem;
use App\Services\GeminiQuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QuizGeneratorController extends Controller
{
    use AdminPreviewCustomization;
    protected $geminiQuizService;

    public function __construct(GeminiQuizService $geminiQuizService)
    {
        $this->middleware('admin.director.auth');
        $this->geminiQuizService = $geminiQuizService;
    }

    public function index()
    {
        $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';
        if (!$aiQuizEnabled) {
            return redirect()->route('admin.dashboard')->with('error', 'AI Quiz Generator is currently disabled.');
        }

        // Get all programs for admin (not limited like professor)
        $assignedPrograms = Program::where('is_archived', false)->orderBy('program_name')->get();
        
        // Get current admin ID from session
        $adminId = session('user_id'); // Admin login sets user_id to admin_id
        
        // Get ALL quizzes (both admin and professor created) - Admin can see everything
        $allQuizzes = Quiz::with(['program', 'module', 'course', 'questions'])
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        $draftQuizzes = $allQuizzes->where('status', 'draft');
        $publishedQuizzes = $allQuizzes->where('status', 'published');
        $archivedQuizzes = $allQuizzes->where('status', 'archived');
        
        return view('admin.quiz-generator.index', compact('assignedPrograms', 'draftQuizzes', 'publishedQuizzes', 'archivedQuizzes'));
    }

    public function getModulesByProgram($programId)
    {
        $modules = Module::where('program_id', $programId)
            ->where('is_archived', false)
            ->orderBy('module_name')
            ->get(['modules_id as module_id', 'module_name']);
        return response()->json(['success' => true, 'modules' => $modules]);
    }

    public function getCoursesByModule($moduleId)
    {
        $courses = Course::where('module_id', $moduleId)
            ->where('is_archived', false)
            ->orderBy('subject_name')
            ->get(['subject_id as course_id', 'subject_name as course_name']);
        return response()->json(['success' => true, 'courses' => $courses]);
    }

    public function getContentsByCourse($courseId)
    {
        $contents = ContentItem::where('course_id', $courseId)
                       ->active()
                       ->ordered()
                       ->get(['id as content_id','content_title']);
        return response()->json(['success' => true, 'contents' => $contents]);
    }

    // Alias for route compatibility: singular 'Content'
    public function getContentByCourse($courseId)
    {
        return $this->getContentsByCourse($courseId);
    }

    public function generateAIQuestions(Request $request)
    {
        Log::info('=== AI Quiz Generation Request (ADMIN) ===', [
            'admin_id' => session('user_id'), // Admin login sets user_id
            'request_data' => $request->except(['file', '_token']),
            'file' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'No file'
        ]);

        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,csv,txt,jpg,jpeg,png|max:51200', // 50MB limit
                'num_questions' => 'required|integer|min:1|max:50',
                'question_type' => 'required|in:multiple_choice,true_false,mixed',
            ]);

            if (empty(env('GEMINI_API_KEY')) || env('GEMINI_API_KEY') === 'your_gemini_api_key_here') {
                Log::error('Gemini API key is not configured.');
                return response()->json(['success' => false, 'message' => 'AI Service is not configured. Please contact administrator.'], 500);
            }

            $file = $request->file('file');
            $numQuestions = (int) $request->input('num_questions');
            $quizType = $request->input('question_type');

            if ($quizType === 'mixed') {
                $minMcq = (int) ceil($numQuestions / 2);
                $minTf = (int) floor($numQuestions / 2);
            } else {
                $minMcq = ($quizType === 'multiple_choice') ? $numQuestions : 0;
                $minTf = ($quizType === 'true_false') ? $numQuestions : 0;
            }
            
            $documentPath = $file->store('quiz-documents', 'public');
            $documentResult = $this->geminiQuizService->extractDocumentContent('public/' . $documentPath);

            if (empty($documentResult['content'])) {
                Log::warning('Failed to extract document content', ['error' => $documentResult['error'] ?? 'Unknown error']);
                return response()->json(['success' => false, 'message' => 'Failed to extract content from document.'], 400);
            }

            $quizData = $this->geminiQuizService->generateQuiz($documentResult['content'], $minMcq, $minTf);

            // Convert old format to new format for compatibility
            $generatedQuestions = [];
            
            // If Gemini returns an error or no questions, try the simple approach
            if (!empty($quizData['error']) || empty($quizData['mcqs']) && empty($quizData['true_false'])) {
                Log::warning('Standard Gemini service failed, trying simple approach', [
                    'error' => $quizData['error'] ?? 'No questions generated'
                ]);
                
                // Try simple service as fallback
                $simpleService = new \App\Services\SimpleGeminiQuizService();
                $simpleResult = $simpleService->generateQuiz($documentResult['content'], $numQuestions, 0);
                
                if ($simpleResult['success'] && !empty($simpleResult['questions'])) {
                    $generatedQuestions = $simpleResult['questions'];
                    Log::info('✓ Simple Gemini service succeeded as fallback', ['count' => count($generatedQuestions)]);
                } else {
                    $userMessage = 'AI quiz generation failed. The AI could not generate questions from the provided document. Try a different document, a smaller file, or fewer questions.';
                    return response()->json(['success' => false, 'message' => $userMessage], 500);
                }
            } else {
                // Convert standard format to frontend format
                if (!empty($quizData['mcqs'])) {
                    foreach ($quizData['mcqs'] as $mcq) {
                        $generatedQuestions[] = [
                            'question' => $mcq['text'],
                            'type' => 'multiple_choice',
                            'options' => $mcq['options'],
                            'correct_answer' => $mcq['correct_answer'] ?? 'A',
                            'explanation' => $mcq['explanation'] ?? 'Based on the document content.'
                        ];
                    }
                }
                
                if (!empty($quizData['true_false'])) {
                    foreach ($quizData['true_false'] as $tf) {
                        $generatedQuestions[] = [
                            'question' => $tf['statement'],
                            'type' => 'true_false',
                            'options' => ['True', 'False'],
                            'correct_answer' => $tf['correct_answer'] ?? 'True',
                            'explanation' => $tf['explanation'] ?? 'Based on the document content.'
                        ];
                    }
                }
            }

            // If the AI returns no questions, log and suggest next steps
            if (empty($generatedQuestions)) {
                Log::warning('AI service returned no questions after all attempts');
                return response()->json([
                    'success' => false,
                    'message' => 'The AI could not generate questions from the provided document. Try a different document, a smaller file, or fewer questions.'
                ], 400);
            }

            Log::info('✓ AI questions generated successfully via service (ADMIN)', ['count' => count($generatedQuestions)]);

            return response()->json([
                'success' => true,
                'questions' => $generatedQuestions
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed during AI question generation (ADMIN)', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Invalid input.', 'errors' => $e->errors()], 422);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('API connection error during AI question generation (ADMIN)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Connection to AI service timed out. Please try with a smaller document or fewer questions.'
            ], 504); // Gateway Timeout
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('API request error during AI question generation (ADMIN)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Error communicating with AI service: ' . $e->getMessage()
            ], 502); // Bad Gateway
        } catch (\Exception $e) {
            // Check for timeout error specifically
            if (strpos($e->getMessage(), 'time') !== false && strpos($e->getMessage(), 'exceeded') !== false) {
                Log::error('Timeout error in AI question generation (ADMIN)', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false, 
                    'message' => 'AI quiz generation timed out. Please try with a smaller document or fewer questions.'
                ], 504);
            }
            
            Log::error('An unexpected error occurred in generateAIQuestions (ADMIN)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'error' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'AI quiz generation failed: ' . $e->getMessage(),
                'error' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function saveQuizWithQuestions(Request $request)
    {
        Log::info('=== Saving AI-Generated Quiz (ADMIN) ===', [
            'admin_id' => session('user_id'), // Admin login sets user_id
            'request_data' => $request->except(['questions', '_token']),
            'has_questions' => $request->has('questions'),
            'questions_count' => $request->has('questions') ? count($request->input('questions', [])) : 0
        ]);

        try {
            // Handle both title and quiz_title fields
            $title = $request->input('title') ?? $request->input('quiz_title');
            if (!$title) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz title is required'
                ], 422);
            }
            
            // Ensure both title formats are available for validation
            $request->merge(['title' => $title, 'quiz_title' => $title]);
            
            // Log the questions data for debugging
            $questions = $request->input('questions', []);
            Log::info('Questions data received:', [
                'questions_count' => count($questions),
                'first_question_keys' => !empty($questions) ? array_keys($questions[0]) : [],
                'first_question_sample' => !empty($questions) ? $questions[0] : null
            ]);
            
            // Normalize questions data to handle different field name conventions
            $normalizedQuestions = [];
            foreach ($questions as $index => $question) {
                $normalizedQuestion = [
                    'question_text' => $question['question_text'] ?? $question['question'] ?? '',
                    'question_type' => $question['question_type'] ?? 'multiple_choice',
                    'options' => $question['options'] ?? [],
                    'correct_answer' => $question['correct_answer'] ?? null,
                    'correct_answers' => $question['correct_answers'] ?? null,
                    'explanation' => $question['explanation'] ?? '',
                    'points' => $question['points'] ?? 1,
                ];
                
                // Ensure we have a question text
                if (empty($normalizedQuestion['question_text'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Question text is required for question " . ($index + 1)
                    ], 422);
                }
                
                $normalizedQuestions[] = $normalizedQuestion;
            }
            
            // Replace the questions array with normalized data
            $request->merge(['questions' => $normalizedQuestions]);
            
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,program_id',
                'module_id' => 'nullable|exists:modules,modules_id',
                'course_id' => 'nullable|exists:courses,subject_id',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.question_type' => 'required|string|in:multiple_choice,true_false,short_answer,essay',
                'questions.*.options' => 'nullable',
                'questions.*.correct_answer' => 'nullable',
                'questions.*.correct_answers' => 'nullable',
                'questions.*.explanation' => 'nullable|string',
                'questions.*.points' => 'nullable|numeric',
            ]);

            // Admin doesn't need to be found like professor
            $adminId = session('user_id'); // Admin login sets user_id
            if (!$adminId) {
                Log::error('Admin not found for saving quiz', ['session_id' => session('user_id')]);
                return response()->json(['success' => false, 'message' => 'Admin session not found.'], 401);
            }

            DB::beginTransaction();

            // Check if infinite_retakes column exists in quizzes table
            $hasInfiniteRetakesColumn = false;
            try {
                $columns = Schema::getColumnListing('quizzes');
                $hasInfiniteRetakesColumn = in_array('infinite_retakes', $columns);
            } catch (\Exception $e) {
                Log::warning('Error checking for infinite_retakes column', ['error' => $e->getMessage()]);
            }
            
            // Create base quiz data
            $quizData = [
                'professor_id' => null, // Admin created
                'admin_id' => $adminId, // Set admin_id for admin created quizzes
                'program_id' => $validatedData['program_id'],
                'module_id' => $validatedData['module_id'],
                'course_id' => $validatedData['course_id'],
                'quiz_title' => $validatedData['title'],
                'quiz_description' => $request->input('description') ?? 'AI-generated quiz.',
                'instructions' => 'Please answer the following questions.',
                'status' => 'draft',
                'is_draft' => true,
                'is_active' => false,
                'time_limit' => $request->input('time_limit') ?? 60,
                'max_attempts' => $request->input('max_attempts') ?? 1,
                'has_deadline' => $request->input('has_deadline') ?? false,
                'total_questions' => count($validatedData['questions']),
                'created_at' => now(),
            ];
            
            // Add infinite_retakes if column exists
            if ($hasInfiniteRetakesColumn) {
                $quizData['infinite_retakes'] = $request->input('infinite_retakes') ?? false;
            }
            
            // Add due_date if provided and has_deadline is true
            if ($request->input('has_deadline') && $request->input('due_date')) {
                $quizData['due_date'] = $request->input('due_date');
            }
            
            $quiz = Quiz::create($quizData);

            $contentItem = ContentItem::create([
                'content_title' => $validatedData['title'],
                'content_description' => 'AI Generated Quiz',
                'course_id' => $validatedData['course_id'],
                'content_type' => 'quiz',
                'content_data' => json_encode(['quiz_id' => $quiz->quiz_id]),
                'is_active' => true,
            ]);

            $quiz->update(['content_id' => $contentItem->id]);

            foreach ($validatedData['questions'] as $questionData) {
                // Handle both field naming conventions (question vs question_text)
                $questionText = $questionData['question_text'] ?? $questionData['question'] ?? '';
                
                // Handle correct answer formats (could be string, array, or index)
                $correctAnswer = null;
                if (isset($questionData['correct_answer'])) {
                    $correctAnswer = $questionData['correct_answer'];
                } elseif (isset($questionData['correct_answers']) && is_array($questionData['correct_answers'])) {
                    $correctAnswer = $questionData['correct_answers'][0] ?? 0;
                }
                
                // Create the quiz question with properly mapped fields
                QuizQuestion::create([
                    'quiz_id' => $quiz->quiz_id,
                    'quiz_title' => $quiz->quiz_title,
                    'program_id' => $quiz->program_id,
                    'question_text' => $questionText,
                    'question_type' => $questionData['question_type'],
                    'options' => $questionData['options'] ?? [],
                    'correct_answer' => $correctAnswer,
                    'explanation' => $questionData['explanation'] ?? '',
                    'question_source' => 'manual',
                    'points' => $questionData['points'] ?? 1,
                    'is_active' => true,
                    'created_by_professor' => null, // Admin created
                ]);
            }

            DB::commit();

            Log::info('✓ AI-generated quiz saved successfully (ADMIN)', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz saved successfully as a draft.',
                'quiz_id' => $quiz->quiz_id,
                'redirect_url' => route('admin.quiz-generator')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An unexpected error occurred while saving AI quiz (ADMIN)', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Provide more specific error messages based on the exception
            $errorMessage = 'An unexpected server error occurred while saving the quiz.';
            
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $errorMessage = 'Validation error: ' . implode(', ', $e->errors());
            } elseif ($e instanceof \Illuminate\Database\QueryException) {
                if (strpos($e->getMessage(), 'Cannot add or update a child row') !== false) {
                    $errorMessage = 'Database constraint error: Foreign key constraint failed.';
                } elseif (strpos($e->getMessage(), 'Column cannot be null') !== false) {
                    $errorMessage = 'Database error: Required field cannot be null. Check all question fields.';
                }
            }
            
            return response()->json([
                'success' => false, 
                'message' => $errorMessage,
                'debug_info' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function saveManualQuiz(Request $request)
    {
        Log::info('=== Manual Quiz Save Request (ADMIN) ===', [
            'admin_id' => session('user_id'), // Admin login sets user_id
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'is_json' => $request->isJson(),
            'raw_content' => substr($request->getContent(), 0, 2000),
            'request_all' => $request->all(),
            'json_all' => $request->isJson() ? $request->json()->all() : null,
        ]);

        try {
            // Handle both form data and JSON requests
            $inputData = $request->isJson() ? $request->json()->all() : $request->all();
            
            Log::info('Input data extracted:', [
                'data_type' => $request->isJson() ? 'JSON' : 'Form',
                'input_keys' => array_keys($inputData),
                'title_value' => $inputData['title'] ?? 'NOT SET',
                'questions_count' => isset($inputData['questions']) ? count($inputData['questions']) : 0,
                'questions_sample' => isset($inputData['questions'][0]) ? $inputData['questions'][0] : 'NO QUESTIONS'
            ]);

            // Create new request with the input data for validation
            $request->merge($inputData);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,program_id',
                'module_id' => 'nullable|exists:modules,modules_id',
                'course_id' => 'nullable|exists:courses,subject_id',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'is_draft' => 'nullable|boolean',
                'time_limit' => 'nullable|integer|min:1',
                'max_attempts' => 'nullable|integer|min:1',
                'infinite_retakes' => 'nullable|boolean',
                'has_deadline' => 'nullable|boolean',
                'due_date' => 'nullable|date',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.question_type' => 'required|string|in:multiple_choice,true_false',
                'questions.*.options' => 'nullable|array',
                'questions.*.correct_answers' => 'nullable|array',
                'questions.*.explanation' => 'nullable|string',
                'questions.*.points' => 'nullable|numeric|min:0',
                'questions.*.order' => 'nullable|integer|min:1',
            ]);

            $adminId = session('user_id'); // Admin login sets user_id
            if (!$adminId) {
                Log::error('Admin not found for saving manual quiz', ['session_id' => session('user_id')]);
                return response()->json(['success' => false, 'message' => 'Admin session not found.'], 401);
            }

            DB::beginTransaction();

            // Determine status based on is_draft flag
            $isDraft = $validatedData['is_draft'] ?? true;
            $status = $isDraft ? 'draft' : 'published';

            $quiz = Quiz::create([
                'professor_id' => null, // Admin created
                'admin_id' => $adminId, // Set admin_id for admin created quizzes
                'program_id' => $validatedData['program_id'],
                'module_id' => $validatedData['module_id'],
                'course_id' => $validatedData['course_id'],
                'quiz_title' => $validatedData['title'],
                'quiz_description' => $validatedData['description'] ?? 'Manually created quiz.',
                'instructions' => $validatedData['instructions'] ?? 'Please answer the following questions.',
                'status' => $status,
                'is_draft' => $isDraft,
                'is_active' => !$isDraft,
                'time_limit' => $validatedData['time_limit'] ?? 60,
                'max_attempts' => $validatedData['infinite_retakes'] ? 999 : ($validatedData['max_attempts'] ?? 1),
                'infinite_retakes' => $validatedData['infinite_retakes'] ?? false,
                'has_deadline' => $validatedData['has_deadline'] ?? false,
                'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                'total_questions' => count($validatedData['questions']),
                'created_at' => now(),
            ]);

            // Create content item if course is specified
            if (!empty($validatedData['course_id'])) {
                $contentItem = ContentItem::create([
                    'content_title' => $validatedData['title'],
                    'content_description' => 'Manually Created Quiz',
                    'course_id' => $validatedData['course_id'],
                    'content_type' => 'quiz',
                    'content_data' => json_encode(['quiz_id' => $quiz->quiz_id]),
                    'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                    'is_active' => !$isDraft,
                ]);

                $quiz->update(['content_id' => $contentItem->id]);
            }

            // Create questions
            foreach ($validatedData['questions'] as $index => $questionData) {
                // Handle correct answers (support both array and string formats)
                $correctAnswer = '';
                if (!empty($questionData['correct_answers']) && is_array($questionData['correct_answers'])) {
                    $correctAnswer = $questionData['correct_answers'][0] ?? '';
                } else {
                    $correctAnswer = $questionData['correct_answers'] ?? '';
                }

                // Convert letter answers (A, B, C, D) to index format (0, 1, 2, 3)
                if (is_string($correctAnswer) && preg_match('/^[A-Z]$/', $correctAnswer)) {
                    $correctAnswer = (string)(ord($correctAnswer) - 65);
                }

                QuizQuestion::create([
                    'quiz_id' => $quiz->quiz_id,
                    'quiz_title' => $quiz->quiz_title,
                    'program_id' => $quiz->program_id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'question_order' => $questionData['order'] ?? ($index + 1),
                    'options' => $questionData['options'] ?? [],
                    'correct_answer' => $correctAnswer,
                    'explanation' => $questionData['explanation'] ?? '',
                    'question_source' => 'manual',
                    'points' => $questionData['points'] ?? 1,
                    'is_active' => true,
                    'created_by_professor' => null, // Admin created
                ]);
            }

            DB::commit();

            Log::info('✓ Manual quiz saved successfully (ADMIN)', [
                'quiz_id' => $quiz->quiz_id, 
                'status' => $status,
                'questions_saved' => count($validatedData['questions'])
            ]);

            $message = $isDraft 
                ? 'Quiz saved successfully as a draft.' 
                : 'Quiz published successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'quiz_id' => $quiz->quiz_id,
                'status' => $status,
                'redirect_url' => route('admin.quiz-generator')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed during manual quiz save (ADMIN)', [
                'errors' => $e->errors(),
                'input_data' => $request->all()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Invalid data provided.', 
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An unexpected error occurred while saving manual quiz (ADMIN)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input_data' => $request->except(['questions'])
            ]);
            return response()->json(['success' => false, 'message' => 'An unexpected server error occurred while saving the quiz.'], 500);
        }
    }

    /**
     * General save method (can handle both manual and AI-generated quizzes)
     */
    public function save(Request $request)
    {
        Log::info('=== General Quiz Save (ADMIN) ===', [
            'admin_id' => session('user_id'), // Admin login sets user_id
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'is_json' => $request->isJson(),
            'quiz_type' => $request->input('quiz_type', 'unknown')
        ]);

        // Handle both form data and JSON requests
        $inputData = $request->isJson() ? $request->json()->all() : $request->all();
        
        Log::info('Input data extracted:', [
            'data_type' => $request->isJson() ? 'JSON' : 'Form',
            'input_keys' => array_keys($inputData),
            'title_value' => $inputData['title'] ?? 'NOT SET',
            'questions_count' => isset($inputData['questions']) ? count($inputData['questions']) : 0
        ]);

        // Create new request with the input data for validation
        $request->merge($inputData);

        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,program_id',
                'module_id' => 'nullable|exists:modules,modules_id',
                'course_id' => 'nullable|exists:courses,subject_id',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'is_draft' => 'nullable|boolean',
                'time_limit' => 'nullable|integer|min:1',
                'max_attempts' => 'nullable|integer|min:1',
                'infinite_retakes' => 'nullable|boolean',
                'has_deadline' => 'nullable|boolean',
                'due_date' => 'nullable|date',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.question_type' => 'required|string|in:multiple_choice,true_false,short_answer,essay',
                'questions.*.options' => 'nullable|array',
                'questions.*.correct_answers' => 'nullable|array',
                'questions.*.explanation' => 'nullable|string',
                'questions.*.points' => 'nullable|numeric|min:0',
                'questions.*.order' => 'nullable|integer|min:1',
            ]);

            $adminId = session('user_id'); // Admin login sets user_id, not admin_id
            if (!$adminId) {
                Log::error('Admin not found for saving quiz', [
                    'session_user_id' => session('user_id'),
                    'session_user_type' => session('user_type'),
                    'all_session' => session()->all()
                ]);
                return response()->json(['success' => false, 'message' => 'Admin session not found.'], 401);
            }

            DB::beginTransaction();

            // Determine status based on is_draft flag
            $isDraft = $validatedData['is_draft'] ?? true;
            $status = $isDraft ? 'draft' : 'published';

            $quiz = Quiz::create([
                'professor_id' => null, // Admin created
                'admin_id' => $adminId, // Set admin_id for admin created quizzes
                'program_id' => $validatedData['program_id'],
                'module_id' => $validatedData['module_id'],
                'course_id' => $validatedData['course_id'],
                'quiz_title' => $validatedData['title'],
                'quiz_description' => $validatedData['description'] ?? 'Admin created quiz.',
                'instructions' => $validatedData['instructions'] ?? 'Please answer the following questions.',
                'status' => $status,
                'is_draft' => $isDraft,
                'is_active' => !$isDraft,
                'time_limit' => $validatedData['time_limit'] ?? 60,
                'max_attempts' => $validatedData['infinite_retakes'] ? 999 : ($validatedData['max_attempts'] ?? 1),
                'infinite_retakes' => $validatedData['infinite_retakes'] ?? false,
                'has_deadline' => $validatedData['has_deadline'] ?? false,
                'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                'total_questions' => count($validatedData['questions']),
                'created_at' => now(),
            ]);

            // Create content item if course is specified
            if (!empty($validatedData['course_id'])) {
                $contentItem = ContentItem::create([
                    'content_title' => $validatedData['title'],
                    'content_description' => 'Admin Created Quiz',
                    'course_id' => $validatedData['course_id'],
                    'content_type' => 'quiz',
                    'content_data' => json_encode(['quiz_id' => $quiz->quiz_id]),
                    'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                    'is_active' => !$isDraft,
                ]);

                $quiz->update(['content_id' => $contentItem->id]);
            }

            // Create questions
            foreach ($validatedData['questions'] as $index => $questionData) {
                // Handle correct answers (support both array and string formats)
                $correctAnswer = '';
                if (!empty($questionData['correct_answers']) && is_array($questionData['correct_answers'])) {
                    $correctAnswer = $questionData['correct_answers'][0] ?? '';
                } else {
                    $correctAnswer = $questionData['correct_answers'] ?? '';
                }

                // Convert letter answers (A, B, C, D) to index format (0, 1, 2, 3)
                if (is_string($correctAnswer) && preg_match('/^[A-Z]$/', $correctAnswer)) {
                    $correctAnswer = (string)(ord($correctAnswer) - 65);
                }

                QuizQuestion::create([
                    'quiz_id' => $quiz->quiz_id,
                    'quiz_title' => $quiz->quiz_title,
                    'program_id' => $quiz->program_id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'question_order' => $questionData['order'] ?? ($index + 1),
                    'options' => $questionData['options'] ?? [],
                    'correct_answer' => $correctAnswer,
                    'explanation' => $questionData['explanation'] ?? '',
                    'question_source' => 'manual',
                    'points' => $questionData['points'] ?? 1,
                    'is_active' => true,
                    'created_by_professor' => null, // Admin created
                ]);
            }

            DB::commit();

            Log::info('✓ Quiz saved successfully (ADMIN)', [
                'quiz_id' => $quiz->quiz_id, 
                'status' => $status,
                'questions_saved' => count($validatedData['questions'])
            ]);

            $message = $isDraft 
                ? 'Quiz saved successfully as a draft.' 
                : 'Quiz published successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'quiz_id' => $quiz->quiz_id,
                'status' => $status,
                'redirect_url' => route('admin.quiz-generator')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed during quiz save (ADMIN)', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Invalid input data.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An unexpected error occurred while saving quiz (ADMIN)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input_data' => $request->except(['questions'])
            ]);
            return response()->json(['success' => false, 'message' => 'An unexpected server error occurred while saving the quiz.'], 500);
        }
    }

    /**
     * Preview a quiz
     */
    public function preview(Quiz $quiz)
    {
        // Admin can view any quiz (removed restriction on professor-created quizzes)

        $questions = $quiz->questions()->orderBy('question_order')->get();
        
        return view('Quiz Generator.admin.quiz-preview', compact('quiz', 'questions'));
    }

    /**
     * Preview a quiz (alternative method name)
     */
    public function previewQuiz(Quiz $quiz)
    {
        return $this->preview($quiz);
    }

    /**
     * Publish a quiz
     */
    public function publish($quizId)
    {
        Log::info('Admin trying to publish quiz', ['quiz_id' => $quizId]);
        
        try {
            // Find the quiz by ID instead of relying on route model binding
            $quiz = Quiz::findOrFail($quizId);
            
            // Admin can publish any quiz (both admin and professor created)
            $quiz->update([
                'status' => 'published',
                'is_draft' => false,
                'is_active' => true
            ]);

            // Update content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->update([
                    'is_active' => true
                ]);
            }

            Log::info('Quiz published successfully (ADMIN)', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz published successfully.',
                'quiz_id' => $quiz->quiz_id
            ]);

        } catch (\Exception $e) {
            Log::error('Error publishing quiz (ADMIN)', [
                'quiz_id' => $quizId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error publishing quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive a quiz
     */
    public function archive($quizId)
    {
        Log::info('Admin trying to archive quiz', ['quiz_id' => $quizId]);
        
        try {
            // Find the quiz by ID instead of relying on route model binding
            $quiz = Quiz::findOrFail($quizId);
            
            // Admin can archive any quiz (both admin and professor created)
            $quiz->update([
                'status' => 'archived',
                'is_draft' => false,
                'is_active' => false
            ]);

            // Update content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->update([
                    'is_active' => false
                ]);
            }

            Log::info('Quiz archived successfully (ADMIN)', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz archived successfully.',
                'quiz_id' => $quiz->quiz_id
            ]);

        } catch (\Exception $e) {
            Log::error('Error archiving quiz (ADMIN)', [
                'quiz_id' => $quizId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error archiving quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move quiz to draft
     */
    public function draft($quizId)
    {
        Log::info('Admin trying to move quiz to draft', ['quiz_id' => $quizId]);
        
        try {
            // Find the quiz by ID instead of relying on route model binding
            $quiz = Quiz::findOrFail($quizId);
            
            // Admin can modify any quiz (both admin and professor created)
            $quiz->update([
                'status' => 'draft',
                'is_draft' => true,
                'is_active' => false
            ]);

            // Update content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->update([
                    'is_active' => false
                ]);
            }

            Log::info('Quiz moved to draft successfully (ADMIN)', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz moved to draft successfully.',
                'quiz_id' => $quiz->quiz_id
            ]);

        } catch (\Exception $e) {
            Log::error('Error moving quiz to draft (ADMIN)', [
                'quiz_id' => $quizId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error moving quiz to draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a quiz
     */
    public function delete($quizId)
    {
        try {
            // Find the quiz by ID instead of relying on route model binding
            $quiz = Quiz::findOrFail($quizId);
            
            // Admin can delete any quiz (both admin and professor created)
            
            DB::beginTransaction();
            
            // Delete associated content item
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->delete();
            }

            // Delete quiz questions
            QuizQuestion::where('quiz_id', $quiz->quiz_id)->delete();

            // Delete the quiz
            $quiz->delete();
            
            DB::commit();

            Log::info('Quiz deleted successfully (ADMIN)', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting quiz (ADMIN)', [
                'quiz_id' => $quizId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive a quiz (legacy method)
     */
    public function archiveQuiz(Quiz $quiz)
    {
        try {
            // Admin can archive any quiz
            // Removed professor_id restriction - admin can modify any quiz

            $quiz->update(['status' => 'archived']);

            Log::info('Quiz archived successfully (ADMIN)', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz archived successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error archiving quiz (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error archiving quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a quiz from archived status
     */
    public function restoreQuiz(Quiz $quiz)
    {
        try {
            // Admin can restore any quiz
            // Removed professor_id restriction - admin can modify any quiz

            $quiz->update(['status' => 'draft']);

            Log::info('Quiz restored successfully (ADMIN)', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz restored successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error restoring quiz (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error restoring quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move quiz to draft
     */
    public function moveToDraft(Quiz $quiz)
    {
        try {
            // Admin can modify any quiz (removed restriction on professor-created quizzes)

            $quiz->update([
                'status' => 'draft',
                'is_draft' => true,
                'is_active' => false
            ]);

            // Update content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->update([
                    'is_active' => false
                ]);
            }

            Log::info('Quiz moved to draft successfully (ADMIN)', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz moved to draft successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error moving quiz to draft (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error moving quiz to draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a quiz permanently
     */
    public function deleteQuiz(Quiz $quiz)
    {
        try {
            // Admin can delete any quiz (both admin and professor created)
            
            DB::beginTransaction();

            // Delete content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->delete();
            }

            // Delete all quiz questions
            QuizQuestion::where('quiz_id', $quiz->quiz_id)->delete();

            // Delete the quiz
            $quiz->delete();

            DB::commit();

            Log::info('Quiz deleted successfully (ADMIN)', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting quiz (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit quiz questions (Admin can edit ANY quiz)
     */
    public function editQuestions(Quiz $quiz)
    {
        // Admin can edit any quiz (no ownership restriction)
        $questions = $quiz->questions()->orderBy('question_order')->get();
        $programs = Program::where('is_archived', false)->orderBy('program_name')->get();
        
        Log::info('Admin editing quiz questions', ['quiz_id' => $quiz->quiz_id, 'admin_id' => session('user_id')]);
        
        return view('admin.quiz-generator.quiz-questions-edit', compact('quiz', 'questions', 'programs'));
    }

    /**
     * Edit quiz (alias for editQuestions)
     */
    public function editQuiz(Quiz $quiz)
    {
        return $this->editQuestions($quiz);
    }

    /**
     * Get quiz data for editing (alternative method name)
     */
    public function getQuizForEdit(Quiz $quiz)
    {
        return $this->editQuestions($quiz);
    }

    /**
     * Update question in quiz
     */
    public function updateQuestion(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        try {
            // Admin can modify any quiz (removed restriction on professor-created quizzes)

            if ($question->quiz_id !== $quiz->quiz_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question does not belong to this quiz.'
                ], 403);
            }

            $validatedData = $request->validate([
                'question_text' => 'required|string',
                'question_type' => 'required|string|in:multiple_choice,true_false',
                'options' => 'nullable|array',
                'correct_answer' => 'required|string',
                'explanation' => 'nullable|string',
                'points' => 'nullable|numeric|min:0',
            ]);

            $question->update([
                'question_text' => $validatedData['question_text'],
                'question_type' => $validatedData['question_type'],
                'options' => $validatedData['options'] ?? [],
                'correct_answer' => $validatedData['correct_answer'],
                'explanation' => $validatedData['explanation'] ?? '',
                'points' => $validatedData['points'] ?? 1,
            ]);

            Log::info('Quiz question updated successfully (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'question_id' => $question->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully.',
                'question' => $question->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating quiz question (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'question_id' => $question->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error updating question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add new question to quiz
     */
    public function addQuestion(Request $request, Quiz $quiz)
    {
        try {
            // Admin can modify any quiz (removed restriction on professor-created quizzes)

            $validatedData = $request->validate([
                'question_text' => 'required|string',
                'question_type' => 'required|string|in:multiple_choice,true_false',
                'options' => 'nullable|array',
                'correct_answer' => 'required|string',
                'explanation' => 'nullable|string',
                'points' => 'nullable|numeric|min:0',
            ]);

            // Get next question order
            $nextOrder = $quiz->questions()->max('question_order') + 1;

            $question = QuizQuestion::create([
                'quiz_id' => $quiz->quiz_id,
                'quiz_title' => $quiz->quiz_title,
                'program_id' => $quiz->program_id,
                'question_text' => $validatedData['question_text'],
                'question_type' => $validatedData['question_type'],
                'question_order' => $nextOrder,
                'options' => $validatedData['options'] ?? [],
                'correct_answer' => $validatedData['correct_answer'],
                'explanation' => $validatedData['explanation'] ?? '',
                'question_source' => 'manual',
                'points' => $validatedData['points'] ?? 1,
                'is_active' => true,
                'created_by_professor' => null, // Admin created
            ]);

            // Update total questions count
            $totalQuestions = $quiz->questions()->count();
            $quiz->update(['total_questions' => $totalQuestions]);

            Log::info('Quiz question added successfully (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'question_id' => $question->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Question added successfully.',
                'question' => $question,
                'total_questions' => $totalQuestions
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding quiz question (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error adding question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete question from quiz
     */
    public function deleteQuestion(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        try {
            // Admin can modify any quiz (removed restriction on professor-created quizzes)

            if ($question->quiz_id !== $quiz->quiz_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question does not belong to this quiz.'
                ], 403);
            }

            $question->delete();

            // Update total questions count
            $remainingQuestions = $quiz->questions()->count();
            $quiz->update(['total_questions' => $remainingQuestions]);

            Log::info('Quiz question deleted successfully (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'question_id' => $question->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully.',
                'remaining_questions' => $remainingQuestions
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting quiz question (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'question_id' => $question->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update quiz with questions
     */
    public function updateQuizWithQuestions(Request $request, Quiz $quiz)
    {
        Log::info('=== Updating Quiz with Questions (ADMIN) ===', [
            'quiz_id' => $quiz->quiz_id,
            'admin_id' => session('user_id'), // Admin login sets user_id
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'is_json' => $request->isJson(),
            'request_all' => $request->all(),
        ]);

        try {
            // Admin can modify any quiz (removed restriction on professor-created quizzes)

            // Handle both form data and JSON requests
            $inputData = $request->isJson() ? $request->json()->all() : $request->all();
            $request->merge($inputData);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,program_id',
                'module_id' => 'nullable|exists:modules,modules_id',
                'course_id' => 'nullable|exists:courses,subject_id',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'is_draft' => 'nullable|boolean',
                'time_limit' => 'nullable|integer|min:1',
                'max_attempts' => 'nullable|integer|min:1',
                'infinite_retakes' => 'nullable|boolean',
                'has_deadline' => 'nullable|boolean',
                'due_date' => 'nullable|date',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.question_type' => 'required|string|in:multiple_choice,true_false',
                'questions.*.options' => 'nullable|array',
                'questions.*.correct_answers' => 'nullable|array',
                'questions.*.explanation' => 'nullable|string',
                'questions.*.points' => 'nullable|numeric|min:0',
                'questions.*.order' => 'nullable|integer|min:1',
            ]);

            DB::beginTransaction();

            // Determine status based on is_draft flag
            $isDraft = $validatedData['is_draft'] ?? true;
            $status = $isDraft ? 'draft' : 'published';

            // Update quiz
            $quiz->update([
                'quiz_title' => $validatedData['title'],
                'quiz_description' => $validatedData['description'] ?? 'Manually created quiz.',
                'instructions' => $validatedData['instructions'] ?? 'Please answer the following questions.',
                'program_id' => $validatedData['program_id'],
                'module_id' => $validatedData['module_id'],
                'course_id' => $validatedData['course_id'],
                'status' => $status,
                'is_draft' => $isDraft,
                'is_active' => !$isDraft,
                'time_limit' => $validatedData['time_limit'] ?? 60,
                'max_attempts' => $validatedData['infinite_retakes'] ? 999 : ($validatedData['max_attempts'] ?? 1),
                'infinite_retakes' => $validatedData['infinite_retakes'] ?? false,
                'has_deadline' => $validatedData['has_deadline'] ?? false,
                'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                'total_questions' => count($validatedData['questions']),
                'updated_at' => now(),
            ]);

            // Update content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->update([
                    'content_title' => $validatedData['title'],
                    'content_description' => 'Updated Quiz',
                    'course_id' => $validatedData['course_id'],
                    'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                    'is_active' => !$isDraft,
                ]);
            } elseif (!empty($validatedData['course_id'])) {
                // Create content item if doesn't exist but course is specified
                $contentItem = ContentItem::create([
                    'content_title' => $validatedData['title'],
                    'content_description' => 'Updated Quiz',
                    'course_id' => $validatedData['course_id'],
                    'content_type' => 'quiz',
                    'content_data' => json_encode(['quiz_id' => $quiz->quiz_id]),
                    'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                    'is_active' => !$isDraft,
                ]);

                $quiz->update(['content_id' => $contentItem->id]);
            }

            // Delete existing questions
            QuizQuestion::where('quiz_id', $quiz->quiz_id)->delete();

            // Create new questions
            foreach ($validatedData['questions'] as $index => $questionData) {
                $correctAnswer = '';
                if (!empty($questionData['correct_answers']) && is_array($questionData['correct_answers'])) {
                    $correctAnswer = $questionData['correct_answers'][0] ?? '';
                } else {
                    $correctAnswer = $questionData['correct_answers'] ?? '';
                }
                
                // Convert letter answers (A, B, C, D) to index format (0, 1, 2, 3)
                if (is_string($correctAnswer) && preg_match('/^[A-Z]$/', $correctAnswer)) {
                    $correctAnswer = (string)(ord($correctAnswer) - 65);
                }

                QuizQuestion::create([
                    'quiz_id' => $quiz->quiz_id,
                    'quiz_title' => $quiz->quiz_title,
                    'program_id' => $quiz->program_id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'question_order' => $questionData['order'] ?? ($index + 1),
                    'options' => $questionData['options'] ?? [],
                    'correct_answer' => $correctAnswer,
                    'explanation' => $questionData['explanation'] ?? '',
                    'question_source' => 'manual',
                    'points' => $questionData['points'] ?? 1,
                    'is_active' => true,
                    'created_by_professor' => null, // Admin created
                ]);
            }

            DB::commit();

            Log::info('✓ Quiz updated successfully (ADMIN)', [
                'quiz_id' => $quiz->quiz_id, 
                'status' => $status,
                'questions_updated' => count($validatedData['questions'])
            ]);

            return response()->json([
                'success' => true,
                'message' => $isDraft ? 'Quiz draft updated successfully!' : 'Quiz updated and published successfully!',
                'quiz_id' => $quiz->quiz_id,
                'is_draft' => $isDraft
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed for quiz update (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating quiz (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error updating quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get questions for modal display
     */
    public function getQuestionsForModal(Quiz $quiz)
    {
        try {
            // Admin can view any quiz
            // Removed professor_id restriction - admin can modify any quiz

            $questions = $quiz->questions()->orderBy('question_order')->get();
            
            return response()->json([
                'success' => true,
                'quiz' => [
                    'quiz_id' => $quiz->quiz_id,
                    'title' => $quiz->quiz_title,
                    'quiz_title' => $quiz->quiz_title,
                    'quiz_description' => $quiz->quiz_description,
                    'program_id' => $quiz->program_id,
                    'module_id' => $quiz->module_id,
                    'course_id' => $quiz->course_id,
                    'time_limit' => $quiz->time_limit,
                    'max_attempts' => $quiz->max_attempts,
                    'infinite_retakes' => $quiz->infinite_retakes,
                    'has_deadline' => $quiz->has_deadline,
                    'due_date' => $quiz->due_date,
                    'instructions' => $quiz->instructions,
                    'status' => $quiz->status,
                    'is_draft' => $quiz->is_draft,
                ],
                'questions' => $questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question_text' => $question->question_text,
                        'question_type' => $question->question_type,
                        'options' => $question->options ?? [],
                        'correct_answers' => [$question->correct_answer],
                        'explanation' => $question->explanation,
                        'points' => $question->points,
                        'order' => $question->question_order
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching quiz questions for modal (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error fetching quiz questions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quiz data for editing in modal
     */
    public function getQuiz($quizId)
    {
        try {
            Log::info('Admin fetching quiz data for editing', ['quiz_id' => $quizId]);
            
            // Find the quiz by ID
            $quiz = Quiz::with('questions')->findOrFail($quizId);
            
            // Get the questions in correct order
            $questions = $quiz->questions()->orderBy('question_order')->get();
            
            // Format the response data
            return response()->json([
                'success' => true,
                'quiz' => [
                    'quiz_id' => $quiz->quiz_id,
                    'title' => $quiz->quiz_title,
                    'quiz_title' => $quiz->quiz_title,
                    'quiz_description' => $quiz->quiz_description,
                    'program_id' => $quiz->program_id,
                    'module_id' => $quiz->module_id,
                    'course_id' => $quiz->course_id,
                    'time_limit' => $quiz->time_limit,
                    'max_attempts' => $quiz->max_attempts,
                    'infinite_retakes' => $quiz->infinite_retakes,
                    'has_deadline' => $quiz->has_deadline,
                    'due_date' => $quiz->due_date,
                    'instructions' => $quiz->instructions,
                    'status' => $quiz->status,
                    'is_draft' => $quiz->is_draft,
                ],
                'questions' => $questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question_text' => $question->question_text,
                        'question_type' => $question->question_type,
                        'options' => $question->options,
                        'correct_answers' => [$question->correct_answer],
                        'explanation' => $question->explanation,
                        'points' => $question->points,
                        'order' => $question->question_order
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching quiz for editing', [
                'quiz_id' => $quizId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading quiz data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update quiz based on input type (form or JSON)
     */
    public function updateQuiz(Request $request, $quizId)
    {
        Log::info('=== Update Quiz Request (ADMIN) ===', [
            'quiz_id' => $quizId,
            'admin_id' => session('user_id'), // Admin login sets user_id
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'is_json' => $request->isJson(),
            'request_all' => $request->all(),
        ]);

        try {
            // Find the quiz
            $quiz = Quiz::findOrFail($quizId);
            
            // Admin can modify any quiz (removed restriction on professor-created quizzes)

            // Handle both form data and JSON requests
            $inputData = $request->isJson() ? $request->json()->all() : $request->all();
            $request->merge($inputData);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,program_id',
                'module_id' => 'nullable|exists:modules,modules_id',
                'course_id' => 'nullable|exists:courses,subject_id',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'is_draft' => 'nullable|boolean',
                'time_limit' => 'nullable|integer|min:1',
                'max_attempts' => 'nullable|integer|min:1',
                'infinite_retakes' => 'nullable|boolean',
                'has_deadline' => 'nullable|boolean',
                'due_date' => 'nullable|date',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.question_type' => 'required|string|in:multiple_choice,true_false',
                'questions.*.options' => 'nullable|array',
                'questions.*.correct_answers' => 'nullable|array',
                'questions.*.explanation' => 'nullable|string',
                'questions.*.points' => 'nullable|numeric|min:0',
                'questions.*.order' => 'nullable|integer|min:1',
            ]);

            DB::beginTransaction();

            // Determine status based on is_draft flag
            $isDraft = $validatedData['is_draft'] ?? true;
            $status = $isDraft ? 'draft' : 'published';

            // Update quiz
            $quiz->update([
                'quiz_title' => $validatedData['title'],
                'quiz_description' => $validatedData['description'] ?? 'Manually updated quiz.',
                'program_id' => $validatedData['program_id'],
                'module_id' => $validatedData['module_id'],
                'course_id' => $validatedData['course_id'],
                'instructions' => $validatedData['instructions'] ?? 'Please answer the following questions.',
                'status' => $status,
                'is_draft' => $isDraft,
                'is_active' => !$isDraft,
                'time_limit' => $validatedData['time_limit'] ?? 60,
                'max_attempts' => $validatedData['infinite_retakes'] ? 999 : ($validatedData['max_attempts'] ?? 1),
                'infinite_retakes' => $validatedData['infinite_retakes'] ?? false,
                'has_deadline' => $validatedData['has_deadline'] ?? false,
                'due_date' => $validatedData['has_deadline'] ? $validatedData['due_date'] : null,
                'total_questions' => count($validatedData['questions']),
            ]);

            // Delete existing questions
            $quiz->questions()->delete();

            // Create new questions
            foreach ($validatedData['questions'] as $index => $questionData) {
                QuizQuestion::create([
                    'quiz_id' => $quiz->quiz_id,
                    'quiz_title' => $quiz->quiz_title,
                    'program_id' => $quiz->program_id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'question_order' => $questionData['order'] ?? $index + 1,
                    'options' => $questionData['options'] ?? [],
                    'correct_answer' => $questionData['correct_answers'][0] ?? ($questionData['options'][0] ?? 'True'),
                    'explanation' => $questionData['explanation'] ?? '',
                    'question_source' => 'manual',
                    'points' => $questionData['points'] ?? 1,
                    'is_active' => true,
                    'created_by_professor' => null, // Admin created
                ]);
            }

            DB::commit();

            Log::info('Quiz updated successfully (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'total_questions' => count($validatedData['questions'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz updated successfully.',
                'quiz_id' => $quiz->quiz_id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed for quiz update (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating quiz (ADMIN)', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error updating quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update method for route compatibility
     */
    public function update(Request $request, $quizId)
    {
        // This method is an alias for updateQuiz to maintain route compatibility
        return $this->updateQuiz($request, $quizId);
    }

    /**
     * Preview mode for tenant preview system - Quiz Generator
     */
    public function previewIndex($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Mock quiz data
            $quizzesCollection = collect([
                (object)[
                    'quiz_id' => 1,
                    'quiz_title' => 'Nursing Fundamentals Quiz',
                    'quiz_description' => 'Basic nursing concepts and principles',
                    'quiz_instructions' => 'Answer all questions to the best of your ability',
                    'total_questions' => 10,
                    'time_limit' => 30,
                    'passing_score' => 70,
                    'is_active' => true,
                    'created_at' => now()->subDays(7),
                    'module' => (object)[
                        'module_name' => 'Nursing Fundamentals',
                        'module_id' => 1
                    ]
                ],
                (object)[
                    'quiz_id' => 2,
                    'quiz_title' => 'Medical Terminology Assessment',
                    'quiz_description' => 'Test your knowledge of medical terms',
                    'quiz_instructions' => 'Select the best answer for each question',
                    'total_questions' => 15,
                    'time_limit' => 45,
                    'passing_score' => 80,
                    'is_active' => true,
                    'created_at' => now()->subDays(3),
                    'module' => (object)[
                        'module_name' => 'Medical Terminology',
                        'module_id' => 2
                    ]
                ]
            ]);

            // Generate mock data for quiz generator
            $modules = $this->generateMockData('modules');
            $assignedPrograms = $this->generateMockData('programs');
            
            // Mock quiz collections by status
            $draftQuizzes = collect([
                $this->createMockObject([
                    'id' => 1,
                    'quiz_id' => 1,
                    'title' => 'Draft Quiz 1',
                    'quiz_title' => 'Draft Quiz 1',
                    'status' => 'draft',
                    'created_at' => now()
                ])
            ]);
            
            $publishedQuizzes = collect([
                $this->createMockObject([
                    'id' => 2,
                    'quiz_id' => 2,
                    'title' => 'Published Quiz 1',
                    'quiz_title' => 'Published Quiz 1',
                    'status' => 'published',
                    'created_at' => now()
                ])
            ]);
            
            $archivedQuizzes = collect([
                $this->createMockObject([
                    'id' => 3,
                    'quiz_id' => 3,
                    'title' => 'Archived Quiz 1',
                    'quiz_title' => 'Archived Quiz 1',
                    'status' => 'archived',
                    'created_at' => now()
                ])
            ]);
            
            view()->share('modules', $modules);
            view()->share('assignedPrograms', $assignedPrograms);
            view()->share('draftQuizzes', $draftQuizzes);
            view()->share('publishedQuizzes', $publishedQuizzes);
            view()->share('archivedQuizzes', $archivedQuizzes);
            view()->share('isPreviewMode', true);

            $html = view('admin.quiz-generator.index', compact('assignedPrograms', 'draftQuizzes', 'publishedQuizzes', 'archivedQuizzes'))->render();

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin quiz generator preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>Admin Quiz Generator Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Admin Quiz Generator Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        } finally {
            session()->forget(['user_name', 'user_role', 'logged_in', 'preview_mode']);
        }
    }
}
