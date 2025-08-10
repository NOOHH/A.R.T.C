<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizattemptsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->bigInteger('attempt_id')->nullable(false)->primary();
            $table->bigInteger('quiz_id')->nullable(false);
            $table->string('255')('student_id')->nullable(false);
            $table->text('answers')->nullable(false);
            $table->decimal('5', '2')('score');
            $table->integer('total_questions')->nullable(false);
            $table->integer('correct_answers')->nullable(false)->default(0);
            $table->timestamp('started_at');
            $table->timestamp('completed_at');
            $table->integer('time_taken');
            $table->enum(''in_progress','completed','abandoned'')('status')->nullable(false)->default('in_progress');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('quiz_attempts');
    }
}
