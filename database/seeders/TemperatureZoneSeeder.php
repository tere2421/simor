<?php

namespace Database\Seeders;

use App\Models\TemperatureZone;
use Illuminate\Database\Seeder;

class TemperatureZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'Chiller A',   'location' => 'Dapur Utama',  'min_temp' => 0,   'max_temp' => 4,   'description' => 'Bumbu sauce'],
            ['name' => 'Chiller B',   'location' => 'Dapur Utama',  'min_temp' => 0,   'max_temp' => 4,   'description' => 'Sambal dan sauce'],
            ['name' => 'Freezer 1',   'location' => 'Gudang',       'min_temp' => -22, 'max_temp' => -18, 'description' => 'WIP Protein'],
            ['name' => 'Freezer 2',   'location' => 'Gudang',       'min_temp' => -22, 'max_temp' => -18, 'description' => 'Frozen food'],
            ['name' => 'Dry Storage', 'location' => 'Gudang Kering','min_temp' => 18,  'max_temp' => 30,  'description' => 'Bahan kering, dry goods, packaging'],
        ];

        foreach ($zones as $zone) {
            TemperatureZone::create($zone);
        }
    }
}
