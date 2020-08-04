<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Class Exception
 * @package App\Notifications
 */
class Deploy extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @SuppressWarnings("unused")
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable): array
    {
        // Because we are trigger notification in Handler not model. We can't use database here
        return ['slack'];
    }

    /**
     * @return SlackMessage
     */
    public function toSlack(): SlackMessage
    {
        return (new SlackMessage)
            ->from('XGallery - Deployer')
            ->info()
            ->content('XGallery was deployed');
    }
}
