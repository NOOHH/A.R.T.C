<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->unsignedBigInteger('registration_id')->nullable()->after('enrollment_id');
            $table->string('enrollment_status')->default('pending')->after('learning_mode');
            
            // Add foreign key constraint if registrations table exists
            $table->foreign('registration_id')->references('registration_id')->on('registrations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['registration_id']);
            $table->dropColumn(['registration_id', 'enrollment_status']);
        });
    }
};
