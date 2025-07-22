<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, let's add the reference_number field if it doesn't exist
        if (!Schema::hasColumn('payments', 'reference_number')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('reference_number')->nullable()->after('payment_details');
            });
        }

        // Update the payment_method enum to match payment_methods table
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('credit_card', 'gcash', 'maya', 'bank_transfer', 'cash', 'other', 'installment') NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove reference_number field
        if (Schema::hasColumn('payments', 'reference_number')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('reference_number');
            });
        }

        // Revert payment_method enum to original values
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('credit_card', 'gcash', 'bank_transfer', 'installment') NULL");
    }
};