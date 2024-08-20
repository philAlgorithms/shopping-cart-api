<?php

namespace Database\Seeders;

use App\Models\Media\Disk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $disks = config('filesystems.disks');
        foreach($disks as $name=>$attributes)
        {
            Disk::create(['name' => $name]);
        }
    }
}
