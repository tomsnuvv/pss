<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Observers\CertificateObserver;
use App\Models\Traits\Findable;

/**
 * Certificate Model.
 *
 * Represents a Certificate record.
 * A certificate can be served by Websites and Services, for example ES.
 * Certificates might be also related with Dommains, but not always (self-signed on host).
 */
class Certificate extends Model
{
    use Findable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        # Subject
        'subject_common_name', 'subject_org_unit',
        # Issuer
        'issuer_common_name', 'issuer_org', 'issuer_country', 'issuer_county', 'issuer_locality',
        # Key
        'key_type', 'key_length',
        # Dates
        'creation_date', 'expiration_date',
        # Others
        'serial', 'signature_algorithm',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'creation_date', 'expiration_date'];

    /**
     * Boot events.
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(CertificateObserver::class);
    }

    /**
     * Calculates the number of days to expiration.
     *
     * @return int|void
     */
    public function daysToExpire()
    {
        if (!$this->expiration_date) {
            return;
        }

        return Carbon::now()->diffInDays($this->expiration_date, false);
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
     * Hosts relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function hosts()
    {
        return $this->belongsToMany('App\Models\Host')->using('App\Models\Pivots\CertificateHost');
    }

    /**
     * Findings relation.
     *
     * @example Expired certificate, low crypto...
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function findings()
    {
        return $this->morphMany('App\Models\Finding', 'child_target');
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
