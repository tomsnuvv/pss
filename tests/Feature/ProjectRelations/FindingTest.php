<?php

namespace Tests\Feature\ProjectRelations;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use App\Models\Domain;
use App\Models\Host;
use App\Models\Port;
use App\Models\Project;
use App\Models\Website;
use App\Models\Finding;
use App\Models\Repository;
use App\Models\Certificate;

class FindingTest extends TestCase
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

        Notification::fake();

        $this->project = factory(Project::class)->create();
    }

    /**
     * Associate the Finding to it's target model Projects as Website
     *
     * @return void
     */
    public function testFindingWebsite()
    {
        $website = factory(Website::class)->create();
        $website->projects()->attach($this->project);

        $finding = factory(Finding::class)->make();
        $finding->target()->associate($website);
        $finding->save();

        $this->assertEquals($this->project->id, $finding->projects()->first()->id);
    }

    /**
     * Associate the Finding to it's target model Projects as Host
     *
     * @return void
     */
    public function testFindingHost()
    {
        $host = factory(Host::class)->create();
        $host->projects()->attach($this->project);

        $finding = factory(Finding::class)->make();
        $finding->target()->associate($host);
        $finding->save();

        $this->assertEquals($this->project->id, $finding->projects()->first()->id);
    }

    /**
     * Associate the Finding to it's target model Projects as Repository
     *
     * @return void
     */
    public function testFindingRepository()
    {
        $repository = factory(Repository::class)->create();
        $repository->projects()->attach($this->project);

        $finding = factory(Finding::class)->make();
        $finding->target()->associate($repository);
        $finding->save();

        $this->assertEquals($this->project->id, $finding->projects()->first()->id);
    }

    /**
     * Associate the Finding to it's target model Projects as Domain
     *
     * @return void
     */
    public function testFindingDomain()
    {
        $domain = factory(Domain::class)->create();
        $domain->projects()->attach($this->project);

        $finding = factory(Finding::class)->make();
        $finding->target()->associate($domain);
        $finding->save();

        $this->assertEquals($this->project->id, $finding->projects()->first()->id);
    }

    /**
     * Associate the Finding to it's target model Projects as Certificate
     *
     * @return void
     */
    public function testFindingCertificate()
    {
        $domain = factory(Domain::class)->create();
        $certificate = factory(Certificate::class)->create();
        $certificate->projects()->attach($this->project);

        $finding = factory(Finding::class)->make();
        $finding->target()->associate($domain);
        $finding->childTarget()->associate($certificate);
        $finding->save();

        $this->assertEquals($this->project->id, $finding->projects()->first()->id);
    }

    /**
     * Associate the Finding to it's target model Projects as Port
     *
     * @return void
     */
    public function testFindingPort()
    {
        $port = factory(Port::class)->make();
        $host = factory(Host::class)->create();
        $port->host()->associate($host);
        $port->save();
        $port->projects()->attach($this->project);

        $finding = factory(Finding::class)->make();
        $finding->target()->associate($host);
        $finding->childTarget()->associate($port);
        $finding->save();

        $this->assertEquals($this->project->id, $finding->projects()->first()->id);
    }
}
