<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentcoursesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('enrollment_courses', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->bigInteger('enrollment_id')->nullable(false);
            $table->integer('course_id')->nullable(false);
            $table->integer('module_id')->nullable(false);
            $table->enum(''module','course'')('enrollment_type')->nullable(false)->default('course');
            $table->decimal('10', '2')('course_price')->nullable(false)->default(0.00);
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
        Schema::dropIfExists('enrollment_courses');
    }
}
