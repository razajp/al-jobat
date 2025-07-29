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
            ['short_title' => 'CMT|S', 'title' => 'CMT|S',           'type' => 'supplier_category'],
            ['short_title' => 'FBR', 'title' => 'Fabric',        'type' => 'supplier_category'],
            ['short_title' => 'FND', 'title' => 'Fund',          'type' => 'supplier_category'],
            ['short_title' => 'GMT', 'title' => 'Garments',      'type' => 'supplier_category'],
            ['short_title' => 'MCH', 'title' => 'Machinery',     'type' => 'supplier_category'],
            ['short_title' => 'MTR', 'title' => 'Materials',     'type' => 'supplier_category'],
            ['short_title' => 'OFS', 'title' => 'Office Supply', 'type' => 'supplier_category'],
            ['short_title' => 'PLT', 'title' => 'Plot',          'type' => 'supplier_category'],
            ['short_title' => 'PVT', 'title' => 'Private',       'type' => 'supplier_category'],
            ['short_title' => 'SRV', 'title' => 'Services',      'type' => 'supplier_category'],
            ['short_title' => 'CUT', 'title' => 'Cutting',   'type' => 'worker_type'],
            ['short_title' => 'DHP', 'title' => 'Dhaap',     'type' => 'worker_type'],
            ['short_title' => 'CMT', 'title' => 'CMT',       'type' => 'worker_type'],
            ['short_title' => 'SNG', 'title' => 'Singer',    'type' => 'worker_type'],
            ['short_title' => 'OFL', 'title' => 'O/F Look',  'type' => 'worker_type'],
            ['short_title' => 'BTK', 'title' => 'Bartake',   'type' => 'worker_type'],
            ['short_title' => 'TKN', 'title' => 'Token',     'type' => 'worker_type'],
            ['short_title' => 'CRP', 'title' => 'Cropping',  'type' => 'worker_type'],
            ['short_title' => 'PRS', 'title' => 'Press',     'type' => 'worker_type'],
            ['short_title' => 'PKG', 'title' => 'Packing',   'type' => 'worker_type'],
        ];

        foreach ($setups as $setup) {
            Setup::firstOrCreate(
                ['title' => $setup['title'], 'type' => $setup['type']],
                ['short_title' => $setup['short_title']]
            );
        }
    }
}
