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
    protected $model = 'gemini-1.5-flash';
    // Google Gemini API endpoint
    protected $apiEndpoint = 'https://generativelanguage.googleapis.com/v1';

    /**
     * Safe JSON encoding that handles UTF-8 issues
     */
    protected function safeJsonEncode($data)
    {
        // Clean any string values in the data recursively
        $cleanData = $this->cleanUtf8Recursive($data);
        
        $json = json_encode($cleanData);
        if ($json === false) {
            return 'JSON encoding failed: ' . json_last_error_msg();
        }
        return $json;
    }

    /**
     * Recursively clean UTF-8 issues in data structures
     */
    protected function cleanUtf8Recursive($data)
    {
        if (is_string($data)) {
            $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            $data = iconv('UTF-8', 'UTF-8//IGNORE', $data);
            return preg_replace('/[^\x20-\x7E\xA0-\xFF]/u', ' ', $data);
        } elseif (is_array($data)) {
            return array_map([$this, 'cleanUtf8Recursive'], $data);
        } elseif (is_object($data)) {
            foreach ($data as $key => $value) {
                $data->$key = $this->cleanUtf8Recursive($value);
            }
            return $data;
        }
        return $data;
    }

    /**
     * Enhanced PDF content extraction with multiple methods including OCR
     */
    protected function extractPdfContent($filePath)
    {
        $content = '';
        $methods = [];
        
        Log::info("Extracting PDF content from: " . basename($filePath));
        
        // Method 1: Smalot PDF Parser (traditional text extraction)
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $smalotContent = $pdf->getText();
            
            if (strlen(trim($smalotContent)) > 100) {
                $content = $smalotContent;
                $methods[] = 'Smalot PDF Parser';
                Log::info("Smalot PDF Parser extracted " . strlen($content) . " characters");
            }
        } catch (Exception $e) {
            Log::warning("Smalot PDF Parser failed: " . $e->getMessage());
        }
        
        // Method 2: Spatie PDF-to-Text (uses pdftotext)
        if (strlen(trim($content)) < 100) {
            try {
                $spatieContent = \Spatie\PdfToText\Pdf::getText($filePath);
                
                if (strlen(trim($spatieContent)) > 100) {
                    $content = $spatieContent;
                    $methods[] = 'Spatie PDF-to-Text';
                    Log::info("Spatie PDF-to-Text extracted " . strlen($content) . " characters");
                }
            } catch (Exception $e) {
                Log::warning("Spatie PDF-to-Text failed: " . $e->getMessage());
            }
        }
        
        // Method 3: Tesseract OCR (for scanned PDFs/images)
        if (strlen(trim($content)) < 100) {
            try {
                // Convert PDF to images first, then use OCR
                $ocrContent = $this->extractWithOCR($filePath);
                
                if (strlen(trim($ocrContent)) > 100) {
                    $content = $ocrContent;
                    $methods[] = 'Tesseract OCR';
                    Log::info("Tesseract OCR extracted " . strlen($content) . " characters");
                }
            } catch (Exception $e) {
                Log::warning("Tesseract OCR failed: " . $e->getMessage());
            }
        }
        
        // Method 4: Fallback - try reading as plain text
        if (strlen(trim($content)) < 100) {
            try {
                $rawContent = file_get_contents($filePath);
                // Look for readable text patterns
                if (preg_match_all('/[a-zA-Z0-9\s\.\,\;\:\!\?]{10,}/', $rawContent, $matches)) {
                    $fallbackContent = implode(' ', $matches[0]);
                    if (strlen(trim($fallbackContent)) > 100) {
                        $content = $fallbackContent;
                        $methods[] = 'Raw text extraction';
                        Log::info("Raw text extraction found " . strlen($content) . " characters");
                    }
                }
            } catch (Exception $e) {
                Log::warning("Raw text extraction failed: " . $e->getMessage());
            }
        }
        
        Log::info("PDF extraction completed using methods: " . implode(', ', $methods));
        
        return $content;
    }
    
    /**
     * Extract text using Tesseract OCR
     */
    protected function extractWithOCR($filePath)
    {
        try {
            // For PDFs, we need to convert to images first
            // This is a simplified approach - in production you'd use ImageMagick or similar
            
            // Try direct OCR on the PDF (some OCR tools can handle PDFs directly)
            $ocr = new \thiagoalessio\TesseractOCR\TesseractOCR($filePath);
            $ocrText = $ocr->lang('eng')
                          ->configFile('pdf')
                          ->run();
            
            return $ocrText;
            
        } catch (Exception $e) {
            Log::warning("OCR extraction failed: " . $e->getMessage());
            return '';
        }
    }

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
                    // Enhanced PDF processing with multiple extraction methods
                    $content = $this->extractPdfContent($filePath);
                    break;
                case 'csv':
                    // Handle CSV files
                    $csvContent = array_map('str_getcsv', file($filePath));
                    $content = '';
                    foreach ($csvContent as $row) {
                        $content .= implode(' ', $row) . "\n";
                    }
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
            
            // Validate extracted content
            if (empty(trim($content))) {
                throw new Exception("No content could be extracted from the document");
            }
            
            // Fix UTF-8 encoding issues
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
            $content = iconv('UTF-8', 'UTF-8//IGNORE', $content);
            
            // Remove non-printable characters except basic punctuation and spaces
            $content = preg_replace('/[^\x20-\x7E\xA0-\xFF]/u', ' ', $content);
            
            // Trim excessive whitespace and normalize line endings
            $content = preg_replace('/\s+/', ' ', trim($content));
            
            // Check minimum content length (e.g., 100 characters)
            if (strlen($content) < 100) {
                throw new Exception("Extracted content is too short to generate meaningful questions");
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
     * Generate MCQs and True/False questions using Gemini AI with structured output and retry mechanism
     * @param string|array $documentContent Either a single document content string or an array of document contents
     * @param int $minMcq
     * @param int $minTf
     * @return array
     */
    public function generateQuiz($documentContent, $minMcq = 8, $minTf = 6)
    {
        $maxRetries = 3;
        $attempt = 1;
        
        while ($attempt <= $maxRetries) {
            try {
                Log::info("Quiz generation attempt {$attempt} of {$maxRetries}");
                $result = $this->attemptQuizGeneration($documentContent, $minMcq, $minTf, $attempt);
                Log::info("Attempt {$attempt} results: " . $this->safeJsonEncode([
                    'mcq_count' => count($result['mcqs'] ?? []),
                    'tf_count' => count($result['true_false'] ?? []),
                    'has_error' => !empty($result['error']),
                    'error_message' => $result['error'] ?? null
                ]));
                // Accept partial results if at least 1 MCQ or 1 T/F is present
                if (!empty($result['mcqs']) || !empty($result['true_false'])) {
                    if (count($result['mcqs']) < $minMcq || count($result['true_false']) < $minTf) {
                        $result['disclaimer'] = 'Only ' . count($result['mcqs']) . ' MCQs and ' . count($result['true_false']) . ' True/False could be generated from the document.';
                    }
                    Log::info("Returning partial quiz on attempt {$attempt}");
                    return $result;
                }
                if (!empty($result['error'])) {
                    Log::warning("Attempt {$attempt} returned error: " . $result['error']);
                } else {
                    Log::warning("Attempt {$attempt} returned no questions");
                }
                $attempt++;
            } catch (Exception $e) {
                Log::error("Attempt {$attempt} error: " . $e->getMessage());
                $attempt++;
            }
        }
        Log::error("All quiz generation attempts failed");
        return [
            'mcqs' => [],
            'true_false' => [],
            'answer_key' => [],
            'error' => 'Failed to generate quality quiz after multiple attempts.',
            'disclaimer' => 'Quiz generation failed due to quality issues.',
        ];
    }
    
    /**
     * Single attempt at quiz generation
     * @param string|array $documentContent
     * @param int $minMcq
     * @param int $minTf
     * @param int $attempt
     * @return array
     */
    protected function attemptQuizGeneration($documentContent, $minMcq, $minTf, $attempt)
    {
        try {
            // Increase execution time limit for this request
            $timeoutLimit = 120; // Default 2 minutes
            ini_set('max_execution_time', $timeoutLimit);
            set_time_limit($timeoutLimit);
            
            if (empty($this->apiKey)) {
                throw new Exception('Gemini API key is not configured');
            }
            
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
            
            Log::info("Generating quiz from " . (is_array($documentContent) ? count($documentContent) . " documents" : "1 document") . " - Attempt {$attempt}");
            
            // Validate document content before generating quiz
            if (strlen(trim($combinedContent)) < 500) {
                Log::error("Document content too short: " . strlen(trim($combinedContent)) . " characters. Sample: " . substr($combinedContent, 0, 200));
                throw new Exception('Document content is too short to generate meaningful questions. Minimum 500 characters required.');
            }
            
            // Limit content size to avoid timeouts (around 25k characters for larger documents)
            $contentLength = strlen(trim($combinedContent));
            if ($contentLength > 25000) {
                Log::info("Document content too large, trimming from {$contentLength} to 25000 characters");
                $combinedContent = substr(trim($combinedContent), 0, 25000);
                Log::info("Content trimmed, new length: " . strlen($combinedContent));
            }
            
            Log::info("Document content validation passed: " . strlen(trim($combinedContent)) . " characters available for quiz generation");
            
            // Define the structured schema for quiz generation with enhanced validation
            $responseSchema = [
                "type" => "object",
                "properties" => [
                    "multiple_choice_questions" => [
                        "type" => "array",
                        "minItems" => $minMcq,
                        "items" => [
                            "type" => "object",
                            "properties" => [
                                "question_number" => ["type" => "integer"],
                                "question_text" => [
                                    "type" => "string",
                                    "minLength" => 30,
                                    "pattern" => "^(?!.*What is \\w+ What\\?)(?!.*What is \\w{1,5}\\?).*\\?$",
                                    "description" => "Complete, specific question ending with '?' and at least 30 characters. Must not be vague like 'What is X What?' or 'What is [short word]?'"
                                ],
                                "options" => [
                                    "type" => "object",
                                    "properties" => [
                                        "A" => [
                                            "type" => "string",
                                            "minLength" => 15,
                                            "pattern" => "^(?!^A \\w+$)(?!^An? \\w+$)(?!^\\w+ and$).{15,}$",
                                            "description" => "Complete answer option of at least 15 characters, not a fragment like 'A software' or 'An authentication'"
                                        ],
                                        "B" => [
                                            "type" => "string",
                                            "minLength" => 15,
                                            "pattern" => "^(?!^A \\w+$)(?!^An? \\w+$)(?!^\\w+ and$).{15,}$",
                                            "description" => "Complete answer option of at least 15 characters, not a fragment like 'A software' or 'An authentication'"
                                        ],
                                        "C" => [
                                            "type" => "string",
                                            "minLength" => 15,
                                            "pattern" => "^(?!^A \\w+$)(?!^An? \\w+$)(?!^\\w+ and$).{15,}$",
                                            "description" => "Complete answer option of at least 15 characters, not a fragment like 'A software' or 'An authentication'"
                                        ],
                                        "D" => [
                                            "type" => "string",
                                            "minLength" => 15,
                                            "pattern" => "^(?!^A \\w+$)(?!^An? \\w+$)(?!^\\w+ and$).{15,}$",
                                            "description" => "Complete answer option of at least 15 characters, not a fragment like 'A software' or 'An authentication'"
                                        ]
                                    ],
                                    "required" => ["A", "B", "C", "D"],
                                    "propertyOrdering" => ["A", "B", "C", "D"]
                                ],
                                "correct_answer" => [
                                    "type" => "string",
                                    "enum" => ["A", "B", "C", "D"]
                                ],
                                "explanation" => [
                                    "type" => "string",
                                    "minLength" => 20,
                                    "description" => "Clear explanation referencing specific document content"
                                ]
                            ],
                            "required" => ["question_number", "question_text", "options", "correct_answer", "explanation"],
                            "propertyOrdering" => ["question_number", "question_text", "options", "correct_answer", "explanation"]
                        ]
                    ],
                    "true_false_questions" => [
                        "type" => "array",
                        "minItems" => $minTf,
                        "items" => [
                            "type" => "object",
                            "properties" => [
                                "question_number" => ["type" => "integer"],
                                "statement" => [
                                    "type" => "string",
                                    "minLength" => 15,
                                    "description" => "Clear, specific statement that can be definitively true or false based on document content"
                                ],
                                "correct_answer" => [
                                    "type" => "string",
                                    "enum" => ["True", "False"]
                                ],
                                "explanation" => [
                                    "type" => "string",
                                    "minLength" => 20,
                                    "description" => "Explanation citing specific document content to justify the answer"
                                ]
                            ],
                            "required" => ["question_number", "statement", "correct_answer", "explanation"],
                            "propertyOrdering" => ["question_number", "statement", "correct_answer", "explanation"]
                        ]
                    ]
                ],
                "required" => ["multiple_choice_questions", "true_false_questions"],
                "propertyOrdering" => ["multiple_choice_questions", "true_false_questions"]
            ];
            
            // Validate document content before generating quiz
            if (strlen(trim($combinedContent)) < 500) {
                throw new Exception('Document content is too short to generate meaningful questions. Minimum 500 characters required.');
            }
            
            // Create the enhanced prompt for text-based generation (more compatible)
            $prompt = "SYSTEM / TASK INSTRUCTION:\n\n";
            $prompt .= "You are an expert educational content generator. You are given a single uploaded lesson file (a PDF) from a professor. Your task is to read and fully understand the content of that PDF, then generate a high-quality quiz based solely on that material. Do NOT hallucinate or invent facts; every question, correct answer, distractor, and explanation must be directly supported by or safely inferred from the PDF. If something cannot be determined confidently from the text, say so instead of guessing.\n\n";
            
            $prompt .= "GOALS:\n";
            $prompt .= "1. Extract key concepts, definitions, distinctions, processes, laws, terminology, and common confusions from the document.\n";
            $prompt .= "2. Generate quiz items that are **specific**, **clear**, and **test understanding**, not vague or generic.\n";
            $prompt .= "3. Provide an answer key with justifications for any subtle or false items.\n\n";
            
            $prompt .= "REQUIREMENTS:\n\n";
            
            $prompt .= "A. QUESTION FORMATS (only these unless otherwise requested):\n";
            $prompt .= "   1. **Multiple-Choice Questions (MCQ)**\n";
            $prompt .= "      - At least {$minMcq} MCQs.\n";
            $prompt .= "      - Each must have:\n";
            $prompt .= "         * A precise, single-focus stem (e.g., “Which law states that pressure and volume are inversely proportional at constant temperature?” not “What does the document state about this topic?”).\n";
            $prompt .= "         * Four options labeled A–D.\n";
            $prompt .= "         * Exactly one correct answer.\n";
            $prompt .= "         * Three **plausible** distractors drawn from related concepts or common misconceptions present in the lesson.\n";
            $prompt .= "         * Randomized order of options.\n";
            $prompt .= "      - Vary difficulty: include straightforward recall, application/distinction, and one nuanced detail question.\n\n";
            
            $prompt .= "   2. **True/False Statements**\n";
            $prompt .= "      - At least {$minTf} statements.\n";
            $prompt .= "      - Each is a declarative factual sentence grounded in the PDF.\n";
            $prompt .= "      - Clearly mark whether it is True or False.\n";
            $prompt .= "      - False statements should mirror realistic misunderstandings (e.g., swapping roles or negating a precise condition), not arbitrary falsehoods.\n\n";
            
            $prompt .= "B. GROUNDING & TRANSPARENCY\n";
            $prompt .= "   - For each question, include (internally or in metadata) the source sentence or snippet from the PDF that justifies it.\n";
            $prompt .= "   - If a question relies on an implicit but safe inference, annotate it as “Inference: …” in the explanation.\n";
            $prompt .= "   - Do not produce vague stems like “What is What?” or “According to the document, which statement is correct?” without specifying which concept is being tested.\n\n";
            
            $prompt .= "C. DISTRACTOR CONSTRUCTION (for MCQs)\n";
            $prompt .= "   - Distractors must be:\n";
            $prompt .= "     * Related in domain (e.g., confusing similar standards, swapping “security in the cloud” vs “security of the cloud,” mixing up definitions of terms that are explained nearby).\n";
            $prompt .= "     * Grammatically and structurally parallel to the correct answer.\n";
            $prompt .= "     * Not obviously wrong or irrelevant.\n\n";
            
            $prompt .= "D. OUTPUT STRUCTURE (Markdown-style)\n";
            $prompt .= "   **I. Multiple-Choice Questions**\n";
            $prompt .= "   1. [Stem]?\n";
            $prompt .= "      A. Option\n";
            $prompt .= "      B. Option\n";
            $prompt .= "      C. Option\n";
            $prompt .= "      D. Option\n";
            $prompt .= "   2. …\n\n";
            
            $prompt .= "   **II. True/False Statements**\n";
            $prompt .= "   " . ($minMcq + 1) . ". [Statement]. (True/False)\n";
            $prompt .= "   " . ($minMcq + 2) . ". …\n\n";
            
            $prompt .= "   **III. Answer Key**\n";
            $prompt .= "   1. [Letter]\n";
            $prompt .= "   2. [Letter]\n";
            $prompt .= "   …\n";
            $prompt .= "   " . ($minMcq + 1) . ". True/False — [If False or subtle, include a one-sentence explanation or citation]\n";
            $prompt .= "   " . ($minMcq + 2) . ". …\n\n";
            
            $prompt .= "E. QUALITY CONTROLS\n";
            $prompt .= "   - Reject candidate questions that:\n";
            $prompt .= "     * Are too vague to be answered without ambiguity.\n";
            $prompt .= "     * Have multiple plausible “correct” answers.\n";
            $prompt .= "     * Use filler language or ask about “the document” without tying to a concrete fact.\n";
            $prompt .= "   - Prefer coverage: don’t over-focus on one paragraph; sample across distinct major topics in the PDF.\n\n";
            
            $prompt .= "F. EXAMPLES (for clarity):\n\n";
            
            $prompt .= "   **Bad / Vague:**\n";
            $prompt .= "   “According to the document, which statement is correct?”\n";
            $prompt .= "     A. Something generic\n";
            $prompt .= "     B. A partially related phrase\n";
            $prompt .= "     C. Unclear concept\n";
            $prompt .= "     D. Irrelevant option\n";
            $prompt .= "   *Problem:* No specific concept being tested; options are unfocused.\n\n";
            
            $prompt .= "   **Improved:**\n";
            $prompt .= "   “Which law states that pressure and volume are inversely proportional when temperature is constant?”\n";
            $prompt .= "     A. Charles’s Law\n";
            $prompt .= "     B. Boyle’s Law\n";
            $prompt .= "     C. Zeroth Law\n";
            $prompt .= "     D. First Law\n";
            $prompt .= "   *Answer:* B — Clear stem, one correct choice, distractors are commonly confused gas laws.\n\n";
            
            $prompt .= "   **True/False example:**\n";
            $prompt .= "   “Boyle’s Law says that pressure and volume are directly proportional at constant temperature.” (False) — Explanation: They are inversely proportional. Source: [include brief paraphrase or reference].\n\n";
            
            $prompt .= "INSTRUCTIONS FOR EACH GENERATION RUN:\n";
            $prompt .= "1. Retrieve the most relevant passages from the uploaded PDF (e.g., definitions, laws, comparisons).\n";
            $prompt .= "2. From those, generate the required number of MCQs and True/False statements, following all rules above.\n";
            $prompt .= "3. Provide the answer key with correct labels and explanations for any non-obvious or false items, including the source snippet or a tight paraphrase.\n\n";
            
            $prompt .= "DISCLAIMER IF LIMITED:\n";
            $prompt .= "If the PDF lacks enough distinct factual units to meet the target counts ({$minMcq} MCQs + {$minTf} T/F), generate as many high-quality items as possible and prepend a note:\n";
            $prompt .= "“Generated X MCQs and Y True/False statements; source material did not contain enough distinct facts to safely produce more without redundancy.”\n\n";
            
            $prompt .= "END OF INSTRUCTION.\n\n";
            
            $prompt .= "DOCUMENT CONTENT TO ANALYZE:\n" . $combinedContent;
            
            // Log API call details (without API key)
            Log::info("Calling Gemini API with model: {$this->model}");
            
            // Call Gemini API with simplified request for better compatibility
            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.5 + ($attempt * 0.1),
                    'topK' => 30 + ($attempt * 10),
                    'topP' => 0.7 + ($attempt * 0.1),
                    'maxOutputTokens' => 4096 // Reduced from 8192 to make responses faster
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_NONE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_NONE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                        'threshold' => 'BLOCK_NONE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                        'threshold' => 'BLOCK_NONE'
                    ]
                ]
            ];
            
            // Set timeout based on config or default
            $apiTimeout = 90; // Default 90 seconds
            $response = Http::timeout($apiTimeout)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->apiEndpoint}/models/{$this->model}:generateContent?key={$this->apiKey}", $payload);
            
            if ($response->failed()) {
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);
                throw new Exception('Failed to generate quiz: ' . $response->status() . ' - ' . $response->body());
            }
            
            // Additional check for timeout or connection issues
            if (empty($response->body())) {
                Log::error('Gemini API request returned empty response (possible timeout)');
                throw new Exception('API request timed out or returned empty response');
            }
            
            $result = $response->json();
            
            // Parse the structured response
            // Handle potential different response formats
            $generatedText = '';
            
            // Try to get text from candidates array (standard structure)
            if (isset($result['candidates']) && is_array($result['candidates'])) {
                if (isset($result['candidates'][0]['content']['parts'])) {
                    foreach ($result['candidates'][0]['content']['parts'] as $part) {
                        if (isset($part['text'])) {
                            $generatedText .= $part['text'];
                        }
                    }
                }
            }
            
            // Alternative path - some models might return a different structure
            if (empty($generatedText) && isset($result['text'])) {
                $generatedText = $result['text'];
            }
            
            // Final fallback for other possible structures
            if (empty($generatedText) && isset($result['content'])) {
                if (is_string($result['content'])) {
                    $generatedText = $result['content'];
                } elseif (isset($result['content']['text'])) {
                    $generatedText = $result['content']['text'];
                }
            }
            
            // Log the structure to help debug future issues
            Log::info('Gemini API response structure: ' . $this->safeJsonEncode(array_keys($result)));
            
            if (empty($generatedText)) {
                Log::error('Empty response from Gemini API. Full response: ' . $this->safeJsonEncode($result));
                throw new Exception('Empty response from Gemini API');
            }
            
            // Clean UTF-8 in the API response text
            $generatedText = mb_convert_encoding($generatedText, 'UTF-8', 'UTF-8');
            $generatedText = iconv('UTF-8', 'UTF-8//IGNORE', $generatedText);
            $generatedText = preg_replace('/[^\x20-\x7E\xA0-\xFF]/u', ' ', $generatedText);
            
            // Log the actual response for debugging
            Log::info('Gemini API Generated Text (first 500 chars): ' . substr($generatedText, 0, 500));
            
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
     * Parse the structured JSON quiz content from Gemini API response
     * @param string $jsonContent
     * @return array
     */
    protected function parseStructuredQuizContent($jsonContent)
    {
        try {
            $data = json_decode($jsonContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            
            // Validate the structured data
            if (!$this->validateStructuredQuizData($data)) {
                Log::warning('Structured data validation failed, falling back to text parsing');
                Log::info('Raw JSON content for debugging: ' . substr($jsonContent, 0, 1000) . '...');
                return $this->parseQuizContent($jsonContent);
            }
            
            $mcqs = [];
            $trueFalse = [];
            $answerKey = [];
            
            // Process Multiple Choice Questions
            if (isset($data['multiple_choice_questions']) && is_array($data['multiple_choice_questions'])) {
                foreach ($data['multiple_choice_questions'] as $index => $mcq) {
                    $questionNumber = $mcq['question_number'] ?? ($index + 1);
                    $questionText = trim($mcq['question_text'] ?? '');
                    
                    // Additional quality check during parsing with comprehensive patterns
                    $badPatterns = [
                        '/^What is \w+ What\?/i',
                        '/^What is .{1,8}\?$/i',
                        '/^What is MIT What/i',
                        '/What\? What/i',
                        '/What What/i',
                        '/^Define .{1,10}\.?$/i',
                        '/^\w{1,5} \w{1,5}\?$/i'
                    ];
                    
                    $isValidQuestion = true;
                    foreach ($badPatterns as $pattern) {
                        if (preg_match($pattern, $questionText)) {
                            Log::warning("Skipping bad MCQ pattern: {$questionText}");
                            $isValidQuestion = false;
                            break;
                        }
                    }
                    
                    if (!$isValidQuestion || strlen($questionText) < 30) {
                        continue;
                    }
                    
                    $options = [
                        'A' => trim($mcq['options']['A'] ?? ''),
                        'B' => trim($mcq['options']['B'] ?? ''),
                        'C' => trim($mcq['options']['C'] ?? ''),
                        'D' => trim($mcq['options']['D'] ?? '')
                    ];
                    
                    // Validate all options are complete with stricter checking
                    $validOptions = true;
                    foreach ($options as $letter => $option) {
                        if (strlen($option) < 15) {
                            Log::warning("MCQ {$questionNumber}: Option {$letter} too short: {$option}");
                            $validOptions = false;
                            break;
                        }
                        
                        // Check for various fragment patterns
                        $fragmentPatterns = [
                            '/^A \w+$/i',
                            '/^An? \w+$/i',
                            '/^\w+ and$/i',
                            '/^[A-Z][a-z]+ [a-z]+$/i',
                            '/^A \w+ \w+$/i'
                        ];
                        
                        foreach ($fragmentPatterns as $pattern) {
                            if (preg_match($pattern, $option)) {
                                Log::warning("MCQ {$questionNumber}: Option {$letter} is a fragment: {$option}");
                                $validOptions = false;
                                break 2;
                            }
                        }
                    }
                    
                    if (!$validOptions) {
                        continue;
                    }
                    
                    $mcqs[] = [
                        'number' => $questionNumber,
                        'text' => $questionText,
                        'options' => $options
                    ];
                    
                    // Add to answer key
                    $answerKey[] = [
                        'number' => $questionNumber,
                        'answer' => $mcq['correct_answer'] ?? 'A',
                        'explanation' => trim($mcq['explanation'] ?? '')
                    ];
                }
            }
            
            // Process True/False Questions
            if (isset($data['true_false_questions']) && is_array($data['true_false_questions'])) {
                $tfStartNumber = count($mcqs) + 1;
                foreach ($data['true_false_questions'] as $index => $tf) {
                    $questionNumber = $tf['question_number'] ?? ($tfStartNumber + $index);
                    $statement = trim($tf['statement'] ?? '');
                    
                    // Additional quality check during parsing
                    if (strlen($statement) < 15) {
                        Log::warning("Skipping low-quality T/F statement: {$statement}");
                        continue;
                    }
                    
                    $trueFalse[] = [
                        'number' => $questionNumber,
                        'statement' => $statement
                    ];
                    
                    // Add to answer key
                    $answerKey[] = [
                        'number' => $questionNumber,
                        'answer' => $tf['correct_answer'] ?? 'True',
                        'explanation' => trim($tf['explanation'] ?? '')
                    ];
                }
            }
            
            // Final validation - ensure we have sufficient quality questions
            if (count($mcqs) < 3 || count($trueFalse) < 2) {
                Log::warning('Insufficient quality questions generated, falling back to text parsing');
                return $this->parseQuizContent($jsonContent);
            }
            
            // Accept partial results if at least 1 MCQ or 1 T/F is present
            if (count($mcqs) < 1 && count($trueFalse) < 1) {
                Log::warning('No quality questions generated, falling back to text parsing');
                return $this->parseQuizContent($jsonContent);
            }
            $disclaimer = '';
            if (count($mcqs) < 3 || count($trueFalse) < 2) {
                $disclaimer = 'Only ' . count($mcqs) . ' MCQs and ' . count($trueFalse) . ' True/False could be generated from the document.';
            }
            Log::info('Successfully parsed structured quiz content: ' . count($mcqs) . ' MCQs, ' . count($trueFalse) . ' T/F questions');
            return [
                'mcqs' => $mcqs,
                'true_false' => $trueFalse,
                'answer_key' => $answerKey,
                'disclaimer' => $disclaimer,
                'raw_content' => $jsonContent
            ];
            
        } catch (Exception $e) {
            Log::error('Error parsing structured quiz content: ' . $e->getMessage());
            
            // Fallback to original parsing method if structured parsing fails
            return $this->parseQuizContent($jsonContent);
        }
    }

    /**
     * Enhanced quiz content parsing with multiple strategies
     */
    
    protected function parseQuizContent($content)
    {
        Log::info('Starting quiz content parsing...');
        $mcqs = [];
        $trueFalse = [];
        $answerKey = [];
        $disclaimer = '';
        
        try {
            // Try multiple parsing approaches
            $result = $this->tryStructuredParsing($content);
            if (!empty($result['mcqs']) || !empty($result['true_false'])) {
                Log::info('Structured parsing successful');
                return $result;
            }
            
            $result = $this->tryFlexibleParsing($content);
            if (!empty($result['mcqs']) || !empty($result['true_false'])) {
                Log::info('Flexible parsing successful');
                return $result;
            }
            
            $result = $this->trySimpleParsing($content);
            if (!empty($result['mcqs']) || !empty($result['true_false'])) {
                Log::info('Simple parsing successful');
                return $result;
            }
            
            Log::warning('All parsing methods failed');
            return [
                'mcqs' => [],
                'true_false' => [],
                'answer_key' => [],
                'disclaimer' => '',
                'error' => 'Could not parse quiz content with any method',
                'raw_content' => $content
            ];
            
        } catch (Exception $e) {
            Log::error('Error parsing quiz content: ' . $e->getMessage());
            
            return [
                'mcqs' => [],
                'true_false' => [],
                'answer_key' => [],
                'disclaimer' => '',
                'error' => 'Error parsing quiz content: ' . $e->getMessage(),
                'raw_content' => $content
            ];
        }
    }
    
    /**
     * Try structured parsing (original method)
     */
    protected function tryStructuredParsing($content)
    {
        $mcqs = [];
        $trueFalse = [];
        $answerKey = [];
        $disclaimer = '';
        
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
        
        return [
            'mcqs' => $mcqs,
            'true_false' => $trueFalse,
            'answer_key' => $answerKey,
            'disclaimer' => $disclaimer
        ];
    }
    
    /**
     * Try flexible parsing for various formats
     */
    protected function tryFlexibleParsing($content)
    {
        $mcqs = [];
        $trueFalse = [];
        $answerKey = [];
        
        // Look for question patterns with more flexibility
        // Pattern: "1. Question text" followed by options A. B. C. D.
        preg_match_all('/(\d+)\.\s*(.+?)(?=\d+\.|$)/s', $content, $questionBlocks, PREG_SET_ORDER);
        
        foreach ($questionBlocks as $block) {
            $questionNum = trim($block[1]);
            $questionContent = trim($block[2]);
            
            // Check if it's a multiple choice question (has A. B. C. D. options)
            if (preg_match('/A\.\s*(.+?)\s*B\.\s*(.+?)\s*C\.\s*(.+?)\s*D\.\s*(.+?)(?:\s*(?:Answer|Correct)|$)/s', $questionContent, $mcqMatch)) {
                // Extract question text (before options)
                $questionText = preg_replace('/A\.\s*.+/s', '', $questionContent);
                $questionText = trim($questionText);
                
                $mcqs[] = [
                    'number' => $questionNum,
                    'text' => $questionText,
                    'options' => [
                        'A' => trim($mcqMatch[1]),
                        'B' => trim($mcqMatch[2]),
                        'C' => trim($mcqMatch[3]),
                        'D' => trim($mcqMatch[4])
                    ]
                ];
            }
            // Check if it's a True/False question
            elseif (preg_match('/true|false/i', $questionContent) && !preg_match('/A\.|B\.|C\.|D\./', $questionContent)) {
                $trueFalse[] = [
                    'number' => $questionNum,
                    'statement' => $questionContent
                ];
            }
        }
        
        return [
            'mcqs' => $mcqs,
            'true_false' => $trueFalse,
            'answer_key' => $answerKey,
            'disclaimer' => ''
        ];
    }
    
    /**
     * Try simple parsing for basic formats
     */
    protected function trySimpleParsing($content)
    {
        $mcqs = [];
        $lines = explode("\n", $content);
        $currentQuestion = null;
        $options = [];
        $questionCounter = 1;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Look for question patterns
            if (preg_match('/^(\d+)\.?\s*(.+)/', $line, $match)) {
                // Save previous question if exists
                if ($currentQuestion && count($options) >= 4) {
                    $mcqs[] = [
                        'number' => $questionCounter,
                        'text' => $currentQuestion,
                        'options' => [
                            'A' => $options[0] ?? '',
                            'B' => $options[1] ?? '',
                            'C' => $options[2] ?? '',
                            'D' => $options[3] ?? ''
                        ]
                    ];
                    $questionCounter++;
                }
                
                $currentQuestion = trim($match[2]);
                $options = [];
            }
            // Look for option patterns
            elseif (preg_match('/^[A-D]\.?\s*(.+)/', $line, $match)) {
                $options[] = trim($match[1]);
            }
            // Add to current question if no pattern matches
            elseif ($currentQuestion && !preg_match('/^[A-D]\./', $line)) {
                $currentQuestion .= ' ' . $line;
            }
        }
        
        // Save last question
        if ($currentQuestion && count($options) >= 4) {
            $mcqs[] = [
                'number' => $questionCounter,
                'text' => $currentQuestion,
                'options' => [
                    'A' => $options[0] ?? '',
                    'B' => $options[1] ?? '',
                    'C' => $options[2] ?? '',
                    'D' => $options[3] ?? ''
                ]
            ];
        }
        
        return [
            'mcqs' => $mcqs,
            'true_false' => [],
            'answer_key' => [],
            'disclaimer' => ''
        ];
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
SYSTEM / TASK INSTRUCTION:

You are an expert educational content generator. You are given a single uploaded lesson file (a PDF) from a professor. Your task is to read and fully understand the content of that PDF, then generate a high-quality quiz based solely on that material. Do NOT hallucinate or invent facts; every question, correct answer, distractor, and explanation must be directly supported by or safely inferred from the PDF. If something cannot be determined confidently from the text, say so instead of guessing.

GOALS:
1. Extract key concepts, definitions, distinctions, processes, laws, terminology, and common confusions from the document.
2. Generate quiz items that are **specific**, **clear**, and **test understanding**, not vague or generic.
3. Provide an answer key with justifications for any subtle or false items.

REQUIREMENTS:

A. QUESTION FORMATS (only these unless otherwise requested):
   1. **Multiple-Choice Questions (MCQ)**  
      - Generate exactly {$minMcq} MCQs.  
      - Each must have:
         * A precise, single-focus stem (e.g., "Which law states that pressure and volume are inversely proportional at constant temperature?" not "What does the document state about this topic?").  
         * Four options labeled A–D.  
         * Exactly one correct answer.  
         * Three **plausible** distractors drawn from related concepts or common misconceptions present in the lesson.  
         * Randomized order of options.  
      - Vary difficulty: include straightforward recall, application/distinction, and one nuanced detail question.

   2. **True/False Statements**  
      - Generate exactly {$minTf} statements.  
      - Each is a declarative factual sentence grounded in the PDF.  
      - Clearly mark whether it is True or False.  
      - False statements should mirror realistic misunderstandings (e.g., swapping roles or negating a precise condition), not arbitrary falsehoods.

B. GROUNDING & TRANSPARENCY  
   - For each question, include (internally or in metadata) the source sentence or snippet from the PDF that justifies it.  
   - If a question relies on an implicit but safe inference, annotate it as "Inference: …" in the explanation.  
   - Do not produce vague stems like "What is What?" or "According to the document, which statement is correct?" without specifying which concept is being tested.

C. DISTRACTOR CONSTRUCTION (for MCQs)  
   - Distractors must be:  
     * Related in domain (e.g., confusing similar standards, swapping "security in the cloud" vs "security of the cloud," mixing up definitions of terms that are explained nearby).  
     * Grammatically and structurally parallel to the correct answer.  
     * Not obviously wrong or irrelevant.

D. OUTPUT STRUCTURE (Markdown-style)
   **I. Multiple-Choice Questions**  
   1. [Stem]?  
      A. Option  
      B. Option  
      C. Option  
      D. Option  
   2. …  

   **II. True/False Statements**  
   " . ($minMcq + 1) . ". [Statement]. (True/False)  
   " . ($minMcq + 2) . ". [Statement]. (True/False)  
   …  

   **III. Answer Key**  
   1. [Letter]  
   2. [Letter]  
   …  
   " . ($minMcq + 1) . ". True/False — [If False or subtle, include a one-sentence explanation or citation]  
   " . ($minMcq + 2) . ". True/False — [If False or subtle, include a one-sentence explanation or citation]  
   …  

E. QUALITY CONTROLS  
   - Reject candidate questions that:
     * Are too vague to be answered without ambiguity.  
     * Have multiple plausible "correct" answers.  
     * Use filler language or ask about "the document" without tying to a concrete fact.  
   - Prefer coverage: don't over-focus on one paragraph; sample across distinct major topics in the PDF.

F. EXAMPLES (for clarity):

   **Bad / Vague:**  
   "According to the document, which statement is correct?"  
     A. Something generic  
     B. A partially related phrase  
     C. Unclear concept  
     D. Irrelevant option  
   *Problem:* No specific concept being tested; options are unfocused.

   **Improved:**  
   "Which law states that pressure and volume are inversely proportional when temperature is constant?"  
     A. Charles's Law  
     B. Boyle's Law  
     C. Zeroth Law  
     D. First Law  
   *Answer:* B — Clear stem, one correct choice, distractors are commonly confused gas laws.

   **True/False example:**  
   "Boyle's Law says that pressure and volume are directly proportional at constant temperature." (False) — Explanation: They are inversely proportional. Source: [include brief paraphrase or reference].

INSTRUCTIONS FOR EACH GENERATION RUN:
1. Retrieve the most relevant passages from the uploaded PDF (e.g., definitions, laws, comparisons).  
2. From those, generate the required number of MCQs and True/False statements, following all rules above.  
3. Provide the answer key with correct labels and explanations for any non-obvious or false items, including the source snippet or a tight paraphrase.

DISCLAIMER IF LIMITED:  
If the PDF lacks enough distinct factual units to meet the target counts ({$minMcq} MCQs + {$minTf} T/F), generate as many high-quality items as possible and prepend a note:  
"Generated X MCQs and Y True/False statements; source material did not contain enough distinct facts to safely produce more without redundancy."

END OF INSTRUCTION.
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
     * Clean and validate document content for better quiz generation
     * @param string $content
     * @return string
     */
    private function cleanDocumentContent($content)
    {
        // Remove excessive whitespace and normalize line breaks
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        // Remove common PDF artifacts
        $content = preg_replace('/[^\w\s\.\,\;\:\!\?\-\(\)\[\]\{\}\"\']+/', ' ', $content);
        
        // Ensure content has meaningful length
        if (strlen($content) < 100) {
            return '';
        }
        
        return $content;
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
            $explanation = '';
            
            foreach ($quizData['answer_key'] as $key) {
                if ($key['number'] == $mcq['number']) {
                    $correctOption = $key['answer'];
                    $explanation = $key['explanation'] ?? '';
                    break;
                }
            }
            
            $questions[] = [
                'question_text' => $mcq['text'],
                'question_type' => 'multiple_choice',
                'options' => $this->safeJsonEncode($mcq['options']),
                'correct_answer' => $correctOption,
                'explanation' => $explanation,
                'question_source' => 'gemini_structured',
                'points' => 1,
                'is_active' => true,
            ];
        }
        
        // Convert True/False questions
        foreach ($quizData['true_false'] as $tf) {
            // Find the correct answer from answer key
            $correctAnswer = 'True'; // Default
            $explanation = '';
            
            foreach ($quizData['answer_key'] as $key) {
                if ($key['number'] == $tf['number']) {
                    $correctAnswer = $key['answer'];
                    $explanation = $key['explanation'] ?? '';
                    break;
                }
            }
            
            $questions[] = [
                'question_text' => $tf['statement'],
                'question_type' => 'true_false',
                'options' => $this->safeJsonEncode(['True', 'False']),
                'correct_answer' => $correctAnswer,
                'explanation' => $explanation,
                'question_source' => 'gemini_structured',
                'points' => 1,
                'is_active' => true,
            ];
        }
        
        return $questions;
    }
    
    /**
     * Validate structured quiz data with enhanced quality checks
     * @param array $data
     * @return bool
     */
    private function validateStructuredQuizData($data)
    {
        // Check if we have any questions at all
        $hasMcqs = isset($data['multiple_choice_questions']) && is_array($data['multiple_choice_questions']);
        $hasTf = isset($data['true_false_questions']) && is_array($data['true_false_questions']);
        
        if (!$hasMcqs && !$hasTf) {
            Log::warning('No questions found in structured data');
            return false;
        }
        
        // Basic validation - just check if we have some valid questions
        $validQuestions = 0;
        
        if ($hasMcqs) {
            foreach ($data['multiple_choice_questions'] as $index => $mcq) {
                if (isset($mcq['question_text'], $mcq['options'], $mcq['correct_answer']) && 
                    strlen(trim($mcq['question_text'])) > 10) {
                    $validQuestions++;
                }
            }
        }
        
        if ($hasTf) {
            foreach ($data['true_false_questions'] as $index => $tf) {
                if (isset($tf['statement'], $tf['correct_answer']) && 
                    strlen(trim($tf['statement'])) > 10) {
                    $validQuestions++;
                }
            }
        }
        
        if ($validQuestions > 0) {
            Log::info("Structured quiz data validation passed with {$validQuestions} valid questions");
            return true;
        }
        
        Log::warning('No valid questions found in structured data');
        return false;
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
