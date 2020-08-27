<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Integration Model.
 *
 * Handles Integrations data, such as Slack, Github...
 */
class Integration extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['token', 'settings'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'json',
    ];

    /**
     * Boot events.
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->token) {
                $model->token = encrypt($model->token);
            }
        });

        static::retrieved(function ($model) {
            if ($model->token) {
                $model->token = decrypt($model->token);
            }
        });
    }

    /**
     * Type relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('App\Models\IntegrationType');
    }

    /**
     * Filters type by string.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $ype
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType(Builder $query, $type)
    {
        return $query->whereHas('type', function ($query) use ($type) {
            $query->where('name', $type);
        });
    }
}
