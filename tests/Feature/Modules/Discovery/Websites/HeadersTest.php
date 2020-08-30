<?php

namespace Tests\Feature\KeyModels;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Libs\Modules\Discovery\Websites\Headers as Module;
use App\Models\Website;
use App\Models\ModuleLog;

class HeadersTest extends TestCase
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

    public function testObtainHeaders()
    {
        $website = new Website(['url' => 'https://google.com']);
        $website->key = true;
        $website->save();

        (new Module($website))->execute();

        $this->assertTrue($website->headers()->where('name', 'Content-Type')->where('value', 'text/html; charset=UTF-8')->exists());
    }

    public function testDeleteOldHeaders()
    {
        $website = new Website(['url' => 'https://google.com']);
        $website->key = true;
        $website->save();

        $query = $website->headers()->where('name', 'Set-Cookie')->where('value', 'like', '%google.com%');

        (new Module($website))->execute();
        $this->assertTrue($query->exists());

        $website->url = 'https://bing.com';
        $website->save();

        ModuleLog::truncate();
        (new Module($website))->execute();
        
        $this->assertFalse($query->exists());
    }
}
