<?php

namespace App\Console\Commands\System;

use App\Notifications\Deploy;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Symfony\Component\Process\Process;

class Update extends Command
{
    use Notifiable;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Entry point
     */
    public function handle()
    {
        $process = new Process(base_path() . '/update.sh');
        $process->run();

        $this->notify(new Deploy($process->getOutput()));
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @SuppressWarnings("unused")
     *
     * @param  Notification  $notification
     *
     * @return string
     */
    public function routeNotificationForSlack($notification)
    {
        return config('services.slack.webhook_url');
    }
}
