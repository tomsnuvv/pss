<?php

use Illuminate\Database\Seeder;
use App\Models\ModuleLogStatus;

class ModuleLogStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModuleLogStatus::firstOrCreate([
            'name' => 'Started',
        ]);
        ModuleLogStatus::firstOrCreate([
            'name' => 'Finished',
        ]);
        ModuleLogStatus::firstOrCreate([
            'name' => 'Can\'t run',
        ]);
        ModuleLogStatus::firstOrCreate([
            'name' => 'Error',
        ]);
    }
}
