<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Change ENUM or VARCHAR to allow new role values
            $table->string('role', 32)->default('unverified')->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert to previous definition if needed (example: 16 chars)
            $table->string('role', 16)->default('student')->change();
        });
    }
};
