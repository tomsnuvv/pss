<?php

namespace App\Observers;

use App\Libs\Helpers\Products;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * Product Observer class.
 */
class ProductObserver
{
    /**
     * Handle the Product "saving" event.
     *
     * @param \App\Models\Product $product
     */
    public function saving(Product $product)
    {
        $product->code = Products::cleanCode($product->code);
        $product->generateName();
    }
}
