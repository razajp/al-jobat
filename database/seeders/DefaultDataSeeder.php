<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultDataSeeder extends Seeder
{
    public function run()
    {
        // Default users
        DB::table('users')->updateOrInsert(
            [
                'name' => 'dev',
                'username' => 'dev',
                'password' => '$2y$12$V6SBN1THQHkTbhGarfCk1eArE5Mye2FkOjHLgAmbubQXQlQNMSZSe',
                'role' => 'developer',
                'theme' => 'dark',
            ],
        );

        // add type="supplier_category", "created_at":"2025-03-05T17:37:26.000000Z","updated_at":"2025-03-05T17:37:26.000000Z", title="fabric" and short_title="FBR" to setups table
        DB::table('setups')->insert([
            ['type' =>'supplier_category', 'title' => 'Fabric', 'short_title' => 'FBR', 'created_at' => '2025-03-05T17:37:26.000000Z', 'updated_at' => '2025-03-05T17:37:26.000000Z'],
            ['type' =>'supplier_category', 'title' => 'Embroidery', 'short_title' => 'EMB', 'created_at' => '2025-03-05T17:37:26.000000Z', 'updated_at' => '2025-03-05T17:37:26.000000Z'],
        ]);
    }
}
