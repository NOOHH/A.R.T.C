<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfessorprogramTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('professor_program', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->bigInteger('professor_id')->nullable(false);
            $table->bigInteger('program_id')->nullable(false);
            $table->string('255')('video_link');
            $table->text('video_description');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('professor_program');
    }
}
