<?php
namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        Shift::truncate();

        // Format kode: H + 2 digit durasi + 4 digit jam masuk
        $codes = ['H080700', 'H080800', 'H081200', 'H081600', 'H060700', 'H061200'];

        foreach ($codes as $code) {
            try {
                $parsed = Shift::parseCode($code);
                Shift::create([
                    ...$parsed,
                    'description' => null,
                ]);
            } catch (\Exception $e) {
                // skip invalid
            }
        }
    }
}
