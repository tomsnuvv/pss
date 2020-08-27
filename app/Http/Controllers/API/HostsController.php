<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\Host;
use App\Models\Module;
use App\Models\HostType;

/**
 * Hosts API Controller.
 */
class HostsController extends Controller
{
    /**
     * Current Host.
     *
     * @var \App\Models\Host
     */
    private $host;

    /**
     * Module Code
     *
     * @const string
     */
    const CODE = 'Discovery\Hosts\Products\PSS';

    /**
     * Loads the host model based on the request origin.
     *
     * @param \Illuminate\Http\Request $request
     */
    private function authenticate(Request $request)
    {
        $ip = $request->ip();

        $this->host = Host::firstOrCreate([
            'type_id' => HostType::server()->first()->id,
            'ip' => $ip,
            'name' => gethostbyaddr($ip),
        ]);
    }

    /**
     * Perform a host sync
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function sync(Request $request)
    {
        $this->authenticate($request);

        $this->importSystem(ProductType::whereName('OS')->first()->id, $request->post('os'));
        $this->importSystem(ProductType::whereName('Kernel')->first()->id, $request->post('kernel'));
        $this->importPackages($request->post('packages'));

        return response()->json(['status' => 'ok']);
    }

    /**
     * Import System products (OS and Kernel)
     *
     * @param  int $type
     * @param  array $data
     */
    private function importSystem($type, $data)
    {
        if (is_array($data) && isset($data['name']) && isset($data['name'])) {
            $this->importProduct($type, $data['name'], $data['version']);
        }
    }

    /**
     * Import the packages products.
     *
     * @param array $data
     */
    private function importPackages($data)
    {
        if (!is_array($data)) {
            return;
        }

        $productType = ProductType::whereName('Package')->first()->id;

        foreach ($data as $package) {
            if (isset($package['name']) && isset($package['version'])) {
                $this->importProduct($productType, $package['name'], $package['version']);
            }
        }
    }

    /**
     * Import a package products.
     *
     * @param int $type
     * @param string $name
     * @param string $version
     *
     * @return \App\Models\Installation
     */
    private function importProduct($type, $name, $version)
    {
        $product = Product::firstOrCreate([
            'type_id' => $type,
            'name' => $name,
            'code' => $name,
        ]);

        return $this->host->installations()->firstOrCreate([
            'module_id' => Module::whereCode(self::CODE)->first()->id,
            'product_id' => $product->id,
            'version' => $version,
        ]);
    }
}
