<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * DNS Model.
 *
 * Represents a DNS record targeting the website's domain.
 */
class DNS extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'value', 'class', 'ttl', 'pri'];

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
     * Get all of the sources models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function target()
    {
        return $this->morphTo();
    }
}
