<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Libs\Helpers\Products;
use App\Models\Product;

class ProductCreationTest extends TestCase
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
    }

    /**
     * A new product with an already existing code / synonym,
     * but with a different type, will merge into the
     * already existing product.
     */
    public function testAssociationBySynonym()
    {
        $original = Products::createWordPressPluginProduct('test-vendor/test-product');
        $original->synonyms()->firstOrCreate(['name' => 'test-vendor/test-product']);

        // Different type, same code, same vendor, matching synonym
        $dupe = Products::createComposerProduct('test-vendor/test-product');
        $this->assertEquals($dupe->id, $original->id);

        // Different type, same code, different vendor, matching synonym
        $original->synonyms()->firstOrCreate(['name' => 'test-vendor2/test-product']);
        $dupe = Products::createComposerProduct('test-vendor2/test-product');
        $this->assertEquals($dupe->id, $original->id);

        // Different type, different code, different vendor, matching synonym
        $original->synonyms()->firstOrCreate(['name' => 'test-apache']);
        $dupe = Products::createServiceProduct('test-apache');
        $this->assertEquals($dupe->id, $original->id);
    }

    /**
     * Tests product duplication with different product names.
     */
    public function testProductSeeds()
    {
        $original = Product::where('name', 'nginx / nginx')->first();
        $dupe = Products::createServiceProduct('nginx');
        $this->assertEquals($dupe->id, $original->id);

        $original = Product::where('name', 'Apache / http-server')->first();
        $dupe = Products::createServiceProduct('apache-httpd');
        $this->assertEquals($dupe->id, $original->id);

        $original = Product::where('name', 'Team Yoast / wordpress-seo')->first();
        $dupe = Products::createServiceProduct('wordpress-seo');
        $this->assertEquals($dupe->id, $original->id);
    }

    /**
     * Avoid duplications when using the same vendor, code and type.
     */
    public function testAvoidDuplications(){
        $original = Products::createJavascriptProduct('@babel/code-frame');
        $dupe = Products::createJavascriptProduct('@babel/code-frame');
        $this->assertEquals($dupe->id, $original->id);
        
        Products::createServiceProduct('vendor/code-frame');
        $dupe = Products::createJavascriptProduct('code-frame');
        $this->assertEquals($dupe->id, $original->id);

        $original = Products::createWordPressPluginProduct('booking');
        $dupe = Products::createWordPressPluginProduct('booking');
        $this->assertEquals($dupe->id, $original->id);
    }
}
