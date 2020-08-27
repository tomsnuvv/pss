<?php

namespace App\Models;

use App\Libs\Helpers\Products;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Observers\ProductObserver;

/**
 * Product Model.
 *
 * Defines a piece of code (plugin, library, theme, CMS...),
 * usually created by a Vendor.
 */
class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type_id', 'license_id', 'code',
        'latest_version', 'description', 'website',
        'source',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'last_update', 'latest_info_check'];

    /**
     * Boot events.
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(ProductObserver::class);
    }

    /**
     * Generate a product name (if empty).
     *
     * @return string
     */
    public function generateName()
    {
        if (!$this->name || $this->name == $this->code) {
            $this->name = '';
            if ($this->vendor) {
                $this->name = $this->vendor->name . ' / ';
            }

            $this->name .= $this->code;
        }

        return $this->name;
    }

    /**
     * Type relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('App\Models\ProductType', 'type_id');
    }

    /**
     * License relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function license()
    {
        return $this->belongsTo('App\Models\ProductLicense');
    }

    /**
     * Vendor relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor');
    }

    /**
     * Vulnerabilities relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function vulnerabilities()
    {
        return $this->belongsToMany('App\Models\Vulnerability', 'vulnerability_affectances', 'product_id', 'vulnerability_id')->distinct();
    }

    /**
     * Affectances relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affectances()
    {
        return $this->hasMany('App\Models\VulnerabilityAffectance');
    }

    /**
     * Installations relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function installations()
    {
        return $this->hasMany('App\Models\Installation');
    }

    /**
     * Synonyms relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function synonyms()
    {
        return $this->hasMany('App\Models\ProductSynonym');
    }
}
