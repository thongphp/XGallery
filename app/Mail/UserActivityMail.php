<?php

namespace App\Mail;

use App\Models\Core\UserActivity;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserActivityMail extends Mailable
{
    use Queueable, SerializesModels;

    private UserActivity $userActivity;

    private User $actor;

    /**
     * @param  UserActivity  $userActivity
     * @param  User  $actor
     */
    public function __construct(UserActivity $userActivity, User $actor)
    {
        $this->userActivity = $userActivity;
        $this->actor = $actor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        $subject = config('app.name').' - '.$this->actor->name.' - Activity';

        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($subject)
            ->markdown('emails.user.userActivity')
            ->with(
                [
                    'actor' => $this->actor,
                    'activity' => $this->userActivity
                ]
            );
    }
}
