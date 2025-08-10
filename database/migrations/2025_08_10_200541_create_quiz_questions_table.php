<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizquestionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->bigInteger('quiz_id')->nullable(false);
            $table->string('255')('quiz_title');
            $table->integer('program_id');
            $table->text('question_text')->nullable(false);
            $table->enum(''multiple_choice','true_false','short_answer','essay'')('question_type')->nullable(false);
            $table->integer('question_order');
            $table->text('options');
            $table->text('correct_answer')->default('''');
            $table->text('explanation');
            $table->enum(''generated','manual','quizapi'')('question_source')->nullable(false)->default('generated');
            $table->text('question_metadata');
            $table->text('instructions');
            $table->integer('points')->nullable(false)->default(1);
            $table->string('255')('source_file');
            $table->boolean('is_active')->nullable(false)->default(1);
            $table->integer('created_by_admin');
            $table->integer('created_by_professor');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('quiz_questions');
    }
}
