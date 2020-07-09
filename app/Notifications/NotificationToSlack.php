<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Class NotificationToSlack
 * @package App\Notifications
 */
class NotificationToSlack extends Notification
{
    use Queueable;

    private string $message;
    private string $level;

    /**
     * FlickrNotification constructor.
     * @param  string  $message
     * @param  string  $level
     */
    public function __construct(string $message, string $level = 'error')
    {
        $this->message = $message;
        $this->level = $level;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @SuppressWarnings("unused")
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * @SuppressWarnings("unused")
     *
     * @param mixed $notifiable
     *
     * @return mixed
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage())
            ->from('XGallery')
            ->{$this->level}()
            ->content($this->message);
    }
}
