<?php

namespace App\Libs\Helpers;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductLicense;
use App\Models\Vendor;
use App\Models\ProductSynonym;
use App\Libs\Modules\Info\Products\Composer\Packagist;
use App\Libs\Modules\Info\Products\WordPress\WPAPI;
use App\Libs\Modules\Info\Products\Javascript\Yarnpkg;
use App\Libs\Modules\Info\Products\Jenkins\Plugins\Jenkins;

/**
 * Products Helper class.
 */
class Products
{
    /**
     * Create (if new) a WordPress Plugin product.
     *
     * @param  string $code
     * @param  $bool  $forceInfo
     * @return \App\Models\Product
     */
    public static function createWordPressPluginProduct($code, $forceInfo = false)
    {
        return self::createCodeProduct(ProductType::wordpressPlugin()->first(), $code, WPAPI::class, $forceInfo);
    }

    /**
     * Create (if new) a WordPress Theme product.
     *
     * @param  string $code
     * @param  $bool  $forceInfo
     * @return \App\Models\Product
     */
    public static function createWordPressThemeProduct($code, $forceInfo = false)
    {
        return self::createCodeProduct(ProductType::wordpressTheme()->first(), $code, WPAPI::class, $forceInfo);
    }

    /**
     * Create (if new) the WordPress Core product.

     * @return \App\Models\Product
     */
    public static function createWordPressCoreProduct()
    {
        $product = self::createCodeProduct(ProductType::cms()->first(), 'wordpress');
        $product->license()->associate(ProductLicense::free()->first());
        $product->save();

        return $product;
    }

    /**
     * Create (if new) a Composer product.
     *
     * Composer packages can be WP Plugins, WP Themes, CMS and WebApps, so type is not enforced.
     * Vendor is always present.
     *
     * @param  string $code
     * @param  $bool  $forceInfo
     * @return \App\Models\Product
     */
    public static function createComposerProduct($code, $forceInfo = false)
    {
        return self::createCodeProduct(ProductType::composer()->first(), $code, Packagist::class, $forceInfo, false);
    }

    /**
     * Create (if new) a Javascript product.
     *
     * @param  string $code
     * @param  $bool  $forceInfo
     * @return \App\Models\Product
     */
    public static function createJavascriptProduct($code, $forceInfo = false)
    {
        return self::createCodeProduct(ProductType::javascript()->first(), $code, Yarnpkg::class, $forceInfo);
    }

    /**
     * Create (if new) a Jenkins Plugin product.
     *
     * @param  string $code
     * @param  $bool  $forceInfo
     * @return \App\Models\Product
     */
    public static function createJenkinsPluginProduct($code, $forceInfo = false)
    {
        return self::createCodeProduct(ProductType::jenkinsPlugin()->first(), 'Jenkins/' . $code, Jenkins::class, $forceInfo);
    }

    /**
     * Create (if new) a Service product.
     *
     * As most of products don't have a type,
     * lets ignore when searching and update it later.
     * That will help to automatically categorise the products.
     *
     * @param string $code
     * @return \App\Models\Product
     */
    public static function createServiceProduct($code)
    {
        $product = self::createCodeProduct(null, $code);
        if (!$product->type) {
            $product->type()->associate(ProductType::service()->first());
            $product->save();
        }
        return $product;
    }

    /**
     * Creates a generic code-product.
     *
     * @param  \App\Models\ProductType $type
     * @param  string                  $name
     * @param  mixed                   $infoModule
     * @param  bool                    $forceInfo
     * @param  bool                    $forceType
     * @return \App\Models\Product
     */
    public static function createCodeProduct(ProductType $type = null, $name, $infoModule = null, $forceInfo = false, $forceType = true)
    {
        $name = Products::cleanCode($name);

        // Prioritise synonyms, as those are manually populated to avoid product duplitaion
        $synonym = ProductSynonym::where('name', $name)->first();
        if ($synonym) {
            return $synonym->product;
        }

        // vendor/code format
        if (strstr($name, '/')) {
            $tmp = explode('/', $name);
            $vendor = trim($tmp[0]);
            $code = trim($tmp[1]);
        } else {
            $code = $name;
        }

        // Product with a Vendor
        if (isset($vendor) && $vendor) {
            $vendorModel = Vendor::firstOrCreate(['name' => $vendor]);
            $query = $vendorModel->products();
        // Product without a vendor (dangerous!)
        } else {
            $query = Product::query();
        }

        // Search by code & synonyms (code != name)
        $query->where(function ($query) use ($code) {
            $query->where('code', $code)
                ->orWhereHas('synonyms', function ($query) use ($code) {
                    $query->where('name', $code);
                });
        });

        // Force search by type
        if ($type && $forceType) {
            $query->where('type_id', $type->id);
        }

        $total = $query->count();

        if ($total === 1) {
            return $query->first();
        } elseif ($total > 1) {
            if ($type) {
                $query->where('type_id', $type->id);
            }
            // Order by amount of vulnerabilities (most popular one)
            return $query->withCount('affectances')->orderBy('affectances_count', 'desc')->first();
        }

        // No results (new product)
        $product = $query->firstOrCreate(['code' => $code]);
        if ($type) {
            $product->type()->associate($type);
        }
        if (isset($vendorModel)) {
            $product->vendor()->associate($vendorModel);
        }
        $product->save();

        // Obtain product info
        if ((!$product->latest_info_check || $forceInfo) && $infoModule) {
            $module = new $infoModule($product);
            $module->execute();
        }

        return $product;
    }

    /**
     * Creates a Vendor, and associates a product (if any) to it.
     *
     * @param  string              $name
     * @param  \App\Models\Product $product
     * @return \App\Models\Vendor
     */
    public static function createVendor($name, Product $product = null)
    {
        $vendor = Vendor::firstOrCreate(['name' => $name]);
        if ($product) {
            $product->vendor()->associate($vendor);
            $product->save();
        }
        return $vendor;
    }

    /**
     * Get the WordPress Core Product.
     *
     * @return \App\Models\Product
     */
    public static function getWordPressCore()
    {
        return ProductType::cms()->first()->products()->where('code', 'wordpress')->first();
    }

    /**
     * Get the Jenkins Core Product.
     *
     * @return \App\Models\Product
     */
    public static function getJenkinsCore()
    {
        return ProductType::webApp()->first()->products()->where('code', 'jenkins')->first();
    }

    /**
     * Clean a product code, in order to standarise searches.
     *
     * @param  string $code
     * @return string
     */
    public static function cleanCode($code)
    {
        return str_replace([' ', '_'], '-', strtolower($code));
    }

    /**
     * Merge two products.
     *
     * @param  Product $original
     * @param  Product $product
     */
    public static function merge($original, $product)
    {
        if ($original->id == $product->id) {
            return;
        }

        // Transfer the vulnerabilities
        foreach($product->affectances as $affectance){
            $affectance->product()->associate($original);
            $affectance->save();
        }


        // Transfer the installations
        foreach ($product->installations()->get() as $installation) {
            $installation->product()->associate($original);
            $installation->save();
        }

        // Add the synonyms
        $product->synonyms()->delete();
        if ($product->vendor) {
            $name = $product->vendor->name . '/' . $product->code;
        } else {
            $name = $product->code;
        }
        $name = self::cleanCode($name);
        if (ProductSynonym::where('name', $name)->doesntExist()) {
            $original->synonyms()->create(['name' => $name]);
        }


        $product->delete();
    }
}
