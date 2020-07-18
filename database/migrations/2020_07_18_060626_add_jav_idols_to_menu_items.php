<?php

use Illuminate\Database\Migrations\Migration;

class AddJavIdolsToMenuItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::table('menu_items')->insert(
            [
                [
                    'name' => 'Jav - Idols',
                    'link' => 'jav.idols',
                    'type' => 'item',
                    'icon' => null,
                    'ordering' => 3,
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::table('menu_items')
            ->where('link', '=', 'jav.idols')
            ->where('type', '=', 'item')
            ->delete();
    }
}
