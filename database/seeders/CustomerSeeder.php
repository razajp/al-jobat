<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Setup;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $allMyData = [
            [
                "category" => "cash",
                "urdu_title" => "501907",
                "customer_name" => "Ashraf Garments (Sharqiya)",
                "person_name" => "Ashraf Garments (Sharqiya)",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Ahmedpur",
                "username" => "ashraf",
                "phone_number" => "03176717919",
                "address" => ""
            ],
            [
                "category" => "cash",
                "urdu_title" => "501856",
                "customer_name" => "Bara Garments ",
                "person_name" => "Bara Garments ",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Timergara",
                "username" => "baragarments",
                "phone_number" => "03439583595",
                "address" => ""
            ],
            [
                "category" => "cash",
                "urdu_title" => "501624",
                "customer_name" => "Ibaad Garments",
                "person_name" => "Ibaad Garments",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Timergara",
                "username" => "ibaad",
                "phone_number" => "0302-9442105",
                "address" => ""
            ],
            [
                "category" => "cash",
                "urdu_title" => "501016",
                "customer_name" => "Karachi Garments",
                "person_name" => "Karachi Garments",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Timergara",
                "username" => "karachitmg",
                "phone_number" => "0-",
                "address" => ""
            ],
            [
                "category" => "cash",
                "urdu_title" => "500256",
                "customer_name" => "Marjan Garments",
                "person_name" => "Marjan Garments",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Timergara",
                "username" => "marjan",
                "phone_number" => "0311-9939996",
                "address" => ""
            ],
            [
                "category" => "cash",
                "urdu_title" => "501209",
                "customer_name" => "Muzafar Shah",
                "person_name" => "Muzafar Shah",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Timergara",
                "username" => "muzafarshah",
                "phone_number" => "0\\0344-9725105",
                "address" => ""
            ],
            [
                "category" => "cash",
                "urdu_title" => "500569",
                "customer_name" => "Nasir Garments (Sharqiya)",
                "person_name" => "Nasir Garments (Sharqiya)",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Ahmedpur",
                "username" => "nasir",
                "phone_number" => "0314-6880516",
                "address" => ""
            ],
            [
                "category" => "cash",
                "urdu_title" => "501023",
                "customer_name" => "Sahab Hosiery",
                "person_name" => "Sahab Hosiery",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Timergara",
                "username" => "sahabhosiery",
                "phone_number" => "0345-9520511",
                "address" => ""
            ],
            [
                "category" => "cash",
                "urdu_title" => "501132",
                "customer_name" => "Saif Ur Rehman Bengal Store",
                "person_name" => "Saif Ur Rehman Bengal Store",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Timergara",
                "username" => "saifurrehman",
                "phone_number" => "0348-9481996",
                "address" => ""
            ],
            [
                "category" => "cash",
                "urdu_title" => "501716",
                "customer_name" => "Shakir Garments",
                "person_name" => "Shakir Garments",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Timergara",
                "username" => "shakirgarments",
                "phone_number" => "03015945160",
                "address" => ""
            ],
            [
                "category" => "cash",
                "urdu_title" => "501258",
                "customer_name" => "Sufyan Garments",
                "person_name" => "Sufyan Garments",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Jatoi",
                "username" => "sufyangarmnts",
                "phone_number" => "0311-3400323",
                "address" => ""
            ],
            [
                "category" => "cash",
                "urdu_title" => "501421",
                "customer_name" => "Zia Garments",
                "person_name" => "Zia Garments",
                "date" => "2024-01-01 00:00:00",
                "password" => "1234",
                "city" => "Victoria",
                "username" => "ziagarments",
                "phone_number" => "0304-2824077",
                "address" => ""
            ]
        ];

        // City 'Ahmedpur ' not found. Skipping. Urdu Title: '501907'
        // City 'Timergara' not found. Skipping. Urdu Title: '501856'
        // City 'Timergara' not found. Skipping. Urdu Title: '501624'
        // City 'Timergara' not found. Skipping. Urdu Title: '501016'
        // City 'Timergara' not found. Skipping. Urdu Title: '500256'
        // City 'Timergara' not found. Skipping. Urdu Title: '501209'
        // City 'Ahmedpur ' not found. Skipping. Urdu Title: '500569'
        // City 'Cmt' not found. Skipping. Urdu Title: '501597'
        // City 'Timergara' not found. Skipping. Urdu Title: '501023'
        // City 'Timergara' not found. Skipping. Urdu Title: '501132'
        // City 'Timergara' not found. Skipping. Urdu Title: '501716'
        // City 'Jatoi' not found. Skipping. Urdu Title: '501258'
        // City 'Victoria ' not found. Skipping. Urdu Title: '501421'

        foreach ($allMyData as $data) {
            // Find city
            $city = Setup::where('title', $data['city'])->first();
            if (!$city) {
                $this->command->warn("City '{$data['city']}' not found. Skipping. Urdu Title: '{$data['urdu_title']}'");
                continue;
            }

            // Create user if not exists
            $user = User::firstOrCreate(
                ['username' => $data['username']],
                [
                    'name' => $data['customer_name'],
                    'password' => Hash::make($data['password']),
                    'role' => 'customer',
                    'profile_picture' => 'default_avatar.png'
                ]
            );

            // Create customer if not exists
            Customer::firstOrCreate(
                [
                    'customer_name' => $data['customer_name'],
                    'city_id' => $city->id
                ],
                [
                    'user_id' => $user->id,
                    'creator_id' => 1, // replace with actual creator/admin ID
                    'person_name' => $data['person_name'],
                    'phone_number' => $data['phone_number'],
                    'urdu_title' => $data['urdu_title'],
                    'date' => $data['date'],
                    'category' => $data['category'],
                    'address' => $data['address'],
                ]
            );
        }
    }
}
