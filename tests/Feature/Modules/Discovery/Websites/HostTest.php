<?php

namespace Tests\Feature\KeyModels;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Libs\Modules\Discovery\Websites\Host as Module;
use App\Models\Website;
use App\Models\Project;
use App\Models\ModuleLog;
use App\Models\ModuleLogStatus;

class HostTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    /**
     * Tests setup.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->withoutEvents();
    }

    public function testHost()
    {
        $website = new Website(['url' => 'https://github.com']);
        $website->key = true;
        $website->save();

        $ip = gethostbyname('github.com');

        (new Module($website))->execute();
        $log = ModuleLog::all()->last();
        $this->assertEquals($log->status_id, ModuleLogStatus::finished()->first()->id);

        $hosts = $website->hosts;
        $this->assertCount(1, $hosts);
        $this->assertEquals($hosts->first()->ip, $ip);
    }
}
