<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Organisation Model.
 *
 * Represents an Organisation.
 */
class Organisation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

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
