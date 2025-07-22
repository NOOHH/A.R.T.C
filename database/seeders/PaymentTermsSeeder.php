<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentTermsSeeder extends Seeder
{
    public function run()
    {
        DB::table('payment_terms')->insert([
            'terms_html' => '<h4>Default Payment Terms and Conditions</h4><p>Please read and accept these terms before submitting your payment.</p>',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
} 