<?php

namespace Tests\Feature\KeyModels;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Libs\Modules\Discovery\Websites\Certificate as Module;
use App\Models\Website;
use App\Models\Project;
use App\Models\ModuleLog;
use App\Models\ModuleLogStatus;

class CertificateTest extends TestCase
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
        $website = new Website(['url' => 'https://google.com']);
        $website->key = true;
        $website->save();

        (new Module($website))->execute();
        $log = ModuleLog::all()->last();
        $this->assertEquals($log->status_id, ModuleLogStatus::finished()->first()->id);

        $domains = $website->domains;
        $this->assertCount(1, $domains);
        $certificate = $domains->first()->certificate;
        $this->assertIsInt($certificate->id);
        $this->assertEquals($certificate->subject_common_name, 'www.google.com');
    }

    public function testHost()
    {
        $website = new Website(['url' => 'https://1.1.1.1']);
        $website->key = true;
        $website->save();

        (new Module($website))->execute();

        $hosts = $website->hosts;
        $this->assertCount(1, $hosts);
        $certificates = $hosts->first()->certificates;
        $this->assertCount(1, $certificates);
        $this->assertEquals($certificates->first()->subject_common_name, 'cloudflare-dns.com');
    }

    public function testCantRunOnNonHttp()
    {
        $website = new Website(['url' => 'http://www.stealmylogin.com']);
        $website->key = true;
        $website->save();

        (new Module($website))->execute();
        $log = ModuleLog::all()->last();
        $this->assertEquals($log->status_id, ModuleLogStatus::cantRun()->first()->id);
    }
}
