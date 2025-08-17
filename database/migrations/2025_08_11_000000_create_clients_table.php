<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('db_name')->unique();
            $table->string('db_host')->default(env('DB_HOST', '127.0.0.1'));
            $table->unsignedInteger('db_port')->default((int) env('DB_PORT', 3306));
            $table->string('db_username')->default(env('DB_USERNAME', 'root'));
            $table->string('db_password')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};



