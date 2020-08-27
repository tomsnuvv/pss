<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Request Content Model.
 *
 * Represents a website request content.
 */
class RequestContent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['hash', 'body'];

    /**
     * Requests relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests()
    {
        return $this->hasMany('App\Models\Request', 'content_id');
    }
}
