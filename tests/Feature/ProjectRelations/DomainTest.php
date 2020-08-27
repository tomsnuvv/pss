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
use App\Models\Website;

class DomainTest extends TestCase
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
        $this->domain = factory(Domain::class)->create();
        $this->host = factory(Host::class)->create();
        $this->subdomain = factory(Domain::class)->create();
        $this->parent = factory(Domain::class)->create();
        $this->finding = factory(Finding::class)->make();
        $this->certificate = factory(Certificate::class)->create();
        $this->website = factory(Website::class)->create();
    }

    /**
     * Associate the factory models.
     */
    private function associateModels()
    {
        $this->domain->parent()->associate($this->parent);
        $this->domain->host()->associate($this->host);
        $this->domain->certificate()->associate($this->certificate);
        $this->domain->save();

        $this->domain->subdomains()->save($this->subdomain);
        $this->domain->websites()->attach($this->website);
        $this->domain->findings()->save($this->finding);
    }

    /**
     * Assert for Projects.
     */
    private function assertProjects()
    {
        $this->assertEquals($this->project->id, $this->host->projects()->first()->id);
        $this->assertEquals($this->project->id, $this->certificate->projects()->first()->id);
        $this->assertEquals($this->project->id, $this->subdomain->projects()->first()->id);
        $this->assertEquals($this->project->id, $this->website->projects()->first()->id);
        $this->assertEquals($this->project->id, $this->finding->projects()->first()->id);
        $this->assertNull($this->parent->projects()->first());
    }

    /**
     * Attach a Project into a Domain.
     *
     * @return void
     */
    public function testAttachProjectIntoDomain()
    {
        $this->associateModels();

        $this->domain->projects()->attach($this->project);

        $this->assertProjects();
    }

    /**
     * Attach a Domain into a Project.
     *
     * @return void
     */
    public function testAttachDomainIntoProject()
    {
        $this->associateModels();

        $this->project->domains()->attach($this->domain);

        $this->assertProjects();
    }

    /**
     * Attach a Domain into a Project, then attach other models into the domain.
     *
     * @return void
     */
    public function testDomainRelationships()
    {
        $this->project->domains()->attach($this->domain);

        $this->associateModels();

        $this->assertProjects();
    }
}
