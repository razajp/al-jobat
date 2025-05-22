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
                'title' => 'Fabric',
                'short_title' => 'FBR',
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
            [
                'type' => 'city',
                'title' => 'Karachi',
                'short_title' => null,
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'city',
                'title' => 'Lahore',
                'short_title' => null,
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'city',
                'title' => 'Islamabad',
                'short_title' => null,
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
            [
                'type' => 'city',
                'title' => 'Rawalpindi',
                'short_title' => null,
                'creator_id' => 1,
                'created_at' => '2025-03-05T17:37:26.000000Z',
                'updated_at' => '2025-03-05T17:37:26.000000Z',
            ],
        ]);
    }
}
