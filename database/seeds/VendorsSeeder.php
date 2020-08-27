<?php

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Vendor::firstOrCreate([
            'name' => 'WordPress',
        ]);

        Vendor::firstOrCreate([
            'name' => 'Jenkins',
        ]);

        Vendor::firstOrCreate([
            'name' => 'modernizr',
        ]);

        Vendor::firstOrCreate([
            'name' => 'jquery',
        ]);
    }
}
