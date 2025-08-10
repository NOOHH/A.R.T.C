<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->bigInteger('quiz_id')->nullable(false)->primary();
            $table->bigInteger('professor_id');
            $table->integer('admin_id');
            $table->bigInteger('program_id')->nullable(false);
            $table->bigInteger('module_id');
            $table->bigInteger('course_id');
            $table->bigInteger('content_id');
            $table->string('255')('quiz_title')->nullable(false);
            $table->text('instructions');
            $table->text('quiz_description');
            $table->integer('total_questions')->nullable(false)->default(10);
            $table->integer('time_limit')->nullable(false)->default(60);
            $table->string('255')('document_path');
            $table->boolean('is_active')->nullable(false)->default(1);
            $table->enum(''draft','published','archived'')('status')->nullable(false)->default('draft');
            $table->boolean('allow_retakes')->nullable(false)->default(0);
            $table->boolean('infinite_retakes')->default(0);
            $table->boolean('instant_feedback')->nullable(false)->default(0);
            $table->boolean('show_correct_answers')->nullable(false)->default(1);
            $table->integer('max_attempts');
            $table->boolean('has_deadline')->default(0);
            $table->dateTime('due_date');
            $table->boolean('is_draft')->nullable(false)->default(0);
            $table->boolean('randomize_order')->nullable(false)->default(0);
            $table->boolean('randomize_mc_options')->nullable(false)->default(0);
            $table->text('tags');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('quizzes');
    }
}
