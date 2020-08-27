<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\InstallationObserver;
use App\Models\Traits\Findable;

/**
 * Installation.
 *
 * Represents a Product installation.
 */
class Installation extends Model
{
    use Findable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Boot events.
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(InstallationObserver::class);
    }

    /**
     * Get all of the sources models.
     *
     * @example A Host, Website or Repository.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function source()
    {
        return $this->morphTo();
    }

    /**
     * Get all of the child sources models.
     *
     * @example Port of a Host.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function childSource()
    {
        return $this->morphTo();
    }

    /**
     * Product relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    /**
     * Module relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }

    /**
     * Findings relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function findings()
    {
        return $this->hasMany('App\Models\Finding');
    }

    /**
    * Projects relation.
    *
    * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
    */
    public function projects()
    {
        return $this->morphToMany('App\Models\Project', 'projectable')->using('App\Models\Pivots\Projectable');
    }
}
