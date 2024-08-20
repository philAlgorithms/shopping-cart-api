<?php

namespace Database\Seeders;

use App\Models\RangeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RangeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [
                'title' => 'Home Delivery Cost',
                'key' => 'HOME_DELIVERY_COST',
            ],
            [
                'title' => 'Waybill Cost',
                'key' => 'WAYBILL_COST',
            ]
        ];

        foreach($types as $type)
        {
            RangeType::create($type);
        }
    }
}
