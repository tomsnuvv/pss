<?php

use Illuminate\Database\Seeder;
use App\Models\IntegrationType;

class IntegrationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IntegrationType::firstOrCreate([
            'name' => 'Github',
        ]);
        IntegrationType::firstOrCreate([
            'name' => 'Slack',
        ]);
    }
}
