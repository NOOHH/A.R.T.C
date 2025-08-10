<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfessorbatchTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('professor_batch', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->bigInteger('batch_id')->nullable(false);
            $table->integer('professor_id')->nullable(false);
            $table->timestamp('assigned_at');
            $table->integer('assigned_by');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('professor_batch');
    }
}
