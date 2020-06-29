<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Class TruyenchonRequestDownloadException
 * @package App\Notifications
 */
class TruyenchonRequestDownloadException extends Notification
{
    use Queueable;

    private \Exception $exception;
    private string $url;


    /**
     * Create a new notification instance.
     *
     * @param  \Exception  $exception
     * @param  string  $url
     */
    public function __construct(\Exception $exception, string $url)
    {
        $this->exception = $exception;
        $this->url = $url;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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
        $content = 'Requested download URL `%s` failed: %s';
        return (new SlackMessage())
            ->from('Truyenchon')
            ->content(
                sprintf($content, $this->url, $this->exception->getMessage())
            );
    }
}
