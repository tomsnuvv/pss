<?php

use Illuminate\Database\Seeder;
use App\Models\ProductType;

class ProductsTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductType::firstOrCreate([
            'name' => 'CMS',
        ]);
        ProductType::firstOrCreate([
            'name' => 'WordPress Plugin',
        ]);
        ProductType::firstOrCreate([
            'name' => 'WordPress Theme',
        ]);
        ProductType::firstOrCreate([
            'name' => 'Composer Package',
        ]);
        ProductType::firstOrCreate([
            'name' => 'Service',
        ]);
        ProductType::firstOrCreate([
            'name' => 'OS',
        ]);
        ProductType::firstOrCreate([
            'name' => 'Kernel',
        ]);
        ProductType::firstOrCreate([
            'name' => 'Package',
        ]);
        ProductType::firstOrCreate([
            'name' => 'Javascript',
        ]);
        ProductType::firstOrCreate([
            'name' => 'Web App',
        ]);
        ProductType::firstOrCreate([
            'name' => 'Jenkins Plugin',
        ]);
    }
}
