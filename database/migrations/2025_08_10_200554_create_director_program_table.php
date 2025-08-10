<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectorprogramTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('director_program', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->integer('director_id')->nullable(false);
            $table->integer('program_id')->nullable(false);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('director_program');
    }
}
