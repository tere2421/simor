<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            IngredientSeeder::class,
            TemperatureZoneSeeder::class,
            ChecklistItemSeeder::class,
            ShiftSeeder::class,          // ← seeder shift kode baru
            UserSeeder::class,
            ManagerTaskSeeder::class,    // ← seeder task SM & PIC
        ]);
    }
}
