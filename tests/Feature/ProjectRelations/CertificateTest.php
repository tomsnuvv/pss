<?php

namespace Tests\Feature\ProjectRelations;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use App\Models\Project;
use App\Models\Finding;
use App\Models\Domain;
use App\Models\Host;
use App\Models\Certificate;

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

        Notification::fake();

        $this->createModels();
    }

    /**
     * Create the factory models.
     */
    private function createModels()
    {
        $this->project = factory(Project::class)->create();
        $this->certificate = factory(Certificate::class)->create();
        $this->domain = factory(Domain::class)->create();
        $this->host = factory(Host::class)->create();
        $this->finding = factory(Finding::class)->make();
    }

    /**
     * Associate the factory models.
     */
    private function associateModels()
    {
        $this->finding->target()->associate($this->domain);
        $this->certificate->findings()->save($this->finding);
        $this->certificate->domains()->save($this->domain);
        $this->certificate->hosts()->save($this->host);
    }

    /**
     * Assert for Projects.
     */
    private function assertProjects()
    {
        $this->assertEquals($this->project->id, $this->finding->projects()->first()->id);
        $this->assertNull($this->domain->projects()->first());
        $this->assertNull($this->host->projects()->first());
    }

    /**
     * Attach a Project into a Certificate.
     *
     * @return void
     */
    public function testAttachProjectIntoCertificate()
    {
        $this->associateModels();

        $this->certificate->projects()->attach($this->project);

        $this->assertProjects();
    }

    /**
     * Attach a Certificate into a Project.
     *
     * @return void
     */
    public function testAttachCertificateIntoProject()
    {
        $this->associateModels();

        $this->project->certificates()->attach($this->certificate);

        $this->assertProjects();
    }

    /**
     * Attach a Certificate into a Project, then attach other models into the Certificate.
     *
     * @return void
     */
    public function testCertificateRelationships()
    {
        $this->project->certificates()->attach($this->certificate);

        $this->associateModels();

        $this->assertProjects();
    }
}
