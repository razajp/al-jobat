<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setup;

class SetupSeeder extends Seeder
{
    public function run(): void
    {
        $setups = [
            ['short_title' => 'AJG', 'title' => 'Al Jobat',      'type' => 'supplier_category'],
            ['short_title' => 'CMT', 'title' => 'CMT',           'type' => 'supplier_category'],
            ['short_title' => 'FBR', 'title' => 'Fabric',        'type' => 'supplier_category'],
            ['short_title' => 'FND', 'title' => 'Fund',          'type' => 'supplier_category'],
            ['short_title' => 'GMT', 'title' => 'Garments',      'type' => 'supplier_category'],
            ['short_title' => 'MCH', 'title' => 'Machinery',     'type' => 'supplier_category'],
            ['short_title' => 'MTR', 'title' => 'Materials',     'type' => 'supplier_category'],
            ['short_title' => 'OFS', 'title' => 'Office Supply', 'type' => 'supplier_category'],
            ['short_title' => 'PLT', 'title' => 'Plot',          'type' => 'supplier_category'],
            ['short_title' => 'PVT', 'title' => 'Private',       'type' => 'supplier_category'],
            ['short_title' => 'SRV', 'title' => 'Services',      'type' => 'supplier_category'],
        ];

        foreach ($setups as $setup) {
            Setup::firstOrCreate(
                ['title' => $setup['title'], 'type' => $setup['type']],
                ['short_title' => $setup['short_title']]
            );
        }
    }
}
