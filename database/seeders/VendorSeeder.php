<?php

namespace Database\Seeders;

use App\Models\Location\{Country, Town};
use App\Models\User;
use App\Models\Users\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Vendor::firstWhere('email', 'seller.samandcart@gmail.com')) {
            $country = Country::firstWhere('nicename', 'Nigeria');
            $town = Town::firstWhere('name', 'Nsukka');
            $sam = User::create([
                'first_name' => 'Samson',
                'last_name' => 'Ukwueze',
                'country_id' => $country->id,
                'phone_number' => '+2348106212763',
            ]);
            $vendor = $sam->vendor()->create([
                'email' => 'seller.samandcart@gmail.com',
                'password' => bcrypt('Sam##769'),
                'town_id' => $town->id ?? 1
            ]);

            $vendor->store()->create([
                'name' => "Samandcart",
                'description' => "You get what's in the cart",
                'key' => "samandcart"
            ]);
        }
    }
}
