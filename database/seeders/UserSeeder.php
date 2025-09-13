<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Raza',
                'username' => 'raza',
                'password' => '$2y$12$yUf5P2b4.YKMflkAKT61.usbLx.4tKanp9MJkHHarMqopJC0OD.nu',
                'role' => 'admin',
                'status' => 'active',
                'profile_picture' => 'default_avatar.png',
                'theme' => 'light',
                'layout' => null,
                'invoice_type' => 'order',
            ],
            [
                'name' => 'Arif Ali',
                'username' => 'arifali',
                'password' => '$2y$12$F9WyZcKM3lg0dN67k2o8hOsHv641/MSUBcA3pM1CxLfa.fL.0uIfS',
                'role' => 'admin',
                'status' => 'active',
                'profile_picture' => 'default_avatar.png',
                'theme' => 'light',
                'layout' => null,
                'invoice_type' => 'order',
            ],
            [
                'name' => 'Fozail',
                'username' => 'fozail',
                'password' => '$2y$12$SCoIfL791BIfdV00I/Q.vOY5BuU8n5SgLHOZkEQ.WQqS6ulfA2Vvm',
                'role' => 'admin',
                'status' => 'active',
                'profile_picture' => 'default_avatar.png',
                'theme' => 'light',
                'layout' => null,
                'invoice_type' => 'order',
            ],
            [
                'name' => 'Talha',
                'username' => 'talha',
                'password' => '$2y$12$vxl4c6sMe6nD/TqCV0hnT.ndopRyOtcIHyL6oqpisypJuzc4II8QG',
                'role' => 'admin',
                'status' => 'active',
                'profile_picture' => 'default_avatar.png',
                'theme' => 'light',
                'layout' => null,
                'invoice_type' => 'order',
            ],
            [
                'name' => 'Anas',
                'username' => 'anas',
                'password' => '$2y$12$cOvfNs4PaYAZVvAh00FqqeCxLTL1NX2GpPNA.RK4DIFlDDjXizX/S',
                'role' => 'store_keeper',
                'status' => 'active',
                'profile_picture' => 'default_avatar.png',
                'theme' => 'light',
                'layout' => null,
                'invoice_type' => 'order',
            ],
            [
                'name' => 'Abdullah',
                'username' => 'abdullah',
                'password' => '$2y$12$pDQs9mWHHmPPpS04ZRGx8.wuMOPqJxTDPiH8.OjEv53uOqDqmYoza',
                'role' => 'owner',
                'status' => 'active',
                'profile_picture' => 'default_avatar.png',
                'theme' => 'light',
                'layout' => null,
                'invoice_type' => 'order',
            ],
            [
                'name' => 'Zubair',
                'username' => 'zubair',
                'password' => '$2y$12$nWc8nnf7iH8hVgaJ7Ixdq.rncnFgfh5qke3J7W9Cs5xw4yomIMP9K',
                'role' => 'owner',
                'status' => 'active',
                'profile_picture' => 'default_avatar.png',
                'theme' => 'light',
                'layout' => null,
                'invoice_type' => 'order',
            ],
            [
                'name' => 'Ali',
                'username' => 'ali',
                'password' => '$2y$12$N0atKuo3yMvd6qymcIKgAeyuiXwFP3Z2XR7r/dGhNqEdUNFDJvMyS',
                'role' => 'owner',
                'status' => 'active',
                'profile_picture' => 'default_avatar.png',
                'theme' => 'light',
                'layout' => null,
                'invoice_type' => 'order',
            ],
            [
                'name' => 'Qasim',
                'username' => 'qasim',
                'password' => '$2y$12$yUf5P2b4.YKMflkAKT61.usbLx.4tKanp9MJkHHarMqopJC0OD.nu',
                'role' => 'accountant',
                'status' => 'active',
                'profile_picture' => 'default_avatar.png',
                'theme' => 'light',
                'layout' => null,
                'invoice_type' => 'order',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['username' => $user['username']],
                $user
            );
        }
    }
}
