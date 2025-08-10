<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchprofessorsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('batch_professors', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->integer('batch_id')->nullable(false);
            $table->integer('professor_id')->nullable(false);
            $table->timestamp('assigned_at')->nullable(false)->default('current_timestamp()');
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
        Schema::dropIfExists('batch_professors');
    }
}
