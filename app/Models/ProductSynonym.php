<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\ProductSynonymObserver;

/**
 * Product Synonym Model.
 *
 * Synonyms are used to relate other product codes with a product.
 *
 * @example Apache HTTP server product can be name as:
 * - apache / http
 * - apache-httpd
 * - apache / http_server
 * - apache / http_server2.0a1
 */
class ProductSynonym extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Boot events.
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(ProductSynonymObserver::class);
    }

    /**
     * Product relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
