<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class CreateScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('command');
            $table->string('every')->nullable(true);
            $table->timestamps();
        });

        DB::table('schedules')->insert(
            [
                [
                    'command' => 'cache:clear',
                    'every' => 'daily',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'queue:restart',
                    'every' => 'daily',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'queue:retry all',
                    'every' => 'daily',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'jav:onejav daily',
                    'every' => 'daily',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'jav:r18 daily',
                    'every' => 'daily',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'flickr:contacts',
                    'every' => 'daily',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'batdongsan',
                    'every' => 'everyMinute',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'jav:onejav fully',
                    'every' => 'everyFiveMinutes',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'jav:r18 fully',
                    'every' => 'everyFiveMinutes',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'jav:xcityprofile',
                    'every' => 'everyFiveMinutes',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'jav:xcityvideo',
                    'every' => 'everyFiveMinutes',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'xiuren',
                    'every' => 'everyFiveMinutes',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'phodacbiet',
                    'every' => 'everyFiveMinutes',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'kissgoddess',
                    'every' => 'everyFiveMinutes',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'flickr:photos',
                    'every' => 'everyFiveMinutes',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'flickr:photossizes',
                    'every' => 'everyFiveMinutes',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'command' => 'truyentranh:truyenchon',
                    'every' => 'everyFifteenMinutes',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule');
    }
}