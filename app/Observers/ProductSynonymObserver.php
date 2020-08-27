<?php

namespace App\Observers;

use App\Libs\Helpers\Products;
use App\Models\ProductSynonym;

/**
 * Product Synonym Observer class.
 */
class ProductSynonymObserver
{
    /**
     * Handle the ProductSynonym "saving" event.
     *
     * @param \App\Models\ProductSynonym $synonym
     */
    public function saving(ProductSynonym $synonym)
    {
        $synonym->name = Products::cleanCode($synonym->name);
    }
}
