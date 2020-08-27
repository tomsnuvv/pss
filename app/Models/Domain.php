<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Actions\Actionable;
use App\Observers\DomainObserver;
use App\Models\Traits\Findable;

/**
 * Domain Model.
 *
 * Defines a Domain.
 */
class Domain extends Model
{
    use Actionable;
    use Findable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'wildcard', 'name'];

    /**
     * Boot events.
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(DomainObserver::class);
    }

    /**
     * Checks if the domain registration is not expired.
     *
     * @param int $days Days upfront
     * @return bool|void
     */
    public function isValid($days = 0)
    {
        if ($this->whois) {
            return $this->whois->isValid($days);
        }
    }

    /**
     * Filters top level domains.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTopLevel(Builder $query)
    {
        return $query->whereNull('domain_id');
    }

    /**
     * Filters subdomains domains.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQuerySubdomains(Builder $query)
    {
        return $query->whereNotNull('domain_id');
    }

    /**
     * Websites relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function websites()
    {
        return $this->belongsToMany('App\Models\Website')->using('App\Models\Pivots\DomainWebsite');
    }

    /**
     * Subdomains relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subdomains()
    {
        return $this->hasMany('App\Models\Domain', 'domain_id');
    }

    /**
     * Parent Domain relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Domain', 'domain_id');
    }

    /**
     * Host relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function host()
    {
        return $this->belongsTo('App\Models\Host');
    }

    /**
     * NameServers relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function nameservers()
    {
        return $this->hasMany('App\Models\Nameserver');
    }

    /**
     * DNS relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dns()
    {
        return $this->hasMany('App\Models\DNS');
    }

    /**
     * Target DNS relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function targetDNS()
    {
        return $this->morphMany('App\Models\DNS', 'target');
    }

    /**
     * Whois relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function whois()
    {
        return $this->hasOne('App\Models\Whois');
    }

    /**
     * Certificate relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function certificate()
    {
        return $this->belongsTo('App\Models\Certificate');
    }

    /**
     * Findings relation.
     *
     * @example Domain expired
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function findings()
    {
        return $this->morphMany('App\Models\Finding', 'target');
    }

    /**
     * Module Logs relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function moduleLogs()
    {
        return $this->morphMany('App\Models\ModuleLog', 'model');
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
