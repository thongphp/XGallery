<?php

namespace App\Notifications;

use App\Mail\UserActivityMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class UserActivity extends Notification
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
        return ['slack', 'mail', 'database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @SuppressWarnings("unused")
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * @param  \App\Models\Core\UserActivity  $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack(\App\Models\Core\UserActivity $notifiable): SlackMessage
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

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return Mailable
     */
    public function toMail(\App\Models\Core\UserActivity $notifiable): Mailable
    {
        $actor = User::find($notifiable->actor_id);

        return (new UserActivityMail($notifiable, $actor))->to($actor->email);
    }
}
