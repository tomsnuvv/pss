<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Role Model.
 *
 * User Role.
 */
class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Users relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    /**
     * Checks if the role is user.
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->name == 'User';
    }

    /**
     * Checks if the role is admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->name == 'Admin';
    }

    /**
     * Filters Admin roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmin(Builder $query)
    {
        return $query->where('name', 'Admin');
    }

    /**
     * Filters User roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUser(Builder $query)
    {
        return $query->where('name', 'User');
    }
}
