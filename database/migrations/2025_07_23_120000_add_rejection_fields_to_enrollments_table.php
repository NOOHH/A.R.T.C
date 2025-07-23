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
        Schema::table('enrollments', function (Blueprint $table) {
            // Add rejection tracking fields if they don't exist
            if (!Schema::hasColumn('enrollments', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('enrollments', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_at');
            }
            if (!Schema::hasColumn('enrollments', 'rejected_fields')) {
                $table->json('rejected_fields')->nullable()->after('rejection_reason')->comment('JSON array of field names that need to be redone');
            }
            if (!Schema::hasColumn('enrollments', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_fields')->comment('Admin ID who rejected the enrollment');
            }
            if (!Schema::hasColumn('enrollments', 'resubmitted_at')) {
                $table->timestamp('resubmitted_at')->nullable()->after('rejected_by');
            }
            if (!Schema::hasColumn('enrollments', 'resubmission_count')) {
                $table->integer('resubmission_count')->default(0)->after('resubmitted_at');
            }

            // Update enrollment_status enum to include rejected values if it exists
            if (Schema::hasColumn('enrollments', 'enrollment_status')) {
                $table->enum('enrollment_status', [
                    'pending', 'approved', 'rejected', 'rejected_registration', 'resubmitted'
                ])->default('pending')->change();
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
        Schema::table('enrollments', function (Blueprint $table) {
            // Remove rejection fields
            $table->dropColumn([
                'rejected_at',
                'rejection_reason',
                'rejected_fields',
                'rejected_by',
                'resubmitted_at',
                'resubmission_count'
            ]);

            // Revert enrollment_status enum if it exists
            if (Schema::hasColumn('enrollments', 'enrollment_status')) {
                $table->enum('enrollment_status', ['pending', 'approved', 'rejected'])->default('pending')->change();
            }
        });
    }
};
