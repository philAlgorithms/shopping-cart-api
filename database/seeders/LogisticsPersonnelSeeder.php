<?php

namespace Database\Seeders;

use App\Models\Location\Country;
use App\Models\User;
use App\Models\Users\LogisticsPersonnel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LogisticsPersonnelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!LogisticsPersonnel::firstWhere('email', 'logistics.samandcart@gmail.com'))
        {
            $country = Country::firstWhere('nicename', 'Nigeria');
            $sam = User::create([
                'first_name' => 'Samson',
                'last_name' => 'Ukwueze',
                'country_id' => $country->id,
                'phone_number' => '+2347061853005x'
            ]);
            $sam->logisticsPersonnel()->create([
                'email' => 'logistics.samandcart@gmail.com',
                'password' => bcrypt('Sam##769'),
                'base_town_id' => 326
            ]);
        }
    }
}
