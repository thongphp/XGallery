<?php

namespace App\Notifications;

use App\Services\Flickr\Url\FlickrUrlInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Class FlickrRequestDownload
 * @package App\Notifications
 */
class FlickrRequestDownload extends Notification
{
    use Queueable;

    /**
     * @var FlickrUrlInterface
     */
    private FlickrUrlInterface $url;

    /**
     * Create a new notification instance.
     *
     * @param  FlickrUrlInterface  $url
     */
    public function __construct(FlickrUrlInterface $url)
    {
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
        $content = 'Requested `%s` URL `%s` with type `%s` and owner `%s`';
        return (new SlackMessage())
            ->from('Flickr')
            ->info()
            ->content(
                sprintf($content, 'download', $this->url->getUrl(), $this->url->getType(), $this->url->getOwner())
            );
    }
}
