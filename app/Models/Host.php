<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;
use App\Observers\HostObserver;
use App\Models\Traits\Findable;

/**
 * Host Model.
 *
 * Defines a Host, usually related to a Website.
 */
class Host extends Model
{
    use Actionable;
    use Findable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'ip', 'type_id'];

    /**
     * Boot events.
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(HostObserver::class);
    }

    /**
     * Type relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('App\Models\HostType');
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
     * Ports relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ports()
    {
        return $this->hasMany('App\Models\Port');
    }

    /**
     * Domains relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domains()
    {
        return $this->hasMany('App\Models\Domain');
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
     * Websites relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function websites()
    {
        return $this->belongsToMany('App\Models\Website')->using('App\Models\Pivots\HostWebsite');
    }

    /**
     * Certificates relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function certificates()
    {
        return $this->belongsToMany('App\Models\Certificate')->using('App\Models\Pivots\CertificateHost');
    }

    /**
     * Installations relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function installations()
    {
        return $this->morphMany('App\Models\Installation', 'source');
    }

    /**
     * Findings relation.
     *
     * @example: Further attacks, such as DoS
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function findings()
    {
        return $this->morphMany('App\Models\Finding', 'target');
    }

    /**
     * Token relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function token()
    {
        return $this->morphOne('App\Models\Token', 'model');
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
