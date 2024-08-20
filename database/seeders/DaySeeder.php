<?php

namespace Database\Seeders;

use App\Models\Day;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ];

        foreach($days as $i => $day)
        {
            $key = strtoupper($day);
            $name = ucfirst($day);

            if(is_null(Day::firstWhere('key', $key)))
            {
                Day::create([
                    'name' => $name,
                    'key' => $key,
                    'iso_8601' => $i + 1,
                    'numeric' => $key === 'SUNDAY' ? 0 : $i + 1
                ]);
            }
        }
    }
}
