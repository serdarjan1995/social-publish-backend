<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment_types = [
            [
                'id' => 1,
                'name' => 'PayTR',
            ],
        ];

        DB::table('payment_types')->insert($payment_types);
    }
}
