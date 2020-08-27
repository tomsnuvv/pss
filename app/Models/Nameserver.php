<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Nameserver Model.
 *
 * Model representing a nameserver obtained from a domain.
 */
class Nameserver extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'host_id'];

    /**
     * Domain relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo('App\Models\Domain');
    }

    /**
     * Host relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function host()
    {
        return $this->belongsTo('App\Models\Host');
    }
}
