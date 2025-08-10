<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizoptionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->bigInteger('option_id')->nullable(false)->primary();
            $table->bigInteger('question_id')->nullable(false);
            $table->text('option_text')->nullable(false);
            $table->boolean('is_correct')->nullable(false)->default(0);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('quiz_options');
    }
}
