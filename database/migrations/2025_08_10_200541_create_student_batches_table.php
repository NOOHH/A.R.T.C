<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentbatchesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('student_batches', function (Blueprint $table) {
            $table->bigInteger('batch_id')->nullable(false)->primary();
            $table->string('255')('batch_name')->nullable(false);
            $table->integer('program_id')->nullable(false);
            $table->integer('professor_id');
            $table->integer('max_capacity')->nullable(false);
            $table->integer('current_capacity')->nullable(false)->default(0);
            $table->boolean('is_active')->nullable(false)->default(1);
            $table->enum(''pending','available','ongoing','closed','completed'')('batch_status')->default('pending');
            $table->date('registration_deadline')->nullable(false);
            $table->date('start_date')->nullable(false);
            $table->date('end_date');
            $table->text('description');
            $table->integer('created_by');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('student_batches');
    }
}
