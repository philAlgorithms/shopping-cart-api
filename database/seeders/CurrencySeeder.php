<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Location\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            [
                'name' => 'United States Dollar',
                'acronym' => 'USD',
                'symbol' => '$'
            ],
            [
                'name' => 'Nigerian Naira',
                'acronym' => 'NGN',
                'symbol' => '₦'
            ],
        ];

        foreach($currencies as $currency)
        {
            Currency::create($currency);
        }

        $dollar = Currency::firstWhere('acronym', 'USD');
        $usa = Country::firstWhere('iso3', 'USA');
        $naira = Currency::firstWhere('acronym', 'NGN');
        $nigeria = Country::firstWhere('iso3', 'NGA');

        if(!is_null($dollar) and !is_null($usa)) $usa->currencies()->sync([$dollar->id]);
        if(!is_null($naira) and !is_null($nigeria)) $nigeria->currencies()->sync([$naira->id]);
    }
}
