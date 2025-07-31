<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use \Exception;

class GeminiQuizService
{
    /**
     * Google Generative AI API key and model configuration
     */
    protected $apiKey;
    protected $model = 'gemini-2.0-flash';
    // Google Gemini API endpoint
    protected $apiEndpoint = 'https://generativelanguage.googleapis.com/v1';

    /**
     * Constructor that initializes API key from environment
     */
    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        
        if (empty($this->apiKey)) {
            Log::error('Gemini API key is not configured');
        }
    }

    /**
     * Parse the uploaded document and extract key facts, terms, acronyms, etc.
     * @param string $documentPath
     * @return array
     */
    public function extractDocumentContent($documentPath)
    {
        try {
            $filePath = storage_path('app/' . $documentPath);
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $content = '';
            
            // Extract text based on file type
            switch (strtolower($fileExtension)) {
                case 'txt':
                    $content = file_get_contents($filePath);
                    break;
                case 'pdf':
                    // Requires the PDF parser library
                    // Install via: composer require smalot/pdfparser
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($filePath);
                    $content = $pdf->getText();
                    break;
                case 'docx':
                    // Requires the DOCX parser library
                    // Install via: composer require phpoffice/phpword
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                    $content = '';
                    $sections = $phpWord->getSections();
                    foreach ($sections as $section) {
                        $elements = $section->getElements();
                        foreach ($elements as $element) {
                            // Handle text runs inside text element
                            if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                $textRunElements = $element->getElements();
                                foreach ($textRunElements as $textRunElement) {
                                    if ($textRunElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                        $content .= $textRunElement->getText() . ' ';
                                    }
                                }
                            } 
                            // Handle direct text elements
                            elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                                $content .= $element->getText() . ' ';
                            }
                        }
                    }
                    break;
                default:
                    throw new Exception("Unsupported file type: {$fileExtension}");
            }
            
            return [
                'content' => $content,
                'file_type' => $fileExtension,
                'file_name' => basename($filePath)
            ];
        } catch (Exception $e) {
            Log::error("Error extracting document content: " . $e->getMessage());
            return [
                'content' => '',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate MCQs and True/False questions using Gemini AI
     * @param string|array $documentContent Either a single document content string or an array of document contents
     * @param int $minMcq
     * @param int $minTf
     * @return array
     */
    public function generateQuiz($documentContent, $minMcq = 8, $minTf = 6)
    {
        try {
            if (empty($this->apiKey)) {
                throw new Exception('Gemini API key is not configured');
            }
            
            // Prepare the system instruction with quiz generation guidelines
            $systemInstruction = $this->getQuizGenerationPrompt($minMcq, $minTf);
            
            // Handle multiple documents if an array is provided
            if (is_array($documentContent)) {
                // Combine documents with section headers
                $combinedContent = "";
                foreach ($documentContent as $index => $content) {
                    $docNum = $index + 1;
                    $combinedContent .= "=== DOCUMENT {$docNum} ===\n\n";
                    $combinedContent .= $content . "\n\n";
                }
            } else {
                $combinedContent = $documentContent;
            }
            
            Log::info("Generating quiz from " . (is_array($documentContent) ? count($documentContent) . " documents" : "1 document"));
            
            // Call Gemini API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("{$this->apiEndpoint}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $combinedContent]
                        ]
                    ]
                ],
                'systemInstruction' => [
                    'role' => 'system',
                    'parts' => [
                        ['text' => $systemInstruction]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.9,  // Higher temperature for more variation
                    'topK' => 50,
                    'topP' => 0.95,
                    'maxOutputTokens' => 8192,
                    'seed' => time() + rand(1, 1000),  // Use current timestamp + random for variation
                ]
            ]);
            
            if ($response->failed()) {
                Log::error('Gemini API request failed: ' . $response->body());
                throw new Exception('Failed to generate quiz: ' . $response->status());
            }
            
            $result = $response->json();
            
            // Parse the generated quiz content
            $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            if (empty($generatedText)) {
                throw new Exception('Empty response from Gemini API');
            }
            
            return $this->parseQuizContent($generatedText);
            
        } catch (Exception $e) {
            Log::error('Quiz generation error: ' . $e->getMessage());
            
            return [
                'mcqs' => [],
                'true_false' => [],
                'answer_key' => [],
                'error' => $e->getMessage(),
                'disclaimer' => 'Failed to generate quiz due to an error.',
            ];
        }
    }

    /**
     * Parse the quiz content from Gemini API response
     * @param string $content
     * @return array
     */
    protected function parseQuizContent($content)
    {
        $mcqs = [];
        $trueFalse = [];
        $answerKey = [];
        $disclaimer = '';
        
        try {
            // Split content by sections
            if (preg_match('/\*\*I\.\s+Multiple-Choice\s+Questions\*\*(.*?)(?:\*\*II\.\s+True\/False\s+Statements\*\*)/s', $content, $mcqMatch)) {
                $mcqContent = trim($mcqMatch[1]);
                
                // Parse MCQs
                preg_match_all('/(\d+)\.\s+(.*?)\s+A\.\s+(.*?)\s+B\.\s+(.*?)\s+C\.\s+(.*?)\s+D\.\s+(.*?)(?=\d+\.|$)/s', $mcqContent, $matches, PREG_SET_ORDER);
                
                foreach ($matches as $match) {
                    $questionNumber = trim($match[1]);
                    $questionText = trim($match[2]);
                    
                    $mcqs[] = [
                        'number' => $questionNumber,
                        'text' => $questionText,
                        'options' => [
                            'A' => trim($match[3]),
                            'B' => trim($match[4]),
                            'C' => trim($match[5]),
                            'D' => trim($match[6])
                        ]
                    ];
                }
            }
            
            // Extract True/False statements
            if (preg_match('/\*\*II\.\s+True\/False\s+Statements\*\*(.*?)(?:\*\*III\.\s+Answer\s+Key\*\*)/s', $content, $tfMatch)) {
                $tfContent = trim($tfMatch[1]);
                
                preg_match_all('/(\d+)\.\s+(.*?)(?=\d+\.|$)/s', $tfContent, $matches, PREG_SET_ORDER);
                
                foreach ($matches as $match) {
                    $questionNumber = trim($match[1]);
                    $statementText = trim($match[2]);
                    
                    $trueFalse[] = [
                        'number' => $questionNumber,
                        'statement' => $statementText
                    ];
                }
            }
            
            // Extract Answer Key
            if (preg_match('/\*\*III\.\s+Answer\s+Key\*\*(.*)/s', $content, $keyMatch)) {
                $keyContent = trim($keyMatch[1]);
                
                preg_match_all('/(\d+)\.\s+([A-D]|True|False|[^—]+?)(?:\s+—\s+(.*?))?(?=\d+\.|$)/s', $keyContent, $matches, PREG_SET_ORDER);
                
                foreach ($matches as $match) {
                    $questionNumber = trim($match[1]);
                    $answer = trim($match[2]);
                    $explanation = isset($match[3]) ? trim($match[3]) : '';
                    
                    $answerKey[] = [
                        'number' => $questionNumber,
                        'answer' => $answer,
                        'explanation' => $explanation
                    ];
                }
            }
            
            // Check for disclaimer
            if (preg_match('/Only\s+(\d+)\s+MCQs\s+and\s+(\d+)\s+True\/False\s+could\s+be\s+generated.*?/i', $content, $disclaimerMatch)) {
                $disclaimer = $disclaimerMatch[0];
            }
            
            return [
                'mcqs' => $mcqs,
                'true_false' => $trueFalse,
                'answer_key' => $answerKey,
                'disclaimer' => $disclaimer,
                'raw_content' => $content // Store raw content for debugging
            ];
            
        } catch (Exception $e) {
            Log::error('Error parsing quiz content: ' . $e->getMessage());
            
            return [
                'mcqs' => $mcqs,
                'true_false' => $trueFalse,
                'answer_key' => $answerKey,
                'disclaimer' => $disclaimer,
                'error' => 'Error parsing quiz content: ' . $e->getMessage(),
                'raw_content' => $content
            ];
        }
    }
    
    /**
     * Get the quiz generation prompt with detailed instructions
     * @param int $minMcq
     * @param int $minTf
     * @return string
     */
    protected function getQuizGenerationPrompt($minMcq = 8, $minTf = 6)
    {
        return <<<EOT
System: You are an expert quiz maker. Use only the provided source material to create questions. Do not guess.

Task:
1. Generate {$minMcq} multiple-choice questions. Each should:
   - Have a clear stem about a distinct fact from the document.
   - Include 4 options labeled A–D (one correct, three plausible distractors).
   - Randomize the order of options.
2. Generate {$minTf} true/false statements based strictly on the content. 
3. Provide an answer key. For any false statement or incorrect option, include a 1-sentence justification citing the source.

Format:
**I. Multiple-Choice Questions**
1. [Clear question stem based on document content]
   A. [Option]
   B. [Option]  
   C. [Option]
   D. [Option]
2. [Next question]
   A. [Option]
   B. [Option]
   C. [Option] 
   D. [Option]

**II. True/False Statements**
" . ($minMcq + 1) . ". [Clear statement based on document content]
" . ($minMcq + 2) . ". [Clear statement based on document content]

**III. Answer Key**
1. [Correct letter] — [Brief explanation if needed]
2. [Correct letter] — [Brief explanation if needed]
" . ($minMcq + 1) . ". True/False — [Explanation citing source]
" . ($minMcq + 2) . ". True/False — [Explanation citing source]

Requirements:
- Use ONLY information explicitly stated in the provided document
- Make questions specific and factual, not vague or generic
- Ensure each question tests a different concept or fact
- Create plausible distractors based on related content from the document
- Keep language clear and professional
EOT;
    }

    /**
     * Format the quiz output for display or export
     * @param array $quizData
     * @return string
     */
    public function formatQuizOutput($quizData)
    {
        $output = '';
        
        // Format MCQs
        if (!empty($quizData['mcqs'])) {
            $output .= "# Multiple-Choice Questions\n\n";
            
            foreach ($quizData['mcqs'] as $mcq) {
                $output .= "{$mcq['number']}. {$mcq['text']}\n";
                $output .= "   A. {$mcq['options']['A']}\n";
                $output .= "   B. {$mcq['options']['B']}\n";
                $output .= "   C. {$mcq['options']['C']}\n";
                $output .= "   D. {$mcq['options']['D']}\n\n";
            }
        }
        
        // Format True/False statements
        if (!empty($quizData['true_false'])) {
            $output .= "# True/False Statements\n\n";
            
            foreach ($quizData['true_false'] as $tf) {
                $output .= "{$tf['number']}. {$tf['statement']}\n\n";
            }
        }
        
        // Format Answer Key
        if (!empty($quizData['answer_key'])) {
            $output .= "# Answer Key\n\n";
            
            foreach ($quizData['answer_key'] as $key) {
                $output .= "{$key['number']}. {$key['answer']}";
                if (!empty($key['explanation'])) {
                    $output .= " — {$key['explanation']}";
                }
                $output .= "\n";
            }
        }
        
        // Add disclaimer if present
        if (!empty($quizData['disclaimer'])) {
            $output .= "\n# Note\n{$quizData['disclaimer']}\n";
        }
        
        return $output;
    }

    /**
     * Convert quiz data to JSON for storage in database
     * @param array $quizData
     * @return array
     */
    public function convertToQuizQuestions($quizData)
    {
        $questions = [];
        
        // Convert MCQs
        foreach ($quizData['mcqs'] as $mcq) {
            // Find the correct answer from answer key
            $correctOption = 'A'; // Default
            foreach ($quizData['answer_key'] as $key) {
                if ($key['number'] == $mcq['number']) {
                    $correctOption = $key['answer'];
                    break;
                }
            }
            
            $questions[] = [
                'question_text' => $mcq['text'],
                'question_type' => 'multiple_choice',
                'options' => json_encode($mcq['options']),
                'correct_answer' => $correctOption,
                'explanation' => $this->getExplanationForQuestion($mcq['number'], $quizData['answer_key']),
                'question_source' => 'generated',
                'points' => 1,
                'is_active' => true,
            ];
        }
        
        // Convert True/False questions
        foreach ($quizData['true_false'] as $tf) {
            // Find the correct answer from answer key
            $correctAnswer = 'True'; // Default
            foreach ($quizData['answer_key'] as $key) {
                if ($key['number'] == $tf['number']) {
                    $correctAnswer = $key['answer'];
                    break;
                }
            }
            
            $questions[] = [
                'question_text' => $tf['statement'],
                'question_type' => 'true_false',
                'options' => json_encode(['True', 'False']),
                'correct_answer' => $correctAnswer,
                'explanation' => $this->getExplanationForQuestion($tf['number'], $quizData['answer_key']),
                'question_source' => 'generated',
                'points' => 1,
                'is_active' => true,
            ];
        }
        
        return $questions;
    }
    
    /**
     * Get explanation for a question from the answer key
     * @param string $questionNumber
     * @param array $answerKey
     * @return string
     */
    private function getExplanationForQuestion($questionNumber, $answerKey)
    {
        foreach ($answerKey as $key) {
            if ($key['number'] == $questionNumber && !empty($key['explanation'])) {
                return $key['explanation'];
            }
        }
        return '';
    }
    
    /**
     * Process multiple PDF documents and generate a combined quiz
     * @param array $documentPaths Array of document paths to process
     * @param int $minMcq Minimum number of MCQs to generate
     * @param int $minTf Minimum number of True/False questions to generate
     * @return array
     */
    public function generateQuizFromMultipleDocuments($documentPaths, $minMcq = 10, $minTf = 8)
    {
        try {
            // Extract content from each document
            $documentContents = [];
            $documentTitles = [];
            
            foreach ($documentPaths as $path) {
                $result = $this->extractDocumentContent($path);
                if (!empty($result['content'])) {
                    $documentContents[] = $result['content'];
                    $documentTitles[] = $result['file_name'];
                } else {
                    Log::warning("Could not extract content from document: $path - " . ($result['error'] ?? 'Unknown error'));
                }
            }
            
            if (empty($documentContents)) {
                throw new Exception('Failed to extract content from any of the provided documents');
            }
            
            // Generate quiz from all document contents combined
            $quizData = $this->generateQuiz($documentContents, $minMcq, $minTf);
            
            // Add document sources metadata
            $quizData['source_documents'] = $documentTitles;
            $quizData['document_count'] = count($documentContents);
            
            return $quizData;
        } catch (Exception $e) {
            Log::error('Error generating quiz from multiple documents: ' . $e->getMessage());
            return [
                'mcqs' => [],
                'true_false' => [],
                'answer_key' => [],
                'error' => $e->getMessage(),
                'disclaimer' => 'Failed to generate quiz from multiple documents due to an error.'
            ];
        }
    }
}
