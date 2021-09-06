<?php

namespace Database\Seeders;

use App\Models\Variable;
use Illuminate\Database\Seeder;

class VariableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV') !== 'local') {
            return;
        }
        $variables = array(
            'RAZORPAY_KEY_ID' => "rzp_test_e23RPT6s2ojg9n",
            'RAZORPAY_KEY_SECRET' => "pWxM31ttoWmu7qOCdeKPvqvX",
            'RAZORPAY_CURRENCY' => "INR",
            'RAZORPAY_WEBHOOK_SECRET' => "r4ZVPgiYZYJdYRKSm4TRNEbIT40J4g8g",
        );

        foreach ($variables as $key => $value) {
            Variable::create([
                'key' => $key,
                'value' => $value
            ]);
        }
    }
}
