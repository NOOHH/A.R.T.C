<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeadlinesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('deadlines', function (Blueprint $table) {
            $table->bigInteger('deadline_id')->nullable(false)->primary();
            $table->bigInteger('student_id')->nullable(false);
            $table->bigInteger('program_id')->nullable(false);
            $table->string('255')('title')->nullable(false);
            $table->text('description');
            $table->enum(''assignment','quiz','activity','exam'')('type')->nullable(false)->default('assignment');
            $table->bigInteger('reference_id');
            $table->dateTime('due_date')->nullable(false);
            $table->enum(''pending','completed','overdue'')('status')->nullable(false)->default('pending');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('deadlines');
    }
}
