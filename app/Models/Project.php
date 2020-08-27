<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;
use App\Observers\ProjectObserver;
use App\Models\Traits\Findable;

/**
 * Project Model.
 *
 * Groups Domains, Websites, Repositories, Hosts and Findings.
 */
class Project extends Model
{
    use Actionable;
    use Findable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Register any other events for your application.
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(ProjectObserver::class);
    }

    /**
     * Organisations relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function organisations()
    {
        return $this->morphedByMany('App\Models\Organisation', 'projectable')->using('App\Models\Pivots\Projectable');
    }

    /**
     * Websites relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function websites()
    {
        return $this->morphedByMany('App\Models\Website', 'projectable')->using('App\Models\Pivots\Projectable');
    }

    /**
     * Repositories relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function repositories()
    {
        return $this->morphedByMany('App\Models\Repository', 'projectable')->using('App\Models\Pivots\Projectable');
    }

    /**
     * Domains relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function domains()
    {
        return $this->morphedByMany('App\Models\Domain', 'projectable')->using('App\Models\Pivots\Projectable');
    }

    /**
     * Certificates relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function certificates()
    {
        return $this->morphedByMany('App\Models\Certificate', 'projectable')->using('App\Models\Pivots\Projectable');
    }

    /**
     * Hosts relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function hosts()
    {
        return $this->morphedByMany('App\Models\Host', 'projectable')->using('App\Models\Pivots\Projectable');
    }
    /**
     * Ports relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function ports()
    {
        return $this->morphedByMany('App\Models\Port', 'projectable')->using('App\Models\Pivots\Projectable');
    }

    /**
     * Installations relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function installations()
    {
        return $this->morphedByMany('App\Models\Installation', 'projectable')->using('App\Models\Pivots\Projectable');
    }

    /**
     * Findings relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function findings()
    {
        return $this->morphedByMany('App\Models\Finding', 'projectable')->using('App\Models\Pivots\Projectable');
    }
}
