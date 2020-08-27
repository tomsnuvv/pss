<?php

namespace App\Notifications;

use App\Models\Finding;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NewFinding extends Notification
{
    use Queueable;

    /**
     * Finding model.
     *
     * @var \App\Models\Finding
     */
    private $finding;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Finding  $finding
     * @return void
     */
    public function __construct(Finding $finding)
    {
        $this->finding = $finding;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        $finding = $this->finding;
        $targetString = '';
        $target = $finding->target;

        switch ($finding->target_type) {
            case 'App\Models\Website':
                $targetString = $target->url;
                break;
            case 'App\Models\Domain':
                $targetString = $target->name;
                break;
            case 'App\Models\Host':
                $targetString = $target->name ? $target->name : $target->ip;
                break;
        }

        return (new SlackMessage)
            ->warning()
            ->content('New finding')
            ->attachment(function ($attachment) use ($finding, $targetString) {
                $attachment->title($finding->title, url('resources/findings/'.$finding->id))
                    ->fields([
                        'Target' => $targetString,
                        'Projects' => implode(', ', $finding->target->projects()->pluck('name')->toArray()),
                        'Severity' => $finding->severity ? $finding->severity->name : 'Unknown',
                        'Vuln. Type' => $finding->vulnerabilityType ? $finding->vulnerabilityType->code : 'Unknown'
                    ])
                    ->content($finding->details);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
