<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Integration Type Model.
 */
class IntegrationType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Integrations relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function integration()
    {
        return $this->hasMany('App\Models\Integration', 'type_id');
    }
}
