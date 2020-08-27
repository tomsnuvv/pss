<?php

namespace Tests\Feature\ProjectRelations;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use App\Models\Project;
use App\Models\Finding;
use App\Models\Installation;
use App\Models\Host;
use App\Models\Port;

class PortTest extends TestCase
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
        $this->port = factory(Port::class)->make();
        $this->host = factory(Host::class)->create();
        $this->finding = factory(Finding::class)->make();
        $this->installation = factory(Installation::class)->make();
    }

    /**
     * Associate the factory models.
     */
    private function associateModels()
    {
        $this->port->host()->associate($this->host);
        $this->port->save();

        $this->installation->source()->associate($this->host);
        $this->port->installation()->save($this->installation);

        $this->finding->target()->associate($this->host);
        $this->port->findings()->save($this->finding);
    }

    /**
     * Assert for Projects.
     */
    private function assertProjects()
    {
        $this->assertEquals($this->project->id, $this->finding->projects()->first()->id);
        $this->assertEquals($this->project->id, $this->installation->projects()->first()->id);
        $this->assertNull($this->host->projects()->first());
    }

    /**
     * Attach a Project into a Port.
     *
     * @return void
     */
    public function testAttachProjectIntoPort()
    {
        $this->associateModels();

        $this->port->projects()->attach($this->project);

        $this->assertProjects();
    }

    /**
     * Attach a Port into a Project.
     *
     * @return void
     */
    public function testAttachPortIntoProject()
    {
        $this->associateModels();

        $this->project->ports()->attach($this->port);

        $this->assertProjects();
    }

    /**
     * Attach a Port into a Project, then attach other models into the Port.
     *
     * @return void
     */
    public function testPortRelationships()
    {
        $this->port->host()->associate($this->host);
        $this->port->save();

        $this->project->ports()->attach($this->port);

        $this->associateModels();

        $this->assertProjects();
    }
}
