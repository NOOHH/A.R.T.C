<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use thiagoalessio\TesseractOCR\TesseractOCR;

class GeminiQuizService
{
    private $apiKey;
    private $apiUrl;
    private $model;
    private $systemInstruction;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', 'AIzaSyApwLadkEmUpUe8kv5Nl5-7p35ob9_DSsY');
        $this->model = 'gemini-2.0-flash';
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
        
        $this->systemInstruction = $this->getSystemInstruction();
    }

    /**
     * Enhanced system instruction for comprehensive quiz generation
     */
    private function getSystemInstruction(): string
    {
        return "You are an expert educational quiz generator specializing in comprehensive content analysis. Your role is to read uploaded files entirely and create diverse, accurate quiz questions based strictly on the content provided.

CORE INSTRUCTIONS:
1. Read and understand the uploaded file in its entirety
2. Extract key terms, concepts, definitions, dates, acronyms, processes, and relationships
3. Generate ONLY factual content that exists in the provided text - DO NOT invent or assume information
4. Create questions that test different levels of understanding and recall

QUESTION FORMATS TO GENERATE (minimum 3 questions each):

A. ACRONYM EXPANSION
- \"What does [ACRONYM] stand for?\"
- \"Which term is abbreviated as [ACRONYM]?\"
- \"[ACRONYM] is an abbreviation for what?\"

B. DEFINITION RECALL
- \"Define [TERM].\"
- \"[TERM] refers to what concept?\"
- \"What is the meaning of [TERM]?\"

C. MULTIPLE CHOICE (4 options)
- One correct answer + three plausible distractors
- Label options A, B, C, D
- Distractors must be plausible but clearly incorrect based on the content

D. TRUE/FALSE
- Factual statements extracted or derived from the text
- Include both true and false statements
- Base false statements on common misconceptions or slight modifications of true facts

E. MATCHING
- Column A: terms, concepts, processes, dates
- Column B: definitions, explanations, related information
- Minimum 4-6 matching pairs per set

F. FILL-IN-THE-BLANK
- Sentences with key words or phrases replaced by blanks
- Focus on important terms, names, dates, or concepts
- Provide clear context clues

G. SHORT ANSWER
- \"Explain in 1-2 sentences...\"
- \"List three components of...\"
- \"Describe the process of...\"
- \"What are the main characteristics of...\"

RESPONSE FORMAT:
Generate questions in this exact JSON structure:
{
  \"content_analysis\": {
    \"key_terms\": [\"list of important terms found\"],
    \"concepts\": [\"list of main concepts\"],
    \"acronyms\": [\"list of acronyms with expansions\"],
    \"processes\": [\"list of processes or procedures\"],
    \"dates_events\": [\"list of important dates or events\"]
  },
  \"questions\": {
    \"acronym_expansion\": [
      {
        \"question\": \"What does TECHINT stand for?\",
        \"correct_answer\": \"Technical Intelligence\",
        \"type\": \"acronym_expansion\"
      }
    ],
    \"definition_recall\": [
      {
        \"question\": \"Define Technical Intelligence.\",
        \"correct_answer\": \"The systematic collection and analysis of technical data...\",
        \"type\": \"definition_recall\"
      }
    ],
    \"multiple_choice\": [
      {
        \"question\": \"Which of the following best describes TECHINT?\",
        \"options\": {
          \"A\": \"Human intelligence gathering\",
          \"B\": \"Technical intelligence collection\",
          \"C\": \"Social media monitoring\",
          \"D\": \"Financial intelligence analysis\"
        },
        \"correct_answer\": \"B\",
        \"explanation\": \"TECHINT refers to Technical Intelligence collection methods\",
        \"type\": \"multiple_choice\"
      }
    ],
    \"true_false\": [
      {
        \"question\": \"TECHINT involves obtaining intelligence through technical means rather than human sources.\",
        \"correct_answer\": \"True\",
        \"explanation\": \"Technical Intelligence focuses on technical collection methods\",
        \"type\": \"true_false\"
      }
    ],
    \"matching\": [
      {
        \"question\": \"Match the following terms with their definitions:\",
        \"column_a\": [\"TECHINT\", \"HUMINT\", \"OSINT\"],
        \"column_b\": [\"Technical Intelligence\", \"Human Intelligence\", \"Open Source Intelligence\"],
        \"correct_matches\": {\"TECHINT\": \"Technical Intelligence\", \"HUMINT\": \"Human Intelligence\", \"OSINT\": \"Open Source Intelligence\"},
        \"type\": \"matching\"
      }
    ],
    \"fill_in_blank\": [
      {
        \"question\": \"_______ is the systematic collection and analysis of technical data to support intelligence requirements.\",
        \"correct_answer\": \"Technical Intelligence\",
        \"type\": \"fill_in_blank\"
      }
    ],
    \"short_answer\": [
      {
        \"question\": \"Explain in 1-2 sentences what Technical Intelligence involves.\",
        \"correct_answer\": \"Technical Intelligence involves the systematic collection and analysis of technical data through various technical means and methods to support intelligence requirements.\",
        \"type\": \"short_answer\"
      }
    ]
  },
  \"answer_key\": {
    \"1\": \"Technical Intelligence\",
    \"2\": \"The systematic collection...\",
    \"note\": \"Complete answer key with question numbers mapped to correct answers\"
  }
}

QUALITY STANDARDS:
- Only use information explicitly stated in the provided content
- Create plausible but clearly incorrect distractors for multiple choice
- Ensure true/false statements are definitively determinable from the content
- Make fill-in-the-blank questions have clear context clues
- Keep short answers focused and concise
- Maintain academic integrity and educational value
- Use clear, professional language appropriate for the academic level";
    }

    /**
     * Process uploaded file and extract content
     */
    public function processUploadedFile(UploadedFile $file): string
    {
        try {
            $extension = strtolower($file->getClientOriginalExtension());
            $content = '';

            switch ($extension) {
                case 'txt':
                    $content = $this->extractTextFromTxt($file);
                    break;
                case 'pdf':
                    $content = $this->extractTextFromPdf($file);
                    break;
                case 'doc':
                case 'docx':
                    $content = $this->extractTextFromWord($file);
                    break;
                default:
                    throw new Exception("Unsupported file type: {$extension}");
            }

            // Clean and validate content
            $content = $this->cleanExtractedContent($content);
            
            if (strlen($content) < 100) {
                throw new Exception("Extracted content is too short. Please provide a more substantial document.");
            }

            Log::info("File processed successfully", [
                'filename' => $file->getClientOriginalName(),
                'type' => $extension,
                'content_length' => strlen($content)
            ]);

            return $content;

        } catch (Exception $e) {
            Log::error("File processing failed", [
                'filename' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Extract text from TXT file
     */
    private function extractTextFromTxt(UploadedFile $file): string
    {
        return file_get_contents($file->getRealPath());
    }

    /**
     * Extract text from PDF file with Tesseract OCR fallback
     */
    private function extractTextFromPdf(UploadedFile $file): string
    {
        $extractedText = '';
        
        try {
            // First attempt: Use smalot/pdfparser library for text-based PDFs
            Log::info('Attempting PDF text extraction with smalot/pdfparser');
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            $extractedText = $pdf->getText();
            
            // Check if we got meaningful text (more than just whitespace)
            $cleanText = trim(preg_replace('/\s+/', ' ', $extractedText));
            
            if (strlen($cleanText) > 50) {
                Log::info('PDF text extraction successful with smalot/pdfparser', [
                    'text_length' => strlen($cleanText)
                ]);
                return $extractedText;
            } else {
                Log::warning('PDF text extraction with smalot/pdfparser yielded minimal text', [
                    'text_length' => strlen($cleanText)
                ]);
                throw new Exception('Minimal text extracted, trying OCR');
            }
            
        } catch (Exception $e) {
            Log::warning('PDF text extraction with smalot/pdfparser failed, trying Tesseract OCR', [
                'error' => $e->getMessage()
            ]);
            
            // Fallback: Use Tesseract OCR for image-based PDFs or when parsing fails
            try {
                return $this->extractTextFromPdfWithOCR($file);
            } catch (Exception $ocrError) {
                Log::error('Both PDF extraction methods failed', [
                    'parser_error' => $e->getMessage(),
                    'ocr_error' => $ocrError->getMessage()
                ]);
                throw new Exception("Failed to extract text from PDF. Parser error: " . $e->getMessage() . ". OCR error: " . $ocrError->getMessage());
            }
        }
    }
    
    /**
     * Extract text from PDF using Tesseract OCR
     */
    private function extractTextFromPdfWithOCR(UploadedFile $file): string
    {
        try {
            Log::info('Starting Tesseract OCR extraction for PDF', [
                'file_size' => $file->getSize(),
                'file_name' => $file->getClientOriginalName()
            ]);
            
            // Increase memory and time limits for large files
            ini_set('memory_limit', '2G');
            ini_set('max_execution_time', 600); // 10 minutes
            
            // Create temporary directory for processing
            $tempDir = storage_path('app/temp_ocr');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $filename = uniqid('pdf_ocr_') . '.pdf';
            $pdfPath = $tempDir . '/' . $filename;
            
            // Copy uploaded file to temporary location
            copy($file->getRealPath(), $pdfPath);
            
            // Convert PDF to images and then OCR each page
            $extractedText = '';
            
            // Use ImageMagick to convert PDF pages to images (if available)
            if (extension_loaded('imagick') && class_exists('Imagick')) {
                Log::info('Using ImageMagick for PDF to image conversion');
                $extractedText = $this->ocrPdfWithImageMagick($pdfPath);
            } else {
                Log::info('ImageMagick not available, attempting direct OCR');
                // Try direct OCR on PDF (some versions of Tesseract support this)
                try {
                    $ocr = new TesseractOCR($pdfPath);
                    $ocr->lang('eng'); // Set language to English
                    $ocr->configFile('pdf'); // Use PDF configuration if available
                    $extractedText = $ocr->run();
                } catch (Exception $directOcrError) {
                    Log::warning('Direct OCR failed, trying alternative method', [
                        'error' => $directOcrError->getMessage()
                    ]);
                    throw new Exception('Direct OCR not supported for this PDF format');
                }
            }
            
            // Clean up temporary file
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            $cleanText = trim($extractedText);
            if (strlen($cleanText) < 10) {
                throw new Exception('OCR extraction yielded minimal text');
            }
            
            Log::info('Tesseract OCR extraction successful', [
                'text_length' => strlen($cleanText),
                'file_size' => $file->getSize()
            ]);
            
            return $extractedText;
            
        } catch (Exception $e) {
            Log::error('Tesseract OCR extraction failed', [
                'error' => $e->getMessage(),
                'file_size' => $file->getSize()
            ]);
            throw new Exception("OCR extraction failed: " . $e->getMessage());
        }
    }
    
    /**
     * OCR PDF using ImageMagick conversion
     */
    private function ocrPdfWithImageMagick(string $pdfPath): string
    {
        $extractedText = '';
        
        try {
            if (!extension_loaded('imagick') || !class_exists('Imagick')) {
                throw new Exception('ImageMagick extension not available');
            }
            
            $imagick = new \Imagick();
            
            // Set resource limits for large files
            $imagick->setResourceLimit(\Imagick::RESOURCETYPE_MEMORY, 1024 * 1024 * 1024); // 1GB
            $imagick->setResourceLimit(\Imagick::RESOURCETYPE_DISK, 2048 * 1024 * 1024);   // 2GB
            
            // Optimize settings for OCR
            $imagick->setResolution(200, 200); // Good balance between quality and speed
            $imagick->readImage($pdfPath);
            
            // Get number of pages
            $pageCount = $imagick->getNumberImages();
            Log::info("Processing PDF with {$pageCount} pages for OCR");
            
            // Limit processing for very large documents
            $maxPages = 50; // Process maximum 50 pages
            if ($pageCount > $maxPages) {
                Log::warning("PDF has {$pageCount} pages, limiting to first {$maxPages} pages");
                $pageCount = $maxPages;
            }
            
            // Process each page
            $processedPages = 0;
            foreach ($imagick as $pageNum => $page) {
                if ($processedPages >= $pageCount) break;
                
                $processedPages++;
                Log::info("Processing PDF page " . $processedPages . " with OCR");
                
                try {
                    // Convert to format suitable for OCR
                    $page->setImageFormat('png');
                    $page->setImageCompressionQuality(90); // Reduce quality slightly for speed
                    $page->setImageType(\Imagick::IMGTYPE_GRAYSCALE); // Convert to grayscale
                    
                    // Enhance image for better OCR
                    $page->normalizeImage(); // Normalize contrast
                    $page->despeckleImage(); // Remove noise
                    
                    // Save temporary image
                    $tempImagePath = storage_path('app/temp_ocr/page_' . $processedPages . '.png');
                    $page->writeImage($tempImagePath);
                    
                    // OCR the image
                    $ocr = new TesseractOCR($tempImagePath);
                    $ocr->lang('eng');
                    $ocr->psm(6); // Assume uniform block of text
                    $ocr->oem(3); // Use default OCR Engine Mode
                    
                    $pageText = $ocr->run();
                    
                    if (!empty(trim($pageText))) {
                        $extractedText .= "\n\n--- Page " . $processedPages . " ---\n" . $pageText;
                    }
                    
                    // Clean up temporary image
                    if (file_exists($tempImagePath)) {
                        unlink($tempImagePath);
                    }
                    
                } catch (Exception $pageError) {
                    Log::warning("Failed to process page {$processedPages}", [
                        'error' => $pageError->getMessage()
                    ]);
                    // Continue with next page
                    continue;
                }
            }
            
            $imagick->clear();
            $imagick->destroy();
            
            if (empty(trim($extractedText))) {
                throw new Exception("No text could be extracted from any pages");
            }
            
        } catch (Exception $e) {
            throw new Exception("ImageMagick OCR processing failed: " . $e->getMessage());
        }
        
        return $extractedText;
    }

    /**
     * Extract text from Word document
     */
    private function extractTextFromWord(UploadedFile $file): string
    {
        try {
            $extension = strtolower($file->getClientOriginalExtension());
            
            if ($extension === 'docx') {
                // For DOCX files
                $zip = new \ZipArchive();
                if ($zip->open($file->getRealPath()) === TRUE) {
                    $content = $zip->getFromName('word/document.xml');
                    $zip->close();
                    
                    // Remove XML tags and extract text
                    $content = strip_tags($content);
                    return $content;
                }
            } else {
                // For DOC files - basic extraction
                $content = file_get_contents($file->getRealPath());
                // Simple text extraction for DOC files
                $content = preg_replace('/[^\x20-\x7E\x0A\x0D]/', '', $content);
                return $content;
            }
            
            throw new Exception("Could not extract content from Word document");
        } catch (Exception $e) {
            throw new Exception("Failed to extract text from Word document: " . $e->getMessage());
        }
    }

    /**
     * Clean and prepare extracted content
     */
    private function cleanExtractedContent(string $content): string
    {
        // Remove excessive whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        
        // Remove special characters that might interfere
        $content = preg_replace('/[^\w\s\.\,\;\:\!\?\-\(\)\[\]\"\'\/]/', '', $content);
        
        // Trim and ensure proper encoding
        $content = trim($content);
        $content = mb_convert_encoding($content, 'UTF-8', 'auto');
        
        return $content;
    }

    /**
     * Generate comprehensive quiz questions from content using Gemini AI
     */
    public function generateComprehensiveQuiz(
        string $content, 
        int $numQuestionsPerType = 3,
        array $additionalParams = []
    ): array {
        try {
            // Prepare the comprehensive prompt
            $prompt = $this->buildComprehensivePrompt($content, $numQuestionsPerType, $additionalParams);
            
            // Make API request
            $response = $this->makeComprehensiveApiRequest($prompt);
            
            // Parse and validate response
            $comprehensiveQuiz = $this->parseComprehensiveResponse($response);
            
            Log::info("Comprehensive quiz generated successfully", [
                'total_question_types' => count($comprehensiveQuiz['questions']),
                'content_length' => strlen($content)
            ]);
            
            return $comprehensiveQuiz;

        } catch (Exception $e) {
            Log::error("Comprehensive quiz generation failed", [
                'error' => $e->getMessage(),
                'content_length' => strlen($content),
                'num_questions_per_type' => $numQuestionsPerType
            ]);
            throw $e;
        }
    }

    /**
     * Generate quiz questions from content using Gemini AI (backwards compatibility)
     */
    public function generateQuizQuestions(
        string $content, 
        int $numQuestions = 10, 
        string $difficulty = 'mixed',
        array $additionalParams = []
    ): array {
        // For backwards compatibility, generate comprehensive quiz and flatten to simple format
        $comprehensiveQuiz = $this->generateComprehensiveQuiz($content, 3, $additionalParams);
        
        // Convert to old format for existing code compatibility
        $flattenedQuestions = [];
        
        foreach ($comprehensiveQuiz['questions'] as $type => $questions) {
            foreach ($questions as $question) {
                if ($type === 'multiple_choice') {
                    $flattenedQuestions[] = [
                        'question' => $question['question'],
                        'options' => $question['options'],
                        'correct_answer' => $question['correct_answer'],
                        'explanation' => $question['explanation'] ?? 'No explanation provided',
                        'difficulty' => 'medium', // Default since we removed difficulty
                        'topic' => $question['type'] ?? 'General',
                        'type' => 'multiple_choice'
                    ];
                } else if ($type === 'true_false') {
                    // Convert true/false to multiple choice format for compatibility
                    $flattenedQuestions[] = [
                        'question' => $question['question'],
                        'options' => ['A' => 'True', 'B' => 'False'],
                        'correct_answer' => $question['correct_answer'] === 'True' ? 'A' : 'B',
                        'explanation' => $question['explanation'] ?? 'No explanation provided',
                        'difficulty' => 'medium',
                        'topic' => $question['type'] ?? 'General',
                        'type' => 'true_false'
                    ];
                }
            }
        }
        
        // Limit to requested number
        return array_slice($flattenedQuestions, 0, $numQuestions);
    }

    /**
     * Build comprehensive quiz prompt with multiple question types
     */
    private function buildComprehensivePrompt(
        string $content, 
        int $numQuestionsPerType = 3,
        array $additionalParams = []
    ): array {
        $topicFocus = $additionalParams['topic_focus'] ?? 'general understanding';
        
        $contents = [
            [
                'parts' => [
                    [
                        'text' => $this->getSystemInstruction()
                    ]
                ]
            ],
            [
                'parts' => [
                    [
                        'text' => "CONTENT TO ANALYZE:\n\n{$content}\n\n"
                            . "GENERATION PARAMETERS:\n"
                            . "- Questions per type: {$numQuestionsPerType}\n"
                            . "- Topic focus: {$topicFocus}\n\n"
                            . "Please generate a comprehensive quiz with the 7 question types as specified in the system instruction. "
                            . "Ensure all questions are derived from the provided content and maintain high accuracy."
                    ]
                ]
            ]
        ];

        return [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.3,
                'topK' => 40,
                'topP' => 0.8,
                'maxOutputTokens' => 8192,
            ]
        ];
    }

    /**
     * Parse comprehensive API response with multiple question types
     */
    private function parseComprehensiveResponse(string $response): array {
        try {
            // Clean the response
            $cleanResponse = $this->cleanJsonResponse($response);
            $decodedResponse = json_decode($cleanResponse, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("JSON decode error: " . json_last_error_msg());
            }
            
            // Validate comprehensive response structure
            if (!isset($decodedResponse['content_analysis']) || !isset($decodedResponse['questions'])) {
                throw new Exception("Invalid comprehensive response structure");
            }
            
            // Validate all question types are present
            $expectedTypes = [
                'acronym_expansion', 'definition_recall', 'multiple_choice',
                'true_false', 'matching', 'fill_in_blank', 'short_answer'
            ];
            
            foreach ($expectedTypes as $type) {
                if (!isset($decodedResponse['questions'][$type])) {
                    Log::warning("Missing question type in response: {$type}");
                    $decodedResponse['questions'][$type] = [];
                }
            }
            
            // Validate answer key exists
            if (!isset($decodedResponse['answer_key'])) {
                throw new Exception("Missing answer key in comprehensive response");
            }
            
            return $decodedResponse;
            
        } catch (Exception $e) {
            Log::error("Failed to parse comprehensive response", [
                'error' => $e->getMessage(),
                'response_snippet' => substr($response, 0, 500)
            ]);
            throw $e;
        }
    }

    /**
     * Build comprehensive prompt for quiz generation (backwards compatibility)
     */
    private function buildPrompt(string $content, int $numQuestions, string $difficulty, array $params): string
    {
        $difficultyInstruction = $this->getDifficultyInstruction($difficulty);
        $topicFocus = isset($params['topic_focus']) ? $params['topic_focus'] : 'all topics covered in the content';
        $questionTypes = isset($params['question_types']) ? $params['question_types'] : 'mixed cognitive levels';
        
        return "CONTENT TO ANALYZE:
{$content}

QUIZ GENERATION REQUIREMENTS:
- Generate exactly {$numQuestions} multiple choice questions
- Difficulty level: {$difficulty} - {$difficultyInstruction}
- Focus on: {$topicFocus}
- Question types: {$questionTypes}
- Ensure questions test understanding of the provided content
- Make sure all questions are answerable from the given material
- Create plausible but clearly incorrect distractors
- Vary question difficulty and cognitive demand appropriately

Please generate the quiz questions following the specified JSON format.";
    }

    /**
     * Get difficulty-specific instructions
     */
    private function getDifficultyInstruction(string $difficulty): string
    {
        switch ($difficulty) {
            case 'easy':
                return 'Focus on basic recall, definitions, and simple comprehension';
            case 'medium':
                return 'Include application questions and moderate analysis';
            case 'hard':
                return 'Emphasize analysis, synthesis, and complex application';
            case 'mixed':
            default:
                return 'Include a balanced mix of easy (30%), medium (50%), and hard (20%) questions';
        }
    }

    /**
     * Make API request specifically for comprehensive quiz generation
     */
    private function makeComprehensiveApiRequest(array $requestData): string
    {
        Log::info("Making comprehensive API request", ['request_size' => strlen(json_encode($requestData))]);
        
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=" . $this->apiKey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        Log::info("Sending request to Gemini API...");
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        Log::info("API response received", ['http_code' => $httpCode, 'has_error' => !empty($error)]);

        if ($error) {
            Log::error("cURL Error: " . $error);
            throw new Exception("cURL Error: " . $error);
        }

        if ($httpCode !== 200) {
            Log::error("Gemini API request failed", [
                'http_code' => $httpCode,
                'response' => substr($response, 0, 1000) // Log first 1000 chars
            ]);
            throw new Exception("API request failed with HTTP code: " . $httpCode);
        }

        $responseData = json_decode($response, true);
        if (!$responseData || !isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            Log::error("Invalid API response structure", ['response' => substr($response, 0, 500)]);
            throw new Exception("Invalid API response structure");
        }

        $textResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
        Log::info("API response parsed successfully", ['response_length' => strlen($textResponse)]);
        
        return $textResponse;
    }

    /**
     * Clean JSON response to handle potential formatting issues
     */
    private function cleanJsonResponse(string $response): string
    {
        // Remove any markdown code block markers
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*$/', '', $response);
        
        // Trim whitespace
        $response = trim($response);
        
        // Try to find JSON content between braces if wrapped in other text
        if (preg_match('/\{.*\}/s', $response, $matches)) {
            $response = $matches[0];
        }
        
        return $response;
    }

    /**
     * Make API request to Gemini
     */
    private function makeApiRequest(string $prompt): array
    {
        $curl = curl_init();
        
        $requestData = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $this->systemInstruction . "\n\n" . $prompt
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3, // Lower temperature for more consistent results
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 8192,
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl . '?key=' . $this->apiKey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }

        if ($httpCode !== 200) {
            throw new Exception("API request failed with HTTP code: " . $httpCode . ". Response: " . $response);
        }

        $decodedResponse = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON response from API");
        }

        return $decodedResponse;
    }

    /**
     * Parse and validate API response
     */
    private function parseApiResponse(array $response): array
    {
        if (!isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Exception("Invalid API response structure");
        }

        $generatedText = $response['candidates'][0]['content']['parts'][0]['text'];
        
        // Extract JSON from the response
        preg_match('/\{.*\}/s', $generatedText, $matches);
        
        if (empty($matches)) {
            throw new Exception("No valid JSON found in API response");
        }

        $jsonData = json_decode($matches[0], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to parse JSON from API response: " . json_last_error_msg());
        }

        if (!isset($jsonData['questions']) || !is_array($jsonData['questions'])) {
            throw new Exception("Invalid question format in API response");
        }

        // Validate each question
        $validatedQuestions = [];
        foreach ($jsonData['questions'] as $index => $question) {
            $validated = $this->validateQuestion($question, $index);
            if ($validated) {
                $validatedQuestions[] = $validated;
            }
        }

        if (empty($validatedQuestions)) {
            throw new Exception("No valid questions found in API response");
        }

        return $validatedQuestions;
    }

    /**
     * Validate individual question structure
     */
    private function validateQuestion(array $question, int $index): ?array
    {
        $required = ['question', 'options', 'correct_answer'];
        
        foreach ($required as $field) {
            if (!isset($question[$field]) || empty($question[$field])) {
                Log::warning("Question {$index} missing required field: {$field}");
                return null;
            }
        }

        // Validate options
        if (!is_array($question['options']) || count($question['options']) < 4) {
            Log::warning("Question {$index} has invalid options");
            return null;
        }

        // Validate correct answer
        if (!isset($question['options'][$question['correct_answer']])) {
            Log::warning("Question {$index} has invalid correct_answer reference");
            return null;
        }

        // Set defaults for optional fields
        $question['explanation'] = $question['explanation'] ?? 'No explanation provided';
        $question['difficulty'] = $question['difficulty'] ?? 'medium';
        $question['topic'] = $question['topic'] ?? 'General';

        return $question;
    }

    /**
     * Get content summary for preview
     */
    public function getContentSummary(string $content, int $maxLength = 500): string
    {
        if (strlen($content) <= $maxLength) {
            return $content;
        }

        return substr($content, 0, $maxLength) . '...';
    }

    /**
     * Validate content quality before processing
     */
    public function validateContent(string $content): array
    {
        $issues = [];
        
        if (strlen($content) < 100) {
            $issues[] = 'Content is too short (minimum 100 characters required)';
        }
        
        if (strlen($content) > 50000) {
            $issues[] = 'Content is too long (maximum 50,000 characters allowed)';
        }
        
        $wordCount = str_word_count($content);
        if ($wordCount < 50) {
            $issues[] = 'Content has too few words (minimum 50 words required)';
        }
        
        // Check for reasonable text structure
        $sentences = preg_split('/[.!?]+/', $content);
        if (count($sentences) < 5) {
            $issues[] = 'Content should contain more sentences for better question generation';
        }

        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'word_count' => $wordCount,
            'character_count' => strlen($content)
        ];
    }
}
