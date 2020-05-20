<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 * @package App\Console
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * Schedule with daily
         */
        $dailyTasks = [
            'jav:onejav daily',
            'jav:r18 daily',
            'flickr:contacts',
            'queue:restart',
            'queue:retry all'
        ];
        foreach ($dailyTasks as $dailyTask) {
            $schedule->command($dailyTask)
                ->daily()
                ->withoutOverlapping()->runInBackground();
        }

        /**
         * Schedule everyMinute
         */
        $minuteTasks = [
            'batdongsan',
        ];
        foreach ($minuteTasks as $minuteTask) {
            $schedule->command($minuteTask)
                ->everyMinute()
                ->withoutOverlapping()->runInBackground();
        }

        /**
         * Schedule everyFiveMinute
         */
        $fiveMinutesTasks = [
            'jav:onejav fully',
            'jav:r18 fully',
            'jav:xcityprofile',
            'jav:xcityvideo',
            'xiuren',
            'phodacbiet',
            'kissgoddess',
            'flickr:photos',
            'flickr:photossizes'
        ];
        foreach ($fiveMinutesTasks as $fiveMinutesTask) {
            $schedule->command($fiveMinutesTask)
                ->everyFiveMinutes()
                ->withoutOverlapping()->runInBackground();
        }

        $teenMinutesTasks = [
        ];
        foreach ($teenMinutesTasks as $teenMinutesTask) {
            $schedule->command($teenMinutesTask)
                ->everyTenMinutes()
                ->withoutOverlapping()->runInBackground();
        }

        $fifteenMinutesTasks = [
            'truyentranh:truyenchon',
        ];
        foreach ($fifteenMinutesTasks as $fifteenMinutesTask) {
            $schedule->command($fifteenMinutesTask)
                ->everyFifteenMinutes()
                ->withoutOverlapping()->runInBackground();
        }

        // Clear cache
        $schedule->command('cache:clear')
            ->hourly()
            ->withoutOverlapping()->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
