<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Module Log Status Model.
 */
class ModuleLogStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Executed Status Id.
     *
     * @var integer
     */
    const EXECUTED = 1;

    /**
     * Finished Status Id.
     *
     * @var integer
     */
    const FINISHED = 2;

    /**
     * Can't run Status Id.
     *
     * @var integer
     */
    const NORUN = 3;

    /**
     * Error Status Id.
     *
     * @var integer
     */
    const ERROR = 4;

    /**
     * Filters executed status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExecuted(Builder $query)
    {
        return $query->where('name', 'Executed');
    }

    /**
     * Filters finished status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFinished(Builder $query)
    {
        return $query->where('name', 'Finished');
    }

    /**
     * Filters cant run status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCantRun(Builder $query)
    {
        return $query->where('name', 'Can\'t run');
    }

    /**
     * Filters error status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeError(Builder $query)
    {
        return $query->where('name', 'Error');
    }
}
