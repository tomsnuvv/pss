<?php

namespace App\Libs\Helpers;

use App\Models\Repository;
use App\Models\Product;
use App\Models\Module;

/**
 * Repositories Helper class.
 */
class Repositories
{
    /**
     * Providers class relation.
     *
     * @var array
     */
    const PROVIDERS = [
        'github' => \App\Libs\Providers\Repositories\Github::class,
    ];

    /**
     * Get the repository provider.
     *
     * @param  \App\Models\Repository $repository
     * @return \App\Libs\Contracts\Providers\Abstracts\Repositories
     */
    public static function getProvider(Repository $repository)
    {
        if (strstr($repository->url, 'https://github.com/')) {
            $provider = 'github';
        }

        $class = self::PROVIDERS[$provider];

        return new $class($repository);
    }

    /**
     * Create (if new) an Installation model, related to a repository.
     *
     * @param  \App\Models\Repository $repository
     * @param  \App\Models\Product    $product
     * @param  string                 $version
     * @param  \App\Models\Module     $module
     * @return \App\Models\Installation
     */
    public static function installProduct(Repository $repository, Product $product, $version, Module $module)
    {
        $installation = $repository->installations()->firstOrNew(['product_id' => $product->id]);
        $installation->version = $version;
        $installation->module()->associate($module);
        $installation->save();

        return $installation;
    }
}
