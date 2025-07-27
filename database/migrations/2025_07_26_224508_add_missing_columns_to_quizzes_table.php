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
        Schema::table('quizzes', function (Blueprint $table) {
            // Add missing column used by the Gemini quiz generator
            if (!Schema::hasColumn('quizzes', 'randomize_mc_options')) {
                $table->boolean('randomize_mc_options')->default(false)->after('randomize_order');
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
        Schema::table('quizzes', function (Blueprint $table) {
            // Remove the column if rolling back
            if (Schema::hasColumn('quizzes', 'randomize_mc_options')) {
                $table->dropColumn('randomize_mc_options');
            }
        });
    }
};
