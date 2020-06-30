<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Class FlickrNotification
 * @package App\Notifications
 */
class FlickrNotification extends Notification
{
    use Queueable;

    private string $message;

    /**
     * FlickrNotification constructor.
     * @param  string  $message
     */
    public function __construct(string $message)
    {
       $this->message = $message;
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

    public function toSlack($notifiable)
    {
        return (new SlackMessage())
            ->from('Flickr')
            ->info()
            ->content($this->message);
    }
}
