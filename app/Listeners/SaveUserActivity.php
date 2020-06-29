<?php

namespace App\Listeners;

use App\Events\UserActivity;

class SaveUserActivity
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //

    }

    /**
     * Handle the event.
     *
     * @param  UserActivity  $event
     * @return void
     */
    public function handle(UserActivity $event)
    {
        \App\Models\UserActivity::create([
            'actor_id' => $event->getActor()->getAuthIdentifier(),
            'actor_table' => 'oauths',
            'action' => $event->getAction(),
            'object_id' => 1,
            'object_table' => 'test',
            'extra' => $event->get
        ]);
    }
}
