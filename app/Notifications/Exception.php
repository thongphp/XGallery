<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Class Exception
 * @package App\Notifications
 */
class Exception extends Notification
{
    use Queueable;

    private \Exception $exception;

    /**
     * Exception constructor.
     * @param  \Exception  $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

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
     * @SuppressWarnings("unused")
     *
     * @param $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack($notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->from('Exception')
            ->error()
            ->content($this->exception->getMessage())
            ->attachment(function ($attachment) {
                /**
                 * @var SlackAttachment $attachment
                 */
                $attachment
                    ->title($this->exception->getFile(). '. Line ' . $this->exception->getLine())
                    ->footer(config('app.url'));
            });
    }
}
