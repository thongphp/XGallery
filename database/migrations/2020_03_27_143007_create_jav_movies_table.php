<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJavMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jav_movies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name')->nullable();
            $table->string('cover')->nullable();
            $table->dateTime('sales_date')->nullable();
            $table->dateTime('release_date')->nullable();
            $table->string('content_id')->nullable();
            $table->string('dvd_id');
            $table->text('description')->nullable();
            $table->integer('time')->nullable();
            $table->string('director')->nullable();
            $table->string('studio')->nullable();
            $table->string('label')->nullable();
            $table->string('channel')->nullable();
            $table->string('series')->nullable();
            $table->text('gallery')->nullable();
            $table->string('sample')->nullable();
            $table->boolean('is_downloadable')->nullable();
            $table->string('reference_url')->nullable();
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
        Schema::dropIfExists('jav_movies');
    }
}
