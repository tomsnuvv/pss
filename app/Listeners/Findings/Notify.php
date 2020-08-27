<?php

namespace App\Listeners\Findings;

use App\Events\Contracts\Interfaces\ModelEvent;
use App\Models\User;
use App\Notifications\NewFinding;
use Notification;
use App\Models\Integration;

/**
 * Notify a new finding.
 */
class Notify
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Contracts\Interfaces\ModelEvent $event
     * @return void
     */
    public function handle(ModelEvent $event)
    {
        $finding = $event->model;

        // Notification
        if (app()->environment() == 'testing') {
            return;
        }

        $integration = Integration::ofType('Slack')->first();
        if (!$integration) {
            return;
        }
        if (isset($integration->settings['min_severity'])) {
            if ($finding->severity_id && $finding->severity_id < $integration->settings['min_severity']) {
                return;
            }
        }

        // Notify a user (any user is fine for slack channel notifications)
        Notification::send(User::first(), new NewFinding($finding));
    }
}
