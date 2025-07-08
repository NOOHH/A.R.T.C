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
        Schema::table('form_requirements', function (Blueprint $table) {
            if (!Schema::hasColumn('form_requirements', 'entity_type')) {
                $table->enum('entity_type', ['student', 'professor', 'admin'])->default('student')->after('field_type');
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
        Schema::table('form_requirements', function (Blueprint $table) {
            if (Schema::hasColumn('form_requirements', 'entity_type')) {
                $table->dropColumn('entity_type');
            }
        });
    }
};
