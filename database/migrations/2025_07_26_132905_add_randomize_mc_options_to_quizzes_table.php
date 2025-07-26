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
            if (!Schema::hasColumn('quizzes', 'randomize_mc_options')) {
                $table->boolean('randomize_mc_options')->default(false)->after('randomize_order');
            }
            if (!Schema::hasColumn('quizzes', 'allow_retakes')) {
                $table->boolean('allow_retakes')->default(false)->after('randomize_mc_options');
            }
            if (!Schema::hasColumn('quizzes', 'instant_feedback')) {
                $table->boolean('instant_feedback')->default(false)->after('allow_retakes');
            }
            if (!Schema::hasColumn('quizzes', 'max_attempts')) {
                $table->integer('max_attempts')->default(1)->after('instant_feedback');
            }
            if (!Schema::hasColumn('quizzes', 'status')) {
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('is_draft');
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
            $table->dropColumn(['randomize_mc_options', 'allow_retakes', 'instant_feedback', 'max_attempts', 'status']);
        });
    }
};
