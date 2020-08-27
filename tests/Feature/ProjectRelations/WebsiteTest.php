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
use App\Models\Website;
use App\Models\Installation;

class WebsiteTest extends TestCase
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
        $this->website = factory(Website::class)->create();
        $this->domain = factory(Domain::class)->create();
        $this->host = factory(Host::class)->create();
        $this->finding = factory(Finding::class)->make();
        $this->installation = factory(Installation::class)->make();
    }

    /**
     * Associate the factory models.
     */
    private function associateModels()
    {
        $this->website->domains()->attach($this->domain);
        $this->website->hosts()->attach($this->host);
        $this->website->findings()->save($this->finding);
        $this->website->installations()->save($this->installation);
    }

    /**
     * Assert for Projects.
     */
    private function assertProjects()
    {
        $this->assertEquals($this->project->id, $this->domain->projects()->first()->id);
        $this->assertEquals($this->project->id, $this->host->projects()->first()->id);
        $this->assertEquals($this->project->id, $this->finding->projects()->first()->id);
        $this->assertEquals($this->project->id, $this->installation->projects()->first()->id);
    }

    /**
     * Attach a Project into a Website.
     *
     * @return void
     */
    public function testAttachProjectIntoWebsite()
    {
        $this->associateModels();

        $this->website->projects()->attach($this->project);

        $this->assertProjects();
    }

    /**
     * Attach a Website into a Project.
     *
     * @return void
     */
    public function testAttachWebsiteIntoProject()
    {
        $this->associateModels();

        $this->project->websites()->attach($this->website);

        $this->assertProjects();
    }

    /**
     * Attach a Website into a Project, then attach other models into the Website.
     *
     * @return void
     */
    public function testWebsiteRelationships()
    {
        $this->associateModels();

        $this->project->domains()->attach($this->website);

        $this->assertProjects();
    }
}
