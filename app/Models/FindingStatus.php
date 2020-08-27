<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Finding Status Model.
 *
 * Defines a status for a Finding.
 */
class FindingStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Findings relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function findings()
    {
        return $this->hasMany('App\Models\Finding');
    }

    /**
     * Check if a finding status is false positive.
     *
     * @return bool
     */
    public function isFalsePositive()
    {
        return $this->name === 'False Positive';
    }

    /**
     * Filters open status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen(Builder $query)
    {
        return $query->where('name', 'Open');
    }

    /**
     * Filters fixed status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFixed(Builder $query)
    {
        return $query->where('name', 'Fixed');
    }

    /**
     * Filters false positive status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFalsePositive(Builder $query)
    {
        return $query->where('name', 'False Positive');
    }
}
