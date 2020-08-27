<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;
use App\Observers\WebsiteObserver;
use App\Models\Traits\Findable;

/**
 * Website Model.
 *
 * Represents a website.
 *
 * When a Website model is created, it tries
 * to get the last redirection URL, using the
 * Websites Helper.
 */
class Website extends Model
{
    use Actionable;
    use Findable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['url', 'environment_id'];

    /**
     * Boot events.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($website) {
            $website->headers()->delete();
            if ($website->token) {
                $website->token->delete();
            }
            $website->installations()->delete();
            $website->findings()->delete();
            $website->projects()->detach();
            $website->moduleLogs()->delete();
            $website->requests()->delete();
        });

        static::observe(WebsiteObserver::class);
    }

    /**
     * Get the website domain from the URL.
     *
     * @param bool $removeSubdomain
     * @return string
     */
    public function getUrlDomain($removeSubdomain = false)
    {
        $domain = parse_url($this->url, PHP_URL_HOST);

        if ($removeSubdomain) {
            preg_match('#[^\.]+[\.]{1}[^\.]+$#', $domain, $matches);
            return $matches[0];
        }

        return $domain;
    }

    /**
     * Environment relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function environment()
    {
        return $this->belongsTo('App\Models\Environment');
    }

    /**
     * Domains relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function domains()
    {
        return $this->belongsToMany('App\Models\Domain')->using('App\Models\Pivots\DomainWebsite');
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
     * Hosts relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function hosts()
    {
        return $this->belongsToMany('App\Models\Host')->using('App\Models\Pivots\HostWebsite');
    }

    /**
     * Headers relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function headers()
    {
        return $this->hasMany('App\Models\Header');
    }

    /**
     * Requests relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests()
    {
        return $this->hasMany('App\Models\Request');
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
     * @example Missing WAF
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
     * Token relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function token()
    {
        return $this->morphOne('App\Models\Token', 'model');
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
