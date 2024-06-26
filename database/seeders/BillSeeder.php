<?php

namespace Database\Seeders;


use App\Models\Fee;
use App\Models\Bill;
use Illuminate\Database\Seeder;
use App\Jobs\CreateBillWithInvoiceJob;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(env('APP_ENV') !== 'local'){
            return;
        }
        $fee_ids = Fee::all()->modelKeys();
        $bill = Bill::factory()->create([
            'name' => 'Outstanding bill'
        ]);
        $bill->fees()->attach($fee_ids);
        $fees = Fee::whereIn('id', $fee_ids)->get();
        CreateBillWithInvoiceJob::dispatch($bill, $fees);
    }
}
