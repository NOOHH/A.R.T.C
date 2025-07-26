<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuizApiService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = 'hT3HqPH5nfI86Ixt8sOisvZqgF7SMNaGyVcAeyTP';
        $this->baseUrl = 'https://quizapi.io/api/v1';
    }

    /**
     * Test connection to QuizAPI
     */
    public function testConnection()
    {
        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/questions', [
                'apiKey' => $this->apiKey,
                'limit' => 1
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('QuizAPI connection test failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Determine if QuizAPI should be used for a given topic
     */
    public function shouldUseQuizApi($topic)
    {
        if (empty($topic)) {
            return false;
        }

        $topic = strtolower($topic);
        
        // Technical keywords that indicate QuizAPI should be used
        $technicalKeywords = [
            'linux', 'devops', 'programming', 'javascript', 'python', 'php', 'java',
            'docker', 'kubernetes', 'aws', 'cloud', 'sql', 'database', 'git',
            'react', 'vue', 'angular', 'node', 'html', 'css', 'bash', 'shell',
            'api', 'rest', 'json', 'xml', 'http', 'tcp', 'networking', 'security',
            'encryption', 'algorithm', 'data structure', 'oop', 'mvc', 'framework',
            'laravel', 'symfony', 'express', 'django', 'spring', 'test', 'testing',
            'unit test', 'integration', 'deployment', 'ci/cd', 'agile', 'scrum'
        ];

        foreach ($technicalKeywords as $keyword) {
            if (strpos($topic, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get available quiz categories
     */
    public function getCategories()
    {
        return [
            'linux' => 'Linux',
            'bash' => 'Bash',
            'uncategorized' => 'Programming',
            'docker' => 'Docker',
            'sql' => 'SQL',
            'cms' => 'CMS',
            'code' => 'Code',
            'devops' => 'DevOps'
        ];
    }

    /**
     * Get questions from QuizAPI
     */
    public function getQuestions($params = [])
    {
        try {
            $defaultParams = [
                'apiKey' => $this->apiKey,
                'limit' => 10,
                'difficulty' => 'Easy'
            ];

            $requestParams = array_merge($defaultParams, $params);

            Log::info('QuizAPI request', ['params' => $requestParams]);

            $response = Http::timeout(30)->get($this->baseUrl . '/questions', $requestParams);

            if (!$response->successful()) {
                Log::error('QuizAPI request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $questions = $response->json();

            if (empty($questions)) {
                Log::warning('QuizAPI returned no questions', ['params' => $requestParams]);
                return [];
            }

            return $this->transformQuestions($questions);

        } catch (\Exception $e) {
            Log::error('QuizAPI getQuestions failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Transform QuizAPI questions to our format
     */
    private function transformQuestions($apiQuestions)
    {
        $transformedQuestions = [];

        foreach ($apiQuestions as $apiQuestion) {
            if (empty($apiQuestion['question'])) {
                continue;
            }

            // Build options array from the 'answers' field
            $options = [];
            $correctAnswer = null;

            if (!empty($apiQuestion['answers'])) {
                $answerKeys = ['a', 'b', 'c', 'd', 'e', 'f'];
                
                foreach ($answerKeys as $key) {
                    $answerField = 'answer_' . $key;
                    if (!empty($apiQuestion['answers'][$answerField])) {
                        $options[$key] = $apiQuestion['answers'][$answerField];
                    }
                }
            }

            // Find correct answer
            if (!empty($apiQuestion['correct_answer'])) {
                $correctAnswer = $apiQuestion['correct_answer'];
            } elseif (!empty($apiQuestion['correct_answers'])) {
                // Find the correct answer from the correct_answers object
                $answerKeys = ['a', 'b', 'c', 'd', 'e', 'f'];
                foreach ($answerKeys as $key) {
                    $correctField = 'answer_' . $key . '_correct';
                    if (!empty($apiQuestion['correct_answers'][$correctField]) && 
                        $apiQuestion['correct_answers'][$correctField] === 'true') {
                        $correctAnswer = $key;
                        break;
                    }
                }
            }

            if (empty($options) || empty($correctAnswer)) {
                Log::warning('Skipping question due to missing options or correct answer', [
                    'question' => substr($apiQuestion['question'], 0, 50),
                    'options_count' => count($options),
                    'correct_answer' => $correctAnswer
                ]);
                continue;
            }

            $transformedQuestions[] = [
                'question' => $apiQuestion['question'],
                'options' => $options,
                'correct_answer' => $correctAnswer,
                'difficulty' => $apiQuestion['difficulty'] ?? 'Easy',
                'category' => $apiQuestion['category'] ?? 'Programming',
                'explanation' => $apiQuestion['explanation'] ?? '',
                'question_type' => 'multiple_choice'
            ];
        }

        Log::info('Transformed QuizAPI questions', [
            'original_count' => count($apiQuestions),
            'transformed_count' => count($transformedQuestions)
        ]);

        return $transformedQuestions;
    }

    /**
     * Detect category from topic
     */
    public function detectCategory($topic)
    {
        $topic = strtolower($topic);
        
        $categoryMap = [
            'linux' => 'linux',
            'bash' => 'bash', 
            'shell' => 'bash',
            'docker' => 'docker',
            'sql' => 'sql',
            'database' => 'sql',
            'mysql' => 'sql',
            'postgresql' => 'sql',
            'devops' => 'devops',
            'javascript' => 'uncategorized',
            'python' => 'uncategorized',
            'php' => 'uncategorized',
            'java' => 'uncategorized',
            'programming' => 'uncategorized',
            'code' => 'code'
        ];

        foreach ($categoryMap as $keyword => $category) {
            if (strpos($topic, $keyword) !== false) {
                return $category;
            }
        }

        return 'uncategorized'; // Default to programming
    }

    /**
     * Generate quiz questions using QuizAPI
     */
    public function generateQuizQuestions($topic, $numQuestions = 10, $difficulty = 'Easy')
    {
        try {
            $category = $this->detectCategory($topic);
            
            $params = [
                'limit' => $numQuestions,
                'category' => $category,
                'difficulty' => $difficulty
            ];

            Log::info('Generating quiz from QuizAPI', [
                'topic' => $topic,
                'category' => $category,
                'params' => $params
            ]);

            $questions = $this->getQuestions($params);

            if (empty($questions)) {
                // Try without category filter
                Log::info('Retrying QuizAPI without category filter');
                unset($params['category']);
                $questions = $this->getQuestions($params);
            }

            if (empty($questions)) {
                // Try with easier difficulty
                Log::info('Retrying QuizAPI with Easy difficulty');
                $params['difficulty'] = 'Easy';
                $questions = $this->getQuestions($params);
            }

            return $questions;

        } catch (\Exception $e) {
            Log::error('QuizAPI generateQuizQuestions failed', [
                'topic' => $topic,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
