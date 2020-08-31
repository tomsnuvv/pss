<?php

namespace Tests\Feature\KeyModels;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Libs\Modules\Discovery\Websites\Domains as Module;
use App\Models\Website;
use App\Models\Project;
use App\Models\ModuleLog;
use App\Models\ModuleLogStatus;

class DomainsTest extends TestCase
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

    public function testDomain()
    {
        $website = new Website(['url' => 'https://www.google.com']);
        $website->key = true;
        $website->save();

        (new Module($website))->execute();
        $log = ModuleLog::all()->last();
        $this->assertEquals($log->status_id, ModuleLogStatus::finished()->first()->id);

        $domains = $website->domains;
        $this->assertCount(1, $domains);
        $domain = $domains->first();
        $this->assertEquals($domain->name, 'www.google.com');
    }

    public function testHost()
    {
        $website = new Website(['url' => 'https://1.1.1.1']);
        $website->key = true;
        $website->save();

        (new Module($website))->execute();
        $log = ModuleLog::all()->last();
        $this->assertEquals($log->status_id, ModuleLogStatus::cantRun()->first()->id);
    }
}
