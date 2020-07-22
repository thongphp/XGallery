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
        $message = (new SlackMessage)
            ->from(get_class($this->exception))
            ->error()
            ->content($this->exception->getMessage());

        $traces = $this->exception->getTrace();
        $trace = reset($traces);
        $message->attachment(function ($attachment) use ($trace) {
            /**
             * @var SlackAttachment $attachment
             */
            $attachment
                ->fields($trace)
                ->footer(config('app.url'))
                ->timestamp(Carbon::now()->getTimestamp());
        });

        return $message;
    }
}
