<?php

namespace App\Notifications;

use App\Models\Core\UserActivityModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class UserActivity extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack', 'database'];
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

    public function toSlack(UserActivityModel $notifiable)
    {
        $slackMessage = (new SlackMessage)
            ->from('User activity')
            ->info()
            ->content($notifiable->trans());

        if ($notifiable->extra === null) {
            return $slackMessage;
        }

        /**
         * @var SlackAttachment $attachment
         */
        $slackMessage->attachment(function ($attachment) use ($notifiable) {
            $attachment
                ->title($notifiable->extra->title, $notifiable->extra->title_link ?? null)
                ->fields(get_object_vars($notifiable->extra->fields))
                ->footer($notifiable->extra->footer ?? null);

            if (isset($notifiable->extra->action)) {
                $attachment->action($notifiable->extra->action[0], $notifiable->extra->action[1]);
            }
        });

        return $slackMessage;
    }
}
