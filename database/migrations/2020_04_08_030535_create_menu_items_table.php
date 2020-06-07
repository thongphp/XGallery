<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateMenuItemsTable
 */
class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable(false);
            $table->string('link')->nullable();
            $table->string('class')->nullable();
            $table->string('type')->nullable(false);
            $table->string('icon')->nullable();
            $table->integer('ordering')->nullable();
            $table->timestamps();
        });

        DB::table('menu_items')->insert(
            [
                [
                    'name' => 'Dashboard',
                    'link' => null,
                    'type' => 'header',
                    'icon' => 'fas fa-home',
                    'ordering' => 1
                ],
                [
                    'name' => 'Adult',
                    'link' => null,
                    'type' => 'header',
                    'icon' => 'fas fa-female',
                    'ordering' => 2
                ],
                [
                    'name' => 'Jav',
                    'link' => 'jav',
                    'type' => 'item',
                    'icon'=> null,
                    'ordering' => 3
                ],
                [
                    'name' => 'Xiuren',
                    'link' => 'xiuren',
                    'type' => 'item',
                    'icon'=> null,
                    'ordering' => 4
                ],
                [
                    'name' => 'Comics',
                    'link' => null,
                    'type' => 'header',
                    'icon' => 'fas fa-book',
                    'ordering' => 5
                ],
                [
                    'name' => 'Truyện chọn',
                    'link' => 'truyenchon',
                    'type' => 'item',
                    'icon'=> null,
                    'ordering' => 6
                ],
                [
                    'name' => 'Tools',
                    'link' => null,
                    'type' => 'header',
                    'icon' => 'fas fa-tools',
                    'ordering' => 7
                ],
                [
                    'name' => 'Flickr',
                    'link' => 'flickr',
                    'type' => 'item',
                    'icon'=> null,
                    'ordering' => 8
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
        Schema::dropIfExists('menu_items');
    }
}
