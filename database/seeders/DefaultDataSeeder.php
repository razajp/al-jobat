<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultDataSeeder extends Seeder
{
    public function run()
    {
        // Default user
        DB::table('users')->updateOrInsert(
            [
                'username' => 'dev',
            ],
            [
                'name' => 'dev',
                'password' => '$2y$12$V6SBN1THQHkTbhGarfCk1eArE5Mye2FkOjHLgAmbubQXQlQNMSZSe',
                'role' => 'developer',
                'theme' => 'dark',
            ]
        );

        // Insert default setups
        DB::table('setups')->insert([
            [
                'type' => 'bank_name',
                'title' => 'Meezan Bank Limited',
                'short_title' => 'MBL',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
        ]);

        DB::unprepared(file_get_contents(database_path('seeders/sql/cities.sql')));
    }
}
