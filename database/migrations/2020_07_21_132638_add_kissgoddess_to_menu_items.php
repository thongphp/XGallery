<?php

use Illuminate\Database\Migrations\Migration;

class AddKissgoddessToMenuItems extends Migration
{
    public function up(): void
    {
        DB::table('menu_items')->insert(
            [
                [
                    'name' => 'Kissgoddess',
                    'link' => 'kissgoddess',
                    'type' => 'item',
                    'icon' => null,
                    'ordering' => 4,
                ],
            ]
        );
    }

    public function down(): void
    {
        DB::table('menu_items')
            ->where('link', '=', 'kissgoddess')
            ->where('type', '=', 'item')
            ->delete();
    }
}
