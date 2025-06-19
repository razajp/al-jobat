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
                'type' => 'supplier_category',
                'title' => 'Al Jobat',
                'short_title' => 'AJG',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'supplier_category',
                'title' => 'CMT',
                'short_title' => 'CMT',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'supplier_category',
                'title' => 'Fabric',
                'short_title' => 'FBR',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'supplier_category',
                'title' => 'Fund',
                'short_title' => 'FND',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'supplier_category',
                'title' => 'Garments',
                'short_title' => 'GMT',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'supplier_category',
                'title' => 'Machinery',
                'short_title' => 'MCH',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'supplier_category',
                'title' => 'Materials',
                'short_title' => 'MTR',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'supplier_category',
                'title' => 'Office Supply',
                'short_title' => 'OFS',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'supplier_category',
                'title' => 'Plot',
                'short_title' => 'PLT',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'supplier_category',
                'title' => 'Private',
                'short_title' => 'PVT',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'supplier_category',
                'title' => 'Services',
                'short_title' => 'SRV',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'supplier_category',
                'title' => 'Embroidery',
                'short_title' => 'EMB',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],

            [
                'type' => 'bank_name',
                'title' => 'Meezan Bank Limited',
                'short_title' => 'MBL',
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
        ]);

        DB::unprepared(file_get_contents(database_path('seeders/sql/cities.sql')));
    }
}
