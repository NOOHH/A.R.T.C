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

            Log::info('OCR Text extraction started', [
                'file_path' => $filePath,
                'file_type' => $fileType
            ]);

            if (in_array($fileType, ['pdf'])) {
                $text = $this->extractFromPdf($filePath);
            } else {
                // For images, try preprocessing if available
                $processedPath = $this->preprocessImageForOcr($filePath);
                $text = $this->extractFromImage($processedPath);
                
                // Clean up temporary processed file if it was created
                if ($processedPath !== $filePath && file_exists($processedPath)) {
                    unlink($processedPath);
                }
            }
            
            // Log the extracted text for debugging
            Log::info('OCR Text extraction completed', [
                'text_length' => strlen($text),
                'text_preview' => substr($text, 0, 500),
                'file_type' => $fileType
            ]);
            
            return $text;
        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage(), [
                'file_path' => $filePath,
                'file_type' => $fileType,
                'trace' => $e->getTraceAsString()
            ]);
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
                        
                        // Enhanced character whitelist including special characters like ñ, é, ü, etc.
                        $tempOcr2->configVar('tessedit_char_whitelist', 
                            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.,:-/()[]{}ñÑáéíóúüÁÉÍÓÚÜàèìòùÀÈÌÒÙâêîôûÂÊÎÔÛäëïöüÄËÏÖÜçÇß');
                        
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
            
            // Special character corrections
            '/(\w)ni(\w)/i' => '$1ñ$2',  // 'ni' might be 'ñ'
            '/(\w)ny(\w)/i' => '$1ñ$2',  // 'ny' might be 'ñ'
            '/(\w)ue(\w)/i' => '$1ü$2',  // 'ue' might be 'ü'
            '/(\w)ae(\w)/i' => '$1ä$2',  // 'ae' might be 'ä'
            '/(\w)oe(\w)/i' => '$1ö$2',  // 'oe' might be 'ö'
            '/(\w)ss(\w)/i' => '$1ß$2',  // 'ss' might be 'ß'
            
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
        
        // Handle special character normalization
        $text = $this->normalizeSpecialCharactersInText($text);
        
        return trim($text);
    }
    
    /**
     * Normalize special characters in OCR text
     * @param string $text
     * @return string
     */
    private function normalizeSpecialCharactersInText($text)
    {
        // Common OCR misreadings and their corrections
        $specialCharCorrections = [
            // Spanish/Portuguese characters
            'ni' => 'ñ',  // 'ni' often misread as 'ñ'
            'ny' => 'ñ',  // 'ny' often misread as 'ñ'
            
            // German characters
            'ue' => 'ü',  // 'ue' often misread as 'ü'
            'ae' => 'ä',  // 'ae' often misread as 'ä'
            'oe' => 'ö',  // 'oe' often misread as 'ö'
            'ss' => 'ß',  // 'ss' often misread as 'ß'
            
            // Common OCR errors in cursive text
            'rn' => 'm',  // 'rn' often misread as 'm'
            'nn' => 'm',  // 'nn' often misread as 'm'
            'uu' => 'w',  // 'uu' often misread as 'w'
            'ii' => 'u',  // 'ii' often misread as 'u'
            'ri' => 'n',  // 'ri' often misread as 'n'
        ];
        
        // Apply corrections (but be careful not to over-correct)
        foreach ($specialCharCorrections as $error => $correction) {
            // Only replace if it's likely to be a special character
            // This is a simplified approach - in practice, you might want more context
            $text = str_replace($error, $correction, $text);
        }
        
        return $text;
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
        
        // Normalize special characters for better matching
        $text = $this->normalizeSpecialCharacters($text);
        $firstName = $this->normalizeSpecialCharacters($firstName);
        $lastName = $this->normalizeSpecialCharacters($lastName);
        
        // Log the normalized text for debugging
        Log::info('Name validation - Normalized text', [
            'original_first_name' => $firstName,
            'original_last_name' => $lastName,
            'normalized_first_name' => $firstName,
            'normalized_last_name' => $lastName,
            'text_preview' => substr($text, 0, 200)
        ]);
        
        // Check for exact matches and allow some variations
        $patterns = [
            $firstName . ' ' . $lastName,
            $lastName . ' ' . $firstName,
            $firstName . ', ' . $lastName,
            $lastName . ', ' . $firstName,
        ];
        
        foreach ($patterns as $pattern) {
            if (strpos($text, $pattern) !== false) {
                Log::info('Name validation - Exact pattern match found', ['pattern' => $pattern]);
                return true;
            }
        }
        
        // Check for partial matches (handle OCR errors with special characters)
        $firstNameVariations = $this->generateNameVariations($firstName);
        $lastNameVariations = $this->generateNameVariations($lastName);
        
        Log::info('Name validation - Generated variations', [
            'first_name_variations' => $firstNameVariations,
            'last_name_variations' => $lastNameVariations
        ]);
        
        foreach ($firstNameVariations as $firstNameVar) {
            foreach ($lastNameVariations as $lastNameVar) {
                $fullNamePattern = $firstNameVar . ' ' . $lastNameVar;
                if (strpos($text, $fullNamePattern) !== false) {
                    Log::info('Name validation - Variation pattern match found', ['pattern' => $fullNamePattern]);
                    return true;
                }
            }
        }
        
        // Allow small typos using Levenshtein distance
        $fullName = $firstName . ' ' . $lastName;
        $words = explode(' ', $text);
        
        for ($i = 0; $i < count($words) - 1; $i++) {
            $nameCandidate = $words[$i] . ' ' . $words[$i + 1];
            $distance = levenshtein($fullName, $nameCandidate);
            if ($distance <= 3) { // Increased tolerance for OCR errors
                Log::info('Name validation - Levenshtein match found', [
                    'full_name' => $fullName,
                    'candidate' => $nameCandidate,
                    'distance' => $distance
                ]);
                return true;
            }
        }
        
        // Check for individual name parts (more lenient approach)
        $firstNameFound = false;
        $lastNameFound = false;
        
        foreach ($firstNameVariations as $firstNameVar) {
            if (strpos($text, $firstNameVar) !== false) {
                $firstNameFound = true;
                Log::info('Name validation - First name variation found', ['variation' => $firstNameVar]);
                break;
            }
        }
        
        foreach ($lastNameVariations as $lastNameVar) {
            if (strpos($text, $lastNameVar) !== false) {
                $lastNameFound = true;
                Log::info('Name validation - Last name variation found', ['variation' => $lastNameVar]);
                break;
            }
        }
        
        // Accept if at least one name part is found (very lenient for OCR issues)
        $result = $firstNameFound || $lastNameFound;
        Log::info('Name validation - Final result', [
            'first_name_found' => $firstNameFound,
            'last_name_found' => $lastNameFound,
            'validation_passed' => $result
        ]);
        
        return $result;
    }
    
    /**
     * Normalize special characters for better OCR text matching
     * @param string $text
     * @return string
     */
    private function normalizeSpecialCharacters($text)
    {
        // Common OCR misreadings for special characters
        $normalizations = [
            // Spanish/Portuguese characters
            'ñ' => ['n', 'ni', 'ny'], // ñ might be read as n, ni, or ny
            'á' => ['a', 'a'], // á might be read as a
            'é' => ['e', 'e'], // é might be read as e
            'í' => ['i', 'i'], // í might be read as i
            'ó' => ['o', 'o'], // ó might be read as o
            'ú' => ['u', 'u'], // ú might be read as u
            'ü' => ['u', 'ue'], // ü might be read as u or ue
            
            // German characters
            'ä' => ['a', 'ae'], // ä might be read as a or ae
            'ö' => ['o', 'oe'], // ö might be read as o or oe
            'ß' => ['ss', 'b'], // ß might be read as ss or b
            
            // French characters
            'à' => ['a', 'a'], // à might be read as a
            'è' => ['e', 'e'], // è might be read as e
            'ì' => ['i', 'i'], // ì might be read as i
            'ò' => ['o', 'o'], // ò might be read as o
            'ù' => ['u', 'u'], // ù might be read as u
            'ç' => ['c', 'c'], // ç might be read as c
        ];
        
        // For now, just return the original text
        // The normalization will be handled in generateNameVariations
        return $text;
    }
    
    /**
     * Generate variations of a name to handle OCR errors with special characters
     * @param string $name
     * @return array
     */
    private function generateNameVariations($name)
    {
        $variations = [$name];
        
        // Common OCR misreadings for special characters
        $characterMappings = [
            // Spanish/Portuguese characters
            'ñ' => ['n', 'ni', 'ny', 'n~', 'n^'],
            'á' => ['a', 'a`', 'a^'],
            'é' => ['e', 'e`', 'e^'],
            'í' => ['i', 'i`', 'i^'],
            'ó' => ['o', 'o`', 'o^'],
            'ú' => ['u', 'u`', 'u^'],
            'ü' => ['u', 'ue', 'u"', 'u^'],
            
            // German characters
            'ä' => ['a', 'ae', 'a"', 'a^'],
            'ö' => ['o', 'oe', 'o"', 'o^'],
            'ß' => ['ss', 'b', 's'],
            
            // French characters
            'à' => ['a', 'a`', 'a^'],
            'è' => ['e', 'e`', 'e^'],
            'ì' => ['i', 'i`', 'i^'],
            'ò' => ['o', 'o`', 'o^'],
            'ù' => ['u', 'u`', 'u^'],
            'ç' => ['c', 'c,', 'c^'],
        ];
        
        // Generate variations by replacing special characters
        foreach ($characterMappings as $specialChar => $replacements) {
            if (strpos($name, $specialChar) !== false) {
                foreach ($replacements as $replacement) {
                    $variation = str_replace($specialChar, $replacement, $name);
                    if (!in_array($variation, $variations)) {
                        $variations[] = $variation;
                    }
                }
            }
        }
        
        // Also add variations with common OCR errors
        $ocrErrors = [
            'rn' => 'm', // 'rn' often misread as 'm'
            'nn' => 'm', // 'nn' often misread as 'm'
            'uu' => 'w', // 'uu' often misread as 'w'
            'ii' => 'u', // 'ii' often misread as 'u'
            'ri' => 'n', // 'ri' often misread as 'n'
            'ni' => 'ñ', // 'ni' might be 'ñ'
            'ny' => 'ñ', // 'ny' might be 'ñ'
            'n~' => 'ñ', // 'n~' might be 'ñ'
            'n^' => 'ñ', // 'n^' might be 'ñ'
        ];
        
        foreach ($ocrErrors as $error => $correction) {
            if (strpos($name, $error) !== false) {
                $variation = str_replace($error, $correction, $name);
                if (!in_array($variation, $variations)) {
                    $variations[] = $variation;
                }
            }
        }
        
        // Add case variations
        $caseVariations = [
            strtolower($name),
            strtoupper($name),
            ucfirst(strtolower($name)),
            ucwords(strtolower($name))
        ];
        
        foreach ($caseVariations as $caseVar) {
            if (!in_array($caseVar, $variations)) {
                $variations[] = $caseVar;
            }
        }
        
        Log::info('Generated name variations', [
            'original_name' => $name,
            'variations_count' => count($variations),
            'variations' => $variations
        ]);
        
        return $variations;
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
        
        // Log the input text for debugging
        Log::info('OCR Keywords extraction started', [
            'text_length' => strlen($text),
            'text_preview' => substr($text, 0, 300)
        ]);
        
        // Remove common words but keep important program-related terms
        $stopWords = [
            'the', 'of', 'in', 'and', 'or', 'but', 'for', 'with', 'to', 'from',
            'at', 'by', 'on', 'up', 'as', 'an', 'a', 'is', 'was', 'are', 'were',
            'this', 'that', 'has', 'been', 'awarded', 'given', 'completion',
            'university', 'college', 'school', 'major', 'minor', 'concentration', 'specialization'
        ];
        
        // Extract meaningful terms
        preg_match_all('/\b[a-z]{3,}\b/', $text, $matches);
        $words = $matches[0];
        
        // Filter out stop words
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return !in_array($word, $stopWords);
        });
        
        // Enhanced program-related terms with better detection
        $programTerms = [
            // Healthcare & Nursing (Enhanced)
            'nursing', 'nurse', 'medicine', 'medical', 'health', 'care', 'healthcare',
            'pharmacy', 'dentistry', 'therapy', 'clinical', 'patient', 'hospital',
            'certificate', 'certification', 'diploma', 'degree', 'bachelor', 'master', 'doctor',
            'internship', 'prescribed', 'course', 'study', 'satisfactorily', 'completed',
            
            // Culinary & Food Service
            'culinary', 'cooking', 'chef', 'food', 'nutrition', 'hospitality', 
            'restaurant', 'kitchen', 'baking', 'pastry', 'cuisine', 'gastronomy',
            'catering', 'foodservice', 'culinarys', 'chefs', 'cook', 'baker',
            'sommelier', 'barista', 'patissier', 'culinarian',
            
            // Engineering
            'engineering', 'engineer', 'civil', 'mechanical', 'electrical', 'chemical',
            'industrial', 'aerospace', 'automotive', 'structural',
            
            // Business
            'business', 'management', 'administration', 'finance', 'accounting',
            'marketing', 'economics', 'entrepreneurship', 'commerce', 'trade',
            
            // Technology
            'computer', 'information', 'technology', 'programming', 'software',
            'coding', 'development', 'web', 'database', 'networking', 'cybersecurity',
            'digital', 'systems', 'applications', 'tech',
            
            // Education
            'education', 'teaching', 'teacher', 'elementary', 'secondary',
            'pedagogy', 'curriculum', 'instruction',
            
            // Social Sciences
            'psychology', 'social', 'work', 'counseling', 'therapy',
            'sociology', 'anthropology', 'communications',
            
            // Sciences
            'mathematics', 'physics', 'chemistry', 'biology', 'science',
            'environmental', 'laboratory', 'research',
            
            // Arts & Design
            'art', 'design', 'graphic', 'multimedia', 'photography', 'animation',
            'creative', 'visual', 'digital', 'media', 'advertising'
        ];
        
        // Add program-specific terms found in text
        foreach ($programTerms as $term) {
            if (strpos($text, $term) !== false) {
                $keywords[] = $term;
                
                // Add related terms for better matching
                if (in_array($term, ['nursing', 'nurse', 'medical', 'health', 'care', 'internship', 'prescribed', 'course', 'study', 'satisfactorily', 'completed'])) {
                    $keywords = array_merge($keywords, ['nursing', 'healthcare', 'medical', 'health', 'certificate']);
                }
                if (in_array($term, ['chef', 'cooking', 'culinary', 'food', 'kitchen', 'baking'])) {
                    $keywords = array_merge($keywords, ['culinary', 'chef', 'cooking', 'food', 'hospitality']);
                }
            }
        }
        
        // Special pattern matching for compound terms
        $compoundPatterns = [
            '/nursing\s*certificate/i' => ['nursing', 'healthcare', 'medical', 'certificate'],
            '/certificate\s*of\s*nursing/i' => ['nursing', 'healthcare', 'medical', 'certificate'],
            '/prescribed\s*course\s*of\s*study/i' => ['nursing', 'healthcare', 'medical', 'course', 'study'],
            '/satisfactorily\s*completed/i' => ['nursing', 'healthcare', 'medical', 'completed'],
            '/internship\s*in\s*nursing/i' => ['nursing', 'healthcare', 'medical', 'internship'],
            '/chef\s*certificate/i' => ['chef', 'culinary', 'cooking', 'food'],
            '/culinary\s*arts/i' => ['culinary', 'chef', 'cooking', 'arts'],
            '/food\s*service/i' => ['food', 'culinary', 'hospitality', 'restaurant'],
            '/web\s*development/i' => ['web', 'development', 'programming', 'technology'],
            '/graphic\s*design/i' => ['graphic', 'design', 'art', 'creative'],
            '/business\s*administration/i' => ['business', 'administration', 'management'],
        ];
        
        foreach ($compoundPatterns as $pattern => $terms) {
            if (preg_match($pattern, $text)) {
                $keywords = array_merge($keywords, $terms);
                Log::info('Compound pattern matched', ['pattern' => $pattern, 'terms' => $terms]);
            }
        }
        
        // Log extracted keywords for debugging
        Log::info('OCR Keywords extracted', [
            'original_text_preview' => substr($text, 0, 200),
            'extracted_keywords' => array_unique($keywords),
            'keywords_count' => count(array_unique($keywords))
        ]);
        
        return array_unique($keywords);
    }

    /**
     * Suggest programs based on OCR extracted keywords
     */
    public function suggestPrograms($ocrText)
    {
        $keywords = $this->extractKeywords($ocrText);
        
        if (empty($keywords)) {
            Log::info('No keywords extracted from OCR text for program suggestions');
            return [];
        }
        
        Log::info('Suggesting programs with keywords', ['keywords' => $keywords]);
        
        $programs = Program::where('is_archived', 0)->get();
        $modules = Module::where('is_archived', 0)->get()->groupBy('program_id');
        
        Log::info('Available programs for matching', [
            'total_programs' => $programs->count(),
            'program_names' => $programs->pluck('program_name')->toArray()
        ]);
        
        $suggestions = [];
        
        foreach ($programs as $program) {
            $score = 0;
            $matchingKeywords = [];
            
            // Enhanced scoring system
            foreach ($keywords as $keyword) {
                $keywordLower = strtolower($keyword);
                $programNameLower = strtolower($program->program_name);
                $programDescLower = strtolower($program->program_description ?? '');
                
                // Program name matches (highest weight)
                if (stripos($programNameLower, $keywordLower) !== false) {
                    $score += 5; // Increased weight for program name matches
                    $matchingKeywords[] = $keyword;
                    
                    // Extra bonus for exact nursing matches
                    if (in_array($keywordLower, ['nursing', 'nurse', 'medical', 'health', 'care']) && 
                        strpos($programNameLower, 'nursing') !== false) {
                        $score += 5; // Bonus for nursing matches
                        Log::info('Nursing program match found', [
                            'program_name' => $program->program_name,
                            'keyword' => $keywordLower,
                            'score' => $score
                        ]);
                    }
                    
                    // Extra bonus for exact culinary matches
                    if (in_array($keywordLower, ['chef', 'culinary', 'cooking', 'food']) && 
                        strpos($programNameLower, 'culinary') !== false) {
                        $score += 3; // Bonus for culinary matches
                    }
                }
                
                // Program description matches
                if ($program->program_description && stripos($programDescLower, $keywordLower) !== false) {
                    $score += 3; // Increased weight for description matches
                    $matchingKeywords[] = $keyword;
                }
                
                // Special scoring for related keywords
                if ($this->areKeywordsRelated($keywordLower, $programNameLower)) {
                    $score += 2;
                    $matchingKeywords[] = $keyword;
                }
            }
            
            // Check related modules
            if (isset($modules[$program->program_id])) {
                foreach ($modules[$program->program_id] as $module) {
                    foreach ($keywords as $keyword) {
                        $keywordLower = strtolower($keyword);
                        
                        if (stripos($module->module_name, $keywordLower) !== false) {
                            $score += 1;
                            $matchingKeywords[] = $keyword;
                        }
                        if ($module->module_description && stripos($module->module_description, $keywordLower) !== false) {
                            $score += 1;
                            $matchingKeywords[] = $keyword;
                        }
                    }
                }
            }
            
            if ($score > 0) {
                $suggestions[] = [
                    'program' => $program,
                    'score' => $score,
                    'matching_keywords' => array_unique($matchingKeywords)
                ];
                
                Log::info('Program scored for suggestion', [
                    'program_name' => $program->program_name,
                    'score' => $score,
                    'matching_keywords' => array_unique($matchingKeywords)
                ]);
            }
        }
        
        // Sort by score (highest first)
        usort($suggestions, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        // Return top 5 suggestions instead of 3 for better coverage
        $topSuggestions = array_slice($suggestions, 0, 5);
        
        Log::info('Final program suggestions', [
            'total_suggestions' => count($topSuggestions),
            'suggestions' => array_map(function($s) {
                return [
                    'program_name' => $s['program']['program_name'],
                    'score' => $s['score'],
                    'keywords' => $s['matching_keywords']
                ];
            }, $topSuggestions)
        ]);
        
        return $topSuggestions;
    }
    
    /**
     * Check if keywords are related for better program matching
     */
    private function areKeywordsRelated($keyword, $programName)
    {
        $relations = [
            // Culinary relations
            'chef' => ['culinary', 'food', 'cooking', 'hospitality', 'restaurant'],
            'cooking' => ['culinary', 'chef', 'food', 'kitchen', 'hospitality'],
            'food' => ['culinary', 'chef', 'cooking', 'nutrition', 'hospitality'],
            'baking' => ['culinary', 'pastry', 'food', 'chef'],
            
            // Technology relations
            'programming' => ['computer', 'software', 'development', 'coding', 'web'],
            'web' => ['development', 'programming', 'computer', 'software'],
            'software' => ['computer', 'programming', 'development', 'technology'],
            
            // Business relations
            'management' => ['business', 'administration', 'leadership'],
            'finance' => ['business', 'accounting', 'economics'],
            'marketing' => ['business', 'advertising', 'communications'],
            
            // Healthcare relations
            'nursing' => ['healthcare', 'medical', 'health', 'care'],
            'medical' => ['healthcare', 'nursing', 'health', 'medicine'],
            
            // Art & Design relations
            'design' => ['art', 'creative', 'graphic', 'visual'],
            'graphic' => ['design', 'art', 'visual', 'creative'],
        ];
        
        if (isset($relations[$keyword])) {
            foreach ($relations[$keyword] as $relatedTerm) {
                if (strpos($programName, $relatedTerm) !== false) {
                    return true;
                }
            }
        }
        
        return false;
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
