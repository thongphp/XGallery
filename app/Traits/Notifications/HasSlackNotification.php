<?php

namespace App\Traits\Notifications;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

trait HasSlackNotification
{

    /**
     * @return Repository|Application|mixed
     */
    public function routeNotificationForSlack()
    {
        return config('logging.channels.slack.url');
    }
}
