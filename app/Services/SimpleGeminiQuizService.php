<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use \Exception;

class SimpleGeminiQuizService
{
    protected $apiKey;
    protected $apiEndpoint = 'https://generativelanguage.googleapis.com/v1';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        
        if (empty($this->apiKey)) {
            Log::error('Gemini API key is not configured');
        }
    }

    /**
     * Generate quiz questions using a simple, working approach
     */
    public function generateQuiz($content, $numMcq = 5, $numTf = 3)
    {
        try {
            if (empty($this->apiKey)) {
                throw new Exception('Gemini API key is not configured');
            }

            if (strlen(trim($content)) < 100) {
                throw new Exception('Content too short to generate meaningful questions');
            }

            // Fix UTF-8 encoding issues
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
            $content = iconv('UTF-8', 'UTF-8//IGNORE', $content);
            $content = preg_replace('/[^\x20-\x7E\xA0-\xFF]/u', ' ', $content);
            $content = preg_replace('/\s+/', ' ', trim($content));

            // Create a simple, effective prompt
            $prompt = "Based on the following content, generate exactly {$numMcq} multiple choice questions and {$numTf} true/false questions.

Content: {$content}

Instructions:
1. Create {$numMcq} multiple choice questions with 4 options (A, B, C, D)
2. Create {$numTf} true/false statements
3. Use this exact format:

MCQ1: Question text?
A. Option 1
B. Option 2
C. Option 3
D. Option 4
Answer: [Letter]

TF1: Statement text
Answer: [True/False]

Make questions specific and test understanding of the content provided.";

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
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2000
                ]
            ];

            $response = Http::timeout(60)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->apiEndpoint}/models/gemini-1.5-flash:generateContent?key={$this->apiKey}", $payload);

            if ($response->failed()) {
                throw new Exception('API request failed: ' . $response->status() . ' - ' . $response->body());
            }

            $result = $response->json();
            $generatedText = '';

            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $generatedText = $result['candidates'][0]['content']['parts'][0]['text'];
            } else {
                throw new Exception('No content in API response');
            }

            // Clean UTF-8 in the API response text
            $generatedText = mb_convert_encoding($generatedText, 'UTF-8', 'UTF-8');
            $generatedText = iconv('UTF-8', 'UTF-8//IGNORE', $generatedText);
            $generatedText = preg_replace('/[^\x20-\x7E\xA0-\xFF]/u', ' ', $generatedText);

            return $this->parseSimpleQuizContent($generatedText);

        } catch (Exception $e) {
            Log::error('Simple Gemini service error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'questions' => []
            ];
        }
    }

    /**
     * Parse the simple quiz format
     */
    private function parseSimpleQuizContent($content)
    {
        $questions = [];
        $lines = explode("\n", $content);
        $currentQuestion = null;
        $currentOptions = [];
        $currentType = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Multiple choice question
            if (preg_match('/^MCQ\d+:\s*(.+\?)$/', $line, $matches)) {
                if ($currentQuestion) {
                    $questions[] = $this->buildQuestion($currentQuestion, $currentOptions, $currentType);
                }
                $currentQuestion = trim($matches[1]);
                $currentOptions = [];
                $currentType = 'multiple_choice';
            }
            // True/False question
            elseif (preg_match('/^TF\d+:\s*(.+)$/', $line, $matches)) {
                if ($currentQuestion) {
                    $questions[] = $this->buildQuestion($currentQuestion, $currentOptions, $currentType);
                }
                $currentQuestion = trim($matches[1]);
                $currentOptions = ['True', 'False'];
                $currentType = 'true_false';
            }
            // Options A, B, C, D
            elseif (preg_match('/^([ABCD])\.\s*(.+)$/', $line, $matches)) {
                $currentOptions[$matches[1]] = trim($matches[2]);
            }
            // Answer
            elseif (preg_match('/^Answer:\s*(.+)$/', $line, $matches)) {
                $answer = trim($matches[1]);
                if ($currentQuestion) {
                    $questions[] = $this->buildQuestion($currentQuestion, $currentOptions, $currentType, $answer);
                    $currentQuestion = null;
                    $currentOptions = [];
                    $currentType = null;
                }
            }
        }

        // Add the last question if exists
        if ($currentQuestion) {
            $questions[] = $this->buildQuestion($currentQuestion, $currentOptions, $currentType);
        }

        return [
            'success' => true,
            'questions' => $questions,
            'count' => count($questions)
        ];
    }

    /**
     * Build a question array
     */
    private function buildQuestion($question, $options, $type, $answer = null)
    {
        if ($type === 'multiple_choice') {
            return [
                'question' => $question,
                'type' => 'multiple_choice',
                'options' => $options,
                'correct_answer' => $answer ?? 'A',
                'explanation' => 'Based on the provided content.'
            ];
        } else {
            return [
                'question' => $question,
                'type' => 'true_false',
                'options' => ['True', 'False'],
                'correct_answer' => $answer ?? 'True',
                'explanation' => 'Based on the provided content.'
            ];
        }
    }
}
