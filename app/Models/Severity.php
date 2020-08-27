<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Severity Model.
 *
 * Represents a Severity level.
 */
class Severity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Vulnerability Types relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vulnerabilitiTypes()
    {
        return $this->hasMany('App\Models\VulnerabilityType');
    }

    /**
     * Findings relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function findings()
    {
        return $this->hasMany('App\Models\Finding');
    }
}
