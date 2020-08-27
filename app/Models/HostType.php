<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Host Type Model.
 *
 * Defines a Host type.
 */
class HostType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Hosts relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hosts()
    {
        return $this->hasMany('App\Models\Host');
    }

    /**
     * Filters server host type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeServer(Builder $query)
    {
        return $query->where('name', 'server');
    }

    /**
     * Filters nameserver host type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNameServer(Builder $query)
    {
        return $query->where('name', 'nameserver');
    }
}
