<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchAndLearningModeToModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules', function (Blueprint $table) {
            // Add batch_id column
            if (!Schema::hasColumn('modules', 'batch_id')) {
                $table->unsignedBigInteger('batch_id')->nullable()->after('program_id');
                $table->foreign('batch_id')->references('batch_id')->on('student_batches')->onDelete('set null');
            }
            
            // Add learning_mode column
            if (!Schema::hasColumn('modules', 'learning_mode')) {
                $table->enum('learning_mode', ['Synchronous', 'Asynchronous'])
                      ->default('Synchronous')
                      ->after('batch_id');
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
        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'batch_id')) {
                $table->dropForeign(['batch_id']);
                $table->dropColumn('batch_id');
            }
            
            if (Schema::hasColumn('modules', 'learning_mode')) {
                $table->dropColumn('learning_mode');
            }
        });
    }
}
