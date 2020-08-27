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
use App\Models\Installation;
use App\Models\Repository;

class InstallationTest extends TestCase
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
     * Associate the Installation to it's source model Projects as Website
     *
     * @return void
     */
    public function testInstallationWebsite()
    {
        $website = factory(Website::class)->create();
        $website->projects()->attach($this->project);

        $installation = factory(Installation::class)->make();
        $installation->source()->associate($website);
        $installation->save();

        $this->assertEquals($this->project->id, $installation->projects()->first()->id);
    }

    /**
     * Associate the Installation to it's source model Projects as Host
     *
     * @return void
     */
    public function testInstallationHost()
    {
        $host = factory(Host::class)->create();
        $host->projects()->attach($this->project);

        $installation = factory(Installation::class)->make();
        $installation->source()->associate($host);
        $installation->save();

        $this->assertEquals($this->project->id, $installation->projects()->first()->id);
    }

    /**
     * Associate the Installation to it's source model Projects as Repository
     *
     * @return void
     */
    public function testInstallationRepository()
    {
        $repository = factory(Repository::class)->create();
        $repository->projects()->attach($this->project);

        $installation = factory(Installation::class)->make();
        $installation->source()->associate($repository);
        $installation->save();

        $this->assertEquals($this->project->id, $installation->projects()->first()->id);
    }

    /**
     * Associate the Installation to it's source model Projects as Port
     *
     * @return void
     */
    public function testInstallationPort()
    {
        $host = factory(Host::class)->create();
        $port = factory(Port::class)->make();
        $port->host()->associate($host);
        $port->save();

        $port->projects()->attach($this->project);

        $installation = factory(Installation::class)->make();
        $installation->source()->associate($host);
        $installation->childSource()->associate($port);
        $installation->save();

        $this->assertEquals($this->project->id, $installation->projects()->first()->id);
    }
}
