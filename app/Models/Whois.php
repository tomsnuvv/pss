<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Whois Model.
 *
 * Model based on a Whois response of a domain.
 */
class Whois extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'registrar', 'creation_date', 'expiration_date', 'raw',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'creation_date', 'expiration_date'];

    /**
     * Checks if is not expired.
     *
     * @param int $days Days upfront
     *
     * @return bool|null
     */
    public function isValid($days = 0)
    {
        if (!$this->expiration_date) {
            return;
        }

        return $this->daysToExpire() > $days;
    }

    /**
     * Calculates the number of days to expiration.
     *
     * @return int|null
     */
    public function daysToExpire()
    {
        if (!$this->expiration_date) {
            return;
        }

        return Carbon::now()->diffInDays($this->expiration_date, false);
    }

    /**
     * Domain relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo('App\Models\Domain');
    }
}
