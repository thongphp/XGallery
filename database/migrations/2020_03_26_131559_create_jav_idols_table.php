<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJavIdolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jav_idols', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('alias')->nullable(true);
            $table->dateTime('birthday')->nullable(true);
            $table->string('blood_type')->nullable(true);
            $table->string('city')->nullable(true);
            $table->smallInteger('height')->nullable(true);
            $table->smallInteger('breast')->nullable(true);
            $table->smallInteger('waist')->nullable(true);
            $table->smallInteger('hips')->nullable(true);
            $table->string('cover')->nullable(true);
            $table->integer('favorite')->nullable(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jav_idols');
    }
}
