<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrService
{
    /**
     * Extract text from an image using Tesseract OCR.
     * @param string $imagePath
     * @return string
     */
    public function extractText(string $imagePath): string
    {
        return (new TesseractOCR($imagePath))->run();
    }

    /**
     * Suggest programs/subjects based on extracted text.
     * @param string $text
     * @return array
     */
    public function suggestPrograms(string $text): array
    {
        $suggestions = [];
        $text = strtolower($text);
        if (strpos($text, 'nursing') !== false) {
            $suggestions[] = 'Nursing Review Program';
        }
        if (strpos($text, 'engineering') !== false) {
            $suggestions[] = 'Engineering Board Review';
        }
        if (strpos($text, 'accountancy') !== false) {
            $suggestions[] = 'Accountancy Review';
        }
        // Add more rules as needed
        if (empty($suggestions)) {
            $suggestions[] = 'General Review Program';
        }
        return $suggestions;
    }
}
