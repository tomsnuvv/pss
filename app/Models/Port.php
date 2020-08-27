<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\PortObserver;
use App\Models\Traits\Findable;

/**
 * Port Model.
 *
 * Represents a port running on a Host.
 * An Port always indicates an open state.
 */
class Port extends Model
{
    use Findable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['port', 'protocol', 'service'];

    /**
     * Boot events.
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(PortObserver::class);
    }

    /**
     * Host relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function host()
    {
        return $this->belongsTo('App\Models\Host');
    }

    /**
     * Installation relation.
     *
     * Not all the ports have instalaltions, as sometimes
     * port scans don't reveal the product name.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function installation()
    {
        return $this->morphOne('App\Models\Installation', 'child_source');
    }

    /**
     * Findings relation.
     *
     * @example a port without an installation (as the product was not obtained) can be bruteforced.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function findings()
    {
        return $this->morphMany('App\Models\Finding', 'child_target');
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
