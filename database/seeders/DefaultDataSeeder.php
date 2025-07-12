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
                'title' => 'Allied Bank Limited',
                'short_title' => 'ABL',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Bank Al Habib Limited',
                'short_title' => 'BAH',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Bank Alfalah Limited',
                'short_title' => 'BAF',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Bank Islami Pakistan Limited',
                'short_title' => 'BIPL',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Bank of Punjab',
                'short_title' => 'BOP',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Dubai Islamic Bank Pakistan Limited',
                'short_title' => 'DFIBL',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'EasyPaisa',
                'short_title' => 'EZP',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Faysal Bank Limited',
                'short_title' => 'FBL',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Habib Bank Limited',
                'short_title' => 'HBL',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Habib Metropolitan Bank Limited',
                'short_title' => 'HMB',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'JazzCash',
                'short_title' => 'JZC',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'JS Bank Limited',
                'short_title' => 'JS',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'MCB Bank Limited',
                'short_title' => 'MCB',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Meezan Bank Limited',
                'short_title' => 'MBL',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'National Bank of Pakistan',
                'short_title' => 'NBP',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Sindh Bank Limited',
                'short_title' => 'SBL',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Standard Chartered Bank Limited',
                'short_title' => 'SCB',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'Summit Bank Limited',
                'short_title' => 'SNBL',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'U Paisa',
                'short_title' => 'UP',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'bank_name',
                'title' => 'United Bank Limited',
                'short_title' => 'UBL',
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
        ]);

        DB::unprepared(file_get_contents(database_path('seeders/sql/cities.sql')));
    }
}
