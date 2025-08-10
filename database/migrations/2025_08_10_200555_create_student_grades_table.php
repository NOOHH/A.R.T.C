<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentgradesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('student_grades', function (Blueprint $table) {
            $table->bigInteger('grade_id')->nullable(false);
            $table->string('30')('student_id')->nullable(false);
            $table->integer('program_id')->nullable(false);
            $table->integer('professor_id')->nullable(false);
            $table->string('255')('assignment_name')->nullable(false);
            $table->decimal('5', '2')('grade')->nullable(false);
            $table->decimal('5', '2')('max_points')->nullable(false);
            $table->text('feedback');
            $table->timestamp('graded_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('student_grades');
    }
}
