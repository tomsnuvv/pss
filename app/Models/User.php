<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\User
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Route notifications for the Slack channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForSlack($notification)
    {
        $integration = Integration::ofType('Slack')->first();
        if ($integration) {
            return $integration->token;
        }
    }

    /**
     * Check if the user is an admin.
     *
     * Hardcoded due the amount of requests that Laravel Nova does on Policies.
     *
     * @return bool
     */
    public function isAdmin()
    {
        if (!$this->role_id) {
            return false;
        }

        return $this->role_id == 2;
    }

    /**
     * Role relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }
}
