<?php

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\ProductType;
use App\Models\ProductLicense;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->create('WordPress', 'wordpress', 'free', 'cms', ['wordpress/wordpress', 'wp']);
        $this->create('Joomla', 'joomla', 'free', 'cms', ['joomla!']);
        $this->create('modernizr', 'modernizr', 'free', 'javascript');
        $this->create('jquery', 'jquery', 'free', 'javascript');
        $this->create('Cloudbees', 'jenkins', 'free', 'webApp', ['jenkins/jenkins', 'cloudbees/jenkins']);

        $this->create('Team Yoast', 'wordpress-seo', 'comercial', 'wordpressPlugin', [
            'wordpress-seo',
            'yoast-seo',
        ]);

        $this->create('Apache', 'http-server', 'free', 'service', [
            'apache-httpd',
            'apache-http-server',
            'apache/http-server',
        ]);

        $this->create('nginx', 'nginx', 'free', 'service', [
            'nginx',
            'nginx/nginx',
        ]);

        $this->create('openbsd', 'openssh', 'free', 'service', [
            'openssh',
        ]);

        $this->create('wietse_venema', 'postfix', 'free', 'service', [
            'postfix-smtpd',
            'Postfix',
        ]);

        $this->create('microsoft', 'iis', 'comercial', 'service', [
            'microsoft-iis-httpd-6.0',
            'microsoft-iis-httpd-7.0',
            'microsoft-iis-httpd-7.5',
            'microsoft-iis-httpd',
            'microsoft-iis',
            'iis',
        ]);

        $this->create('double_precision-incorporated', 'courier-mta', 'free', 'service', [
            'courier-imapd',
            'courier-pop3d',
            'courier-esmtp',
            'courier',
        ]);

        $this->create('Jenkins', 'blue-ocean', 'free', 'jenkinsPlugin', ['blueocean']);
        $this->create('Jenkins', 'pam-auth', 'free', 'jenkinsPlugin', ['pluggable-authentication-module']);
        $this->create('Jenkins', 'github-oauth', 'free', 'jenkinsPlugin', ['github-authentication']);
        $this->create('Jenkins', 'warnings-ng', 'free', 'jenkinsPlugin', ['warnings-next-generation']);
        $this->create('Jenkins', 'email-ext', 'free', 'jenkinsPlugin', ['email-extension']);
        $this->create('Jenkins', 'workflow-cps', 'free', 'jenkinsPlugin', ['pipeline:-groovy', 'pipeline:groovy-plugin']);


        $this->create('miniOrange', 'miniorange-sso', 'comercial', 'wordpressPlugin', [
            'miniorange-saml-20-single-sign-on',
            'miniorange',
            'miniorange-sso'
        ]);

    }

    /**
     * Create (if doesn't exist) a Product.
     *
     * @param  string $vendor
     * @param  array  $code
     * @param  string $license
     * @param  string $type
     * @param  array  $synonyms
     */
    protected function create($vendor, $code, $license, $type, $synonyms = [])
    {
        $vendor = Vendor::firstOrCreate(['name' => $vendor]);
        $product = $vendor->products()->firstOrCreate(['code' => $code]);
        $product->license()->associate(ProductLicense::$license()->first());
        $product->type()->associate(ProductType::$type()->first());
        $product->save();

        if (!empty($synonyms)) {
            foreach ($synonyms as $synonym) {
                $product->synonyms()->firstOrCreate(['name' => $synonym]);
                $this->merge($product, $synonym);
            }
        }
    }

    /**
     * Merge products.
     *
     * @param  \App\Models\Product $product
     * @param  string              $synonym
     * @return void
     */
    protected function merge(Product $product, $synonym)
    {
        $related = $product->vendor->products()->whereCode($synonym)->where('id', '!=', $product->id)->first();
        if (!$related) {
            return;
        }

        foreach ($related->vulnerabilities()->get() as $vulnerability) {
            $vulnerability->product()->associate($product);
            $vulnerability->save();
        }

        foreach ($related->installations()->get() as $installation) {
            $installation->product()->associate($product);
            $installation->save();
        }

        $related->delete();

        echo 'Product ' . $related->code . '(' . $related->id . ') deleted' . PHP_EOL;
    }
}
