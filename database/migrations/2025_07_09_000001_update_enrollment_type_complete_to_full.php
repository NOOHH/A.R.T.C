<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateEnrollmentTypeCompleteToFull extends Migration
{
    public function up()
    {
        // Update existing records
        DB::table('enrollments')
            ->where('enrollment_type', 'Complete')
            ->update(['enrollment_type' => 'Full']);

        // Modify the enum
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN enrollment_type ENUM('Modular', 'Full') NOT NULL");
    }

    public function down()
    {
        // Revert the enum
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN enrollment_type ENUM('Modular', 'Complete') NOT NULL");

        // Revert existing records
        DB::table('enrollments')
            ->where('enrollment_type', 'Full')
            ->update(['enrollment_type' => 'Complete']);
    }
}
