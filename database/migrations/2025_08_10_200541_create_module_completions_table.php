<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulecompletionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('module_completions', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->string('30')('student_id')->nullable(false);
            $table->integer('program_id');
            $table->integer('modules_id');
            $table->bigInteger('content_id');
            $table->timestamp('completed_at');
            $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('module_completions');
    }
}
