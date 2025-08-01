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
            // Add due_date column (nullable for no deadline option)
            $table->timestamp('due_date')->nullable()->after('max_attempts');
            
            // Add infinite_retakes column (boolean, default false)
            $table->boolean('infinite_retakes')->default(false)->after('due_date');
            
            // Add has_deadline column for UI state management
            $table->boolean('has_deadline')->default(false)->after('infinite_retakes');
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
            $table->dropColumn(['due_date', 'infinite_retakes', 'has_deadline']);
        });
    }
};
