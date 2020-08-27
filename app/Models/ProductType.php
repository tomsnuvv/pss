<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Product Type Model.
 *
 * Defines the type of a Product.
 */
class ProductType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Checks if the type is composer.
     *
     * @return bool
     */
    public function isComposer()
    {
        return $this->name == 'Composer Package';
    }

    /**
     * Checks if the type is javascript.
     *
     * @return bool
     */
    public function isJavascript()
    {
        return $this->name == 'Javascript';
    }

    /**
     * Checks if the type is WordPress Plugin.
     *
     * @return bool
     */
    public function isWordPressPlugin()
    {
        return $this->name == 'WordPress Plugin';
    }

    /**
     * Checks if the type is WordPress Theme.
     *
     * @return bool
     */
    public function isWordPressTheme()
    {
        return $this->name == 'WordPress Theme';
    }

    /**
     * Checks if the type is Jenkins Plugin.
     *
     * @return bool
     */
    public function isJenkinsPlugin()
    {
        return $this->name == 'Jenkins Plugin';
    }

    /**
     * Filters CMS product type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCms(Builder $query)
    {
        return $query->where('name', 'CMS');
    }

    /**
     * Filters composer product type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeComposer(Builder $query)
    {
        return $query->where('name', 'Composer Package');
    }

    /**
     * Filters WordPress plugin product type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWordpressPlugin(Builder $query)
    {
        return $query->where('name', 'WordPress Plugin');
    }

    /**
     * Filters WordPress theme product type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWordpressTheme(Builder $query)
    {
        return $query->where('name', 'WordPress Theme');
    }

    /**
     * Filters service product type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeService(Builder $query)
    {
        return $query->where('name', 'Service');
    }

    /**
     * Filters javascript product type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeJavascript(Builder $query)
    {
        return $query->where('name', 'Javascript');
    }

    /**
     * Filters Web App product type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWebApp(Builder $query)
    {
        return $query->where('name', 'Web App');
    }

    /**
     * Filters Jenkins Plugin product type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeJenkinsPlugin(Builder $query)
    {
        return $query->where('name', 'Jenkins Plugin');
    }

    /**
     * Products relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('App\Models\Product', 'type_id');
    }
}
