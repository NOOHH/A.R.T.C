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
        Schema::table('payments', function (Blueprint $table) {
            // Add encrypted QR code data field
            if (!Schema::hasColumn('payments', 'qr_code_data_encrypted')) {
                $table->text('qr_code_data_encrypted')->nullable()->after('reference_number');
            }
            
            // Add encrypted QR code path field
            if (!Schema::hasColumn('payments', 'qr_code_path_encrypted')) {
                $table->text('qr_code_path_encrypted')->nullable()->after('qr_code_data_encrypted');
            }
            
            // Modify reference_number field to handle encrypted data (increase size)
            $table->text('reference_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remove encrypted fields
            if (Schema::hasColumn('payments', 'qr_code_data_encrypted')) {
                $table->dropColumn('qr_code_data_encrypted');
            }
            
            if (Schema::hasColumn('payments', 'qr_code_path_encrypted')) {
                $table->dropColumn('qr_code_path_encrypted');
            }
            
            // Revert reference_number field size
            $table->string('reference_number')->nullable()->change();
        });
    }
};
