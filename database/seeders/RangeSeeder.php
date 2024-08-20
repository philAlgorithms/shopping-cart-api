<?php

namespace Database\Seeders;

use App\Models\{Range, RangeType};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $home_delivery_cost_key = 'HOME_DELIVERY_COST';
        $waybill_cost_key = 'WAYBILL_COST';

        $waybill_type = RangeType::firstWhere('key', $waybill_cost_key);
        $home_delivery_type = RangeType::firstWhere('key', $home_delivery_cost_key);

        $home_delivery_ranges = [
            [
                'minimum' => 0,
                'maximum' => 1000,
                'value' => 100
            ],
            [
                'minimum' => 1001,
                'maximum' => 10000,
                'value' => 500
            ],
            [
                'minimum' => 10001,
                'maximum' => 100000,
                'value' => 1000
            ],
            [
                'minimum' => 100001,
                'maximum' => 1000000,
                'value' => 2000,
            ],
            [
                'minimum' => 1000001,
                'maximum' => 999999999999,
                'value' => 50000
            ],
        ];

        $waybill_ranges = [
            [
                'minimum' => 0,
                'maximum' => 1000,
                'value' => 100
            ],
            [
                'minimum' => 1001,
                'maximum' => 10000,
                'value' => 500
            ],
            [
                'minimum' => 10001,
                'maximum' => 100000,
                'value' => 1000
            ],
            [
                'minimum' => 100001,
                'maximum' => 1000000,
                'value' => 2000,
            ],
            [
                'minimum' => 1000001,
                'maximum' => 999999999999,
                'value' => 50000
            ],
        ];

        if (!is_null($waybill_type)) {
            foreach ($waybill_ranges as $range) {
                Range::create([
                    ...$range,
                    'range_type_id' => $waybill_type->id
                ]);
            }
        }

        if (!is_null($home_delivery_type)) {
            foreach ($home_delivery_ranges as $range) {
                Range::create([
                    ...$range,
                    'range_type_id' => $home_delivery_type->id
                ]);
            }
        }
    }
}
