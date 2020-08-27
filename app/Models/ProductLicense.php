<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Product License Model.
 *
 * Defines the license type of a Product.
 */
class ProductLicense extends Model
{
    /**
     * Products relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    /**
     * Filters free product license.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFree(Builder $query)
    {
        return $query->where('name', 'Free');
    }

    /**
     * Filters comercial product license.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeComercial(Builder $query)
    {
        return $query->where('name', 'Comercial');
    }

    /**
     * Filters internal product license.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInternal(Builder $query)
    {
        return $query->where('name', 'Internal');
    }
}
