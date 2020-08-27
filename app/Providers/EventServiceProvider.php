<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        \App\Events\Website\Created::class => [
            \App\Listeners\Websites\DiscoverDomain::class,
            \App\Listeners\Websites\DiscoverHost::class,
        ],

        \App\Events\Domain\Created::class => [
            \App\Listeners\Domains\CreateParentDomain::class,
            \App\Listeners\Domains\DiscoverHost::class,
        ],

        \App\Events\Finding\Created::class => [
            \App\Listeners\Findings\Notify::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
