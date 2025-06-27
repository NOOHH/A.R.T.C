<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // This migration assumes the STUDENTS table already exists and only adds missing columns if needed.
        Schema::table('students', function (Blueprint $table) {
            // Add columns only if they do not exist (for safety, but Laravel does not check existence by default)
            // You may need to manually check your DB and remove any duplicate add attempts.
            if (!Schema::hasColumn('students', 'user_id')) $table->unsignedBigInteger('user_id')->nullable();
            if (!Schema::hasColumn('students', 'firstname')) $table->string('firstname', 50);
            if (!Schema::hasColumn('students', 'middlename')) $table->string('middlename', 50);
            if (!Schema::hasColumn('students', 'lastname')) $table->string('lastname', 50);
            if (!Schema::hasColumn('students', 'student_school')) $table->string('student_school', 50);
            if (!Schema::hasColumn('students', 'street_address')) $table->string('street_address', 50);
            if (!Schema::hasColumn('students', 'state_province')) $table->string('state_province', 50);
            if (!Schema::hasColumn('students', 'city')) $table->string('city', 50);
            if (!Schema::hasColumn('students', 'zipcode')) $table->string('zipcode', 20);
            if (!Schema::hasColumn('students', 'email')) $table->string('email', 100);
            if (!Schema::hasColumn('students', 'contact_number')) $table->string('contact_number', 15);
            if (!Schema::hasColumn('students', 'emergency_contact_number')) $table->string('emergency_contact_number', 15);
            if (!Schema::hasColumn('students', 'good_moral')) $table->string('good_moral', 255);
            if (!Schema::hasColumn('students', 'PSA')) $table->string('PSA', 255);
            if (!Schema::hasColumn('students', 'Course_Cert')) $table->string('Course_Cert', 255);
            if (!Schema::hasColumn('students', 'TOR')) $table->string('TOR', 255);
            if (!Schema::hasColumn('students', 'Cert_of_Grad')) $table->string('Cert_of_Grad', 255);
            if (!Schema::hasColumn('students', 'Undergraduate')) $table->string('Undergraduate', 255);
            if (!Schema::hasColumn('students', 'Graduate')) $table->string('Graduate', 255);
            if (!Schema::hasColumn('students', 'photo_2x2')) $table->string('photo_2x2', 255);
            if (!Schema::hasColumn('students', 'Start_Date')) $table->date('Start_Date');
            if (!Schema::hasColumn('students', 'date_approved')) $table->timestamp('date_approved')->useCurrent();
            // Foreign key
            // $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'user_id', 'firstname', 'middlename', 'lastname', 'student_school', 'street_address', 'state_province', 'city', 'zipcode', 'email', 'contact_number', 'emergency_contact_number', 'good_moral', 'PSA', 'Course_Cert', 'TOR', 'Cert_of_Grad', 'Undergraduate', 'Graduate', 'photo_2x2', 'Start_Date', 'date_approved'
            ]);
        });
    }
};
