<?php

use Illuminate\Database\Seeder;
use App\Models\ProductLicense;

class ProductsLicensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductLicense::firstOrCreate([
            'name' => 'Free',
        ]);
        ProductLicense::firstOrCreate([
            'name' => 'Comercial',
        ]);
        ProductLicense::firstOrCreate([
            'name' => 'Internal',
        ]);
    }
}
