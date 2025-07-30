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
        Schema::table('content_items', function (Blueprint $table) {
            // Add file_path if it doesn't exist
            if (!Schema::hasColumn('content_items', 'file_path')) {
                $table->string('file_path')->nullable();
            }
            
            // Add file_name if it doesn't exist
            if (!Schema::hasColumn('content_items', 'file_name')) {
                $table->string('file_name')->nullable();
            }
            
            // Add file_size if it doesn't exist
            if (!Schema::hasColumn('content_items', 'file_size')) {
                $table->bigInteger('file_size')->nullable();
            }
            
            // Add file_mime if it doesn't exist
            if (!Schema::hasColumn('content_items', 'file_mime')) {
                $table->string('file_mime')->nullable();
            }
            
            // Add a flag to indicate multiple files
            if (!Schema::hasColumn('content_items', 'has_multiple_files')) {
                $table->boolean('has_multiple_files')->default(false);
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
        Schema::table('content_items', function (Blueprint $table) {
            // Only drop columns if they exist
            if (Schema::hasColumn('content_items', 'has_multiple_files')) {
                $table->dropColumn('has_multiple_files');
            }
            
            // We don't drop the other columns as they might be used elsewhere
        });
    }
};
