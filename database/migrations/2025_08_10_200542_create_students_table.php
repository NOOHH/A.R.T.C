<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->string('30')('student_id')->nullable(false)->primary();
            $table->integer('user_id')->nullable(false);
            $table->string('50')('firstname')->nullable(false);
            $table->string('50')('middlename');
            $table->string('50')('lastname')->nullable(false);
            $table->string('50')('student_school');
            $table->string('50')('street_address');
            $table->string('50')('state_province');
            $table->string('50')('city');
            $table->string('20')('zipcode');
            $table->string('15')('contact_number');
            $table->string('20')('emergency_contact_number');
            $table->string('255')('good_moral');
            $table->string('255')('PSA');
            $table->string('255')('Course_Cert');
            $table->string('255')('TOR');
            $table->string('255')('Cert_of_Grad');
            $table->string('255')('photo_2x2');
            $table->string('255')('profile_photo');
            $table->date('Start_Date');
            $table->timestamp('date_approved')->nullable(false)->default('current_timestamp()');
            $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            $table->string('100')('email')->nullable(false);
            $table->boolean('is_archived')->nullable(false)->default(0);
            $table->integer('package_id');
            $table->string('255')('package_name');
            $table->integer('plan_id');
            $table->string('255')('plan_name');
            $table->integer('program_id');
            $table->string('255')('education_level')->nullable(false)->default('');
            $table->string('255')('program_name');
            $table->string('20')('enrollment_type');
            $table->string('50')('learning_mode');
            $table->boolean('Undergraduate')->default(0);
            $table->boolean('Graduate')->default(0);
            $table->text('dynamic_fields');
            $table->enum(''pending','approved','rejected'')('status')->default('pending');
            $table->string('20')('phone_number');
            $table->string('20')('telephone_number');
            $table->string('100')('religion');
            $table->string('100')('citizenship');
            $table->string('50')('civil_status');
            $table->date('birthdate');
            $table->string('20')('gender');
            $table->text('work_experience');
            $table->string('50')('preferred_schedule');
            $table->string('100')('emergency_contact_relationship');
            $table->text('health_conditions');
            $table->boolean('disability_support')->default(0);
            $table->string('255')('valid_id');
            $table->string('255')('birth_certificate');
            $table->string('255')('diploma_certificate');
            $table->string('255')('medical_certificate');
            $table->string('255')('passport_photo');
            $table->string('255')('parent_guardian_name');
            $table->string('20')('parent_guardian_contact');
            $table->string('20')('referral_code');
            $table->string('255')('ama_namin');
            $table->string('255')('previous_school');
            $table->string('graduation_year');
            $table->string('255')('course_taken');
            $table->text('special_needs');
            $table->string('255')('scholarship_program');
            $table->string('100')('employment_status');
            $table->decimal('10', '2')('monthly_income');
            $table->string('255')('school_name');
            $table->text('selected_modules');
            $table->string('255')('test_field_auto');
            $table->string('255')('testering');
            $table->string('255')('master');
            $table->string('255')('bagit');
            $table->string('255')('real');
            $table->string('255')('test_auto_column_1752439854');
            $table->string('255')('nyan');
            $table->string('255')('Test');
            $table->string('255')('last_name');
            $table->string('255')('school_id');
            $table->string('255')('diploma');
            $table->string('255')('valid_school_identification');
            $table->string('255')('transcript_of_records');
            $table->string('255')('certificate_of_good_moral_character');
            $table->string('255')('psa_birth_certificate');
            $table->string('255')('transcript_records');
            $table->string('255')('moral_certificate');
            $table->string('255')('birth_cert');
            $table->string('255')('id_photo');
            $table->string('255')('barangay_clearance');
            $table->string('255')('police_clearance');
            $table->string('255')('nbi_clearance');
            $table->string('255')('birthday');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
