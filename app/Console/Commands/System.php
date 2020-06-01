<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

final class System extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:database';

    public function handle()
    {
        try {
            if (!file_exists(storage_path('framework/down'))) {
                DB::connection('mongodb')->getMongoClient()->listDatabases();
            }
        } catch (Exception $exception) {
            Artisan::call('down');
            app('sentry')->captureException($exception);
        }
    }
}
