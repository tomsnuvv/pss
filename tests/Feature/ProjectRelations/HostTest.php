<?php

namespace Tests\Feature\ProjectRelations;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use App\Models\Domain;
use App\Models\Host;
use App\Models\Project;
use App\Models\Finding;
use App\Models\Certificate;
use App\Models\Port;
use App\Models\Website;

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

        Notification::fake();

        $this->createModels();
    }

    /**
     * Create the factory models.
     */
    private function createModels()
    {
        $this->project = factory(Project::class)->create();
        $this->host = factory(Host::class)->create();
        $this->domain = factory(Domain::class)->create();
        $this->certificate = factory(Certificate::class)->create();
        $this->website = factory(Website::class)->create();
        $this->finding = factory(Finding::class)->make();
        $this->port = factory(Port::class)->make();
    }

    /**
     * Associate the factory models.
     */
    private function associateModels()
    {
        $this->host->domains()->save($this->domain);
        $this->host->ports()->save($this->port);
        $this->host->websites()->attach($this->website);
        $this->host->findings()->save($this->finding);
        $this->host->certificates()->save($this->certificate);
    }

    /**
     * Assert for Projects.
     */
    private function assertProjects()
    {
        $this->assertEquals($this->project->id, $this->host->projects()->first()->id);
        $this->assertEquals($this->project->id, $this->port->projects()->first()->id);
        $this->assertEquals($this->project->id, $this->finding->projects()->first()->id);
        $this->assertNull($this->domain->projects()->first());
        $this->assertNull($this->website->projects()->first());
    }

    /**
     * Attach a Project into a Host.
     *
     * @return void
     */
    public function testAttachProjectIntoHost()
    {
        $this->associateModels();

        $this->host->projects()->attach($this->project);

        $this->assertProjects();
    }

    /**
     * Attach a Host into a Project.
     *
     * @return void
     */
    public function testAttachHostIntoProject()
    {
        $this->associateModels();

        $this->project->hosts()->attach($this->host);

        $this->assertProjects();
    }

    /**
     * Attach a Host into a Project, then attach other models into the Host.
     *
     * @return void
     */
    public function testHostRelationships()
    {
        $this->project->hosts()->attach($this->host);

        $this->associateModels();

        $this->assertProjects();
    }
}
