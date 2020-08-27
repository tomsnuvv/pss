<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Environment Model.
 *
 * Defines an environment used by a website.
 */
class Environment extends Model
{
    /**
     * Production environment.
     *
     * @var integer
     */
    const PRODUCTION = 9;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'public'];

    /**
     * Websites relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function websites()
    {
        return $this->hasMany('App\Models\Website');
    }

    /**
     * Checks if the environment is public.
     *
     * @return bool
     */
    public function isPublic()
    {
        return $this->public === 1;
    }

    /**
     * Checks if the environment is production.
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this->name == 'Prod';
    }

    /**
     * Checks if the environment is test.
     *
     * @return bool
     */
    public function isTest()
    {
        return $this->name == 'Test';
    }

    /**
     * Filters public environments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic(Builder $query)
    {
        return $query->where('public', 1);
    }

    /**
     * Filters production environments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProduction(Builder $query)
    {
        return $query->where('name', 'Prod');
    }
}
