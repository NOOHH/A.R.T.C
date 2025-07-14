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
     * Extract text from a file (image or PDF)
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

            return $this->extractFromImage($filePath);
        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Extract text from an image
     * @param string $imagePath
     * @return string
     */
    private function extractFromImage(string $imagePath): string
    {
        $ocr = new TesseractOCR($imagePath);
        $text = $ocr->run();
        
        Log::info('OCR Text Extracted from Image', ['text' => $text]);
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
        
        $documentKeywords = [
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
                'master of',
                'doctor of',
                'valedictorian',
                'summa cum laude',
                'magna cum laude',
                'cum laude',
                'graduate',
                'graduated',
                'degree of',
                'master\'s degree',
                'doctoral degree',
                'phd',
                'ms ',
                'ma ',
                'md ',
                'LPU'
            ]
        ];
        
        if (!isset($documentKeywords[$documentType])) {
            return true; // If type not defined, allow it
        }
        
        $keywords = $documentKeywords[$documentType];
        
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
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
     * Get document type validation error message
     */
    public function getDocumentTypeError($documentType)
    {
        $messages = [
            'PSA' => 'Please upload a valid PSA Birth Certificate.',
            'good_moral' => 'Please upload a valid Certificate of Good Moral Character.',
            'Course_Cert' => 'Please upload a valid Course Certificate.',
            'TOR' => 'Please upload a valid Transcript of Records.',
            'Cert_of_Grad' => 'Please upload a valid Certificate of Graduation.',
            'Undergraduate' => 'Please upload a valid Undergraduate Certificate.',
        ];
        
        return $messages[$documentType] ?? 'Please upload a valid document for this field.';
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
                'priority' => ['certificate of graduation', 'diploma', 'graduation'],
                'secondary' => ['graduate', 'graduated', 'degree conferred', 'conferment']
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
