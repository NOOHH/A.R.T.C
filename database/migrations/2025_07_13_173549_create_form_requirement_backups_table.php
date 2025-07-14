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
        Schema::create('form_requirement_backups', function (Blueprint $table) {
            $table->id();
            $table->string('field_name');
            $table->string('table_name'); // 'registrations' or 'students'
            $table->longText('backup_data'); // JSON data backup
            $table->timestamp('archived_at');
            $table->timestamps();
            
            $table->index(['field_name', 'table_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_requirement_backups');
    }
};
