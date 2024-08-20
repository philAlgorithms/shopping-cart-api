<?php

namespace Database\Seeders;

use App\Models\TimeUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = ['second', 'minute', 'day', 'week', 'month', 'year'];

        foreach($units as $unit)
        {
            TimeUnit::create(['name' => $unit]);
        }
    }
}
