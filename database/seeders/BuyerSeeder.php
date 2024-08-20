<?php

namespace Database\Seeders;

use App\Models\Users\Buyer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BuyerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Buyer::factory()->create([
            'email' => 'boscophilz@gmail.com',
            'password' => bcrypt('1234')
        ]);
    }
}
