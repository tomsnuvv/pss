<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(RolesSeeder::class);
        $this->call(FindingStatusesSeeder::class);
        $this->call(SeveritiesSeeder::class);
        $this->call(VulnerabilityTypesSeeder::class);
        $this->call(EnvironmentsSeeder::class);
        $this->call(ProductsTypesSeeder::class);
        $this->call(ProductsLicensesSeeder::class);
        $this->call(VendorsSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(ModulesSeeder::class);
        $this->call(HostsTypesSeeder::class);
        $this->call(ModuleLogStatusesSeeder::class);
        $this->call(IntegrationTypesSeeder::class);

        Model::reguard();
    }
}
