<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Token.
 *
 * Auth token used in the APIs (pull & push).
 */
class Token extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['token'];

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
     * Generate a security token.
     *
     * @return bool
     */
    private function generateToken()
    {
        return $this->token = str_random(40);
    }

    /**
     * Get the related model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

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
     * Host relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function host()
    {
        return $this->belongsTo('App\Models\Host');
    }
}
