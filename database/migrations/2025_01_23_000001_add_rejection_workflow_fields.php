<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add rejection fields to registrations table
        Schema::table('registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('registrations', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('registrations', 'rejected_fields')) {
                $table->json('rejected_fields')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('registrations', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_fields');
            }
            if (!Schema::hasColumn('registrations', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            }
            if (!Schema::hasColumn('registrations', 'resubmitted_at')) {
                $table->timestamp('resubmitted_at')->nullable()->after('rejected_at');
            }
            if (!Schema::hasColumn('registrations', 'original_submission')) {
                $table->json('original_submission')->nullable()->after('resubmitted_at');
            }
        });

        // Add rejection fields to payments table
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('payments', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('payments', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            }
            if (!Schema::hasColumn('payments', 'resubmitted_at')) {
                $table->timestamp('resubmitted_at')->nullable()->after('rejected_at');
            }
        });

        // Update enum values for status fields
        DB::statement("ALTER TABLE registrations MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'resubmitted') DEFAULT 'pending'");
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'cancelled', 'rejected', 'resubmitted') DEFAULT 'pending'");

        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'access_period_days')) {
                $table->integer('access_period_days')->nullable()->after('price');
            }
            if (!Schema::hasColumn('packages', 'access_period_months')) {
                $table->integer('access_period_months')->nullable()->after('access_period_days');
            }
            if (!Schema::hasColumn('packages', 'access_period_years')) {
                $table->integer('access_period_years')->nullable()->after('access_period_months');
            }
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->date('access_start_date')->nullable()->after('payment_status');
            $table->date('access_end_date')->nullable()->after('access_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'rejection_reason',
                'rejected_fields',
                'rejected_by',
                'rejected_at',
                'resubmitted_at',
                'original_submission'
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'rejection_reason',
                'rejected_by',
                'rejected_at',
                'resubmitted_at'
            ]);
        });

        // Revert enum values
        DB::statement("ALTER TABLE registrations MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'cancelled') DEFAULT 'pending'");

        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'access_period_days')) {
                $table->dropColumn(['access_period_days']);
            }
            if (Schema::hasColumn('packages', 'access_period_months')) {
                $table->dropColumn(['access_period_months']);
            }
            if (Schema::hasColumn('packages', 'access_period_years')) {
                $table->dropColumn(['access_period_years']);
            }
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['access_start_date', 'access_end_date']);
        });
    }
};
