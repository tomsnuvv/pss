<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;
use App\Observers\RepositoryObserver;
use App\Models\Traits\Findable;

/**
 * Repository Model.
 *
 * This model represents the Repository entity.
 * Contains a name, a code and an URL.
 * Also checks if the WordPress plugin is installed
 * (in case the repository is a WordPress website).
 */
class Repository extends Model
{
    use Actionable;
    use Findable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'url', 'clone_url', 'public'];

    /**
     * Register any other events for your application.
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(RepositoryObserver::class);
    }

    /**
     * Get the repo user (or organization).
     *
     * @return string
     */
    public function getUserAttribute()
    {
        return head(explode('/', $this->name));
    }

    /**
     * Get the repo name without the user (or organization).
     *
     * @return string
     */
    public function getRepoAttribute()
    {
        return last(explode('/', $this->name));
    }

    /**
     * Checks if the repository is public.
     *
     * @return bool
     */
    public function isPublic()
    {
        return $this->public === 1;
    }

    /**
     * Repository Type relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('App\Models\RepositoryType');
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
