<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Program;
use App\Models\Module;

class OcrService
{
    /**
     * Extract text from a file (image or PDF) with enhanced preprocessing
     * @param string $filePath
     * @param string $fileType
     * @return string
     */
    public function extractText(string $filePath, string $fileType = null): string
    {
        try {
            if (!$fileType) {
                $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            }

            if (in_array($fileType, ['pdf'])) {
                return $this->extractFromPdf($filePath);
            }

            // For images, try preprocessing if available
            $processedPath = $this->preprocessImageForOcr($filePath);
            $text = $this->extractFromImage($processedPath);
            
            // Clean up temporary processed file if it was created
            if ($processedPath !== $filePath && file_exists($processedPath)) {
                unlink($processedPath);
            }
            
            return $text;
        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Preprocess image for better OCR results with cursive/stylized text
     * @param string $imagePath
     * @return string Path to processed image (original if no processing applied)
     */
    private function preprocessImageForOcr(string $imagePath): string
    {
        // Check if ImageMagick or GD is available for preprocessing
        if (!class_exists('Imagick') && !extension_loaded('gd')) {
            Log::info('No image processing extension available, using original image');
            return $imagePath;
        }
        
        try {
            $processedPath = sys_get_temp_dir() . '/ocr_processed_' . uniqid() . '.png';
            
            if (class_exists('Imagick')) {
                $this->preprocessWithImageMagick($imagePath, $processedPath);
            } elseif (extension_loaded('gd')) {
                $this->preprocessWithGD($imagePath, $processedPath);
            }
            
            return file_exists($processedPath) ? $processedPath : $imagePath;
        } catch (\Exception $e) {
            Log::warning('Image preprocessing failed', ['error' => $e->getMessage()]);
            return $imagePath;
        }
    }
    
    /**
     * Preprocess image using ImageMagick for better cursive text recognition
     * @param string $inputPath
     * @param string $outputPath
     */
    private function preprocessWithImageMagick(string $inputPath, string $outputPath): void
    {
        if (!class_exists('Imagick')) {
            throw new \Exception('Imagick class not available');
        }
        
        $imagick = new \Imagick($inputPath);
        
        // Convert to grayscale
        $imagick->transformImageColorspace(\Imagick::COLORSPACE_GRAY);
        
        // Enhance contrast
        $imagick->contrastImage(true);
        $imagick->normalizeImage();
        
        // Sharpen the image to help with cursive text
        $imagick->sharpenImage(0, 1);
        
        // Remove noise
        $imagick->despeckleImage();
        
        // Increase resolution for better character recognition
        $imagick->resampleImage(300, 300, \Imagick::FILTER_LANCZOS, 1);
        
        // Set format to PNG for best quality
        $imagick->setImageFormat('png');
        $imagick->writeImage($outputPath);
        $imagick->destroy();
        
        Log::info('Image preprocessed with ImageMagick for OCR');
    }
    
    /**
     * Preprocess image using GD for better cursive text recognition
     * @param string $inputPath
     * @param string $outputPath
     */
    private function preprocessWithGD(string $inputPath, string $outputPath): void
    {
        $imageInfo = getimagesize($inputPath);
        $mimeType = $imageInfo['mime'];
        
        switch ($mimeType) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($inputPath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($inputPath);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($inputPath);
                break;
            default:
                throw new \Exception('Unsupported image type for GD preprocessing');
        }
        
        $width = imagesx($source);
        $height = imagesy($source);
        
        // Create a new image with enhanced size
        $newWidth = $width * 2;
        $newHeight = $height * 2;
        $processed = imagecreatetruecolor($newWidth, $newHeight);
        
        // Make background white
        $white = imagecolorallocate($processed, 255, 255, 255);
        imagefill($processed, 0, 0, $white);
        
        // Resize with high quality
        imagecopyresampled($processed, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Convert to grayscale and enhance contrast
        imagefilter($processed, IMG_FILTER_GRAYSCALE);
        imagefilter($processed, IMG_FILTER_CONTRAST, -20);
        
        // Save as PNG
        imagepng($processed, $outputPath);
        
        imagedestroy($source);
        imagedestroy($processed);
        
        Log::info('Image preprocessed with GD for OCR');
    }

    /**
     * Extract text from an image with enhanced OCR settings for cursive/stylized fonts
     * @param string $imagePath
     * @return string
     */
    private function extractFromImage(string $imagePath): string
    {
        // First attempt with default settings
        $ocr = new TesseractOCR($imagePath);
        $text = $ocr->run();
        
        // If text is minimal or low quality, try enhanced settings for cursive/stylized fonts
        if (strlen(trim($text)) < 10 || $this->isLowQualityText($text)) {
            Log::info('First OCR attempt yielded minimal text, trying enhanced settings');
            
            // Try with enhanced settings for stylized fonts
            $ocr = new TesseractOCR($imagePath);
            
            // Use multiple PSM (Page Segmentation Mode) options
            $psmModes = [
                3,  // Fully automatic page segmentation, but no OSD (default)
                6,  // Assume a single uniform block of text
                7,  // Treat the image as a single text line
                8,  // Treat the image as a single word
                13  // Raw line. Treat the image as a single text line, bypassing hacks
            ];
            
            $bestText = '';
            $bestScore = 0;
            
            foreach ($psmModes as $psm) {
                try {
                    $tempOcr = new TesseractOCR($imagePath);
                    $tempOcr->psm($psm);
                    
                    // Try different OCR Engine Modes
                    $oems = [
                        0, // Legacy engine only
                        1, // Neural nets LSTM engine only
                        2, // Legacy + LSTM engines
                        3  // Default, based on what is available
                    ];
                    
                    foreach ($oems as $oem) {
                        $tempOcr2 = new TesseractOCR($imagePath);
                        $tempOcr2->psm($psm)->oem($oem);
                        
                        // Add configuration for better cursive/stylized text recognition
                        $tempOcr2->configVar('tessedit_char_whitelist', 
                            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.,:-/()[]{}');
                        
                        $tempText = $tempOcr2->run();
                        $score = $this->calculateTextQuality($tempText);
                        
                        if ($score > $bestScore) {
                            $bestText = $tempText;
                            $bestScore = $score;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('OCR PSM mode failed', ['psm' => $psm, 'error' => $e->getMessage()]);
                    continue;
                }
            }
            
            // If enhanced OCR found better text, use it
            if (strlen(trim($bestText)) > strlen(trim($text))) {
                $text = $bestText;
                Log::info('Enhanced OCR produced better results', ['original_length' => strlen(trim($text)), 'enhanced_length' => strlen(trim($bestText))]);
            }
        }
        
        // Apply post-processing to improve cursive text recognition
        $text = $this->postProcessOcrText($text);
        
        Log::info('OCR Text Extracted from Image', ['text' => $text]);
        return $text;
    }
    
    /**
     * Check if OCR text appears to be low quality
     * @param string $text
     * @return bool
     */
    private function isLowQualityText(string $text): bool
    {
        $text = trim($text);
        
        if (strlen($text) < 5) {
            return true;
        }
        
        // Check for excessive special characters or gibberish
        $specialCharCount = preg_match_all('/[^a-zA-Z0-9\s.,:-]/', $text);
        $totalChars = strlen($text);
        
        if ($totalChars > 0 && ($specialCharCount / $totalChars) > 0.3) {
            return true;
        }
        
        // Check for common OCR errors with cursive text
        $errorPatterns = [
            '/[il1|]{3,}/', // Multiple consecutive similar characters
            '/[^\w\s.,:-]{2,}/', // Multiple consecutive special characters
            '/\s{3,}/', // Excessive spacing
        ];
        
        foreach ($errorPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Calculate text quality score
     * @param string $text
     * @return int
     */
    private function calculateTextQuality(string $text): int
    {
        $score = 0;
        $text = trim($text);
        
        // Length bonus
        $score += min(strlen($text), 100);
        
        // Word count bonus
        $words = str_word_count($text);
        $score += $words * 2;
        
        // Penalty for special characters
        $specialChars = preg_match_all('/[^a-zA-Z0-9\s.,:-]/', $text);
        $score -= $specialChars * 3;
        
        // Bonus for common document words
        $commonWords = ['certificate', 'name', 'date', 'born', 'course', 'university', 'college'];
        foreach ($commonWords as $word) {
            if (stripos($text, $word) !== false) {
                $score += 10;
            }
        }
        
        return max(0, $score);
    }
    
    /**
     * Post-process OCR text to fix common cursive recognition errors
     * @param string $text
     * @return string
     */
    private function postProcessOcrText(string $text): string
    {
        // Common cursive OCR corrections
        $corrections = [
            // Common letter confusions in cursive
            '/(\w)rn/i' => '$1m',     // 'rn' often misread as 'm'
            '/(\w)nn/i' => '$1m',     // 'nn' often misread as 'm'
            '/(\w)uu/i' => '$1w',     // 'uu' often misread as 'w'
            '/(\w)ii/i' => '$1u',     // 'ii' often misread as 'u'
            '/(\w)ri/i' => '$1n',     // 'ri' often misread as 'n'
            
            // Fix common spacing issues
            '/([a-z])([A-Z])/' => '$1 $2', // Add space before capitals
            '/\s+/' => ' ',            // Multiple spaces to single space
            
            // Fix common punctuation issues
            '/\s+([,.;:])/' => '$1',   // Remove space before punctuation
            '/([,.;:])\s*([a-zA-Z])/' => '$1 $2', // Ensure space after punctuation
        ];
        
        foreach ($corrections as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }
        
        return trim($text);
    }

    /**
     * Extract text from a PDF
     * @param string $pdfPath
     * @return string
     */
    private function extractFromPdf(string $pdfPath): string
    {
        $text = (new Pdf())
            ->setPdf($pdfPath)
            ->text();
            
        Log::info('OCR Text Extracted from PDF', ['text' => $text]);
        return $text;
    }

    /**
     * Validate user name against document
     * @param string $firstName
     * @param string $lastName
     * @param string $extractedText
     * @return bool
     */
    public function validateName(string $firstName, string $lastName, string $extractedText): bool
    {
        $extractedText = strtolower($extractedText);
        $firstName = strtolower($firstName);
        $lastName = strtolower($lastName);

        return strpos($extractedText, $firstName) !== false && 
               strpos($extractedText, $lastName) !== false;
    }

    /**
     * Validate if user's name appears in the document
     */
    public function validateUserName($ocrText, $firstName, $lastName)
    {
        $text = strtolower($ocrText);
        $firstName = strtolower($firstName);
        $lastName = strtolower($lastName);
        
        // Check for exact matches and allow some variations
        $patterns = [
            $firstName . ' ' . $lastName,
            $lastName . ' ' . $firstName,
            $firstName . ', ' . $lastName,
            $lastName . ', ' . $firstName,
        ];
        
        foreach ($patterns as $pattern) {
            if (strpos($text, $pattern) !== false) {
                return true;
            }
        }
        
        // Allow small typos using Levenshtein distance
        $fullName = $firstName . ' ' . $lastName;
        $words = explode(' ', $text);
        
        for ($i = 0; $i < count($words) - 1; $i++) {
            $nameCandidate = $words[$i] . ' ' . $words[$i + 1];
            if (levenshtein($fullName, $nameCandidate) <= 2) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Extract educational background from document
     * @param string $text
     * @return array
     */
    public function extractEducationalBackground(string $text): array
    {
        $text = strtolower($text);
        $background = [];

        // Common educational keywords
        $keywords = [
            'course' => ['course:', 'program:', 'degree:'],
            'school' => ['university:', 'college:', 'institute:'],
            'year' => ['year:', 'batch:', 'graduated:']
        ];

        foreach ($keywords as $type => $searchTerms) {
            foreach ($searchTerms as $term) {
                if (strpos($text, $term) !== false) {
                    $pos = strpos($text, $term);
                    $endPos = strpos($text, "\n", $pos);
                    if ($endPos === false) $endPos = strlen($text);
                    $value = trim(substr($text, $pos + strlen($term), $endPos - $pos - strlen($term)));
                    $background[$type] = $value;
                    break;
                }
            }
        }

        return $background;
    }

    /**
     * Validate document type based on expected keywords
     */
    public function validateDocumentType($ocrText, $documentType)
    {
        $text = strtolower($ocrText);
        
        // Normalize diploma to Cert_of_Grad for validation
        if ($documentType === 'diploma') {
            $documentType = 'Cert_of_Grad';
        }
        
        // Get dynamic keywords from education level document requirements
        $dynamicKeywords = $this->getDynamicDocumentKeywords($documentType);
        
        // Fallback hardcoded keywords for common document types
        $fallbackKeywords = [
            'PSA' => [
                'philippine statistics authority',
                'psa',
                'birth certificate',
                'certificate of live birth',
                'republic of the philippines'
            ],
            'good_moral' => [
                'certificate of good moral',
                'good moral character',
                'moral character',
                'certificate of character',
                'good moral certificate'
            ],
            'Course_Cert' => [
                'certificate of completion',
                'course certificate',
                'certificate of training',
                'training certificate',
                'completion certificate'
            ],
            'TOR' => [
                'transcript of records',
                'tor',
                'official transcript',
                'academic transcript',
                'transcript'
            ],
            'Cert_of_Grad' => [
                'graduation',
                'diploma',
                'certificate',
                'master of',
                'doctor of',
                'bachelor of',
                'valedictorian',
                'summa cum laude',
                'magna cum laude',
                'cum laude',
                'graduate',
                'graduated',
                'degree of',
                'master\'s degree',
                'doctoral degree',
                'bachelor\'s degree',
                'phd',
                'ms ',
                'ma ',
                'md ',
                'university',
                'college'
            ],
            'school_id' => [
                'student id',
                'identification',
                'id card',
                'student card',
                'school id'
            ]
        ];
        
        // Use dynamic keywords if available, otherwise use fallback
        $keywords = !empty($dynamicKeywords) ? array_merge($dynamicKeywords, $fallbackKeywords[$documentType] ?? []) : ($fallbackKeywords[$documentType] ?? []);
        
        if (empty($keywords)) {
            return true; // If no keywords defined, allow any document
        }
        
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get dynamic document keywords from education level requirements
     */
    private function getDynamicDocumentKeywords($documentType)
    {
        try {
            // Get all education levels with their document requirements
            $educationLevels = \App\Models\EducationLevel::where('is_active', true)->get();
            
            $keywords = [];
            foreach ($educationLevels as $level) {
                $fileRequirements = $level->file_requirements ?? [];
                
                foreach ($fileRequirements as $requirement) {
                    $reqDocType = $requirement['document_type'] ?? null;
                    
                    // Normalize for comparison
                    if ($reqDocType === 'diploma') {
                        $reqDocType = 'Cert_of_Grad';
                    }
                    
                    if ($reqDocType === $documentType) {
                        // Extract keywords from custom names or document type names
                        $customName = $requirement['custom_name'] ?? null;
                        if ($customName) {
                            $keywords[] = strtolower($customName);
                        }
                        
                        // Add document type name as keyword
                        $keywords[] = strtolower(str_replace('_', ' ', $documentType));
                    }
                }
            }
            
            return array_unique($keywords);
        } catch (\Exception $e) {
            \Log::warning('Failed to get dynamic document keywords: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Extract keywords from OCR text for program suggestion
     */
    public function extractKeywords($ocrText)
    {
        $text = strtolower($ocrText);
        
        // Remove common words
        $stopWords = [
            'the', 'of', 'in', 'and', 'or', 'but', 'for', 'with', 'to', 'from',
            'at', 'by', 'on', 'up', 'as', 'an', 'a', 'is', 'was', 'are', 'were',
            'certificate', 'degree', 'bachelor', 'master', 'doctor', 'university',
            'college', 'school', 'major', 'minor', 'concentration', 'specialization'
        ];
        
        // Extract meaningful terms
        preg_match_all('/\b[a-z]{3,}\b/', $text, $matches);
        $words = $matches[0];
        
        // Filter out stop words
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return !in_array($word, $stopWords);
        });
        
        // Extract specific program-related terms
        $programTerms = [
            'engineering', 'engineer', 'civil', 'mechanical', 'electrical', 'chemical',
            'nursing', 'nurse', 'medicine', 'medical', 'health', 'care',
            'culinary', 'cooking', 'chef', 'food', 'nutrition', 'hospitality',
            'business', 'management', 'administration', 'finance', 'accounting',
            'computer', 'information', 'technology', 'programming', 'software',
            'education', 'teaching', 'teacher', 'elementary', 'secondary',
            'psychology', 'social', 'work', 'counseling', 'therapy',
            'mathematics', 'physics', 'chemistry', 'biology', 'science'
        ];
        
        // Add program-specific terms found in text
        foreach ($programTerms as $term) {
            if (strpos($text, $term) !== false) {
                $keywords[] = $term;
            }
        }
        
        return array_unique($keywords);
    }

    /**
     * Suggest programs based on OCR extracted keywords
     */
    public function suggestPrograms($ocrText)
    {
        $keywords = $this->extractKeywords($ocrText);
        
        if (empty($keywords)) {
            return [];
        }
        
        $programs = Program::where('is_archived', 0)->get();
        $modules = Module::where('is_archived', 0)->get()->groupBy('program_id');
        
        $suggestions = [];
        
        foreach ($programs as $program) {
            $score = 0;
            
            // Check program name and description
            foreach ($keywords as $keyword) {
                if (stripos($program->program_name, $keyword) !== false) {
                    $score += 3; // Higher weight for program name matches
                }
                if ($program->program_description && stripos($program->program_description, $keyword) !== false) {
                    $score += 2;
                }
            }
            
            // Check related modules
            if (isset($modules[$program->program_id])) {
                foreach ($modules[$program->program_id] as $module) {
                    foreach ($keywords as $keyword) {
                        if (stripos($module->module_name, $keyword) !== false) {
                            $score += 1;
                        }
                        if ($module->module_description && stripos($module->module_description, $keyword) !== false) {
                            $score += 1;
                        }
                    }
                }
            }
            
            if ($score > 0) {
                $suggestions[] = [
                    'program' => $program,
                    'score' => $score,
                    'matching_keywords' => array_filter($keywords, function($keyword) use ($program, $modules) {
                        $match = stripos($program->program_name, $keyword) !== false ||
                                ($program->program_description && stripos($program->program_description, $keyword) !== false);
                        
                        if (!$match && isset($modules[$program->program_id])) {
                            foreach ($modules[$program->program_id] as $module) {
                                if (stripos($module->module_name, $keyword) !== false ||
                                    ($module->module_description && stripos($module->module_description, $keyword) !== false)) {
                                    $match = true;
                                    break;
                                }
                            }
                        }
                        
                        return $match;
                    })
                ];
            }
        }
        
        // Sort by score (highest first)
        usort($suggestions, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        return array_slice($suggestions, 0, 3); // Return top 3 suggestions
    }


    /**
     * Get document type validation error message (dynamic based on education levels)
     */
    public function getDocumentTypeError($documentType)
    {
        // Normalize diploma to Cert_of_Grad for error messages
        if ($documentType === 'diploma') {
            $documentType = 'Cert_of_Grad';
        }
        
        // Try to get dynamic error message from education level requirements
        $dynamicMessage = $this->getDynamicDocumentErrorMessage($documentType);
        if ($dynamicMessage) {
            return $dynamicMessage;
        }
        
        // Fallback error messages
        $messages = [
            'PSA' => 'Please upload a valid PSA Birth Certificate.',
            'good_moral' => 'Please upload a valid Certificate of Good Moral Character.',
            'Course_Cert' => 'Please upload a valid Course Certificate.',
            'TOR' => 'Please upload a valid Transcript of Records.',
            'Cert_of_Grad' => 'Please upload a valid Certificate of Graduation or Diploma.',
            'school_id' => 'Please upload a valid School ID.',
        ];
        
        return $messages[$documentType] ?? 'Please upload a valid document for this field.';
    }

    /**
     * Get dynamic error message from education level document requirements
     */
    private function getDynamicDocumentErrorMessage($documentType)
    {
        try {
            $educationLevels = \App\Models\EducationLevel::where('is_active', true)->get();
            
            foreach ($educationLevels as $level) {
                $fileRequirements = $level->file_requirements ?? [];
                
                foreach ($fileRequirements as $requirement) {
                    $reqDocType = $requirement['document_type'] ?? null;
                    
                    // Normalize for comparison
                    if ($reqDocType === 'diploma') {
                        $reqDocType = 'Cert_of_Grad';
                    }
                    
                    if ($reqDocType === $documentType) {
                        $customName = $requirement['custom_name'] ?? null;
                        if ($customName) {
                            return "Please upload a valid {$customName}.";
                        }
                        
                        // Use document type as fallback
                        $displayName = str_replace('_', ' ', ucwords($documentType));
                        return "Please upload a valid {$displayName}.";
                    }
                }
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::warning('Failed to get dynamic document error message: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Analyze certificate level from OCR text
     */
    public function analyzeCertificateLevel($ocrText)
    {
        $text = strtolower($ocrText);
        
        // Define certificate level keywords
        $levels = [
            'undergraduate' => [
                'bachelor', 'undergraduate', 'college diploma', 'associate',
                'bachelor of', 'bs ', 'ba ', 'ab ', 'bsed', 'bsit', 'bscs'
            ],
            'graduate' => [
                'master', 'graduate', 'master of', 'ms ', 'ma ', 'mba', 'med',
                'master\'s degree', 'graduate degree'
            ],
            'doctoral' => [
                'doctor', 'doctoral', 'phd', 'doctorate', 'doctor of', 'dds',
                'md ', 'doctoral degree', 'ph.d'
            ],
            'professional' => [
                'professional', 'license', 'board exam', 'licensure',
                'certified', 'registration', 'prc'
            ]
        ];
        
        $scores = [];
        foreach ($levels as $level => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    $score++;
                }
            }
            if ($score > 0) {
                $scores[$level] = $score;
            }
        }
        
        if (empty($scores)) {
            return 'unknown';
        }
        
        // Return the level with highest score
        arsort($scores);
        return array_key_first($scores);
    }

    /**
     * Enhanced name extraction with flexible formatting
     */
    public function extractName($ocrText)
    {
        $text = trim($ocrText);
        $lines = explode("\n", $text);
        
        $namePatterns = [
            // Pattern for "Name: John Doe"
            '/(?:name|student|applicant):\s*([A-Za-z\s,\.]+)/i',
            // Pattern for names in all caps
            '/\b([A-Z][A-Z\s,\.]{10,})\b/',
            // Pattern for formal name format
            '/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]*\.?\s*)*[A-Z][a-z]+)\b/'
        ];
        
        foreach ($lines as $line) {
            foreach ($namePatterns as $pattern) {
                if (preg_match($pattern, $line, $matches)) {
                    $extractedName = trim($matches[1]);
                    if (strlen($extractedName) > 5) { // Reasonable name length
                        return $this->parseNameFormat($extractedName);
                    }
                }
            }
        }
        
        return null;
    }

    /**
     * Parse different name formats into first, middle, last
     */
    public function parseNameFormat($fullName)
    {
        $fullName = trim($fullName);
        $fullName = preg_replace('/[,\.]+/', ' ', $fullName);
        $fullName = preg_replace('/\s+/', ' ', $fullName);
        
        $parts = explode(' ', $fullName);
        $parts = array_filter($parts, function($part) {
            return strlen(trim($part)) > 0;
        });
        $parts = array_values($parts);
        
        if (count($parts) < 2) {
            return null;
        }
        
        $result = [
            'first_name' => '',
            'middle_name' => '',
            'last_name' => ''
        ];
        
        // Check if it's "Last, First Middle" format
        if (strpos($fullName, ',') !== false) {
            $commaParts = explode(',', $fullName);
            if (count($commaParts) >= 2) {
                $result['last_name'] = trim($commaParts[0]);
                $remainingParts = explode(' ', trim($commaParts[1]));
                $result['first_name'] = trim($remainingParts[0] ?? '');
                $result['middle_name'] = trim($remainingParts[1] ?? '');
                return $result;
            }
        }
        
        // Handle "First Middle Last" format
        if (count($parts) == 2) {
            $result['first_name'] = $parts[0];
            $result['last_name'] = $parts[1];
        } elseif (count($parts) == 3) {
            $result['first_name'] = $parts[0];
            $result['middle_name'] = $parts[1];
            $result['last_name'] = $parts[2];
        } elseif (count($parts) > 3) {
            // Assume first is first name, last is last name, middle parts are middle name
            $result['first_name'] = $parts[0];
            $result['last_name'] = $parts[count($parts) - 1];
            $result['middle_name'] = implode(' ', array_slice($parts, 1, -1));
        }
        
        return $result;
    }

    /**
     * Enhanced document type validation with better keyword matching
     */
    public function validateDocumentTypeEnhanced($ocrText, $documentType)
    {
        $text = strtolower($ocrText);
        
        // Normalize diploma to Cert_of_Grad for validation
        if ($documentType === 'diploma') {
            $documentType = 'Cert_of_Grad';
        }
        
        $documentKeywords = [
            'PSA' => [
                'priority' => ['philippine statistics authority', 'psa', 'birth certificate'],
                'secondary' => ['certificate of live birth', 'republic of the philippines', 'civil registrar']
            ],
            'good_moral' => [
                'priority' => ['certificate of good moral', 'good moral character'],
                'secondary' => ['moral character', 'certificate of character', 'conduct']
            ],
            'Course_Cert' => [
                'priority' => ['certificate of completion', 'course certificate'],
                'secondary' => ['certificate of training', 'training certificate', 'completion']
            ],
            'TOR' => [
                'priority' => ['transcript of records', 'tor', 'official transcript'],
                'secondary' => ['academic transcript', 'student records', 'grades']
            ],
            'Cert_of_Grad' => [
                'priority' => ['certificate of graduation', 'diploma', 'graduation', 'certificate', 'degree'],
                'secondary' => ['graduate', 'graduated', 'degree conferred', 'conferment', 'bachelor', 'master', 'bachelor of', 'master of']
            ]
        ];
        
        if (!isset($documentKeywords[$documentType])) {
            return ['valid' => true, 'confidence' => 0];
        }
        
        $keywords = $documentKeywords[$documentType];
        $confidence = 0;
        
        // Check priority keywords (higher weight)
        foreach ($keywords['priority'] as $keyword) {
            if (strpos($text, $keyword) !== false) {
                $confidence += 3;
            }
        }
        
        // Check secondary keywords (lower weight)
        foreach ($keywords['secondary'] as $keyword) {
            if (strpos($text, $keyword) !== false) {
                $confidence += 1;
            }
        }
        
        $isValid = $confidence >= 2; // Require at least moderate confidence
        
        return [
            'valid' => $isValid,
            'confidence' => $confidence,
            'type' => $documentType
        ];
    }
}
