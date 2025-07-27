<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing quiz records to use Gemini instead of QuizAPI
        // Check if question_source column exists before updating
        if (Schema::hasColumn('quiz_questions', 'question_source')) {
            DB::table('quiz_questions')
                ->where('question_source', 'quizapi')
                ->update(['question_source' => 'gemini']);
        }
        
        // Update quiz descriptions to reference Gemini instead of QuizAPI
        DB::table('quizzes')
            ->where('quiz_description', 'like', '%QuizAPI%')
            ->update(['quiz_description' => DB::raw("REPLACE(quiz_description, 'QuizAPI', 'Google Gemini AI')")]);
            
        // Update question_metadata to reference Gemini
        if (Schema::hasColumn('quiz_questions', 'question_metadata')) {
            $questions = DB::table('quiz_questions')
                ->whereNotNull('question_metadata')
                ->get();
                
            foreach ($questions as $question) {
                $metadata = json_decode($question->question_metadata, true);
                if (is_array($metadata)) {
                    // Update any QuizAPI references to Gemini
                    if (isset($metadata['source']) && $metadata['source'] === 'quizapi') {
                        $metadata['source'] = 'gemini';
                        $metadata['ai_service'] = 'google_gemini';
                    }
                    if (isset($metadata['category']) && $metadata['category'] === 'QuizAPI') {
                        $metadata['category'] = 'AI Generated';
                    }
                    
                    DB::table('quiz_questions')
                        ->where('id', $question->id)
                        ->update(['question_metadata' => json_encode($metadata)]);
                }
            }
        }
        
        // Add a note in quiz_description for AI-generated quizzes
        DB::table('quizzes')
            ->whereNull('quiz_description')
            ->orWhere('quiz_description', '')
            ->update(['quiz_description' => 'Generated using Google Gemini AI']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Gemini back to QuizAPI
        if (Schema::hasColumn('quiz_questions', 'question_source')) {
            DB::table('quiz_questions')
                ->where('question_source', 'gemini')
                ->update(['question_source' => 'quizapi']);
        }
            
        DB::table('quizzes')
            ->where('quiz_description', 'like', '%Gemini%')
            ->update(['quiz_description' => DB::raw("REPLACE(quiz_description, 'Google Gemini AI', 'QuizAPI')")]);
    }
};
