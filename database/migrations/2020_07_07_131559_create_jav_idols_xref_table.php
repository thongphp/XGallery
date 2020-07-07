<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJavIdolsXrefTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jav_idols_xref', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idol_id')->unsigned();
            $table->bigInteger('movie_id')->unsigned();
            $table->foreign('idol_id')->references('id')->on('jav_idols');
            $table->foreign('movie_id')->references('id')->on('jav_movies');
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
        Schema::dropIfExists('jav_idols_xref');
    }
}
