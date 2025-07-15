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
            // Add education level for progression tracking
            $table->unsignedBigInteger('education_level_id')->nullable()->after('registration_id');
            $table->foreign('education_level_id')->references('id')->on('education_levels')->onDelete('set null');
            
            // Dynamic fields inherited from registrations
            $table->json('inherited_registration_data')->nullable()->after('education_level_id');
            
            // Track which fields were inherited and when
            $table->json('inheritance_metadata')->nullable()->after('inherited_registration_data');
            
            // Progression tracking
            $table->string('progression_stage')->default('initial')->after('inheritance_metadata'); // initial, continuing, advanced
            $table->timestamp('education_level_started_at')->nullable()->after('progression_stage');
            $table->timestamp('education_level_completed_at')->nullable()->after('education_level_started_at');
            
            // Allow multiple enrollments for same user at different levels
            $table->index(['user_id', 'education_level_id', 'program_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['education_level_id']);
            $table->dropIndex(['user_id', 'education_level_id', 'program_id']);
            $table->dropColumn([
                'education_level_id',
                'inherited_registration_data',
                'inheritance_metadata',
                'progression_stage',
                'education_level_started_at',
                'education_level_completed_at'
            ]);
        });
    }
};
