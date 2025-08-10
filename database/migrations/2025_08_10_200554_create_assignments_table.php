<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->bigInteger('assignment_id')->nullable(false)->primary();
            $table->bigInteger('professor_id')->nullable(false);
            $table->bigInteger('program_id')->nullable(false);
            $table->string('255')('title')->nullable(false);
            $table->text('description');
            $table->text('instructions');
            $table->integer('max_points')->nullable(false)->default(100);
            $table->dateTime('due_date')->nullable(false);
            $table->boolean('is_active')->nullable(false)->default(1);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('assignments');
    }
}
