<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Repository Type Model.
 *
 * Defines a type of Repository.
 */
class RepositoryType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Repositories relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function repositories()
    {
        return $this->hasMany('App\Models\Repository');
    }
}
