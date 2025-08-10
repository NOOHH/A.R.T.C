<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->integer('enrollment_id')->nullable(false)->primary();
            $table->integer('registration_id');
            $table->string('30')('student_id');
            $table->bigInteger('user_id');
            $table->integer('program_id')->nullable(false);
            $table->integer('package_id')->nullable(false);
            $table->enum(''modular','full'')('enrollment_type')->nullable(false)->default('Modular');
            $table->enum(''synchronous','asynchronous'')('learning_mode')->nullable(false)->default('Synchronous');
            $table->bigInteger('batch_id');
            $table->dateTime('individual_start_date');
            $table->dateTime('individual_end_date');
            $table->enum(''pending','approved','rejected','completed'')('enrollment_status')->nullable(false)->default('pending');
            $table->decimal('5', '2')('progress_percentage')->nullable(false)->default(0.00);
            $table->timestamp('completion_date');
            $table->timestamp('last_activity');
            $table->integer('total_modules')->nullable(false)->default(0);
            $table->integer('completed_modules')->nullable(false)->default(0);
            $table->integer('total_courses')->nullable(false)->default(0);
            $table->integer('completed_courses')->nullable(false)->default(0);
            $table->boolean('certificate_eligible')->nullable(false)->default(0);
            $table->boolean('certificate_requested')->nullable(false)->default(0);
            $table->boolean('certificate_issued')->nullable(false)->default(0);
            $table->enum(''pending','paid','failed','cancelled'')('payment_status')->default('pending');
            $table->boolean('batch_access_granted')->nullable(false)->default(0);
            $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('start_date');
            $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            $table->string('255')('school_id');
            $table->string('255')('diploma');
            $table->string('255')('valid_school_identification');
            $table->string('255')('transcript_of_records');
            $table->string('255')('certificate_of_good_moral_character');
            $table->string('255')('psa_birth_certificate');
            $table->string('255')('photo_2x2');
            $table->string('255')('diploma_certificate');
            $table->string('255')('transcript_records');
            $table->string('255')('moral_certificate');
            $table->string('255')('birth_cert');
            $table->string('255')('id_photo');
            $table->string('255')('passport_photo');
            $table->string('255')('medical_certificate');
            $table->string('255')('barangay_clearance');
            $table->string('255')('police_clearance');
            $table->string('255')('nbi_clearance');
            $table->string('255')('tor');
            $table->string('255')('good_moral');
            $table->string('255')('psa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
}
