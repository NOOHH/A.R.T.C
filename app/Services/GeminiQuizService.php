<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class GeminiQuizService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = 'AIzaSyApwLadkEmUpUe8kv5Nl5-7p35ob9_DSsY';
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    }

    /**
     * Test connection to Gemini API
     */
    public function testConnection()
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Test connection']
                        ]
                    ]
                ]
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Gemini API connection test failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Generate quiz questions using Gemini AI from text content
     */
    public function generateQuizFromText($content, $options = [])
    {
        $numQuestions = $options['num_questions'] ?? 10;
        $difficulty = $options['difficulty'] ?? 'Medium';
        $quizType = $options['quiz_type'] ?? 'multiple_choice';
        $topic = $options['topic'] ?? 'General';

        $systemInstruction = $this->getSystemInstruction($quizType, $difficulty, $numQuestions);
        
        try {
            $prompt = $this->buildPrompt($content, $topic, $numQuestions, $difficulty, $quizType);
            
            $response = Http::timeout(60)->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemInstruction . "\n\n" . $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 4096,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->parseGeminiResponse($data);
            } else {
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Gemini API generateQuizFromText failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate quiz questions from uploaded file
     */
    public function generateQuizFromFile(UploadedFile $file, $options = [])
    {
        try {
            // Extract text content from file
            $content = $this->extractTextFromFile($file);
            
            if (empty($content)) {
                Log::warning('No content extracted from file', ['filename' => $file->getClientOriginalName()]);
                return null;
            }

            // Use the text-based generation method
            return $this->generateQuizFromText($content, $options);

        } catch (\Exception $e) {
            Log::error('Gemini API generateQuizFromFile failed', [
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName()
            ]);
            return null;
        }
    }

    /**
     * Extract text content from uploaded file
     */
    private function extractTextFromFile(UploadedFile $file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $content = '';

        try {
            switch ($extension) {
                case 'txt':
                    $content = file_get_contents($file->getRealPath());
                    break;
                
                case 'pdf':
                    // Use a PDF parser library if available
                    $content = $this->extractTextFromPdf($file);
                    break;
                
                case 'doc':
                case 'docx':
                    $content = $this->extractTextFromDoc($file);
                    break;
                
                default:
                    Log::warning('Unsupported file type for text extraction', ['extension' => $extension]);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error extracting text from file', [
                'error' => $e->getMessage(),
                'extension' => $extension
            ]);
        }

        return $content;
    }

    /**
     * Extract text from PDF file
     */
    private function extractTextFromPdf($file)
    {
        // Basic PDF text extraction - you might want to use a more robust solution
        try {
            $content = file_get_contents($file->getRealPath());
            // Simple regex to extract readable text from PDF
            preg_match_all('/\((.*?)\)/', $content, $matches);
            return implode(' ', $matches[1] ?? []);
        } catch (\Exception $e) {
            Log::error('Error extracting text from PDF', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Extract text from DOC/DOCX file
     */
    private function extractTextFromDoc($file)
    {
        // Basic DOC text extraction - you might want to use PHPWord or similar
        try {
            $content = file_get_contents($file->getRealPath());
            // Simple approach - extract readable text
            $content = preg_replace('/[^\x20-\x7E]/', ' ', $content);
            return $content;
        } catch (\Exception $e) {
            Log::error('Error extracting text from DOC', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Get system instruction for quiz generation
     */
    private function getSystemInstruction($quizType, $difficulty, $numQuestions)
    {
        $questionTypeInstructions = [
            'multiple_choice' => "Generate multiple-choice questions with 4 options (A, B, C, D) where only one answer is correct. Include plausible distractors.",
            'true_false' => "Generate true/false questions based on factual statements from the content. Use options A (True) and B (False).",
            'short_answer' => "Generate short-answer questions that require brief explanations (1-2 sentences). Focus on key concepts and definitions.",
            'essay' => "Generate essay questions that require detailed explanations and analysis. Questions should encourage critical thinking."
        ];

        $typeInstruction = $questionTypeInstructions[$quizType] ?? $questionTypeInstructions['multiple_choice'];

        return "You are an expert educational content creator. Generate exactly {$numQuestions} {$quizType} questions at {$difficulty} difficulty level based on the provided content.

{$typeInstruction}

IMPORTANT FORMATTING REQUIREMENTS:
- Return ONLY a valid JSON array
- Each question must have this exact structure:
{
  \"question\": \"Question text here\",
  \"type\": \"{$quizType}\",
  \"options\": {
    \"A\": \"Option A text\",
    \"B\": \"Option B text\", 
    \"C\": \"Option C text\",
    \"D\": \"Option D text\"
  },
  \"correct_answer\": \"A\",
  \"explanation\": \"Brief explanation of why this answer is correct\"
}

For true_false questions, use options: {\"A\": \"True\", \"B\": \"False\"}
For short_answer and essay questions, omit the options field and set correct_answer to a sample answer.

QUALITY GUIDELINES:
- Extract key terms, concepts, definitions, dates, acronyms, processes, and relationships
- Questions should test understanding, not just memorization
- Each question should be clear and unambiguous
- Do NOT invent facts—only quiz on content present in the provided text
- Use clear, concise language appropriate for the educational level

Generate exactly {$numQuestions} questions. Return only the JSON array.";
    }

    /**
     * Build the generation prompt
     */
    private function buildPrompt($content, $topic, $numQuestions, $difficulty, $quizType)
    {
        return "Based on the following content about {$topic}, generate exactly {$numQuestions} {$quizType} questions at {$difficulty} difficulty level:

CONTENT:
{$content}

Generate the questions now as a JSON array:";
    }

    /**
     * Parse Gemini API response and extract questions
     */
    private function parseGeminiResponse($data)
    {
        try {
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error('Invalid Gemini response structure', ['data' => $data]);
                return null;
            }

            $responseText = $data['candidates'][0]['content']['parts'][0]['text'];
            
            // Clean the response text - remove code block markers if present
            $responseText = preg_replace('/```json\s*/', '', $responseText);
            $responseText = preg_replace('/```\s*$/', '', $responseText);
            $responseText = trim($responseText);

            // Try to decode JSON
            $questions = json_decode($responseText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON decode error in Gemini response', [
                    'error' => json_last_error_msg(),
                    'response' => $responseText
                ]);
                return null;
            }

            // Validate and transform questions to our format
            return $this->transformQuestions($questions);

        } catch (\Exception $e) {
            Log::error('Error parsing Gemini response', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Transform Gemini questions to our database format
     */
    private function transformQuestions($questions)
    {
        $transformedQuestions = [];

        foreach ($questions as $question) {
            if (!isset($question['question'])) {
                Log::warning('Invalid question format from Gemini - missing question text', ['question' => $question]);
                continue;
            }

            // Handle different question types
            $questionType = $question['type'] ?? 'multiple_choice';
            $options = $question['options'] ?? [];
            $correctAnswer = $question['correct_answer'] ?? '';
            
            // Convert options to the format expected by our database
            if ($questionType === 'multiple_choice' || $questionType === 'true_false') {
                $optionsArray = [];
                foreach ($options as $key => $value) {
                    $optionsArray[strtoupper($key)] = $value;
                }
                $options = $optionsArray;
            } else {
                // For short_answer and essay, options should be empty
                $options = [];
            }

            $transformedQuestion = [
                'question' => $question['question'],
                'type' => $questionType,
                'options' => $options,
                'correct_answer' => strtoupper($correctAnswer),
                'explanation' => $question['explanation'] ?? '',
                'points' => 1 // Default points
            ];

            $transformedQuestions[] = $transformedQuestion;
        }

        Log::info('Transformed Gemini questions', [
            'original_count' => count($questions),
            'transformed_count' => count($transformedQuestions),
            'questions' => $transformedQuestions
        ]);

        return $transformedQuestions;
    }

    /**
     * Generate quiz questions for admin use
     */
    public function generateAdminQuiz($content, $options = [])
    {
        $defaultOptions = [
            'num_questions' => 10,
            'difficulty' => 'Medium',
            'quiz_type' => 'multiple_choice',
            'topic' => 'Educational Content'
        ];

        $options = array_merge($defaultOptions, $options);
        
        return $this->generateQuizFromText($content, $options);
    }

    /**
     * Generate quiz from module content
     */
    public function generateQuizFromModule($moduleId, $options = [])
    {
        try {
            // Get module content
            $module = \App\Models\Module::find($moduleId);
            if (!$module) {
                return null;
            }

            // Extract content from module courses and content items
            $content = $this->extractModuleContent($module);
            
            if (empty($content)) {
                return null;
            }

            $options['topic'] = $module->title ?? 'Module Content';
            
            return $this->generateQuizFromText($content, $options);

        } catch (\Exception $e) {
            Log::error('Error generating quiz from module', [
                'module_id' => $moduleId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Extract content from module
     */
    private function extractModuleContent($module)
    {
        $content = '';

        // Add module title and description
        if ($module->title) {
            $content .= "Module: " . $module->title . "\n\n";
        }
        
        if ($module->description) {
            $content .= $module->description . "\n\n";
        }

        // Get content from courses
        foreach ($module->courses as $course) {
            if ($course->title) {
                $content .= "Course: " . $course->title . "\n";
            }
            
            if ($course->description) {
                $content .= $course->description . "\n";
            }

            // Get content items
            foreach ($course->contentItems as $contentItem) {
                if ($contentItem->title) {
                    $content .= "Topic: " . $contentItem->title . "\n";
                }
                
                if ($contentItem->content) {
                    $content .= $contentItem->content . "\n";
                }
                
                $content .= "\n";
            }
            
            $content .= "\n";
        }

        return $content;
    }
}
