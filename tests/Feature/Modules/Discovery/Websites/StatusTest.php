<?php

namespace Tests\Feature\KeyModels;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Libs\Modules\Discovery\Websites\Status as Module;
use App\Models\Website;
use App\Models\Project;

class StatusTest extends TestCase
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

    public function testStatus200()
    {
        $website = new Website(['url' => 'https://google.com']);
        $website->key = true;
        $website->save();

        (new Module($website))->execute();
        $this->assertEquals($website->status, 200);

        $request = $website->requests()->first();
        $this->assertEquals($request->status, 200);
    }

    public function testStatus301()
    {
        $website = new Website(['url' => 'https://php.net']);
        $website->key = true;
        $website->save();

        $project = factory(Project::class)->create();
        $website->projects()->attach($project);

        // Prevent the Website Observer to store the last URL
        $website->url = 'http://php.net';
        $website->save();

        (new Module($website))->execute();
        $this->assertEquals($website->status, 301);

        // Create a new website based on the redirection
        $website2 = Website::where('url', 'https://www.php.net')->first();
        $this->assertEquals($website2->id, 2);

        // The new Website will be added in the original Website Projects
        $this->assertEquals($project->id, $website2->projects()->first()->id);
    }

    public function testStatus404()
    {
        $website = new Website(['url' => 'https://google.com']);
        $website->key = true;
        $website->save();

        // Websites doesn't allow paths for now
        $website->url = 'https://google.com/nonexistent';
        $website->save();
        (new Module($website))->execute();
        $this->assertEquals($website->status, 404);
    }
}
