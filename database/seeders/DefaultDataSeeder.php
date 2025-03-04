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
            ],
        );
    }
}
