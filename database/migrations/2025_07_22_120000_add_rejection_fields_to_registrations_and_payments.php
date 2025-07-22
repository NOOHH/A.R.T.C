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
        // Add missing rejection fields to payments table
        Schema::table('payments', function (Blueprint $table) {
            // Only add the fields that don't exist yet
            if (!Schema::hasColumn('payments', 'rejected_fields')) {
                $table->json('rejected_fields')->nullable()->after('rejected_at')->comment('JSON array of field names that need to be redone');
            }
            if (!Schema::hasColumn('payments', 'resubmission_count')) {
                $table->integer('resubmission_count')->default(0)->after('resubmitted_at');
            }
        });

        // Add rejection fields to registrations table (if it exists)
        if (Schema::hasTable('registrations')) {
            Schema::table('registrations', function (Blueprint $table) {
                // Check if status column exists, if not add it
                if (!Schema::hasColumn('registrations', 'status')) {
                    $table->enum('status', ['pending', 'approved', 'rejected', 'resubmitted'])->default('pending')->after('id');
                } else {
                    // Update existing status enum to include new values
                    $table->enum('status', ['pending', 'approved', 'rejected', 'resubmitted'])->default('pending')->change();
                }
                
                // Add rejection tracking fields if they don't exist
                if (!Schema::hasColumn('registrations', 'rejected_at')) {
                    $table->timestamp('rejected_at')->nullable()->after('updated_at');
                }
                if (!Schema::hasColumn('registrations', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('rejected_at');
                }
                if (!Schema::hasColumn('registrations', 'rejected_fields')) {
                    $table->json('rejected_fields')->nullable()->after('rejection_reason')->comment('JSON array of field names that need to be redone');
                }
                if (!Schema::hasColumn('registrations', 'resubmitted_at')) {
                    $table->timestamp('resubmitted_at')->nullable()->after('rejected_fields');
                }
                if (!Schema::hasColumn('registrations', 'resubmission_count')) {
                    $table->integer('resubmission_count')->default(0)->after('resubmitted_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove only the fields we added
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'rejected_fields',
                'resubmission_count'
            ]);
        });

        // Remove rejection fields from registrations table
        if (Schema::hasTable('registrations')) {
            Schema::table('registrations', function (Blueprint $table) {
                // Revert status enum
                $table->enum('status', ['pending', 'approved'])->default('pending')->change();
                
                // Remove rejection fields
                $table->dropColumn([
                    'rejected_at',
                    'rejection_reason',
                    'rejected_fields', 
                    'resubmitted_at',
                    'resubmission_count'
                ]);
            });
        }
    }
};