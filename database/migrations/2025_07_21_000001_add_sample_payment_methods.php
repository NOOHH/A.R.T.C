<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Insert sample payment methods if they don't exist
        $existingMethods = DB::table('payment_methods')->count();
        
        if ($existingMethods == 0) {
            DB::table('payment_methods')->insert([
                [
                    'method_name' => 'GCash',
                    'method_type' => 'gcash',
                    'description' => 'Pay via GCash mobile wallet',
                    'instructions' => 'Send payment to GCash number and upload receipt',
                    'is_enabled' => true,
                    'sort_order' => 1,
                    'created_by_admin_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'method_name' => 'Maya (PayMaya)',
                    'method_type' => 'maya',
                    'description' => 'Pay via Maya mobile wallet',
                    'instructions' => 'Send payment to Maya account and upload receipt',
                    'is_enabled' => true,
                    'sort_order' => 2,
                    'created_by_admin_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'method_name' => 'Bank Transfer',
                    'method_type' => 'bank_transfer',
                    'description' => 'Pay via bank transfer',
                    'instructions' => 'Transfer to our bank account and upload receipt',
                    'is_enabled' => true,
                    'sort_order' => 3,
                    'created_by_admin_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }

    public function down()
    {
        DB::table('payment_methods')->where('created_by_admin_id', 1)->delete();
    }
};
