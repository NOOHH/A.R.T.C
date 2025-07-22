<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Add file upload columns if they don't exist
            if (!Schema::hasColumn('registrations', 'school_id')) {
                $table->string('school_id')->nullable()->comment('School ID document path');
            }
            if (!Schema::hasColumn('registrations', 'diploma')) {
                $table->string('diploma')->nullable()->comment('Diploma/Certificate document path');
            }
            if (!Schema::hasColumn('registrations', 'tor')) {
                $table->string('tor')->nullable()->comment('Transcript of Records document path');
            }
            if (!Schema::hasColumn('registrations', 'psa_birth_certificate')) {
                $table->string('psa_birth_certificate')->nullable()->comment('PSA Birth Certificate document path');
            }
            if (!Schema::hasColumn('registrations', 'form_137')) {
                $table->string('form_137')->nullable()->comment('Form 137 document path');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $columns = ['school_id', 'diploma', 'tor', 'psa_birth_certificate', 'form_137'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('registrations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
