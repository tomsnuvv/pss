<?php

namespace Tests\Feature\KeyModels;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Libs\Modules\Discovery\Certificates\Domain as Module;
use App\Models\Certificate;
use App\Models\Domain;
use App\Models\ModuleLog;

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
    }

    /**
     * When discoverying Domains from a Certificate,
     * the domains will only be set as key if the discovered domain
     * has a parent domain and is set as key.
     */
    public function testDiscoveryCertificatesDomain()
    {
        $domain = factory(Domain::class)->create();
        $domain->key = true;
        $domain->save();

        $subdomainName = 'subdomain.' . $domain->name;
        $certificate = factory(Certificate::class)->create();
        $certificate->subject_common_name = $subdomainName;
        $certificate->save();

        (new Module($certificate))->execute();
        $subdomain = $domain->subdomains()->first();
        $this->assertEquals($subdomain->key, 1);

        $subdomain->delete();
        $domain->key = false;
        $domain->save();

        ModuleLog::query()->delete();

        (new Module($certificate))->execute();
        $this->assertEquals($domain->subdomains()->first()->key, 0);
    }
}
