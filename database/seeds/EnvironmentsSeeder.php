<?php

use Illuminate\Database\Seeder;
use App\Models\Environment;

class EnvironmentsSeeder extends Seeder
{
    /**
     * Environments.
     */
    const ENVIRONMENTS = [
        ['name' => 'Prod', 'public' => 1],
        ['name' => 'Demo', 'public' => 1],
        ['name' => 'Accept'],
        ['name' => 'Stage'],
        ['name' => 'Dev'],
        ['name' => 'Test'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::ENVIRONMENTS as $env) {
            $this->createEnvironment($env['name'], isset($env['public']) ? $env['public'] : 0);
        }
    }

    /**
     * Creates an Environment.
     *
     * @param string $name
     * @param int    $public
     *
     * @return App\Models\Environment
     */
    private function createEnvironment($name, $public = 0)
    {
        return Environment::firstOrCreate([
            'name'   => $name,
            'public' => $public,
        ]);
    }
}
