<?php

namespace Tests\Feature\ProjectRelations;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use App\Models\Project;
use App\Models\Installation;
use App\Models\Finding;
use App\Models\Repository;

class RepositoryTest extends TestCase
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
        $this->repository = factory(Repository::class)->create();
        $this->finding = factory(Finding::class)->make();
        $this->installation = factory(Installation::class)->make();
    }

    /**
     * Associate the factory models.
     */
    private function associateModels()
    {
        $this->repository->findings()->save($this->finding);
        $this->repository->installations()->save($this->installation);
    }

    /**
     * Assert for Projects.
     */
    private function assertProjects()
    {
        $this->assertEquals($this->project->id, $this->finding->projects()->first()->id);
        $this->assertEquals($this->project->id, $this->installation->projects()->first()->id);
    }

    /**
     * Attach a Project into a Repository.
     *
     * @return void
     */
    public function testAttachProjectIntoRepository()
    {
        $this->associateModels();

        $this->repository->projects()->attach($this->project);

        $this->assertProjects();
    }

    /**
     * Attach a Repository into a Project.
     *
     * @return void
     */
    public function testAttachRepositoryIntoProject()
    {
        $this->associateModels();

        $this->project->repositories()->attach($this->repository);

        $this->assertProjects();
    }

    /**
     * Attach a Repository into a Project, then attach other models into the Repository.
     *
     * @return void
     */
    public function testRepositoryRelationships()
    {
        $this->project->repositories()->attach($this->repository);

        $this->associateModels();

        $this->assertProjects();
    }
}
