<?php

namespace Database\Seeders;

use App\Handler\Paystack\PaystackMiscellaneous;
use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $banks = (new PaystackMiscellaneous)->listBanks(
            per_page: 1000
        );

        if (array_key_exists('data', $banks)) {
            foreach ($banks['data'] as $bank) {
                Bank::create(
                    [
                        ...Arr::only(
                            $bank,
                            [
                                'name',
                                'slug',
                                'code',
                                'long_code',
                                'gateway',
                                'pay_with_bank',
                                'active',
                                'currency',
                                'country',
                                'type'
                            ]
                        ),
                        ...[
                            'paystack_id' => $bank['id']
                        ]
                    ]
                );
            }
        }
    }
}
