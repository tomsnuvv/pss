<?php

use Illuminate\Database\Seeder;
use App\Models\HostType;

class HostsTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HostType::firstOrCreate([
            'name' => 'Server',
        ]);
        HostType::firstOrCreate([
            'name' => 'Nameserver',
        ]);
        HostType::firstOrCreate([
            'name' => 'Workstation',
        ]);
    }
}
