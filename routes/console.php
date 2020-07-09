<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('clear:all', function () {
    array_map('unlink', array_filter((array) glob(storage_path('logs/*.log'))));
    $this->comment('Logs have been cleared!');
    array_map('unlink', array_filter((array) glob(storage_path('app/*.tmp'))));
    $this->comment('Tmp files have been cleared!');
    $tableNames = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
    Schema::disableForeignKeyConstraints();
    foreach ($tableNames as $name) {
        //if you don't want to truncate migrations
        if ($name == 'migrations') {
            continue;
        }
        DB::table($name)->truncate();
        $this->comment('Table ' . $name . ' is truncated');
    }
    Schema::enableForeignKeyConstraints();

    Artisan::call('config:clear');
    Artisan::call('event:clear');
    Artisan::call('optimize:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    $this->comment('Cleared all Laravel cache types. Except cache type');
})->describe('Clear log files & truncate tables');
