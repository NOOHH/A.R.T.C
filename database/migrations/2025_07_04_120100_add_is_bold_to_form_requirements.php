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
            if (!Schema::hasColumn('form_requirements', 'is_bold')) {
                $table->boolean('is_bold')->default(false)->after('is_active');
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
            if (Schema::hasColumn('form_requirements', 'is_bold')) {
                $table->dropColumn('is_bold');
            }
        });
    }
};
