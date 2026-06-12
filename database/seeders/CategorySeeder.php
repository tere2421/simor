<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Protein',    'description' => 'Ayam marinated, chicken fillet, ayam geprek'],
            ['name' => 'Frozen',     'description' => 'Frozen food — egg roll, katsu, tteok, dimsum'],
            ['name' => 'Bumbu',      'description' => 'WIP sauce, bumbu, sambal, marinade'],
            ['name' => 'Dry Goods',  'description' => 'Tepung, beras, minyak, seasoning powder, sauce kemasan'],
            ['name' => 'Dairy',      'description' => 'Susu, krim, syrup, powder minuman'],
            ['name' => 'Sayuran',    'description' => 'Sayuran segar — mentimun, kol, kimchi, telur'],
            ['name' => 'Packaging',  'description' => 'Kemasan — box, bowl, cup, flag, sleeve, sticker'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
