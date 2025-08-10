<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardpassersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('board_passers', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->string('255')('student_id');
            $table->string('255')('student_name')->nullable(false);
            $table->string('255')('program');
            $table->string('255')('board_exam')->nullable(false);
            $table->integer('exam_year')->nullable(false);
            $table->date('exam_date');
            $table->enum(''pass','fail','pending'')('result')->nullable(false)->default('PENDING');
            $table->decimal('5', '2')('rating');
            $table->text('notes');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('board_passers');
    }
}
