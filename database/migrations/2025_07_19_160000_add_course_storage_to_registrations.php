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
            // Add course selection storage columns if they don't exist
            if (!Schema::hasColumn('registrations', 'selected_courses')) {
                $table->json('selected_courses')->nullable()->after('selected_modules');
            }
            
            // Add file storage columns for education level documents that aren't already there
            $fileColumns = [
                'ama_namin' // This seems to be a custom field that was added already
            ];
            
            // These should already exist from previous migrations, but let's ensure they're there
            $standardFileColumns = [
                'good_moral',
                'PSA', 
                'Course_Cert',
                'TOR',
                'Cert_of_Grad',
                'valid_id',
                'birth_certificate',
                'diploma_certificate'
            ];
            
            foreach ($standardFileColumns as $column) {
                if (!Schema::hasColumn('registrations', $column)) {
                    $table->string($column)->nullable();
                }
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
            if (Schema::hasColumn('registrations', 'selected_courses')) {
                $table->dropColumn('selected_courses');
            }
        });
    }
};
