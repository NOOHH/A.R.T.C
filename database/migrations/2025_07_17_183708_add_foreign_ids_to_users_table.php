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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable()->after('role');
            $table->unsignedBigInteger('directors_id')->nullable()->after('admin_id');
            
            // Add foreign key constraints
            $table->foreign('admin_id')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('directors_id')->references('directors_id')->on('directors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropForeign(['directors_id']);
            $table->dropColumn(['admin_id', 'directors_id']);
        });
    }
};
