<?php

use Illuminate\Database\Seeder;
use App\Models\FindingStatus;

class FindingStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FindingStatus::firstOrCreate([
            'name' => 'Open',
        ]);
        FindingStatus::firstOrCreate([
            'name' => 'Fixed',
        ]);
        FindingStatus::firstOrCreate([
            'name' => 'False Positive',
        ]);
    }
}
