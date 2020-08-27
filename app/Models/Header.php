<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Header Model.
 *
 * Represents a HTTP header obtained from a website.
 * Contains the header name and it's value.
 */
class Header extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'value'];

    /**
     * Website relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function website()
    {
        return $this->belongsTo('App\Models\Website');
    }
}
