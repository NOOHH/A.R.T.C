<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Make emergency_contact_number nullable with default null
            $table->string('emergency_contact_number')->nullable()->default(null)->change();
            
            // Also ensure other potentially problematic fields are nullable
            $table->string('Telephone_Number')->nullable()->default(null)->change();
            $table->string('good_moral')->nullable()->default(null)->change();
            $table->string('PSA')->nullable()->default(null)->change();
            $table->string('Course_Cert')->nullable()->default(null)->change();
            $table->string('TOR')->nullable()->default(null)->change();
            $table->string('Cert_of_Grad')->nullable()->default(null)->change();
            $table->string('Undergraduate')->nullable()->default(null)->change();
            $table->string('Graduate')->nullable()->default(null)->change();
            $table->string('photo_2x2')->nullable()->default(null)->change();
        });
    }

    public function down()
    {
        // Reverse migration if needed
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('emergency_contact_number')->nullable(false)->change();
        });
    }
};
