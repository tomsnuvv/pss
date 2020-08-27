<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Module Model.
 *
 * Represents a Module executed as an action to obtain or process information.
 */
class Module extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code'];

    /**
     * Instalaltion relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function installations()
    {
        return $this->hasMany('App\Models\Installation');
    }

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
     * ModuleLogs relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany('App\Models\ModuleLog');
    }
}
