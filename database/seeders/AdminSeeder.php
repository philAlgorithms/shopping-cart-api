<?php

namespace Database\Seeders;

use App\Models\Location\Country;
use App\Models\User;
use App\Models\Users\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Admin::firstWhere('email', 'info.samandcart@gmail.com'))
        {
            $country = Country::firstWhere('nicename', 'Nigeria');
            $sam = User::create([
                'first_name' => 'Samson',
                'last_name' => 'Ukwueze',
                'country_id' => $country->id,
                'phone_number' => '+2347061853005'
            ]);
            $sam->admin()->create([
                'email' => 'info.samandcart@gmail.com',
                'password' => bcrypt('Sam##769')]);
        }
    }
}
