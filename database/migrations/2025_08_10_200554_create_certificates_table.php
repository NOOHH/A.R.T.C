<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->bigInteger('certificate_id')->nullable(false)->primary();
            $table->string('255')('student_id')->nullable(false);
            $table->bigInteger('enrollment_id')->nullable(false);
            $table->bigInteger('program_id')->nullable(false);
            $table->string('255')('certificate_number')->nullable(false);
            $table->string('255')('student_name')->nullable(false);
            $table->string('255')('program_name')->nullable(false);
            $table->date('start_date')->nullable(false);
            $table->date('completion_date')->nullable(false);
            $table->decimal('5', '2')('final_score');
            $table->string('255')('certificate_type')->nullable(false)->default('completion');
            $table->string('255')('status')->nullable(false)->default('pending');
            $table->text('certificate_data');
            $table->string('255')('file_path');
            $table->string('255')('qr_code');
            $table->timestamp('issued_at');
            $table->bigInteger('issued_by');
            $table->text('rejection_reason');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('certificates');
    }
}
