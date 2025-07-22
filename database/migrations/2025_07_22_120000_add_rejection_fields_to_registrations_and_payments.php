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
        // Add rejection fields to registrations table
        Schema::table('registrations', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
            $table->json('rejected_fields')->nullable()->after('rejection_reason');
            $table->timestamp('rejected_at')->nullable()->after('rejected_fields');
            $table->boolean('can_resubmit')->default(false)->after('rejected_at');
            $table->timestamp('resubmitted_at')->nullable()->after('can_resubmit');
        });

        // Add rejection fields to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('payment_status');
            $table->json('rejected_fields')->nullable()->after('rejection_reason');
            $table->timestamp('rejected_at')->nullable()->after('rejected_fields');
            $table->boolean('can_resubmit')->default(false)->after('rejected_at');
            $table->timestamp('resubmitted_at')->nullable()->after('can_resubmit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'rejection_reason',
                'rejected_fields',
                'rejected_at',
                'can_resubmit',
                'resubmitted_at'
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'rejection_reason',
                'rejected_fields',
                'rejected_at',
                'can_resubmit',
                'resubmitted_at'
            ]);
        });
    }
};
