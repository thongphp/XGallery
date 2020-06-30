<?php

namespace App\Listeners;

use App\Events\Traits\ActivityEvent;
use App\Models\UserActivity;
use App\Notifications\FlickrNotification;
use App\Traits\Notifications\HasSlackNotification;
use Illuminate\Notifications\Notifiable;

class SaveUserActivity
{
    use Notifiable, HasSlackNotification;

    /**
     * Handle the event.
     *
     * @param  UserActivity  $event
     * @return void
     */
    public function handle(ActivityEvent $event)
    {
        UserActivity::create([
            'actor_id' => $event->getActor()->getAuthIdentifier(),
            'actor_table' => 'oauths',
            'action' => $event->getAction(),
            'object_id' => $event->getObjectId(),
            'object_table' => $event->getObjectTable(),
            'text' => $event->getText(),
            'extra' => json_encode($event->getExtra())
        ]);

        $this->notify(new FlickrNotification($event->translate()));
    }
}
