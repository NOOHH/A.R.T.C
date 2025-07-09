<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\DB;

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
        if (!$fileType) {
            $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        }

        if (in_array($fileType, ['pdf'])) {
            return $this->extractFromPdf($filePath);
        }

        return $this->extractFromImage($filePath);
    }

    /**
     * Extract text from an image
     * @param string $imagePath
     * @return string
     */
    private function extractFromImage(string $imagePath): string
    {
        return (new TesseractOCR($imagePath))->run();
    }

    /**
     * Extract text from a PDF
     * @param string $pdfPath
     * @return string
     */
    private function extractFromPdf(string $pdfPath): string
    {
        return (new Pdf())
            ->setPdf($pdfPath)
            ->text();
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
     * Suggest programs based on extracted text and available programs
     * @param string $text
     * @return array
     */
    public function suggestPrograms(string $text): array
    {
        $text = strtolower($text);
        $suggestions = [];
        
        // Get all active programs from database
        $programs = DB::table('programs')
            ->where('status', 'active')
            ->get();

        // Extract educational background
        $background = $this->extractEducationalBackground($text);
        
        foreach ($programs as $program) {
            $score = 0;
            
            // Check program name relevance
            if (strpos(strtolower($program->name), $background['course'] ?? '') !== false) {
                $score += 3;
            }
            
            // Check modules content relevance
            $modules = DB::table('modules')
                ->where('program_id', $program->id)
                ->where('status', 'active')
                ->get();
                
            foreach ($modules as $module) {
                if (strpos($text, strtolower($module->name)) !== false) {
                    $score += 1;
                }
                if (strpos($text, strtolower($module->description)) !== false) {
                    $score += 1;
                }
            }
            
            if ($score > 0) {
                $suggestions[] = [
                    'program' => $program,
                    'score' => $score,
                    'reason' => 'Based on your ' . ($background['course'] ?? 'educational') . ' background'
                ];
            }
        }
        
        // Sort by relevance score
        usort($suggestions, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        return array_slice($suggestions, 0, 3); // Return top 3 suggestions
    }
}
