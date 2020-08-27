<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Request Model.
 *
 * Represents a website request.
 */
class Request extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['method', 'path', 'status', 'body'];

    /**
     * Website relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function website()
    {
        return $this->belongsTo('App\Models\Website');
    }

    /**
     * Content relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content()
    {
        return $this->belongsTo('App\Models\RequestContent', 'content_id');
    }
}
