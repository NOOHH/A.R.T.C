<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentsubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->string('255')('student_id')->nullable(false);
            $table->bigInteger('module_id')->nullable(false);
            $table->bigInteger('content_id');
            $table->bigInteger('program_id')->nullable(false);
            $table->string('255')('file_path');
            $table->string('255')('original_filename');
            $table->text('files');
            $table->text('comments');
            $table->timestamp('submitted_at')->nullable(false)->default('current_timestamp()');
            $table->enum(''pending','draft','submitted','graded','returned','reviewed'')('status');
            $table->decimal('5', '2')('grade');
            $table->text('feedback');
            $table->timestamp('graded_at');
            $table->bigInteger('graded_by');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('assignment_submissions');
    }
}
